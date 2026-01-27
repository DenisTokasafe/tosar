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
            <td class="no-border" style="width: 25%; vertical-align: top; text-align: left; padding: 0;">
                <table style="width: 100%;">
                    <tr>
                        <th colspan="2"
                            style="background-color: white; border: none; text-align: left; padding-bottom: 5px;">Note :
                        </th>
                    </tr>
                    <tr>
                        <td style="width: 30px; font-family: DejaVu Sans, sans-serif;">✔</td>
                        <td style="text-align: left;">: Good</td>
                    </tr>
                    <tr>
                        <td style="font-family: DejaVu Sans, sans-serif; color: red;">✘</td>
                        <td style="text-align: left;">: No Good</td>
                    </tr>
                </table>
            </td>

            <td class="no-border" style="width: 5%;"></td>

            <td class="no-border" style="width: 40%; vertical-align: top; padding: 0;">
                <table style="width: 100%;">
                    <tr>
                        <td class="bg-gray" style="text-align: left; font-weight: bold; width: 100px;">Input to Tosar by
                        </td>
                        <td style="text-align: left;">: {{ explode('|', $data->first()->inspected_by ?? '-')[0] }}</td>
                    </tr>

                    <tr>
                        <td class="bg-gray" style="text-align: left; font-weight: bold;">Date</td>
                        <td style="text-align: left;">: {{ now()->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td class="bg-gray" style="text-align: left; font-weight: bold;">Checked By</td>
                        <td style="text-align: left;">: ................................</td>
                    </tr>
                </table>
            </td>

            <td class="no-border" style="width: 30%; vertical-align: top; padding: 0 0 0 10px;">
                <table style="width: 100%;">
                    @php
                        $allNames = [];
                        foreach ($data as $d) {
                            // Memecah string 'Nama 1|Nama 2' menjadi array
                            foreach (explode('|', $d->inspected_by) as $n) {
                                $allNames[] = trim($n);
                            }
                        }
                        $uniqueNames = array_unique($allNames);
                    @endphp

                    @foreach ($uniqueNames as $name)
                        <tr>
                            <td style="width: 50px;" class="bg-gray">
                                @php
                                    // Logika Inisial: BANEA, Yoman Denis -> BYD
                                    $words = preg_split('/[\s,]+/', $name); // Memecah berdasarkan spasi atau koma
                                    $initials = '';
                                    foreach ($words as $w) {
                                        if (!empty($w)) {
                                            $initials .= strtoupper(substr($w, 0, 1));
                                        }
                                    }
                                    echo $initials;
                                @endphp
                            </td>
                            <td style="text-align: left; padding-left: 5px;">{{ $name }}</td>
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
