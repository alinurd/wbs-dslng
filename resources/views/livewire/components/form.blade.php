@props([
    'showModal' => false,
    'updateMode' => false,
    'form' => [],
    'onClose' => '',
    'onSave' => '',
    'title' => 'Form Data',
    'size' => 'md',
    'fields' => [],
])

@if ($showModal)
    <div class="fixed inset-0 z-50 overflow-y-auto animate-fade-in" style="background-color: rgba(0,0,0,0.5)">
        <div class="flex min-h-full items-center justify-center p-4">
            <div
                class="modal-content bg-white rounded-lg shadow-xl w-full max-w-2{{ $size }} transform transition-all duration-300 scale-95 animate-scale-in">
                <div
                    class="modal-header  text-white rounded-t-lg px-6 py-5 shadow-lg">
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
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach ($fields as $field)
                                @php
                                    $colSpan = $field['colspan'] ?? 1;
                                    $fieldClass = $colSpan == 2 ? 'md:col-span-2' : '';
                                @endphp

                                <div class="mb-3 {{ $fieldClass }}">
                                    <label class="form-label font-medium text-gray-700">
                                        {{ $field['label'] }}
                                        @if ($field['required'] ?? false)
                                            <span class="text-red-500">*</span>
                                        @endif
                                    </label>

                                    <!-- TEXT INPUT -->
                                    @if ($field['type'] === 'text')
                                        <input type="text" wire:model.defer="{{ $field['model'] }}"
                                            class="w-full rounded-lg border p-2 border-gray-300 bg-white text-gray-900 focus:border-[rgb(0,111,188)] focus:ring-[rgb(0,111,188)] shadow-sm transition-all duration-300 @error($field['error']) border-red-500 @enderror"
                                            placeholder="{{ $field['placeholder'] ?? '' }}"
                                            {{ $field['disabled'] ?? false ? 'disabled' : '' }}>
                                        @error($field['error'])
                                            <div class="text-red-600 text-sm mt-1 animate-shake">{{ $message }}</div>
                                        @enderror

                                        <!-- NUMBER INPUT -->
                                    @elseif($field['type'] === 'number')
                                        <input type="number" wire:model.defer="{{ $field['model'] }}"
                                            class="w-full rounded-lg border p-2 border-gray-300 bg-white text-gray-900 focus:border-[rgb(0,111,188)] focus:ring-[rgb(0,111,188)] shadow-sm transition-all duration-300 @error($field['error']) border-red-500 @enderror"
                                            placeholder="{{ $field['placeholder'] ?? '' }}"
                                            min="{{ $field['min'] ?? '' }}" max="{{ $field['max'] ?? '' }}"
                                            step="{{ $field['step'] ?? '1' }}"
                                            {{ $field['disabled'] ?? false ? 'disabled' : '' }}>
                                        @error($field['error'])
                                            <div class="text-red-600 text-sm mt-1 animate-shake">{{ $message }}</div>
                                        @enderror

                                        <!-- EMAIL INPUT -->
                                    @elseif($field['type'] === 'email')
                                        <input type="email" wire:model.defer="{{ $field['model'] }}"
                                            class="w-full rounded-lg border p-2 border-gray-300 bg-white text-gray-900 focus:border-[rgb(0,111,188)] focus:ring-[rgb(0,111,188)] shadow-sm transition-all duration-300 @error($field['error']) border-red-500 @enderror"
                                            placeholder="{{ $field['placeholder'] ?? '' }}"
                                            {{ $field['disabled'] ?? false ? 'disabled' : '' }}>
                                        @error($field['error'])
                                            <div class="text-red-600 text-sm mt-1 animate-shake">{{ $message }}</div>
                                        @enderror

                                        <!-- PASSWORD INPUT -->
                                    @elseif($field['type'] === 'password')
                                        <input type="password" wire:model.defer="{{ $field['model'] }}"
                                            class="w-full rounded-lg border p-2 border-gray-300 bg-white text-gray-900 focus:border-[rgb(0,111,188)] focus:ring-[rgb(0,111,188)] shadow-sm transition-all duration-300 @error($field['error']) border-red-500 @enderror"
                                            placeholder="{{ $field['placeholder'] ?? '' }}"
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
                                            @foreach ($field['options'] ?? [] as $value => $label)
                                                <option value="{{ $value }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error($field['error'])
                                            <div class="text-red-600 text-sm mt-1 animate-shake">{{ $message }}</div>
                                        @enderror

                                        <!-- TEXTAREA -->
                                    @elseif($field['type'] === 'textarea')
                                        <textarea wire:model.defer="{{ $field['model'] }}" rows="{{ $field['rows'] ?? 3 }}"
                                            class="w-full rounded-lg border p-2 border-gray-300 bg-white text-gray-900 focus:border-[rgb(0,111,188)] focus:ring-[rgb(0,111,188)] shadow-sm transition-all duration-300 @error($field['error']) border-red-500 @enderror"
                                            placeholder="{{ $field['placeholder'] ?? '' }}" {{ $field['disabled'] ?? false ? 'disabled' : '' }}></textarea>
                                        @error($field['error'])
                                            <div class="text-red-600 text-sm mt-1 animate-shake">{{ $message }}</div>
                                        @enderror

                                        <!-- CHECKBOX -->
                                    @elseif($field['type'] === 'checkbox')
                                        <div class="flex items-center">
                                            <input type="checkbox" wire:model.defer="{{ $field['model'] }}"
                                                class="h-5 w-5 rounded border-gray-300 text-[rgb(0,111,188)] focus:ring-[rgb(0,111,188)] transition-all duration-300"
                                                id="{{ $field['model'] }}"
                                                {{ $field['disabled'] ?? false ? 'disabled' : '' }}>
                                            <label class="form-check-label font-medium text-gray-700 ms-3"
                                                for="{{ $field['model'] }}">
                                                {{ $field['checkbox_label'] ?? $field['label'] }}
                                            </label>
                                        </div>

                                        <!-- RADIO BUTTONS -->
                                    @elseif($field['type'] === 'radio')
                                        <div class="space-y-2">
                                            @foreach ($field['options'] ?? [] as $value => $label)
                                                <div class="flex items-center">
                                                    <input type="radio" wire:model.defer="{{ $field['model'] }}"
                                                        value="{{ $value }}"
                                                        class="h-4 w-4 text-[rgb(0,111,188)] focus:ring-[rgb(0,111,188)] border-gray-300"
                                                        id="{{ $field['model'] }}_{{ $value }}"
                                                        {{ $field['disabled'] ?? false ? 'disabled' : '' }}>
                                                    <label for="{{ $field['model'] }}_{{ $value }}"
                                                        class="ml-2 text-sm text-gray-700">
                                                        {{ $label }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                        @error($field['error'])
                                            <div class="text-red-600 text-sm mt-1 animate-shake">{{ $message }}</div>
                                        @enderror

                                        <!-- DATE INPUT -->
                                    @elseif($field['type'] === 'date')
                                        <input type="date" wire:model.defer="{{ $field['model'] }}"
                                            class="w-full rounded-lg border p-2 border-gray-300 bg-white text-gray-900 focus:border-[rgb(0,111,188)] focus:ring-[rgb(0,111,188)] shadow-sm transition-all duration-300 @error($field['error']) border-red-500 @enderror"
                                            {{ $field['disabled'] ?? false ? 'disabled' : '' }}>
                                        @error($field['error'])
                                            <div class="text-red-600 text-sm mt-1 animate-shake">{{ $message }}</div>
                                        @enderror

                                        <!-- TIME INPUT -->
                                    @elseif($field['type'] === 'time')
                                        <input type="time" wire:model.defer="{{ $field['model'] }}"
                                            class="w-full rounded-lg border p-2 border-gray-300 bg-white text-gray-900 focus:border-[rgb(0,111,188)] focus:ring-[rgb(0,111,188)] shadow-sm transition-all duration-300 @error($field['error']) border-red-500 @enderror"
                                            {{ $field['disabled'] ?? false ? 'disabled' : '' }}>
                                        @error($field['error'])
                                            <div class="text-red-600 text-sm mt-1 animate-shake">{{ $message }}</div>
                                        @enderror

                                        <!-- DATETIME INPUT -->
                                    @elseif($field['type'] === 'datetime')
                                        <input type="datetime-local" wire:model.defer="{{ $field['model'] }}"
                                            class="w-full rounded-lg border p-2 border-gray-300 bg-white text-gray-900 focus:border-[rgb(0,111,188)] focus:ring-[rgb(0,111,188)] shadow-sm transition-all duration-300 @error($field['error']) border-red-500 @enderror"
                                            {{ $field['disabled'] ?? false ? 'disabled' : '' }}>
                                        @error($field['error'])
                                            <div class="text-red-600 text-sm mt-1 animate-shake">{{ $message }}</div>
                                        @enderror

                                        <!-- FILE UPLOAD -->
                                    @elseif($field['type'] === 'file')
                                        <input type="file" wire:model="{{ $field['model'] }}"
                                            class="w-full rounded-lg border p-2 border-gray-300 bg-white text-gray-900 focus:border-[rgb(0,111,188)] focus:ring-[rgb(0,111,188)] shadow-sm transition-all duration-300 @error($field['error']) border-red-500 @enderror"
                                            {{ $field['multiple'] ?? false ? 'multiple' : '' }}
                                            accept="{{ $field['accept'] ?? '' }}"
                                            {{ $field['disabled'] ?? false ? 'disabled' : '' }}>
                                        @error($field['error'])
                                            <div class="text-red-600 text-sm mt-1 animate-shake">{{ $message }}</div>
                                        @enderror

                                        <!-- COLOR PICKER -->
                                    @elseif($field['type'] === 'color')
                                        <input type="color" wire:model.defer="{{ $field['model'] }}"
                                            class="w-full h-10 rounded-lg border border-gray-300 bg-white focus:border-[rgb(0,111,188)] focus:ring-[rgb(0,111,188)] shadow-sm transition-all duration-300 @error($field['error']) border-red-500 @enderror"
                                            {{ $field['disabled'] ?? false ? 'disabled' : '' }}>
                                        @error($field['error'])
                                            <div class="text-red-600 text-sm mt-1 animate-shake">{{ $message }}</div>
                                        @enderror

                                        <!-- RANGE SLIDER -->
                                    @elseif($field['type'] === 'range')
                                        <input type="range" wire:model.defer="{{ $field['model'] }}"
                                            class="w-full rounded-lg border border-gray-300 bg-white focus:border-[rgb(0,111,188)] focus:ring-[rgb(0,111,188)] shadow-sm transition-all duration-300 @error($field['error']) border-red-500 @enderror"
                                            min="{{ $field['min'] ?? 0 }}" max="{{ $field['max'] ?? 100 }}"
                                            step="{{ $field['step'] ?? 1 }}"
                                            {{ $field['disabled'] ?? false ? 'disabled' : '' }}>
                                        @error($field['error'])
                                            <div class="text-red-600 text-sm mt-1 animate-shake">{{ $message }}</div>
                                        @enderror

                                        <!-- URL INPUT -->
                                    @elseif($field['type'] === 'url')
                                        <input type="url" wire:model.defer="{{ $field['model'] }}"
                                            class="w-full rounded-lg border p-2 border-gray-300 bg-white text-gray-900 focus:border-[rgb(0,111,188)] focus:ring-[rgb(0,111,188)] shadow-sm transition-all duration-300 @error($field['error']) border-red-500 @enderror"
                                            placeholder="{{ $field['placeholder'] ?? '' }}"
                                            {{ $field['disabled'] ?? false ? 'disabled' : '' }}>
                                        @error($field['error'])
                                            <div class="text-red-600 text-sm mt-1 animate-shake">{{ $message }}</div>
                                        @enderror

                                        <!-- TEL INPUT -->
                                    @elseif($field['type'] === 'tel')
                                        <input type="tel" wire:model.defer="{{ $field['model'] }}"
                                            class="w-full rounded-lg border p-2 border-gray-300 bg-white text-gray-900 focus:border-[rgb(0,111,188)] focus:ring-[rgb(0,111,188)] shadow-sm transition-all duration-300 @error($field['error']) border-red-500 @enderror"
                                            placeholder="{{ $field['placeholder'] ?? '' }}"
                                            {{ $field['disabled'] ?? false ? 'disabled' : '' }}>
                                        @error($field['error'])
                                            <div class="text-red-600 text-sm mt-1 animate-shake">{{ $message }}</div>
                                        @enderror

                                        <!-- READONLY TEXT -->
                                    @elseif($field['type'] === 'readonly')
                                        <div
                                            class="w-full rounded-lg border p-2 border-gray-300 bg-gray-100 text-gray-700">
                                            {{ $field['value'] ?? '' }}
                                        </div>

                                        <!-- CUSTOM HTML -->
                                    @elseif($field['type'] === 'custom')
                                        {!! $field['html'] ?? '' !!}
                                    @endif

                                    <!-- HELPER TEXT -->
                                    @if ($field['helper'] ?? false)
                                        <p class="text-xs text-gray-500 mt-1">{{ $field['helper'] }}</p>
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
