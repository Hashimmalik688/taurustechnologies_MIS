<table class="stbl">
    <thead>
        <tr>
            <th style="width:16px">#</th>
            <th>Agent</th>
            <th class="r">Sales</th>
            <th class="r">Paid</th>
            <th class="r">Approved</th>
            <th class="r">Draft</th>
            <th class="r">Not Issued</th>
            <th class="r">Declined</th>
            <th class="r">Paid Premium/mo</th>
            <th class="r" style="min-width:70pt">Paid Rate</th>
        </tr>
    </thead>
    <tbody>
        @forelse($rows as $i => $row)
            @php $rc = $row->paid_rate >= 50 ? 'g' : ($row->paid_rate >= 20 ? 'a' : 'r'); @endphp
            <tr class="{{ $i===0 ? 'top1' : '' }}">
                <td>
                    @if($i===0)<span class="rk rk1">1</span>
                    @elseif($i===1)<span class="rk rk2">2</span>
                    @elseif($i===2)<span class="rk rk3">3</span>
                    @else<span class="rk rkn">{{ $i+1 }}</span>@endif
                </td>
                <td style="font-weight:700">{{ $row->agent_name }}@if($i===0 && $rows->count()>1) <span style="color:#d4af37">★</span>@endif</td>
                <td class="r"><span class="b b-tot">{{ $row->total }}</span></td>
                <td class="r"><span class="b {{ $row->paid>0?'b-paid':'b-zero' }}">{{ $row->paid }}</span></td>
                <td class="r"><span class="b {{ $row->approved>0?'b-appr':'b-zero' }}">{{ $row->approved }}</span></td>
                <td class="r"><span class="b {{ $row->draft>0?'b-drft':'b-zero' }}">{{ $row->draft }}</span></td>
                <td class="r"><span class="b {{ $row->not_issued>0?'b-niss':'b-zero' }}">{{ $row->not_issued }}</span></td>
                <td class="r"><span class="b {{ $row->declined>0?'b-decl':'b-zero' }}">{{ $row->declined }}</span></td>
                <td class="r">${{ number_format($row->premium,2) }}</td>
                <td class="r">
                    <div class="rb">
                        <div class="rt"><div class="rf c-{{ $rc }}" style="width:{{ $row->paid_rate }}%"></div></div>
                        <span class="rl t-{{ $rc }}">{{ $row->paid_rate }}%</span>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="10" style="text-align:center;padding:8px;color:#64748b">No data</td></tr>
        @endforelse
    </tbody>
    @if($rows->count() > 0)
    <tfoot>
        <tr>
            <td colspan="2">Total</td>
            <td class="r">{{ $rows->sum('total') }}</td>
            <td class="r">{{ $rows->sum('paid') }}</td>
            <td class="r">{{ $rows->sum('approved') }}</td>
            <td class="r">{{ $rows->sum('draft') }}</td>
            <td class="r">{{ $rows->sum('not_issued') }}</td>
            <td class="r">{{ $rows->sum('declined') }}</td>
            <td class="r">${{ number_format($rows->sum('premium'),2) }}</td>
            <td class="r">@php $t=$rows->sum('total');$p=$rows->sum('paid'); @endphp{{ $t>0?round(($p/$t)*100,1):0 }}%</td>
        </tr>
    </tfoot>
    @endif
</table>
