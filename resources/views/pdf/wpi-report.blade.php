<html>
<head>
    <style>
        body { font-family: sans-serif; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid black; padding: 4px; text-align: left; }
        .header-table td { border: none; }
        .logo { width: 80px; }
        .bg-gray { background-color: #f2f2f2; }
        .center { text-align: center; }
    </style>
</head>
<body>
    <table>
        <tr>
            <td width="20%"><img src="logo-msm.png" class="logo"></td>
            <td width="60%" class="center">
                <h3>TOKA TINDUNG PROJECT</h3>
                <h2>Formulir Laporan WPI KPLH</h2>
            </td>
            <td width="20%" class="center">TT-MGT-FRS-024A</td>
        </tr>
    </table>

    <table>
        <tr>
            <td class="bg-gray">Tanggal / Date</td>
            <td>{{ date('d F Y', strtotime($report->report_date)) }}</td>
            <td class="bg-gray" rowspan="4">Nama Petugas Inspeksi</td>
            <td rowspan="4">
                @foreach($report->inspectors as $ins)
                    {{ $loop->iteration }}. {{ $ins['name'] }} ({{ $ins['id_number'] }})<br>
                @endforeach
            </td>
        </tr>
        <tr>
            <td class="bg-gray">Jam / Time</td>
            <td>{{ $report->report_time }}</td>
        </tr>
        <tr>
            <td class="bg-gray">Lokasi / Location</td>
            <td>{{ $report->locationRelation->name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="bg-gray">Dept / Contractor</td>
            <td>{{ $report->department }}</td>
        </tr>
    </table>

    <table>
        <thead class="bg-gray">
            <tr>
                <th>#</th>
                <th>OHS Risk</th>
                <th>Uraian Temuan & Foto</th>
                <th>Tindakan Pencegahan & Foto</th>
                <th>Follow Up (PIC & Due)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report->findings as $find)
            <tr>
                <td class="center">{{ $loop->iteration }}</td>
                <td class="center">{{ $find->ohs_risk }}</td>
                <td>
                    {{ $find->description }}
                    <br>
                    @if(!empty($find->photos))
                        @foreach($find->photos as $p)
                            <img src="{{ public_path('storage/'.$p) }}" width="100">
                        @endforeach
                    @endif
                </td>
                <td>
                    {{ $find->prevention_action }}
                    <br>
                    @if(!empty($find->prevention_photos))
                        @foreach($find->prevention_photos as $pp)
                            <img src="{{ public_path('storage/'.$pp) }}" width="100">
                        @endforeach
                    @endif
                </td>
                <td>
                    PIC: {{ $find->pic_responsible }}<br>
                    Due: {{ $find->due_date ? date('d/m/Y', strtotime($find->due_date)) : '-' }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
