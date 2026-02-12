@props([
    // Data
    'records' => null,
    'columns' => [],
    'selectedItems' => [],
    'permissions' => [],
    
    // State
    'perPage' => 10,
    'search' => '',
    'filterMode' => false,
    'firstItem' => 1,
    
    // Actions - SEMUA ACTION HARUS ADA
    'onCreate' => '',
    'onExportExcel' => '',
    'onExportPdf' => '',
    'onDeleteBulk' => '',
    'onPerPageChange' => '',
    'onSearch' => '',
    'onOpenFilter' => '',
    'onResetFilter' => '',
    'onSort' => '',
    'onView' => '',
    'onComment' => '',
    'onEdit' => '',
    'onDelete' => '',
    'onSelectItem' => '',
])
            
<div class="w-full space-y-4">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 p-4 bg-white rounded-lg shadow-sm border">
        <!-- Action Bar - Left -->
        <div class="flex items-center gap-2 flex-wrap">
            @include('livewire.components.action-bar', [
                'permissions' => $permissions,
                'selectedItems' => $selectedItems,
                'onCreate' =>$onCreate,
                'onExportExcel' => $onExportExcel,
                'onExportPdf' => $onExportPdf,
                'onDeleteBulk' => $onDeleteBulk,
            ])
        </div>

        <!-- Search & Filter - Right -->
        <div class="flex items-center gap-3 flex-1 justify-end min-w-0">
            @include('livewire.components.search-filter', [
                'perPage' => $perPage,
                'search' => $search,
                'filterMode' => $filterMode,
                'onPerPageChange' => $onPerPageChange,
                'onSearch' => $onSearch,
                'onOpenFilter' => $onOpenFilter,
                'onResetFilter' => $onResetFilter,
            ])
        </div>
    </div>

    <!-- Active Filters Indicator -->
    {{-- @if($filterMode)
    <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2 flex-wrap">
                <i class="fas fa-filter text-blue-600"></i>
                <span class="text-sm font-medium text-blue-800">Filter aktif:</span>
                @if($filters['data_id'] ?? '')
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    Data ID: {{ $filters['data_id'] }}
                </span>
                @endif
                @if($filters['data_en'] ?? '')
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    Data EN: {{ $filters['data_en'] }}
                </span>
                @endif
                @if(($filters['is_active'] ?? '') !== '')
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    Status: {{ $filters['is_active'] ? 'Aktif' : 'Nonaktif' }}
                </span>
                @endif
                <button wire:click="{{ $onResetFilter }}"
                class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:z-10 focus:ring-1 focus:ring-gray-400 focus:text-gray-800 transition-all duration-200 transform hover:scale-[1.02] active:scale-95">
                <i class="fas fa-refresh mr-1.5 text-xs"></i>
                <span class="whitespace-nowrap">Reset</span>
            </button>
            
            </div>
        </div>
    </div>
    @endif --}}
    <!-- Table Section -->
    @if($records && $records->count() > 0)
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        @include('livewire.components.table', [
            'records' => $records,
            'selectedItems' => $selectedItems,
            'permissions' => $permissions,
            'columns' => $columns,
            'onSort' => $onSort,
            'onView' => $onView,
            'onComment' => $onComment,
            'onEdit' => $onEdit,
            'onDelete' => $onDelete,
            'onSelectItem' => $onSelectItem,
            'firstItem' => $firstItem,
        ])
    </div>
    @else
    <!-- Empty State -->
    <div class="text-center py-12 bg-white rounded-lg border">
        <i class="fas fa-inbox text-gray-400 text-4xl mb-4"></i>
         <p class="text-gray-500 mb-4">
            @if($filterMode)
            {{__('table.no_data')}}
                {{-- Tidak ada data yang ditemukan untuk filter saat ini. --}}
            @else
            {{__('table.no_data')}}
                {{-- Belum ada data yang ditambahkan. --}}
            @endif
        </p>
       
    </div>
    @endif
</div>