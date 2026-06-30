<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #1f2937;
            font-size: 12px;
        }

        h1 {
            margin: 0;
            font-size: 20px;
            color: #0f766e;
        }

        .meta {
            margin: 4px 0 14px;
            color: #4b5563;
            font-size: 11px;
        }

        table.ledger {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .ledger th,
        .ledger td {
            border: 1px solid #d1d5db;
            padding: 6px;
            font-size: 9px;
            word-wrap: break-word;
        }

        .ledger th {
            background: #f3f4f6;
            text-transform: uppercase;
            color: #4b5563;
            letter-spacing: .04em;
        }
    </style>
</head>
<body>
    <h1>{{ $title }}</h1>
    <div class="meta">Generated: {{ $generatedAt->format('d M Y H:i:s') }}</div>

    <table class="ledger">
        <thead>
            <tr>
                @foreach($headers as $header)
                    <th>{{ str_replace('_', ' ', $header) }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
            <tr>
                @foreach($headers as $header)
                    <td>{{ $row[$header] ?? '' }}</td>
                @endforeach
            </tr>
            @empty
            <tr>
                <td colspan="{{ count($headers) }}" style="text-align: center; color: #6b7280;">No records found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
