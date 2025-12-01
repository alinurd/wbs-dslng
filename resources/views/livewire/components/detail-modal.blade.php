@props([
    'show' => false,
    'title' => 'Detail Data',
    'data' => [],
    'onClose' => '',
])

@php
    // Cek apakah ada data hierarchy atau children untuk menentukan layout
    $hasHierarchy = isset($data['Struktur Hirarki']) || isset($data['Hierarchy Structure']);
    $hasChildren = isset($data['Jumlah Children']) || isset($data['Children List']) || isset($data['Daftar Children']);
    $useTwoColumns = $hasHierarchy || $hasChildren;

    // Tentukan ukuran modal berdasarkan konten
    $modalSize = $useTwoColumns ? 'max-w-6xl' : 'max-w-2xl';
@endphp

@if ($show)
    <div class="fixed inset-0 z-50 overflow-y-auto animate-fade-in" style="background-color: rgba(0,0,0,0.5)">
        <div class="flex min-h-full items-center justify-center p-4">
            <div
                class="modal-content bg-white rounded-lg shadow-xl w-full {{ $modalSize }} transform transition-all duration-300 scale-95 ">
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
                    @if ($useTwoColumns)
                        <!-- Layout Dua Kolom untuk data dengan hierarchy/children -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Kolom Kiri - Data Utama -->
                            <div class="space-y-6">
                                <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                                    <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 rounded-t-lg">
                                        <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                                            <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                                            Informasi Utama
                                        </h3>
                                    </div>
                                    <div class="p-4 space-y-4">
                                        @foreach ($data as $label => $value)
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
                                                        @if ($label === 'Status' || $label === 'Status Lapor' || $label === 'Status Lapor Kerja')
                                                            <span
                                                                class="px-3 py-1 text-sm font-semibold rounded-full {{ $value === 'Aktif' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200' }}">
                                                                <i
                                                                    class="fas fa-circle text-xs mr-1 {{ $value === 'Aktif' ? 'text-green-500' : 'text-red-500' }}"></i>
                                                                {{ $value }}
                                                            </span>
                                                        @elseif($label === 'Deskripsi' || $label === 'Description')
                                                            <div class="bg-gray-50 border border-gray-200 rounded p-3">
                                                                <p
                                                                    class="text-sm text-gray-700 whitespace-pre-line leading-relaxed">
                                                                    {{ $value ?? 'Tidak ada deskripsi' }}</p>
                                                            </div>
                                                        @elseif(is_array($value))
                                                            <div class="space-y-1">
                                                                @foreach ($value as $item)
                                                                    <div class="flex items-center space-x-2">
                                                                        <i class="fas user text-blue-400 text-xs"></i>
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

                            </div>

                            <!-- Kolom Kanan - Struktur Hirarki -->
                            <div class="space-y-6">
                                @if ($hasHierarchy)
                                    @php
                                        $hierarchyData =
                                            $data['Struktur Hirarki'] ?? ($data['Hierarchy Structure'] ?? []);
                                    @endphp
                                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                                        <div
                                            class="bg-gradient-to-r from-blue-500 to-blue-600 px-4 py-3 border-b border-blue-600 rounded-t-lg">
                                            <h3 class="text-lg font-semibold text-white flex items-center">
                                                <i class="fas fa-sitemap mr-2"></i>
                                                Struktur Organisasi
                                            </h3>
                                        </div>
                                        <div class="p-4">
                                            @if (is_array($hierarchyData) && count($hierarchyData) > 0)
                                                <div class="space-y-4">
                                                    @foreach ($hierarchyData as $level => $items)
                                                        @if (is_array($items) && count($items) > 0)
                                                            <div class="bg-gray-50 border border-gray-200 rounded p-4">
                                                                <div class="flex items-center mb-3">
                                                                    <div class="flex-shrink-0">
                                                                        @switch($level)
                                                                            @case('grandparent')
                                                                                <i class="fas fa-user text-purple-500"></i>
                                                                            @break

                                                                            @case('parent')
                                                                                <i class="fas fa-user text-blue-500"></i>
                                                                            @break

                                                                            @case('current')
                                                                                <i class="fas fa-user text-green-500"></i>
                                                                            @break

                                                                            @case('children')
                                                                                <i class="fas fa-users text-orange-500"></i>
                                                                            @break

                                                                            @default
                                                                                <i class="fas user text-gray-500"></i>
                                                                        @endswitch
                                                                    </div>
                                                                    <div class="ml-3">
                                                                        <span
                                                                            class="font-bold text-gray-800 capitalize">
                                                                            {{ $level === 'current' ? 'Current' : ucfirst($level) }}
                                                                        </span>
                                                                        <span
                                                                            class="ml-2 px-2 py-1 text-xs font-semibold bg-blue-100 text-blue-800 rounded">
                                                                            {{ count($items) }} item
                                                                        </span>
                                                                    </div>
                                                                </div>

                                                                <div class="space-y-2">
                                                                    @foreach ($items as $item)
                                                                        <div
                                                                            class="flex items-center space-x-2 p-2 bg-white rounded border border-gray-200 hover:shadow-sm transition-all">
                                                                            <i
                                                                                class="fas user text-blue-400 text-sm"></i>
                                                                            <span class="text-sm text-gray-700">
                                                                                @if (is_array($item))
                                                                                    {{ $item['name'] ?? ($item['owner_name'] ?? json_encode($item)) }}
                                                                                @else
                                                                                    {{ $item }}
                                                                                @endif
                                                                            </span>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>

                                                            @if (!$loop->last)
                                                                <div class="flex justify-center">
                                                                    <div
                                                                        class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                                                        <i class="fas fa-arrow-down text-blue-500"></i>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="text-center py-8">
                                                    <i class="fas fa-project-diagram text-gray-300 text-4xl mb-3"></i>
                                                    <p class="text-gray-500">Tidak ada data hirarki</p>
                                                    <p class="text-gray-400 text-sm mt-1">Data struktur organisasi tidak
                                                        tersedia</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <!-- Layout Satu Kolom untuk data sederhana -->
                        <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                            <div class="p-6 space-y-4">
                                @foreach ($data as $label => $value)
                                    <div
                                        class="flex flex-col sm:flex-row sm:items-start justify-between border-b border-gray-100 pb-4 last:border-b-0">
                                        <div class="sm:w-2/5 mb-2 sm:mb-0">
                                            <span class="font-semibold text-gray-700">{{ $label }}:</span>
                                        </div>
                                        <div class="sm:w-3/5">
                                            @if ($label === 'Status Pengaduan')
                                                <span class="px-3 py-1 text-sm font-semibold rounded-full">
                                                    {!! $value !!}
                                                </span>
                                            @elseif ($label === 'Status' || $label === 'Status Lapor' || $label === 'Status Lapor Kerja')
                                                <span
                                                    class="px-3 py-1 text-sm font-semibold rounded-full {{ $value === 'Aktif' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200' }}">
                                                    <i
                                                        class="fas fa-circle text-xs mr-1 {{ $value === 'Aktif' ? 'text-green-500' : 'text-red-500' }}"></i>
                                                    {{ $value }}
                                                </span>
                                            @elseif($label === 'Deskripsi' || $label === 'Description')
                                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                                    <p class="text-gray-700 whitespace-pre-line leading-relaxed">
                                                        {{ $value ?? 'Tidak ada deskripsi' }}</p>
                                                </div>
                                            @elseif ($label === 'Files')
                                                @forelse ($value as $file)
                                                    <div
                                                        class="flex items-center justify-between bg-white rounded-xl p-4 border border-gray-200 hover:border-blue-300 hover:shadow-lg transition-all duration-300 group">
                                                        <div class="flex items-center space-x-3 min-w-0 flex-1">
                                                            <div
                                                                class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                                                                <i class="fas fa-file text-white text-sm"></i>
                                                            </div>
                                                            <div class="min-w-0 flex-1">
                                                                <p class="text-sm font-medium text-gray-900 truncate">
                                                                    {{ $file['original_name'] }}
                                                                </p>
                                                                <p class="text-xs text-gray-500 mt-1">
                                                                    {{ strtoupper($file['extension'] ?? 'FILE') }} •
                                                                    {{ $this->formatFileSize($file['size'] ?? 0) }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                        <button
                                                            wire:click="downloadFile('{{ $file['path'] }}', '{{ $file['original_name'] }}')"
                                                            class="flex items-center space-x-2 text-green-600 hover:text-green-700 bg-green-50 hover:bg-green-100 px-3 py-2 rounded-lg transition-colors duration-200 text-sm font-medium"
                                                            title="Download {{ $file['original_name'] }}">
                                                            <i class="fas fa-download text-xs"></i>
                                                            <span>Download</span>
                                                        </button>
                                                    </div>
                                                @empty
                                                @endforelse
                                            @elseif(is_array($value))
                                                <div class="space-y-2">
                                                    @foreach ($value as $item)
                                                        <div class="flex items-center space-x-2">
                                                            <i class="fas user text-blue-400 text-sm"></i>
                                                            <span
                                                                class="text-gray-700">{{ is_array($item) ? json_encode($item) : $item }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @elseif($label === 'Tes Connect')
                                                <div
                                                    class="space-y-4 p-4 border border-gray-200 rounded-lg bg-white shadow-sm">
                                                    <h6 class="font-semibold text-base text-gray-800">Test Email
                                                        Connection</h6>
                                                    <div class="space-y-2">
                                                        <label class="block text-sm font-medium text-gray-700">
                                                            Email Tujuan <span class="text-red-500">*</span>
                                                        </label>
                                                        <div class="relative">
                                                            <input type="email" wire:model="testEmail"
                                                                class="w-full rounded-lg border p-3 bg-white text-gray-900 focus:border-[rgb(0,111,188)] focus:ring-2 focus:ring-[rgb(0,111,188)] shadow-sm transition-all duration-300 border-gray-300"
                                                                placeholder="Masukan Email Tujuan"
                                                                {{ $isTesting ? 'disabled' : '' }}>
                                                            @error('testEmail')
                                                                <p class="text-red-500 text-xs mt-1">{{ $message }}
                                                                </p>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <!-- Test Buttons -->
                                                    <div class="flex flex-col space-y-2 pt-2">
                                                        <!-- Test Current Form Configuration -->
                                                        <button wire:click="testConnect({{ $value }})"
                                                            wire:loading.attr="disabled"
                                                            wire:loading.class="opacity-50 cursor-not-allowed"
                                                            class="inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-white bg-blue-600 border border-blue-600 rounded-md hover:bg-blue-700 focus:z-10 focus:ring-1 focus:ring-blue-600 transition-all duration-200">
                                                            <span wire:loading wire:target="testConnect"
                                                                class="inline-flex items-center">
                                                                <i class="fas fa-spinner fa-spin mr-2"></i>
                                                                Testing Config...
                                                            </span>
                                                            <span wire:loading.remove wire:target="testConnect"
                                                                class="inline-flex items-center">
                                                                <i class="fas fa-envelope mr-2"></i>
                                                                Test Configuration
                                                            </span>
                                                        </button>
                                                    </div>
                                                </div>


                                                <!-- add custom actoin -->
                                            @else
                                                <span class="text-gray-900">{{ $value ?? '-' }}</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
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
