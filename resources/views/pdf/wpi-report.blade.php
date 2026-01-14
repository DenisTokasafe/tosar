<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        @page {
            size: a4 portrait;
            /** * Tinggi footer total sekitar 120px.
             * Untuk mendapatkan jarak 10px, margin-bottom harus 130px.
             */
            margin: 130px 1.5cm 130px 1.5cm;
        }

        header {
            position: fixed;
            top: -100px;
            left: 0;
            right: 0;
            height: 100px;
        }

        footer {
            position: fixed;
            /**
             * Posisi bottom disetel agar tepat berada di bawah batas margin dokumen.
             */
            bottom: -115px;
            left: 0;
            right: 0;
            height: 120px;
        }

        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 9pt;
            margin: 0;
            padding: 0;
            line-height: 1.2;
        }

        .main-table,
        .footer-table,
        .header-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        /* Teks footer tetap 8px */
        .footer-table td {
            font-size: 8px !important;
            border: 1px solid #000;
            padding: 4px 6px;
            vertical-align: top;
            word-wrap: break-word;
        }

        .main-table td,
        .main-table th,
        .header-table td {
            border: 1px solid #000;
            padding: 5px 6px;
            vertical-align: top;
            word-wrap: break-word;
        }

        .header-table td {
            vertical-align: middle;
        }

        .main-table tr {
            page-break-inside: avoid;
        }

        .en {
            color: #1e40af;
            font-style: italic;
        }

        .footer-table .en {
            font-size: 7px;
        }

        .red-note {
            color: #ff0000;
            font-weight: bold;
            font-size: 8px;
            text-align: center;
        }

        .bg-label {
            background-color: #f1f5f9;
            font-weight: bold;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        img.photo {
            width: 140px;
            height: auto;
            margin: 5px 2px;
            border: 1px solid #000;
        }

        .page-break {
            page-break-before: always;
        }

        .risk-table-page td {
            font-size: 8pt;
            line-height: 1.3;
        }

        .bg-extrim {
            background-color: #ff0000 !important;
            color: white;
            font-weight: bold;
        }

        .bg-tinggi {
            background-color: #ffff00 !important;
            color: black;
            font-weight: bold;
        }

        .bg-menengah {
            background-color: #0070c0 !important;
            color: white;
            font-weight: bold;
        }

        .bg-rendah {
            background-color: #92d050 !important;
            color: black;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <script type="text/php">
        if (isset($pdf)) {
            $pdf->page_script('
                $font = $fontMetrics->get_font("Times-Roman", "bold");
                $size = 8.5;
                $pageText = "Halaman " . $PAGE_NUM . " dari " . $PAGE_COUNT;
                // Koordinat Y disesuaikan agar nomor halaman berada di bawah footer
                $pdf->text(460, 815, $pageText, $font, $size);
            ');
        }
    </script>

    <header>
        <table class="header-table">
            <tr>
                <td width="15%" class="center">
                    <img src="{{ public_path('images/logo-msm.png') }}" width="65">
                </td>
                <td width="70%" class="center">
                    <strong style="font-size: 14pt;">TOKA TINDUNG PROJECT</strong><br>
                    <strong style="font-size: 11pt;">Formulir Laporan WPI KPLH</strong><br>
                    <span style="font-size: 9pt;">TT-MGT-FRS-024A</span>
                </td>
                <td width="15%" class="center">
                    <img src="{{ public_path('images/logo-archi.png') }}" width="65">
                </td>
            </tr>
        </table>
    </header>

    <footer>
        <table class="footer-table">
            <tr>
                <td width="25%">Nama Dokumen/<br><span class="en">Document Name</span></td>
                <td width="30%">Formulir Laporan WPI KPLH</td>
                <td width="25%">Tanggal Terbit/<br><span class="en">Date of Issue</span></td>
                <td width="20%">15 Maret 2023</td>
            </tr>
            <tr>
                <td>Ditetapkan Oleh/<br><span class="en">Determined By</span></td>
                <td>Kepala Teknik Tambang/<br><span class="en">Mining Technical Head</span></td>
                <td>Tanggal Tinjau Ulang/<br><span class="en">Review Date</span></td>
                <td>15 Maret 2026</td>
            </tr>
            <tr>
                <td>No Dokumen/<br><span class="en">No Document</span></td>
                <td>TT-MGT-FRS-024A</td>
                <td>No Revisi</td>
                <td>00</td>
            </tr>
            <tr>
                <td colspan="3" class="red-note">
                    Dokumen terkendali dan valid hanya ada di SharePoint Archi Indonesia
                </td>
                <td class="right" style="border-left: none;"></td>
            </tr>
        </table>
    </footer>

    <main>
        <table class="main-table" style="margin-bottom: 15px;">
            <tr>
                <td width="18%" class="bg-label">Tanggal / <span class="en" style="color:black">Date</span></td>
                <td width="32%">{{ date('d F Y', strtotime($report->report_date)) }}</td>
                <td width="25%" class="bg-label center">Nama Petugas Inspeksi / <br><span class="en"
                        [cite_start]style="color:black">Inspector Name</span></td>
                <td width="10%" class="bg-label center">ID</td>
                <td width="15%" class="bg-label center">Dept/Cont</td>
            </tr>
            @php $maxRows = 6; @endphp
            @for ($i = 0; $i < $maxRows; $i++)
                <tr>
                    @if ($i == 0)
                        <td class="bg-label">Jam / <span class="en" style="color:black">Time</span></td>
                        <td>{{ $report->report_time }}</td>
                    @elseif ($i == 1)
                        <td class="bg-label">Lokasi / <span class="en" style="color:black">Location</span></td>
                        <td>{{ $report->location ?? '-' }}</td>
                    @elseif ($i == 2)
                        <td class="bg-label">Site Name</td>
                        <td>Tokatindung</td>
                    @elseif ($i == 3)
                        <td class="bg-label">Area</td>
                        <td>{{ $report->area }}</td>
                    @elseif ($i == 4)
                        <td class="bg-label">Company</td>
                        <td>PT. MSM/PT. TTN</td>
                    @elseif ($i == 5)
                        <td class="bg-label">{{ $deptLabel }}</td>
                        <td>{{ $report->department }}</td>
                    @endif

                    <td>{{ isset($report->inspectors[$i]) ? $i + 1 . '. ' . $report->inspectors[$i]['name'] : '' }}
                    </td>
                    <td class="center">{{ $report->inspectors[$i]['id_number'] ?? '' }}</td>
                    <td class="center">{{ $report->inspectors[$i]['dept_con'] ?? '' }}</td>
                </tr>
            @endfor
        </table>

        <table class="main-table">
            <thead>
                <tr class="bg-label center">
                    <th width="6%">No</th>
                    <th width="8%">OHS Risk</th>
                    <th width="32%">Uraian Tindakan / Kondisi Tidak Aman<br><span class="en"
                            [cite_start]style="color:black; font-weight:normal">Unsafe Act / Unsafe Condition
                            Description</span>
                    </th>
                    <th width="28%">Jenis Tindakan Pencegahan<br><span class="en"
                            [cite_start]style="color:black; font-weight:normal">Type of Preventive Action</span></th>
                    <th width="28%">Tindak Lanjut / <span class="en"
                            [cite_start]style="color:black; font-weight:normal">Follow Up</span></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($report->findings as $index => $find)
                    <tr>
                        <td class="center">{{ $index + 1 }}</td>
                        <td class="center" style="font-weight: bold;">{{ $find->ohs_risk }}</td>
                        <td>
                            {{ $find->description }}<br><br>
                            @if (!empty($find->photos))
                                @foreach ($find->photos as $p)
                                    <img src="{{ public_path('storage/' . $p) }}" class="photo">
                                @endforeach
                            @endif
                        </td>
                        <td>
                            {{ $find->prevention_action }}<br><br>
                            @if (!empty($find->photos_prevention))
                                @foreach ($find->photos_prevention as $pp)
                                    <img src="{{ public_path('storage/' . $pp) }}" class="photo">
                                @endforeach
                            @endif
                        </td>
                        <td>
                            <strong>PIC:</strong>
                            @if (!empty($find->pic_responsible))
                                @php $picList = explode('|', $find->pic_responsible); @endphp
                                <ul style="margin: 0; padding-left: 15px; list-style-type: disc;">
                                    @foreach ($picList as $picName)
                                        <li>{{ trim($picName) }}</li>
                                    @endforeach
                                </ul>
                            @else
                                -
                            @endif
                            <div style="margin-top: 10px; padding-top: 5px; border-top: 1px dashed #000;">
                                <strong>Due:</strong>
                                {{ $find->due_date ? date('d-m-Y', strtotime($find->due_date)) : '-' }}<br>

                                <strong>Selesai:</strong>
                                {{ $find->completion_date ? date('d-m-Y', strtotime($find->completion_date)) : '-' }}
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </main>
</body>

</html>
