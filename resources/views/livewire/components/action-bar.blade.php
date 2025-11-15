<!-- components/action-bar.blade.php -->
@props([
    'permissions' => [],
    'selectedItems' => [],
    'onCreate' => '',
    'onExportExcel' => '',
    'onExportPdf' => '',
    'onDeleteBulk' => ''
])

<div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
    @if ($permissions['create'] ?? false)
        <button wire:click="{{ $onCreate }}"
            class="inline-flex items-center px-3 py-2 text-xs font-medium text-white bg-[rgb(0,111,188)] border border-[rgb(0,111,188)] rounded-md hover:bg-[rgb(0,95,160)] focus:z-10 focus:ring-1 focus:ring-[rgb(0,111,188)] focus:text-white transition-all duration-200 transform hover:scale-[1.02] active:scale-95">
            <i class="fas fa-plus mr-1.5 text-xs"></i>
            <span class="whitespace-nowrap">Tambah Data</span>
        </button>
    @endif
    
    <div class="flex items-center gap-1.5">
        <button wire:click="{{ $onExportExcel }}"
            class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-white bg-green-600 border border-green-600 rounded-md hover:bg-green-700 focus:z-10 focus:ring-1 focus:ring-green-600 focus:text-white transition-all duration-200 transform hover:scale-[1.02] active:scale-95">
            <i class="fas fa-file-excel mr-1.5 text-xs"></i>
            <span class="whitespace-nowrap">Excel</span>
        </button>

        <button wire:click="{{ $onExportPdf }}"
            class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-white bg-red-600 border border-red-600 rounded-md hover:bg-red-700 focus:z-10 focus:ring-1 focus:ring-red-600 focus:text-white transition-all duration-200 transform hover:scale-[1.02] active:scale-95">
            <i class="fas fa-file-pdf mr-1.5 text-xs"></i>
            <span class="whitespace-nowrap">PDF</span>
        </button>
    </div>

    <!-- Bulk Action -->
    <div class="flex items-center gap-2 transition-all duration-300 {{ empty($selectedItems) ? 'opacity-0 invisible w-0' : 'opacity-100 visible w-auto' }}">
        @if ($permissions['delete'] ?? false && !empty($selectedItems))
            <button wire:click="{{ $onDeleteBulk }}"
                wire:confirm="Apakah Anda yakin menghapus {{ count($selectedItems) }} data yang dipilih?"
                class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-white bg-red-600 border border-red-600 rounded-md hover:bg-red-700 focus:z-10 focus:ring-1 focus:ring-red-600 focus:text-white transition-all duration-200 transform hover:scale-[1.02] active:scale-95">
                <i class="fas fa-trash mr-1.5 text-xs"></i>
                <span class="whitespace-nowrap">Hapus {{ count($selectedItems) }} Data</span>
            </button>
        @endif
    </div>
</div>