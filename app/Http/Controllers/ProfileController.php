<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class ProfileController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request, $id)
    {
        $request->validate([
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
        ]);

        $user = User::find($id);

        if (!$user) {
            Session::flash('message', 'User not found!');
            Session::flash('alert-class', 'alert-danger');
            return response()->json([
                'isSuccess' => false,
                'Message' => 'User not found!',
            ], 404);
        }

        // Update name if provided
        if ($request->filled('name')) {
            $user->name = trim($request->name);
        }

        // Update email if provided
        if ($request->filled('email')) {
            $user->email = strtolower(trim($request->email));
        }

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar && file_exists(public_path($user->avatar))) {
                @unlink(public_path($user->avatar));
            }

            $avatar = $request->file('avatar');
            $avatarName = time() . '.' . $avatar->getClientOriginalExtension();
            $avatarPath = public_path('/images/');
            
            // Create directory if it doesn't exist
            if (!is_dir($avatarPath)) {
                mkdir($avatarPath, 0755, true);
            }
            
            $avatar->move($avatarPath, $avatarName);
            $user->avatar = '/images/' . $avatarName;
        }

        $user->save();

        if ($user) {
            Session::flash('message', 'Profile updated successfully!');
            Session::flash('alert-class', 'alert-success');
            return response()->json([
                'isSuccess' => true,
                'Message' => 'Profile updated successfully!',
            ], 200);
        }

        Session::flash('message', 'Something went wrong!');
        Session::flash('alert-class', 'alert-danger');
        return response()->json([
            'isSuccess' => false,
            'Message' => 'Something went wrong!',
        ], 200);
    }

    /**
     * Update user password
     */
    public function updatePassword(Request $request, $id)
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        if (!(Hash::check($request->get('current_password'), Auth::user()->password))) {
            return response()->json([
                'isSuccess' => false,
                'Message' => 'Your Current password does not match. Please try again.',
            ], 200);
        }

        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'isSuccess' => false,
                'Message' => 'User not found!',
            ], 404);
        }

        $user->password = Hash::make($request->get('password'));
        $user->update();

        if ($user) {
            Session::flash('message', 'Password updated successfully!');
            Session::flash('alert-class', 'alert-success');
            return response()->json([
                'isSuccess' => true,
                'Message' => 'Password updated successfully!',
            ], 200);
        }

        Session::flash('message', 'Something went wrong!');
        Session::flash('alert-class', 'alert-danger');
        return response()->json([
            'isSuccess' => false,
            'Message' => 'Something went wrong!',
        ], 200);
    }
}
