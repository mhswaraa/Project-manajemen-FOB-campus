<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Bukti Pembayaran #{{ $payout->id }}</title>
    <style>
        @page { margin: 0cm; }
        body {
            font-family: 'Helvetica', sans-serif;
            color: #333;
            line-height: 1.5;
            font-size: 13px;
            margin: 2.5cm;
            position: relative;
        }
        .container { width: 100%; }
        .header h1 {
            margin: 0;
            font-size: 28px;
            color: #000;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .company-details h2 {
            margin: 0;
            font-size: 20px;
            font-weight: bold;
        }
        .details p { margin: 0; line-height: 1.4; }
        table.info-table {
            width: 100%;
            border: none;
            margin-bottom: 30px;
        }
        table.info-table td { border: none; padding: 0; vertical-align: top; }
        table.items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table.items-table th, table.items-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        table.items-table thead { background-color: #f7f7f7; }
        table.items-table th { font-weight: bold; color: #555; }
        .totals { margin-top: 20px; float: right; width: 45%; }
        .totals table { width: 100%; }
        .totals table td { border: none; padding: 5px 0; }
        .totals .grand-total {
            font-weight: bold;
            font-size: 18px;
            background-color: #f0f5ff;
            color: #1d4ed8;
            padding: 10px !important;
            border-radius: 5px;
        }
        .footer {
            position: fixed;
            bottom: 2.5cm;
            left: 2.5cm;
            right: 2.5cm;
            text-align: center;
            font-size: 12px;
            color: #888;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
        .paid-stamp {
            position: fixed;
            top: 40%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-15deg);
            font-size: 100px;
            font-weight: bold;
            color: #16a34a;
            border: 7px solid #16a34a;
            padding: 10px 30px;
            border-radius: 10px;
            opacity: 0.1;
            text-transform: uppercase;
            z-index: -1;
        }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    @php
        $investor = $payout->investment->investor;
        $investment = $payout->investment;
        $project = $investment->project;
        
        $investorProfit = $payout->profit_amount;
        $investorInitialInvestment = $investment->amount;
        $totalReturn = $investorInitialInvestment + $investorProfit;
    @endphp

    <div class="paid-stamp">LUNAS</div>

    <div class="container">
        
        <table class="info-table">
            <tr>
                <td style="width: 50%;" class="company-details">
                    <h2>Mariee Konveksi</h2>
                    <p>Jalan Soekarno Hatta No. 9, Malang</p>
                    <p>kontak@mariee.com</p>
                </td>
                <td style="width: 50%; text-align: right;" class="header">
                    <h1>Bukti Pembayaran</h1>
                    <p><strong>No. Referensi:</strong> #PAYOUT-{{ $payout->id }}</p>
                </td>
            </tr>
        </table>
        
        <table class="info-table">
            <tr>
                <td style="width: 50%;" class="details">
                    <p style="color: #888;">DIBAYARKAN KEPADA:</p>
                    <p style="font-weight: bold; font-size: 16px;">{{ $investor->user->name }}</p>
                </td>
                <td style="width: 50%; text-align: right;" class="details">
                     <p><strong>Tanggal Pembayaran:</strong> {{ \Carbon\Carbon::parse($payout->payment_date)->isoFormat('D MMMM YYYY') }}</p>
                     <p><strong>Diproses oleh:</strong> {{ $payout->processor->name ?? 'Admin' }}</p>
                </td>
            </tr>
        </table>

        <table class="items-table">
            <thead>
                <tr>
                    <th style="width:70%;">Deskripsi</th>
                    <th style="width:30%;" class="text-right">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>Pengembalian Investasi Awal</strong><br>
                        <span style="font-size:11px; color: #666">
                            Untuk proyek: {{ $project->name }}
                        </span>
                    </td>
                    <td class="text-right">Rp {{ number_format($investorInitialInvestment, 0, ',', '.') }}</td>
                </tr>
                 <tr>
                    <td>
                        <strong>Pembayaran Profit Investasi ({{ $investment->equity_percentage }}%)</strong><br>
                        <span style="font-size:11px; color: #666">
                            Dari total profit proyek
                        </span>
                    </td>
                    <td class="text-right">Rp {{ number_format($investorProfit, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <div class="totals">
            <table>
                <tr>
                    <td>Subtotal</td>
                    <td class="text-right">Rp {{ number_format($totalReturn, 0, ',', '.') }}</td>
                </tr>
                <tr class="grand-total">
                    <td>Total Dibayarkan</td>
                    <td class="text-right">Rp {{ number_format($totalReturn, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        @if($payout->receipt_path && file_exists(public_path('storage/' . $payout->receipt_path)))
        <div style="clear: both; margin-top: 120px; border-top: 1px dashed #ccc; padding-top: 20px;">
            <h3>Lampiran: Bukti Transfer</h3>
            <img src="{{ public_path('storage/' . $payout->receipt_path) }}" alt="Bukti Transfer" style="max-width: 100%; height: auto; border: 1px solid #ddd; padding: 5px;">
        </div>
        @endif

        <div class="footer">
            <p>Terima kasih atas investasi dan kepercayaan Anda.</p>
            <p>Dokumen ini dibuat secara otomatis oleh sistem dan sah tanpa tanda tangan.</p>
        </div>

    </div>
</body>
</html>
