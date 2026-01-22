<!-- components/action-bar.blade.php -->
@props([
    'permissions' => [],
    'selectedItems' => [],
    'onCreate' => '',
    'onExportExcel' => '',
    'onExportPdf' => '',
    'onPreview' => '',
    'onDeleteBulk' => '',
])
@php
    if ($modul == 'p_tracking') {
        $permissions['delete'] = true;
    }
    if ($modul == 'complien') {
        $permissions['delete'] = false;
    }
    if ($modul == 'r_full') {
        $permissions['create'] = false;
        $permissions['preview'] = true;
        $onPreview='export("preview")';
        $onExportExcel='export("excelReportFull")';

    }
    if ($modul == 'r_jenis') {
        $permissions['create'] = false;
        $permissions['preview'] = true;
        $onExportExcel='export("excelReportJenis")';
        $onPreview='export("previewJenis")';
    }
    if($modul == 'complien'){
        $permissions['create'] = false;
        $permissions['preview'] = true;
        $permissions['showHistory'] = true;
        $onPreview='export("preview")';
        $onExportExcel='export("excelReportComplien")';
    }
@endphp

<div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
    @if ($permissions['create'] ?? false)
        @if ($modul == 'p_tracking')
            <a href="{{ route('p_report') }}"
                class="inline-flex items-center px-3 py-2 text-xs font-medium text-white bg-[rgb(0,111,188)] border border-[rgb(0,111,188)] rounded-md hover:bg-[rgb(0,95,160)] focus:z-10 focus:ring-1 focus:ring-[rgb(0,111,188)] focus:text-white transition-all duration-200 transform hover:scale-[1.02] active:scale-95">
                <i class="fas fa-plus mr-1.5 text-xs"></i>
                <span class="whitespace-nowrap">Buat Pengaduan</span>
            </a>
        @else
            <button wire:click="{{ $onCreate }}"
                class="inline-flex items-center px-3 py-2 text-xs font-medium text-white bg-[rgb(0,111,188)] border border-[rgb(0,111,188)] rounded-md hover:bg-[rgb(0,95,160)] focus:z-10 focus:ring-1 focus:ring-[rgb(0,111,188)] focus:text-white transition-all duration-200 transform hover:scale-[1.02] active:scale-95">
                <i class="fas fa-plus mr-1.5 text-xs"></i>
                <span class="whitespace-nowrap">Tambah Data</span>
            </button>
        @endif
    @endif

    <div class="flex items-center gap-1.5">
    <!-- Excel Button -->
    
    @if ($permissions['preview'] ?? false)
    <button wire:click="{{$onExportExcel}}" 
            wire:loading.attr="disabled"
            wire:loading.class="opacity-50 cursor-not-allowed"
            class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-white bg-green-600 border border-green-600 rounded-md hover:bg-green-700 focus:z-10 focus:ring-1 focus:ring-green-600 focus:text-white transition-all duration-200 transform hover:scale-[1.02] active:scale-95">
        <span wire:loading wire:target="export" class="inline-flex items-center">
            <i class="fas fa-spinner fa-spin mr-1"></i>
        </span>
        <span class="whitespace-nowrap inline-flex items-center">
            <span wire:loading.remove wire:target="export">
                <i class="fas fa-file-excel mr-1.5 text-xs"></i>
            </span>
            Excel
        </span>
    </button>

    <!-- PDF Button -->
    {{-- <button wire:click="export('pdf')" 
            wire:loading.attr="disabled"
            wire:loading.class="opacity-50 cursor-not-allowed"
            class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-white bg-red-600 border border-red-600 rounded-md hover:bg-red-700 focus:z-10 focus:ring-1 focus:ring-red-600 focus:text-white transition-all duration-200 transform hover:scale-[1.02] active:scale-95">
        <span wire:loading wire:target="export" class="inline-flex items-center">
            <i class="fas fa-spinner fa-spin mr-1"></i>
        </span>
        <span class="whitespace-nowrap inline-flex items-center">
            <span wire:loading.remove wire:target="export">
                <i class="fas fa-file-pdf mr-1.5 text-xs"></i>
            </span>
            PDF
        </span>
    </button> --}}

    @if($modul !== 'complien')
    <!-- Preview Button -->
    <button wire:click="{{$onPreview}}" 
            wire:loading.attr="disabled"
            wire:loading.class="opacity-50 cursor-not-allowed"
            class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-white bg-yellow-600 border border-yellow-600 rounded-md hover:bg-yellow-700 focus:z-10 focus:ring-1 focus:ring-yellow-600 focus:text-white transition-all duration-200 transform hover:scale-[1.02] active:scale-95">
        <span wire:loading wire:target="export" class="inline-flex items-center">
            <i class="fas fa-spinner fa-spin mr-1"></i>
        </span>
        <span class="whitespace-nowrap inline-flex items-center">
            <span wire:loading.remove wire:target="export">
                <i class="fas fa-eye mr-1.5 text-xs"></i>
            </span>
            Preview
        </span>
    </button>
    @endif
    @endif
    @if ($permissions['showHistory'] ?? false)

    @if($modul == 'complien' && $showHistory)
<button wire:click="history(false)" 
            wire:loading.attr="disabled"
            wire:loading.class="opacity-50 cursor-not-allowed"
            class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-white bg-gray-600 border border-bg-gray-600 rounded-md hover:bg-gray-700 focus:z-10 focus:ring-1 focus:ring-bg-gray-600 focus:text-white transition-all duration-200 transform hover:scale-[1.02] active:scale-95">
        <span wire:loading wire:target="history" class="inline-flex items-center">
            <i class="fas fa-spinner fa-spin mr-1"></i>
        </span>
        <span class="whitespace-nowrap inline-flex items-center">
            <span wire:loading.remove wire:target="history">
                <i class="fas fa-history mr-1.5 text-xs"></i>
            </span>
            Reset History
        </span>
    </button> 
    @else
      <button wire:click="history(true)" 
            wire:loading.attr="disabled"
            wire:loading.class="opacity-50 cursor-not-allowed"
            class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-white bg-cyan-600 border border-bg-cyan-600 rounded-md hover:bg-cyan-700 focus:z-10 focus:ring-1 focus:ring-bg-cyan-600 focus:text-white transition-all duration-200 transform hover:scale-[1.02] active:scale-95">
        <span wire:loading wire:target="history" class="inline-flex items-center">
            <i class="fas fa-spinner fa-spin mr-1"></i>
        </span>
        <span class="whitespace-nowrap inline-flex items-center">
            <span wire:loading.remove wire:target="history">
                <i class="fas fa-history mr-1.5 text-xs"></i>
            </span>
            History
        </span>
    </button>  
    @endif
    @endif
</div>

    <!-- Bulk Action -->
    <div
        class="flex items-center gap-2 transition-all duration-300 {{ empty($selectedItems) ? 'opacity-0 invisible w-0' : 'opacity-100 visible w-auto' }}">
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
