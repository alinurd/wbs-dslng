@props([
    'showPreviewModal' => false,
    'previewMonth' => '',
    'previewTotal' => 0,
    'previewData' => [],
    'filterData' => [],
    'title' => 'Preview Export Data',
    'onClose' => '$set(\'showPreviewModal\', false)',
    'onExportExcel' => 'export(\'excel\')',
    'onExportPdf' => 'export(\'pdf\')'
])

@if ($showPreviewModal)
@php
    $activeFilters = !empty($filterData) ? array_filter($filterData) : [];
    $hasActiveFilters = count($activeFilters) > 0;
@endphp

<div class="fixed inset-0 z-50 overflow-hidden" x-data="{ search: '' }">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity"></div>

    <!-- Modal Panel -->
    <div class="flex min-h-full items-end justify-center p-0 text-center sm:items-center sm:p-4">
        <div class="relative transform overflow-hidden rounded-lg bg-white shadow-xl transition-all w-full h-full max-h-[95vh] sm:max-w-[95vw]">
            
            <!-- Header -->
            <div class="bg-gradient-to-r from-[rgb(0,111,188)] to-[rgb(0,95,160)] text-white px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="flex items-center justify-center w-10 h-10 bg-white/20 rounded-full">
                            <i class="fas fa-table text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold">
                                {{ $title }}
                            </h3>
                            <p class="text-white/90 text-sm mt-1">
                                <i class="fas fa-database mr-1"></i>
                                Total: <strong>{{ $previewTotal }}</strong> records
                                @if($previewMonth)
                                • <i class="fas fa-calendar mr-1"></i>
                                Periode: <strong>{{ $previewMonth }}</strong>
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <!-- Search Box -->
                        <div class="relative">
                            <input x-model="search" 
                                   type="text" 
                                   class="block w-64 pl-4 pr-3 py-2 border border-white/30 rounded-lg bg-white/10 text-white placeholder-white/70 focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-white/50"
                                   placeholder="Cari data...">
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="flex items-center space-x-2">
                            <button wire:click="{{ $onExportExcel }}" 
                                    wire:loading.attr="disabled"
                                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-green-700 bg-white rounded-lg hover:bg-green-50 border border-green-200 transition-colors">
                                <i class="fas fa-file-excel mr-2 text-green-600"></i>
                                Excel
                            </button>

                            <button wire:click="{{ $onExportPdf }}" 
                                    wire:loading.attr="disabled"
                                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-red-700 bg-white rounded-lg hover:bg-red-50 border border-red-200 transition-colors">
                                <i class="fas fa-file-pdf mr-2 text-red-600"></i>
                                PDF
                            </button>

                            <button wire:click="{{ $onClose }}"
                                    class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-white/10 hover:bg-white/20 text-white transition-colors">
                                <i class="fas fa-times text-lg"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Filter Info -->
                @if($hasActiveFilters)
                <div class="mt-3 pt-3 border-t border-white/20">
                    <div class="flex items-start space-x-3 text-sm">
                        <span class="font-semibold whitespace-nowrap pt-1 flex items-center">
                            <i class="fas fa-filter mr-1"></i>
                            Filter Aktif:
                        </span>
                        <div class="flex flex-wrap gap-2">
                            @foreach($activeFilters as $label => $value)
                            <span class="bg-white/20 px-3 py-1 rounded-full text-xs flex items-center space-x-1">
                                <span class="font-medium">{{ $label }}:</span>
                                <span class="max-w-xs truncate">{{ $value }}</span>
                            </span>
                            @endforeach
                        </div>
                    </div>
                </div>
                @else
                <!-- Info ketika tidak ada filter spesifik -->
                <div class="mt-3 pt-3 border-t border-white/20">
                    <div class="text-sm text-white/80 flex items-center">
                        <i class="fas fa-info-circle mr-2"></i>
                        Menampilkan 
                        @if($previewMonth)
                        data periode <strong>{{ $previewMonth }}</strong>
                        @else
                        <strong>semua data</strong>
                        @endif
                    </div>
                </div>
                @endif
            </div>

            <!-- Table Container -->
            <div class="flex-1 overflow-auto bg-white">
                <div class="min-w-full inline-block align-middle">
                    <div class="overflow-hidden">
                        <!-- Info Summary -->
                        <div class="bg-blue-50 border-b border-blue-200 px-6 py-3">
                            <div class="flex items-center justify-between text-sm">
                                <div class="flex items-center space-x-4 text-blue-700">
                                    <span class="flex items-center">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Data yang akan di-export
                                    </span>
                                    @if($hasActiveFilters)
                                    <span class="flex items-center">
                                        <i class="fas fa-filter mr-1"></i>
                                        {{ count($activeFilters) }} filter diterapkan
                                    </span>
                                    @endif
                                </div>
                                <div class="text-blue-600">
                                    <span x-text="`${$el.querySelectorAll('tbody tr:not([style*=\"display: none\"])').length} data tampil`"></span>
                                </div>
                            </div>
                        </div>

                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50 sticky top-0 z-10">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase border-r">No</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase border-r">Kode</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase border-r">Waktu Kejadian</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase border-r">Tanggal Aduan</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase border-r">Pelapor</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase border-r">Kontak</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase border-r">Terlapor</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase border-r">Direktorat</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase border-r">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase border-r">Jenis</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase border-r">Perihal</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Admin</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @foreach($previewData as $index => $item)
                                <tr class="hover:bg-blue-50 transition-colors"
                                    x-show="search === '' || 
                                            '{{ strtolower($item->code_pengaduan ?? '') }}'.includes(search.toLowerCase()) ||
                                            '{{ strtolower($item->perihal ?? '') }}'.includes(search.toLowerCase()) ||
                                            '{{ strtolower($this->getNamaUser($item)) }}'.includes(search.toLowerCase())">
                                    
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 border-r text-center">
                                        {{ $index + 1 }}
                                    </td>
                                    
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-mono text-blue-600 border-r">
                                        {{ $item->code_pengaduan ?? '-' }}
                                    </td>
                                    
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 border-r">
                                        @if(isset($item->waktu_kejadian_mulai))
                                            {{ \Carbon\Carbon::parse($item->waktu_kejadian_mulai)->format('d/m/Y') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 border-r">
                                        @if(isset($item->tanggal_pengaduan))
                                            {{ \Carbon\Carbon::parse($item->tanggal_pengaduan)->format('d/m/Y') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 border-r">
                                        {{ $this->getNamaUser($item) }}
                                    </td>
                                    
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 border-r">
                                        {{ $item->pelapor->phone ?? $item->user->phone ?? '-' }}
                                    </td>
                                    
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 border-r">
                                        {{ $item->nama_terlapor ?? '-' }}
                                    </td>
                                    
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 border-r">
                                        {{ $item->direktorat_terlapor ?? '-' }}
                                    </td>
                                    
                                    <td class="px-4 py-3 whitespace-nowrap text-sm border-r">
                                        @php
                                            $statusInfo = $this->getStatusInfo($item->status ?? 0, $item->sts_final ?? 0);
                                        @endphp
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-{{ $statusInfo['color'] }}-100 text-{{ $statusInfo['color'] }}-800">
                                            {{ $statusInfo['text'] }}
                                        </span>
                                    </td>
                                    
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 border-r">
                                        {{ $this->getJenisPelanggaran($item) }}
                                    </td>
                                    
                                    <td class="px-4 py-3 text-sm text-gray-900 border-r max-w-xs">
                                        <div class="space-y-1">
                                            <div class="font-medium line-clamp-1">{{ $item->perihal ?? '-' }}</div>
                                            @if($item->uraian)
                                            <div class="text-xs text-gray-600 line-clamp-2">{{ $item->uraian }}</div>
                                            @endif
                                        </div>
                                    </td>
                                    
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                        {{ $item->admin->name ?? 'System' }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <!-- Empty Search State -->
                        <template x-if="search !== '' && $el.querySelectorAll('tbody tr[x-show\\:expression]').length === 0">
                            <div class="text-center py-12">
                                <i class="fas fa-search text-gray-400 text-4xl mb-4"></i>
                                <p class="text-gray-500 text-lg">Tidak ada data yang sesuai dengan pencarian</p>
                                <p class="text-gray-400 text-sm mt-2">Coba kata kunci lain</p>
                            </div>
                        </template>

                        <!-- No Data State -->
                        @if($previewTotal === 0)
                        <div class="text-center py-12">
                            <i class="fas fa-inbox text-gray-400 text-4xl mb-4"></i>
                            <p class="text-gray-500 text-lg">Tidak ada data untuk ditampilkan</p>
                            <p class="text-gray-400 text-sm mt-2">Data tidak ditemukan dengan filter yang diterapkan</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-200 flex items-center justify-between">
                <div class="text-sm text-gray-600 flex items-center space-x-4">
                    <span x-text="`${$el.querySelectorAll('tbody tr:not([style*=\"display: none\"])').length} dari {{ $previewTotal }} records`"></span>
                    @if($hasActiveFilters)
                    <span class="text-gray-500">• {{ count($activeFilters) }} filter aktif</span>
                    @endif
                </div>
                <div class="flex items-center space-x-3">
                    <button wire:click="{{ $onClose }}"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50 transition-colors">
                        Tutup
                    </button>
                    @if($previewTotal > 0)
                    <button wire:click="{{ $onExportExcel }}"
                            class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded hover:bg-green-700 transition-colors flex items-center space-x-2">
                        <i class="fas fa-download"></i>
                        <span>Download Excel</span>
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endif