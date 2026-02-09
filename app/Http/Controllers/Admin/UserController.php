<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\Employee;
use App\Models\AuditLog;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index()
    {
        // Get all active users (excluding soft-deleted/terminated and partners) with their relationships
        // Partners have their own separate management system
        $users = User::whereNull('deleted_at')
            ->excludePartners() // Partners should not appear in user management
            ->with(['roles', 'userDetail'])
            ->orderBy('name')
            ->get();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(StoreUserRequest $request)
    {
        $user = new User;
        $user->name = $request->name;
        $user->email = strtolower($request->email); // Convert to lowercase
        $user->password = Hash::make($request->password);
        $user->zoom_number = $request->zoom_number;
        $user->save();

        // Assign roles - detach all first then attach new ones
        $roles = $request->roles ?? [];
        try {
            // Detach all existing roles first
            $user->roles()->detach();
            
            // Attach new roles by name
            if (!empty($roles)) {
                $user->syncRoles($roles);
            }
            
            // Verify roles were assigned
            \Log::info("User created with roles", [
                'email' => $user->email,
                'requested_roles' => $roles,
                'assigned_roles' => $user->roles->pluck('name')->toArray()
            ]);
        } catch (\Exception $e) {
            \Log::error("Error assigning roles to user", [
                'email' => $user->email,
                'roles' => $roles,
                'error' => $e->getMessage()
            ]);
            // Continue without roles rather than failing the entire operation
        }

        $userDetail = new UserDetail;
        $userDetail->user_id = $user->id;
        $userDetail->phone = $request->phone;
        $userDetail->plain_password = $request->plain_password;
        $userDetail->dob = $request->dob;
        $userDetail->gender = $request->gender;
        $userDetail->join_date = $request->join_date;
        $userDetail->address = $request->address;
        $userDetail->city = $request->city;
        $userDetail->role = implode(', ', $roles); // Store multiple roles as comma-separated string
        $userDetail->save();

        // Sync with EMS - create or update employee record
        // Skip EMS entry for CEO and Agent (partners) as they are outside the system
        if (!$user->hasRole('CEO') && !$user->hasRole('Agent')) {
            $employee = Employee::where('email', $user->email)->first();
            if ($employee) {
                $employee->update([
                    'name' => $user->name,
                    'mis' => 'Yes',
                    'status' => 'Active',
                ]);
            } else {
                Employee::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'mis' => 'Yes',
                    'status' => 'Active',
                    'contact_info' => '',
                    'emergency_contact' => '',
                    'cnic' => '',
                    'position' => '',
                    'area_of_residence' => '',
                ]);
            }
        }

        // Log the action
        AuditLog::logAction(
            action: 'user_created',
            user: auth()->user(),
            model: 'User',
            model_id: $user->id,
            description: "New user created: {$user->email} with roles " . implode(', ', $roles)
        );

        return redirect()->route('users.index')->with('success', 'Employee created successfully.');
    }

    public function show($id)
    {
        $user = User::with(['userDetail', 'roles'])->findOrFail($id);

        return view('admin.users.show', compact('user'));
    }

    public function edit($id)
    {
        $user = User::with(['userDetail', 'roles'])->findOrFail($id);

        return view('admin.users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, $id)
    {
        $user = User::findOrFail($id);

        // Store old values for audit log
        $oldRoles = $user->roles()->pluck('name')->toArray();
        $oldEmail = $user->email;
        $oldStatus = $user->status;

        // Update user basic info
        $user->name = $request->name;
        $user->email = strtolower($request->email); // Convert to lowercase
        $user->zoom_number = $request->zoom_number;
        $user->status = $request->status ?? 'active';

        // Update password only if provided and not blank
        if ($request->filled('password') && trim($request->password) !== '') {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // Update or create user details
        $userDetail = $user->userDetail ?? new UserDetail(['user_id' => $user->id]);
        $userDetail->phone = $request->phone;
        $userDetail->plain_password = $request->plain_password;
        $userDetail->dob = $request->dob;
        $userDetail->gender = $request->gender;
        $userDetail->join_date = $request->join_date;
        $userDetail->address = $request->address;
        $userDetail->city = $request->city;
        
        $newRoles = $request->roles ?? [];
        $userDetail->role = implode(', ', $newRoles); // Store multiple roles as comma-separated string
        $userDetail->save();

        // Update user roles in Spatie - detach all first then attach new ones
        try {
            // Detach all existing roles first
            $user->roles()->detach();
            
            // Attach new roles by name
            if (!empty($newRoles)) {
                $user->syncRoles($newRoles);
            }
        } catch (\Exception $e) {
            \Log::error("Error updating roles for user", [
                'user_id' => $user->id,
                'roles' => $newRoles,
                'error' => $e->getMessage()
            ]);
            // Continue without roles rather than failing the entire operation
        }

        // Sync with EMS - update employee record
        // CEO and Agent (partners) should not be in EMS as they are outside the system
        if ($user->hasRole('CEO') || $user->hasRole('Agent')) {
            // If user became CEO or Agent, remove from EMS
            Employee::where('email', $user->email)->update(['mis' => 'No']);
        } else {
            // Regular user - sync with EMS
            $employee = Employee::where('email', $user->email)->first();
            if ($employee) {
                $employee->update([
                    'name' => $user->name,
                    'mis' => 'Yes',
                    'status' => $user->status === 'inactive' ? 'Not Active' : 'Active',
                ]);
            } else {
                // Create if doesn't exist
                Employee::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'mis' => 'Yes',
                    'status' => $user->status === 'inactive' ? 'Not Active' : 'Active',
                    'contact_info' => '',
                    'emergency_contact' => '',
                    'cnic' => '',
                    'position' => '',
                    'area_of_residence' => '',
                ]);
            }
        }

        // Log the action
        AuditLog::logAction(
            action: 'user_updated',
            user: auth()->user(),
            model: 'User',
            model_id: $user->id,
            description: "User updated: {$user->email}",
            changes: [
                'email' => ['old' => $oldEmail, 'new' => $user->email],
                'roles' => ['old' => implode(', ', $oldRoles), 'new' => implode(', ', $newRoles)],
                'status' => ['old' => $oldStatus, 'new' => $user->status],
            ]
        );

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Soft delete the user
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }

    /**
     * Show password reset form for admin
     */
    public function resetPasswordForm($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.reset-password', compact('user'));
    }

    /**
     * Reset user password by admin
     */
    public function resetPassword(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::findOrFail($id);
        $oldEmail = $user->email;
        $newPassword = $request->password;

        // Update password
        $user->password = Hash::make($newPassword);
        $user->save();

        // Log the action
        AuditLog::logAction(
            action: 'password_reset_by_admin',
            user: auth()->user(),
            model: 'User',
            model_id: $user->id,
            description: "Admin reset password for {$user->email}",
            changes: ['email' => $oldEmail]
        );

        // Send email notification
        try {
            \Mail::to($user->email)->send(new \App\Mail\PasswordResetMail($user, $newPassword));
        } catch (\Exception $e) {
            \Log::error('Failed to send password reset email: ' . $e->getMessage());
        }

        return redirect()->route('users.show', $id)->with('success', 'Password reset successfully. Email sent to user.');
    }

    /**
     * Display a listing of trashed users.
     */
    public function trashed()
    {
        $users = User::onlyTrashed()->with(['roles', 'userDetail'])->get();

        return view('admin.users.trashed', compact('users'));
    }

    /**
     * Restore a soft deleted user.
     */
    public function restore($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();

        return redirect()->route('users.trashed')->with('success', 'User restored successfully.');
    }

    /**
     * Permanently delete a user.
     */
    public function forceDelete($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);

        // Delete associated user details first
        if ($user->userDetail) {
            $user->userDetail->delete();
        }

        // Permanently delete the user
        $user->forceDelete();

        return redirect()->route('users.trashed')->with('success', 'User permanently deleted.');
    }

    /**
     * Show avatar upload form
     */
    public function uploadAvatarForm($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.upload-avatar', compact('user'));
    }

    /**
     * Upload user avatar
     */
    public function uploadAvatar(Request $request, $id)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpg,jpeg,png,gif|max:5120',
        ]);

        $user = User::findOrFail($id);
        $fileUploadService = new FileUploadService();

        // Delete old avatar if exists
        if ($user->avatar && \Storage::disk('local')->exists($user->avatar)) {
            $fileUploadService->deleteFile($user->avatar);
        }

        // Upload new avatar
        $avatarPath = $fileUploadService->uploadAvatar($request->file('avatar'), (string)$user->id);

        if ($avatarPath) {
            $user->update(['avatar' => $avatarPath]);

            // Log the action
            AuditLog::logAction(
                action: 'avatar_uploaded',
                user: auth()->user(),
                model: 'User',
                model_id: $user->id,
                description: "Avatar uploaded for user {$user->email}"
            );

            return redirect()->route('users.show', $id)->with('success', 'Avatar uploaded successfully.');
        }

        return redirect()->back()->with('error', 'Failed to upload avatar. Please try again.');
    }

    /**
     * Update user's plain password via AJAX
     */
    public function updatePassword(Request $request, $id)
    {
        $request->validate([
            'plain_password' => 'nullable|string|max:255'
        ]);

        $user = User::findOrFail($id);
        
        $userDetail = $user->userDetail ?? new UserDetail(['user_id' => $user->id]);
        $userDetail->plain_password = $request->plain_password;
        $userDetail->save();

        return response()->json([
            'success' => true,
            'message' => 'Password saved successfully'
        ]);
    }
}
