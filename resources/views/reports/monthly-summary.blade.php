<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Discipline Summary - {{ $month_name }} {{ $year }}</title>
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

        /* Summary Stats */
        .summary-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin: 25px 0;
        }

        .stat-box {
            text-align: center;
            padding: 20px;
            border: 2px solid #1a472a;
            background: #fafafa;
        }

        .stat-number {
            font-size: 28pt;
            font-weight: bold;
            color: #1a472a;
        }

        .stat-label {
            font-size: 9pt;
            text-transform: uppercase;
            color: #666;
            margin-top: 5px;
        }

        .stat-compare {
            font-size: 9pt;
            margin-top: 5px;
        }

        .stat-up { color: #dc2626; }
        .stat-down { color: #10b981; }

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

        /* Grade Level Grid */
        .grade-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin: 15px 0;
        }

        .grade-box {
            border: 1px solid #ddd;
            padding: 15px;
            text-align: center;
            background: #fafafa;
        }

        .grade-label {
            font-weight: bold;
            color: #1a472a;
        }

        .grade-count {
            font-size: 20pt;
            font-weight: bold;
            margin: 5px 0;
        }

        /* Trends */
        .trend-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        .trend-label {
            font-weight: bold;
        }

        .trend-bar {
            height: 20px;
            background: #1a472a;
            border-radius: 3px;
        }

        /* Footer */
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #333;
        }

        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
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
        }

        .signature-title {
            font-size: 10pt;
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

            .stat-box, .grade-box {
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
            <div class="document-title">Monthly Discipline Summary</div>
            <div class="report-period">{{ $month_name }} {{ $year }}</div>
        </div>

        <!-- Key Statistics -->
        <div class="summary-stats">
            <div class="stat-box">
                <div class="stat-number">{{ $stats['total_incidents'] }}</div>
                <div class="stat-label">Total Incidents</div>
                @if(isset($stats['previous_month']))
                    <div class="stat-compare {{ $stats['total_incidents'] > $stats['previous_month'] ? 'stat-up' : 'stat-down' }}">
                        {{ $stats['total_incidents'] > $stats['previous_month'] ? '↑' : '↓' }}
                        {{ abs($stats['total_incidents'] - $stats['previous_month']) }} vs prev. month
                    </div>
                @endif
            </div>
            <div class="stat-box">
                <div class="stat-number">{{ $stats['pending'] }}</div>
                <div class="stat-label">Pending Review</div>
            </div>
            <div class="stat-box">
                <div class="stat-number">{{ $stats['approved'] }}</div>
                <div class="stat-label">Approved</div>
            </div>
            <div class="stat-box">
                <div class="stat-number">{{ $stats['resolved'] }}</div>
                <div class="stat-label">Resolved</div>
            </div>
        </div>

        <!-- Severity Breakdown -->
        <h3 class="section-title">Incidents by Severity</h3>
        <table>
            <thead>
                <tr>
                    <th>Severity Level</th>
                    <th class="text-center">Count</th>
                    <th class="text-center">Percentage</th>
                    <th>Visual</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total = $stats['total_incidents'] ?: 1;
                @endphp
                <tr>
                    <td><strong style="color: #dc2626;">HIGH</strong></td>
                    <td class="text-center">{{ $severity['high'] ?? 0 }}</td>
                    <td class="text-center">{{ number_format((($severity['high'] ?? 0) / $total) * 100, 1) }}%</td>
                    <td>
                        <div class="trend-bar" style="width: {{ (($severity['high'] ?? 0) / $total) * 100 }}%; background: #dc2626;"></div>
                    </td>
                </tr>
                <tr>
                    <td><strong style="color: #f59e0b;">MEDIUM</strong></td>
                    <td class="text-center">{{ $severity['medium'] ?? 0 }}</td>
                    <td class="text-center">{{ number_format((($severity['medium'] ?? 0) / $total) * 100, 1) }}%</td>
                    <td>
                        <div class="trend-bar" style="width: {{ (($severity['medium'] ?? 0) / $total) * 100 }}%; background: #f59e0b;"></div>
                    </td>
                </tr>
                <tr>
                    <td><strong style="color: #10b981;">LOW</strong></td>
                    <td class="text-center">{{ $severity['low'] ?? 0 }}</td>
                    <td class="text-center">{{ number_format((($severity['low'] ?? 0) / $total) * 100, 1) }}%</td>
                    <td>
                        <div class="trend-bar" style="width: {{ (($severity['low'] ?? 0) / $total) * 100 }}%; background: #10b981;"></div>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Grade Level Distribution -->
        <h3 class="section-title">Incidents by Grade Level</h3>
        <div class="grade-grid">
            @foreach($by_grade as $grade => $count)
                <div class="grade-box">
                    <div class="grade-label">Grade {{ $grade }}</div>
                    <div class="grade-count">{{ $count }}</div>
                    <div style="font-size: 9pt; color: #666;">{{ number_format(($count / $total) * 100, 1) }}% of total</div>
                </div>
            @endforeach
        </div>

        <!-- Top Categories -->
        <h3 class="section-title">Top Violation Categories</h3>
        <table>
            <thead>
                <tr>
                    <th style="width: 40px;">#</th>
                    <th>Category</th>
                    <th class="text-center">Incidents</th>
                    <th class="text-center">% of Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($by_category as $index => $category)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $category->name ?? 'Uncategorized' }}</td>
                        <td class="text-center">{{ $category->count }}</td>
                        <td class="text-center">{{ number_format(($category->count / $total) * 100, 1) }}%</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center" style="font-style: italic; color: #666;">
                            No categorized incidents this month
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Recent High-Priority Incidents -->
        @if(count($high_priority) > 0)
            <h3 class="section-title">High Priority Incidents Requiring Attention</h3>
            <table>
                <thead>
                    <tr>
                        <th style="width: 80px;">Date</th>
                        <th>Description</th>
                        <th style="width: 100px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($high_priority as $incident)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($incident->incident_date)->format('M d') }}</td>
                            <td>{{ Str::limit($incident->description, 80) }}</td>
                            <td>{{ ucfirst($incident->status) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
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
                    <div class="signature-name">Noted By</div>
                    <div class="signature-title">School Principal</div>
                </div>
            </div>

            <div class="document-footer">
                <p><em>This document is an official monthly report from {{ $school['name'] }}.</em></p>
                <p>Generated on {{ $generated_at }} | Behavioral Evaluation Unit (BEU) System</p>
                <p style="margin-top: 10px;">Document Reference: MS-{{ $year }}{{ str_pad($month, 2, '0', STR_PAD_LEFT) }}-{{ date('Ymd') }}</p>
            </div>
        </div>
    </div>
</body>
</html>
