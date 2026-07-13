<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;

/**
 * Partner-side sale submission.
 *
 * Reuses the exact validation rules a Peregrine/PJC closer sees internally
 * (StoreLeadRequest) so a partner company's closers fill the identical form,
 * but authorizes against the `partner` guard instead of the employee guard.
 */
class StorePartnerSaleRequest extends StoreLeadRequest
{
    public function authorize(): bool
    {
        $partner = Auth::guard('partner')->user();

        // Only an active partner company or one of its closers may submit a sale.
        return $partner
            && $partner->is_active
            && ($partner->isCcPartner() || $partner->isCloser());
    }
}
