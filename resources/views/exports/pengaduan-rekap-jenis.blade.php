<div class="header">
    <div class="subtitle">
        Dicetak pada: {{ date('d/m/Y H:i') }} 
        | Total Data: {{ is_array($data) ? count($data) : 0 }} records
        @if(!empty($periode))
            | Periode: {{ $periode }}
        @endif
    </div>
</div>

@if (!empty($filterData) && count($filterData) > 0)
<div class="filter-info" style="margin:8px 0; padding:6px; background:#f8f8f8; border:1px solid #ccc; border-radius:4px;">
    <strong>Filter yang diterapkan:</strong>
    @foreach ($filterData as $label => $value)
        <span style="display:inline-block; margin-right:6px; padding:2px 6px; background:#e5e7eb; border-radius:3px;">
            <strong>{{ $label }}:</strong> {{ $value }}
        </span>
    @endforeach
</div>
@endif

<table border="1" style="border-collapse:collapse; font-size:13px; width:100%;">
<thead>
    <tr>
        <th colspan="34" style="text-align:center; font-weight:bold; font-size:15px; background:#0da7d9; color:white; padding:6px;">
            BERDASARKAN JENIS PELANGGARAN
        </th>
    </tr>

    <tr style="background:#f3f4f6; font-weight:bold;">
        <th style="text-align:center; width:40px;">No</th>
        <th style="text-align:left; width:200px;">Jenis Pelanggaran</th>

        @for ($i = 1; $i <= 31; $i++)
            <th style="text-align:center; width:30px;">{{ $i }}</th>
        @endfor

        <th style="text-align:center; width:80px;">Jumlah</th>
    </tr>

    <tr>
        <th colspan="34" style="text-align:center; font-weight:bold; background:#006fbc; color:white; padding:4px;">
            {{ $periode ?? '' }}
        </th>
    </tr>
</thead>

<tbody>
    @foreach ($data as $row)
    <tr>
        <td align="center">{{ $row['No'] ?? $loop->iteration }}</td>
        <td>{{ $row['Jenis Pelanggaran'] ?? '-' }}</td>

        @for ($d = 1; $d <= 31; $d++)
            <td align="center">{{ $row[$d] ?? 0 }}</td>
        @endfor

        <td align="center" style="font-weight:bold; color:green;">{{ $row['Jumlah'] ?? 0 }}</td>
    </tr>
    @endforeach
</tbody>

<tfoot>
    @if(is_array($data))
    <tr style="background:#f3f4f6; font-weight:bold;">
        <td colspan="2" align="center">JUMLAH</td>
        @for ($d = 1; $d <= 31; $d++)
        <td align="center">
            {{ array_sum(array_column($data, $d)) }}
        </td>
        @endfor
        <td align="center" style="color:green;">
            {{ array_sum(array_column($data, 'Jumlah')) }}
        </td>
    </tr>
    @endif
</tfoot>
</table>
