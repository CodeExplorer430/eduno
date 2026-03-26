<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Submissions Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1f2937; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        p.subtitle { font-size: 11px; color: #6b7280; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #1e3a5f; color: #ffffff; text-align: left; padding: 6px 8px; font-size: 10px; text-transform: uppercase; }
        td { padding: 5px 8px; border-bottom: 1px solid #e5e7eb; }
        tr:nth-child(even) td { background: #f9fafb; }
        .late { color: #dc2626; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Submissions Report</h1>
    <p class="subtitle">Generated {{ now()->format('Y-m-d H:i') }} &mdash; Eduno LMS</p>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Student</th>
                <th>Assignment</th>
                <th>Submitted At</th>
                <th>Late?</th>
                <th>Score</th>
                <th>Released</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $row)
            <tr>
                <td>{{ $row['id'] }}</td>
                <td>{{ $row['student'] }}</td>
                <td>{{ $row['assignment'] }}</td>
                <td>{{ $row['submitted_at'] }}</td>
                <td class="{{ $row['is_late'] === 'Yes' ? 'late' : '' }}">{{ $row['is_late'] }}</td>
                <td>{{ $row['score'] }}</td>
                <td>{{ $row['released'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
