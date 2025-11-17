<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100"> 
    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        @php
        // Sample data untuk dashboard
        $stats = [
            'total_pengaduan' => 24,
            'dalam_proses' => 8,
            'selesai' => 14,
            'rating' => 4.8
        ];

        $pengaduan_terbaru = [
            [
                'id' => '7-DTRPOG',
                'no' => 4,
                'judul' => 'Laporan 1',
                'progress' => 70,
                'tanggal' => '17/11/2025 02:16',
                'status' => 'Dalam Proses',
                'status_color' => 'yellow'
            ],
            [
                'id' => '6-gbgQRT',
                'no' => 1,
                'judul' => 'vgygyygyygyygyy',
                'progress' => 70,
                'tanggal' => '16/11/2025 00:14',
                'status' => 'Dalam Proses',
                'status_color' => 'yellow'
            ],
            [
                'id' => '5-urlAzy',
                'no' => 5,
                'judul' => 'gsvcdghv',
                'progress' => 70,
                'tanggal' => '15/11/2025 23:16',
                'status' => 'Selesai',
                'status_color' => 'green'
            ],
            [
                'id' => '5-Jj322Y',
                'no' => 1,
                'judul' => 'rrere@gmail.con',
                'progress' => 70,
                'tanggal' => '15/11/2025 20:13',
                'status' => 'Ditolak',
                'status_color' => 'red'
            ]
        ];

        $log_approval = [
            [
                'id' => '7-DTRPOG',
                'judul' => 'Approval CCO #001',
                'waktu' => '2 jam lalu',
                'deskripsi' => 'Pengaduan telah disetujui untuk proses investigasi lebih lanjut',
                'komentar' => '3 komentar',
                'file' => true
            ],
            [
                'id' => '6-gbgQRT',
                'judul' => 'Approval CCO #002',
                'waktu' => '5 jam lalu',
                'deskripsi' => 'Dokumen pendukung telah diverifikasi dan approved',
                'komentar' => '1 komentar',
                'file' => true
            ],
            [
                'id' => '5-urlAzy',
                'judul' => 'Approval CCO #003',
                'waktu' => '1 hari lalu',
                'deskripsi' => 'Proses approval tahap pertama selesai',
                'komentar' => '0 komentar',
                'file' => false
            ]
        ];

        $progress_bulanan = [
            ['label' => 'Pengaduan Baru', 'jumlah' => 12, 'persentase' => 70, 'color' => 'blue'],
            ['label' => 'Dalam Investigasi', 'jumlah' => 8, 'persentase' => 50, 'color' => 'yellow'],
            ['label' => 'Selesai', 'jumlah' => 4, 'persentase' => 25, 'color' => 'green']
        ];
        @endphp

        <!-- Welcome Banner -->  
        <div class="bg-gradient-to-r from-[#0077C8] to-[#003B73] rounded-2xl p-8 text-white mb-8 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold mb-2">Selamat Datang, {{ $user->name }}!</h1>
                    <p class="text-blue-100 text-lg mb-4">
                        Sistem pelaporan yang aman, rahasia, dan terpercaya untuk menciptakan budaya kerja transparan dan berintegritas
                    </p>
                    @if(!$user->email_verified_at)
                    <div class="bg-red-500/20 border border-red-300 rounded-lg p-4 inline-block">
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-exclamation-triangle text-red-300"></i>
                            <span class="text-red-100 font-medium">Silahkan lakukan verifikasi email terlebih dahulu!</span>
                        </div>
                    </div>
                    @endif
                </div>
                <div class="hidden lg:block">
                    <i class="fas fa-shield-alt text-blue-300 text-8xl opacity-50"></i>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Pengaduan</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_pengaduan'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-file-alt text-blue-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm text-green-600">
                    <i class="fas fa-arrow-up mr-1"></i>
                    <span>+12% dari bulan lalu</span>
                </div>
            </div>

            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Dalam Proses</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['dalam_proses'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-sync-alt text-yellow-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm text-blue-600">
                    <i class="fas fa-clock mr-1"></i>
                    <span>Menunggu tindak lanjut</span>
                </div>
            </div>

            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Selesai</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['selesai'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm text-green-600">
                    <i class="fas fa-chart-line mr-1"></i>
                    <span>58% terselesaikan</span>
                </div>
            </div>

            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Rating Responsif</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['rating'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-star text-purple-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm text-yellow-600">
                    <i class="fas fa-star mr-1"></i>
                    <span>Dari 5.0 rating</span>
                </div>
            </div>
        </div>

        <!-- Main Dashboard Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Quick Actions -->
            <div class="lg:col-span-1">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-1 gap-6">
                    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                        <div class="flex items-center space-x-4 mb-4">
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-plus-circle text-green-600 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">Buat Pengaduan Baru</h3>
                                <p class="text-sm text-gray-500">Laporkan pelanggaran</p>
                            </div>
                        </div>
                        <a href="{{route('p_report')}}" class="w-full bg-green-600 hover:bg-green-700 text-white py-3 px-4 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center space-x-2">
                            <i class="fas fa-plus"></i>
                            <span>Mulai Pengaduan</span>
                        </a >
                    </div>

                    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                        <div class="flex items-center space-x-4 mb-4">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-search text-blue-600 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">Tracking Pengaduan</h3>
                                <p class="text-sm text-gray-500">Pantau status laporan</p>
                            </div>
                        </div>
                        <button class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center space-x-2">
                            <i class="fas fa-truck"></i>
                            <span>Lacak Status</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Recent Reports & Tracking -->
            <div class="lg:col-span-2">
                <!-- Recent Pengaduan -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-semibold text-gray-900">Pengaduan Terbaru</h2>
                            <a href="{{ route('p_tracking') }}"  class="text-blue-600 hover:text-blue-700 text-sm font-medium flex items-center space-x-1">
                                <span>Lihat Semua</span>
                                <i class="fas fa-chevron-right text-xs"></i>
                            </a>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            @foreach($pengaduan_terbaru as $pengaduan)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-blue-50 transition-colors duration-200">
                                <div class="flex items-center space-x-4">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-exclamation-triangle text-blue-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $pengaduan['judul'] }} - {{ $pengaduan['id'] }}</p>
                                        <p class="text-sm text-gray-500">Dilaporkan {{ $pengaduan['tanggal'] }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <span class="px-3 py-1 bg-{{ $pengaduan['status_color'] }}-100 text-{{ $pengaduan['status_color'] }}-800 text-xs font-medium rounded-full">
                                        {{ $pengaduan['status'] }}
                                    </span>
                                    <a href="{{ route('log_detail', $pengaduan['id']) }}" 
           class="w-8 h-8 flex items-center justify-center text-gray-400 hover:text-blue-600 transition-colors duration-200">
            <i class="fas fa-chevron-right"></i>
        </a>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar - Approval Log & Activity -->
            <div class="lg:col-span-3 space-y-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Approval Tracking -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h2 class="text-lg font-semibold text-gray-900">Log Approval</h2>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                @foreach($log_approval as $log)
                                <div class="border-l-4 border-blue-500 pl-4 py-2">
                                    <div class="flex justify-between items-start mb-1">
                                        <p class="font-medium text-gray-900">{{ $log['judul'] }}</p>
                                        <span class="text-xs text-gray-500">{{ $log['waktu'] }}</span>
                                    </div>
                                    <p class="text-sm text-gray-600 mb-2">{{ $log['deskripsi'] }}</p>
                                    <div class="flex items-center space-x-3">
                                        <button class="text-blue-600 hover:text-blue-700 text-sm flex items-center space-x-1">
                                            <i class="fas fa-comment text-xs"></i>
                                            <span>{{ $log['komentar'] }}</span>
                                        </button>
                                        @if($log['file'])
                                        <button class="text-green-600 hover:text-green-700 text-sm flex items-center space-x-1">
                                            <i class="fas fa-download text-xs"></i>
                                            <span>Download File</span>
                                        </button>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Progress Chart -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h2 class="text-lg font-semibold text-gray-900">Progress Bulan Ini</h2>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                @foreach($progress_bulanan as $progress)
                                <div>
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="text-gray-600">{{ $progress['label'] }}</span>
                                        <span class="font-medium text-gray-900">{{ $progress['jumlah'] }} laporan</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-{{ $progress['color'] }}-600 h-2 rounded-full" style="width: {{ $progress['persentase'] }}%"></div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Support Info -->
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
                    <div class="flex items-center space-x-3 mb-3">
                        <i class="fas fa-headset text-blue-600 text-xl"></i>
                        <h3 class="font-semibold text-blue-900">Butuh Bantuan?</h3>
                    </div>
                    <p class="text-blue-800 text-sm mb-4">
                        Tim support kami siap membantu 24/7 untuk pertanyaan terkait pengaduan.
                    </p>
                    <button class="w-full bg-white hover:bg-blue-100 text-blue-600 border border-blue-300 py-2 px-4 rounded-lg font-medium transition-colors duration-200">
                        Hubungi Support
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>