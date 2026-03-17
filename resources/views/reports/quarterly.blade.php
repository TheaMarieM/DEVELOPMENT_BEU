<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quarterly Discipline Report - Q{{ $quarter }} {{ $year }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #000;
            background: #fff;
        }

        .container {
            max-width: 8.5in;
            margin: 0 auto;
            padding: 0.5in;
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px double #000;
        }

        .school-name {
            font-size: 18pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #1a472a;
        }

        .school-address {
            font-size: 10pt;
            color: #333;
            margin-top: 5px;
        }

        .document-title {
            font-size: 16pt;
            font-weight: bold;
            margin-top: 15px;
            text-transform: uppercase;
        }

        .report-period {
            font-size: 14pt;
            color: #1a472a;
            margin-top: 5px;
        }

        .quarter-dates {
            font-size: 11pt;
            color: #666;
        }

        /* Executive Summary */
        .executive-summary {
            background: #f0fdf4;
            border: 2px solid #1a472a;
            padding: 20px;
            margin: 20px 0;
        }

        .exec-title {
            font-size: 14pt;
            font-weight: bold;
            color: #1a472a;
            margin-bottom: 10px;
        }

        .exec-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        .exec-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .exec-number {
            font-size: 24pt;
            font-weight: bold;
            color: #1a472a;
        }

        .exec-label {
            font-size: 10pt;
            color: #666;
        }

        /* Key Metrics */
        .metrics-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin: 25px 0;
        }

        .metric-box {
            text-align: center;
            padding: 20px;
            border: 2px solid #ddd;
            background: #fafafa;
        }

        .metric-number {
            font-size: 28pt;
            font-weight: bold;
            color: #1a472a;
        }

        .metric-label {
            font-size: 9pt;
            text-transform: uppercase;
            color: #666;
            margin-top: 5px;
        }

        .metric-change {
            font-size: 10pt;
            margin-top: 5px;
            padding: 3px 8px;
            border-radius: 3px;
            display: inline-block;
        }

        .change-up { background: #fecaca; color: #991b1b; }
        .change-down { background: #d1fae5; color: #065f46; }
        .change-neutral { background: #e5e7eb; color: #374151; }

        /* Section Titles */
        .section-title {
            font-size: 14pt;
            font-weight: bold;
            margin: 25px 0 10px 0;
            padding-bottom: 5px;
            border-bottom: 2px solid #1a472a;
        }

        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }

        th, td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
            font-size: 10pt;
        }

        th {
            background: #1a472a;
            color: #fff;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background: #f5f5f5;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }

        /* Monthly Breakdown */
        .monthly-comparison {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin: 15px 0;
        }

        .month-card {
            border: 1px solid #ddd;
            padding: 15px;
            text-align: center;
            background: #fafafa;
        }

        .month-name {
            font-weight: bold;
            color: #1a472a;
            font-size: 12pt;
        }

        .month-count {
            font-size: 24pt;
            font-weight: bold;
            margin: 10px 0;
        }

        .month-detail {
            font-size: 9pt;
            color: #666;
        }

        /* Trend Chart Placeholder */
        .trend-visual {
            border: 1px solid #ddd;
            padding: 20px;
            margin: 15px 0;
            background: #fafafa;
        }

        .trend-bar-container {
            display: flex;
            align-items: flex-end;
            justify-content: space-around;
            height: 150px;
            padding: 10px;
        }

        .trend-bar-item {
            text-align: center;
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .trend-bar {
            width: 40px;
            background: #1a472a;
            border-radius: 3px 3px 0 0;
            transition: height 0.3s;
        }

        .trend-bar-label {
            margin-top: 5px;
            font-size: 9pt;
            color: #666;
        }

        /* Recommendations */
        .recommendations {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
        }

        .rec-title {
            font-weight: bold;
            color: #92400e;
            margin-bottom: 10px;
        }

        .rec-list {
            margin-left: 20px;
        }

        .rec-list li {
            margin: 5px 0;
            color: #78350f;
        }

        /* Footer */
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #333;
        }

        .signatures {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            margin-top: 30px;
        }

        .signature-block {
            text-align: center;
        }

        .signature-line {
            border-bottom: 1px solid #000;
            height: 40px;
            margin-bottom: 5px;
        }

        .signature-name {
            font-weight: bold;
            font-size: 10pt;
        }

        .signature-title {
            font-size: 9pt;
            color: #666;
        }

        .document-footer {
            margin-top: 30px;
            font-size: 9pt;
            color: #666;
            text-align: center;
        }

        /* Print Controls */
        .print-controls {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }

        .print-btn {
            background: #1a472a;
            color: #fff;
            border: none;
            padding: 12px 24px;
            font-size: 14px;
            cursor: pointer;
            border-radius: 8px;
            font-weight: bold;
        }

        .print-btn:hover {
            background: #0d2818;
        }

        .back-btn {
            background: #6b7280;
            color: #fff;
            border: none;
            padding: 12px 24px;
            font-size: 14px;
            cursor: pointer;
            border-radius: 8px;
            font-weight: bold;
            margin-right: 10px;
            text-decoration: none;
        }

        .back-btn:hover {
            background: #4b5563;
        }

        /* Print Styles */
        @media print {
            .print-controls {
                display: none !important;
            }

            body {
                background: #fff;
            }

            .container {
                padding: 0;
                max-width: 100%;
            }

            .metric-box, .month-card, .trend-visual, .executive-summary {
                background: #fff;
            }
        }
    </style>
</head>
<body>
    <!-- Print Controls -->
    <div class="print-controls">
        <a href="{{ route('reports.index') }}" class="back-btn">
            ← Back to Reports
        </a>
        <button onclick="window.print()" class="print-btn">
            🖨️ Print / Save as PDF
        </button>
    </div>

    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="school-name">{{ $school['name'] }}</div>
            <div class="school-address">{{ $school['address'] }}</div>
            <div class="school-address">{{ $school['contact'] }}</div>
            <div class="document-title">Quarterly Discipline Report</div>
            <div class="report-period">Quarter {{ $quarter }}, {{ $year }}</div>
            <div class="quarter-dates">{{ $period['start'] }} - {{ $period['end'] }}</div>
        </div>

        <!-- Executive Summary -->
        <div class="executive-summary">
            <div class="exec-title">Executive Summary</div>
            <div class="exec-grid">
                <div class="exec-item">
                    <div class="exec-number">{{ $stats['total_incidents'] }}</div>
                    <div>
                        <div style="font-weight: bold;">Total Incidents</div>
                        <div class="exec-label">Recorded this quarter</div>
                    </div>
                </div>
                <div class="exec-item">
                    <div class="exec-number">{{ $stats['students_involved'] }}</div>
                    <div>
                        <div style="font-weight: bold;">Students Involved</div>
                        <div class="exec-label">Unique students</div>
                    </div>
                </div>
                <div class="exec-item">
                    <div class="exec-number">{{ $stats['resolution_rate'] }}%</div>
                    <div>
                        <div style="font-weight: bold;">Resolution Rate</div>
                        <div class="exec-label">Cases resolved</div>
                    </div>
                </div>
                <div class="exec-item">
                    <div class="exec-number">{{ $stats['avg_resolution_days'] }}</div>
                    <div>
                        <div style="font-weight: bold;">Avg. Days to Resolve</div>
                        <div class="exec-label">From report to resolution</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Key Metrics Row -->
        <div class="metrics-row">
            <div class="metric-box">
                <div class="metric-number">{{ $severity['high'] ?? 0 }}</div>
                <div class="metric-label">High Severity</div>
                <div class="metric-change {{ ($severity['high'] ?? 0) > ($prev_quarter['high'] ?? 0) ? 'change-up' : (($severity['high'] ?? 0) < ($prev_quarter['high'] ?? 0) ? 'change-down' : 'change-neutral') }}">
                    @if(($severity['high'] ?? 0) > ($prev_quarter['high'] ?? 0))
                        ↑ {{ ($severity['high'] ?? 0) - ($prev_quarter['high'] ?? 0) }} vs Q{{ $quarter - 1 ?: 4 }}
                    @elseif(($severity['high'] ?? 0) < ($prev_quarter['high'] ?? 0))
                        ↓ {{ ($prev_quarter['high'] ?? 0) - ($severity['high'] ?? 0) }} vs Q{{ $quarter - 1 ?: 4 }}
                    @else
                        No change
                    @endif
                </div>
            </div>
            <div class="metric-box">
                <div class="metric-number">{{ $severity['medium'] ?? 0 }}</div>
                <div class="metric-label">Medium Severity</div>
            </div>
            <div class="metric-box">
                <div class="metric-number">{{ $severity['low'] ?? 0 }}</div>
                <div class="metric-label">Low Severity</div>
            </div>
            <div class="metric-box">
                <div class="metric-number">{{ $stats['repeat_offenders'] ?? 0 }}</div>
                <div class="metric-label">Repeat Offenders</div>
            </div>
        </div>

        <!-- Monthly Breakdown -->
        <h3 class="section-title">Monthly Breakdown</h3>
        <div class="monthly-comparison">
            @foreach($monthly_data as $month)
                <div class="month-card">
                    <div class="month-name">{{ $month['name'] }}</div>
                    <div class="month-count">{{ $month['count'] }}</div>
                    <div class="month-detail">
                        High: {{ $month['high'] }} | Med: {{ $month['medium'] }} | Low: {{ $month['low'] }}
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Visual Trend -->
        @php
            $maxCount = max(array_column($monthly_data, 'count')) ?: 1;
        @endphp
        <div class="trend-visual">
            <div style="font-weight: bold; margin-bottom: 10px; color: #1a472a;">Incident Trend Visualization</div>
            <div class="trend-bar-container">
                @foreach($monthly_data as $month)
                    <div class="trend-bar-item">
                        <div class="trend-bar" style="height: {{ ($month['count'] / $maxCount) * 120 }}px;"></div>
                        <div class="trend-bar-label">{{ $month['short_name'] }}<br>{{ $month['count'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Top Categories -->
        <h3 class="section-title">Top Violation Categories</h3>
        <table>
            <thead>
                <tr>
                    <th style="width: 40px;">#</th>
                    <th>Category</th>
                    <th class="text-center">Incidents</th>
                    <th class="text-center">High</th>
                    <th class="text-center">Medium</th>
                    <th class="text-center">Low</th>
                </tr>
            </thead>
            <tbody>
                @forelse($by_category as $index => $category)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $category['name'] }}</td>
                        <td class="text-center"><strong>{{ $category['total'] }}</strong></td>
                        <td class="text-center">{{ $category['high'] }}</td>
                        <td class="text-center">{{ $category['medium'] }}</td>
                        <td class="text-center">{{ $category['low'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center" style="font-style: italic; color: #666;">
                            No categorized incidents this quarter
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Grade Level Analysis -->
        <h3 class="section-title">Grade Level Analysis</h3>
        <table>
            <thead>
                <tr>
                    <th>Grade Level</th>
                    <th class="text-center">Total Incidents</th>
                    <th class="text-center">Students Involved</th>
                    <th class="text-center">Incidents per Student</th>
                </tr>
            </thead>
            <tbody>
                @foreach($by_grade as $grade)
                    <tr>
                        <td><strong>Grade {{ $grade['level'] }}</strong></td>
                        <td class="text-center">{{ $grade['incidents'] }}</td>
                        <td class="text-center">{{ $grade['students'] }}</td>
                        <td class="text-center">{{ number_format($grade['per_student'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Recommendations -->
        @if(count($recommendations ?? []) > 0)
            <div class="recommendations">
                <div class="rec-title">
                    <i style="margin-right: 5px;">📋</i> Recommendations for Next Quarter
                </div>
                <ul class="rec-list">
                    @foreach($recommendations as $rec)
                        <li>{{ $rec }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <div class="signatures">
                <div class="signature-block">
                    <div class="signature-line"></div>
                    <div class="signature-name">Prepared By</div>
                    <div class="signature-title">Discipline Officer</div>
                </div>
                <div class="signature-block">
                    <div class="signature-line"></div>
                    <div class="signature-name">Reviewed By</div>
                    <div class="signature-title">Guidance Counselor</div>
                </div>
                <div class="signature-block">
                    <div class="signature-line"></div>
                    <div class="signature-name">Approved By</div>
                    <div class="signature-title">School Principal</div>
                </div>
            </div>

            <div class="document-footer">
                <p><em>This document is an official quarterly report from {{ $school['name'] }}.</em></p>
                <p>Generated on {{ $generated_at }} | Behavioral Evaluation Unit (BEU) System</p>
                <p style="margin-top: 10px;">Document Reference: QR-{{ $year }}-Q{{ $quarter }}-{{ date('Ymd') }}</p>
            </div>
        </div>
    </div>
</body>
</html>
