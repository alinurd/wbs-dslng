@props([
    'showPreviewModal' => false,
    'previewMonth' => '',
    'previewTotal' => 0,
    'previewData' => [],
    'filterData' => [],
    'title' => 'Preview Export Data',
    'onClose' => '$set(\'showPreviewModal\', false)',
    'onExportExcel' => 'export(\'excelReportFull\')',
    'onExportPdf' => 'export(\'pdf\')',
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
            <div class="relative transform overflow-hidden rounded-lg bg-white shadow-xl transition-all w-full h-full max-h-[95vh] sm:max-w-[95vw] flex flex-col">

                <!-- Header -->
                <div class="bg-gradient-to-r from-[rgb(0,111,188)] to-[rgb(0,95,160)] text-white px-6 py-4 shrink-0">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="flex items-center justify-center w-10 h-10 bg-white/20 rounded-full">
                                <i class="fas fa-table text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold uppercase">
                                    {{ $title }}
                                </h3>
                                <p class="text-white/90 text-sm mt-1">
                                    <i class="fas fa-database mr-1"></i>
                                    Total: <strong>{{ $previewTotal }}</strong> records
                                    @if ($previewMonth)
                                        • <i class="fas fa-calendar mr-1"></i>
                                        Periode: <strong>{{ $previewMonth }}</strong>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <!-- Action Buttons -->
                            <div class="flex items-center space-x-2">
                                <button wire:click="{{ $onExportExcel }}" wire:loading.attr="disabled"
                                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-green-700 bg-white rounded-lg hover:bg-green-50 border border-green-200 transition-colors">
                                    <i class="fas fa-file-excel mr-2 text-green-600"></i>
                                    Excel
                                </button>

                                {{-- <button wire:click="{{ $onExportPdf }}" wire:loading.attr="disabled"
                                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-red-700 bg-white rounded-lg hover:bg-red-50 border border-red-200 transition-colors">
                                    <i class="fas fa-file-pdf mr-2 text-red-600"></i>
                                    PDF
                                </button> --}}

                                <button wire:click="{{ $onClose }}"
                                    class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-white/10 hover:bg-white/20 text-white transition-colors">
                                    <i class="fas fa-times text-lg"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Info -->
                    @if ($hasActiveFilters)
                        <div class="mt-3 pt-3 border-t border-white/20">
                            <div class="flex items-start space-x-3 text-sm">
                                <span class="font-semibold whitespace-nowrap pt-1 flex items-center">
                                    <i class="fas fa-filter mr-1"></i>
                                    Filter Aktif:
                                </span>
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($activeFilters as $label => $value)
                                        <span
                                            class="bg-white/20 px-3 py-1 rounded-full text-xs flex items-center space-x-1">
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
                                {{__('global.showwing')}}
                                @if ($previewMonth)
                                    data {{__('global.year')}} <strong>{{ $previewMonth }}</strong>
                                @else
                                    <strong>{{__('global.semua').' Data'}}</strong>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Main Content Area dengan Scroll -->
                <div class="flex-1 flex flex-col min-h-0">
                    <!-- Info Summary -->
                    <div class="bg-blue-50 border-b border-blue-200 px-6 py-3 shrink-0">
                        <div class="flex items-center justify-between text-sm">
                            <div class="flex items-center space-x-4 text-blue-700">
                                <span class="flex items-center">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    {{__('global.data_to_export')}}
                                </span>
                                @if ($hasActiveFilters)
                                    <span class="flex items-center">
                                        <i class="fas fa-filter mr-1"></i>
                                        {{ count($activeFilters) }}{{__('global.apply_filter')}}  
                                    </span>
                                @endif
                            </div>
                            <div class="text-blue-600">
                                <span x-text="`${$el.querySelectorAll('tbody tr:not([style*=\"display: none\"])').length} data tampil`"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Table Container dengan Scroll yang Bekerja -->
                    <div class="flex-1 overflow-auto">
                        <div class="min-w-full inline-block align-middle">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50 sticky top-0 z-10">
                                    <!-- Baris pertama header -->
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase border-r bg-gray-50 sticky left-0 z-20"
                                            rowspan="2">No</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase border-r bg-gray-50 sticky left-12 z-20"
                                            rowspan="2">{{__('table.columns.code')}}</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase border-r"
                                            rowspan="2">{{__('table.columns.estimasi_waktu')}}</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase border-r"
                                            rowspan="2">{{__('table.columns.tgl_aduan')}}</th>

                                        <!-- Identitas Pelapor - colspan 3 -->
                                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase border-r bg-blue-50"
                                            colspan="3">
                                           {{__('table.columns.pelapor_identitas')}}
                                        </th>

                                        <!-- Identitas Terlapor - colspan 2 -->
                                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase border-r bg-orange-50"
                                            colspan="2">
                                            {{__('table.columns.terlapor_identitas')}}
                                            
                                        </th>

                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase border-r"
                                            rowspan="2">Status</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase border-r"
                                            rowspan="2">{{__('global.jenis_pelanggaran')}}</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase border-r"
                                            rowspan="2">Uraian</th>
                                        {{-- <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase"
                                            rowspan="2">Admin</th> --}}
                                    </tr>

                                    <!-- Baris kedua header (sub-header) -->
                                    <tr>
                                        <!-- Sub-header untuk Identitas Pelapor -->
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase border-r bg-blue-50">
                                            {{__('table.columns.name')}}</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase border-r bg-blue-50">
                                            {{__('table.columns.no_hp')}}
                                            </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase border-r bg-blue-50">
                                            {{__('table.columns.cont_detail')}}</th>

                                        <!-- Sub-header untuk Identitas Terlapor -->
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase border-r bg-orange-50">
                                            {{__('table.columns.name')}}</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase border-r bg-orange-50">
                                            Direktorat</th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y divide-gray-200 bg-white">
                                    @forelse ($previewData as $index => $item)
                                        <tr class="hover:bg-blue-50 transition-colors group"
                                            x-show="search === '' || 
                                                    '{{ strtolower($item->code_pengaduan ?? '') }}'.includes(search.toLowerCase()) ||
                                                    '{{ strtolower($item->uraian ?? '') }}'.includes(search.toLowerCase()) ||
                                                    '{{ strtolower($this->getNamaUser($item)) }}'.includes(search.toLowerCase())">

                                            <!-- No - Sticky Column -->
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 border-r text-center bg-white sticky left-0 z-10 group-hover:bg-blue-50">
                                                {{ $index + 1 }}
                                            </td>

                                            <!-- Kode - Sticky Column -->
                                            <td class="px-4 py-3 whitespace-nowrap text-sm font-mono text-blue-600 border-r bg-white sticky left-12 z-10 group-hover:bg-blue-50">
                                                {{ $item->code_pengaduan ?? '-' }}
                                            </td>

                                            <!-- Waktu Kejadian -->
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 border-r">
                                                @if (isset($item->waktu_kejadian_mulai))
                                                    {{ \Carbon\Carbon::parse($item->waktu_kejadian_mulai)->format('d/m/Y') }}
                                                @else
                                                    -
                                                @endif
                                            </td>

                                            <!-- Tanggal Aduan -->
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 border-r">
                                                @if (isset($item->tanggal_pengaduan))
                                                    {{ \Carbon\Carbon::parse($item->tanggal_pengaduan)->format('d/m/Y') }}
                                                @else
                                                    -
                                                @endif
                                            </td>

                                            <!-- Identitas Pelapor -->
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 border-r bg-blue-50/30 group-hover:bg-blue-100">
                                                {{ $this->getNamaUser($item) }}
                                            </td>

                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 border-r bg-blue-50/30 group-hover:bg-blue-100">
                                                {{ $item->telepon_pelapor ?? ($item->pelapor->phone ?? ($item->user->phone ?? '-')) }}
                                            </td>

                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 border-r bg-blue-50/30 group-hover:bg-blue-100">
                                                {{ $item->alamat_kejadian ?? '-' }}
                                            </td>

                                            <!-- Identitas Terlapor -->
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 border-r bg-orange-50/30 group-hover:bg-orange-100">
                                                {{ $item->nama_terlapor ?? '-' }}
                                            </td>

                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 border-r bg-orange-50/30 group-hover:bg-orange-100">
                                                {{ $this->getDirektoratName($item->direktorat ?? $item->direktorat_terlapor) ?? '-' }}
                                            </td>

                                            <!-- Status -->
                                            <td class="px-4 py-3 whitespace-nowrap text-sm border-r">
                                                @php
                                                    $statusInfo = $this->getStatusInfo(
                                                        $item->status ?? 0,
                                                        $item->sts_final ?? 0,
                                                    );
                                                @endphp
                                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-{{ $statusInfo['color'] }}-100 text-{{ $statusInfo['color'] }}-800 border border-{{ $statusInfo['color'] }}-200">
                                                    <span class="w-2 h-2 bg-{{ $statusInfo['color'] }}-500 rounded-full mr-1.5"></span>
                                                    {{ $statusInfo['text'] }}
                                                </span>
                                            </td>

                                            <!-- Jenis -->
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 border-r">
                                                {{ $this->getJenisPelanggaran($item) }}
                                            </td>

                                            <!-- Uraian -->
                                            <td class="px-4 py-3 text-sm text-gray-900 border-r max-w-xs">
                                                <div class="space-y-1">
                                                    <div class="font-medium line-clamp-1 text-gray-900">{{ $item->uraian ?? '-' }}</div>
                                                     
                                                </div>
                                            </td>

                                            <!-- Admin -->
                                            {{-- <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                                {{ $item->admin->name ?? 'System' }}
                                            </td> --}}
                                        </tr>
                                    @empty
                                        <!-- Empty State untuk tidak ada data -->
                                        <tr>
                                            <td colspan="14" class="px-4 py-8 text-center">
                                                <div class="flex flex-col items-center justify-center text-gray-500">
                                                    <i class="fas fa-inbox text-4xl mb-3 text-gray-300"></i>
                                                    <p class="text-lg font-medium">{{__('table.no_data')}}</p>
                                                    {{-- <p class="text-sm mt-1">Tidak ada data yang sesuai dengan kriteria</p> --}}
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>

                            <!-- Empty Search State -->
                            <template x-if="search !== '' && $el.querySelectorAll('tbody tr[x-show\\:expression]').length === 0">
                                <div class="absolute inset-0 flex items-center justify-center bg-white/95 backdrop-blur-sm">
                                    <div class="text-center py-12">
                                        <i class="fas fa-search text-gray-400 text-4xl mb-4"></i>
                                        <p class="text-gray-500 text-lg font-medium">{{__('table.no_data')}}</p>
                                        {{-- <p class="text-gray-400 text-sm mt-2">Coba kata kunci lain</p> --}}
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                {{-- <div class="bg-gray-50 px-6 py-3 border-t border-gray-200 flex items-center justify-between shrink-0">
                    <div class="text-sm text-gray-600 flex items-center space-x-4">
                        <span x-text="`${$el.querySelectorAll('tbody tr:not([style*=\"display: none\"])').length} dari {{ $previewTotal }} records`"></span>
                        @if ($hasActiveFilters)
                            <span class="text-gray-500">• {{ count($activeFilters) }} filter aktif</span>
                        @endif
                    </div>
                    <div class="flex items-center space-x-3">
                        <button wire:click="{{ $onClose }}"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                            Tutup
                        </button>
                        @if ($previewTotal > 0)
                            <button wire:click="{{ $onExportExcel }}" wire:loading.attr="disabled"
                                class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700 transition-colors flex items-center space-x-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                <i class="fas fa-download"></i>
                                <span>Download Excel</span>
                            </button>
                        @endif
                    </div>
                </div> --}}
            </div>
        </div>
    </div>

    <style>
        /* Custom scrollbar styling */
        .flex-1.overflow-auto {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 #f1f5f9;
        }

        .flex-1.overflow-auto::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        .flex-1.overflow-auto::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
        }

        .flex-1.overflow-auto::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
            border: 2px solid #f1f5f9;
        }

        .flex-1.overflow-auto::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .flex-1.overflow-auto::-webkit-scrollbar-corner {
            background: #f1f5f9;
        }

        /* Sticky columns dengan improved styling */
        .sticky {
            position: sticky;
            backdrop-filter: blur(8px);
        }

        .sticky.left-0 {
            left: 0;
            box-shadow: 
                2px 0 0 #e5e7eb,
                inset -1px 0 0 #e5e7eb;
        }

        .sticky.left-12 {
            left: 48px;
            box-shadow: 
                2px 0 0 #e5e7eb,
                inset -1px 0 0 #e5e7eb;
        }

        /* Z-index hierarchy */
        .sticky.z-10 {
            z-index: 10;
        }

        .sticky.z-20 {
            z-index: 20;
        }

        /* Background consistency dengan backdrop */
        .bg-white.sticky {
            background-color: rgba(255, 255, 255, 0.95);
        }

        .bg-gray-50.sticky {
            background-color: rgba(249, 250, 251, 0.95);
        }

        /* Hover states untuk sticky columns */
        .group:hover .bg-white.sticky {
            background-color: rgba(219, 234, 254, 0.95);
        }

        /* Line clamp utilities */
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

        /* Flex utilities untuk layout yang tepat */
        .min-h-0 {
            min-height: 0;
        }

        .shrink-0 {
            flex-shrink: 0;
        }

        /* Smooth transitions */
        .transition-colors {
            transition-property: color, background-color, border-color, text-decoration-color, fill, stroke;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 150ms;
        }

        /* Focus states untuk accessibility */
        .focus\:outline-none:focus {
            outline: 2px solid transparent;
            outline-offset: 2px;
        }

        .focus\:ring-2:focus {
            --tw-ring-offset-shadow: var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);
            --tw-ring-shadow: var(--tw-ring-inset) 0 0 0 calc(2px + var(--tw-ring-offset-width)) var(--tw-ring-color);
            box-shadow: var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow, 0 0 #0000);
        }

        .focus\:ring-offset-2:focus {
            --tw-ring-offset-width: 2px;
        }
    </style>
@endif