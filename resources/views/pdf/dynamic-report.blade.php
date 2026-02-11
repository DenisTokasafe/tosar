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

        .main-table th,
        .main-table td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
            vertical-align: middle;
            word-wrap: break-word;
        }

        .main-table th {
            background-color: #ffff00;
            font-size: 7pt;
            line-height: 1.1;
            text-transform: uppercase;
        }

        /* Style Baru untuk Lampiran Foto */
        .photo-section {
            page-break-before: always;
            margin-top: 20px;
        }

        .photo-grid {
            width: 100%;
        }

        .photo-card {
            width: 31%;
            display: inline-block;
            margin: 1%;
            border: 1px solid #000;
            vertical-align: top;
            background-color: #fff;
        }

        .photo-img {
            width: 100%;
            height: 160px;
            object-fit: cover;
            border-bottom: 1px solid #000;
        }

        .photo-caption {
            padding: 5px;
            text-align: left;
            font-size: 7pt;
            line-height: 1.2;
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

        .legend-table {
            width: 100%;
            border-collapse: collapse;
        }

        .legend-table td,
        .legend-table th {
            border: 1px solid black;
            padding: 3px;
        }
    </style>
</head>

<body>

    <header>
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="border-bottom: 2px solid #999; width: 15%; text-align: center;"><img
                        src="{{ public_path('images/logo-msm.png') }}" width="60"></td>
                <td style="border-bottom: 2px solid #999; width: 70%; text-align: center;">
                    <strong style="font-size: 14pt;">TOKA TINDUNG PROJECT</strong><br>
                    <strong style="font-size: 11pt;">LAPORAN INSPEKSI {{ strtoupper($type) }}</strong>
                </td>
                <td style="border-bottom: 2px solid #999; width: 15%; text-align: center;"><img
                        src="{{ public_path('images/logo-archi.png') }}" width="60"></td>
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
                    <th width="60px">DIPERIKSA OLEH</th>
                    <th width="120px">REMARKS</th>
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
                                @if ($val === true || $val === 'true' || $val === 1)
                                    <span class="good">✔</span>
                                @elseif($val === false || $val === 'false' || $val === 0)
                                    <span class="nogood">✘</span>
                                @else
                                    -
                                @endif
                            </td>
                        @endforeach
                        <td> {{ \Carbon\Carbon::parse($item->inspectionSession->inspection_date)->format('d/m/Y') }}</td>
                        <td>
                    @php
                                $daftarNama = explode('|', $item->inspected_by ?? '');
                            @endphp
                            @foreach ($daftarNama as $namaOrang)
                                @php
                                    if (empty(trim($namaOrang))) {
                                        continue;
                                    }
                                    // 1. Hapus tanda kutip (") DAN ubah koma (,) menjadi spasi
$search = ['"', ','];
$replace = ['', ' '];
$cleanName = str_replace($search, $replace, $namaOrang);

// 2. Ambil inisial dari tiap kata yang sudah bersih
$initials = collect(preg_split('/\s+/', trim($cleanName)))
    ->filter()
    ->map(fn($word) => strtoupper(substr($word, 0, 1)))
    ->implode('');
                                @endphp
                                {{ $initials }}
                            @endforeach
                        </td>
                        <td style="text-align: left;">{{ $item->remarks }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="photo-section" style="font-family: Arial, sans-serif;">
            <div
                style="background-color: #eee; padding: 5px; text-align: center; border: 1px solid #000; margin-bottom: 20px;">
                <strong style="font-size: 10pt; letter-spacing: 1px;">LAMPIRAN DOKUMENTASI FOTO</strong>
            </div>

            @php
                $firstItem = $data->first();
                $areaPhotoPath =
                    $firstItem && $firstItem->inspectionSession ? $firstItem->inspectionSession->area_photo_path : null;

                $documentationPhotos = $data->filter(
                    fn($item) => $item->documentation_path &&
                        file_exists(storage_path('app/public/' . $item->documentation_path)),
                );

                $areaPhotoExists = $areaPhotoPath && file_exists(storage_path('app/public/' . $areaPhotoPath));
            @endphp

            <div style="width: 100%; display: block;">
                {{-- 1. FOTO INSPEKSI AREA (KIRI) --}}
                @if ($areaPhotoExists)
                    <div
                        style="width: 30%; float: left; margin-right: 20px; border: 1px solid #000; box-sizing: border-box;">
                        <div style="padding: 5px;">
                            <img src="{{ storage_path('app/public/' . $areaPhotoPath) }}"
                                style="width: 100%; height: 200px; object-fit: cover; display: block;">
                        </div>
                        <div
                            style="background-color: #fcd5b4; border-top: 1px solid #000; padding: 10px; min-height: 60px;">
                            <span style="font-size: 11pt; font-weight: bold;">Foto Inspeksi Area :</span><br>
                            <span style="font-size: 11pt; color: blue; text-decoration: underline;">
                                {{ $firstItem->inspectionSession->area_name ?? 'Environment' }}
                            </span>
                        </div>
                    </div>
                @endif

                {{-- 2. FOTO DOKUMENTASI (KANAN - BERJEJER) --}}
                @foreach ($documentationPhotos as $item)
                    <div
                        style="width: 25%; float: left; margin-right: 15px; border: 1px solid #000; box-sizing: border-box; margin-bottom: 20px;">
                        <div style="padding: 5px;">
                            <img src="{{ storage_path('app/public/' . $item->documentation_path) }}"
                                style="width: 100%; height: 200px; object-fit: cover; display: block;">
                        </div>
                        <div
                            style="background-color: #fcd5b4; border-top: 1px solid #000; padding: 8px; font-size: 9pt; min-height: 80px;">
                            <strong>No:</strong> {{ $loop->iteration }}<br>
                            <strong>Lokasi :</strong>
                            {{ $item->equipmentMaster->specific_location ?? 'Environment' }}<br>
                            <strong>Ket :</strong> {{ Str::limit($item->remarks ?? '-', 30) }}
                        </div>
                    </div>
                @endforeach

                <div style="clear: both;"></div>
            </div>

            @if (!$areaPhotoExists && $documentationPhotos->isEmpty())
                <div style="text-align: center; color: #999; padding: 40px; border: 1px dashed #ccc;">
                    Tidak ada lampiran foto dokumentasi.
                </div>
            @endif
        </div>


        {{-- Footer Section --}}
        <div style="margin-top: 30px; page-break-inside: avoid;">
            <table class="no-border" style="width: 100%;">
                <tr>
                    {{-- Legenda --}}
                    <td class="no-border" style="width: 15%; vertical-align: top;">
                        <table class="legend-table">
                            <tr>
                                <th class="bg-gray">Keerangan</th>
                            </tr>
                            <tr>
                                <td><span class="good">✔</span> Baik</td>
                            </tr>
                            <tr>
                                <td><span class="nogood">✘</span> Rusak / Tidak Baik</td>
                            </tr>
                        </table>
                    </td>

                    <td class="no-border" style="width: 5%;"></td>

                    {{-- Approval --}}
                    <td class="no-border" style="width: 45%; vertical-align: top;">
                        <table class="legend-table">
                            <tr>
                                <td class="bg-gray" style="width: 100px; font-weight: bold;">Di Input Oleh</td>
                                <td>: {{ $submitted_by ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="bg-gray" style="font-weight: bold; ">Nomor Inspeksi</td>
                                <td>: {{ $inspection_number ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="bg-gray" style="font-weight: bold;">Date</td>
                                <td>: {{ $tgl }}</td>
                            </tr>
                        </table>
                    </td>

                    <td class="no-border" style="width: 5%;"></td>

                    {{-- Inisial Pemeriksa --}}
                    <td class="no-border" style="width: 30%; vertical-align: top;">
                        <table class="legend-table">
                            @php
                                $daftarNama = collect($data)
                                    ->pluck('inspected_by')
                                    ->flatMap(fn($item) => explode('|', $item))
                                    ->map(fn($name) => trim($name))
                                    ->unique()
                                    ->filter();
                            @endphp
                            <tr>
                                <th colspan="2">Inisial Pemeriksa</th>
                            </tr>
                            @foreach ($daftarNama as $name)
                                <tr>
                                    <td class="bg-gray" style="width: 40px; text-align: center; font-weight: bold;">
                                        @php
                                            if (empty(trim($name))) {
                                                continue;
                                            }
                                            // 1. Hapus tanda kutip (") DAN ubah koma (,) menjadi spasi
$search = ['"', ','];
$replace = ['', ' '];
$cleanName = str_replace($search, $replace, $name);

// 2. Ambil inisial dari tiap kata yang sudah bersih
$initials = collect(preg_split('/\s+/', trim($cleanName)))
    ->filter()
    ->map(fn($word) => strtoupper(substr($word, 0, 1)))
    ->implode('');
                                        @endphp
                                        {{ $initials }}
                                    </td>
                                    <td style="text-align: left;"> {{ trim(str_replace('"', '', $name)) }}</td>
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
