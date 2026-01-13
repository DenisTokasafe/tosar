<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        @page {
            /* Margin yang cukup untuk header & footer agar tidak tertimpa isi main */
            margin: 110px 1cm 110px 1cm;
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
            bottom: -95px;
            left: 0;
            right: 0;
            height: 100px;
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

        .bg-gray {
            background-color: #ffffff; /* Sesuai gambar, background putih bersih */
        }

        /* Styling teks bilingual (Biru Muda/Italic untuk Inggris) */
        .eng-text {
            color: #1e40af;
            font-style: italic;
        }

        /* Styling teks merah di tengah footer */
        .red-warning {
            color: #ff0000;
            font-weight: bold;
            font-size: 10pt; /* Lebih besar sesuai gambar */
        }

        .center { text-align: center; }
        .right { text-align: right; }

        /* Penomoran Halaman */
        .pagenum:before { content: counter(page); }
        .totalpage:before { content: counter(pages); }

        /* Khusus untuk Main Content */
        main .main-table td { vertical-align: top; }
        img.photo { width: 130px; height: auto; margin: 3px; border: 0.5px solid #000; }
    </style>
</head>
<body>

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
                <td width="35%">Nama Dokumen/<span class="eng-text">Document Name</span></td>
                <td width="70%" colspan="3">Formulir Laporan WPI KPLH</td>
            </tr>
            <tr>
                <td width="35%">Ditetapkan Oleh/<span class="eng-text">Determined By</span></td>
                <td width="35%">Kepala Teknik Tambang/<span class="eng-text">Mining Technical Head</span></td>
                <td width="20%">Tanggal Terbit /<span class="eng-text">Date of Issue</span></td>
                <td width="15%">15 Maret 2023</td>
            </tr>
            <tr>
                <td width="35%">No Dokumen/<span class="eng-text">No Document</span></td>
                <td width="35%">TT-MGT-FRS-024A</td>
                <td width="20%">Tanggal Tinjau Ulang / <span class="eng-text">Review Date</span></td>
                <td width="15%">15 Maret 2026</td>
            </tr>
            <tr>
                <td width="12%">No Revisi</td>
                <td width="8%" class="center">00</td>
                <td colspan="2" width="55%" class="center">
                    <span class="red-warning">Dokumen terkendali dan valid hanya ada di sharepoint Archi Indonesia</span>
                </td>
                <td width="25%" class="right">
                    <strong>Halaman <span class="pagenum"></span> dari <span class="totalpage"></span></strong>
                </td>
            </tr>
        </table>
    </footer>

    <main>
        <table class="main-table" style="margin-bottom: 10px;">
            <tr style="background-color: #e2e8f0; font-weight: bold;">
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
                    <td style="background-color: #e2e8f0; font-weight: bold;">Jam /Time</td>
                    <td>{{ $report->report_time }}</td>
                @elseif ($i == 1)
                    <td style="background-color: #e2e8f0; font-weight: bold;">Lokasi/Location</td>
                    <td>{{ $report->location}}</td>
                @elseif ($i == 2)
                    <td style="background-color: #e2e8f0; font-weight: bold;">Site Name</td>
                    <td>Tokatindung</td>
                @elseif ($i == 3)
                    <td style="background-color: #e2e8f0; font-weight: bold;">Area</td>
                    <td>Mining</td>
                @elseif ($i == 4)
                    <td style="background-color: #e2e8f0; font-weight: bold;">Company</td>
                    <td>PT MSM</td>
                @elseif ($i == 5)
                    <td style="background-color: #e2e8f0; font-weight: bold;">Department</td>
                    <td>{{ $report->department }}</td>
                @endif

                <td>{{ isset($report->inspectors[$i]) ? ($i + 1) . '. ' . $report->inspectors[$i]['name'] : '' }}</td>
                <td class="center">{{ $report->inspectors[$i]['id_number'] ?? '' }}</td>
                <td class="center">{{ $report->inspectors[$i]['dept_con'] ?? '' }}</td>
            </tr>
            @endfor
            <tr>
                <td style="background-color: #e2e8f0; font-weight: bold;">Contractor</td>
                <td>{{ $report->searchContractor ?? '-' }}</td>
                <td style="background-color: #e2e8f0; font-weight: bold;" class="center">Direview oleh/Reviewing by:</td>
                <td colspan="2" class="center">
                    <div style="height: 25px;"></div>
                    <strong>MARDIN</strong><br>
                    ID: 3242289
                </td>
            </tr>
        </table>

        <table class="main-table">
            <thead>
                <tr style="background-color: #e2e8f0; font-weight: bold;" class="center">
                    <th width="4%">No</th>
                    <th width="6%">OHS Risk</th>
                    <th width="30%">Uraian Tindakan Tidak Aman / Kondisi Tidak Aman</th>
                    <th width="30%">Jenis Tindakan Pencegahan</th>
                    <th width="30%">Tindak Lanjut / Follow Up</th>
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
