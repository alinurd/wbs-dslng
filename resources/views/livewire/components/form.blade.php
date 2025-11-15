@props([
    'showModal' => false,
    'updateMode' => false,
    'form' => [],
    'onClose' => '',
    'onSave' => '',
    'title' => 'Form Data',
    'size' => 'md',
    'cols' => 2,
    'fields' => [],
])

@if ($showModal)
    <div class="fixed inset-0 z-50 overflow-y-auto animate-fade-in" style="background-color: rgba(0,0,0,0.5)">
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="modal-content bg-white rounded-lg shadow-xl w-full max-w-2xl transform transition-all duration-300 scale-95 animate-scale-in">
                <!-- Header -->
                <div class="modal-header bg-gradient-to-r from-[rgb(0,111,188)] to-[rgb(0,95,160)] text-white rounded-t-lg px-6 py-5 shadow-lg">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="flex items-center justify-center w-10 h-10 bg-white/20 rounded-full">
                                <i class="fas {{ $updateMode ? 'fa-edit' : 'fa-plus' }}"></i>
                            </div>
                            <div>
                                <h5 class="modal-title text-xl font-bold tracking-tight">
                                    {{ $updateMode ? 'Edit ' . $title : 'Tambah ' . $title }}
                                </h5>
                                <p class="text-white/80 text-sm">
                                    {{ $updateMode ? 'Perbarui data yang sudah ada' : 'Isi form untuk menambah data baru' }}
                                </p>
                            </div>
                        </div>
                        <button type="button" wire:click="{{ $onClose }}"
                            class="flex items-center justify-center w-9 h-9 rounded-full hover:bg-white/20 transition-all duration-300 hover:rotate-90">
                            <i class="fas fa-times text-base"></i>
                        </button>
                    </div>
                </div>

                <form wire:submit="{{ $onSave }}">
                    <div class="modal-body p-6">
                        <div class="grid grid-cols-1 md:grid-cols-{{ $cols }} gap-4">
                            @foreach($fields as $field)
                                @php
                                    $colSpan = $field['colspan'] ?? 1;
                                    $fieldClass = $colSpan == 2 ? 'md:col-span-2' : '';
                                    
                                    // Get field value from form array
                                    $fieldName = str_replace('form.', '', $field['model']);
                                    $fieldValue = $form[$fieldName] ?? null;
                                @endphp
                                
                                <div class="mb-3 {{ $fieldClass }}">
                                    <div class="flex items-center justify-between mb-2">
                                        <label class="form-label font-medium text-gray-700">
                                            {{ $field['label'] }} 
                                            @if($field['required'] ?? false)
                                                <span class="text-red-500">*</span>
                                            @endif
                                        </label>
                                        
                                        <!-- Helper dengan icon informasi -->
                                        @if($field['helper'] ?? false)
                                            <div class="relative group">
                                                <button type="button" class="text-gray-400 hover:text-[rgb(0,111,188)] transition-colors duration-200">
                                                    <i class="fas fa-question-circle text-sm"></i>
                                                </button>
                                                <div class="absolute bottom-full right-0 mb-2 hidden group-hover:block z-10">
                                                    <div class="bg-gray-800 text-white text-xs rounded py-2 px-3 whitespace-nowrap shadow-lg max-w-xs">
                                                        {{ $field['helper'] }}
                                                        <div class="absolute top-full right-2 -mt-1 border-4 border-transparent border-t-gray-800"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- SWITCH TOGGLE MULTI OPTIONS -->
                                    @if($field['type'] === 'switch')
                                        <div class="flex items-center space-x-6">
                                            @foreach($field['options'] ?? [] as $value => $label)
                                                <label class="flex items-center cursor-pointer">
                                                    <div class="relative">
                                                        <input type="radio" 
                                                               wire:model.defer="{{ $field['model'] }}"
                                                               value="{{ $value }}"
                                                               class="sr-only"
                                                               {{ $field['disabled'] ?? false ? 'disabled' : '' }}>
                                                        <div class="block bg-gray-300 w-12 h-6 rounded-full transition-all duration-300 
                                                            {{ $fieldValue == $value ? 'bg-[rgb(0,111,188)]' : 'bg-gray-300' }}"></div>
                                                        <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-all duration-300 shadow-sm
                                                            {{ $fieldValue == $value ? 'transform translate-x-6' : '' }}"></div>
                                                    </div>
                                                    <div class="ml-3 text-sm font-medium text-gray-700 {{ $fieldValue == $value ? 'text-[rgb(0,111,188)] font-semibold' : '' }}">
                                                        {{ $label }}
                                                    </div>
                                                </label>
                                            @endforeach
                                        </div>
                                        @error($field['error'])
                                            <div class="text-red-600 text-sm mt-1 animate-shake">{{ $message }}</div>
                                        @enderror

                                    <!-- SWITCH TOGGLE SINGLE (ON/OFF) -->
                                    
                                    <!-- SWITCH TOGGLE SINGLE (ON/OFF) - CUSTOM LABEL INSIDE -->
