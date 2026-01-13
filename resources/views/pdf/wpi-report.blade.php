<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        @page {
            /* Margin 110px atas/bawah untuk ruang header & footer agar tidak tertimpa konten */
            margin: 110px 1cm 115px 1cm;
        }

        header {
            position: fixed;
            top: -95px;
            left: 0;
            right: 0;
            height: 90px;
        }

        footer {
            position: fixed;
            bottom: -105px;
            left: 0;
            right: 0;
            height: 105px;
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
        }

        .main-table td, .main-table th, .footer-table td, .header-table td {
            border: 1px solid #000;
            padding: 2px 4px;
            vertical-align: middle;
        }

        /* Styling Label Bilingual (Italic & Blue sesuai gambar) */
        .en {
            color: #1e40af;
            font-style: italic;
        }

        /* Styling teks merah di footer */
        .red-note {
            color: #ff0000;
            font-weight: bold;
            font-size: 9pt;
            text-align: center;
        }

        .center { text-align: center; }
        .right { text-align: right; }
        .bg-label { background-color: #e2e8f0; font-weight: bold; }

        /* Style khusus konten utama */
        main .main-table td { vertical-align: top; }
        img.photo { width: 130px; height: auto; margin: 3px; border: 0.5px solid #000; }
    </style>
</head>
<body>

    <script type="text/php">
        if (isset($pdf)) {
            // page_script memastikan nomor muncul di SETIAP halaman
            $pdf->page_script('
                $font = $fontMetrics->get_font("Times-Roman", "bold");
                $size = 8.5;
                // Koordinat X: 480 (kanan), Y: 788 (sejajar baris 4 footer)
                $pageText = "Halaman " . $PAGE_NUM . " dari " . $PAGE_COUNT;
                $pdf->text(480, 788, $pageText, $font, $size);
            ');
        }
    </script>

    <header>
        <table class="header-table">
            <tr>
                <td width="15%" class="center">
                    <img src="{{ public_path('images/logo-msm.png') }}" width="60">
                </td>
                <td width="70%" class="center">
                    <strong style="font-size: 13pt;">TOKA TINDUNG PROJECT</strong><br>
                    <strong style="font-size: 11pt;">Formulir Laporan WPI KPLH</strong><br>
                    <span>TT-MGT-FRS-024A</span>
                </td>
                <td width="15%" class="center">
                    <img src="{{ public_path('images/logo-archi.png') }}" width="60">
                </td>
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
                <td class="right">
                    &nbsp;
                </td>
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
                <td width="20%" class="center">Department/Contractor</td>
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
            <tr>
                <td class="bg-label">Contractor</td>
                <td>{{ $report->searchContractor ?? '-' }}</td>
                <td class="bg-label center">Direview oleh/Reviewing by:</td>
                <td colspan="2" class="center">
                    <div style="height: 25px;"></div>
                    <strong>MARDIN</strong><br>
                    ID: 3242289
                </td>
            </tr>
        </table>

        <table class="main-table">
            <thead>
                <tr class="bg-label center">
                    <th width="4%">No</th>
                    <th width="6%">OHS Risk</th>
                    <th width="32%">Uraian Tindakan Tidak Aman / Kondisi Tidak Aman</th>
                    <th width="30%">Jenis Tindakan Pencegahan</th>
                    <th width="28%">Tindak Lanjut / Follow Up</th>
                </tr>
            </thead>
            <tbody>
                @foreach($report->findings as $index => $find)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td class="center">{{ $find->ohs_risk }}</td>
                    <td>
                        {{ $find->description }}<br><br>
                        @if(!empty($find->photos))
                            @foreach($find->photos as $p)
                                <img src="{{ public_path('storage/'.$p) }}" class="photo">
                            @endforeach
                        @endif
                    </td>
                    <td>
                        {{ $find->prevention_action }}<br><br>
                        @if(!empty($find->photos_prevention))
                            @foreach($find->photos_prevention as $pp)
                                <img src="{{ public_path('storage/'.$pp) }}" class="photo">
                            @endforeach
                        @endif
                    </td>
                    <td>
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
