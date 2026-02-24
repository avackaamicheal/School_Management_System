<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Report Card</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; color: #333; margin: 0; padding: 0; }
        .page-break { page-break-after: always; }
        /* Prevent blank page at the very end of batch */
        .page-break:last-child { page-break-after: avoid; }

        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #2c3e50; padding-bottom: 10px; }
        .header h1 { margin: 0; color: #2c3e50; font-size: 24px; }
        .header h3 { margin: 5px 0 0 0; color: #7f8c8d; font-size: 16px; }

        .student-info { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .student-info td { padding: 8px; border: 1px solid #ddd; background-color: #f9f9f9; }

        .grades-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .grades-table th { background-color: #2c3e50; color: white; padding: 10px; text-align: left; }
        .grades-table td { padding: 10px; border: 1px solid #ddd; }
        .grades-table tr:nth-child(even) { background-color: #f9f9f9; }

        .summary { margin-top: 20px; border: 2px solid #2c3e50; padding: 15px; background-color: #ecf0f1; text-align: center;}
        .summary strong { font-size: 18px; color: #2c3e50; }
    </style>
</head>
<body>

    @foreach($reports as $data)
        <div class="page-break">
            <div class="header">
                <h1>{{ $school->name ?? 'Official School Report' }}</h1>
                <h3>Academic Term: {{ $data['term']->name }}</h3>
                <p>Official Student Report Card</p>
            </div>

            <table class="student-info">
                <tr>
                    <td><strong>Student Name:</strong> {{ $data['student']->name }}</td>
                    <td><strong>Admission No:</strong> {{ $data['student']->studentProfile->admission_number ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td><strong>Class Section:</strong> {{ $data['student']->studentProfile->section->name ?? 'N/A' }}</td>
                    <td><strong>Date Generated:</strong> {{ date('F j, Y') }}</td>
                </tr>
            </table>

            <table class="grades-table">
                <thead>
                    <tr>
                        <th style="width: 40%">Subject</th>
                        <th style="width: 15%; text-align: center;">Score (100)</th>
                        <th style="width: 15%; text-align: center;">Grade</th>
                        <th style="width: 30%">Teacher Remark</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['grades'] as $grade)
                        <tr>
                            <td><strong>{{ $grade->subject->name }}</strong></td>
                            <td style="text-align: center;">{{ $grade->total_score }}</td>
                            <td style="text-align: center;"><strong>{{ $grade->letter }}</strong></td>
                            <td>{{ $grade->remark }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="summary">
                <strong>Overall Average: {{ $data['average'] }}%</strong> &nbsp; | &nbsp;
                <strong>Final Grade: {{ $data['overall_grade'] }}</strong>
            </div>

            <div style="margin-top: 40px; text-align: right;">
                <p>___________________________</p>
                <p>Principal's Signature</p>
            </div>
        </div>
    @endforeach

</body>
</html>
