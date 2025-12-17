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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:1024'],
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

        $user->name = $request->get('name');
        $user->email = $request->get('email');

        if ($request->file('avatar')) {
            $avatar = $request->file('avatar');
            $avatarName = time() . '.' . $avatar->getClientOriginalExtension();
            $avatarPath = public_path('/images/');
            $avatar->move($avatarPath, $avatarName);
            $user->avatar = '/images/' . $avatarName;
        }

        $user->update();

        if ($user) {
            Session::flash('message', 'User Details Updated successfully!');
            Session::flash('alert-class', 'alert-success');
            return response()->json([
                'isSuccess' => true,
                'Message' => 'User Details Updated successfully!',
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
