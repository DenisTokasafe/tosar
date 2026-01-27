<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        @page {
            size: a4 landscape;
            /* Margin atas dikurangi karena footer dihapus, margin bawah standar */
            margin: 110px 1cm 1cm 1cm;
        }

        header {
            position: fixed;
            top: -90px;
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

        .main-table, .header-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        /* Border Header (Warna abu-abu sesuai referensi Anda) */
        .header-table td {
            border: 1px solid #dcdcdc;
            padding: 4px 6px;
            vertical-align: middle;
        }

        /* Main table (Border hitam pekat) */
        .main-table td, .main-table th {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
            word-wrap: break-word;
        }

        .main-table th { background-color: #ffff00; text-transform: uppercase; }

        .bg-gray { background-color: #f1f5f9; }
        .center { text-align: center; }
        .good { font-family: DejaVu Sans, sans-serif; color: green; font-weight: bold; }
        .nogood { font-family: DejaVu Sans, sans-serif; color: red; font-weight: bold; }
        .no-border { border: none !important; }

        /* Utility thick border */
        .border-b-thick { border-bottom: 2px solid #999999 !important; }
        .border-l-none { border-left: none !important; }
        .border-r-none { border-right: none !important; }
    </style>
</head>

<body>
    {{-- Penomoran Halaman di Pojok Kanan Bawah --}}
    <script type="text/php">
        if (isset($pdf)) {
            $font = $fontMetrics->get_font("Times-Roman", "bold");
            $pdf->page_text(730, 565, "Halaman {PAGE_NUM} dari {PAGE_COUNT}", $font, 8, array(0,0,0));
        }
    </script>

    <header>
        <table class="header-table">
            <tr>
                <td width="15%" class="center border-b-thick border-l-none">
                    <img src="{{ public_path('images/logo-msm.png') }}" width="60">
                </td>
                <td width="70%" class="center border-b-thick">
                    <strong style="font-size: 14pt;">TOKA TINDUNG PROJECT</strong><br>
                    <strong style="font-size: 11pt;">LAPORAN INSPEKSI {{ strtoupper($type) }}</strong><br>
                    <span style="font-size: 9pt;">FIRE PROTECTION MAINTENANCE SYSTEM</span>
                </td>
                <td width="15%" class="center border-b-thick border-r-none">
                    <img src="{{ public_path('images/logo-archi.png') }}" width="60">
                </td>
            </tr>
        </table>
    </header>

    <main>
        <div style="margin-bottom: 10px;">
            <strong>Periode:</strong> {{ $month }} | <strong>Kategori:</strong> {{ $type }}
        </div>

        <table class="main-table">
            <thead>
                <tr>
                    <th width="30px">NO</th>
                    <th>LOKASI</th>
                    @foreach($structure['inputs'] as $header) <th>{{ $header }}</th> @endforeach
                    @foreach($structure['checks'] as $header) <th>{{ $header }}</th> @endforeach
                    <th width="70px">TANGGAL</th>
                    <th width="70px">PEMERIKSA</th>
                    <th width="100px">REMARKS</th>
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
                            $names = explode('|', $item->inspected_by);
                            $formattedInitials = array_map(function($n) {
                                $words = preg_split("/[\s,]+/", trim($n));
                                $initial = '';
                                foreach ($words as $w) { if (!empty($w)) $initial .= strtoupper(substr($w, 0, 1)); }
                                return $initial;
                            }, $names);
                        @endphp
                        {{ implode(', ', $formattedInitials) }}
                    </td>
                    <td style="text-align: left;">{{ $item->remarks }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Tabel Legenda & Signature (Hanya muncul sekali di akhir data) --}}
        <div style="margin-top: 25px; page-break-inside: avoid;">
            <table class="no-border" style="width: 100%;">
                <tr>
                    {{-- Note --}}
                    <td class="no-border" style="width: 20%; vertical-align: top; padding: 0;">
                        <table style="width: 100%; border: 1px solid black;">
                            <tr><th style="background-color: #eee; border: 1px solid black;">Note :</th></tr>
                            <tr><td style="border: 1px solid black; font-family: DejaVu Sans, sans-serif;"><span class="good">✔</span> Good</td></tr>
                            <tr><td style="border: 1px solid black; font-family: DejaVu Sans, sans-serif;"><span class="nogood">✘</span> No Good</td></tr>
                        </table>
                    </td>

                    <td class="no-border" style="width: 5%;"></td>

                    {{-- Signature Box --}}
                    <td class="no-border" style="width: 45%; vertical-align: top; padding: 0;">
                        <table style="width: 100%; border: 1px solid black;">
                            <tr>
                                <td class="bg-gray" style="text-align: left; font-weight: bold; width: 120px; border: 1px solid black;">Input to Tosar by</td>
                                <td style="text-align: left; border: 1px solid black;">: {{ auth()->check() ? auth()->user()->initials() : '-' }}</td>
                            </tr>
                            <tr>
                                <td class="bg-gray" style="text-align: left; font-weight: bold; border: 1px solid black;">Date</td>
                                <td style="text-align: left; border: 1px solid black;">: {{ now()->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <td class="bg-gray" style="text-align: left; font-weight: bold; border: 1px solid black;">Checked By</td>
                                <td style="text-align: left; border: 1px solid black; height: 30px;">: </td>
                            </tr>
                        </table>
                    </td>

                    <td class="no-border" style="width: 5%;"></td>

                    {{-- Legend Inisial --}}
                    <td class="no-border" style="width: 25%; vertical-align: top; padding: 0;">
                        <table style="width: 100%; border: 1px solid black;">
                            @php
                                $allNames = [];
                                foreach ($data as $d) {
                                    foreach (explode('|', $d->inspected_by) as $n) { $allNames[] = trim($n); }
                                }
                                $uniqueNames = array_unique($allNames);
                            @endphp
                            @foreach ($uniqueNames as $name)
                                <tr>
                                    <td style="width: 45px; border: 1px solid black;" class="bg-gray">
                                        @php
                                            $words = preg_split('/[\s,]+/', $name);
                                            $init = '';
                                            foreach ($words as $w) { if (!empty($w)) $init .= strtoupper(substr($w, 0, 1)); }
                                            echo $init;
                                        @endphp
                                    </td>
                                    <td style="text-align: left; padding-left: 5px; border: 1px solid black;">{{ $name }}</td>
                                </tr>
                            @endforeach
                        </table>
                    </td>
                </tr>
            </table>
        </div>
    </main>
</body>
</html>
