<div class="header">
        <h1>LAPORAN PENGADUAN</h1>
        <div class="subtitle">
            Dicetak pada: {{ date('d/m/Y H:i') }} | Total Data: {{ $data->count() }} records
            @if($periodInfo)
                | Periode: {{ $periodInfo }}
            @endif
        </div>
    </div>

    <!-- Filter Information -->
    @if(!empty($filterData) && count($filterData) > 0)
    <div class="filter-info">
        <strong>Filter yang diterapkan:</strong>
        @foreach($filterData as $label => $value)
            <span class="filter-item">
                <strong>{{ $label }}:</strong> {{ $value }}
            </span>
        @endforeach
    </div>
    @endif

    <!-- Table -->
<table border="1" style="border-collapse:collapse; ; width:100%;">
        <thead>
            <tr>
                <th  width="30">NO</th>
                <th  width="100">Kode Tracking</th>
                <th  width="80">Username</th>
                <th  width="90">Nama</th>
                <th  width="90">Jenis Pelanggaran</th>  
                <th  width="90">Perihal</th> 
                <th  width="90">Uraian</th> 
                <th  width="90">Kontak Detail</th> 
                <th  width="90">Perkiraan Waktu Kejadian</th> 
                <th  width="90">Nama Terlapor</th> 
                <th  width="90">Direktorat</th> 
                <th  width="90">Tanggal Aduan</th> 
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $item)
            <tr>
                <td class="row-number">{{ $index + 1 }}</td>
                <td style="font-family: 'Courier New', monospace;">{{ $item->code_pengaduan ?? '-' }}</td>
                <td class="bg-blue">{{ ($item->pelapor->username ?? '-') }}</td>
                <td class="bg-blue">{{ ($item->pelapor->name ?? '-') }}</td>
                <td>{{ $getJenisPelanggaran($item) }}</td>
                <td>{{ $item->perihal ?? '-' }}</td>
                <td>{{ $item->uraian ?? '-' }}</td>
                 <td>{{ $item->alamat_kejadian ?? '-' }}</td>
                <td class="text-center">
                    @if(isset($item->waktu_kejadian))
                        {{ \Carbon\Carbon::parse($item->waktu_kejadian)->format('d/m/Y') }}
                    @else
                        -
                    @endif
                </td>
                
                <td class="bg-orange">{{ $item->nama_terlapor ?? '-' }}</td>
                <td class="bg-orange">{{ $getDirektoratName($item->direktorat ?? $item->direktorat_terlapor) ?? '-' }}</td>
                <td class="text-center">
                    @if(isset($item->tanggal_pengaduan))
                        {{ \Carbon\Carbon::parse($item->tanggal_pengaduan)->format('d/m/Y') }}
                    @else
                        -
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>