<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        @page {
            /* Menentukan ukuran kertas A4 Portrait */
            size: a4 portrait;
            /* Margin untuk memberi ruang bagi Header (top) dan Footer (bottom) */
            margin: 115px 1cm 120px 1cm;
        }

        header {
            position: fixed;
            top: -100px;
            left: 0;
            right: 0;
            height: 90px;
            opacity: 0.9;
        }

        footer {
            position: fixed;
            bottom: -110px;
            left: 0;
            right: 0;
            height: 105px;
            opacity: 0.9;
        }

        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 8.5pt;
            margin: 0;
            padding: 0;
            line-height: 1.1;
        }

        .main-table, .footer-table, .header-table {
            width: 100%;
            border-collapse: collapse;
            /* Memaksa tabel mengikuti ukuran layout kertas A4 */
            table-layout: fixed;
        }

        .main-table td, .main-table th, .footer-table td, .header-table td {
            border: 1px solid rgba(0, 0, 0, 0.8);
            padding: 3px 5px;
            vertical-align: middle;
            /* Mencegah teks panjang atau gambar merusak layout kolom */
            word-wrap: break-word;
            overflow: hidden;
        }

        /* Menghindari baris temuan terpotong secara vertikal saat ganti halaman */
        .main-table tr {
            page-break-inside: avoid;
        }

        .en { color: #1e40af; font-style: italic; }
        .red-note { color: #ff0000; font-weight: bold; font-size: 9pt; text-align: center; }
        .bg-label { background-color: rgba(226, 232, 240, 0.7); font-weight: bold; }

        .center { text-align: center; }
        .right { text-align: right; }

        img.photo {
            width: 130px;
            max-width: 100%;
            height: auto;
            margin: 3px;
            border: 0.5px solid #000;
        }
    </style>
</head>
<body>

   <script type="text/php">
        if (isset($pdf)) {
            // page_script memastikan teks dicetak ulang pada setiap halaman baru
            $pdf->page_script('
                $font = $fontMetrics->get_font("Times-Roman", "bold");
                $size = 8.5;
                $pageText = "Halaman " . $PAGE_NUM . " dari " . $PAGE_COUNT;

                // Koordinat X: 480 (kanan), Y: 807 (baris terakhir footer)
                // Jika posisi masih kurang pas di halaman 2, coba ubah 806 menjadi 800
                $pdf->text(480, 807, $pageText, $font, $size);
            ');
        }
    </script>

    <header>
        <table class="header-table">
            <tr>
                <td width="15%" class="center"><img src="{{ public_path('images/logo-msm.png') }}" width="60"></td>
                <td width="70%" class="center">
                    <strong style="font-size: 13pt;">TOKA TINDUNG PROJECT</strong><br>
                    <strong style="font-size: 11pt;">Formulir Laporan WPI KPLH</strong><br>
                    <span>TT-MGT-FRS-024A</span>
                </td>
                <td width="15%" class="center"><img src="{{ public_path('images/logo-archi.png') }}" width="60"></td>
            </tr>
        </table>
    </header>

    <footer>
        <table class="footer-table">
            <tr>
                <td width="25%">Nama Dokumen/<span class="en">Document Name</span></td>
                <td width="35%">Formulir Laporan WPI KPLH</td>
                <td width="20%">Tanggal Terbit/<span class="en">Date of Issue</span></td>
                <td width="20%">15 Maret 2023</td>
            </tr>
            <tr>
                <td>Ditetapkan Oleh/<span class="en">Determined By</span></td>
                <td>Kepala Teknik Tambang/<span class="en">Mining Technical Head</span></td>
                <td>Tanggal Tinjau Ulang/<span class="en">Review Date</span></td>
                <td>15 Maret 2026</td>
            </tr>
            <tr>
                <td>No Dokumen/<span class="en">No Document</span></td>
                <td>TT-MGT-FRS-024A</td>
                <td>No Revisi</td>
                <td>00</td>
            </tr>
            <tr>
                <td colspan="3" class="red-note">
                    Dokumen terkendali dan valid hanya ada di SharePoint Archi Indonesia
                </td>
                <td class="right">&nbsp;</td>
            </tr>
        </table>
    </footer>

    <main>
        <table class="main-table" style="margin-bottom: 10px;">
            <tr class="bg-label">
                <td width="15%">Tanggal /Date</td>
                <td width="30%">{{ date('d F Y', strtotime($report->report_date)) }}</td>
                <td width="25%" class="center">Nama Petugas Inspeksi/Inspector</td>
                <td width="10%" class="center">ID</td>
                <td width="20%" class="center">Dept/Cont</td>
            </tr>
            @php $maxRows = 6; @endphp
            @for ($i = 0; $i < $maxRows; $i++)
            <tr>
                @if ($i == 0)
                    <td class="bg-label">Jam /Time</td>
                    <td>{{ $report->report_time }}</td>
                @elseif ($i == 1)
                    <td class="bg-label">Lokasi/Location</td>
                    <td>{{ $report->locationRelation->name ?? '-' }}</td>
                @elseif ($i == 2)
                    <td class="bg-label">Site Name</td>
                    <td>Tokatindung</td>
                @elseif ($i == 3)
                    <td class="bg-label">Area</td>
                    <td>Mining</td>
                @elseif ($i == 4)
                    <td class="bg-label">Company</td>
                    <td>PT MSM</td>
                @elseif ($i == 5)
                    <td class="bg-label">Department</td>
                    <td>{{ $report->department }}</td>
                @endif
                <td>{{ isset($report->inspectors[$i]) ? ($i + 1) . '. ' . $report->inspectors[$i]['name'] : '' }}</td>
                <td class="center">{{ $report->inspectors[$i]['id_number'] ?? '' }}</td>
                <td class="center">{{ $report->inspectors[$i]['dept_con'] ?? '' }}</td>
            </tr>
            @endfor
        </table>

        <table class="main-table">
            <thead>
                <tr class="bg-label center">
                    <th width="4%">No</th>
                    <th width="7%">OHS Risk</th>
                    <th width="32%">Uraian Tindakan/Kondisi Tidak Aman</th>
                    <th width="30%">Jenis Tindakan Pencegahan</th>
                    <th width="27%">Tindak Lanjut / Follow Up</th>
                </tr>
            </thead>
            <tbody>
                @foreach($report->findings as $index => $find)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td class="center">{{ $find->ohs_risk }}</td>
                    <td style="vertical-align: top;">
                        {{ $find->description }}<br>
                        @if(!empty($find->photos))
                            @foreach($find->photos as $p)
                                <img src="{{ public_path('storage/'.$p) }}" class="photo">
                            @endforeach
                        @endif
                    </td>
                    <td style="vertical-align: top;">
                        {{ $find->prevention_action }}<br>
                        @if(!empty($find->photos_prevention))
                            @foreach($find->photos_prevention as $pp)
                                <img src="{{ public_path('storage/'.$pp) }}" class="photo">
                            @endforeach
                        @endif
                    </td>
                    <td style="vertical-align: top;">
                        <strong>PIC:</strong> {{ $find->pic_responsible }}<br>
                        <strong>Due:</strong> {{ $find->due_date ? date('d/m/y', strtotime($find->due_date)) : '-' }}<br>
                        <strong>Selesai:</strong> {{ $find->completion_date ? date('d/m/y', strtotime($find->completion_date)) : '-' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </main>
</body>
</html>
