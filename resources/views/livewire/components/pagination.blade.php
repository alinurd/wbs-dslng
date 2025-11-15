@props([
    'records' => [],
])

<!-- Pagination -->
@if($records->hasPages())
<div class="bg-gray-50 border-t border-gray-200 px-6 py-4">
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <!-- Pagination Info -->
        <div class="text-sm text-gray-700">
            Menampilkan 
            <span class="font-medium">{{ $records->firstItem() }}</span>
            sampai 
            <span class="font-medium">{{ $records->lastItem() }}</span>
            dari 
            <span class="font-medium">{{ $records->total() }}</span>
            hasil
        </div>

        <!-- Pagination Links -->
        <div class="flex items-center space-x-1">
            <!-- Previous Button -->
            @if($records->onFirstPage())
                <span class="px-3 py-1 text-xs text-gray-400 bg-white border border-gray-300 rounded-lg cursor-not-allowed">
                    &laquo;
                </span>
            @else
                <button wire:click="previousPage" 
                        class="px-3 py-1 text-xs text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    &laquo;
                </button>
            @endif

            <!-- Page Numbers -->
            @foreach($records->links()->elements[0] as $page => $url)
                @if($page == $records->currentPage())
                    <span class="px-3 py-1 text-xs text-white bg-blue-600 border border-blue-600 rounded-lg font-medium">
                        {{ $page }}
                    </span>
                @else
                    <button wire:click="gotoPage({{ $page }})" 
                            class="px-3 py-1 text-xs text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        {{ $page }}
                    </button>
                @endif
            @endforeach

            <!-- Next Button -->
            @if($records->hasMorePages())
                <button wire:click="nextPage" 
                        class="px-3 py-1 text-xs text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    &raquo;
                </button>
            @else
                <span class="px-3 py-1 text-xs text-gray-400 bg-white border border-gray-300 rounded-lg cursor-not-allowed">
                    &raquo;
                </span>
            @endif
        </div>
    </div>
</div>

@endif