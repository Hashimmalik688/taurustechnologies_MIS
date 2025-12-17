<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;

class VerifierController extends Controller
{
    public function create(string $team = 'paraguins')
    {
        $team = strtolower($team);
        if (!in_array($team, ['ravens', 'paraguins'])) {
            abort(404);
        }

        // Fetch Paraguins closers (by role or department)
        $closers = User::role('Paraguins Closer')
            ->orWhere('department', 'paraguins')
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('verifier.create', [
            'closers' => $closers,
            'team' => $team,
        ]);
    }

    public function store(Request $request, string $team = 'paraguins')
    {
        $team = strtolower($team);
        if (!in_array($team, ['ravens', 'paraguins'])) {
            abort(404);
        }

        $validated = $request->validate([
            'date' => ['required', 'date'],
            'cn_name' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'max:30'],
            'verifier_name' => ['required', 'string', 'max:255'],
            'closer_id' => ['required', 'exists:users,id'],
            'date_of_birth' => ['required', 'date'],
            'age' => ['required', 'integer', 'min:18', 'max:100'],
            'gender' => ['required', 'in:Male,Female,Other'],
            'account_type' => ['required', 'in:Checking,Savings,Card'],
            'address' => ['required', 'string', 'max:500'],
            'state' => ['required', 'string', 'max:50'],
            'zip_code' => ['required', 'string', 'max:10'],
        ]);

        $closer = User::findOrFail($validated['closer_id']);

        // Create a minimal Lead record with allowed fields
        Lead::create([
            'date' => $validated['date'],
            'cn_name' => $validated['cn_name'],
            'phone_number' => $validated['phone_number'],
            'account_verified_by' => $validated['verifier_name'],
            'closer_name' => $closer->name,
            'managed_by' => $validated['closer_id'],
            'date_of_birth' => $validated['date_of_birth'],
            'age' => $validated['age'],
            'gender' => $validated['gender'],
            'account_type' => $validated['account_type'],
            'address' => $validated['address'],
            'state' => $validated['state'],
            'zip_code' => $validated['zip_code'],
            'status' => 'transferred',
            'team' => $team,
            'verified_by' => auth()->id(),
        ]);

        return redirect()->route('verifier.create', ['team' => $team])
            ->with('success', 'Verification submission saved and transferred to closer.');
    }

    public function dashboard()
    {
        // Get all leads submitted by this verifier
        $leads = Lead::where(function($query) {
                $query->where('verified_by', auth()->id())
                      ->orWhere('account_verified_by', auth()->user()->name);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('verifier.dashboard', compact('leads'));
    }
}
