<div class="bg-white rounded-2xl shadow-lg p-6 mb-5">
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
                                        <span class="text-gray-600 flex items-center">
                                            <i class="fas fa-comment mr-1"></i>
                                            {{ $pengaduan['countComment'] }}
                                        </span>
                                        <span class="text-gray-600 flex items-center">
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