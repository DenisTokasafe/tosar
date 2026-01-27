<style>
    body {
        font-family: 'Helvetica', sans-serif;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 8px;
        table-layout: fixed;
    }

    th {
        background-color: #ffff00;
        border: 1px solid black;
        padding: 4px;
        text-transform: uppercase;
    }

    td {
        border: 1px solid black;
        padding: 4px;
        text-align: center;
        word-wrap: break-word;
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

    .header-info {
        margin-bottom: 10px;
        font-size: 10px;
        font-weight: bold;
    }

    .footer-container {
        margin-top: 20px;
        width: 100%;
    }

    .no-border {
        border: none !important;
    }

    .bg-gray {
        background-color: #f3f4f6;
    }
</style>

<h2 style="text-align: center; margin-bottom: 5px;">{{ strtoupper($type) }} INSPECTION</h2>
<div class="header-info">
    <div>Month : {{ $month }}</div>
    <div>Area : {{ $area ?? ($data->first()->area ?? '-') }}</div>
</div>

{{-- Tabel Utama --}}
<table>
    <thead>
        <tr>
            <th style="width: 25px;">NO</th>
            <th>LOKASI</th>
            @foreach ($structure['inputs'] as $header)
                <th>{{ $header }}</th>
            @endforeach
            @foreach ($structure['checks'] as $header)
                <th>{{ $header }}</th>
            @endforeach
            <th>TANGGAL</th>
            <th>PEMERIKSA</th>
            <th>KETERANGAN</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td style="text-align: left;">{{ $item->location }}</td>

                @foreach ($structure['inputs'] as $input)
                    <td>{{ $item->conditions[$input] ?? '-' }}</td>
                @endforeach

                @foreach ($structure['checks'] as $check)
                    <td>
                        @php $val = $item->conditions[$check] ?? null; @endphp
                        @if ($val === true || $val === 'yes')
                            <span class="good">✔</span>
                        @elseif($val === false || $val === 'no')
                            <span class="nogood">✘</span>
                        @else
                            -
                        @endif
                    </td>
                @endforeach
                <td>{{ \Carbon\Carbon::parse($item->inspection_date)->format('d/m/Y') }}</td>
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
                <td style="text-align: left;">{{ $item->remarks ?? '-' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

{{-- Bagian Footer (Note & Signature) --}}
<div class="footer-container">
    <table class="no-border" style="border: none;">
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
                        <td class="bg-gray" style="text-align: left; font-weight: bold; width: 100px;">Input to INX by
                        </td>
                        <td style="text-align: left;">: {{ explode('|', $data->first()->inspected_by ?? '-')[0] }}</td>
                    </tr>
                    <tr>
                        <td class="bg-gray" style="text-align: left; font-weight: bold;">INX Reference</td>
                        <td style="text-align: left;">: ........</td>
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
