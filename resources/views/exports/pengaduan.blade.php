<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Pengaduan</title>
    <style>
        /* Reset dan base styles */
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        /* Table styles */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #006FBC;
            color: white;
            font-weight: bold;
            text-align: center;
        }

        /* Header styles */
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #006FBC;
            padding-bottom: 10px;
        }

        .header h1 {
            color: #006FBC;
            margin: 0;
            font-size: 18px;
        }

        .header .subtitle {
            color: #666;
            font-size: 12px;
            margin-top: 5px;
        }

        /* Filter info */
        .filter-info {
            background-color: #f8f9fa;
            padding: 10px;
            margin-bottom: 15px;
            border-left: 4px solid #006FBC;
            font-size: 10px;
        }

        .filter-item {
            display: inline-block;
            margin-right: 15px;
        }

        /* Column styles */
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .bg-gray { background-color: #f8f9fa; }
        .bg-blue { background-color: #e3f2fd; }
        .bg-orange { background-color: #fff3e0; }

        /* Status badges */
        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: bold;
            text-align: center;
        }

        .status-open { background-color: #e0e0e0; color: #424242; }
        .status-process { background-color: #fff3cd; color: #856404; }
        .status-completed { background-color: #d1edff; color: #004085; }
        .status-closed { background-color: #d4edda; color: #155724; }
        .status-rejected { background-color: #f8d7da; color: #721c24; }

        /* Numbering */
        .row-number {
            font-weight: bold;
            text-align: center;
        }

        /* Wrap text */
        .wrap-text {
            word-wrap: break-word;
            word-break: break-word;
        }

        /* Footer */
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 9px;
            color: #666;
            text-align: right;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>LAPORAN PENGADUAN LENGKAP</h1>
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
    <table>
        <thead>
            <tr>
                <th rowspan="2" width="30">NO</th>
                <th rowspan="2" width="100">KODE TRACKING</th>
                <th rowspan="2" width="80">WAKTU KEJADIAN</th>
                <th rowspan="2" width="90">TANGGAL ADUAN</th>
                
                <!-- Identitas Pelapor -->
                <th colspan="3" class="bg-blue">IDENTITAS PELAPOR</th>
                
                <!-- Identitas Terlapor -->
                <th colspan="2" class="bg-orange">IDENTITAS TERLAPOR</th>
                
                <th rowspan="2" width="70">STATUS</th>
                <th rowspan="2" width="100">JENIS PELANGGARAN</th>
                <th rowspan="2" width="150">PERIHAL & URAIAN</th>
                <th rowspan="2" width="80">ADMIN</th>
                <th rowspan="2" width="90">TANGGAL DIBUAT</th>
            </tr>
            <tr>
                <!-- Sub-header Pelapor -->
                <th width="80" class="bg-blue">NAMA</th>
                <th width="80" class="bg-blue">NOMOR PONSEL</th>
                <th width="120" class="bg-blue">KONTAK DETAIL</th>
                
                <!-- Sub-header Terlapor -->
                <th width="80" class="bg-orange">NAMA</th>
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
                    @if(isset($item->waktu_kejadian_mulai))
                        {{ \Carbon\Carbon::parse($item->waktu_kejadian_mulai)->format('d/m/Y') }}
                    @else
                        -
                    @endif
                </td>
                
                <!-- Tanggal Aduan -->
                <td class="text-center">
                    @if(isset($item->tanggal_pengaduan))
                        {{ \Carbon\Carbon::parse($item->tanggal_pengaduan)->format('d/m/Y H:i') }}
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
                    <strong>Perihal:</strong> {{ $item->perihal ?? '-' }}<br>
                    @if($item->uraian)
                    <strong>Uraian:</strong> {{ Str::limit($item->uraian, 100) }}
                    @endif
                </td>
                
                <!-- Admin -->
                <td class="text-center">{{ $item->admin->name ?? 'System' }}</td>
                
                <!-- Tanggal Dibuat -->
                <td class="text-center">
                    @if(isset($item->created_at))
                        {{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i') }}
                    @else
                        -
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Footer -->
    <div class="footer">
        Dokumen ini dibuat secara otomatis oleh Sistem Pengaduan | Halaman 1
    </div>
</body>
</html>