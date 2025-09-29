<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Users Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .no-data {
            text-align: center;
            color: #333;
            font-size: 20px;
            padding: 20px;
        }
        .label {
            padding: 3px 8px;
            border-radius: 4px;
            color: #fff;
        }
        .label-success {
            background-color: #28a745;
        }
        .label-danger {
            background-color: #dc3545;
        }
    </style>
</head>
<body>
    <h1 style="text-align: center;">Users Report</h1>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Mobile</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @php $i = 1; @endphp
            @forelse($results as $user)
                <tr>
                    <td>{{ $i++ }}</td>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->countryDetail ? $user->countryDetail->dial_code : '0' }} {{ $user->mobile }}</td>
                    <td>
                        @if ($user->active)
                            <span class="label label-success">Active</span>
                        @else
                            <span class="label label-danger">Inactive</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="no-data">No Data Found</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
