<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        @page {
            size: a4 landscape;
            margin: 110px 1cm 1.5cm 1cm;
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
        }

        .main-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .main-table td,
        .main-table td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
            vertical-align: middle;
            word-wrap: break-word;
        }

        .main-table th {
            border: 1px solid #000;
            background-color: #ffff00;
            padding: 2px 1px;         /* Padding lebih rapat */
            text-align: center;
            vertical-align: middle;
            word-wrap: break-word;
            font-size: 7pt;           /* Ukuran teks header lebih kecil */
            line-height: 1.1;         /* Jarak baris teks yang melipat lebih rapat */
            text-transform: uppercase; /* Membuat teks jadi kapital agar lebih tegas */
        }

        /* Style untuk Foto di Tabel */
        .img-doc {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border: 0.5px solid #ccc;
            display: block;
            margin: 0 auto;
        }

        .good {
            font-family: DejaVu Sans, sans-serif;
            color: green;
            font-weight: bold;
        }

        .nogood {
            font-family: DejaVu Sans, sans-serif;
            color: red;
            font-weight: bold;
        }

        .bg-gray {
            background-color: #f1f5f9;
        }

        .no-border {
            border: none !important;
        }
    </style>
</head>

<body>

    <header>
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="border-bottom: 2px solid #999; width: 15%; text-align: center;">
                    <img src="{{ public_path('images/logo-msm.png') }}" width="60">
                </td>
                <td style="border-bottom: 2px solid #999; width: 70%; text-align: center;">
                    <strong style="font-size: 14pt;">TOKA TINDUNG PROJECT</strong><br>
                    <strong style="font-size: 11pt;">LAPORAN INSPEKSI {{ strtoupper($type) }}</strong><br>
                </td>
                <td style="border-bottom: 2px solid #999; width: 15%; text-align: center;">
                    <img src="{{ public_path('images/logo-archi.png') }}" width="60">
                </td>
            </tr>
        </table>
    </header>

    <main>
        <div style="margin-bottom: 10px;">
            <strong>Periode:</strong> {{ $month }} | <strong>Area:</strong> {{ $area ?? 'Tokatindung Site' }}
        </div>

        <table class="main-table">
            <thead>
                <tr>
                    <th width="25px">NO</th>
                    <th width="120px">LOKASI</th>
                    @foreach ($structure['inputs'] as $header)
                        <th>{{ $header }}</th>
                    @endforeach
                    @foreach ($structure['checks'] as $header)
                        <th>{{ $header }}</th>
                    @endforeach
                    <th width="60px">TANGGAL</th>
                    <th width="60px">PEMERIKSA</th>
                    <th width="60px">FOTO</th> {{-- Header Baru --}}
                    <th width="80px">REMARKS</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td style="text-align: left;">{{ $item->equipmentMaster->specific_location }}</td>

                        @foreach ($structure['inputs'] as $input)
                            <td>{{ $item->conditions[$input] ?? '-' }}</td>
                        @endforeach

                        @foreach ($structure['checks'] as $check)
                            <td>
                                @php $val = $item->conditions[$check] ?? null; @endphp
                                @if ($val === true)
                                    <span class="good">✔</span>
                                @elseif($val === false)
                                    <span class="nogood">✘</span>
                                @else
                                    -
                                @endif
                            </td>
                        @endforeach

                        <td>{{ \Carbon\Carbon::parse($item->inspection_date)->format('d/m/y') }}</td>

                        <td>
                            @php
                                $names = explode('|', $item->inspected_by);
                                $formattedInitials = array_map(function ($n) {
                                    $words = preg_split('/[\s,]+/', trim($n));
                                    $initial = '';
                                    foreach ($words as $w) {
                                        if (!empty($w)) {
                                            $initial .= strtoupper(substr($w, 0, 1));
                                        }
                                    }
                                    return $initial;
                                }, $names);
                            @endphp
                            {{ implode(', ', $formattedInitials) }}
                        </td>

                        {{-- Kolom Dokumentasi --}}
                        <td>
                            @if ($item->documentation_path && file_exists(storage_path('app/public/' . $item->documentation_path)))
                                <img src="{{ storage_path('app/public/' . $item->documentation_path) }}"
                                    class="img-doc">
                            @else
                                <span style="color: #ccc; font-size: 7pt;">No Photo</span>
                            @endif
                        </td>

                        <td style="text-align: left;">{{ $item->remarks }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Tabel Legenda & Signature --}}
        <div style="margin-top: 25px; page-break-inside: avoid;">
            <table class="no-border" style="width: 100%;">
                <tr>
                    <td class="no-border" style="width: 20%; vertical-align: top;">
                        <table style="width: 100%; border: 1px solid black;">
                            <tr>
                                <th style="background-color: #eee; border: 1px solid black;">Note :</th>
                            </tr>
                            <tr>
                                <td
                                    style="border: 1px solid black; font-family: DejaVu Sans, sans-serif; text-align: left;">
                                    <span class="good">✔</span> Good</td>
                            </tr>
                            <tr>
                                <td
                                    style="border: 1px solid black; font-family: DejaVu Sans, sans-serif; text-align: left;">
                                    <span class="nogood">✘</span> No Good</td>
                            </tr>
                        </table>
                    </td>
                    <td class="no-border" style="width: 5%;"></td>
                    <td class="no-border" style="width: 45%; vertical-align: top;">
                        <table style="width: 100%; border: 1px solid black;">
                            <tr>
                                <td class="bg-gray"
                                    style="text-align: left; font-weight: bold; width: 120px; border: 1px solid black;">
                                    Input to Tosar by</td>
                                <td style="text-align: left; border: 1px solid black;">:
                                    {{ auth()->check() ? auth()->user()->name : '-' }}</td>
                            </tr>
                            <tr>
                                <td class="bg-gray"
                                    style="text-align: left; font-weight: bold; border: 1px solid black;">Date</td>
                                <td style="text-align: left; border: 1px solid black;">: {{ now()->format('d/m/Y') }}
                                </td>
                            </tr>
                            <tr>
                                <td class="bg-gray"
                                    style="text-align: left; font-weight: bold; border: 1px solid black;">Checked By
                                </td>
                                <td style="text-align: left; border: 1px solid black; height: 35px;">: </td>
                            </tr>
                        </table>
                    </td>
                    <td class="no-border" style="width: 5%;"></td>
                    <td class="no-border" style="width: 25%; vertical-align: top;">
                        <table style="width: 100%; border: 1px solid black;">
                            @php
                                $allNames = [];
                                foreach ($data as $d) {
                                    if (!empty($d->inspected_by)) {
                                        foreach (explode('|', $d->inspected_by) as $n) {
                                            $allNames[] = trim($n);
                                        }
                                    }
                                }
                                $uniqueNames = array_unique($allNames);
                            @endphp
                            @foreach ($uniqueNames as $name)
                                <tr>
                                    <td style="width: 45px; border: 1px solid black;" class="bg-gray">
                                        @php
                                            $words = preg_split('/[\s,]+/', $name);
                                            $init = '';
                                            foreach ($words as $w) {
                                                if (!empty($w)) {
                                                    $init .= strtoupper(substr($w, 0, 1));
                                                }
                                            }
                                            echo $init;
                                        @endphp
                                    </td>
                                    <td style="text-align: left; padding-left: 5px; border: 1px solid black;">
                                        {{ $name }}</td>
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
