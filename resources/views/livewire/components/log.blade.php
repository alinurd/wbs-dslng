@php
    // Jika data kosong, tampilkan empty state
    $isEmpty = empty($data);
@endphp

<div class="h-full flex flex-col">
    <!-- Header Compact -->
    <div class="mb-4 pb-3 border-b border-gray-200">
        <p class="text-sm text-gray-600">Tracking progress dan status approval pengaduan</p>
    </div>

    @if(!$isEmpty)
        <!-- Scrollable Content - Sama seperti form catatan -->
        <div class="flex-1 overflow-y-auto space-y-4 pr-2">
            @foreach($data as $pengaduan)
            <!-- Card Pengaduan Compact -->
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <!-- Header Pengaduan Compact -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-4 py-3 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <h2 class="text-sm font-semibold text-gray-900 truncate">{{ $pengaduan['judul_pengaduan'] }}</h2>
                            <div class="flex items-center space-x-3 mt-1">
                                <span class="text-xs text-gray-500">ID: {{ $pengaduan['id'] }}</span>
                                @php
                                    $lastLog = end($pengaduan['log_approval']);
                                    $warna = $lastLog['warna'];
                                @endphp
                                <span class="px-2 py-0.5 bg-{{ $warna }}-100 text-{{ $warna }}-800 text-xs font-medium rounded-full">
                                    {{ $pengaduan['status_akhir'] }}
                                </span>
                            </div>
                        </div>
                        <div class="text-right ml-3">
                            <div class="text-lg font-bold text-blue-600">{{ $pengaduan['progress'] }}%</div>
                            <div class="text-xs text-gray-500">Progress</div>
                        </div>
                    </div>
                </div>

                <!-- Progress Bar Compact -->
                <div class="px-4 py-2 bg-gray-50 border-b border-gray-200">
                    <div class="w-full bg-gray-200 rounded-full h-1.5">
                        <div class="bg-blue-600 h-1.5 rounded-full transition-all duration-500" 
                             style="width: {{ $pengaduan['progress'] }}%"></div>
                    </div>
                </div>

                <!-- Timeline Log Approval Compact dengan Scroll -->
                <div class="p-4 pb-9 max-h-96 overflow-y-auto"> <!-- Tambahkan max-height dan overflow-y-auto di sini -->
                    <div class="relative">
                        <!-- Timeline Line -->
                        <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200"></div>
                        
                        @foreach($pengaduan['log_approval'] as $log)
                        <div class="relative flex items-start space-x-3 mb-4 last:mb-0">
                            <!-- Step Indicator Compact -->
                            <div class="relative z-10 flex-shrink-0">
                                @if($log['status'] === 'completed')
                                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                    <i class="fas fa-check text-white text-xs"></i>
                                </div>
                                @elseif($log['status'] === 'in_progress')
                                <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                                    <i class="fas fa-sync-alt text-white text-xs animate-spin"></i>
                                </div>
                                @elseif($log['status'] === 'rejected')
                                <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center">
                                    <i class="fas fa-times text-white text-xs"></i>
                                </div>
                                @else
                                <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                                    <i class="fas fa-clock text-gray-600 text-xs"></i>
                                </div>
                                @endif
                            </div>

                            <!-- Content Compact -->
                            <div class="flex-1 bg-{{ $log['warna'] }}-50 rounded-lg p-3 border border-{{ $log['warna'] }}-200">
                                <div class="flex items-start justify-between mb-2">
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-sm font-semibold text-gray-900">{{ $log['role'] }}</h3>
                                        {{-- <p class="text-xs text-gray-600 truncate">{{ $log['nama'] }}</p> --}}
                                    </div>
                                    <div class="text-right ml-2 flex-shrink-0">
                                        <span class="px-2 py-0.5 bg-{{ $log['warna'] }}-100 text-{{ $log['warna'] }}-800 text-xs font-medium rounded-full">
                                            {{ $log['status_text'] }}
                                        </span>
                                        <p class="text-xs text-gray-500 mt-0.5">{{ $log['waktu'] }}</p>
                                    </div>
                                </div>

                                <!-- Catatan Compact -->
                                @if(!empty($log['catatan']))
                                <div class="mb-2">
                                    <p class="text-xs text-gray-700 bg-white rounded px-2 py-1 border border-gray-200 line-clamp-2">
                                        <span class="font-medium">Catatan:</span> {{ $log['catatan'] }}
                                    </p>
                                </div>
                                @endif

                                <!-- File Attachments Compact -->
                                @if(count($log['file']) > 0)
                                <div class="border-t border-{{ $log['warna'] }}-200 pt-2 mt-2">
                                    <p class="text-xs font-medium text-gray-700 mb-1">File Lampiran:</p>
                                    <div class="space-y-1">
                                        @foreach($log['file'] as $file)
                                        <div class="flex items-center justify-between bg-white rounded px-2 py-1 border border-gray-200">
                                            <div class="flex items-center space-x-1 min-w-0 flex-1">
                                                <i class="fas fa-file text-gray-400 text-xs"></i>
                                                <span class="text-xs text-gray-700 truncate">{{ $file }}</span>
                                            </div>
                                            <button class="text-green-600 hover:text-green-700 text-xs flex items-center space-x-1 ml-2 flex-shrink-0">
                                                <i class="fas fa-download text-xs"></i>
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
                </div>
            </div>
            @endforeach
        </div>
    @else
        <!-- Empty State Compact -->
        <div class="bg-white rounded-lg border border-gray-200 p-6 text-center">
            <i class="fas fa-inbox text-2xl text-gray-300 mb-2"></i>
            <h3 class="text-sm font-medium text-gray-900 mb-1">Belum ada log approval</h3>
            <p class="text-xs text-gray-500 mb-3">Pengaduan yang Anda buat akan muncul di sini</p>
            <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors duration-200">
                Buat Pengaduan
            </button>
        </div>
    @endif
</div>