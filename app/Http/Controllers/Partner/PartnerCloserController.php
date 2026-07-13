<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Lets a partner company self-manage its closer logins.
 *
 * Each closer is a child Partner row (type=closer, parent_partner_id=company)
 * that authenticates on the same `partner` guard and submits sales.
 */
class PartnerCloserController extends Controller
{
    public function index()
    {
        $company = $this->company();

        $closers = $company->closers()
            ->orderBy('name')
            ->get();

        return view('partner.closers.index', compact('company', 'closers'));
    }

    public function store(Request $request)
    {
        $company = $this->company();

        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', Rule::unique('partners', 'email')],
            'phone'    => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'string', 'min:8', 'max:255'],
        ]);

        Partner::create([
            'name'              => $data['name'],
            'email'             => $data['email'],
            'phone'             => $data['phone'] ?? null,
            'password'          => $data['password'], // hashed via model cast
            'code'              => $this->generateCloserCode($company),
            'type'              => 'closer',
            'parent_partner_id' => $company->id,
            'is_active'         => true,
        ]);

        return back()->with('success', "Closer '{$data['name']}' created. They can now log in and submit sales.");
    }

    public function toggleActive(Request $request, int $id)
    {
        $company = $this->company();
        $closer  = $company->closers()->findOrFail($id);

        $closer->update(['is_active' => ! $closer->is_active]);

        $state = $closer->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "Closer '{$closer->name}' {$state}.");
    }

    public function resetPassword(Request $request, int $id)
    {
        $company = $this->company();
        $closer  = $company->closers()->findOrFail($id);

        $data = $request->validate([
            'password' => ['required', 'string', 'min:8', 'max:255'],
        ]);

        $closer->update(['password' => $data['password']]);

        return back()->with('success', "Password updated for '{$closer->name}'.");
    }

    /**
     * The authenticated company, or 403 if the current partner isn't a company.
     */
    protected function company(): Partner
    {
        $partner = Auth::guard('partner')->user();

        abort_unless($partner && $partner->isCcPartner(), 403, 'Only a partner company can manage closers.');

        return $partner;
    }

    /**
     * Generate a unique closer code derived from the company code.
     */
    protected function generateCloserCode(Partner $company): string
    {
        $prefix = strtoupper($company->code ?: 'PTR');

        do {
            $code = $prefix . '-C' . strtoupper(Str::random(4));
        } while (Partner::where('code', $code)->exists());

        return $code;
    }
}
