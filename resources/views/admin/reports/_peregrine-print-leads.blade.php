<table class="ltbl">
    <thead>
        <tr>
            <th style="width:12px">#</th>
            <th>Customer</th>
            <th>Policy Type</th>
            <th>Carrier</th>
            <th>State</th>
            <th class="r">Coverage</th>
            <th class="r">Premium/mo</th>
            <th>Status</th>
            <th class="r">Sale Date</th>
        </tr>
    </thead>
    <tbody>
        @foreach($row->leads as $li => $lead)
            @php
                [$pc,$pl] = match($lead->sale_stage) {
                    'paid'       => ['b-paid','Paid'],
                    'approved'   => ['b-appr','Approved'],
                    'draft'      => ['b-drft','Draft'],
                    'not_issued' => ['b-niss','Not Issued'],
                    'declined'   => ['b-decl','Declined'],
                    default      => ['b-pend','Pending'],
                };
            @endphp
            <tr>
                <td style="color:#94a3b8">{{ $li+1 }}</td>
                <td style="font-weight:600">{{ $lead->cn_name ?? '—' }}</td>
                <td>{{ $lead->policy_type ?? '—' }}</td>
                <td>{{ $lead->carrier_name ?? '—' }}</td>
                <td>{{ $lead->state ?? '—' }}</td>
                <td class="r">${{ number_format($lead->coverage_amount??0) }}</td>
                <td class="r">${{ number_format($lead->monthly_premium??0,2) }}</td>
                <td><span class="b {{ $pc }}">{{ $pl }}</span></td>
                <td class="r" style="white-space:nowrap">{{ $lead->sale_at ? \Carbon\Carbon::parse($lead->sale_at)->format('M d, Y') : '—' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
