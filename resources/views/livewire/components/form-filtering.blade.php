<!-- resources/views/livewire/components/form-filtering.blade.php -->
@props([
    'showFilterModal' => false,
    'filters' => [],
    'onClose' => '',
    'onReset' => '',
    'onApply' => '',
    'title' => 'Filter Data'
])

@if ($showFilterModal)
    <div class="fixed inset-0 z-50 overflow-y-auto animate-fade-in" style="background-color: rgba(0,0,0,0.5)">
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="modal-content bg-white rounded-lg shadow-xl w-full max-w-md transform transition-all duration-300 scale-95 animate-scale-in">
                <div class="modal-header bg-[rgb(0,111,188)] text-white rounded-t-lg px-6 py-4">
                    <h5 class="modal-title text-lg font-semibold">
                        <i class="fas fa-filter me-2"></i>
                        {{ $title }}
                    </h5>
                    <button type="button" wire:click="{{ $onClose }}"
                        class="btn-close btn-close-white bg-transparent border-0 text-white text-xl hover:scale-110 transition-transform duration-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body p-6">
                    <div class="space-y-4">
                        @foreach($filters as $filter)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ $filter['label'] }}
                                </label>
                                
                                @if($filter['type'] === 'text')
                                    <input type="text" 
                                           wire:model="{{ $filter['model'] }}"
                                           class="w-full rounded-lg border p-2 border-gray-300 bg-white text-gray-900 focus:border-[rgb(0,111,188)] focus:ring-[rgb(0,111,188)] shadow-sm transition-all duration-300"
                                           placeholder="{{ $filter['placeholder'] ?? '' }}">
                                
                                @elseif($filter['type'] === 'select')
                                    <select wire:model="{{ $filter['model'] }}"
                                            class="w-full rounded-lg border p-2 border-gray-300 bg-white text-gray-900 focus:border-[rgb(0,111,188)] focus:ring-[rgb(0,111,188)] shadow-sm transition-all duration-300">
                                        <option value="">{{ $filter['placeholder'] ?? 'Semua' }}</option>
                                        @foreach($filter['options'] ?? [] as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                
                                @elseif($filter['type'] === 'date')
                                    <input type="date" 
                                           wire:model="{{ $filter['model'] }}"
                                           class="w-full rounded-lg border p-2 border-gray-300 bg-white text-gray-900 focus:border-[rgb(0,111,188)] focus:ring-[rgb(0,111,188)] shadow-sm transition-all duration-300">
                                
                                @elseif($filter['type'] === 'daterange')
                                    <div class="flex gap-2">
                                        <input type="date" 
                                               wire:model="{{ $filter['model'] }}.start"
                                               class="flex-1 rounded-lg border p-2 border-gray-300 bg-white text-gray-900 focus:border-[rgb(0,111,188)] focus:ring-[rgb(0,111,188)] shadow-sm transition-all duration-300"
                                               placeholder="Dari">
                                        <input type="date" 
                                               wire:model="{{ $filter['model'] }}.end"
                                               class="flex-1 rounded-lg border p-2 border-gray-300 bg-white text-gray-900 focus:border-[rgb(0,111,188)] focus:ring-[rgb(0,111,188)] shadow-sm transition-all duration-300"
                                               placeholder="Sampai">
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer border-t border-gray-200 px-6 py-4 flex justify-end gap-3">
                    <button type="button" wire:click="{{ $onReset }}"
                        class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-all duration-300 transform hover:scale-105 font-medium">
                        <i class="fas fa-refresh me-2"></i>Reset
                    </button>
                    <button type="button" wire:click="{{ $onApply }}"
                        class="px-4 py-2 text-white bg-[rgb(0,111,188)] rounded-lg hover:bg-[rgb(0,95,160)] transition-all duration-300 transform hover:scale-105 font-medium">
                        <i class="fas fa-check me-2"></i>Terapkan
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif