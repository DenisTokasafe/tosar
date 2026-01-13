<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
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

        .main-table,
        .footer-table,
        .header-table {
            width: 100%;
            border-collapse: collapse;
            /* Memaksa tabel mengikuti ukuran layout kertas A4 */
            table-layout: fixed;
        }

        .main-table td,
        .main-table th,
        .footer-table td,
        .header-table td {
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

        .en {
            color: #1e40af;
            font-style: italic;
        }

        .red-note {
            color: #ff0000;
            font-weight: bold;
            font-size: 9pt;
            text-align: center;
        }

        .bg-label {
            background-color: rgba(226, 232, 240, 0.7);
            font-weight: bold;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
        }

        img.photo {
            width: 130px;
            max-width: 100%;
            height: auto;
            margin: 3px;
            border: 0.5px solid #000;
        }

        /* --- Style Tambahan Untuk Tabel Risk Level --- */
        .page-break {
            page-break-before: always;
        }

        .risk-table-page {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-top: 10px;
        }

        .risk-table-page td,
        .risk-table-page th {
            border: 1px solid #000;
            padding: 5px;
            font-size: 8.5pt;
            vertical-align: middle;
        }

        .bg-extrim { background-color: #ff0000; color: white; font-weight: bold; }
        .bg-tinggi { background-color: #ffff00; color: black; font-weight: bold; }
        .bg-menengah { background-color: #0070c0; color: white; font-weight: bold; }
        .bg-rendah { background-color: #92d050; color: black; font-weight: bold; }
    </style>
</head>

<body>

    <script type="text/php">
        if (isset($pdf)) {
            $pdf->page_script('
                $font = $fontMetrics->get_font("Times-Roman", "bold");
                $size = 8.5;
                $pageText = "Halaman " . $PAGE_NUM . " dari " . $PAGE_COUNT;
                $pdf->text(480, 809, $pageText, $font, $size);
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
                <td width="15%" class="center"><img src="{{ public_path('images/logo-archi.png') }}" width="60">
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
                        <td>{{ $report->location ?? '-' }}</td>
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
                    <th width="4%">No</th>
                    <th width="7%">OHS Risk</th>
                    <th width="32%">Uraian Tindakan/Kondisi Tidak Aman</th>
                    <th width="30%">Jenis Tindakan Pencegahan</th>
                    <th width="27%">Tindak Lanjut / Follow Up</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($report->findings as $index => $find)
                    <tr>
                        <td class="center">{{ $index + 1 }}</td>
                        <td class="center">{{ $find->ohs_risk }}</td>
                        <td style="vertical-align: top;">
                            {{ $find->description }}<br>
                            @if (!empty($find->photos))
                                @foreach ($find->photos as $p)
                                    <img src="{{ public_path('storage/' . $p) }}" class="photo">
                                @endforeach
                            @endif
                        </td>
                        <td style="vertical-align: top;">
                            {{ $find->prevention_action }}<br>
                            @if (!empty($find->photos_prevention))
                                @foreach ($find->photos_prevention as $pp)
                                    <img src="{{ public_path('storage/' . $pp) }}" class="photo">
                                @endforeach
                            @endif
                        </td>
                        <td style="vertical-align: top;">
                            <strong>PIC:</strong><br>
                            @if (!empty($find->pic_responsible))
                                @php
                                    $picList = explode('|', $find->pic_responsible);
                                @endphp

                                <ul style="margin: 0; padding-left: 12px; list-style-type: none;">
                                    @foreach ($picList as $picName)
                                        <li style="margin-bottom: 2px;">• {{ trim($picName) }}</li>
                                    @endforeach
                                </ul>
                            @else
                                -
                            @endif

                            <div style="margin-top: 5px; border-top: 0.5px solid #ccc; pt-2">
                                <strong>Due:</strong>
                                {{ $find->due_date ? date('d/m/y', strtotime($find->due_date)) : '-' }}<br>
                                <strong>Selesai:</strong>
                                {{ $find->completion_date ? date('d/m/y', strtotime($find->completion_date)) : '-' }}
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="page-break"></div>

        <h3 class="center">Level Resiko/ Risk Level</h3>
        <table class="risk-table-page">
            <thead>
                <tr class="bg-label center">
                    <th width="80%">Deskripsi / Description</th>
                    <th width="20%">Kode OHS Risk / Code of OHS Risk</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        Prioritas tindakan <strong>"Extrim (E)"</strong>: Menangani resiko bahaya yang mengancam keselamatan jiwa atau kesehatan dengan potensi kejadian level 4 atau 5 (misalnya, bekerja di ketinggian scaffolding atau tower tanpa mengenakan full body harness dan dua lanyard, atau memasuki ruang terbatas tanpa mendapatkan izin dan melakukan uji gas/udara sebagaimana mestinya).<br>
                        <span class="en">"Extreme (E)" Priority actions address risk hazards immediately dangerous to life or health with the potential event Level 4 or 5 event (e.g., working at height on scaffolding or tower without wearing fullbody harness and double lanyard, or entering a confined space without a permit and proper oxygen/gas testing).</span>
                    </td>
                    <td class="center bg-extrim">E - Ekstrim<br><span class="en">E - Extreme</span></td>
                </tr>
                <tr>
                    <td>
                        Prioritas tindakan <strong>"Tinggi (T)"</strong>: Menangani kondisi atau praktik kerja yang membahayakan keselamatan manusia, merusak properti dan menggangu proses kerja (misalnya, melakukan pengelasan di unit/bangunan tanpa dilengkapi dengan ijin kerja dan alat pemadam kebakaran atau mengamati pekerja yang menggunakan peralatan kerja yang tidak sesuai dengan jenis pekerjaan).<br>
                        <span class="en">"High (H)" Priority actions address a condition or practice which could cause harm to people, property and processes (e.g., welding on unit/building without work permit and proper fire suppression support or observing someone using an incorrect hand tool for the job).</span>
                    </td>
                    <td class="center bg-tinggi">T - Tinggi<br><span class="en">T - Tinggi</span></td>
                </tr>
                <tr>
                    <td>
                        Prioritas tindakan <strong>"Menengah (M)"</strong>: Menangani pelanggaran peraturan K3 atau terdapat kekurangan yang membutuhkan tindakan perbaikan meskipun tidak begitu berbahaya, atau memerlukan upaya pencegahan agar tidak timbul kejadian serupa di kemudian hari (misalnya, tempat cuci mata yang rusak, akses keluar atau akses menuju lokasi alat pemadam kebakaran yang terhambat, pelindung yang rusak atau retak, atau label peringatan pipa).<br>
                        <span class="en">"Moderate (M)" Priority actions address safety violations or deficiencies requiring corrective action but are not immediately dangerous, or a preventative measure to prevent the same (e.g., inoperable eye wash station, blocking an exit or fire extinguisher, broken or cracked guards, or labeling of pipelines).</span>
                    </td>
                    <td class="center bg-menengah">M - Menengah<br><span class="en">M - Moderate</span></td>
                </tr>
                <tr>
                    <td>
                        Prioritas tindakan <strong>"Rendah (L)"</strong>: Menangani pelanggaran peraturan K3 atau terdapat kekurangan yang membutuhkan tindakan perbaikan yang tidak begitu siknifikan dampaknya terhadap manusia maupun lingkungan. (misalnya, sampah berceceran di lantai, rembesan air, balon lampu mati, label tanda peringatan buram dll..<br>
                        <span class="en">Priority action "Low (L)": Addressing violations of K3 regulations or there are deficiencies that require corrective actions that have little impact on humans or the environment. (e.g., trash splattered on the floor, water seepage, balloon lights out, blurry warning labels etc..</span>
                    </td>
                    <td class="center bg-rendah">L - Rendah<br><span class="en">L - Low</span></td>
                </tr>
            </tbody>
        </table>
    </main>
</body>

</html>
