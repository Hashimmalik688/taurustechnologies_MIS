<?php

namespace App\Http\Controllers;

use App\Models\Lead;
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
        // Get leads that are ready to call (active or pending)
        $leads = Lead::whereIn('status', ['active', 'pending'])
            ->whereNotNull('phone_number')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return view('ravens.calling', compact('leads'));
    }
}
