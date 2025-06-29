<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice #{{ $invoice->invoice_number }}</title>
    <style>
        @page {
            margin: 0cm; /* Menghilangkan margin default */
        }
        body {
            font-family: 'Helvetica', sans-serif;
            color: #333;
            line-height: 1.5;
            font-size: 13px;
            margin: 2.5cm; /* Menambahkan margin manual */
        }
        .container {
            width: 100%;
        }
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
        .invoice-details p, .client-details p {
            margin: 0;
            line-height: 1.4;
        }
        table.info-table {
            width: 100%;
            border: none;
            margin-bottom: 30px;
        }
        table.info-table td {
            border: none;
            padding: 0;
            vertical-align: top;
        }
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
        table.items-table thead {
            background-color: #f7f7f7;
        }
        table.items-table th {
            font-weight: bold;
            color: #555;
        }
        .totals {
            margin-top: 30px;
            float: right;
            width: 45%;
        }
        .totals table {
            width: 100%;
        }
        .totals table td {
            border: none;
            padding: 5px 0;
        }
        .totals .grand-total {
            font-weight: bold;
            font-size: 18px;
            background-color: #f7f7f7;
            padding: 10px !important;
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
            transform: translate(-50%, -50%) rotate(-20deg);
            font-size: 100px;
            font-weight: bold;
            color: #28a745;
            border: 7px solid #28a745;
            padding: 10px 30px;
            border-radius: 10px;
            opacity: 0.15;
            text-transform: uppercase;
            z-index: -1;
        }
        .page-break {
            page-break-after: always;
        }
        .attachment-section {
            margin-top: 40px;
            border-top: 1px dashed #ccc;
            padding-top: 20px;
        }
        .attachment-section h3 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .attachment-section img {
            max-width: 100%;
            height: auto;
            border: 1px solid #ddd;
            padding: 5px;
        }
    </style>
</head>
<body>

    @if($invoice->status == 'paid')
        <div class="paid-stamp">Lunas</div>
    @endif

    <div class="container">
        
        <table class="info-table">
            <tr>
                <td style="width: 50%;" class="company-details">
                    <h2>MARIEE KONVEKSI</h2>
                    <p>Jalan Bedoyo No.28A RT04/RW04 Kel. Kemlayan, Kec. Serengan, 57151 SURAKARTA</p>
                    <p>marieekonveksi@gmail.com</p>
                </td>
                <td style="width: 50%; text-align: right;" class="header">
                    <h1>{{ $invoice->status == 'paid' ? 'Bukti Pembayaran' : 'INVOICE' }}</h1>
                    <p><strong>No:</strong> #{{ $invoice->invoice_number }}</p>
                </td>
            </tr>
        </table>
        
        <table class="info-table">
            <tr>
                <td style="width: 50%;" class="client-details">
                    <p style="color: #888;">DITAGIHKAN KEPADA:</p>
                    <p style="font-weight: bold; font-size: 16px;">{{ $invoice->tailor->user->name }}</p>
                    <p>Penjahit Borongan</p>
                </td>
                <td style="width: 50%; text-align: right;" class="invoice-details">
                     <p><strong>Tanggal Terbit:</strong> {{ $invoice->issue_date ? $invoice->issue_date->isoFormat('D MMMM YYYY') : 'Tidak ditentukan' }}</p>
                     <p><strong>Batas Waktu:</strong> {{ $invoice->due_date ? $invoice->due_date->isoFormat('D MMMM YYYY') : 'Tidak ditentukan' }}</p>
                     @if($invoice->status == 'paid' && $invoice->payment_date)
                         <p style="font-weight:bold; color: #28a745;"><strong>Tanggal Lunas:</strong> {{ $invoice->payment_date->isoFormat('D MMMM YYYY') }}</p>
                     @endif
                </td>
            </tr>
        </table>

        <table class="items-table">
            <thead>
                <tr>
                    <th style="width:50%;">Deskripsi</th>
                    <th style="width:15%; text-align:right;">Kuantitas Diterima</th>
                    <th style="width:20%; text-align:right;">Harga Satuan</th>
                    <th style="width:15%; text-align:right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                {{-- ==================================================================== --}}
                {{-- AWAL PERUBAHAN: Menggunakan accepted_qty untuk kalkulasi --}}
                {{-- ==================================================================== --}}
                @foreach($invoice->progressItems as $item)
                <tr>
                    <td>
                        <strong>Pengerjaan Proyek: {{ $item->assignment->project->name }}</strong><br>
                        <span style="font-size:11px; color: #666">Pencatatan tanggal: {{ $item->date->isoFormat('D MMM YYYY') }}</span>
                    </td>
                    <td style="text-align:right;">{{ $item->accepted_qty }} pcs</td>
                    <td style="text-align:right;">Rp {{ number_format($item->assignment->project->wage_per_piece, 0, ',', '.') }}</td>
                    <td style="text-align:right;">Rp {{ number_format($item->accepted_qty * $item->assignment->project->wage_per_piece, 0, ',', '.') }}</td>
                </tr>
                @endforeach
                {{-- ==================================================================== --}}
                {{-- AKHIR PERUBAHAN --}}
                {{-- ==================================================================== --}}
            </tbody>
        </table>
        <div class="totals">
            <table>
                {{-- PERUBAHAN: Menyederhanakan bagian total --}}
                <tr class="grand-total">
                    <td style="width: 70%;">Total Tagihan</td>
                    <td style="text-align: right;">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>
    
        <div style="clear: both;"></div>

        {{-- PENAMBAHAN: BAGIAN LAMPIRAN BUKTI TRANSFER --}}
        @if(isset($receiptImagePath))
        <div class="attachment-section">
            <h3>Lampiran: Bukti Pembayaran</h3>
            {{-- `dompdf` akan membaca path absolut dari gambar ini --}}
            <img src="{{ $receiptImagePath }}" alt="Bukti Pembayaran">
        </div>
        @endif

    </div>
</body>
</html>
