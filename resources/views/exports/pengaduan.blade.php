<div class="header">
        <h1>{{__('global.export_pengadual_all')}}</h1>
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
        <strong>{{__('global.apply_filter')}}:</strong>
        @foreach($filterData as $label => $value)
            <span class="filter-item">
                <strong>{{ $label }}:</strong> {{ $value }}
            </span>
        @endforeach
    </div>
    @endif

    <!-- Table -->
<table border="1" style="border-collapse:collapse; font-size:13px; width:100%;">
        <thead>
            <tr class="uppercase">
                <th rowspan="2" width="30">NO</th>
                <th rowspan="2" width="100">{{__('table.columns.code')}}</th>
                <th rowspan="2" width="80">{{__('table.columns.estimasi_waktu')}}</th>
                <th rowspan="2" width="90">{{__('table.columns.tgl_aduan')}}</th>
                
                <!-- Identitas Pelapor -->
                <th colspan="3" class="bg-blue">{{__('table.columns.pelapor_identitas')}}</th>
                
                <!-- Identitas Terlapor -->
                <th colspan="2" class="bg-orange">{{__('table.columns.terlapor_identitas')}}</th>
                
                <th rowspan="2" width="70">STATUS</th>
                <th rowspan="2" width="100">{{__('table.columns.jenis_pelanggaran')}}</th>
                <th rowspan="2" width="150">URAIAN</th>
                {{-- <th rowspan="2" width="80">ADMIN</th> --}}
                <th rowspan="2" width="90">{{__('table.columns.created_at')}}</th>
            </tr>
            <tr class="uppercase">
                <!-- Sub-header Pelapor -->
                <th width="80" class="bg-blue">{{__('table.columns.name')}}</th>
                <th width="80" class="bg-blue">{{__('table.columns.no_hp')}}</th>
                <th width="120" class="bg-blue">{{__('table.columns.cont_detail')}}</th>
                
                <!-- Sub-header Terlapor -->
                <th width="80" class="bg-orange">{{__('table.columns.name')}}</th>
                <th width="100" class="bg-orange">DIREKTORAT</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $index => $item)
            <tr>
                <!-- No -->
                <td class="row-number">{{ $index + 1 }}</td>
                
                <!-- Kode Tracking -->
                <td style="font-family: 'Courier New', monospace;">{{ $item->code_pengaduan ?? '-' }}</td>
                
                <!-- Waktu Kejadian -->
                <td class="text-center">
                    @if(isset($item->waktu_kejadian))
                        {{ \Carbon\Carbon::parse($item->waktu_kejadian)->format('d/m/Y') }}
                    @else
                        -
                    @endif
                </td>
                
                <!-- Tanggal Aduan -->
                <td class="text-center">
                    @if(isset($item->tanggal_pengaduan))
                        {{ \Carbon\Carbon::parse($item->tanggal_pengaduan)->format('d/m/Y') }}
                    @else
                        -
                    @endif
                </td>
                
                <!-- Identitas Pelapor -->
                <td class="bg-blue">{{ $getNamaUser($item) }}</td>
                <td class="bg-blue">{{ $item->telepon_pelapor ?? ($item->pelapor->phone ?? ($item->user->phone ?? '-')) }}</td>
                <td class="bg-blue wrap-text">{{ $item->alamat_kejadian ?? '-' }}</td>
                
                <!-- Identitas Terlapor -->
                <td class="bg-orange">{{ $item->nama_terlapor ?? '-' }}</td>
                <td class="bg-orange">{{ $getDirektoratName($item->direktorat ?? $item->direktorat_terlapor) ?? '-' }}</td>
                
                <!-- Status -->
                <td class="text-center">
                    @php
                        $statusInfo = $getStatusInfo($item->status ?? 0, $item->sts_final ?? 0);
                        $statusClass = 'status-' . strtolower(str_replace(' ', '-', $statusInfo['text']));
                    @endphp
                    <span class="status-badge {{ $statusClass }}">
                        {{ $statusInfo['text'] }}
                    </span>
                </td>
                
                <!-- Jenis Pelanggaran -->
                <td>{{ $getJenisPelanggaran($item) }}</td>
                
                <!-- Perihal & Uraian -->
                <td class="wrap-text">
                     @if($item->uraian)
                    <strong>Uraian:</strong> {{ Str::limit($item->uraian, 100) }}
                    @endif
                </td>
                
                <!-- Admin -->
                {{-- <td class="text-center">{{ $item->admin->name ?? 'System' }}</td> --}}
                
                <!-- Tanggal Dibuat -->
                <td class="text-center">
                    @if(isset($item->created_at))
                        {{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y') }}
                    @else
                        -
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>