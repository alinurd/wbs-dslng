<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Log Approval Pengaduan</h1>
                    <p class="text-gray-600 mt-2">Tracking progress dan status approval pengaduan</p>
                </div>
                <a href="{{route('dashboard')}}"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali</span>
                </a>
            </div>
        </div>

        @if($code_pengaduan && !empty($detailPengaduan))
        <!-- Card Pengaduan -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8 overflow-hidden">
            <!-- Header Pengaduan -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">{{ $detailPengaduan['perihal'] }}</h2>
                        <div class="flex items-center space-x-4 mt-2">
                            <span class="text-sm text-gray-600">ID: {{ $detailPengaduan['code_pengaduan'] }}</span>
                            <span class="px-3 py-1 bg-{{ $detailPengaduan['status_color'] }}-100 text-{{ $detailPengaduan['status_color'] }}-800 text-sm font-medium rounded-full">
                                {{ $detailPengaduan['status'] }}
                            </span>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-blue-600">{{ $detailPengaduan['progress'] }}%</div>
                        <div class="text-sm text-gray-500">Progress</div>
                    </div>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="px-6 py-3 bg-gray-50 border-b border-gray-200">
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-500" 
                         style="width: {{ $detailPengaduan['progress'] }}%"></div>
                </div>
            </div>

            <!-- Detail Pengaduan -->
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Detail Pengaduan</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Jenis Pengaduan</label>
                            <p class="text-gray-900">{{ $detailPengaduan['jenis_pengaduan'] }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Nama Terlapor</label>
                            <p class="text-gray-900">{{ $detailPengaduan['nama_terlapor'] }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Saluran Aduan</label>
                            <p class="text-gray-900">{{ $detailPengaduan['saluran_aduan'] }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Email Pelapor</label>
                            <p class="text-gray-900">{{ $detailPengaduan['email_pelapor'] }}</p>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Telepon Pelapor</label>
                            <p class="text-gray-900">{{ $detailPengaduan['telepon_pelapor'] }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Waktu Kejadian</label>
                            <p class="text-gray-900">{{ $detailPengaduan['waktu_kejadian'] }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Tanggal Pengaduan</label>
                            <p class="text-gray-900">{{ $detailPengaduan['tanggal_pengaduan'] }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Alamat Kejadian</label>
                            <p class="text-gray-900">{{ $detailPengaduan['alamat_kejadian'] }}</p>
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <label class="text-sm font-medium text-gray-500">Uraian Pengaduan</label>
                    <p class="text-gray-900 mt-1 bg-gray-50 p-3 rounded-lg">{{ $detailPengaduan['uraian'] }}</p>
                </div>

                <!-- Lampiran -->
                @if(count($detailPengaduan['lampiran']) > 0)
                <div class="mt-4">
                    <label class="text-sm font-medium text-gray-500">File Lampiran</label>
                    <div class="mt-2 space-y-2">
                        @foreach($detailPengaduan['lampiran'] as $file)
                        <div class="flex items-center justify-between bg-gray-50 rounded-lg px-3 py-2 border border-gray-200">
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-file text-gray-400"></i>
                                <span class="text-sm text-gray-700">{{ $file['original_name'] ?? $file['filename'] }}</span>
                            </div>
                            <a href="{{ $file['url'] }}" target="_blank" 
                               class="text-green-600 hover:text-green-700 text-sm flex items-center space-x-1">
                                <i class="fas fa-download text-xs"></i>
                                <span>Download</span>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Timeline Log Approval -->
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">Timeline Approval</h3>
                <div class="relative">
                    <!-- Timeline Line -->
                    <div class="absolute left-6 top-0 bottom-0 w-0.5 bg-gray-200"></div>
                    
                    @foreach($logApprovalData as $log)
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
                    <button wire:click="comment({{ $detailPengaduan['id'] }})" 
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors duration-200 flex items-center space-x-2">
                        <i class="fas fa-comment"></i>
                        <span>Tambah Komentar</span>
                    </button>
                </div>
            </div>
        </div>
        @else
        <!-- Empty State -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
            <i class="fas fa-inbox text-4xl text-gray-300 mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Pengaduan tidak ditemukan</h3>
            <p class="text-gray-500 mb-4">Code pengaduan tidak valid atau tidak ditemukan</p>
            <a href="{{ route('p_tracking') }}" 
               class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors duration-200">
                Kembali ke Tracking
            </a>
        </div>
        @endif
    </div>
</div>