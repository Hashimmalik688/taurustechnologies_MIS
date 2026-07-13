<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Staff-facing management of CC Partners — outsource sales companies.
 *
 * A CC Partner is a `partners` row with type='cc_partner' and login credentials.
 * They are distinct from affiliate partners/agents (no ledger, no commission);
 * they log into the portal, manage their own closers, and submit sales that
 * land in our pipeline. See docs/partner-portal.md.
 */
class CcPartnerController extends Controller
{
    public function index()
    {
        $ccPartners = Partner::ccPartners()
            ->withCount('closers')
            ->orderBy('name')
            ->get();

        return view('admin.cc-partners.index', compact('ccPartners'));
    }

    public function create()
    {
        return view('admin.cc-partners.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255', Rule::unique('partners', 'name')],
            'code'     => ['nullable', 'string', 'max:10', Rule::unique('partners', 'code')],
            'email'    => ['required', 'email', 'max:255', Rule::unique('partners', 'email')],
            'phone'    => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'max:255'],
        ]);

        Partner::create([
            'name'      => $data['name'],
            'code'      => $data['code'] ?: $this->generateCode($data['name']),
            'email'     => $data['email'],
            'phone'     => $data['phone'] ?? null,
            'password'  => $data['password'], // hashed via model cast
            'type'      => 'cc_partner',
            'is_active' => true,
        ]);

        return redirect()->route('admin.cc-partners.index')
            ->with('success', "CC Partner '{$data['name']}' created. They can now log in at the CC portal.");
    }

    public function edit(int $id)
    {
        $ccPartner = Partner::ccPartners()->findOrFail($id);

        return view('admin.cc-partners.edit', compact('ccPartner'));
    }

    public function update(Request $request, int $id)
    {
        $ccPartner = Partner::ccPartners()->findOrFail($id);

        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255', Rule::unique('partners', 'name')->ignore($ccPartner->id)],
            'code'     => ['required', 'string', 'max:10', Rule::unique('partners', 'code')->ignore($ccPartner->id)],
            'email'    => ['required', 'email', 'max:255', Rule::unique('partners', 'email')->ignore($ccPartner->id)],
            'phone'    => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'string', 'min:8', 'max:255'],
        ]);

        $ccPartner->name  = $data['name'];
        $ccPartner->code  = $data['code'];
        $ccPartner->email = $data['email'];
        $ccPartner->phone = $data['phone'] ?? null;
        if (! empty($data['password'])) {
            $ccPartner->password = $data['password'];
        }
        $ccPartner->save();

        return redirect()->route('admin.cc-partners.index')
            ->with('success', "CC Partner '{$ccPartner->name}' updated.");
    }

    public function toggleActive(int $id)
    {
        $ccPartner = Partner::ccPartners()->findOrFail($id);
        $ccPartner->update(['is_active' => ! $ccPartner->is_active]);

        $state = $ccPartner->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "CC Partner '{$ccPartner->name}' {$state}.");
    }

    protected function generateCode(string $name): string
    {
        $base = strtoupper(Str::of($name)->ascii()->replaceMatches('/[^A-Za-z]/', '')->substr(0, 3));
        $base = $base ?: 'CCP';

        do {
            $code = $base . strtoupper(Str::random(3));
        } while (Partner::where('code', $code)->exists());

        return $code;
    }
}
