<!DOCTYPE html>
<html lang="id">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Perjanjian Kerja Sama Investasi - {{ $investor->name }}</title>
    <style>
        @page {
            margin: 2.5cm;
        }
        body { 
            font-family: 'Times New Roman', Times, serif; 
            font-size: 12pt; 
            line-height: 1.5;
            color: #1f2937;
        }
        .text-center { text-align: center; }
        .text-justify { text-align: justify; }
        .font-bold { font-weight: bold; }
        .underline { text-decoration: underline; }
        .mt-1 { margin-top: 0.25rem; }
        .mt-4 { margin-top: 1rem; }
        .mt-8 { margin-top: 2rem; }
        .mb-1 { margin-bottom: 0.25rem; }
        .mb-4 { margin-bottom: 1rem; }
        .mb-6 { margin-bottom: 1.5rem; }
        
        .header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .header .title {
            font-size: 14pt;
            font-weight: bold;
            text-decoration: underline;
            margin: 0;
        }
        .header .subtitle {
            font-size: 12pt;
            margin: 0.25rem 0;
        }

        .party-section {
            margin-bottom: 1.5rem;
        }
        .party-section table {
            width: 100%;
            border-collapse: collapse;
        }
        .party-section td {
            vertical-align: top;
            padding: 2px 0;
        }
        .party-section .label {
            width: 120px;
        }

        .pasal { margin-bottom: 1rem; }
        .pasal-title { font-weight: bold; text-align: center; margin-bottom: 0.5rem; }
        
        ol {
            padding-left: 20px;
            text-align: justify;
        }
        li {
            margin-bottom: 0.5rem;
        }

        .signature-area { 
            margin-top: 5rem; 
            width: 100%; 
        }
        .signature-block { 
            float: left; 
            width: 50%; 
            text-align: center; 
        }
        .signature-space {
            height: 80px;
        }
        .clear { clear: both; }
    </style>
</head>
<body>
    <div class="header">
        <p class="title">PERJANJIAN KERJA SAMA INVESTASI</p>
        <p class="subtitle">Nomor: ______/MOU-MK/{{ date('m/Y') }}</p>
    </div>

    <p class="text-justify mb-4">
        Pada hari ini, <strong>{{ \Carbon\Carbon::now()->isoFormat('dddd') }}</strong>, tanggal <strong>{{ \Carbon\Carbon::now()->isoFormat('D') }}</strong> bulan <strong>{{ \Carbon\Carbon::now()->isoFormat('MMMM') }}</strong> tahun <strong>{{ \Carbon\Carbon::now()->isoFormat('YYYY') }}</strong> ({{ \Carbon\Carbon::now()->isoFormat('D-m-Y') }}), bertempat di Surakarta, yang bertanda tangan di bawah ini:
    </p>

    <div class="party-section">
        <table>
            <tr>
                <td class="label">Nama</td>
                <td>: _________________________</td>
            </tr>
            <tr>
                <td class="label">Jabatan</td>
                <td>: Direktur Utama Mariee Konveksi</td>
            </tr>
            <tr>
                <td class="label">Alamat</td>
                <td>: Jl. Bedoyo No.28A RT04/RW04 Kel. Kemlayan, Kec. Serengan, 57151, Surakarta</td>
            </tr>
        </table>
        <p class="mt-1">Dalam hal ini bertindak untuk dan atas nama Mariee Konveksi, yang selanjutnya disebut sebagai <strong>PIHAK PERTAMA</strong>.</p>
    </div>

    <div class="party-section">
        <table>
            <tr>
                <td class="label">Nama</td>
                <td>: {{ $investor->name }}</td>
            </tr>
             <tr>
                <td class="label">No. KTP</td>
                <td>: _________________________</td>
            </tr>
            <tr>
                <td class="label">Alamat</td>
                <td>: _________________________________________________</td>
            </tr>
            <tr>
                <td class="label">No. Telepon</td>
                <td>: _________________________</td>
            </tr>
        </table>
        <p class="mt-1">Dalam hal ini bertindak untuk dan atas nama diri sendiri, yang selanjutnya disebut sebagai <strong>PIHAK KEDUA</strong>.</p>
    </div>

    <p class="text-justify mb-4">
        Dengan ini kedua belah pihak sepakat untuk mengikatkan diri dalam suatu Perjanjian Kerja Sama Investasi (selanjutnya disebut "Perjanjian") dengan syarat dan ketentuan sebagai berikut:
    </p>

    <div class="pasal">
        <p class="pasal-title">Pasal 1<br>MAKSUD DAN TUJUAN</p>
        <p class="text-justify">
            PIHAK KEDUA setuju untuk memberikan dana investasi kepada PIHAK PERTAMA, dan PIHAK PERTAMA setuju untuk menerima dan mengelola dana tersebut untuk keperluan modal kerja pada proyek-proyek produksi garmen yang diselenggarakan oleh PIHAK PERTAMA.
        </p>
    </div>

    <div class="pasal">
        <p class="pasal-title">Pasal 2<br>HAK DAN KEWAJIBAN</p>
        <ol>
            <li>
                <strong>PIHAK PERTAMA berkewajiban untuk:</strong>
                <ol type="a">
                    <li>Menggunakan dana investasi dari PIHAK KEDUA secara amanah dan profesional sesuai dengan tujuan Perjanjian.</li>
                    <li>Memberikan laporan perkembangan dan hasil proyek yang didanai melalui sistem aplikasi yang telah disediakan.</li>
                    <li>Melakukan perhitungan dan pembayaran bagi hasil (profit sharing) kepada PIHAK KEDUA setelah proyek selesai dan keuntungan telah direalisasikan.</li>
                </ol>
            </li>
            <li>
                <strong>PIHAK KEDUA berhak untuk:</strong>
                <ol type="a">
                    <li>Mendapatkan akses informasi mengenai portofolio proyek yang didanai melalui sistem aplikasi PIHAK PERTAMA.</li>
                    <li>Menerima pembagian keuntungan dari hasil proyek yang didanai sesuai dengan persentase ekuitas kepemilikan yang telah disetujui pada setiap investasi.</li>
                </ol>
            </li>
        </ol>
    </div>
    
     <div class="pasal">
        <p class="pasal-title">Pasal 3<br>PENYELESAIAN PERSELISIHAN</p>
        <p class="text-justify">
            Apabila di kemudian hari timbul perselisihan dalam pelaksanaan Perjanjian ini, kedua belah pihak sepakat untuk menyelesaikannya secara musyawarah untuk mufakat. Apabila musyawarah tidak mencapai mufakat, maka kedua belah pihak setuju untuk menyelesaikannya melalui jalur hukum di Pengadilan Negeri Malang.
        </p>
    </div>

    <p class="text-justify mt-8">
        Demikian Perjanjian ini dibuat dalam rangkap 2 (dua), asli masing-masing bermeterai cukup dan mempunyai kekuatan hukum yang sama setelah ditandatangani oleh kedua belah pihak dalam keadaan sadar dan tanpa ada paksaan dari pihak manapun.
    </p>

    <div class="signature-area">
        <div class="signature-block">
            <p>PIHAK PERTAMA,</p>
            <div class="signature-space"></div>
            <p class="font-bold underline">(Pamastri Sita Nabila)</p>
            <p>Direktur Mariee Konveksi</p>
        </div>
        <div class="signature-block">
            <p>PIHAK KEDUA,</p>
            <div class="signature-space"></div>
            <p class="font-bold underline">({{ $investor->name }})</p>
            <p>Investor</p>
        </div>
        <div class="clear"></div>
    </div>
</body>
</html>
