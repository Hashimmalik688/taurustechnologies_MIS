<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EmployeeController extends Controller
{
    public function __construct()
    {
        \Log::debug('EMS: EmployeeController constructor called');
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            \Log::debug('EMS: middleware user', ['user' => $user ? $user->email : null, 'roles' => $user ? $user->getRoleNames() : null]);
            if (!$user->hasAnyRole(['CEO', 'Trainer', 'Super Admin', 'HR', 'Co-ordinator', 'Manager'])) {
                \Log::debug('EMS: aborting 403 for user', ['user' => $user ? $user->email : null]);
                abort(403, 'Unauthorized action.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        \Log::debug('EMS index: started');
        try {
            // Sync all ACTIVE users (not soft-deleted) as employees
            // Exclude CEO and Agent (partners) - they are outside the employee system
            $activeUsers = \App\Models\User::whereNull('deleted_at')
                ->get()
                ->filter(fn($user) => !$user->hasRole('CEO') && !$user->hasRole('Agent'));
            
            $userEmails = $activeUsers->pluck('email', 'id')->toArray();
            \Log::debug('EMS index: userEmails', ['userEmails' => $userEmails]);
            
            foreach ($userEmails as $userId => $email) {
                $user = \App\Models\User::find($userId);
                if (!Employee::where('email', $email)->exists()) {
                    Employee::create([
                        'name' => $user->name,
                        'email' => $user->email,
                        'contact_info' => '',
                        'emergency_contact' => '',
                        'cnic' => '',
                        'position' => '',
                        'area_of_residence' => '',
                        'status' => $user->status ?? 'Active',
                        'mis' => 'Yes',
                        'passport_image' => null,
                        'account_password' => null,
                    ]);
                } else {
                    $emp = Employee::where('email', $email)->first();
                    if ($emp->mis !== 'Yes') {
                        $emp->mis = 'Yes';
                        $emp->save();
                    }
                }
            }
            
            // Update MIS to 'No' for soft-deleted users (terminated employees)
            $deletedUserEmails = \App\Models\User::onlyTrashed()->pluck('email')->toArray();
            if (!empty($deletedUserEmails)) {
                Employee::whereIn('email', $deletedUserEmails)
                    ->where('mis', '=', 'Yes')
                    ->update(['mis' => 'No']);
            }

            // Update MIS to 'No' for partners (users with Agent role)
            $partnerEmails = \App\Models\User::whereNull('deleted_at')
                ->whereHas('roles', function($query) {
                    $query->where('name', 'Agent');
                })
                ->pluck('email')
                ->toArray();
            if (!empty($partnerEmails)) {
                Employee::whereIn('email', $partnerEmails)
                    ->where('mis', '=', 'Yes')
                    ->update(['mis' => 'No']);
            }

            // Update MIS to 'No' for employees without any corresponding user (not in active or deleted users)
            $allUserEmails = \App\Models\User::withTrashed()->pluck('email')->toArray();
            Employee::whereNotIn('email', $allUserEmails)
                ->where('mis', '=', 'Yes')
                ->update(['mis' => 'No']);
            
            \Log::debug('EMS index: employees sync complete');
            
            // Get all employees and filter out CEO users and partners (status='Partner')
            // Partners have their own management page and should not appear in EMS
            $employees = Employee::orderBy('id')->get()->filter(function ($employee) {
                // Exclude partners by status
                if ($employee->status === 'Partner') {
                    return false;
                }
                
                // Exclude CEO role users
                $user = \App\Models\User::withTrashed()->where('email', $employee->email)->first();
                return !$user || !$user->hasRole('CEO');
            });
            
            \Log::debug('EMS index: rendering view', ['employees_count' => $employees->count()]);
            return view('admin.employee.ems', compact('employees'));
        } catch (\Throwable $e) {
            \Log::error('EMS index error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            abort(500, 'EMS error: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'contact_info' => 'nullable|string|max:255',
            'emergency_contact' => 'nullable|string|max:255',
            'cnic' => 'nullable|string|max:30',
            'position' => 'nullable|string|max:255',
            'area_of_residence' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:20',
            'passport_image' => 'nullable|file|mimes:webp|max:2048',
        ]);

        // Build data to save
        $saveData = [];

        // Helper function to format phone numbers
        $formatPhone = function($value) {
            if (!$value) return '';
            $digits = preg_replace('/\D/', '', $value);
            if ($digits && substr($digits, 0, 1) === '3' && substr($digits, 0, 2) !== '03') {
                $digits = '0' . $digits;
            }
            return $digits;
        };

        // Add fields to save only if they have values
        $saveData['name'] = $request->filled('name') ? trim($request->name) : '';
        $saveData['email'] = $request->filled('email') ? strtolower(trim($request->email)) : '';
        $saveData['contact_info'] = $request->filled('contact_info') ? $formatPhone($request->contact_info) : '';
        $saveData['emergency_contact'] = $request->filled('emergency_contact') ? $formatPhone($request->emergency_contact) : '';
        $saveData['cnic'] = $request->filled('cnic') ? trim($request->cnic) : '';
        $saveData['position'] = $request->filled('position') ? trim($request->position) : '';
        $saveData['area_of_residence'] = $request->filled('area_of_residence') ? trim($request->area_of_residence) : '';
        
        // Auto-set status and MIS
        $status = $request->filled('status') ? trim($request->status) : 'Active';
        $saveData['status'] = $status;
        // MIS is automatically "Yes" for Active/Not Active, "No" for Terminated
        $saveData['mis'] = ($status === 'Terminated') ? 'No' : 'Yes';

        // Handle image
        if ($request->hasFile('passport_image')) {
            $saveData['passport_image'] = $request->file('passport_image')->store('employee_passports', 'public');
        }

        Employee::create($saveData);
        return redirect()->route('employee.ems')->with('success', 'Employee added successfully.');
    }

    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'contact_info' => 'nullable|string|max:255',
            'emergency_contact' => 'nullable|string|max:255',
            'cnic' => 'nullable|string|max:30',
            'position' => 'nullable|string|max:255',
            'area_of_residence' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:20',
            'passport_image' => 'nullable|file|mimes:webp|max:2048',
        ]);

        // Only update fields that have values
        $updateData = [];
        $statusChangedToTerminated = false;
        
        // Helper function to format phone numbers
        $formatPhone = function($value) {
            if (!$value) return '';
            $digits = preg_replace('/\D/', '', $value);
            if ($digits && substr($digits, 0, 1) === '3' && substr($digits, 0, 2) !== '03') {
                $digits = '0' . $digits;
            }
            return $digits;
        };

        // Add fields to update only if they have values
        if ($request->filled('name')) {
            $updateData['name'] = trim($request->name);
        }
        if ($request->filled('email')) {
            $updateData['email'] = strtolower(trim($request->email));
        }
        if ($request->filled('contact_info')) {
            $updateData['contact_info'] = $formatPhone($request->contact_info);
        }
        if ($request->filled('emergency_contact')) {
            $updateData['emergency_contact'] = $formatPhone($request->emergency_contact);
        }
        if ($request->filled('cnic')) {
            $updateData['cnic'] = trim($request->cnic);
        }
        if ($request->filled('position')) {
            $updateData['position'] = trim($request->position);
        }
        if ($request->filled('area_of_residence')) {
            $updateData['area_of_residence'] = trim($request->area_of_residence);
        }
        
        // Auto-set MIS based on status (not user-editable)
        if ($request->filled('status')) {
            $status = trim($request->status);
            $updateData['status'] = $status;
            // MIS is automatically "Yes" for Active/Not Active, "No" for Terminated
            $updateData['mis'] = ($status === 'Terminated') ? 'No' : 'Yes';
            
            // Track if status is being changed to Terminated
            if ($status === 'Terminated' && $employee->status !== 'Terminated') {
                $statusChangedToTerminated = true;
            }
        }

        // Handle image
        if ($request->hasFile('passport_image')) {
            if ($employee->passport_image) {
                Storage::disk('public')->delete($employee->passport_image);
            }
            $updateData['passport_image'] = $request->file('passport_image')->store('employee_passports', 'public');
        }

        // Only update if there's data to update
        if (!empty($updateData)) {
            $employee->update($updateData);
        }

        // If status changed to Terminated, soft-delete the user account
        if ($statusChangedToTerminated) {
            $user = \App\Models\User::where('email', $employee->email)->first();
            if ($user && !$user->hasRole('CEO')) {
                $user->delete(); // Soft delete - preserves all historical data
            }
        }

        return redirect()->route('employee.ems')->with('success', 'Employee updated successfully.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt,xlsx',
        ]);
        $file = $request->file('csv_file');
        \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\EmployeesImport, $file);
        return back()->with('success', 'Employees imported and updated successfully.');
    }

    public function terminate($id)
    {
        try {
            $employee = Employee::findOrFail($id);
            
            // Update employee status to Terminated and MIS to No
            $employee->update([
                'status' => 'Terminated',
                'mis' => 'No',
            ]);
            
            // Find the user by email and soft-delete it (preserves all historical data)
            $user = \App\Models\User::where('email', $employee->email)->first();
            if ($user && !$user->hasRole('CEO')) {
                // Only soft-delete non-CEO users
                $user->delete(); // Soft delete using SoftDeletes trait - preserves all relationships and data
            }
            
            return redirect()->route('admin.employee.ems')
                ->with('success', 'Employee terminated successfully. User account deactivated (historical data preserved).');
        } catch (\Exception $e) {
            return redirect()->route('admin.employee.ems')
                ->with('error', 'Error terminating employee: ' . $e->getMessage());
        }
    }

    public function export()
    {
        // Get all employees
        $allEmployees = Employee::all();
        
        // Filter out CEO users by matching email
        $employees = $allEmployees->filter(function($emp) {
            // Find user by email
            $user = User::where('email', $emp->email)->first();
            
            if (!$user) return true; // Include if user not found
            
            // Exclude if user has CEO role
            return !$user->roles()->where('name', 'CEO')->exists();
        })->values();
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="employees.csv"',
        ];
        $callback = function() use ($employees) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Sr#','Name','Email','Contact info','Emergency Contact','CNIC','Position','Area of Residence','Status','MIS','Passport Size Image']);
            $sr = 1;
            foreach ($employees as $emp) {
                fputcsv($handle, [
                    $sr++, $emp->name, $emp->email, $emp->contact_info, $emp->emergency_contact, $emp->cnic, $emp->position, $emp->area_of_residence, $emp->status, $emp->mis, $emp->passport_image
                ]);
            }
            fclose($handle);
        };
        return new StreamedResponse($callback, 200, $headers);
    }

    public function destroy($id)
    {
        try {
            $employee = Employee::findOrFail($id);
            $email = $employee->email;
            
            // Delete the employee record
            $employee->delete();
            
            // Also hard-delete the associated user if exists
            $user = User::withTrashed()->where('email', $email)->first();
            if ($user && !$user->hasRole('CEO')) {
                $user->forceDelete(); // Permanently remove user
            }
            
            return redirect()->route('employee.ems')
                ->with('success', 'Employee deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('employee.ems')
                ->with('error', 'Error deleting employee: ' . $e->getMessage());
        }
    }
}
