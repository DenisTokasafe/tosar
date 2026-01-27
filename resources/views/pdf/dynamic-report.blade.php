<style>
    table { width: 100%; border-collapse: collapse; font-size: 9px; table-layout: fixed; }
    th { background-color: #ffff00; border: 1px solid black; padding: 4px; text-transform: uppercase; }
    td { border: 1px solid black; padding: 4px; text-align: center; word-wrap: break-word; }
    .good { font-family: DejaVu Sans, sans-serif; color: green; font-weight: bold; }
    .nogood { font-family: DejaVu Sans, sans-serif; color: red; font-weight: bold; }
</style>

<h2 style="text-align: center;">{{ strtoupper($type) }} INSPECTION REPORT</h2>
<p>Month: {{ $month }}</p>
<p>Area: {{ $area }}</p>

<table>
    <thead>
        <tr>
            <th style="width: 25px;">NO</th>
            <th>LOKASI</th>
            {{-- Render Header untuk Inputs (Teks) --}}
            @foreach($structure['inputs'] as $header)
                <th>{{ $header }}</th>
            @endforeach
            {{-- Render Header untuk Checks (Boolean) --}}
            @foreach($structure['checks'] as $header)
                <th>{{ $header }}</th>
            @endforeach
            <th>TANGGAL</th>
            <th>PEMERIKSA</th>
            <th>REMARKS</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $index => $item)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td style="text-align: left;">{{ $item->location }}</td>

            {{-- Render Isi Data Inputs --}}
            @foreach($structure['inputs'] as $input)
                <td>{{ $item->conditions[$input] ?? '-' }}</td>
            @endforeach

            {{-- Render Isi Data Checks (Centang) --}}
            @foreach($structure['checks'] as $check)
                <td>
                    @php $val = $item->conditions[$check] ?? null; @endphp
                    @if($val === true)
                        <span class="good">✔</span>
                    @elseif($val === false)
                        <span class="nogood">✘</span>
                    @else
                        -
                    @endif
                </td>
            @endforeach
            <td>{{ $item->remarks }}</td>
            <td>{{ \Carbon\Carbon::parse($item->inspection_date)->format('d/m/Y') }}</td>
            <td>{{ str_replace('|', ', ', $item->inspected_by) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
