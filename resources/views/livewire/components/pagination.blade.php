@props([
    'records' => [],
])
<div
            class="mt-6 flex flex-col sm:flex-row items-center justify-between px-4 py-3 bg-white border-t border-gray-200 rounded-b-lg gap-4 transition-all duration-300">


            <!-- Per Page Select -->


            <!-- Pagination Text -->
            <div class="text-sm text-gray-700">
                Showing {{ $records->firstItem() }} to {{ $records->lastItem() }} of {{ $records->total() }} results
            </div>

            <!-- Pagination Buttons -->
            <div class="flex space-x-2">
                <!-- Previous Button -->
                @if ($records->onFirstPage())
                    <span
                        class="px-4 py-2 text-sm text-gray-400 bg-gray-100 border border-gray-300 rounded cursor-not-allowed transition-all duration-300">

                        <i class="fas fa-arrow-left mr-1.5 text-xs"></i> Previous
                    </span>
                @else
                    <button wire:click="previousPage"
                        class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:z-10 focus:ring-1 focus:ring-[rgb(0,111,188)] focus:text-[rgb(0,111,188)] transition-all duration-200 transform hover:scale-[1.02] active:scale-95">
                        <i class="fas fa-arrow-left mr-1.5 text-xs"></i>
                        <span class="whitespace-nowrap">Previous</span>
                    </button>
                @endif

                <!-- Next Button -->
                @if ($records->hasMorePages())
                    <button wire:click="nextPage"
                        class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:z-10 focus:ring-1 focus:ring-[rgb(0,111,188)] focus:text-[rgb(0,111,188)] transition-all duration-200 transform hover:scale-[1.02] active:scale-95">
                        <span class="whitespace-nowrap">Next</span>
                        <i class="fas fa-arrow-right ml-1.5 text-xs"></i>
                    </button>
                @else
                    <span
                        class="px-4 py-2 text-sm text-gray-400 bg-gray-100 border border-gray-300 rounded cursor-not-allowed transition-all duration-300">
                        Next
                        <i class="fas fa-arrow-right ml-1.5 text-xs"></i>
                    </span>
                @endif
            </div>
        </div>