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
    'customMessages' => [], // Tambah prop untuk custom messages
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
                        <!-- Tampilkan error general jika ada -->
                        @if ($errors->any())
                            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg animate-shake">
                                <div class="flex items-center">
                                    <i class="fas fa-exclamation-triangle text-red-500 mr-3"></i>
                                    <span class="text-red-700 font-medium">Terdapat kesalahan dalam pengisian form:</span>
                                </div>
                                <ul class="mt-2 list-disc list-inside text-sm text-red-600">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-{{ $cols }} gap-4">
                            @foreach($fields as $field)
                                @php
                                    $colSpan = $field['colspan'] ?? 1;
                                    $fieldClass = $colSpan == 2 ? 'md:col-span-2' : '';
                                    
                                    // Get field value from form array
                                    $fieldName = str_replace('form.', '', $field['model']);
                                    $fieldValue = $form[$field['model']] ?? null;
                                    
                                    // Get error field name
                                    $errorField = $field['model'];
                                    $hasError = $errors->has($errorField);
                                    
                                    // Get custom message for this field
                                    $customMessage = $customMessages[$errorField] ?? null;
                                    $fieldMessages = $field['messages'] ?? []; // Custom messages di field level
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
                                                               wire:model="{{ $field['model'] }}"
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
                                        <!-- ERROR MESSAGE DI BAWAH SWITCH -->
                                        @error($errorField)
                                            <div class="text-red-600 text-sm mt-2 animate-shake flex items-center bg-red-50 p-2 rounded border border-red-200">
                                                <i class="fas fa-exclamation-circle mr-2 text-xs"></i>
                                                {{ $customMessage ?: ($fieldMessages[$message] ?? $message) }}
                                            </div>
                                        @enderror

                                    <!-- TEXT INPUT -->
                                    @elseif($field['type'] === 'text')
                                        <div class="relative">
                                            <input type="text" 
                                                   wire:model="{{ $field['model'] }}"
                                                   class="w-full rounded-lg border p-3 bg-white text-gray-900 focus:border-[rgb(0,111,188)] focus:ring-2 focus:ring-[rgb(0,111,188)] shadow-sm transition-all duration-300 {{ $hasError ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-gray-300' }}"
                                                   placeholder="{{ $field['placeholder'] ?? '' }}"
                                                   {{ $field['disabled'] ?? false ? 'disabled' : '' }}>
                                        </div>
                                        <!-- ERROR MESSAGE DI BAWAH INPUT TEXT -->
                                        @error($errorField)
                                            <div class="text-red-600 text-sm mt-2 animate-shake flex items-center bg-red-50 p-2 rounded border border-red-200">
                                                <i class="fas fa-exclamation-circle mr-2 text-xs"></i>
                                                {{ $customMessage ?: ($fieldMessages[$message] ?? $message) }}
                                            </div>
                                        @enderror

                                    <!-- EMAIL INPUT -->
                                    @elseif($field['type'] === 'email')
                                        <div class="relative">
                                            <input type="email" 
                                                   wire:model="{{ $field['model'] }}"
                                                   class="w-full rounded-lg border p-3 bg-white text-gray-900 focus:border-[rgb(0,111,188)] focus:ring-2 focus:ring-[rgb(0,111,188)] shadow-sm transition-all duration-300 {{ $hasError ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-gray-300' }}"
                                                   placeholder="{{ $field['placeholder'] ?? '' }}"
                                                   {{ $field['disabled'] ?? false ? 'disabled' : '' }}>
                                        </div>
                                        <!-- ERROR MESSAGE DI BAWAH INPUT EMAIL -->
                                        @error($errorField)
                                            <div class="text-red-600 text-sm mt-2 animate-shake flex items-center bg-red-50 p-2 rounded border border-red-200">
                                                <i class="fas fa-exclamation-circle mr-2 text-xs"></i>
                                                {{ $customMessage ?: ($fieldMessages[$message] ?? $message) }}
                                            </div>
                                        @enderror

                                    <!-- PASSWORD INPUT -->
                                    @elseif($field['type'] === 'password')
                                        <div class="relative">
                                            <input type="password" 
                                                   wire:model="{{ $field['model'] }}"
                                                   class="w-full rounded-lg border p-3 bg-white text-gray-900 focus:border-[rgb(0,111,188)] focus:ring-2 focus:ring-[rgb(0,111,188)] shadow-sm transition-all duration-300 {{ $hasError ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-gray-300' }}"
                                                   placeholder="{{ $field['placeholder'] ?? '' }}"
                                                   {{ $field['disabled'] ?? false ? 'disabled' : '' }}>
                                        </div>
                                        <!-- ERROR MESSAGE DI BAWAH INPUT PASSWORD -->
                                        @error($errorField)
                                            <div class="text-red-600 text-sm mt-2 animate-shake flex items-center bg-red-50 p-2 rounded border border-red-200">
                                                <i class="fas fa-exclamation-circle mr-2 text-xs"></i>
                                                {{ $customMessage ?: ($fieldMessages[$message] ?? $message) }}
                                            </div>
                                        @enderror

                                    <!-- NUMBER INPUT -->
                                    @elseif($field['type'] === 'number')
                                        <div class="relative">
                                            <input type="number" 
                                                   wire:model="{{ $field['model'] }}"
                                                   class="w-full rounded-lg border p-3 bg-white text-gray-900 focus:border-[rgb(0,111,188)] focus:ring-2 focus:ring-[rgb(0,111,188)] shadow-sm transition-all duration-300 {{ $hasError ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-gray-300' }}"
                                                   placeholder="{{ $field['placeholder'] ?? '' }}"
                                                   min="{{ $field['min'] ?? '' }}"
                                                   max="{{ $field['max'] ?? '' }}"
                                                   step="{{ $field['step'] ?? '1' }}"
                                                   {{ $field['disabled'] ?? false ? 'disabled' : '' }}>
                                        </div>
                                        <!-- ERROR MESSAGE DI BAWAH INPUT NUMBER -->
                                        @error($errorField)
                                            <div class="text-red-600 text-sm mt-2 animate-shake flex items-center bg-red-50 p-2 rounded border border-red-200">
                                                <i class="fas fa-exclamation-circle mr-2 text-xs"></i>
                                                {{ $customMessage ?: ($fieldMessages[$message] ?? $message) }}
                                            </div>
                                        @enderror

                                    <!-- SELECT DROPDOWN -->
                                    @elseif($field['type'] === 'select')
                                        <div class="relative">
                                            <select wire:model="{{ $field['model'] }}"
                                                    class="w-full rounded-lg border p-3 bg-white text-gray-900 focus:border-[rgb(0,111,188)] focus:ring-2 focus:ring-[rgb(0,111,188)] shadow-sm transition-all duration-300 {{ $hasError ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-gray-300' }}"
                                                    {{ $field['disabled'] ?? false ? 'disabled' : '' }}>
                                                <option value="">{{ $field['placeholder'] ?? 'Pilih...' }}</option>
                                                @foreach($field['options'] ?? [] as $value => $label)
                                                    <option value="{{ $value }}" {{ $fieldValue == $value ? 'selected' : '' }}>
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <!-- ERROR MESSAGE DI BAWAH SELECT -->
                                        @error($errorField)
                                            <div class="text-red-600 text-sm mt-2 animate-shake flex items-center bg-red-50 p-2 rounded border border-red-200">
                                                <i class="fas fa-exclamation-circle mr-2 text-xs"></i>
                                                {{ $customMessage ?: ($fieldMessages[$message] ?? $message) }}
                                            </div>
                                        @enderror

                                    <!-- TEXTAREA -->
                                    @elseif($field['type'] === 'textarea')
                                        <div class="relative">
                                            <textarea wire:model="{{ $field['model'] }}"
                                                      rows="{{ $field['rows'] ?? 3 }}"
                                                      class="w-full rounded-lg border p-3 bg-white text-gray-900 focus:border-[rgb(0,111,188)] focus:ring-2 focus:ring-[rgb(0,111,188)] shadow-sm transition-all duration-300 {{ $hasError ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-gray-300' }}"
                                                      placeholder="{{ $field['placeholder'] ?? '' }}"
                                                      {{ $field['disabled'] ?? false ? 'disabled' : '' }}></textarea>
                                        </div>
                                        <!-- ERROR MESSAGE DI BAWAH TEXTAREA -->
                                        @error($errorField)
                                            <div class="text-red-600 text-sm mt-2 animate-shake flex items-center bg-red-50 p-2 rounded border border-red-200">
                                                <i class="fas fa-exclamation-circle mr-2 text-xs"></i>
                                                {{ $customMessage ?: ($fieldMessages[$message] ?? $message) }}
                                            </div>
                                        @enderror

                                    <!-- CHECKBOX -->
                                    @elseif($field['type'] === 'checkbox')
                                        <div class="flex items-center">
                                            <input type="checkbox" 
                                                   wire:model="{{ $field['model'] }}"
                                                   class="h-5 w-5 rounded border-gray-300 text-[rgb(0,111,188)] focus:ring-[rgb(0,111,188)] transition-all duration-300 {{ $hasError ? 'border-red-500' : 'border-gray-300' }}"
                                                   id="{{ $fieldName }}"
                                                   {{ $field['disabled'] ?? false ? 'disabled' : '' }}>
                                            <label class="form-check-label font-medium text-gray-700 ms-3" for="{{ $fieldName }}">
                                                {{ $field['checkbox_label'] ?? $field['label'] }}
                                            </label>
                                        </div>
                                        <!-- ERROR MESSAGE DI BAWAH CHECKBOX -->
                                        @error($errorField)
                                            <div class="text-red-600 text-sm mt-2 animate-shake flex items-center bg-red-50 p-2 rounded border border-red-200">
                                                <i class="fas fa-exclamation-circle mr-2 text-xs"></i>
                                                {{ $customMessage ?: ($fieldMessages[$message] ?? $message) }}
                                            </div>
                                        @enderror

                                    @endif
                                    
                                    <!-- HELPER TEXT BOTTOM -->
                                    @if($field['helper_bottom'] ?? false)
                                        <p class="text-xs text-gray-500 mt-1 {{ $hasError ? 'mt-3' : '' }}">{{ $field['helper_bottom'] }}</p>
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