@elseif($field['type'] === 'switch-single')
    <label class="flex items-center cursor-pointer" wire:key="switch-{{ $fieldName }}">
        <div class="relative">
            <input type="checkbox" 
                   wire:model="{{ $field['model'] }}"
                   class="sr-only"
                   {{ $field['disabled'] ?? false ? 'disabled' : '' }}
                   wire:change="$refresh">
            <div class="block w-20 h-6 rounded-full transition-all duration-300 relative overflow-hidden
                {{ $fieldValue ? 'bg-[rgb(0,111,188)]' : 'bg-gray-400' }}">
                <!-- ON Label -->
                <div class="absolute inset-0 flex items-center justify-start px-3 transition-all duration-200
                    {{ $fieldValue ? 'opacity-100' : 'opacity-0' }}">
                    <span class="text-xs font-semibold text-white">{{ $field['on_label'] ?? 'ON' }}</span>
                </div>
                <!-- OFF Label -->
                <div class="absolute inset-0 flex items-center justify-end px-3 transition-all duration-200
                    {{ $fieldValue ? 'opacity-0' : 'opacity-100' }}">
                    <span class="text-xs font-semibold text-white">{{ $field['off_label'] ?? 'OFF' }}</span>
                </div>
            </div>
            <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-all duration-300 shadow-sm
                {{ $fieldValue ? 'transform translate-x-16' : '' }}"></div>
        </div>
    </label>
    @error($field['error'])
        <div class="text-red-600 text-sm mt-1 animate-shake">{{ $message }}</div>
    @enderror

    
                                    <!-- TEXT INPUT -->
                                    @elseif($field['type'] === 'text')
                                        <input type="text" 
                                               wire:model.defer="{{ $field['model'] }}"
                                               class="w-full rounded-lg border p-2 border-gray-300 bg-white text-gray-900 focus:border-[rgb(0,111,188)] focus:ring-[rgb(0,111,188)] shadow-sm transition-all duration-300 @error($field['error']) border-red-500 @enderror"
                                               placeholder="{{ $field['placeholder'] ?? '' }}"
                                               {{ $field['disabled'] ?? false ? 'disabled' : '' }}>
                                        @error($field['error'])
                                            <div class="text-red-600 text-sm mt-1 animate-shake">{{ $message }}</div>
                                        @enderror

                                    <!-- NUMBER INPUT -->
                                    @elseif($field['type'] === 'number')
                                        <input type="number" 
                                               wire:model.defer="{{ $field['model'] }}"
                                               class="w-full rounded-lg border p-2 border-gray-300 bg-white text-gray-900 focus:border-[rgb(0,111,188)] focus:ring-[rgb(0,111,188)] shadow-sm transition-all duration-300 @error($field['error']) border-red-500 @enderror"
                                               placeholder="{{ $field['placeholder'] ?? '' }}"
                                               min="{{ $field['min'] ?? '' }}"
                                               max="{{ $field['max'] ?? '' }}"
                                               step="{{ $field['step'] ?? '1' }}"
                                               {{ $field['disabled'] ?? false ? 'disabled' : '' }}>
                                        @error($field['error'])
                                            <div class="text-red-600 text-sm mt-1 animate-shake">{{ $message }}</div>
                                        @enderror

                                    <!-- SELECT DROPDOWN -->
                                    @elseif($field['type'] === 'select')
                                        <select wire:model.defer="{{ $field['model'] }}"
                                                class="w-full rounded-lg border p-2 border-gray-300 bg-white text-gray-900 focus:border-[rgb(0,111,188)] focus:ring-[rgb(0,111,188)] shadow-sm transition-all duration-300 @error($field['error']) border-red-500 @enderror"
                                                {{ $field['disabled'] ?? false ? 'disabled' : '' }}>
                                            <option value="">{{ $field['placeholder'] ?? 'Pilih...' }}</option>
                                            @foreach($field['options'] ?? [] as $value => $label)
                                                <option value="{{ $value }}" {{ $fieldValue == $value ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error($field['error'])
                                            <div class="text-red-600 text-sm mt-1 animate-shake">{{ $message }}</div>
                                        @enderror

                                    <!-- TEXTAREA -->
                                    @elseif($field['type'] === 'textarea')
                                        <textarea wire:model.defer="{{ $field['model'] }}"
                                                  rows="{{ $field['rows'] ?? 3 }}"
                                                  class="w-full rounded-lg border p-2 border-gray-300 bg-white text-gray-900 focus:border-[rgb(0,111,188)] focus:ring-[rgb(0,111,188)] shadow-sm transition-all duration-300 @error($field['error']) border-red-500 @enderror"
                                                  placeholder="{{ $field['placeholder'] ?? '' }}"
                                                  {{ $field['disabled'] ?? false ? 'disabled' : '' }}></textarea>
                                        @error($field['error'])
                                            <div class="text-red-600 text-sm mt-1 animate-shake">{{ $message }}</div>
                                        @enderror

                                    @endif
                                    
                                    <!-- HELPER TEXT BOTTOM -->
                                    @if($field['helper_bottom'] ?? false)
                                        <p class="text-xs text-gray-500 mt-1">{{ $field['helper_bottom'] }}</p>
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

<style>
    /* Switch Toggle Styles */
    .sr-only {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border: 0;
    }

    /* Animation untuk switch */
    .dot {
        transition: all 0.3s ease-in-out;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
    
    /* Hover effects untuk switch */
    label:hover .block {
        transform: scale(1.05);
    }

    /* Helper tooltip animation */
    .group:hover .group-hover\:block {
        animation: fadeInUp 0.2s ease-out;
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(5px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Active state untuk switch yang dipilih */
    input:checked + .block {
        background-color: rgb(0, 111, 188) !important;
    }
</style>