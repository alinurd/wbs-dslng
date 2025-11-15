<!-- resources/views/livewire/components/form.blade.php -->
@props([
    'showModal' => false,
    'updateMode' => false,
    'form' => [],
    'onClose' => '',
    'onSave' => '',
    'title' => 'Form Data',
    'fields' => []
])

@if ($showModal)
    <div class="fixed inset-0 z-50 overflow-y-auto animate-fade-in" style="background-color: rgba(0,0,0,0.5)">
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="modal-content bg-white rounded-lg shadow-xl w-full max-w-2xl transform transition-all duration-300 scale-95 animate-scale-in">
                <div class="modal-header bg-[rgb(0,111,188)] text-white rounded-t-lg px-6 py-4">
                    <h5 class="modal-title text-lg font-semibold">
                        <i class="fas {{ $updateMode ? 'fa-edit' : 'fa-plus' }} me-2"></i>
                        {{ $updateMode ? 'Edit ' . $title : 'Tambah ' . $title }}
                    </h5>
                    <button type="button" wire:click="{{ $onClose }}"
                        class="btn-close btn-close-white bg-transparent border-0 text-white text-xl hover:scale-110 transition-transform duration-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form wire:submit="{{ $onSave }}">
                    <div class="modal-body p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($fields as $field)
                                <div class="mb-3">
                                    <label class="form-label font-medium text-gray-700">
                                        {{ $field['label'] }} 
                                        @if($field['required'] ?? false)
                                            <span class="text-red-500">*</span>
                                        @endif
                                    </label>
                                    
                                    @if($field['type'] === 'text' || $field['type'] === 'number')
                                        <input type="{{ $field['type'] }}" 
                                               wire:model.defer="{{ $field['model'] }}"
                                               class="w-full rounded-lg border p-2 border-gray-300 bg-white text-gray-900 focus:border-[rgb(0,111,188)] focus:ring-[rgb(0,111,188)] shadow-sm transition-all duration-300 @error($field['error']) border-red-500 @enderror"
                                               placeholder="{{ $field['placeholder'] ?? '' }}">
                                        @error($field['error'])
                                            <div class="text-red-600 text-sm mt-1 animate-shake">{{ $message }}</div>
                                        @enderror
                                    
                                    @elseif($field['type'] === 'select')
                                        <select wire:model.defer="{{ $field['model'] }}"
                                                class="w-full rounded-lg border p-2 border-gray-300 bg-white text-gray-900 focus:border-[rgb(0,111,188)] focus:ring-[rgb(0,111,188)] shadow-sm transition-all duration-300 @error($field['error']) border-red-500 @enderror">
                                            <option value="">{{ $field['placeholder'] ?? 'Pilih...' }}</option>
                                            @foreach($field['options'] ?? [] as $value => $label)
                                                <option value="{{ $value }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error($field['error'])
                                            <div class="text-red-600 text-sm mt-1 animate-shake">{{ $message }}</div>
                                        @enderror
                                    
                                    @elseif($field['type'] === 'textarea')
                                        <textarea wire:model.defer="{{ $field['model'] }}"
                                                  rows="{{ $field['rows'] ?? 3 }}"
                                                  class="w-full rounded-lg border p-2 border-gray-300 bg-white text-gray-900 focus:border-[rgb(0,111,188)] focus:ring-[rgb(0,111,188)] shadow-sm transition-all duration-300 @error($field['error']) border-red-500 @enderror"
                                                  placeholder="{{ $field['placeholder'] ?? '' }}"></textarea>
                                        @error($field['error'])
                                            <div class="text-red-600 text-sm mt-1 animate-shake">{{ $message }}</div>
                                        @enderror
                                    
                                    @elseif($field['type'] === 'checkbox')
                                        <div class="flex items-center">
                                            <input type="checkbox" 
                                                   wire:model.defer="{{ $field['model'] }}"
                                                   class="h-5 w-5 rounded border-gray-300 text-[rgb(0,111,188)] focus:ring-[rgb(0,111,188)] transition-all duration-300"
                                                   id="{{ $field['model'] }}">
                                            <label class="form-check-label font-medium text-gray-700 ms-3" for="{{ $field['model'] }}">
                                                {{ $field['label'] }}
                                            </label>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer border-t border-gray-200 px-6 py-4 flex justify-end gap-3">
                        <button type="button" wire:click="{{ $onClose }}"
                            class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-all duration-300 transform hover:scale-105 font-medium">
                            <i class="fas fa-times me-2"></i>Batal
                        </button>
                        <button type="submit"
                            class="px-4 py-2 text-white bg-[rgb(0,111,188)] rounded-lg hover:bg-[rgb(0,95,160)] transition-all duration-300 transform hover:scale-105 font-medium">
                            <i class="fas {{ $updateMode ? 'fa-save' : 'fa-plus' }} me-2"></i>
                            {{ $updateMode ? 'Update' : 'Simpan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif