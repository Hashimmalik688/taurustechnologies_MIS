@if(count($rows) > 0)
    <div class="rp-results-hdr">
        <h6><i class="bx bx-phone-call"></i> Per-Closer Performance <span>({{ count($rows) }} closers &bull; {{ $startDate }} &ndash; {{ $endDate }} PT)</span></h6>
    </div>
    <div class="table-responsive" style="padding-bottom:.25rem">
        <table class="rp-table" id="closerStatsTable">
            <thead>
                <tr>
                    <th class="rp-th-name">Closer</th>
                    <th>Team</th>
                    <th class="rp-th-num">Total Dialed</th>
                    <th class="rp-th-num">Connected</th>
                    <th class="rp-th-num">Disposed</th>
                    <th class="rp-th-num">Sales</th>
                    <th class="rp-th-num">Contact Rate</th>
                    <th class="rp-th-num">Conversion Rate</th>
                    <th class="rp-th-num">Disposal Rate</th>
                    <th class="rp-th-num">Sales Rate</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rows as $row)
                    <tr>
                        <td class="rp-td-name">{{ $row['name'] }}</td>
                        <td>
                            <span class="rp-badge {{ $row['team'] === 'Ravens' ? 'rp-badge-sale' : 'rp-badge-accepted' }}">
                                {{ $row['team'] }}
                            </span>
                        </td>
                        <td class="rp-td-num">{{ number_format($row['dialed']) }}</td>
                        <td class="rp-td-num">{{ number_format($row['connected']) }}</td>
                        <td class="rp-td-num">{{ number_format($row['disposed']) }}</td>
                        <td class="rp-td-num" style="font-weight:700;color:#1a8754">{{ number_format($row['sales']) }}</td>
                        <td class="rp-td-num">
                            <span class="rp-badge {{ $row['contact_rate'] >= 30 ? 'rp-badge-sale' : ($row['contact_rate'] >= 15 ? 'rp-badge-pending' : 'rp-badge-declined') }}">
                                {{ $row['contact_rate'] }}%
                            </span>
                        </td>
                        <td class="rp-td-num">
                            <span class="rp-badge {{ $row['conversion_rate'] >= 20 ? 'rp-badge-sale' : ($row['conversion_rate'] >= 10 ? 'rp-badge-pending' : 'rp-badge-declined') }}">
                                {{ $row['conversion_rate'] }}%
                            </span>
                        </td>
                        <td class="rp-td-num">
                            <span class="rp-badge rp-badge-default">{{ $row['disposal_rate'] }}%</span>
                        </td>
                        <td class="rp-td-num">
                            <span class="rp-badge {{ $row['sales_rate'] >= 5 ? 'rp-badge-sale' : ($row['sales_rate'] >= 2 ? 'rp-badge-pending' : 'rp-badge-declined') }}">
                                {{ $row['sales_rate'] }}%
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background:rgba(212,175,55,.06);font-weight:700">
                    <td>TOTAL</td>
                    <td></td>
                    <td class="rp-td-num">{{ number_format($totals['dialed']) }}</td>
                    <td class="rp-td-num">{{ number_format($totals['connected']) }}</td>
                    <td class="rp-td-num">{{ number_format($totals['disposed']) }}</td>
                    <td class="rp-td-num" style="color:#1a8754">{{ number_format($totals['sales']) }}</td>
                    <td class="rp-td-num">{{ $totals['contact_rate'] }}%</td>
                    <td class="rp-td-num">{{ $totals['conversion_rate'] }}%</td>
                    <td class="rp-td-num">{{ $totals['disposal_rate'] }}%</td>
                    <td class="rp-td-num">{{ $totals['sales_rate'] }}%</td>
                </tr>
            </tfoot>
        </table>
    </div>
@else
    <div class="rp-empty">
        <i class="bx bx-phone-off"></i>
        <h6>No closer activity found</h6>
        <p>No dial data for the selected date range</p>
    </div>
@endif
