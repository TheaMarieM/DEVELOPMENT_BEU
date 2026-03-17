<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Incident Report - {{ $incident->incident_number }}</title>
    <style>
        @media print {
            body { margin: 0; padding: 20px; }
            .no-print { display: none !important; }
            .page-break { page-break-before: always; }
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #1e5128;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header img {
            height: 60px;
        }
        .header h1 {
            color: #1e5128;
            margin: 10px 0 5px;
            font-size: 1.5rem;
        }
        .header h2 {
            font-size: 1rem;
            color: #666;
            margin: 0;
            font-weight: normal;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            background: #1e5128;
            color: white;
            padding: 8px 15px;
            font-size: 0.9rem;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 15px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }
        .info-item {
            background: #f9f9f9;
            padding: 12px;
            border-left: 3px solid #1e5128;
        }
        .info-label {
            font-size: 0.75rem;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .info-value {
            font-size: 1rem;
            font-weight: 600;
            color: #333;
            margin-top: 4px;
        }
        .full-width {
            grid-column: span 2;
        }
        .description-box {
            background: #f9f9f9;
            padding: 15px;
            border-left: 3px solid #1e5128;
            white-space: pre-wrap;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #f5f5f5;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #666;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-approved { background: #dcfce7; color: #166534; }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-reported { background: #dbeafe; color: #1e40af; }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 0.8rem;
            color: #666;
            text-align: center;
        }
        .signature-line {
            margin-top: 60px;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 40px;
        }
        .signature-box {
            text-align: center;
        }
        .signature-box .line {
            border-bottom: 1px solid #333;
            margin-bottom: 5px;
            height: 40px;
        }
        .signature-box .label {
            font-size: 0.8rem;
            color: #666;
        }
        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #1e5128;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
            font-weight: bold;
        }
        .print-btn:hover {
            background: #166534;
        }
    </style>
</head>
<body>
    <button onclick="window.print()" class="print-btn no-print">
        🖨️ Print / Save as PDF
    </button>

    <a href="{{ route('reports.index') }}" class="print-btn no-print" style="right: 200px; background: #6b7280;">
        ← Back to Reports
    </a>

    <div class="header">
        <h1>{{ $school['name'] }}</h1>
        <h2>Basic Education Unit</h2>
        <p style="font-size: 0.85rem; color: #666; margin-top: 5px;">{{ $school['address'] }}</p>
        <h2 style="margin-top: 20px; font-weight: bold; color: #1e5128; font-size: 1.2rem;">INCIDENT REPORT</h2>
    </div>

    <div class="section">
        <div class="section-title">Incident Information</div>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Incident Number</div>
                <div class="info-value">{{ $incident->incident_number }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Date of Incident</div>
                <div class="info-value">{{ $incident->incident_date->format('F d, Y') }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Location</div>
                <div class="info-value">{{ $incident->location }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Status</div>
                <div class="info-value">
                    <span class="status-badge status-{{ $incident->status === 'approved' ? 'approved' : ($incident->status === 'pending_approval' ? 'pending' : 'reported') }}">
                        {{ ucwords(str_replace('_', ' ', $incident->status)) }}
                    </span>
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">Violation Category</div>
                <div class="info-value">{{ $incident->category?->name ?? 'Not Specified' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Handbook Clause</div>
                <div class="info-value">{{ $incident->clause?->clause_number ?? 'Not Specified' }}</div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Description / Narrative</div>
        <div class="description-box">{{ $incident->description }}</div>
    </div>

    @if($incident->students->isNotEmpty())
    <div class="section">
        <div class="section-title">Involved Students</div>
        <table>
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Name</th>
                    <th>Grade & Section</th>
                    <th>Offense Count</th>
                </tr>
            </thead>
            <tbody>
                @foreach($incident->students as $student)
                <tr>
                    <td>{{ $student->student_id }}</td>
                    <td>{{ $student->last_name }}, {{ $student->first_name }}</td>
                    <td>Grade {{ $student->grade_level }} - {{ $student->section }}</td>
                    <td>{{ $student->pivot->offense_count ?? '1st' }} Offense</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($incident->action_taken)
    <div class="section">
        <div class="section-title">Action Taken</div>
        <div class="description-box">{{ $incident->action_taken }}</div>
    </div>
    @endif

    <div class="section">
        <div class="section-title">Report Details</div>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Reported By</div>
                <div class="info-value">{{ $incident->reporter?->name ?? 'N/A' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Report Date</div>
                <div class="info-value">{{ $incident->created_at->format('F d, Y h:i A') }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Parent Notified</div>
                <div class="info-value">{{ $incident->is_parent_notified ? 'Yes' : 'No' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Last Updated</div>
                <div class="info-value">{{ $incident->updated_at->format('F d, Y h:i A') }}</div>
            </div>
        </div>
    </div>

    <div class="signature-line">
        <div class="signature-box">
            <div class="line"></div>
            <div class="label">Discipline Coordinator</div>
        </div>
        <div class="signature-box">
            <div class="line"></div>
            <div class="label">School Principal</div>
        </div>
    </div>

    <div class="footer">
        <p>Generated on {{ $generated_at }} by {{ auth()->user()?->name ?? 'System' }}</p>
        <p style="color: #999; font-size: 0.7rem;">This document is confidential and intended only for authorized school personnel.</p>
    </div>
</body>
</html>
