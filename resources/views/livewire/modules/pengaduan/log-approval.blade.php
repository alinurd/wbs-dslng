@php
// Sample data untuk log approval detail
$logApprovalDetail = [
    [
        'id' => '7-DTRPOG',
        'judul_pengaduan' => 'Pelanggaran Etika - Penyalahgunaan Wewenang',
        'status_akhir' => 'Menunggu Approval CCO',
        'progress' => 70,
        'log_approval' => [
            [
                'step' => 1,
                'role' => 'Pelapor',
                'nama' => 'Ahmad Santoso',
                'status' => 'completed',
                'status_text' => 'Disubmit',
                'waktu' => '17/11/2024 10:30',
                'catatan' => 'Laporan awal telah disampaikan dengan lengkap',
                'file' => ['bukti_1.pdf', 'foto_1.jpg'],
                'warna' => 'green'
            ],
            [
                'step' => 2,
                'role' => 'WBS Eksternal',
                'nama' => 'dr. Sari Wijaya',
                'status' => 'completed',
                'status_text' => 'Approved',
                'waktu' => '18/11/2024 14:15',
                'catatan' => 'Dokumen sudah lengkap dan memenuhi syarat',
                'file' => ['review_wbs_eksternal.pdf'],
                'warna' => 'green'
            ],
            [
                'step' => 3,
                'role' => 'WBS Internal',
                'nama' => 'Budi Raharjo',
                'status' => 'completed',
                'status_text' => 'Approved',
                'waktu' => '19/11/2024 09:45',
                'catatan' => 'Telah dilakukan verifikasi internal',
                'file' => ['verifikasi_internal.pdf'],
                'warna' => 'green'
            ],
            [
                'step' => 4,
                'role' => 'WBS Forward',
                'nama' => 'Tim Investigasi',
                'status' => 'in_progress',
                'status_text' => 'Dalam Proses',
                'waktu' => '20/11/2024 11:20',
                'catatan' => 'Sedang dilakukan investigasi lebih lanjut',
                'file' => [],
                'warna' => 'yellow'
            ],
            [
                'step' => 5,
                'role' => 'CCO',
                'nama' => '-',
                'status' => 'pending',
                'status_text' => 'Menunggu',
                'waktu' => '-',
                'catatan' => 'Menunggu hasil investigasi dari WBS Forward',
                'file' => [],
                'warna' => 'gray'
            ]
        ]
    ],
    [
        'id' => '6-gbgQRT',
        'judul_pengaduan' => 'Pelanggaran SOP - Penggunaan Dana',
        'status_akhir' => 'Ditolak WBS Internal',
        'progress' => 40,
        'log_approval' => [
            [
                'step' => 1,
                'role' => 'Pelapor',
                'nama' => 'Dewi Lestari',
                'status' => 'completed',
                'status_text' => 'Disubmit',
                'waktu' => '15/11/2024 08:30',
                'catatan' => 'Laporan penggunaan dana tidak sesuai SOP',
                'file' => ['bukti_transfer.pdf', 'kwitansi.jpg'],
                'warna' => 'green'
            ],
            [
                'step' => 2,
                'role' => 'WBS Eksternal',
                'nama' => 'dr. Andi Pratama',
                'status' => 'completed',
                'status_text' => 'Approved',
                'waktu' => '16/11/2024 10:15',
                'catatan' => 'Dokumen pendukung cukup lengkap',
                'file' => ['review_eksternal.pdf'],
                'warna' => 'green'
            ],
            [
                'step' => 3,
                'role' => 'WBS Internal',
                'nama' => 'Citra Dewi',
                'status' => 'rejected',
                'status_text' => 'Ditolak',
                'waktu' => '17/11/2024 13:45',
                'catatan' => 'Bukti tidak cukup kuat untuk dilanjutkan',
                'file' => ['alasan_penolakan.pdf'],
                'warna' => 'red'
            ]
        ]
    ]
];
@endphp

