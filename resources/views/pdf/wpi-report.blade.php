<!DOCTYPE html>
<html>
<head>
    <style>
        @page {
            margin: 1cm;
        }
        body {
            /* Mengatur font ke Times New Roman */
            font-family: "Times New Roman", Times, serif;
            font-size: 9pt;
            margin: 0;
            padding: 0;
            line-height: 1.2;
        }
        .header-table, .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            table-layout: fixed;
        }
        .header-table td {
            border: 1px solid #000;
            padding: 5px;
            vertical-align: middle;
        }
        .main-table th, .main-table td {
            border: 1px solid #000;
            padding: 4px;
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
            color: #b91c1c; /* Merah sesuai template Archi */
            text-align: center;
            margin-top: 10px;
        }
        img.photo {
            width: 140px;
            height: auto;
            margin: 5px;
            border: 0.5px solid #000;
        }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td width="15%" class="center"><img src="{{ public_path('images/logo-msm.png') }}" width="60"></td>
            <td width="70%" class="center">
                <strong style="font-size: 14pt;">TOKA TINDUNG PROJECT</strong><br>
                <strong style="font-size: 12pt;">Formulir Laporan WPI KPLH</strong><br>
                <span>TT-MGT-FRS-024A</span>
            </td>
            <td width="15%" class="center"><img src="{{ public_path('images/logo-archi.png') }}" width="60"></td>
        </tr>
    </table>

    <table class="main-table">
        <tr>
            <td width="15%" class="bg-gray">Tanggal /Date</td>
            <td width="35%">{{ date('d F Y', strtotime($report->report_date)) }}</td>
            <td width="20%" class="bg-gray center">Nama Petugas Inspeksi</td>
            <td width="10%" class="bg-gray center">ID</td>
            <td width="20%" class="bg-gray center">Dept/Cont</td>
        </tr>
        @foreach($report->inspectors as $key => $ins)
        <tr>
            <td class="bg-gray">Jam /Time</td>
            @if($loop->first) <td>{{ $report->report_time }}</td> @else <td style="border:none"></td> @endif
            <td>{{ $key+1 }}. {{ $ins['name'] }}</td>
            <td class="center">{{ $ins['id_number'] }}</td>
            <td class="center">{{ $ins['dept_con'] }}</td>
        </tr>
        @endforeach
    </table>

    <table class="main-table">
        <thead>
            <tr class="bg-gray center">
                <th width="3%">No</th>
                <th width="5%">OHS Risk</th>
                <th width="30%">Uraian Tindakan Tidak Aman / Kondisi Tidak Aman</th>
                <th width="30%">Jenis Tindakan Pencegahan</th>
                <th width="32%">Tindak Lanjut / Follow Up</th>
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

    <div class="footer-text">
        Dokumen terkendali dan valid hanya ada di sharepoint Archi Indonesia
    </div>
</body>
</html>
