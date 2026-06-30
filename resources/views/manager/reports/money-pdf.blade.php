<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Money Donation Ledger</title>
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

        .summary {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }

        .summary td {
            border: 1px solid #d1d5db;
            padding: 8px;
            text-align: center;
        }

        .summary .label {
            color: #6b7280;
            font-size: 10px;
            text-transform: uppercase;
        }

        .summary .value {
            color: #111827;
            font-weight: 700;
            margin-top: 3px;
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
            font-size: 10px;
            word-wrap: break-word;
        }

        .ledger th {
            background: #f3f4f6;
            text-transform: uppercase;
            font-size: 9px;
            color: #4b5563;
            letter-spacing: .04em;
        }

        .right {
            text-align: right;
        }
    </style>
</head>
<body>
    <h1>Money Donation Payment Ledger</h1>
    <div class="meta">Generated: {{ $generatedAt->format('d M Y H:i:s') }}</div>

    <table class="summary">
        <tr>
            <td>
                <div class="label">Transactions</div>
                <div class="value">{{ number_format((int) $summary['total_count']) }}</div>
            </td>
            <td>
                <div class="label">Received (KES)</div>
                <div class="value">{{ number_format((float) $summary['total_received'], 2) }}</div>
            </td>
            <td>
                <div class="label">Pending (KES)</div>
                <div class="value">{{ number_format((float) $summary['total_pending'], 2) }}</div>
            </td>
            <td>
                <div class="label">Failed (KES)</div>
                <div class="value">{{ number_format((float) $summary['total_failed'], 2) }}</div>
            </td>
        </tr>
    </table>

    <table class="ledger">
        <thead>
            <tr>
                <th style="width: 7%;">ID</th>
                <th style="width: 15%;">Donor</th>
                <th style="width: 16%;">Email</th>
                <th style="width: 10%;">Amount</th>
                <th style="width: 10%;">Status</th>
                <th style="width: 14%;">Receipt Ref</th>
                <th style="width: 12%;">Payer Phone</th>
                <th style="width: 16%;">Paid At</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
            <tr>
                <td>{{ $row->donation_id }}</td>
                <td>{{ $row->donor_name }}</td>
                <td>{{ $row->donor_email }}</td>
                <td class="right">{{ number_format((float) $row->amount_kes, 2) }}</td>
                <td>{{ $row->payment_status }}</td>
                <td>{{ $row->payment_reference ?? 'N/A' }}</td>
                <td>{{ $row->payer_phone ?? 'N/A' }}</td>
                <td>{{ $row->paid_at ? \Carbon\Carbon::parse($row->paid_at)->format('d M Y H:i') : 'N/A' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align: center; color: #6b7280;">No money donation records found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
