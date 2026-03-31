<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Discipline Record - {{ $student['name'] }}</title>
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
            text-decoration: underline;
        }

        .student-info {
            margin: 20px 0;
            padding: 15px;
            background: #f9f9f9;
            border: 1px solid #ddd;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .info-item {
            display: flex;
        }

        .info-label {
            font-weight: bold;
            min-width: 140px;
        }

        .summary-stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin: 20px 0;
        }

        .stat-box {
            text-align: center;
            padding: 15px;
            border: 1px solid #ddd;
            background: #fafafa;
        }

        .stat-number {
            font-size: 24pt;
            font-weight: bold;
            color: #1a472a;
        }

        .stat-label {
            font-size: 9pt;
            text-transform: uppercase;
            color: #666;
        }

        .section-title {
            font-size: 14pt;
            font-weight: bold;
            margin: 25px 0 10px 0;
            padding-bottom: 5px;
            border-bottom: 2px solid #1a472a;
        }

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

        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 9pt;
            font-weight: bold;
        }

        .status-pending { background: #fef3c7; color: #92400e; }
        .status-approved { background: #d1fae5; color: #065f46; }
        .status-resolved { background: #dbeafe; color: #1e40af; }
        .status-neutral { background: #e5e7eb; color: #374151; }

        .sanction-card {
            border: 1px solid #ddd;
            padding: 10px;
            margin: 10px 0;
            background: #fafafa;
        }

        .sanction-header {
            font-weight: bold;
            color: #1a472a;
        }

        .sanction-details {
            font-size: 10pt;
            color: #666;
            margin-top: 5px;
        }

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

            .student-info,
            .stat-box,
            .sanction-card {
                background: #fff;
            }
        }
    </style>
</head>
<body>
    <div class="print-controls">
        <a href="{{ route('students.index') }}" class="back-btn">Back to Students</a>
        <button class="print-btn" onclick="window.print()">Print Record</button>
    </div>

    <div class="container">
        <div class="header">
            <div class="school-name">{{ $school['name'] }}</div>
            <div class="school-address">{{ $school['address'] }} &middot; {{ $school['contact'] }}</div>
            <div class="document-title">Student Discipline Record</div>
            <p style="margin-top:8px; font-size:11pt; color:#555;">
                Generated on {{ $generated_at }} | Document Reference: SR-{{ $student['student_id'] }}-{{ date('Ymd') }}
            </p>
        </div>

        <div class="student-info">
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Student Name:</span>
                    <span>{{ $student['name'] }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Student ID:</span>
                    <span>{{ $student['student_id'] }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Grade Level:</span>
                    <span>{{ $student['grade_level'] }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Section:</span>
                    <span>{{ $student['section'] }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Status:</span>
                    <span style="text-transform: uppercase; font-weight:bold;">{{ $student['status'] }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Report Scope:</span>
                    <span>{{ $mode === 'all' ? 'Incidents and Attendance' : 'Incidents Only' }}</span>
                </div>
            </div>
        </div>

        <div class="summary-stats">
            <div class="stat-box">
                <div class="stat-number">{{ $summary['total_incidents'] }}</div>
                <div class="stat-label">Total Incidents</div>
            </div>
            <div class="stat-box">
                <div class="stat-number">{{ $summary['pending_incidents'] }}</div>
                <div class="stat-label">Pending</div>
            </div>
            <div class="stat-box">
                <div class="stat-number">{{ $summary['resolved_incidents'] }}</div>
                <div class="stat-label">Resolved</div>
            </div>
            <div class="stat-box">
                <div class="stat-number">{{ $summary['active_sanctions'] }}</div>
                <div class="stat-label">Active Sanctions</div>
            </div>
        </div>

        <h3 class="section-title">Incident History</h3>
        @if($incidents->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th style="width: 110px;">Date</th>
                        <th>Incident Details</th>
                        <th style="width: 120px;">Severity</th>
                        <th style="width: 120px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($incidents as $incident)
                        @php
                            $statusClass = 'status-neutral';
                            if ($incident->status === 'pending') {
                                $statusClass = 'status-pending';
                            } elseif (in_array($incident->status, ['approved', 'resolved', 'closed'])) {
                                $statusClass = 'status-approved';
                            }

                            $severityLabel = strtoupper($incident->severity ?? 'low');
                            $severityColor = '#10b981';
                            if (($incident->severity ?? '') === 'high') {
                                $severityColor = '#dc2626';
                            } elseif (($incident->severity ?? '') === 'medium') {
                                $severityColor = '#f59e0b';
                            }
                        @endphp
                        <tr>
                            <td>
                                {{ \Carbon\Carbon::parse($incident->incident_date)->format('M d, Y') }}<br>
                                <span style="font-size:9pt; color:#666;">
                                    {{ \Carbon\Carbon::parse($incident->incident_date)->format('l') }}
                                </span>
                            </td>
                            <td>
                                <strong>{{ $incident->category->name ?? 'Behavioral Incident' }}</strong><br>
                                <span style="font-size:10pt; color:#555;">
                                    {{ $incident->description }}
                                </span><br>
                                <span style="font-size:9pt; color:#777;">
                                    Location: {{ $incident->location ?? 'Not specified' }}
                                </span>
                            </td>
                            <td style="font-weight:bold; color: {{ $severityColor }};">
                                {{ $severityLabel }}
                            </td>
                            <td>
                                <span class="status-badge {{ $statusClass }}">
                                    {{ strtoupper(str_replace('_', ' ', $incident->status)) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="padding: 20px; text-align: center; color: #666; font-style: italic;">
                No incidents on record for this student.
            </p>
        @endif

        @if(!empty($attendance))
            <h3 class="section-title">Attendance Snapshot</h3>
            <div class="summary-stats" style="grid-template-columns: repeat(4, 1fr);">
                <div class="stat-box">
                    <div class="stat-number">{{ $attendance['summary']['total_records'] }}</div>
                    <div class="stat-label">Total Records</div>
                </div>
                <div class="stat-box">
                    <div class="stat-number">{{ $attendance['summary']['absent'] }}</div>
                    <div class="stat-label">Absent</div>
                </div>
                <div class="stat-box">
                    <div class="stat-number">{{ $attendance['summary']['tardy'] }}</div>
                    <div class="stat-label">Tardy</div>
                </div>
                <div class="stat-box">
                    <div class="stat-number">{{ $attendance['summary']['excused'] }}</div>
                    <div class="stat-label">Excused</div>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th style="width: 110px;">Date</th>
                        <th style="width: 100px;">Status</th>
                        <th style="width: 100px;">Time In</th>
                        <th>Remarks</th>
                        <th style="width: 160px;">Recorded By</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attendance['records'] as $record)
                        <tr>
                            <td>
                                {{ \Carbon\Carbon::parse($record->date)->format('M d, Y') }}<br>
                                <span style="font-size:9pt; color:#666;">
                                    {{ \Carbon\Carbon::parse($record->date)->format('l') }}
                                </span>
                            </td>
                            <td style="font-weight:bold; text-transform: uppercase;">
                                {{ $record->status }}
                            </td>
                            <td>
                                @if($record->time_in)
                                    {{ \Carbon\Carbon::parse($record->time_in)->format('h:i A') }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>{{ $record->remarks ?: 'N/A' }}</td>
                            <td>
                                {{ $record->recorder->name ?? 'System' }}<br>
                                <span style="font-size:9pt; color:#666;">
                                    {{ $record->created_at->format('M d, Y h:i A') }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align:center; font-style:italic; color:#666; padding:20px;">
                                No attendance records were found for this student.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @endif

        <h3 class="section-title">Sanctions History</h3>
        @if(count($sanctions) > 0)
            @foreach($sanctions as $sanction)
                <div class="sanction-card">
                    <div class="sanction-header">
                        {{ ucfirst($sanction->sanction_type) }} Sanction
                        @if($sanction->status === 'active')
                            <span style="color: #dc2626;">(Active)</span>
                        @elseif($sanction->status === 'completed')
                            <span style="color: #10b981;">(Completed)</span>
                        @endif
                    </div>
                    <div class="sanction-details">
                        <strong>Period:</strong>
                        {{ \Carbon\Carbon::parse($sanction->start_date)->format('M d, Y') }} -
                        {{ $sanction->end_date ? \Carbon\Carbon::parse($sanction->end_date)->format('M d, Y') : 'Ongoing' }}
                        <br>
                        <strong>Description:</strong> {{ $sanction->description }}
                    </div>
                </div>
            @endforeach
        @else
            <p style="padding: 20px; text-align: center; color: #666; font-style: italic;">
                No sanctions on record for this student.
            </p>
        @endif

        <div class="footer">
            <div class="signatures">
                <div class="signature-block">
                    <div class="signature-line"></div>
                    <div class="signature-name">Discipline Officer</div>
                    <div class="signature-title">Prefect of Discipline</div>
                </div>
                <div class="signature-block">
                    <div class="signature-line"></div>
                    <div class="signature-name">{{ $school['principal'] ?? 'Principal' }}</div>
                    <div class="signature-title">School Principal</div>
                </div>
            </div>

            <div class="document-footer">
                <p><em>This document is an official record from {{ $school['name'] }}.</em></p>
                <p>Generated on {{ $generated_at }} | Behavioral Evaluation Unit (BEU) System</p>
                <p style="margin-top: 10px;">Document Reference: SR-{{ $student['student_id'] }}-{{ date('Ymd') }}</p>
            </div>
        </div>
    </div>
</body>
</html>
