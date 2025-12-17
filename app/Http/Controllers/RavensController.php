<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RavensController extends Controller
{
    /**
     * Ravens Dashboard
     */
    public function dashboard()
    {
        return view('ravens.dashboard');
    }

    /**
     * Ravens Calling System
     */
    public function calling()
    {
        return view('ravens.calling');
    }
}
