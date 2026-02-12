@props([
    'perPage' => 10,
    'search' => '',
    'filterMode' => false,
    'onPerPageChange' => 'perPage',
    'onSearch' => 'search',
    'onOpenFilter' => 'openFilter',
    'onResetFilter' => 'resetFilter'
])

<div class="flex items-center gap-2">
    <div class="flex-1 relative">
        <div class="w-full ">
            <div class="flex items-center">
                <span class="text-gray-700 me-2 text-sm">{{__('table.show')}} :</span>
                <select wire:model.live="{{ $onPerPageChange }}"
                    class="rounded-lg border border-gray-300 bg-white text-gray-900 focus:border-[rgb(0,111,188)] focus:ring-[rgb(0,111,188)] shadow-sm transition-all duration-300 text-xs 
                    ">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="10000">{{__('table.all')}}</option>
                </select>
                <span class="text-gray-700 ms-2 text-sm">{{__('table.entries')}}</span>
            </div>
        </div>
    </div>

    <div class="flex items-center gap-1">
        <div class="flex-1 relative">
            <input type="text" wire:model.live="{{ $onSearch }}"
                class="pl-9 pr-3 py-1.5 rounded-md border border-gray-300 bg-white text-gray-900 focus:border-[rgb(0,111,188)] focus:ring-1 focus:ring-[rgb(0,111,188)] shadow-sm transition-all duration-200 text-xs"
                placeholder="{{__('table.search')}} ...">
            <div class="absolute inset-y-0 left-0 flex items-center pl-2.5">
                <i class="fas fa-search h-3.5 w-3.5 text-gray-400"></i>
            </div>
        </div>

        <button wire:click="{{ $onOpenFilter }}"
            class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:z-10 focus:ring-1 focus:ring-[rgb(0,111,188)] focus:text-[rgb(0,111,188)] transition-all duration-200 transform hover:scale-[1.02] active:scale-95">
            <i class="fas fa-filter mr-1.5 text-xs"></i>
            <span class="whitespace-nowrap">Filter</span>
        </button>
        @if($filterMode)
        <button wire:click="{{ $onResetFilter }}"
                class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:z-10 focus:ring-1 focus:ring-gray-400 focus:text-gray-800 transition-all duration-200 transform hover:scale-[1.02] active:scale-95">
                <i class="fas fa-refresh mr-1.5 text-xs"></i>
                <span class="whitespace-nowrap">Reset</span>
            </button>
              @endif 
    </div>
</div>