<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales Report | GreenMarket</title>

    <style>
        @page {
            margin: 5px;
        }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
            background-color: #E5F1D4;
            margin: 0;
            padding: 0;
        }

        .page {
            background-color: #E5F1D4;
            padding: 15px;
        }

        /* HEADER */
        .header {
            display: table;
            width: 100%;
            border-bottom: 3px solid #6BAF5A;
            padding-bottom: 10px;
            margin-bottom: 5px;
        }

        .header-left,
        .header-right {
            display: table-cell;
            vertical-align: middle;
        }

        .header-left {
            width: 60%;
        }

        .header-right {
            width: 40%;
            text-align: left;
            font-size: 11px;
        }

        .logo {
            display: inline-block;
            background-color: #ffffff;
            border-radius: 30px;
            padding: 5px 18px;
            border: 2px solid #6BAF5A;
            margin-top: 5%;
        }

        .logo img {
            height: 45px;
            vertical-align: middle;
        }

        .system-name {
            font-size: 26px;
            font-weight: bold;
            color: #2E7D32;
            margin-left: 10px;
            vertical-align: middle;
        }

        /* TIMELINE */
        .timeline {
            text-align: center;
            margin: 15px 0 20px 0;
        }

        .timeline span {
            background-color: #CFE6B8;
            padding: 6px 18px;
            border-radius: 20px;
            font-weight: bold;
            border: 1px solid #6BAF5A;
        }

        /* STAT CARDS */
        .stats {
            width: 100%;
            margin-bottom: 10px;
            margin-top: 10px;
        }

        .stat-card {
            width: 23%;
            display: inline-block;
            background-color: #ffffff;
            border-radius: 15px;
            border: 2px solid #6BAF5A;
            padding: 12px 5px;
            text-align: center;
            margin-right: 1%;
        }

        .stat-title {
            font-weight: bold;
            margin-bottom: 8px;
        }

        .stat-value {
            font-size: 14px;
            font-weight: bold;
        }

        /* TABLE */
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #ffffff;
            margin-top: 10px;
        }

        thead th {
            background-color: #6BAF5A;
            color: #ffffff;
            padding: 8px;
            border: 1px solid #4E8F3D;
            font-size: 11px;
        }

        tbody td {
            padding: 7px;
            border: 1px solid #A7C796;
            font-size: 11px;
        }

        .text-right {
            text-align: right;
        }

        .total-row td {
            font-weight: bold;
            background-color: #F1F8E9;
        }

        /* FOOTER */
        .footer {
            position: fixed;
            bottom: 15px;
            left: 25px;
            right: 25px;
            font-size: 10px;
            color: #333;
            border-top: 3px solid #6BAF5A;
        }

        .footer-table {
            width: 100%;
        }

        .footer-table td {
            width: 33%;
        }

        .footer-center {
            text-align: center;
        }

        .footer-right {
            text-align: right;
        }
    </style>
</head>
<body>

<div class="page">

    {{-- HEADER --}}
    <div class="header">
        <div class="header-left">
            <div class="logo">
                @php
                    $path = public_path('assets/images/Logo-4.png');
                    $type = pathinfo($path, PATHINFO_EXTENSION);
                    $data = file_get_contents($path);
                    $logo = 'data:image/' . $type . ';base64,' . base64_encode($data);
                @endphp

                <img src="{{ $logo }}" alt="GreenMarket Logo" style="height:45px;">
            </div>
            <span class="system-name">GreenMarket</span>
        </div>

        <div class="header-right">
            <div><strong>Report Type :</strong> Sales Report</div>
            <div><strong>Generated Date :</strong> {{ now()->format('Y-m-d') }}</div>
            <div><strong>Generated Time :</strong> {{ now()->format('H:i') }}</div>
        </div>
    </div>

    {{-- TIMELINE --}}
    <div class="timeline">
        <span>
            Time line :-
            {{ $stats['start_date'] ?? 'System started date' }}
            to
            {{ $stats['end_date'] ?? 'Until Today' }}
        </span>
    </div>
    <br>

    {{-- STATS --}}
    <center>
        <div class="stats">
            <div class="stat-card">
                <div class="stat-title">TOTAL ORDERS</div>
                <div class="stat-value">{{ $stats['total_sales'] }}</div>
            </div>

            <div class="stat-card">
                <div class="stat-title">TOTAL REVENUE</div>
                <div class="stat-value">LKR {{ number_format($stats['total_amount'], 2) }}</div>
            </div>

            <div class="stat-card" style="margin-right:0;">
                <div class="stat-title">UNIQUE BUYERS</div>
                <div class="stat-value">{{ $stats['unique_buyers'] }}</div>
            </div>
        </div>
    </center>

    {{-- TABLE --}}
    <table>
        <thead>
            <tr>
                <th>ORDER ID</th>
                <th>DATE</th>
                <th>CUSTOMER</th>
                <th>LEAD FARMER</th>
                <th class="text-right">AMOUNT (LKR)</th>
            </tr>
        </thead>
        <tbody>

            @if($sales->isEmpty())
                <tr>
                    <td colspan="5" style="text-align:center; font-weight:bold;">
                        No results found. <br> Please adjust your filters to refresh the results.
                    </td>
                </tr>
            @else
                @foreach($sales as $index => $sale)
                    <tr>
                        <td>{{ $sale->order_number }}</td>
                        <td>{{ \Carbon\Carbon::parse($sale->created_at)->format('Y-m-d') }}</td>
                        <td>{{ $sale->buyer_name ?? 'N/A' }}</td>
                        <td>{{ $sale->lead_farmer_name ?? 'N/A' }}</td>
                        <td class="text-right">{{ number_format($sale->total_amount, 2) }}</td>
                    </tr>
                @endforeach

                <tr class="total-row">
                    <td colspan="4" class="text-right">TOTAL REVENUE</td>
                    <td class="text-right">LKR {{ number_format($stats['total_amount'], 2) }}</td>
                </tr>
            @endif

        </tbody>
    </table>

</div>

{{-- FOOTER --}}
<div class="footer">
    <table class="footer-table">
        <tr>
            <td>System Generated Report</td>
            <td class="footer-center">GreenMarket</td>
            <td class="footer-right">{{ now()->format('Y-m-d H:i') }}</td>
        </tr>
    </table>
</div>

</body>
</html>
