@props([
    'showPreviewModal' => false,
    'previewMonth' => '',
    'previewTotal' => 0,
    'previewData' => [],
    'filterData' => [],
    'title' => 'Preview Export Data',
    'onClose' => '$set(\'showPreviewModal\', false)',
    'onExportExcel' => 'export(\'excelReportJenis\')',
    'onExportPdf' => 'export(\'pdf\')',
])

@if ($showPreviewModal)
    {{-- {{dd($previewData)}} --}}
    @php
        $activeFilters = !empty($filterData) ? array_filter($filterData) : [];
        $hasActiveFilters = count($activeFilters) > 0;
    @endphp

    <div class="fixed inset-0 z-50 overflow-hidden" x-data="{ search: '' }">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity"></div>

        <!-- Modal Panel -->
        <div class="flex min-h-full items-end justify-center p-0 text-center sm:items-center sm:p-4">
            <div
                class="relative transform overflow-hidden rounded-lg bg-white shadow-xl transition-all w-full h-full max-h-[95vh] sm:max-w-[95vw] flex flex-col">

                <!-- Header -->
                <div class="bg-gradient-to-r from-[rgb(0,111,188)] to-[rgb(0,95,160)] text-white px-6 py-4 shrink-0">
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

                                <button wire:click="{{ $onExportPdf }}" wire:loading.attr="disabled"
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
                                Menampilkan
                                @if ($previewMonth)
                                    data periode <strong>{{ $previewMonth }}</strong>
                                @else
                                    <strong>semua data</strong>
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
                                    Data yang akan di-export
                                </span>
                                @if ($hasActiveFilters)
                                    <span class="flex items-center">
                                        <i class="fas fa-filter mr-1"></i>
                                        {{ count($activeFilters) }} filter diterapkan
                                    </span>
                                @endif
                            </div>
                            <div class="text-blue-600">
                                <span
                                    x-text="`${$el.querySelectorAll('tbody tr:not([style*=\"display: none\"])').length} data tampil`"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Table Container dengan Scroll yang Bekerja -->
                    <div class="flex-1 overflow-auto p-6 bg-white"> <!-- p-6 biar ada ruang sekitar table -->
 <table class="border border-gray-400 text-[11px] border-collapse w-full">

    <thead>
        <!-- TITLE -->
        <tr>
            <th colspan="34"
                class="border border-gray-400 text-center font-bold py-2 text-lg bg-[rgb(13,167,217)] text-white">
                BERDASARKAN JENIS PELANGGARAN
            </th>
        </tr>

        <!-- DATE HEADER -->
        <tr class="bg-gray-100 font-semibold">
            <th class="border border-gray-400 text-center w-10">No</th>
            <th class="border border-gray-400 text-left px-2 text-[15px] whitespace-nowrap">Jenis Pelanggaran</th>

            @for ($i = 1; $i <= 31; $i++)
                <th class="border border-gray-400 text-center text-[15px] w-[32px] px-1 py-1">{{ $i }}</th>
            @endfor

            <th class="border border-gray-400 text-center w-16 text-[15px]">Jumlah</th>
        </tr>

    </thead>

    <tbody>
        @foreach ($previewData['dataRekap'] as $index => $data)
            <tr>
                <td class="border border-gray-400 text-center text-[15px] py-1 w-10">{{ $index + 1 }}</td>
                <td class="border border-gray-400 text-left px-2 text-[15px] whitespace-nowrap">{{ $data['nama_jenis'] }}</td>

                @for ($d = 1; $d <= 31; $d++)
                    <td class="border border-gray-400 text-center text-[15px] py-1 w-[32px]">
                        {{ $data['detail_harian'][$d] ?? 0 }}
                    </td>
                @endfor

                <td class="border border-gray-400 text-center text-[15px] font-bold text-green-700 w-16 py-1">
                    {{ $data['total'] ?? 0 }}
                </td>
            </tr>
        @endforeach
    </tbody>

    <tfoot>
        <tr class="bg-gray-100 text-center font-bold">
            <td class="border border-gray-400 py-2 text-[15px]" colspan="2">Jumlah</td>

            @for ($d = 1; $d <= 31; $d++)
                <td class="border border-gray-400 text-[15px] py-2 w-[32px]">
                    {{ collect($previewData['dataRekap'])->sum(fn($x) => $x['detail_harian'][$d] ?? 0) }}
                </td>
            @endfor

            <td class="border border-gray-400 py-2 text-blue-800 text-[15px]">
                {{ collect($previewData['dataRekap'])->sum('total') }}
            </td>
        </tr>
    </tfoot>

</table> 
</div>

                </div>
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
