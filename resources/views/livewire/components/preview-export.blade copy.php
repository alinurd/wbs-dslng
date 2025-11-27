@props([
    'showPreviewModal' => false,
    'previewMonth' => '',
    'previewTotal' => 0,
    'previewData' => [],
    'title' => 'Preview Export Data',
    'onClose' => '$set(\'showPreviewModal\', false)',
    'onExportExcel' => 'export(\'excel\')',
    'onExportPdf' => 'export(\'pdf\')'
])

@if ($showPreviewModal)
<div class="fixed inset-0 z-50 overflow-hidden" x-data="{ search: '' }">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
    </div>

    <!-- Modal Panel -->
    <div class="flex min-h-full items-end justify-center p-0 text-center sm:items-center sm:p-4">
        <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all w-full h-full max-h-[95vh] sm:max-w-[95vw]"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
            
            <!-- Header -->
            <div class="bg-gradient-to-r from-[rgb(0,111,188)] to-[rgb(0,95,160)] text-white px-6 py-4 sm:px-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="flex items-center justify-center w-10 h-10 bg-white/20 rounded-full">
                            <i class="fas fa-table text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold leading-6">
                                {{ $title }} - {{ $previewMonth }}
                            </h3>
                            <p class="text-white/90 text-sm mt-1">
                                Total: <strong>{{ $previewTotal }}</strong> records • 
                                <span class="text-white/80">Preview data sebelum export</span>
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <!-- Search Box -->
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input x-model="search" 
                                   type="text" 
                                   class="block w-64 pl-10 pr-3 py-2 border border-white/30 rounded-lg bg-white/10 text-white placeholder-white/70 focus:outline-none focus:ring-2 focus:ring-white/50 focus:border-white/50 transition-all"
                                   placeholder="Cari data...">
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="flex items-center space-x-2">
                            <!-- Excel Button -->
                            <button wire:click="{{ $onExportExcel }}" 
                                    wire:loading.attr="disabled"
                                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-green-700 bg-white rounded-lg hover:bg-green-50 border border-green-200 transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                <span wire:loading wire:target="{{ $onExportExcel }}" class="inline-flex items-center">
                                    <i class="fas fa-spinner fa-spin mr-2"></i>
                                </span>
                                <span wire:loading.remove wire:target="{{ $onExportExcel }}">
                                    <i class="fas fa-file-excel mr-2 text-green-600"></i>
                                </span>
                                Export Excel
                            </button>

                            <!-- PDF Button -->
                            <button wire:click="{{ $onExportPdf }}" 
                                    wire:loading.attr="disabled"
                                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-red-700 bg-white rounded-lg hover:bg-red-50 border border-red-200 transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                <span wire:loading wire:target="{{ $onExportPdf }}" class="inline-flex items-center">
                                    <i class="fas fa-spinner fa-spin mr-2"></i>
                                </span>
                                <span wire:loading.remove wire:target="{{ $onExportPdf }}">
                                    <i class="fas fa-file-pdf mr-2 text-red-600"></i>
                                </span>
                                Export PDF
                            </button>

                            <!-- Close Button -->
                            <button wire:click="{{ $onClose }}"
                                    class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-white/10 hover:bg-white/20 text-white transition-all duration-200 transform hover:rotate-90 focus:outline-none focus:ring-2 focus:ring-white/50">
                                <i class="fas fa-times text-lg"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table Container -->
            <div class="flex-1 overflow-auto bg-gray-50">
                <div class="min-w-full inline-block align-middle">
                    <div class="overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200 bg-white">
                            <thead class="bg-gray-50 sticky top-0 z-10">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider bg-gray-100 border-r border-gray-200" rowspan="2">
                                        <div class="flex items-center space-x-2">
                                            <span>No</span>
                                            <button class="text-gray-400 hover:text-gray-600 transition-colors">
                                                <i class="fas fa-sort"></i>
                                            </button>
                                        </div>
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider bg-gray-100 border-r border-gray-200" rowspan="2">
                                        Kode Tracking
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider bg-gray-100 border-r border-gray-200" rowspan="2">
                                        Perkiraan Waktu Kejadian
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider bg-gray-100 border-r border-gray-200" rowspan="2">
                                        Tanggal Aduan
                                    </th>
                                    
                                    <!-- Identitas Pelapor -->
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider bg-blue-50 border-r border-gray-200" colspan="3">
                                        <div class="flex items-center justify-center space-x-2">
                                            <i class="fas fa-user-circle text-blue-600"></i>
                                            <span>Identitas Pelapor</span>
                                        </div>
                                    </th>
                                    
                                    <!-- Identitas Terlapor -->
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider bg-orange-50 border-r border-gray-200" colspan="2">
                                        <div class="flex items-center justify-center space-x-2">
                                            <i class="fas fa-user-shield text-orange-600"></i>
                                            <span>Identitas Terlapor</span>
                                        </div>
                                    </th>
                                    
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider bg-gray-100 border-r border-gray-200" rowspan="2">
                                        Status Aduan
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider bg-gray-100 border-r border-gray-200" rowspan="2">
                                        Jenis Pelanggaran
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider bg-gray-100 border-r border-gray-200" rowspan="2">
                                        Perihal & Uraian
                                    </th>
                                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider bg-gray-100" rowspan="2">
                                        Admin User
                                    </th>
                                </tr>
                                <tr>
                                    <!-- Sub-headers for Pelapor -->
                                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase bg-blue-50 border-r border-gray-200">
                                        Nomor Ponsel
                                    </th>
                                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase bg-blue-50 border-r border-gray-200">
                                        Nama
                                    </th>
                                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase bg-blue-50 border-r border-gray-200">
                                        Kontak Detail
                                    </th>
                                    
                                    <!-- Sub-headers for Terlapor -->
                                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase bg-orange-50 border-r border-gray-200">
                                        Nama
                                    </th>
                                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase bg-orange-50 border-r border-gray-200">
                                        Direktorat
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @foreach($previewData as $index => $item)
                                <tr class="hover:bg-blue-50 transition-colors duration-150 group"
                                    x-show="search === '' || 
                                            '{{ strtolower($item->code_pengaduan ?? '') }}'.includes(search.toLowerCase()) ||
                                            '{{ strtolower($item->perihal ?? '') }}'.includes(search.toLowerCase())">
                                    <!-- No -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r border-gray-100">
                                        <div class="flex items-center space-x-2">
                                            <span class="bg-gray-100 px-2 py-1 rounded text-xs">{{ $index + 1 }}</span>
                                        </div>
                                    </td>
                                    
                                    <!-- Kode Tracking -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-blue-600 font-semibold border-r border-gray-100">
                                        <div class="flex items-center space-x-2">
                                            <i class="fas fa-barcode text-gray-400"></i>
                                            <span>{{ $item->code_pengaduan ?? '-' }}</span>
                                        </div>
                                    </td>
                                    
                                    <!-- Perkiraan Waktu Kejadian -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-100">
                                        <div class="flex flex-col">
                                            <span class="text-xs text-gray-500">Mulai:</span>
                                            <span class="font-medium">
                                                @if(isset($item->waktu_kejadian_mulai))
                                                    {{ \Carbon\Carbon::parse($item->waktu_kejadian_mulai)->format('d/m/Y H:i') }}
                                                @else
                                                    -
                                                @endif
                                            </span>
                                            <span class="text-xs text-gray-500 mt-1">Selesai:</span>
                                            <span class="font-medium">
                                                @if(isset($item->waktu_kejadian_selesai))
                                                    {{ \Carbon\Carbon::parse($item->waktu_kejadian_selesai)->format('d/m/Y H:i') }}
                                                @else
                                                    -
                                                @endif
                                            </span>
                                        </div>
                                    </td>
                                    
                                    <!-- Tanggal Aduan -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-100">
                                        <div class="flex items-center space-x-2">
                                            <i class="fas fa-calendar text-gray-400"></i>
                                            <span>
                                                @if(isset($item->tanggal_pengaduan))
                                                    {{ \Carbon\Carbon::parse($item->tanggal_pengaduan)->format('d/m/Y H:i') }}
                                                @else
                                                    -
                                                @endif
                                            </span>
                                        </div>
                                    </td>
                                    
                                    <!-- Identitas Pelapor -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-100 bg-blue-50/30">
                                        <div class="flex items-center space-x-2">
                                            <i class="fas fa-phone text-green-600"></i>
                                            <span>{{ $item->pelapor->phone ?? $item->user->phone ?? '-' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-100 bg-blue-50/30">
                                        <div class="flex items-center space-x-2">
                                            <i class="fas fa-user text-blue-600"></i>
                                            <span class="font-medium">{{ $this->getNamaUser($item) }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900 border-r border-gray-100 bg-blue-50/30">
                                        <div class="flex flex-col space-y-1">
                                            <span class="text-xs">Email: {{ $item->pelapor->email ?? $item->user->email ?? '-' }}</span>
                                            <span class="text-xs">Unit: {{ $item->pelapor->unit_kerja ?? '-' }}</span>
                                        </div>
                                    </td>
                                    
                                    <!-- Identitas Terlapor -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-100 bg-orange-50/30">
                                        <div class="flex items-center space-x-2">
                                            <i class="fas fa-user-tag text-orange-600"></i>
                                            <span class="font-medium">{{ $item->nama_terlapor ?? '-' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-100 bg-orange-50/30">
                                        <div class="flex items-center space-x-2">
                                            <i class="fas fa-building text-purple-600"></i>
                                            <span>{{ $item->direktorat_terlapor ?? '-' }}</span>
                                        </div>
                                    </td>
                                    
                                    <!-- Status Aduan -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm border-r border-gray-100">
                                        @php
                                            $statusInfo = $this->getStatusInfo($item->status ?? 0, $item->sts_final ?? 0);
                                        @endphp
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-{{ $statusInfo['color'] }}-100 text-{{ $statusInfo['color'] }}-800 border border-{{ $statusInfo['color'] }}-200">
                                            <span class="w-2 h-2 bg-{{ $statusInfo['color'] }}-500 rounded-full mr-2"></span>
                                            {{ $statusInfo['text'] }}
                                        </span>
                                    </td>
                                    
                                    <!-- Jenis Pelanggaran -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 border-r border-gray-100">
                                        <div class="flex items-center space-x-2">
                                            <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                                            <span>{{ $this->getJenisPelanggaran($item) }}</span>
                                        </div>
                                    </td>
                                    
                                    <!-- Perihal & Uraian -->
                                    <td class="px-6 py-4 text-sm text-gray-900 border-r border-gray-100 max-w-xs">
                                        <div class="space-y-2">
                                            <div>
                                                <span class="text-xs font-semibold text-gray-700">Perihal:</span>
                                                <p class="text-gray-900 line-clamp-2">{{ $item->perihal ?? '-' }}</p>
                                            </div>
                                            <div class="border-t pt-2">
                                                <span class="text-xs font-semibold text-gray-700">Uraian:</span>
                                                <p class="text-gray-600 line-clamp-3 text-xs">{{ $item->uraian ?? '-' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <!-- Admin User -->
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div class="flex items-center space-x-2">
                                            <i class="fas fa-user-cog text-gray-600"></i>
                                            <span>{{ $item->admin->name ?? 'System' }}</span>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        
                        <!-- Empty State -->
                        <template x-if="search !== '' && $el.querySelectorAll('tbody tr[x-show\\:expression]').length === 0">
                            <div class="text-center py-12">
                                <i class="fas fa-search text-gray-400 text-4xl mb-4"></i>
                                <p class="text-gray-500 text-lg">Tidak ada data yang sesuai dengan pencarian</p>
                                <p class="text-gray-400 text-sm mt-2">Coba kata kunci lain</p>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                <div class="text-sm text-gray-600">
                    <span x-text="`Menampilkan ${$el.querySelectorAll('tbody tr:not([style*=\"display: none\"])').length} dari {{ $previewTotal }} records`"></span>
                </div>
                <div class="flex items-center space-x-4">
                    <button wire:click="{{ $onClose }}"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
                        Tutup Preview
                    </button>
                    <button wire:click="{{ $onExportExcel }}"
                            class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700 transition-colors flex items-center space-x-2">
                        <i class="fas fa-download"></i>
                        <span>Download Excel</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Styles untuk line clamp -->
<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endif