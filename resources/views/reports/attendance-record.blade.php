<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Attendance Record - {{ $student['name'] }}</title>
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
            color: #0f172a;
            background: #fff;
        }

        .container {
            max-width: 8.5in;
            margin: 0 auto;
            padding: 0.5in;
        }

        .header {
            text-align: center;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 3px double #0f172a;
        }

        .school-name {
            font-size: 18pt;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #14532d;
        }

        .document-title {
            font-size: 16pt;
            font-weight: bold;
            margin-top: 16px;
            text-transform: uppercase;
        }

        .student-info {
            margin: 20px 0;
            padding: 16px;
            background: #f8fafc;
            border: 1px solid #cbd5f5;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .info-label {
            font-weight: bold;
            min-width: 140px;
            display: inline-block;
        }

        .summary-stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-box {
            text-align: center;
            padding: 16px;
            border: 1px solid #e2e8f0;
            background: #fefefe;
        }

        .stat-number {
            font-size: 24pt;
            font-weight: bold;
            color: #0f172a;
        }

        .stat-label {
            font-size: 9pt;
            text-transform: uppercase;
            color: #475569;
            letter-spacing: 1px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }

        th, td {
            border: 1px solid #0f172a;
            padding: 8px;
            font-size: 10pt;
            text-align: left;
        }

        th {
            background: #14532d;
            color: #fff;
            letter-spacing: 1px;
        }

        tr:nth-child(even) {
            background: #f8fafc;
        }

        .status-badge {
            font-weight: bold;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 9pt;
        }

        .status-absent { background: #fee2e2; color: #b91c1c; }
        .status-tardy { background: #fef3c7; color: #b45309; }
        .status-excused { background: #d1fae5; color: #047857; }

        .print-controls {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }

        .print-controls a,
        .print-controls button {
            border: none;
            border-radius: 8px;
            font-weight: bold;
            padding: 12px 18px;
            cursor: pointer;
            font-size: 14px;
            margin-left: 8px;
        }

        .back-btn {
            background: #475569;
            color: #fff;
            text-decoration: none;
        }

        .print-btn {
            background: #14532d;
            color: #fff;
        }

        @media print {
            .print-controls { display: none; }
            body { background: #fff; }
            .container { padding: 0; max-width: 100%; }
        }
    </style>
</head>
<body>
    <div class="print-controls">
        <a href="{{ route('reports.index') }}" class="back-btn">← Back to Reports</a>
        <button onclick="window.print()" class="print-btn">🖨️ Print / Save as PDF</button>
    </div>

    <div class="container">
        <div class="header">
            <div class="school-name">{{ $school['name'] }}</div>
            <div>{{ $school['address'] }}</div>
            <div>{{ $school['contact'] }}</div>
            <div class="document-title">Student Attendance Record</div>
        </div>

        <div class="student-info">
            <div class="info-grid">
                <div><span class="info-label">Student Name:</span> {{ $student['name'] }}</div>
                <div><span class="info-label">Student ID:</span> {{ $student['student_id'] }}</div>
                <div><span class="info-label">Grade Level:</span> {{ $student['grade_level'] }}</div>
                <div><span class="info-label">Section:</span> {{ $student['section'] }}</div>
                <div><span class="info-label">Enrollment Status:</span> {{ ucfirst($student['status']) }}</div>
                <div><span class="info-label">Report Generated:</span> {{ $generated_at }}</div>
            </div>
        </div>

        <div class="summary-stats">
            <div class="stat-box">
                <div class="stat-number">{{ $summary['total_records'] }}</div>
                <div class="stat-label">Total Records</div>
            </div>
            <div class="stat-box">
                <div class="stat-number">{{ $summary['absent'] }}</div>
                <div class="stat-label">Absences</div>
            </div>
            <div class="stat-box">
                <div class="stat-number">{{ $summary['tardy'] }}</div>
                <div class="stat-label">Tardies</div>
            </div>
            <div class="stat-box">
                <div class="stat-number">{{ $summary['excused'] }}</div>
                <div class="stat-label">Excused</div>
            </div>
        </div>

        <h3 style="font-size:14pt;font-weight:bold;margin-bottom:12px;">Attendance History</h3>
        @if($records->isNotEmpty())
            <table>
                <thead>
                    <tr>
                        <th style="width:110px;">Date</th>
                        <th style="width:120px;">Status</th>
                        <th style="width:110px;">Time In</th>
                        <th>Remarks</th>
                        <th style="width:160px;">Recorded By</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($records as $record)
                        <tr>
                            <td>{{ optional($record->date)->format('M d, Y') }}</td>
                            <td>
                                <span class="status-badge status-{{ $record->status }}">{{ strtoupper($record->status) }}</span>
                            </td>
                            <td>
                                @if($record->time_in)
                                    {{ \Carbon\Carbon::parse($record->time_in)->format('h:i A') }}
                                @else
                                    —
                                @endif
                            </td>
                            <td>{{ $record->remarks ?? '—' }}</td>
                            <td>{{ $record->recorder->name ?? 'System' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="padding:20px;text-align:center;color:#475569;font-style:italic;">No attendance records found for this student.</p>
        @endif

        <div style="margin-top:32px;font-size:10pt;color:#475569;text-align:center;">
            Generated on {{ $generated_at }} · {{ $school['name'] }}
        </div>
    </div>
</body>
</html>
