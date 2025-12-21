@php
    $roleIds = array_keys($userRole);
    $isRole3 = in_array(3, $roleIds);
    $isRole1or3 = in_array(1, $roleIds) || in_array(3, $roleIds);
    $reportRoute = $isRole1or3 ? 'p_tracking' : 'complien';
    $isVerif = $user->email_verified_at;
@endphp

<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100">
    @include('livewire.components.comment', [
            'show' => $showComment,
            'title' => $detailTitle,
            'data' => $detailData,
            'onClose' => 'closeDetailModal',
        ])
    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Welcome Banner dengan Filter -->
        <div
            class="bg-gradient-to-r from-[#0077C8] to-[#003B73] rounded-2xl p-8 text-white mb-6 shadow-lg relative overflow-hidden">
            <div class="relative z-100">
                <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
                    <div class="flex-1">
                        <h1 class="text-3xl font-bold mb-2">Selamat Datang, {{ $user->name }}! 👋</h1>
                        <p class="text-blue-100 text-lg mb-4 max-w-2xl">
                            Sistem pelaporan yang aman, rahasia, dan terpercaya untuk menciptakan budaya kerja
                            transparan dan berintegritas
                        </p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3">
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
             @if (!$isVerif)
            @include('livewire.components.email-verification', [ 'isVerif' => $isVerif,'canResend' => $canResend, ])
             @endif
        </div>
        
        @if ($isVerif)
            <!-- Filter -->
            @include('livewire.components.filter-dashboard')
            <!-- Email Verification Alert -->

            <!-- Quick Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
                @php
                    $statCards = [
                        [
                            'title' => 'Total Pengaduan',
                            'value' => $stats['total_pengaduan'] ?? 0,
                            'icon' => 'file-alt',
                            'color' => 'blue',
                            'description' => 'pengaduan yang diajukan',
                        ],
                        [
                            'title' => 'Menunggu',
                            'value' => $stats['menunggu'] ?? 0,
                            'icon' => 'clock',
                            'color' => 'gray',
                            'description' => 'Belum diproses',
                        ],
                        [
                            'title' => 'Dalam Proses',
                            'value' => $stats['dalam_proses'] ?? 0,
                            'icon' => 'spinner',
                            'color' => 'yellow',
                            'description' => 'Sedang diproses',
                        ],
                        [
                            'title' => 'Selesai',
                            'value' => $stats['selesai'] ?? 0,
                            'icon' => 'check-circle',
                            'color' => 'green',
                            'description' => 'Telah diselesaikan',
                        ],
                    ];
                @endphp

                @foreach ($statCards as $card)
                    <div
                        class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-{{ $card['color'] }}-500 hover:shadow-xl transition-shadow duration-300">
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

            @if (!empty($progress_bulanan) && $isRole3)
                <div class="mb-5">

                    @include('livewire.components.chart-progress')
                </div>
            @else
                @include('livewire.components.chart-admin')
            @endif

            <!-- Charts Section -->

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
                                    <div
                                        class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center mr-4 group-hover:scale-110 transition-transform">
                                        <i class="fas fa-plus-circle text-white text-xl"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-gray-900">Buat Pengaduan Baru</h3>
                                        <p class="text-sm text-gray-600">Laporkan pelanggaran dengan mudah</p>
                                    </div>
                                    <i
                                        class="fas fa-chevron-right text-gray-400 group-hover:text-green-600 transition-colors"></i>
                                </a>
                            @endif

                            <a href="{{ route($reportRoute) }}"
                                class="flex items-center p-4 bg-gradient-to-r from-blue-50 to-blue-100 border border-blue-200 rounded-xl hover:shadow-md transition-all duration-300 group">
                                <div
                                    class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center mr-4 group-hover:scale-110 transition-transform">
                                    <i class="fas fa-search text-white text-xl"></i>
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-900">Tracking Pengaduan</h3>
                                    <p class="text-sm text-gray-600">Pantau status laporan Anda</p>
                                </div>
                                <i
                                    class="fas fa-chevron-right text-gray-400 group-hover:text-blue-600 transition-colors"></i>
                            </a>

                            {{-- <a href="{{ route('faq') }}"
                                class="flex items-center p-4 bg-gradient-to-r from-cyan-50 to-cyan-100 border border-cyan-200 rounded-xl hover:shadow-md transition-all duration-300 group">
                                <div
                                    class="w-12 h-12 bg-cyan-500 rounded-lg flex items-center justify-center mr-4 group-hover:scale-110 transition-transform">
                                    <i class="fas fa-question text-white text-xl"></i>
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-900">FAQ & Bantuan</h3>
                                    <p class="text-sm text-gray-600">Jawaban untuk pertanyaan umum</p>
                                </div>
                                <i
                                    class="fas fa-chevron-right text-gray-400 group-hover:text-cyan-600 transition-colors"></i>
                            </a> --}}
                        </div>
                    </div>
                    <!-- Progress Bulanan -->
                    @if (!empty($progress_bulanan) && !$isRole3)
                        @include('livewire.components.chart-progress')
                    @endif
                </div>

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
                                @php
                                    $cleanCode = str_replace('#', '', $log['code']);
                                @endphp
                                <div
                                    class="border-l-4 border-{{ $log['status_color'] }}-500 pl-4 py-3 hover:bg-gray-50 rounded-r-lg transition-colors">
                                    <div class="flex justify-between items-start mb-2">
                                        <h3 class="font-semibold text-gray-800 text-sm">
                                            Code: #{{ $cleanCode }}
                                        </h3>
                                        <span
                                            class="px-3 py-1 text-xs font-medium rounded-full bg-{{ $log['status_color'] }}-100 text-{{ $log['status_color'] }}-800 border border-{{ $log['status_color'] }}-200">
                                            {{ $log['status'] }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600 mb-3 leading-relaxed">
                                        {{ $log['catatan'] ?? ($log['deskripsi'] ?? 'Tidak ada catatan') }}
                                    </p>
                                    <div class="flex items-center justify-between text-sm text-gray-600">
                                        <div class="flex items-center space-x-4">
                                            <span class="flex items-center">
                                                <i class="fas fa-user mr-2 text-gray-400"></i>
                                                {{ $log['user_name'] }}
                                            </span>
                                            <span class="flex items-center">
                                                <i class="fas fa-calendar mr-2 text-gray-400"></i>
                                                {{ $log['waktu'] }}
                                            </span>
                                        </div>
                                        <div class="flex items-center space-x-4">
                                            <a href="{{ route('log_detail', ['code_pengaduan' => $cleanCode]) }}"
                                                class="text-blue-600 hover:text-blue-800 text-sm font-medium flex items-center group">
                                                Log Aktivitas
                                                <i
                                                    class="fas fa-chevron-right ml-1 group-hover:translate-x-1 transition-transform"></i>
                                            </a>
                                            <div class="flex items-center space-x-3">
                                                <span class="text-blue-600 flex items-center">
                                                    <i class="fas fa-comment mr-1"></i>
                                                    {{ $log['countComment'] }}
                                                </span>
                                                <span class="text-green-600 flex items-center">
                                                    <i class="fas fa-file mr-1"></i>
                                                    {{ $log['countFile'] }}
                                                </span>
                                            </div>
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
                    {{-- @include('livewire.components.suport') --}}

                </div>
            </div>


            <!-- Support Info -->
            <div class="mt-8">
                @include('livewire.components.pengaduan-terbaru')
            </div>
            <!-- Support Info -->
            @if($isRole3)
            <div class="mt-8">
                @include('livewire.components.suport')
            </div>
            @endif
        @endif
    </div>
</div>


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
    </style>
@endpush
