@props([
    'show' => false,
    'title' => 'Detail Data',
    'data' => [],
    'onClose' => '',
])

@if ($show)
    <div class="fixed inset-0 z-50 overflow-y-auto animate-fade-in" style="background-color: rgba(0,0,0,0.5)">
        <div class="flex min-h-full items-center justify-center p-4">
            <div
                class="modal-content bg-white rounded-lg shadow-xl w-full max-w-6xl transform transition-all duration-300 scale-95 ">
                <!-- Header -->
                <div
                    class="modal-header bg-gradient-to-r from-[rgb(0,111,188)] to-[rgb(0,95,160)] text-white rounded-t-lg px-6 py-5 shadow-lg">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="flex items-center justify-center w-10 h-10 bg-white/20 rounded-full">
                                <i class="fas fa-eye"></i>
                            </div>
                            <div>
                                <h5 class="modal-title text-xl font-bold tracking-tight">
                                    {{ $title }}
                                </h5>
                                <p class="text-white/80 text-sm">
                                    Detail informasi data
                                </p>
                            </div>
                        </div>
                        <button type="button" wire:click="{{ $onClose }}"
                            class="flex items-center justify-center w-9 h-9 rounded-full hover:bg-white/20 transition-all duration-300 hover:rotate-90">
                            <i class="fas fa-times text-base"></i>
                        </button>
                    </div>
                </div>

                <!-- Body -->
                <div class="modal-body p-6 max-h-[80vh] overflow-y-auto">
                    <!-- Data Umum (Ditampilkan Sekali) -->
                    @isset($data['common'])
                        <div class="bg-white border border-gray-200 rounded-lg shadow-sm mb-6">
                            <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 rounded-t-lg">
                                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                                    <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                                    Informasi Umum
                                </h3>
                            </div>
                            <div class="p-4 space-y-4">
                                @foreach ($data['common'] as $label => $value)
                                    @if (
                                        !in_array($label, [
                                            'Struktur Hirarki',
                                            'Hierarchy Structure',
                                            'Children List',
                                            'Daftar Children',
                                            'Jumlah Children',
                                        ]))
                                        <div
                                            class="flex flex-col sm:flex-row sm:items-start justify-between border-b border-gray-100 pb-3 last:border-b-0">
                                            <div class="sm:w-2/5 mb-1 sm:mb-0">
                                                <span
                                                    class="font-semibold text-gray-700 text-sm">{{ $label }}:</span>
                                            </div>
                                            <div class="sm:w-3/5">
                                                @if ($label === 'Gambar' && $value)
                                                    <div class="flex items-center space-x-3">
                                                        <div class="w-16 h-16 bg-gray-200 rounded-lg overflow-hidden">
                                                            <img src="{{ $value ? url('/file/' . base64_encode($value['imagePath'])) : asset('assets/images/news/4.png') }}"
                                                                class="w-full h-full object-cover"
                                                                onerror="this.style.display='none'">
                                                        </div>
                                                        <div class="flex items-center justify-between">
                                                                <div>
                                                                    <div class="font-medium text-sm text-gray-800">
                                                                        {{ $value['original_name'] ?? '-' }}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @if ($value['imagePath'])
                                                                <button
                                                                    wire:click="downloadFile('{{ $value['imagePath'] }}', '{{ $value['original_name'] }}')"
                                                                    class="flex items-center space-x-2 text-green-600 hover:text-green-700 bg-green-50 hover:bg-green-100 px-3 py-2 rounded-lg transition-colors duration-200 text-sm font-medium"
                                                                    title="Download {{ $value['original_name'] }}"> <i
                                                                        class="fas fa-download text-xs"></i>
                                                                    <span>Download</span>
                                                                </button>
                                                            @endif 

                                                    </div>
                                                @elseif($label === 'File' && is_array($value) && count($value) > 0)
                                                    <div class="space-y-2">
                                                        @foreach ($value as $file)
                                                            <div class="bg-gray-50 border border-gray-200 rounded p-3">
                                                                <div class="flex items-center justify-between">
                                                                    <div class="flex items-center space-x-3">
                                                                        <i class="fas fa-file text-blue-500"></i>
                                                                        <div>
                                                                            <div class="font-medium text-sm text-gray-800">
                                                                                {{ $file['original_name'] ?? $file['filename'] }}
                                                                            </div>
                                                                            <div class="text-xs text-gray-500">
                                                                                {{ $file['extension'] }} •
                                                                                {{ $file['size'] }}
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    @if ($file['path'])
                                                                        <button
                                                                            wire:click="downloadFile('{{ $file['path'] }}', '{{ $file['original_name'] }}')"
                                                                            class="flex items-center space-x-2 text-green-600 hover:text-green-700 bg-green-50 hover:bg-green-100 px-3 py-2 rounded-lg transition-colors duration-200 text-sm font-medium"
                                                                            title="Download {{ $file['original_name'] }}">
                                                                            <i class="fas fa-download text-xs"></i>
                                                                            <span>Download</span>
                                                                        </button>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @elseif(is_array($value))
                                                    <div class="space-y-1">
                                                        @foreach ($value as $item)
                                                            <div class="flex items-center space-x-2">
                                                                <i class="fas fa-cube text-blue-400 text-xs"></i>
                                                                <span
                                                                    class="text-sm text-gray-700">{{ is_array($item) ? json_encode($item) : $item }}</span>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <span
                                                        class="text-gray-900 text-sm font-medium">{{ $value ?? '-' }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endisset

                    <!-- Layout Dua Kolom untuk Konten Bahasa -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Kolom Kiri - Bahasa Indonesia -->
                        <div class="space-y-6">
                            <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 rounded-t-lg">
                                    <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                                        Konten (Indonesia)
                                    </h3>
                                </div>
                                <div class="p-4 space-y-4">
                                    @isset($data['id'])
                                        @foreach ($data['id'] as $label => $value)
                                            <div
                                                class="flex flex-col sm:flex-row sm:items-start justify-between border-b border-gray-100 pb-3 last:border-b-0">
                                                <div class="sm:w-2/5 mb-1 sm:mb-0">
                                                    <span
                                                        class="font-semibold text-gray-700 text-sm">{{ $label }}:</span>
                                                </div>
                                                <div class="sm:w-3/5">
                                                    @if ($label === 'Konten' || $label === 'Deskripsi')
                                                        <div class="bg-gray-50 border border-gray-200 rounded p-3">
                                                            <div
                                                                class="text-sm text-gray-700 whitespace-pre-line leading-relaxed prose max-w-none">
                                                                {!! $value ?? 'Tidak ada konten' !!}
                                                            </div>
                                                        </div>
                                                    @elseif(is_array($value))
                                                        <div class="space-y-1">
                                                            @foreach ($value as $item)
                                                                <div class="text-sm text-gray-700">{{ $item }}
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <span
                                                            class="text-gray-900 text-sm font-medium">{{ $value ?? '-' }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <p class="text-gray-500 text-center py-4">Tidak ada data</p>
                                    @endisset
                                </div>
                            </div>
                        </div>

                        <!-- Kolom Kanan - Bahasa Inggris -->
                        <div class="space-y-6">
                            <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 rounded-t-lg">
                                    <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                                        Content (English)
                                    </h3>
                                </div>
                                <div class="p-4 space-y-4">
                                    @isset($data['en'])
                                        @foreach ($data['en'] as $label => $value)
                                            <div
                                                class="flex flex-col sm:flex-row sm:items-start justify-between border-b border-gray-100 pb-3 last:border-b-0">
                                                <div class="sm:w-2/5 mb-1 sm:mb-0">
                                                    <span
                                                        class="font-semibold text-gray-700 text-sm">{{ $label }}:</span>
                                                </div>
                                                <div class="sm:w-3/5">
                                                    @if ($label === 'Content' || $label === 'Description')
                                                        <div class="bg-gray-50 border border-gray-200 rounded p-3">
                                                            <div
                                                                class="text-sm text-gray-700 whitespace-pre-line leading-relaxed prose max-w-none">
                                                                {!! $value ?? 'No content' !!}
                                                            </div>
                                                        </div>
                                                    @elseif(is_array($value))
                                                        <div class="space-y-1">
                                                            @foreach ($value as $item)
                                                                <div class="text-sm text-gray-700">{{ $item }}
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <span
                                                            class="text-gray-900 text-sm font-medium">{{ $value ?? '-' }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <p class="text-gray-500 text-center py-4">No data available</p>
                                    @endisset
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer border-t border-gray-200 px-6 py-4 flex justify-end">
                    <button type="button" wire:click="{{ $onClose }}"
                        class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-all duration-300 transform hover:scale-105 font-medium">
                        <i class="fas fa-times me-2"></i>Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif
