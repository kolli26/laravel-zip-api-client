<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table thead {
            background-color: #f0f0f0;
            border-bottom: 2px solid #333;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        table th {
            font-weight: bold;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Városok listája</h1>
        <p>Generálva: {{ date('Y-m-d H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Város</th>
                <th>Megye</th>
                <th>Irányítószám</th>
            </tr>
        </thead>
        <tbody>
            @foreach($entities as $city)
            <tr>
                <td>{{ $city->id }}</td>
                <td>{{ $city->place_name ?? $city->name }}</td>
                <td>{{ $city->county->name ?? '-' }}</td>
                <td>{{ $city->zip_code ?? $city->postal_code }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>&copy; {{ date('Y') }} - Wszystkie prawa zastrzeżone. | Strona <span class="page-number"></span> z <span class="page-count"></span></p>
    </div>
</body>
</html>
