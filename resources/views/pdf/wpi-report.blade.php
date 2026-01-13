<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        @page {
            margin: 1cm;
        }
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 8.5pt;
            margin: 0;
            padding: 0;
            line-height: 1.1;
        }
        .header-table, .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .header-table td {
            border: 1px solid #000;
            padding: 5px;
            vertical-align: middle;
        }
        .main-table th, .main-table td {
            border: 1px solid #000;
            padding: 3px 5px;
            vertical-align: top;
            word-wrap: break-word;
        }
        .bg-gray {
            background-color: #e2e8f0;
            font-weight: bold;
        }
        .center { text-align: center; }
        .footer-text {
            font-size: 8pt;
            font-style: italic;
            color: #b91c1c;
            text-align: center;
            margin-top: 5px;
        }
        img.photo {
            width: 130px;
            height: auto;
            margin: 3px;
            border: 0.5px solid #000;
        }
    </style>
</head>
<body>
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

    <table class="main-table">
        <tr>
            <td width="15%" class="bg-gray">Tanggal /Date</td>
            <td width="30%">{{ date('d F Y', strtotime($report->report_date)) }}</td>
            <td width="25%" class="bg-gray center">Nama Petugas Inspeksi/Inspector</td>
            <td width="10%" class="bg-gray center">ID</td>
            <td width="20%" class="bg-gray center">Department/Contractor</td>
        </tr>

        @php
            // Minimal 6 baris untuk menjaga layout label tetap konsisten
            $maxRows = 6;
        @endphp

        @for ($i = 0; $i < $maxRows; $i++)
        <tr>
            {{-- Kolom Label Kiri --}}
            @if ($i == 0)
                <td class="bg-gray">Jam /Time</td>
                <td>{{ $report->report_time }}</td>
            @elseif ($i == 1)
                <td class="bg-gray">Lokasi/Location</td>
                <td>{{ $report->locationRelation->name ?? '-' }}</td>
            @elseif ($i == 2)
                <td class="bg-gray">Site Name</td>
                <td>Tokatindung</td>
            @elseif ($i == 3)
                <td class="bg-gray">Area</td>
                <td>Mining</td>
            @elseif ($i == 4)
                <td class="bg-gray">Company</td>
                <td>PT MSM</td>
            @elseif ($i == 5)
                <td class="bg-gray">Department</td>
                <td>{{ $report->department }}</td>
            @endif

            {{-- Kolom Data Petugas (Kanan) --}}
            <td>{{ isset($report->inspectors[$i]) ? ($i + 1) . '. ' . $report->inspectors[$i]['name'] : '' }}</td>
            <td class="center">{{ $report->inspectors[$i]['id_number'] ?? '' }}</td>
            <td class="center">{{ $report->inspectors[$i]['dept_con'] ?? '' }}</td>
        </tr>
        @endfor

        <tr>
            <td class="bg-gray">Contractor</td>
            <td>{{ $report->searchContractor ?? '-' }}</td>
            <td class="bg-gray center">Direview oleh/Reviewing by:</td>
            <td colspan="2" class="center">
                <div style="height: 35px;"></div> <strong>MARDIN</strong><br>
                ID: 3242289
            </td>
        </tr>
    </table>

    <table class="main-table">
        <thead>
            <tr class="bg-gray center">
                <th width="3%">No</th>
                <th width="5%">OHS Risk</th>
                <th width="32%">Uraian Tindakan Tidak Aman / Kondisi Tidak Aman</th>
                <th width="30%">Jenis Tindakan Pencegahan</th>
                <th width="30%">Tindak Lanjut / Follow Up</th>
            </tr>
        </thead>
        <tbody>
            @forelse($report->findings as $index => $find)
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
            @empty
            <tr>
                <td colspan="5" class="italic center">Tidak ada temuan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer-text">
        Dokumen terkendali dan valid hanya ada di sharepoint Archi Indonesia
    </div>
</body>
</html>
