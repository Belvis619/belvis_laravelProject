<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Donations Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #4CAF50;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .text-right {
            text-align: right;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <h1>Donations Report</h1>
    <p><strong>Generated:</strong> {{ now()->format('Y-m-d H:i:s') }}</p>
    <p><strong>Total Records:</strong> {{ $donations->count() }}</p>

    <table>
        <thead>
            <tr>
                <th>Donor Name</th>
                <th>Type</th>
                <th>Amount</th>
                <th>Items</th>
                <th>Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($donations as $donation)
                <tr>
                    <td>{{ $donation->donor_name }}</td>
                    <td>{{ $donation->type?->name ?? 'N/A' }}</td>
                    <td class="text-right">
                        @if($donation->amount)
                            ₱{{ number_format($donation->amount, 2) }}
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $donation->items ?? '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($donation->donation_date)->format('Y-m-d') }}</td>
                    <td>{{ ucfirst($donation->status) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center;">No donations found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>This report was generated automatically on {{ now()->format('F d, Y \a\t H:i:s') }}</p>
    </div>
</body>
</html>
