<div x-data="{ open: false }" class="relative">
    <button @click="open = !open"
        class="flex items-center gap-2 px-3 py-2 text-sm rounded-md hover:bg-gray-100">
       {{ __('global.language') }}  &ensp;{{ $locale==='id'? '🇮🇩':'🇺🇸' }} 
        <i class="fas fa-chevron-down text-xs"></i>
    </button>

    <div x-show="open" @click.away="open = false"
        class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border py-2 z-50">

         <button wire:click="change('id')"
                            @click="open = false"
                            class="w-full text-left px-4 py-2 flex items-center gap-2 text-sm rounded-md
                                {{ $locale === 'id' ? 'text-blue-600 font-semibold bg-blue-50' : 'text-gray-700 hover:bg-gray-100' }}">
                            🇮🇩 Indonesia
                            @if ($locale === 'id')
                                <i class="fas fa-check ml-auto text-blue-600"></i>
                            @endif
                        </button>

        

        <button wire:click="change('en')"
        @click="open = false"
                            class="w-full text-left px-4 py-2 flex items-center gap-2 text-sm rounded-md
                                {{ $locale === 'en' ? 'text-blue-600 font-semibold bg-blue-50' : 'text-gray-700 hover:bg-gray-100' }}">
                            🇺🇸 English
                            @if ($locale === 'en')
                                <i class="fas fa-check ml-auto text-blue-600"></i>
                            @endif
        </button>
    </div>
</div>
