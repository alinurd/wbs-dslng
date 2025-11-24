@php
    $roleIds = array_keys($userRole);
    $isRole3 = in_array(3, $roleIds);
    $reportRoute = $isRole3 ? 'p_report' : 'complien';
@endphp

<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100">
    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- {{dd($data)}} --}}


        <!-- Welcome Banner -->
        <div class="bg-gradient-to-r from-[#0077C8] to-[#003B73] rounded-2xl p-8 text-white mb-8 shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold mb-2">Selamat Datang, {{ $user->name }}!</h1>
                    <p class="text-blue-100 text-lg mb-4">
                        Sistem pelaporan yang aman, rahasia, dan terpercaya untuk menciptakan budaya kerja transparan
                        dan berintegritas
                    </p>
                    @if (!$user->email_verified_at)
                        <div class="bg-red-500/20 border border-red-300 rounded-lg p-4 inline-block">
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-exclamation-triangle text-red-300"></i>
                                <span class="text-red-100 font-medium">Silahkan lakukan verifikasi email terlebih
                                    dahulu!</span>
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
                <div class="mt-4 flex items-center text-sm text-blue-600">
                    <i class="fas fa-arrow-up mr-1"></i>
                    <span>pengaduan anda</span>
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
                    <span>{{ $stats['selesai_persentase'] ?? '' }} terselesaikan</span>
                </div>
            </div>

            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Proses Review</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['dalam_proses'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-sync-alt text-purple-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm text-purple-600">
                    <i class="fas fa-clock mr-1"></i>
                    <span>Peninjauan</span>
                </div>
            </div>


            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Menggu Review</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['menunggu'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-yellow-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm text-yellow-600">
                    <i class="fas fa-clock mr-1"></i>
                    <span>menunggu antrian</span>
                </div>
            </div>
        </div>

        <!-- Main Dashboard Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Quick Actions -->
            <div class="lg:col-span-1">
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-1 gap-6">
                    @if($isRole3 || $isRole3)
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
                        <a href="{{ route('p_report') }}"
                            class="w-full bg-green-600 hover:bg-green-700 text-white py-3 px-4 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center space-x-2">
                            <i class="fas fa-plus"></i>
                            <span>Mulai Pengaduan</span>
                        </a>
                    </div>
                    @endif

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
                        <a href="{{ route($reportRoute) }}"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center space-x-2">
                            <i class="fas fa-truck"></i>
                            <span>Lacak Status</span>
                        </a>
                    </div>
                    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                        <div class="flex items-center space-x-4 mb-4">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-question text-cyan-600 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">FAQ</h3>
                                <p class="text-sm text-gray-500">Jawaban cepat untuk pertanyaan umum</p>
                            </div>
                        </div>
                        <a href="{{ route('faq') }}"
                            class="w-full bg-cyan-600 hover:bg-cyan-700 text-white py-3 px-4 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center space-x-2">
                            <i class="fas fa-search"></i>
                            <span>Jelajahi FAQ</span>
                        </a>
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
                            <a href="{{ route($reportRoute) }}"
                                class="text-blue-600 hover:text-blue-700 text-sm font-medium flex items-center space-x-1">
                                <span>Lihat Semua</span>
                                <i class="fas fa-chevron-right text-xs"></i>
                            </a>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            @foreach ($pengaduan_terbaru as $pengaduan)
                                <div
                                    class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-blue-50 transition-colors duration-200">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-exclamation-triangle text-blue-600"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $pengaduan['judul'] }} -
                                                {{ $pengaduan['code_pengaduan'] }}</p>
                                            <p class="text-sm text-gray-500">Dilaporkan {{ $pengaduan['tanggal'] }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <span
                                            class="px-3 py-1 bg-{{ $pengaduan['status_color'] }}-100 text-{{ $pengaduan['status_color'] }}-800 text-xs font-medium rounded-full">
                                            {{ $pengaduan['status'] }}
                                        </span>
                                        <a href="{{ route('log_detail', ['code_pengaduan' => $pengaduan['code_pengaduan']]) }}"
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
                                @foreach ($log_approval as $log)
                                    <div class="border-l-4 border-blue-500 pl-4 py-2">
                                        <div class="flex justify-between items-start mb-1">
                                            <p class="font-medium text-gray-900">{{ $log['judul'] }}</p>
                                            <span class="text-xs text-gray-500">{{ $log['waktu'] }}</span>
                                        </div>
                                        <p class="text-sm text-gray-600 mb-2">{{ $log['deskripsi'] }}</p>
                                        <div class="flex items-center space-x-3">
                                            <span
                                                class="px-2 py-1 bg-{{ $log['status_color'] }}-100 text-{{ $log['status_color'] }}-800 text-xs font-medium rounded-full">
                                                {{ $log['status'] }}
                                            </span>
                                            @if(!empty($log['file']))
    @php
        $files = json_decode($log['file'], true);
    @endphp
    
    @if(is_array($files) && count($files) > 0)
        <div class="mt-3">
            <p class="text-xs font-medium text-gray-500 mb-2">Lampiran:</p>
            <div class="space-y-2">
                @foreach($files as $fileItem)
                    <div class="flex items-center justify-between bg-white rounded-lg px-3 py-2 border border-gray-200">
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-file text-gray-400"></i>
                            <span class="text-sm text-gray-700">{{ $fileItem['original_name'] }}</span>
                        </div>
                        <a href="{{ $fileItem['url'] }}" 
                           target="_blank"
                           class="text-green-600 hover:text-green-700 text-sm flex items-center space-x-1">
                            <i class="fas fa-download text-xs"></i>
                            <span>Download</span>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
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
                                @foreach ($progress_bulanan as $progress)
                                    <div>
                                        <div class="flex justify-between text-sm mb-1">
                                            <span class="text-gray-600">{{ $progress['label'] }}</span>
                                            <span class="font-medium text-gray-900">{{ $progress['jumlah'] }}
                                                laporan</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-{{ $progress['color'] }}-600 h-2 rounded-full"
                                                style="width: {{ $progress['persentase'] }}%"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Support Info -->
                @include('livewire.components.suport')
            </div>
        </div>
    </div>
</div>
