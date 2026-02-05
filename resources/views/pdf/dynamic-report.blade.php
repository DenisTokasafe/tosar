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
                    <th width="60px">CHECK BY</th>
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
                        <td>{{ \Carbon\Carbon::parse($item->inspection_date)->format('d/m/y') }}</td>
                        <td>
                            @php
                                $names = explode('|', $item->inspected_by);
                                $initials = collect($names)
                                    ->map(function ($n) {
                                        return collect(preg_split('/[\s,]+/', trim($n)))
                                            ->map(fn($w) => strtoupper(substr($w, 0, 1)))
                                            ->implode('');
                                    })
                                    ->implode(', ');
                            @endphp
                            {{ $initials }}
                        </td>
                        <td style="text-align: left;">{{ $item->remarks }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="photo-section">
            <div
                style="background-color: #eee; padding: 5px; text-align: center; border: 1px solid #000; margin-bottom: 15px;">
                <strong style="font-size: 10pt;">LAMPIRAN DOKUMENTASI FOTO</strong>
            </div>

            <div class="photo-grid">
                @php
                    $hasPhoto = false;
                    // Ambil foto area dari record pertama yang memiliki area_photo_path
                    $areaPhoto = $data->whereNotNull('area_photo_path')->first();
                @endphp

                @if ($areaPhoto && file_exists(storage_path('app/public/' . $areaPhoto->area_photo_path)))
                    @php $hasPhoto = true; @endphp
                    <div class="photo-card" style="width: 64.5%; border: 2px solid #000;"> <img
                            src="{{ storage_path('app/public/' . $areaPhoto->area_photo_path) }}" class="photo-img"
                            style="height: 250px;">
                        <div class="photo-caption"
                            style="background-color: #ffff00; font-weight: bold; text-align: center;">
                            FOTO AREA: {{ $area ?? 'Tokatindung Site' }}
                        </div>
                    </div>
                @endif

                <div style="clear: both; margin-bottom: 10px;"></div>

                @foreach ($data as $index => $item)
                    @if ($item->documentation_path && file_exists(storage_path('app/public/' . $item->documentation_path)))
                        @php $hasPhoto = true; @endphp
                        <div class="photo-card">
                            <img src="{{ storage_path('app/public/' . $item->documentation_path) }}" class="photo-img">
                            <div class="photo-caption">
                                <strong>No:</strong> {{ $index + 1 }}<br>
                                <strong>Lokasi:</strong> {{ $item->equipmentMaster->specific_location }}<br>
                                <strong>Remarks:</strong> {{ $item->remarks ?? '-' }}
                            </div>
                        </div>
                    @endif
                @endforeach

                @if (!$hasPhoto)
                    <div style="text-align: center; color: #999; padding: 20px;">Tidak ada lampiran foto.</div>
                @endif
            </div>
        </div>

        {{-- Footer Section --}}
        <div style="margin-top: 30px; page-break-inside: avoid;">
            <table class="no-border" style="width: 100%;">
                <tr>
                    {{-- Legenda --}}
                    <td class="no-border" style="width: 15%; vertical-align: top;">
                        <table class="legend-table">
                            <tr>
                                <th class="bg-gray">Note:</th>
                            </tr>
                            <tr>
                                <td><span class="good">✔</span> Good</td>
                            </tr>
                            <tr>
                                <td><span class="nogood">✘</span> No Good</td>
                            </tr>
                        </table>
                    </td>

                    <td class="no-border" style="width: 5%;"></td>

                    {{-- Approval --}}
                    <td class="no-border" style="width: 45%; vertical-align: top;">
                        <table class="legend-table">
                            <tr>
                                <td class="bg-gray" style="width: 100px; font-weight: bold;">Input to Tosar by</td>
                                <td>: {{ auth()->check() ? auth()->user()->name : '-' }}</td>
                            </tr>
                            <tr>
                                <td class="bg-gray" style="font-weight: bold;">Date</td>
                                <td>: {{ now()->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <td class="bg-gray" style="font-weight: bold; height: 30px;">Checked By</td>
                                <td>: </td>
                            </tr>
                        </table>
                    </td>

                    <td class="no-border" style="width: 5%;"></td>

                    {{-- Inisial Pemeriksa --}}
                    <td class="no-border" style="width: 30%; vertical-align: top;">
                        <table class="legend-table">
                            @php
                                $uniqueNames = collect($data)
                                    ->pluck('inspected_by')
                                    ->flatMap(fn($item) => explode('|', $item))
                                    ->map(fn($name) => trim($name))
                                    ->unique()
                                    ->filter();
                            @endphp
                            @foreach ($uniqueNames as $name)
                                <tr>
                                    <td class="bg-gray" style="width: 40px; text-align: center; font-weight: bold;">
                                        {{ collect(preg_split('/[\s,]+/', $name))->map(fn($w) => strtoupper(substr($w, 0, 1)))->implode('') }}
                                    </td>
                                    <td style="text-align: left;">{{ $name }}</td>
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
