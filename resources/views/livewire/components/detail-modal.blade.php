@props([
    'show' => false,
    'title' => 'Detail Data',
    'data' => [],
    'onClose' => '',
])

@if($show)
<div class="fixed inset-0 z-50 overflow-y-auto animate-fade-in" style="background-color: rgba(0,0,0,0.5)">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="modal-content bg-white rounded-lg shadow-xl w-full max-w-2xl transform transition-all duration-300 scale-95 animate-scale-in">
            <!-- Header -->
            <div class="modal-header bg-gradient-to-r from-[rgb(0,111,188)] to-[rgb(0,95,160)] text-white rounded-t-lg px-6 py-5 shadow-lg">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="flex items-center justify-center w-10 h-10 bg-white/20 rounded-full">
                            <i class="fas fa-eye"></i>
                        </div>
                        <div>
                            <h5 class="modal-title text-xl font-bold tracking-tight">
                                {{ $title }}
                            </h5>
                            <p class="text-white/80 text-sm">
                                Detail informasi data
                            </p>
                        </div>
                    </div>
                    <button type="button" wire:click="{{ $onClose }}"
                        class="flex items-center justify-center w-9 h-9 rounded-full hover:bg-white/20 transition-all duration-300 hover:rotate-90">
                        <i class="fas fa-times text-base"></i>
                    </button>
                </div>
            </div>

            <!-- Body -->
            <div class="modal-body p-6">
                <div class="space-y-4">
                    @foreach($data as $label => $value)
                    <div class="flex flex-col sm:flex-row sm:items-center border-b border-gray-100 pb-3">
                        <div class="sm:w-1/3 mb-1 sm:mb-0">
                            <span class="font-semibold text-gray-700">{{ $label }}:</span>
                        </div>
                        <div class="sm:w-2/3">
                            @if($label === 'Status')
                                <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $value === 'Aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $value }}
                                </span>
                            @else
                                <span class="text-gray-900">{{ $value ?? '-' }}</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Footer -->
            <div class="modal-footer border-t border-gray-200 px-6 py-4 flex justify-end">
                <button type="button" wire:click="{{ $onClose }}"
                    class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-all duration-300 transform hover:scale-105 font-medium">
                    <i class="fas fa-times me-2"></i>Tutup
                </button>
            </div>
        </div>
    </div>
</div>
@endif

<style>
    .animate-fade-in {
        animation: fadeIn 0.3s ease-in-out;
    }
    
    .animate-scale-in {
        animation: scaleIn 0.3s ease-in-out;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    @keyframes scaleIn {
        from { transform: scale(0.95); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }
</style>