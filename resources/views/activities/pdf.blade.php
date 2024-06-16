<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        h1, h4 {
            text-align: center;
            margin: 0;
        }
        .header {
            margin-bottom: 20px;
            text-align: center;
            padding: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #dddddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            text-align: center;
            position: fixed;
            bottom: 0;
            width: 100%;
            padding: 10px 0;
        }
    </style>
</head>
<body>
<div class="header">
    <h1>{{ $data->name }}</h1>
    <h4>Start time: {{ $data->start_time }}</h4>
</div>
<table>
    <thead>
    <tr>
        <th>#</th>
        <th>Name</th>
        <th>Email</th>
        <th>Registration Time</th>
    </tr>
    </thead>
    <tbody>
    @foreach($data->participants as $participant)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $participant->name }}</td>
            <td>{{ $participant->email }}</td>
            <td>{{ $participant->pivot->created_at }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
<div class="footer">
    <p>Generated on {{ now()->format('Y-m-d H:i:s') }}</p>
</div>
</body>
</html>
