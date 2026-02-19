<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Print - {{ $periodStart->format('d M Y') }} to {{ $periodEnd->format('d M Y') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Calibri, 'Segoe UI', Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: var(--bs-surface-900);
            background: white;
            padding: 15px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid var(--bs-surface-900);
            padding-bottom: 12px;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: var(--bs-surface-900);
            margin-bottom: 5px;
            letter-spacing: 1px;
        }

        .company-subtext {
            font-size: 12px;
            color: var(--bs-surface-700);
            margin-bottom: 8px;
        }

        .report-title {
            font-size: 15px;
            font-weight: 600;
            color: var(--bs-surface-700);
            margin-bottom: 3px;
        }

        .report-info {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            font-size: 11px;
            margin-bottom: 15px;
            background: var(--bs-surface-50);
            padding: 10px;
            border: 1px solid var(--bs-surface-200);
        }

        .info-item {
            display: flex;
            flex-direction: column;
        }

        .info-label {
            font-weight: bold;
            color: var(--bs-surface-700);
            margin-bottom: 2px;
            font-size: 10px;
        }

        .info-value {
            color: var(--bs-surface-900);
            font-weight: 600;
            font-size: 12px;
        }

        .print-buttons {
            text-align: center;
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        .print-buttons button,
        .print-buttons a {
            padding: 10px 20px;
            background: var(--bs-surface-700);
            color: white;
            border: 1px solid var(--bs-surface-900);
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .print-buttons button:hover,
        .print-buttons a:hover {
            background: var(--bs-surface-900);
        }

        .back-btn {
            background: var(--bs-surface-500) !important;
        }

        .back-btn:hover {
            background: var(--bs-surface-500) !important;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border: 1px solid var(--bs-surface-900);
        }

        th {
            background-color: var(--bs-surface-700);
            color: var(--bs-white, #fff);
            font-weight: bold;
            padding: 6px 3px;
            text-align: center;
            border: 1px solid var(--bs-surface-900);
            font-size: 10px;
            white-space: nowrap;
        }

        td {
            padding: 5px 3px;
            border: 1px solid var(--bs-surface-700);
            font-size: 10px;
            text-align: center;
        }

        tr:nth-child(even) {
            background-color: var(--bs-surface-50);
        }

        .text-left {
            text-align: left !important;
        }

        /* Status color coding */
        .status-p {
            background-color: var(--bs-surface-50) !important;
            color: var(--bs-ui-success-dark);
            font-weight: 600;
        }

        .status-l {
            background-color: var(--bs-surface-50) !important;
            color: var(--bs-gold-dark);
            font-weight: 600;
        }

        .status-a {
            background-color: var(--bs-surface-50) !important;
            color: var(--bs-ui-danger-dark);
            font-weight: 600;
        }

        .status-pl {
            background-color: var(--bs-surface-100) !important;
            color: var(--bs-ui-info-dark);
            font-weight: 600;
        }

        .status-h {
            background-color: var(--bs-surface-200) !important;
            color: var(--bs-surface-600);
            font-weight: 600;
        }

        .status-weekend {
            background-color: var(--bs-status-absent) !important;
            color: var(--bs-white, #fff);
            font-weight: 600;
        }

        .terminated-row {
            background-color: var(--bs-surface-50) !important;
            opacity: 0.85;
        }

        .terminated-badge {
            color: var(--bs-ui-danger-dark);
            font-weight: 600;
            font-size: 9px;
            margin-left: 3px;
        }

        .weekend-header {
            background-color: var(--bs-status-absent) !important;
            color: var(--bs-white, #fff) !important;
        }

        .totals-cell {
            font-weight: 700;
            background-color: var(--bs-surface-200) !important;
        }

        .legend {
            margin: 12px 0;
            padding: 8px;
            background-color: var(--bs-surface-bg-light);
            border: 1px solid var(--bs-surface-200);
            border-radius: 4px;
        }

        .legend-title {
            font-weight: 600;
            margin-bottom: 6px;
            font-size: 12px;
        }

        .legend-items {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
        }

        .legend-box {
            width: 20px;
            height: 20px;
            border: 1px solid var(--bs-surface-900);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 10px;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            body {
                padding: 10px;
            }
            .print-buttons {
                display: none;
            }
            th {
                background-color: var(--bs-surface-700) !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .weekend-header {
                background-color: var(--bs-status-absent) !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .status-p, .status-l, .status-a, .status-pl, .status-h, .status-weekend {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            table {
                page-break-inside: auto;
            }
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            thead {
                display: table-header-group;
            }
        }
    </style>
</head>
<body>
    <div class="print-buttons no-print">
        <button onclick="window.print()">
            🖨️ Print This Page
        </button>
        <a href="{{ route('attendance.index') }}" class="back-btn">
            ← Back to Attendance
        </a>
    </div>

    <!-- HEADER SECTION -->
    <div class="header">
        <div class="company-name">TAURUS TECHNOLOGIES</div>
        <div class="company-subtext">Employee Attendance System</div>
        <div class="report-title">📅 Attendance Record: {{ $periodStart->format('d M Y') }} - {{ $periodEnd->format('d M Y') }}</div>
    </div>

    <!-- REPORT INFO SECTION -->
    <div class="report-info">
        <div class="info-item">
            <span class="info-label">Report Type:</span>
            <span class="info-value">Attendance Report</span>
        </div>
        <div class="info-item">
            <span class="info-label">Generated By:</span>
            <span class="info-value">{{ Auth::user()->name }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Date:</span>
            <span class="info-value">{{ now()->format('d M Y, h:i A') }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Total Employees:</span>
            <span class="info-value">{{ count($employeeData) }}</span>
        </div>
    </div>

    <!-- Legend -->
    <div class="legend">
        <div class="legend-title">Status Legend:</div>
        <div class="legend-items">
            <div class="legend-item">
                <span class="legend-box status-p">P</span>
                <span>Present</span>
            </div>
            <div class="legend-item">
                <span class="legend-box status-l">L</span>
                <span>Late Arrival</span>
            </div>
            <div class="legend-item">
                <span class="legend-box status-a">A</span>
                <span>Absent</span>
            </div>
            <div class="legend-item">
                <span class="legend-box status-pl">PL</span>
                <span>Paid Leave</span>
            </div>
            <div class="legend-item">
                <span class="legend-box status-h">H</span>
                <span>Half Day</span>
            </div>
            <div class="legend-item">
                <span class="legend-box status-weekend">-</span>
                <span>Weekend/Holiday</span>
            </div>
        </div>
    </div>

    <!-- ATTENDANCE TABLE -->
    <table>
        <thead>
            <tr>
                <th rowspan="2" style="width: 25px;">S.No</th>
                <th rowspan="2" class="u-w-40">ID</th>
                <th rowspan="2" class="u-w-140">Name</th>
                <th rowspan="2" style="width: 110px;">Position</th>
                <th colspan="{{ count($dates) }}">Dates</th>
                <th colspan="5">Totals</th>
            </tr>
            <tr>
                @foreach($dates as $date)
                    @php
                        $isWeekend = in_array($date->dayOfWeek, [0, 6]);
                    @endphp
                    <th style="width: 18px;" class="{{ $isWeekend ? 'weekend-header' : '' }}" title="{{ $date->format('D, M d') }}">
                        {{ $date->format('d') }}
                    </th>
                @endforeach
                <th class="u-w-30">P</th>
                <th class="u-w-30">L</th>
                <th class="u-w-30">A</th>
                <th class="u-w-30">PL</th>
                <th class="u-w-30">H</th>
            </tr>
        </thead>
        <tbody>
            @forelse($employeeData as $employee)
                <tr class="{{ $employee['is_terminated'] ? 'terminated-row' : '' }}">
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $employee['id'] }}</td>
                    <td class="text-left">
                        {{ $employee['name'] }}
                        @if($employee['is_terminated'])
                            <span class="terminated-badge">(Terminated)</span>
                        @endif
                    </td>
                    <td class="text-left">{{ $employee['position'] }}</td>
                    
                    @foreach($dates as $date)
                        @php
                            $dateKey = $date->format('Y-m-d');
                            $status = $employee['daily_attendance'][$dateKey] ?? '-';
                            $statusClass = match($status) {
                                'P' => 'status-p',
                                'L' => 'status-l',
                                'A' => 'status-a',
                                'PL' => 'status-pl',
                                'H' => 'status-h',
                                default => 'status-weekend'
                            };
                        @endphp
                        <td class="{{ $statusClass }}">{{ $status }}</td>
                    @endforeach
                    
                    <td class="totals-cell">{{ $employee['totals']['P'] }}</td>
                    <td class="totals-cell">{{ $employee['totals']['L'] }}</td>
                    <td class="totals-cell">{{ $employee['totals']['A'] }}</td>
                    <td class="totals-cell">{{ $employee['totals']['PL'] }}</td>
                    <td class="totals-cell">{{ $employee['totals']['H'] }}</td>
                </tr>
            @empty
                <tr>
 <td class="text-center p-4" colspan="{{ count($dates) + 9 }}">
                        No employee data available for this period.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Summary Section -->
    <div class="u-fs-10 text-surface-500" style="margin-top: 20px">
        <strong>Note:</strong> This report includes all trackable employees. Weekends (Saturday & Sunday) and public holidays are marked with (-) and highlighted in red.
    </div>
</body>
</html>
