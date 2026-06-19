<div class="print-agent-block">
    <div class="print-agent-block-hdr {{ $headerClass }}">
        <div>
            <div class="a-name">
                @if($rank===1)★ @endif{{ $row->agent_name }}
            </div>
            <div class="a-role">{{ $role }} · {{ $row->total }} sale(s) · Paid rate: {{ $row->paid_rate }}%</div>
        </div>
        <div class="a-stats">
            <div class="a-stat"><div class="a-stat-n" style="color:#34c38f">{{ $row->paid }}</div><div class="a-stat-l">Paid</div></div>
            <div class="a-stat"><div class="a-stat-n" style="color:#50a5f1">{{ $row->approved }}</div><div class="a-stat-l">Approved</div></div>
            <div class="a-stat"><div class="a-stat-n" style="color:#f1b44c">{{ $row->draft }}</div><div class="a-stat-l">Draft</div></div>
            <div class="a-stat"><div class="a-stat-n" style="color:#fb923c">{{ $row->not_issued }}</div><div class="a-stat-l">Not Issued</div></div>
            <div class="a-stat"><div class="a-stat-n" style="color:#f46a6a">{{ $row->declined }}</div><div class="a-stat-l">Declined</div></div>
            <div class="a-stat"><div class="a-stat-n" style="color:#d4af37">${{ number_format($row->premium,2) }}</div><div class="a-stat-l">Paid/mo</div></div>
        </div>
    </div>
    <table class="print-leads-table">
        <thead>
            <tr>
                <th style="width:14pt">#</th>
                <th>Customer</th>
                <th>Policy Type</th>
                <th>Carrier</th>
                <th>State</th>
                <th class="th-r">Coverage</th>
                <th class="th-r">Premium/mo</th>
                <th>Status</th>
                <th class="th-r">Sale Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($row->leads as $li => $lead)
                @php
                    [$pc,$pl] = match($lead->sale_stage) {
                        'paid'       => ['pb-paid','Paid'],
                        'approved'   => ['pb-appr','Approved'],
                        'draft'      => ['pb-draft','Draft'],
                        'not_issued' => ['pb-niss','Not Issued'],
                        'declined'   => ['pb-decl','Declined'],
                        default      => ['pb-zero','Pending'],
                    };
                @endphp
                <tr>
                    <td style="color:#94a3b8">{{ $li+1 }}</td>
                    <td style="font-weight:600">{{ $lead->cn_name ?? '—' }}</td>
                    <td>{{ $lead->policy_type ?? '—' }}</td>
                    <td>{{ $lead->carrier_name ?? '—' }}</td>
                    <td>{{ $lead->state ?? '—' }}</td>
                    <td class="td-r">${{ number_format($lead->coverage_amount??0) }}</td>
                    <td class="td-r">${{ number_format($lead->monthly_premium??0,2) }}</td>
                    <td><span class="p-badge {{ $pc }}">{{ $pl }}</span></td>
                    <td class="td-r" style="white-space:nowrap">
                        {{ $lead->sale_at ? \Carbon\Carbon::parse($lead->sale_at)->format('M d, Y') : '—' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
