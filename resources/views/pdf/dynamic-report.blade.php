<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        @page {
            size: a4 landscape; /* Disetel landscape sesuai kebutuhan tabel inspeksi */
            margin: 130px 1cm 110px 1cm;
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
            bottom: -90px;
            left: 0;
            right: 0;
            height: 100px;
        }

        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 8pt;
            margin: 0;
            padding: 0;
            line-height: 1.2;
        }

        .header-table, .footer-table, .main-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        /* Border Abu-abu untuk Header/Footer */
        .header-table td, .footer-table td {
            border: 1px solid #999;
            padding: 4px;
            vertical-align: middle;
        }

        /* Border Hitam untuk Tabel Data Utama */
        .main-table th, .main-table td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
            word-wrap: break-word;
        }

        .main-table th { background-color: #ffff00; font-weight: bold; }

        .en { color: #1e40af; font-style: italic; font-size: 7pt; }
        .center { text-align: center; }
        .bg-gray { background-color: #f1f5f9; }
        .good { font-family: DejaVu Sans, sans-serif; color: green; font-weight: bold; }
        .nogood { font-family: DejaVu Sans, sans-serif; color: red; font-weight: bold; }

        /* Utility Border Thick */
        .border-b-thick { border-bottom: 2px solid #999 !important; }
        .border-l-none { border-left: none !important; }
        .border-r-none { border-right: none !important; }
    </style>
</head>

<body>
    {{-- Script Penomoran Halaman --}}
    <script type="text/php">
        if (isset($pdf)) {
            $font = $fontMetrics->get_font("Times-Roman", "bold");
            $pdf->page_text(730, 560, "Halaman {PAGE_NUM} dari {PAGE_COUNT}", $font, 7, array(0,0,0));
        }
    </script>

    <header>
        <table class="header-table">
            <tr>
                <td width="12%" class="center border-l-none">
                    <img src="{{ public_path('images/logo-msm.png') }}" width="50">
                </td>
                <td width="76%" class="center">
                    <strong style="font-size: 12pt;">TOKA TINDUNG PROJECT</strong><br>
                    <strong style="font-size: 10pt;">LAPORAN INSPEKSI {{ strtoupper($type) }}</strong><br>
                    <span style="font-size: 8pt;">FIRE PROTECTION MAINTENANCE SYSTEM</span>
                </td>
                <td width="12%" class="center border-r-none">
                    <img src="{{ public_path('images/logo-archi.png') }}" width="50">
                </td>
            </tr>
        </table>
    </header>

    <footer>
        <table class="footer-table">
            <tr>
                <td class="border-l-none" width="20%">Nama Dokumen / <span class="en">Doc Name</span></td>
                <td width="40%">Laporan Inspeksi Bulanan {{ $type }}</td>
                <td width="15%" class="bg-gray">No Dokumen</td>
                <td class="border-r-none" width="25%">TT-FIRE-{{ str_replace(' ', '-', strtoupper($type)) }}</td>
            </tr>
            <tr>
                <td class="border-l-none">Ditetapkan Oleh / <span class="en">By</span></td>
                <td>Safety & Fire Dept. Head</td>
                <td class="bg-gray">Tanggal Terbit</td>
                <td class="border-r-none">15-03-2023</td>
            </tr>
            <tr>
                <td class="border-l-none">No Revisi</td>
                <td>01</td>
                <td colspan="2" class="border-r-none center" style="color: red; font-size: 7pt;">
                    Valid di SharePoint Archi Indonesia - Dicetak oleh: {{ auth()->user()->name ?? 'System' }}
                </td>
            </tr>
        </table>
    </footer>

    <main>
        <div style="margin-bottom: 10px; font-weight: bold;">
            Month: {{ $month }} <br>
            Area: {{ $area ?? 'Tokatindung Site' }}
        </div>

        <table class="main-table">
            <thead>
                <tr>
                    <th style="width: 25px;">NO</th>
                    <th>LOKASI</th>
                    @foreach($structure['inputs'] as $header) <th>{{ $header }}</th> @endforeach
                    @foreach($structure['checks'] as $header) <th>{{ $header }}</th> @endforeach
                    <th style="width: 60px;">TANGGAL</th>
                    <th>PEMERIKSA</th>
                    <th>REMARKS</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="text-align: left;">{{ $item->location }}</td>
                    @foreach($structure['inputs'] as $input)
                        <td>{{ $item->conditions[$input] ?? '-' }}</td>
                    @endforeach
                    @foreach($structure['checks'] as $check)
                        <td>
                            @php $val = $item->conditions[$check] ?? null; @endphp
                            @if($val === true) <span class="good">✔</span>
                            @elseif($val === false) <span class="nogood">✘</span>
                            @else - @endif
                        </td>
                    @endforeach
                    <td>{{ \Carbon\Carbon::parse($item->inspection_date)->format('d/m/y') }}</td>
                    <td>
                        @php
                            $words = preg_split("/[\s,]+/", $item->inspected_by);
                            $initials = '';
                            foreach ($words as $w) { if(!empty($w)) $initials .= strtoupper(substr($w, 0, 1)); }
                        @endphp
                        {{ $initials }}
                    </td>
                    <td style="text-align: left;">{{ $item->remarks }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Section Legend & Signature --}}
        <div style="margin-top: 15px;">
            <table style="width: 100%; border: none;">
                <tr>
                    <td style="border: none; vertical-align: top; width: 30%;">
                        <table class="main-table">
                            <tr><th colspan="2" style="background: #eee;">Note</th></tr>
                            <tr><td class="good">✔</td><td style="text-align: left;">Good</td></tr>
                            <tr><td class="nogood">✘</td><td style="text-align: left;">No Good</td></tr>
                        </table>
                    </td>
                    <td style="border: none; width: 40%;">
                        <table class="main-table">
                            <tr><td class="bg-gray" style="text-align: left;">Input to INX by:</td><td>{{ auth()->user() ? auth()->user()->initials() : '---' }}</td></tr>
                            <tr><td class="bg-gray" style="text-align: left;">Checked by:</td><td>................</td></tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
    </main>
</body>
</html>
