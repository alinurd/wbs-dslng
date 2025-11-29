@php
    $roleIds = array_keys($userRole);
    $isRole3 = in_array(3, $roleIds);
    $reportRoute = $isRole3 ? 'p_tracking' : 'complien';
    $isVerif = $user->email_verified_at;
@endphp

<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100">
    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Welcome Banner dengan Filter -->
        <div class="bg-gradient-to-r from-[#0077C8] to-[#003B73] rounded-2xl p-8 text-white mb-6 shadow-lg relative overflow-hidden">
            <div class="relative z-10">
                <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
                    <div class="flex-1">
                        <h1 class="text-3xl font-bold mb-2">Selamat Datang, {{ $user->name }}! 👋</h1>
                        <p class="text-blue-100 text-lg mb-4 max-w-2xl">
                            Sistem pelaporan yang aman, rahasia, dan terpercaya untuk menciptakan budaya kerja transparan dan berintegritas
                        </p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <select wire:model.live="tahunFilter" 
                                class="border border-blue-300 bg-white/10 backdrop-blur-sm text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-white">
                            <option value="" class="text-gray-800">Pilih Tahun</option>
                            @foreach($this->getTahunOptions() as $tahun)
                                <option value="{{ $tahun }}" class="text-gray-800">Tahun {{ $tahun }}</option>
                            @endforeach
                        </select>
                        <button wire:click="refreshDashboard" 
                                class="bg-white/20 hover:bg-white/30 text-white px-4 py-2 rounded-lg transition-colors flex items-center justify-center border border-white/30">
                            <i class="fas fa-refresh mr-2"></i>Refresh
                        </button>
                    </div>
                </div>
            </div>
            <!-- Background Icon -->
            <div class="absolute right-8 top-1/2 transform -translate-y-1/2 opacity-20">
                <i class="fas fa-shield-alt text-white text-9xl"></i>
            </div>
        </div>

        <!-- Email Verification Alert -->
        @if (!$isVerif)
            @include('livewire.components.email-verification', [ 
                'isVerif' => $isVerif,
                'canResend' => $canResend, 
            ])
        @endif

        @if ($isVerif)
        <!-- Quick Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            @php
                $statCards = [
                    [
                        'title' => 'Total Pengaduan',
                        'value' => $stats['total_pengaduan'] ?? 0,
                        'icon' => 'file-alt',
                        'color' => 'blue',
                        'description' => 'Tahun ' . ($tahunFilter ?? date('Y'))
                    ],
                    [
                        'title' => 'Menunggu',
                        'value' => $stats['menunggu'] ?? 0,
                        'icon' => 'clock',
                        'color' => 'gray',
                        'description' => 'Belum diproses'
                    ],
                    [
                        'title' => 'Dalam Proses',
                        'value' => $stats['dalam_proses'] ?? 0,
                        'icon' => 'spinner',
                        'color' => 'yellow',
                        'description' => 'Sedang diproses'
                    ],
                    [
                        'title' => 'Selesai',
                        'value' => $stats['selesai'] ?? 0,
                        'icon' => 'check-circle',
                        'color' => 'green',
                        'description' => 'Telah diselesaikan'
                    ]
                ];
            @endphp

            @foreach($statCards as $card)
                <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-{{ $card['color'] }}-500 hover:shadow-xl transition-shadow duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">{{ $card['title'] }}</p>
                            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $card['value'] }}</p>
                            <p class="text-xs text-gray-500 mt-2">{{ $card['description'] }}</p>
                        </div>
                        <div class="p-3 bg-{{ $card['color'] }}-100 rounded-full">
                            <i class="fas fa-{{ $card['icon'] }} text-{{ $card['color'] }}-600 text-xl"></i>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Charts Section -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Analytics & Charts 📊</h2>
                    <p class="text-gray-600">Visualisasi data pengaduan untuk analisis yang lebih baik</p>
                </div>
                <div class="flex items-center space-x-2 text-sm text-gray-500">
                    <i class="fas fa-info-circle"></i>
                    <span>Data tahun {{ $tahunFilter ?? date('Y') }}</span>
                </div>
            </div>
            
            <!-- Charts Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Status Aduan -->
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-chart-pie mr-2 text-blue-600"></i>
                        Status Aduan
                    </h3>
                    <div class="h-72">
                        <canvas wire:ignore id="statusAduanChart" 
                                data-chart-data='@json($chartData['status_aduan'] ?? [])'></canvas>
                    </div>
                </div>

                <!-- Jenis Pelanggaran -->
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-chart-bar mr-2 text-green-600"></i>
                        Jenis Pelanggaran
                    </h3>
                    <div class="h-72">
                        <canvas wire:ignore id="jenisPelanggaranChart" 
                                data-chart-data='@json($chartData['jenis_pelanggaran'] ?? [])'></canvas>
                    </div>
                </div>

                <!-- Pergerakan Tahunan -->
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200 lg:col-span-2">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-chart-line mr-2 text-purple-600"></i>
                        Trend Bulanan Tahun {{ $tahunFilter ?? date('Y') }}
                    </h3>
                    <div class="h-72">
                        <canvas wire:ignore id="pergerakanTahunanChart" 
                                data-chart-data='@json($chartData['pergerakan_tahunan'] ?? [])'></canvas>
                    </div>
                </div>

                <!-- Saluran Aduan -->
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-inbox mr-2 text-orange-600"></i>
                        Saluran Aduan
                    </h3>
                    <div class="h-72">
                        <canvas wire:ignore id="saluranAduanChart" 
                                data-chart-data='@json($chartData['saluran_aduan'] ?? [])'></canvas>
                    </div>
                </div>

                <!-- Direktorat -->
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-building mr-2 text-red-600"></i>
                        Per Direktorat
                    </h3>
                    <div class="h-72">
                        <canvas wire:ignore id="direktoratChart" 
                                data-chart-data='@json($chartData['direktorat'] ?? [])'></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column - Quick Actions & Progress -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Quick Actions -->
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-bolt mr-2 text-yellow-500"></i>
                        Quick Actions
                    </h2>
                    <div class="space-y-4">
                        @if ($isRole3)
                        <a href="{{ route('p_report') }}" 
                           class="flex items-center p-4 bg-gradient-to-r from-green-50 to-green-100 border border-green-200 rounded-xl hover:shadow-md transition-all duration-300 group">
                            <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center mr-4 group-hover:scale-110 transition-transform">
                                <i class="fas fa-plus-circle text-white text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900">Buat Pengaduan Baru</h3>
                                <p class="text-sm text-gray-600">Laporkan pelanggaran dengan mudah</p>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400 group-hover:text-green-600 transition-colors"></i>
                        </a>
                        @endif

                        <a href="{{ route($reportRoute) }}" 
                           class="flex items-center p-4 bg-gradient-to-r from-blue-50 to-blue-100 border border-blue-200 rounded-xl hover:shadow-md transition-all duration-300 group">
                            <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center mr-4 group-hover:scale-110 transition-transform">
                                <i class="fas fa-search text-white text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900">Tracking Pengaduan</h3>
                                <p class="text-sm text-gray-600">Pantau status laporan Anda</p>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400 group-hover:text-blue-600 transition-colors"></i>
                        </a>

                        <a href="{{ route('faq') }}" 
                           class="flex items-center p-4 bg-gradient-to-r from-cyan-50 to-cyan-100 border border-cyan-200 rounded-xl hover:shadow-md transition-all duration-300 group">
                            <div class="w-12 h-12 bg-cyan-500 rounded-lg flex items-center justify-center mr-4 group-hover:scale-110 transition-transform">
                                <i class="fas fa-question text-white text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900">FAQ & Bantuan</h3>
                                <p class="text-sm text-gray-600">Jawaban untuk pertanyaan umum</p>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400 group-hover:text-cyan-600 transition-colors"></i>
                        </a>
                    </div>
                </div>

                <!-- Progress Bulanan -->
                @if(!empty($progress_bulanan))
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-tasks mr-2 text-purple-500"></i>
                        Progress Bulan Ini
                    </h2>
                    <div class="space-y-4">
                        @foreach($progress_bulanan as $progress)
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-3 h-3 rounded-full bg-{{ $progress['color'] }}-500"></div>
                                    <span class="text-sm font-medium text-gray-700">{{ $progress['label'] }}</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm text-gray-600">{{ $progress['jumlah'] }} aduan</span>
                                    <span class="text-sm font-bold text-{{ $progress['color'] }}-600">{{ $progress['persentase'] }}%</span>
                                </div>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-{{ $progress['color'] }}-500 h-2 rounded-full transition-all duration-1000 ease-out" 
                                     style="width: {{ $progress['persentase'] }}%"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Middle Column - Recent Pengaduan -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Aktivitas Terbaru -->
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-gray-800 flex items-center">
                            <i class="fas fa-stream mr-2 text-green-500"></i>
                            Aktivitas Terbaru
                        </h2>
                        <span class="text-gray-500 text-sm bg-gray-100 px-3 py-1 rounded-full">
                            {{ count($log_approval) }} aktivitas
                        </span>
                    </div>
                    
                    <div class="space-y-4">
                        @forelse($log_approval as $log)
                        <div class="border-l-4 border-{{ $log['status_color'] }}-500 pl-4 py-3 hover:bg-gray-50 rounded-r-lg transition-colors">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="font-semibold text-gray-800 text-sm">
                                    Code: {{ $log['code'] ?? $log['judul'] ?? 'Aktivitas Sistem' }}
                                </h3>
                                <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">{{ $log['waktu'] }}</span>
                            </div>
                            <p class="text-sm text-gray-600 mb-3 leading-relaxed">
                                {{ $log['catatan'] ?? $log['deskripsi'] ?? 'Tidak ada catatan' }}
                            </p>
                            <div class="flex items-center justify-between text-xs text-gray-500">
                                <span class="flex items-center">
                                    <i class="fas fa-user mr-2 text-gray-400"></i>
                                    {{ $log['user_name'] }}
                                </span>
                                <div class="flex items-center space-x-3">
                                    @if(isset($log['countComment']))
                                    <span class="text-blue-600 flex items-center">
                                        <i class="fas fa-comment mr-1"></i>
                                        {{ $log['countComment'] }}
                                    </span>
                                    @endif
                                    @if(isset($log['countFile']) && $log['countFile'])
                                    <span class="text-green-600 flex items-center">
                                        <i class="fas fa-file mr-1"></i>
                                        {{ $log['countFile'] }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-history text-4xl mb-2 opacity-50"></i>
                            <p>Belum ada aktivitas terbaru</p>
                        </div>
                        @endforelse
                    </div>
                </div>
                <!-- Recent Pengaduan -->
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-gray-800 flex items-center">
                            <i class="fas fa-history mr-2 text-blue-500"></i>
                            Pengaduan Terbaru
                        </h2>
                        <a href="{{ route($reportRoute) }}" 
                           class="text-blue-600 hover:text-blue-800 text-sm font-medium flex items-center">
                            Lihat Semua
                            <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                    
                    <div class="space-y-4">
                        @forelse($pengaduan_terbaru as $pengaduan)
                        <div class="border border-gray-200 rounded-xl p-4 hover:shadow-md transition-all duration-300 group">
                            <div class="flex justify-between items-start mb-3">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-800 text-base group-hover:text-blue-600 transition-colors">
                                        {{ $pengaduan['judul'] }}
                                    </h3>
                                    <p class="text-xs text-gray-500 mt-1">Code: #{{ $pengaduan['code_pengaduan'] }}</p>
                                </div>
                                <span class="px-3 py-1 text-xs font-medium rounded-full bg-{{ $pengaduan['status_color'] }}-100 text-{{ $pengaduan['status_color'] }}-800 border border-{{ $pengaduan['status_color'] }}-200">
                                    {{ $pengaduan['status'] }}
                                </span>
                            </div>
                            
                            <div class="flex items-center justify-between text-sm text-gray-600">
                                <div class="flex items-center space-x-4">
                                    <span class="flex items-center">
                                        <i class="fas fa-user mr-2 text-gray-400"></i>
                                        {{ $pengaduan['pelapor'] }}
                                    </span>
                                    <span class="flex items-center">
                                        <i class="fas fa-calendar mr-2 text-gray-400"></i>
                                        {{ $pengaduan['tanggal'] }}
                                    </span>
                                </div>
                                <div class="flex items-center space-x-4">
                                    <a href="{{ route('log_detail', ['code_pengaduan' => $pengaduan['code_pengaduan']]) }}"
                                       class="text-blue-600 hover:text-blue-800 text-sm font-medium flex items-center group">
                                        Log Aktivitas
                                        <i class="fas fa-chevron-right ml-1 group-hover:translate-x-1 transition-transform"></i>
                                    </a>
                                    <div class="flex items-center space-x-3">
                                        <span class="text-blue-600 flex items-center">
                                            <i class="fas fa-comment mr-1"></i>
                                            {{ $pengaduan['countComment'] }}
                                        </span>
                                        <span class="text-green-600 flex items-center">
                                            <i class="fas fa-file mr-1"></i>
                                            {{ $pengaduan['countFile'] }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-12 text-gray-500">
                            <i class="fas fa-inbox text-5xl mb-4 opacity-50"></i>
                            <p class="text-lg">Tidak ada pengaduan terbaru</p>
                            <p class="text-sm mt-2">Pengaduan yang dibuat akan muncul di sini</p>
                        </div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>

        <!-- Support Info -->
        <div class="mt-8">
            @include('livewire.components.suport')
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('livewire:init', function() {
        let charts = {};

        function initializeCharts() {
            destroyExistingCharts();
            
            // Initialize all charts
            const chartIds = [
                'statusAduanChart', 'jenisPelanggaranChart', 
                'pergerakanTahunanChart', 'saluranAduanChart', 'direktoratChart'
            ];

            chartIds.forEach(chartId => {
                const canvas = document.getElementById(chartId);
                if (canvas) {
                    const chartData = JSON.parse(canvas.getAttribute('data-chart-data'));
                    if (chartData.data) {
                        charts[chartId] = new Chart(canvas, chartData);
                    }
                }
            });
        }

        function destroyExistingCharts() {
            Object.values(charts).forEach(chart => {
                if (chart) chart.destroy();
            });
            charts = {};
        }

        // Initialize on load
        initializeCharts();

        // Refresh on Livewire updates
        Livewire.hook('commit', ({ component, succeed }) => {
            succeed(() => {
                if (component.name === 'modules.dashboard-index') {
                    setTimeout(initializeCharts, 100);
                }
            });
        });

        // Handle resize
        let resizeTimer;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(initializeCharts, 250);
        });
    });
</script>
@endpush

@push('styles')
<style>
    .chart-container {
        position: relative;
        height: 280px;
        width: 100%;
    }
    
    @media (max-width: 768px) {
        .chart-container {
            height: 240px;
        }
    }
    
    /* Smooth hover effects */
    .hover-lift:hover {
        transform: translateY(-2px);
        transition: all 0.3s ease;
    }
    
    /* Custom scrollbar for activity feed */
    .activity-feed {
        max-height: 400px;
        overflow-y: auto;
    }
    
    .activity-feed::-webkit-scrollbar {
        width: 6px;
    }
    
    .activity-feed::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    .activity-feed::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 10px;
    }
    
    .activity-feed::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }
</style>
@endpush