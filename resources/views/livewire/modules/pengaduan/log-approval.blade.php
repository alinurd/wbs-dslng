<div class="log-detail">
    @include('livewire.components.head-card', [
        'title' => 'Log Approval Pengaduan',
        'dsc' => 'Tracking progress dan status approval pengaduan',
    ])
    <div class="faq-container">


        @if ($code_pengaduan && !empty($detailPengaduan))
            <!-- Card Pengaduan -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8 overflow-hidden">
                <!-- Header Pengaduan -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900">{{ $detailPengaduan['jenis_pengaduan'] }}</h2>
                            <div class="flex items-center space-x-4 mt-2"> 
                                <span
                                    class="px-3 py-1 bg-{{ $detailPengaduan['status_color'] }}-100 text-{{ $detailPengaduan['status_color'] }}-800 text-sm font-medium rounded-full">
                                    {{ $detailPengaduan['status'] }}
                                </span>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold text-blue-600">
                                #{{ $detailPengaduan['code_pengaduan'] }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="px-6 py-3 bg-gray-50 border-b border-gray-200">
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-{{ $detailPengaduan['status_color'] }}-600 h-2 rounded-full transition-all duration-500"></div>
                    </div>
                </div>

                <!-- Detail Pengaduan -->
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Detail Pengaduan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-3">
                            <div>
                                <label class="text-sm font-medium text-gray-500">Perihal</label>
                                <p class="text-gray-900 text-sm">{{ $detailPengaduan['perihal'] }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Nama Terlapor</label>
                                <p class="text-gray-900 text-sm">{{ $detailPengaduan['nama_terlapor'] }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Saluran Aduan</label>
                                <p class="text-gray-900 text-sm">{{ $detailPengaduan['saluran_aduan'] }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Email Pelapor</label>
                                <p class="text-gray-900 text-sm">{{ $detailPengaduan['email_pelapor'] }}</p>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div>
                                <label class="text-sm font-medium text-gray-500">Telepon Pelapor</label>
                                <p class="text-gray-900 text-sm">{{ $detailPengaduan['telepon_pelapor'] }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Waktu Kejadian</label>
                                <p class="text-gray-900 text-sm">{{ $detailPengaduan['waktu_kejadian'] }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Tanggal Pengaduan</label>
                                <p class="text-gray-900 text-sm">{{ $detailPengaduan['tanggal_pengaduan'] }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-500">Alamat Kejadian</label>
                                <p class="text-gray-900 text-sm">{{ $detailPengaduan['alamat_kejadian'] }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <label class="text-sm font-medium text-gray-500">Uraian Pengaduan</label>
                        <p class="text-gray-900 mt-1 bg-gray-50 p-3 rounded-lg text-sm">{{ $detailPengaduan['uraian'] }}</p>
                    </div>

                    <!-- Lampiran -->
                      @if (!empty($detailPengaduan['lampiran']))
                                        @php
                                            $files = json_decode($detailPengaduan['lampiran'], true);
                                        @endphp 
                        <div class="mt-4">
                            <label class="text-sm font-medium text-gray-500">File Lampiran</label>
                            <div class="mt-2 space-y-2">
                                @foreach ($files as $file)
                                    <div
                                        class="flex items-center justify-between bg-gray-50 rounded-lg px-3 py-2 border border-gray-200">
                                        <div class="flex items-center space-x-2">
                                            <i class="fas fa-file text-gray-400"></i>
                                            <span
                                                class="text-sm text-gray-700">{{ $file['original_name'] ?? $file['filename'] }}</span>
                                        </div>
                                        <button
                                                            wire:click="downloadFile('{{ $file['path'] }}', '{{ $file['original_name'] }}')"
                                                            class="text-green-600 hover:text-green-700 text-xs flex items-center space-x-3 hover:underline">
                                                            <i class="fas fa-download text-xs"></i>
                                                            <span>Download</span>
                                                        </button>
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
                        <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200"></div>

                        @foreach ($logApprovalData as $log)
                             <div class="relative flex items-start space-x-4 mb-8 last:mb-0">
                                <!-- Step Indicator -->
                                <div class="relative z-10 flex-shrink-0">
                                @if($log['infoSts']['text1'] === 'completed')
                                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                    <i class="fas fa-check text-white text-xs"></i>
                                </div>
                                @elseif($log['infoSts']['text1'] === 'in_progress')
                                <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                                    <i class="fas fa-sync-alt text-white text-xs animate-spin"></i>
                                </div>
                                @elseif($log['infoSts']['text1'] === 'rejected')
                                <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center">
                                    <i class="fas fa-times text-white text-xs"></i>
                                </div>
                                @else
                                <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                                    <i class="fas fa-clock text-gray-600 text-xs"></i>
                                </div>
                                @endif
                            </div>
                                <!-- Content -->
                                <div
                                    class="flex-1 bg-{{ $log['status_color'] }}-50 rounded-lg p-4 border border-{{ $log['status_color'] }}-200">
                                    <div class="flex items-center justify-between mb-2">
                                        <div>
                                            <h3 class="font-semibold text-gray-900">{{ $log['role'] }}</h3>
                                            <p class="text-sm text-gray-600">{{ $log['user_name'] }}</p>
                                        </div>
                                        <div class="text-right">
                                            <span
                                                class="px-3 py-1 bg-{{ $log['status_color'] }}-100 text-{{ $log['status_color'] }}-800 text-sm font-medium rounded-full">
                                                {{ $log['status'] }}
                                            </span>
                                            <p class="text-xs text-gray-500 mt-1">{{ $log['waktu'] }}</p>
                                        </div>
                                    </div>

                                    <!-- Catatan -->
                                    @if ($log['catatan_full'])
                                        <div class="mb-3">
                                            <p class="text-sm text-gray-700">{{ $log['catatan_full'] }}</p>
                                        </div>
                                    @endif

                                    <!-- File Attachments -->
                                    @if (!empty($log['file']))
                                        @php
                                            $files = json_decode($log['file'], true);
                                        @endphp
                                        <div class="border-t border-{{ $log['status_color'] }}-200 pt-2 mt-2">
                                            <p class="text-xs font-medium text-gray-700 mb-1">File Lampiran:</p>
                                            <div class="space-y-1">
                                                @foreach ($files as $file)
                                                    <div
                                                        class="flex items-center justify-between bg-white rounded-lg px-2 py-1 border border-gray-200">
                                                        <div class="flex items-center space-x-5 min-w-0 flex-1">
                                                            <i class="fas fa-file text-gray-400 text-xs"></i>
                                                            <span class="text-xs text-gray-700 truncate">
                                                                {{ $file['original_name'] }}</span>
                                                            </span>
                                                        </div>
                                                        <button
                                                            wire:click="downloadFile('{{ $file['path'] }}', '{{ $file['original_name'] }}')"
                                                            class="text-green-600 hover:text-green-700 text-xs flex items-center space-x-3 hover:underline">
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
                        <button wire:click="comment({{ $detailPengaduan['id'] }})" @click="open = false"
                                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors duration-200 flex items-center space-x-2">
                                            <i class="fas fa-comments w-4 h-4"></i>
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