<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Log Approval Pengaduan</h1>
            <p class="text-gray-600 mt-2">Tracking progress dan status approval pengaduan</p>
        </div>

        @foreach($logApprovalDetail as $pengaduan)
        <!-- Card Pengaduan -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8 overflow-hidden">
            <!-- Header Pengaduan -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">{{ $pengaduan['judul_pengaduan'] }}</h2>
                        <div class="flex items-center space-x-4 mt-2">
                            <span class="text-sm text-gray-600">ID: {{ $pengaduan['id'] }}</span>
                            <span class="px-3 py-1 bg-{{ $pengaduan['log_approval'][count($pengaduan['log_approval'])-1]['warna'] }}-100 text-{{ $pengaduan['log_approval'][count($pengaduan['log_approval'])-1]['warna'] }}-800 text-sm font-medium rounded-full">
                                {{ $pengaduan['status_akhir'] }}
                            </span>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-blue-600">{{ $pengaduan['progress'] }}%</div>
                        <div class="text-sm text-gray-500">Progress</div>
                    </div>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="px-6 py-3 bg-gray-50 border-b border-gray-200">
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-500" 
                         style="width: {{ $pengaduan['progress'] }}%"></div>
                </div>
            </div>

            <!-- Timeline Log Approval -->
            <div class="p-6">
                <div class="relative">
                    <!-- Timeline Line -->
                    <div class="absolute left-6 top-0 bottom-0 w-0.5 bg-gray-200"></div>
                    
                    @foreach($pengaduan['log_approval'] as $log)
                    <div class="relative flex items-start space-x-4 mb-8 last:mb-0">
                        <!-- Step Indicator -->
                        <div class="relative z-10">
                            @if($log['status'] === 'completed')
                            <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-check text-white text-sm"></i>
                            </div>
                            @elseif($log['status'] === 'in_progress')
                            <div class="w-12 h-12 bg-yellow-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-sync-alt text-white text-sm animate-spin"></i>
                            </div>
                            @elseif($log['status'] === 'rejected')
                            <div class="w-12 h-12 bg-red-500 rounded-full flex items-center justify-center">
                                <i class="fas fa-times text-white text-sm"></i>
                            </div>
                            @else
                            <div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center">
                                <i class="fas fa-clock text-gray-600 text-sm"></i>
                            </div>
                            @endif
                        </div>

                        <!-- Content -->
                        <div class="flex-1 bg-{{ $log['warna'] }}-50 rounded-lg p-4 border border-{{ $log['warna'] }}-200">
                            <div class="flex items-center justify-between mb-2">
                                <div>
                                    <h3 class="font-semibold text-gray-900">{{ $log['role'] }}</h3>
                                    <p class="text-sm text-gray-600">{{ $log['nama'] }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="px-3 py-1 bg-{{ $log['warna'] }}-100 text-{{ $log['warna'] }}-800 text-sm font-medium rounded-full">
                                        {{ $log['status_text'] }}
                                    </span>
                                    <p class="text-xs text-gray-500 mt-1">{{ $log['waktu'] }}</p>
                                </div>
                            </div>

                            <!-- Catatan -->
                            @if($log['catatan'])
                            <div class="mb-3">
                                <p class="text-sm text-gray-700">{{ $log['catatan'] }}</p>
                            </div>
                            @endif

                            <!-- File Attachments -->
                            @if(count($log['file']) > 0)
                            <div class="border-t border-{{ $log['warna'] }}-200 pt-3">
                                <p class="text-sm font-medium text-gray-700 mb-2">File Lampiran:</p>
                                <div class="space-y-2">
                                    @foreach($log['file'] as $file)
                                    <div class="flex items-center justify-between bg-white rounded-lg px-3 py-2 border border-gray-200">
                                        <div class="flex items-center space-x-2">
                                            <i class="fas fa-file text-gray-400"></i>
                                            <span class="text-sm text-gray-700">{{ $file }}</span>
                                        </div>
                                        <button class="text-green-600 hover:text-green-700 text-sm flex items-center space-x-1">
                                            <i class="fas fa-download text-xs"></i>
                                            <span>Download</span>
                                        </button>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200 mt-6">
                    <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors duration-200 flex items-center space-x-2">
                        <i class="fas fa-comment"></i>
                        <span>Tambah Komentar</span>
                    </button>
                    <button class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors duration-200 flex items-center space-x-2">
                        <i class="fas fa-upload"></i>
                        <span>Upload File</span>
                    </button>
                </div>
            </div>
        </div>
        @endforeach

        <!-- Empty State -->
        @if(count($logApprovalDetail) === 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
            <i class="fas fa-inbox text-4xl text-gray-300 mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada log approval</h3>
            <p class="text-gray-500 mb-4">Pengaduan yang Anda buat akan muncul di sini</p>
            <button class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors duration-200">
                Buat Pengaduan Pertama
            </button>
        </div>
        @endif
    </div>
</div>