<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        .header-banner {
            background-color: #ff0000;
            color: #ffffff;
            text-align: center;
            padding: 10px;
            font-weight: bold;
            font-size: 18px;
            text-transform: uppercase;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px;
            vertical-align: top;
            font-size: 12px;
        }

        .label {
            background-color: #f9f9f9;
            width: 25%;
            font-weight: bold;
        }

        .sub-label {
            color: #0000ff;
            font-style: italic;
            display: block;
            font-weight: normal;
        }

        .photo-box {
            height: 200px;
            text-align: center;
            vertical-align: middle;
        }

        .footer-label {
            background-color: #f0f0f0;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <div class="header-banner">
        Preliminary Significant Incident Alert
    </div>

    <table>
        <tr>
            <td class="label">Safety Alert No.</td>
            <td style="width: 25%">{{ $data['safety_no'] }}</td>
            <td class="label">INX No.</td>
            <td style="width: 25%">{{ $data['inx_no'] }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal / <span class="sub-label">Date</span></td>
            <td>{{ $data['date'] }}</td>
            <td class="label">Waktu / <span class="sub-label">Time</span></td>
            <td>{{ $data['time'] }}</td>
        </tr>
        <tr>
            <td class="label">Lokasi / <span class="sub-label">Location</span></td>
            <td>{{ $data['location'] }}</td>
            <td class="label">Perusahaan/Departemen / <span class="sub-label">Company/Department</span></td>
            <td>{{ $data['department'] }}</td>
        </tr>
    </table>

    <table>
        <tr>
            <td class="label" style="width: 25%;">Uraian singkat insiden / <span class="sub-label">Description of the Incident:</span></td>
            <td>{{ $data['description'] }}</td>
        </tr>
        <tr>
            <td class="label">Tindakan langsung untuk mencegah kejadian serupa terulang / <span class="sub-label">Immediate Actions to Prevent Recurrence on Site</span></td>
            <td>{{ $data['immediate_actions'] }}</td>
        </tr>
    </table>

    <table>
        <tr>
            <td class="photo-box" style="width: 50%;">
                @if($data['photo1'])
                <img src="{{ $message->embed($data['photo1']) }}" style="max-width: 100%; max-height: 180px;">
                @else
                [Photo 1]
                @endif
            </td>
            <td class="photo-box" style="width: 50%;">
                @if($data['photo2'])
                <img src="{{ $message->embed($data['photo2']) }}" style="max-width: 100%; max-height: 180px;">
                @else
                [Photo 2]
                @endif
            </td>
        </tr>
        <tr style="text-align: center; font-weight: bold; font-style: italic;">
            @foreach($data['photos'] as $index => $photo)
            <td class="photo-box" style="width: 50%;">
                @if($photo['exists'])
                <img src="{{ $message->embed($photo['full_path']) }}" style="max-width: 100%; max-height: 180px;">
                @else
                <div style="color: #ccc;">[ File Not Found ]</div>
                @endif
                <div style="font-weight: bold; margin-top: 5px;">Photo {{ $index + 1 }}</div>
            </td>
            @endforeach

            {{-- Jika hanya ada 1 foto, tambahkan kolom kosong agar layout tidak berantakan --}}
            @if($data['photos']->count() == 1)
            <td class="photo-box" style="width: 50%; border: 1px solid #000;">
                <div style="color: #ccc;">[ No Photo 2 ]</div>
            </td>
            @endif

            {{-- Jika tidak ada foto sama sekali --}}
            @if($data['photos']->count() == 0)
            <td class="photo-box" style="width: 50%;">[ No Photo 1 ]</td>
            <td class="photo-box" style="width: 50%;">[ No Photo 2 ]</td>
            @endif
        </tr>
    </table>

    <table>
        <tr>
            <td colspan="4" class="sub-label" style="border-bottom: none; font-weight: bold;">Persetujuan / <span style="color: blue;">Approval</span></td>
        </tr>
        <tr>
            <td rowspan="2" style="width: 20%;">Disetujui oleh / <span class="sub-label">Approved By</span></td>
            <td class="footer-label">Nama / <span class="sub-label">Name</span></td>
            <td class="footer-label">Posisi / <span class="sub-label">Position</span></td>
            <td class="footer-label">Tanggal / <span class="sub-label">Date</span></td>
        </tr>
        <tr>
            <td>{{ $data['approver_name'] }}</td>
            <td>KTT PT MSM</td>
            <td>{{ $data['approval_date'] }}</td>
        </tr>
        <tr>
            <td>Orang yang dapat dihubungi / <span class="sub-label">Contact Person</span></td>
            <td colspan="2">{{ $data['contact_person'] }}</td>
            <td>{{ $data['contact_date'] }}</td>
        </tr>
    </table>

</body>

</html>