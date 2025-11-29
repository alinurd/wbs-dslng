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
            <div
                class="modal-content bg-white rounded-lg shadow-xl w-full max-w-2{{ $size }} transform transition-all duration-300 scale-95 ">
                <!-- Header -->
                <div
                    class="modal-header bg-gradient-to-r from-[rgb(0,111,188)] to-[rgb(0,95,160)] text-white rounded-t-lg px-6 py-5 shadow-lg">
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
                                    <span class="text-red-700 font-medium">Terdapat kesalahan dalam pengisian
                                        form:</span>
                                </div>
                                <ul class="mt-2 list-disc list-inside text-sm text-red-600">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-{{ $cols }} gap-4">
                            @foreach ($fields as $field)
                                @php
                                    $colSpan = $field['colspan'] ?? 1;
                                    $fieldClass = $colSpan == 2 ? 'md:col-span-2' : '';

                                    // Get field value from form array
                                    $fieldName = str_replace('form.', '', $field['model']);
                                    $fieldValue = $form[$fieldName] ?? null;

                                    // Handle error field name dengan benar
                                    $errorField = $field['error'] ?? $fieldName;
                                    $hasError = $errors->has($errorField);

                                    // Get custom messages
                                    $fieldMessages = $field['messages'] ?? [];
                                @endphp

                                <div class="mb-3 {{ $fieldClass }}">
                                    <div class="flex items-center justify-between mb-2">
                                        <label class="form-label font-medium text-gray-700">
                                            {{ $field['label'] }}
                                            @if ($field['required'] ?? false)
                                                <span class="text-red-500">*</span>
                                            @endif
                                        </label>

                                        <!-- Helper dengan icon informasi -->
                                        @if ($field['helper'] ?? false)
                                            <div class="relative group">
                                                <button type="button"
                                                    class="text-gray-400 hover:text-[rgb(0,111,188)] transition-colors duration-200">
                                                    <i class="fas fa-question-circle text-sm"></i>
                                                </button>
                                                <div
                                                    class="absolute bottom-full right-0 mb-2 hidden group-hover:block z-10">
                                                    <div
                                                        class="bg-gray-800 text-white text-xs rounded py-2 px-3 whitespace-nowrap shadow-lg max-w-xs">
                                                        {{ $field['helper'] }}
                                                        <div
                                                            class="absolute top-full right-2 -mt-1 border-4 border-transparent border-t-gray-800">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- TEXT INPUT -->
                                    @if ($field['type'] === 'text')
                                        <div class="relative">
                                            <input type="text" wire:model="{{ $field['model'] }}"
                                                class="w-full rounded-lg border p-3 bg-white text-gray-900 focus:border-[rgb(0,111,188)] focus:ring-2 focus:ring-[rgb(0,111,188)] shadow-sm transition-all duration-300 {{ $hasError ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-gray-300' }}"
                                                placeholder="{{ $field['placeholder'] ?? '' }}"
                                                {{ $field['disabled'] ?? false ? 'disabled' : '' }}>
                                        </div>
                                        <!-- ERROR MESSAGE DI BAWAH INPUT TEXT -->
                                        @error($errorField)
                                            <div
                                                class="text-red-600 text-sm mt-2 animate-shake flex items-center bg-red-50 p-2 rounded border border-red-200">
                                                <i class="fas fa-exclamation-circle mr-2 text-xs"></i>
                                                {{ $fieldMessages[$message] ?? $message }}
                                            </div>
                                        @enderror

                                        <!-- EMAIL INPUT -->
                                    @elseif($field['type'] === 'email')
                                        <div class="relative">
                                            <input type="email" wire:model="{{ $field['model'] }}"
                                                class="w-full rounded-lg border p-3 bg-white text-gray-900 focus:border-[rgb(0,111,188)] focus:ring-2 focus:ring-[rgb(0,111,188)] shadow-sm transition-all duration-300 {{ $hasError ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-gray-300' }}"
                                                placeholder="{{ $field['placeholder'] ?? '' }}"
                                                {{ $field['disabled'] ?? false ? 'disabled' : '' }}>
                                        </div>
                                        <!-- ERROR MESSAGE DI BAWAH INPUT EMAIL -->
                                        @error($errorField)
                                            <div
                                                class="text-red-600 text-sm mt-2 animate-shake flex items-center bg-red-50 p-2 rounded border border-red-200">
                                                <i class="fas fa-exclamation-circle mr-2 text-xs"></i>
                                                {{ $fieldMessages[$message] ?? $message }}
                                            </div>
                                        @enderror

                                        <!-- PASSWORD INPUT -->
                                    @elseif($field['type'] === 'password')
                                        <div class="relative">
                                            <input type="password" wire:model="{{ $field['model'] }}"
                                                class="w-full rounded-lg border p-3 bg-white text-gray-900 focus:border-[rgb(0,111,188)] focus:ring-2 focus:ring-[rgb(0,111,188)] shadow-sm transition-all duration-300 {{ $hasError ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-gray-300' }}"
                                                placeholder="{{ $field['placeholder'] ?? '' }}"
                                                {{ $field['disabled'] ?? false ? 'disabled' : '' }}>
                                            <!-- Show/Hide Password Toggle -->
                                            <button type="button"
                                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
                                                onclick="togglePassword(this)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                        <!-- ERROR MESSAGE DI BAWAH INPUT PASSWORD -->
                                        @error($errorField)
                                            <div
                                                class="text-red-600 text-sm mt-2 animate-shake flex items-center bg-red-50 p-2 rounded border border-red-200">
                                                <i class="fas fa-exclamation-circle mr-2 text-xs"></i>
                                                {{ $fieldMessages[$message] ?? $message }}
                                            </div>
                                        @enderror

                                        <!-- NUMBER INPUT dengan validasi hanya angka -->
                                    @elseif($field['type'] === 'number')
                                        <div class="relative">
                                            <input type="text" wire:model="{{ $field['model'] }}"
                                                x-data="{
                                                    init() {
                                                        // Clean non-numeric characters on init
                                                        if (this.$wire.get('{{ $field['model'] }}') && !/^\d+$/.test(this.$wire.get('{{ $field['model'] }}'))) {
                                                            this.$wire.set('{{ $field['model'] }}', this.$wire.get('{{ $field['model'] }}').replace(/[^\d]/g, ''));
                                                        }
                                                    }
                                                }"
                                                x-on:input="$wire.set('{{ $field['model'] }}', $event.target.value.replace(/[^\d]/g, ''))"
                                                x-on:blur="$wire.set('{{ $field['model'] }}', $event.target.value.replace(/[^\d]/g, ''))"
                                                class="w-full rounded-lg border p-3 bg-white text-gray-900 focus:border-[rgb(0,111,188)] focus:ring-2 focus:ring-[rgb(0,111,188)] shadow-sm transition-all duration-300 {{ $hasError ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-gray-300' }}"
                                                placeholder="{{ $field['placeholder'] ?? '' }}"
                                                {{ $field['disabled'] ?? false ? 'disabled' : '' }}>
                                        </div>
                                        <!-- ERROR MESSAGE DI BAWAH INPUT NUMBER -->
                                        @error($errorField)
                                            <div
                                                class="text-red-600 text-sm mt-2 animate-shake flex items-center bg-red-50 p-2 rounded border border-red-200">
                                                <i class="fas fa-exclamation-circle mr-2 text-xs"></i>
                                                {{ $fieldMessages[$message] ?? $message }}
                                            </div>
                                        @enderror
                                        <!-- MONEY INPUT dengan format Rupiah -->
                                    @elseif($field['type'] === 'money')
                                        <div class="relative">
                                            <input type="text" wire:model="{{ $field['model'] }}"
                                                x-data="{
                                                    value: $wire.entangle('{{ $field['model'] }}'),
                                                    formatRupiah: function(number) {
                                                        return new Intl.NumberFormat('id-ID', {
                                                            style: 'currency',
                                                            currency: 'IDR',
                                                            minimumFractionDigits: 0,
                                                            maximumFractionDigits: 0
                                                        }).format(number).replace('Rp', '').trim();
                                                    },
                                                    parseRupiah: function(rupiah) {
                                                        return parseInt(rupiah.replace(/[^\d]/g, ''));
                                                    }
                                                }"
                                                x-on:blur="value = value ? formatRupiah(parseRupiah(value)) : ''"
                                                x-on:focus="value = value ? parseRupiah(value).toString() : ''"
                                                x-on:input="value = value.replace(/[^\d]/g, '')"
                                                class="w-full rounded-lg border p-3 bg-white text-gray-900 focus:border-[rgb(0,111,188)] focus:ring-2 focus:ring-[rgb(0,111,188)] shadow-sm transition-all duration-300 {{ $hasError ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-gray-300' }}"
                                                placeholder="{{ $field['placeholder'] ?? 'Rp 0' }}"
                                                {{ $field['disabled'] ?? false ? 'disabled' : '' }}>
                                            <div
                                                class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                                <span class="text-gray-500">IDR</span>
                                            </div>
                                        </div>
                                        <!-- ERROR MESSAGE DI BAWAH INPUT MONEY -->
                                        @error($errorField)
                                            <div
                                                class="text-red-600 text-sm mt-2 animate-shake flex items-center bg-red-50 p-2 rounded border border-red-200">
                                                <i class="fas fa-exclamation-circle mr-2 text-xs"></i>
                                                {{ $fieldMessages[$message] ?? $message }}
                                            </div>
                                        @enderror

                                        <!-- TEXTAREA -->
                                    @elseif($field['type'] === 'textarea')
                                        <div class="relative">
                                            <textarea wire:model="{{ $field['model'] }}" rows="{{ $field['rows'] ?? 4 }}"
                                                class="w-full rounded-lg border p-3 bg-white text-gray-900 focus:border-[rgb(0,111,188)] focus:ring-2 focus:ring-[rgb(0,111,188)] shadow-sm transition-all duration-300 {{ $hasError ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-gray-300' }}"
                                                placeholder="{{ $field['placeholder'] ?? '' }}" {{ $field['disabled'] ?? false ? 'disabled' : '' }}></textarea>
                                        </div>
                                        <!-- ERROR MESSAGE DI BAWAH TEXTAREA -->
                                        @error($errorField)
                                            <div
                                                class="text-red-600 text-sm mt-2 animate-shake flex items-center bg-red-50 p-2 rounded border border-red-200">
                                                <i class="fas fa-exclamation-circle mr-2 text-xs"></i>
                                                {{ $fieldMessages[$message] ?? $message }}
                                            </div>
                                        @enderror

                                        <!-- SELECT DROPDOWN -->
                                    @elseif($field['type'] === 'select')
                                        <div class="relative">
                                            <select wire:model="{{ $field['model'] }}"
                                                class="w-full rounded-lg border p-3 bg-white text-gray-900 focus:border-[rgb(0,111,188)] focus:ring-2 focus:ring-[rgb(0,111,188)] shadow-sm transition-all duration-300 {{ $hasError ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-gray-300' }}"
                                                {{ $field['disabled'] ?? false ? 'disabled' : '' }}>
                                                <option value="">{{ $field['placeholder'] ?? 'Pilih...' }}
                                                </option>
                                                @foreach ($field['options'] ?? [] as $value => $label)
                                                    <option value="{{ $value }}"
                                                        {{ $fieldValue == $value ? 'selected' : '' }}>
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <!-- ERROR MESSAGE DI BAWAH SELECT -->
                                        @error($errorField)
                                            <div
                                                class="text-red-600 text-sm mt-2 animate-shake flex items-center bg-red-50 p-2 rounded border border-red-200">
                                                <i class="fas fa-exclamation-circle mr-2 text-xs"></i>
                                                {{ $fieldMessages[$message] ?? $message }}
                                            </div>
                                        @enderror

                                        <!-- CHECKBOX -->
                                    @elseif($field['type'] === 'checkbox')
                                        <div class="flex items-center">
                                            <input type="checkbox" wire:model="{{ $field['model'] }}"
                                                class="h-5 w-5 rounded border-gray-300 text-[rgb(0,111,188)] focus:ring-[rgb(0,111,188)] transition-all duration-300 {{ $hasError ? 'border-red-500' : 'border-gray-300' }}"
                                                id="{{ $fieldName }}"
                                                {{ $field['disabled'] ?? false ? 'disabled' : '' }}>
                                            <label class="form-check-label font-medium text-gray-700 ms-3"
                                                for="{{ $fieldName }}">
                                                {{ $field['checkbox_label'] ?? $field['label'] }}
                                            </label>
                                        </div>
                                        <!-- ERROR MESSAGE DI BAWAH CHECKBOX -->
                                        @error($errorField)
                                            <div
                                                class="text-red-600 text-sm mt-2 animate-shake flex items-center bg-red-50 p-2 rounded border border-red-200">
                                                <i class="fas fa-exclamation-circle mr-2 text-xs"></i>
                                                {{ $fieldMessages[$message] ?? $message }}
                                            </div>
                                        @enderror



                                        <!-- CHECKBOX MULTIPLE -->
                                    @elseif($field['type'] === 'checkbox-multiple')
                                        <div class="space-y-3" wire:key="checkbox-multiple-{{ $fieldName }}">
                                            <!-- Header dengan select all/deselect all -->
                                            <div class="flex items-center justify-between mb-2">
                                                <span class="text-sm font-medium text-gray-700">
                                                    {{ $field['label'] }}
                                                    @if ($field['required'] ?? false)
                                                        <span class="text-red-500">*</span>
                                                    @endif
                                                </span>

                                                @if (count($field['options'] ?? []) > 0)
                                                    <div class="flex space-x-2">
                                                        <button type="button"
                                                            wire:click="$set('{{ $field['model'] }}', [])"
                                                            class="text-xs text-red-600 hover:text-red-800 font-medium px-2 py-1 border border-red-200 rounded hover:bg-red-50 transition-colors">
                                                            <i class="fas fa-times mr-1"></i>Hapus Semua
                                                        </button>
                                                        <button type="button"
                                                            wire:click="$set('{{ $field['model'] }}', {{ json_encode(array_keys($field['options'])) }})"
                                                            class="text-xs text-green-600 hover:text-green-800 font-medium px-2 py-1 border border-green-200 rounded hover:bg-green-50 transition-colors">
                                                            <i class="fas fa-check mr-1"></i>Pilih Semua
                                                        </button>
                                                    </div>
                                                @endif
                                            </div>

                                            <!-- Checkbox Container -->
                                            <div
                                                class="max-h-60 overflow-y-auto border border-gray-200 rounded-lg p-4 bg-gray-50">
                                                @if (count($field['options'] ?? []) > 0)
                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                        @foreach ($field['options'] as $value => $label)
                                                            @php
                                                                $isChecked = in_array($value, $fieldValue ?? []);
                                                                $checkboxId = "{$fieldName}_{$value}";
                                                            @endphp

                                                            <label
                                                                class="flex items-start space-x-3 p-2 rounded-lg hover:bg-white transition-colors cursor-pointer border border-transparent hover:border-gray-200 {{ $isChecked ? 'bg-blue-50 border-blue-200' : '' }}"
                                                                wire:key="checkbox-{{ $fieldName }}-{{ $value }}">

                                                                <!-- Checkbox Input -->
                                                                <div class="flex items-center h-5 mt-0.5">
                                                                    <input type="checkbox"
                                                                        wire:model="{{ $field['model'] }}"
                                                                        value="{{ $value }}"
                                                                        id="{{ $checkboxId }}"
                                                                        class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 transition-all duration-200 {{ $hasError ? 'border-red-500' : '' }}"
                                                                        {{ $field['disabled'] ?? false ? 'disabled' : '' }}>
                                                                </div>

                                                                <!-- Label and Description -->
                                                                <div class="flex-1 min-w-0">
                                                                    <span
                                                                        class="text-sm font-medium text-gray-700 block">{{ $label }}</span>
                                                                    @if (isset($field['descriptions'][$value]))
                                                                        <p class="text-xs text-gray-500 mt-1">
                                                                            {{ $field['descriptions'][$value] }}</p>
                                                                    @endif
                                                                </div>

                                                                <!-- Check Icon -->
                                                                @if ($isChecked)
                                                                    <div class="flex-shrink-0">
                                                                        <i
                                                                            class="fas fa-check-circle text-green-500 text-sm"></i>
                                                                    </div>
                                                                @endif
                                                            </label>
                                                        @endforeach
                                                    </div>

                                                    <!-- Selected Count -->
                                                    <div class="mt-3 pt-3 border-t border-gray-200">
                                                        <p class="text-xs text-gray-600">
                                                            <i class="fas fa-check-circle text-green-500 mr-1"></i>
                                                            Terpilih: <span
                                                                class="font-semibold">{{ count($fieldValue ?? []) }}</span>
                                                            dari {{ count($field['options']) }}
                                                        </p>
                                                    </div>
                                                @else
                                                    <!-- Empty State -->
                                                    <div class="text-center py-4">
                                                        <i class="fas fa-inbox text-gray-300 text-2xl mb-2"></i>
                                                        <p class="text-sm text-gray-500">Tidak ada opsi yang tersedia
                                                        </p>
                                                    </div>
                                                @endif
                                            </div>

                                            <!-- ERROR MESSAGE DI BAWAH CHECKBOX MULTIPLE -->
                                            @error($errorField)
                                                <div
                                                    class="text-red-600 text-sm mt-2 animate-shake flex items-center bg-red-50 p-2 rounded border border-red-200">
                                                    <i class="fas fa-exclamation-circle mr-2 text-xs"></i>
                                                    {{ $fieldMessages[$message] ?? $message }}
                                                </div>
                                            @enderror

                                            <!-- HELPER TEXT BOTTOM -->
                                            @if ($field['helper_bottom'] ?? false)
                                                <p class="text-xs text-gray-500 mt-1">{{ $field['helper_bottom'] }}
                                                </p>
                                            @endif
                                        </div>


                                    


@elseif($field['type'] === 'checkbox-roles')
    <div class="space-y-3" wire:key="role-selection-{{ $fieldName }}">
        <!-- Header -->
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-gray-700">
                {{ $field['label'] }}
                @if ($field['required'] ?? false)
                    <span class="text-red-500">*</span>
                @endif
            </span>
        </div>

        <!-- Radio Button Container -->
        <div class="max-h-60 overflow-y-auto border border-gray-200 rounded-lg p-4 bg-gray-50">
            @if (count($field['options'] ?? []) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach ($field['options'] as $value => $label)
                        @php
                            $isChecked = $fieldValue == $value; // Single value comparison
                            $radioId = "{$fieldName}_{$value}";
                        @endphp

                        <label
                            class="flex items-start space-x-3 p-2 rounded-lg hover:bg-white transition-colors cursor-pointer border border-transparent hover:border-gray-200 {{ $isChecked ? 'bg-blue-50 border-blue-200' : '' }}"
                            wire:key="role-{{ $fieldName }}-{{ $value }}">

                            <!-- Radio Input -->
                            <div class="flex items-center h-5 mt-0.5">
                                <input type="radio"
                                    wire:model.live="{{ $field['model'] }}"
                                    wire:change="onRoleChange({{ $value }})"
                                    value="{{ $value }}"
                                    id="{{ $radioId }}"
                                    name="{{ $fieldName }}"
                                    class="h-4 w-4 rounded-full border-gray-300 text-blue-600 focus:ring-blue-500 transition-all duration-200 {{ $hasError ? 'border-red-500' : '' }}"
                                    {{ $field['disabled'] ?? false ? 'disabled' : '' }}>
                            </div>

                            <!-- Label and Description -->
                            <div class="flex-1 min-w-0">
                                <span class="text-sm font-medium text-gray-700 block">{{ $label }}</span>
                                @if (isset($field['descriptions'][$value]))
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ $field['descriptions'][$value] }}</p>
                                @endif
                            </div>

                            <!-- Check Icon -->
                            @if ($isChecked)
                                <div class="flex-shrink-0">
                                    <i class="fas fa-check-circle text-green-500 text-sm"></i>
                                </div>
                            @endif
                        </label>
                    @endforeach
                </div>

                <!-- FORWARD DROPDOWN - Hanya tampil jika role 6 dipilih -->
                @if($this->shouldShowForwardDropdown())
                    <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg transition-all duration-300" 
                         wire:key="forward-dropdown-{{ $fieldName }}">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-forward mr-2 text-blue-500"></i>
                            Pilih Tujuan Forward:
                            <span class="text-red-500">*</span>
                        </label>
                        <select wire:model="form.fwd_id" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors duration-200 {{ $errors->has('form.fwd_id') ? 'border-red-500' : '' }}">
                            <option value="">-- Pilih Tujuan Forward --</option>
                            @foreach($this->getForwardOptions() as $option)
                                <option value="{{ $option->id }}">{{ $option->data_id }}</option>
                            @endforeach
                        </select>
                        
                        <!-- Error message untuk forward dropdown -->
                        @error('form.fwd_id')
                            <div class="text-red-600 text-sm mt-2 flex items-center bg-red-50 p-2 rounded border border-red-200">
                                <i class="fas fa-exclamation-circle mr-2 text-xs"></i>
                                {{ $message }}
                            </div>
                        @enderror
                        
                        <!-- Helper text untuk forward dropdown -->
                        @if ($field['helper_forward'] ?? false)
                            <p class="text-xs text-gray-500 mt-2 flex items-start">
                                <i class="fas fa-info-circle mr-1 mt-0.5 text-blue-400"></i>
                                {{ $field['helper_forward'] }}
                            </p>
                        @endif
                    </div>
                @endif

            @else
                <!-- Empty State -->
                <div class="text-center py-4">
                    <i class="fas fa-inbox text-gray-300 text-2xl mb-2"></i>
                    <p class="text-sm text-gray-500">Tidak ada opsi role yang tersedia</p>
                </div>
            @endif
        </div>

        <!-- ERROR MESSAGE -->
        @error($errorField)
            <div class="text-red-600 text-sm mt-2 animate-shake flex items-center bg-red-50 p-2 rounded border border-red-200">
                <i class="fas fa-exclamation-circle mr-2 text-xs"></i>
                {{ $fieldMessages[$message] ?? $message }}
            </div>
        @enderror

        <!-- HELPER TEXT BOTTOM -->
        @if ($field['helper_bottom'] ?? false)
            <p class="text-xs text-gray-500 mt-1">{{ $field['helper_bottom'] }}</p>
        @endif
    </div>












                                        <!-- RADIO GROUP -->
                                    @elseif($field['type'] === 'radio')
                                        <div class="space-y-2">
                                            @foreach ($field['options'] ?? [] as $value => $label)
                                                <label class="flex items-center">
                                                    <input type="radio" wire:model="{{ $field['model'] }}"
                                                        value="{{ $value }}"
                                                        class="h-4 w-4 border-gray-300 text-[rgb(0,111,188)] focus:ring-[rgb(0,111,188)] {{ $hasError ? 'border-red-500' : 'border-gray-300' }}"
                                                        {{ $field['disabled'] ?? false ? 'disabled' : '' }}>
                                                    <span
                                                        class="ml-2 text-sm text-gray-700">{{ $label }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                        <!-- ERROR MESSAGE DI BAWAH RADIO -->
                                        @error($errorField)
                                            <div
                                                class="text-red-600 text-sm mt-2 animate-shake flex items-center bg-red-50 p-2 rounded border border-red-200">
                                                <i class="fas fa-exclamation-circle mr-2 text-xs"></i>
                                                {{ $fieldMessages[$message] ?? $message }}
                                            </div>
                                        @enderror

                                        <!-- DATE INPUT -->
                                    @elseif($field['type'] === 'date')
                                        <div class="relative">
                                            <input type="date" wire:model="{{ $field['model'] }}"
                                                class="w-full rounded-lg border p-3 bg-white text-gray-900 focus:border-[rgb(0,111,188)] focus:ring-2 focus:ring-[rgb(0,111,188)] shadow-sm transition-all duration-300 {{ $hasError ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-gray-300' }}"
                                                {{ $field['disabled'] ?? false ? 'disabled' : '' }}>
                                        </div>
                                        <!-- ERROR MESSAGE DI BAWAH DATE -->
                                        @error($errorField)
                                            <div
                                                class="text-red-600 text-sm mt-2 animate-shake flex items-center bg-red-50 p-2 rounded border border-red-200">
                                                <i class="fas fa-exclamation-circle mr-2 text-xs"></i>
                                                {{ $fieldMessages[$message] ?? $message }}
                                            </div>
                                        @enderror

                                        <!-- SWITCH TOGGLE SINGLE (ON/OFF) -->
                                    @elseif($field['type'] === 'switch-single')
                                        <label class="flex items-center cursor-pointer"
                                            wire:key="switch-{{ $fieldName }}">
                                            <div class="relative">
                                                <input type="checkbox" wire:model="{{ $field['model'] }}"
                                                    class="sr-only"
                                                    {{ $field['disabled'] ?? false ? 'disabled' : '' }}
                                                    wire:change="$refresh">
                                                <div
                                                    class="block w-20 h-6 rounded-full transition-all duration-300 relative overflow-hidden
                            {{ $fieldValue ? 'bg-[rgb(0,111,188)]' : 'bg-gray-400' }}">
                                                    <!-- ON Label -->
                                                    <div
                                                        class="absolute inset-0 flex items-center justify-start px-3 transition-all duration-200
                                {{ $fieldValue ? 'opacity-100' : 'opacity-0' }}">
                                                        <span
                                                            class="text-xs font-semibold text-white">{{ $field['on_label'] ?? 'ON' }}</span>
                                                    </div>
                                                    <!-- OFF Label -->
                                                    <div
                                                        class="absolute inset-0 flex items-center justify-end px-3 transition-all duration-200
                                {{ $fieldValue ? 'opacity-0' : 'opacity-100' }}">
                                                        <span
                                                            class="text-xs font-semibold text-white">{{ $field['off_label'] ?? 'OFF' }}</span>
                                                    </div>
                                                </div>
                                                <div
                                                    class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-all duration-300 shadow-sm
                            {{ $fieldValue ? 'transform translate-x-16' : '' }}">
                                                </div>
                                            </div>
                                        </label>
                                        <!-- ERROR MESSAGE DI BAWAH SWITCH SINGLE -->
                                        @error($errorField)
                                            <div
                                                class="text-red-600 text-sm mt-2 animate-shake flex items-center bg-red-50 p-2 rounded border border-red-200">
                                                <i class="fas fa-exclamation-circle mr-2 text-xs"></i>
                                                {{ $fieldMessages[$message] ?? $message }}
                                            </div>
                                        @enderror

                                       
                                        




                                       
                                       

                                       @elseif ($field['type'] === 'file')
    @php
        // Konfigurasi default
        $maxSize = $field['size'] ?? '100';
        $formats = $field['format'] ?? 'ZIP,RAR,DOC,DOCX,XLS,XLSX,PPT,PPTX,PDF,JPG,JPEG,PNG,AVI,MP4,3GP,MP3';
        $isMultiple = $field['multiple'] ?? true;
        $fileModel = $field['model'];
        
        // Parse allowed formats dengan cleaning
        $allowedFormats = array_map('strtolower', array_map('trim', explode(',', $formats)));
        $allowedFormats = array_filter($allowedFormats, function($format) {
            return !empty($format);
        });
        
        $currentFiles = data_get($this, $fileModel, []);
        
        // Pisahkan secara jelas existing files dan new files
        $existingFiles = [];
        $newFiles = [];
        $invalidFiles = [];
        
        $isEditMode = !empty($this->form['id']);
        
        // Handle existing files dari database
        if ($isEditMode) {
            if ($fileModel === 'form.files' && !empty($this->existingFiles)) {
                $existingFilesData = json_decode($this->existingFiles, true);
                if (is_array($existingFilesData)) {
                    $existingFiles = $existingFilesData;
                }
            } elseif ($fileModel === 'form.image' && !empty($this->existingImage)) {
                $existingFilesData = json_decode($this->existingImage, true);
                if (is_array($existingFilesData)) {
                    $existingFiles = [$existingFilesData];
                }
            }
        }
        
        // Handle new uploaded files
        if ($isMultiple) {
            $fileList = is_array($currentFiles) ? $currentFiles : [];
            
            foreach ($fileList as $file) {
                if (is_object($file)) {
                    if ($this->isValidFileFormat($file, $allowedFormats)) {
                        $newFiles[] = $file;
                    } else {
                        $invalidFiles[] = $file;
                    }
                }
            }
        } else {
            if (!empty($currentFiles) && is_object($currentFiles)) {
                if ($this->isValidFileFormat($currentFiles, $allowedFormats)) {
                    $newFiles = [$currentFiles];
                } else {
                    $invalidFiles = [$currentFiles];
                }
            }
        }
        
        // Hitung total
        $existingFileCount = count($existingFiles);
        $newFileCount = count($newFiles);
        $totalFileCount = $existingFileCount + $newFileCount;
        
        // Hitung total size hanya untuk new files
        $totalSize = 0;
        foreach ($newFiles as $file) {
            if (is_object($file) && method_exists($file, 'getSize')) {
                $totalSize += $file->getSize();
            }
        }
    @endphp

    <div class="relative">
        <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center transition-colors duration-200 ease-in-out hover:border-blue-400 bg-white">
            <input type="file" 
                   wire:model="{{ $fileModel }}" 
                   {{ $isMultiple ? 'multiple' : '' }} 
                   id="file-input-{{ $field['model'] }}" 
                   class="hidden"
                   accept="{{ $this->getAcceptAttribute($allowedFormats) }}">
            
            <div class="flex flex-col items-center justify-center space-y-2">
                <i class="fas fa-cloud-upload-alt text-3xl text-gray-400"></i>
                <div class="space-y-1">
                    <p class="text-sm font-medium text-gray-700">Klik untuk memilih file</p>
                    <p class="text-xs text-gray-500 max-w-xs mx-auto">
                        Maksimal {{ $maxSize }}MB {{ $isMultiple ? 'per file' : '' }}. Format: {{ $formats }}
                    </p>
                </div>
                <button type="button" onclick="document.getElementById('file-input-{{ $field['model'] }}').click()"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium flex items-center text-sm">
                    <i class="fas fa-folder-open mr-2"></i>Pilih File
                </button>
            </div>
        </div>
        
        <!-- Tampilkan error untuk file yang tidak valid -->
        @if (count($invalidFiles) > 0)
            <div class="mt-3 p-3 bg-red-50 border border-red-200 rounded-lg">
                <div class="flex items-center text-red-700 text-sm">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <span class="font-medium">File tidak valid:</span>
                </div>
                <ul class="mt-2 text-xs text-red-600 space-y-1">
                    @foreach ($invalidFiles as $invalidFile)
                        @php
                            $fileName = $invalidFile->getClientOriginalName();
                            $extension = strtolower($invalidFile->getClientOriginalExtension());
                        @endphp
                        <li class="flex items-center">
                            <i class="fas fa-times mr-2 text-red-500"></i>
                            {{ $fileName }} (Format .{{ strtoupper($extension) }} tidak diizinkan)
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <!-- Tampilkan existing files -->
        @if ($existingFileCount > 0)
            <div class="mt-4">
                <h4 class="text-sm font-medium text-gray-700 mb-2">File yang sudah ada:</h4>
                <div class="space-y-2">
                    @foreach ($existingFiles as $index => $file)
                        @php
                            $fileName = $file['original_name'] ?? $file['filename'] ?? 'Unknown File';
                            $fileSize = $file['size'] ?? 0;
                            $extension = $file['extension'] ?? '';
                            $filePath = $file['path'] ?? '';
                            
                            $icon = 'fa-file';
                            $iconColor = 'text-blue-500';

                            $iconMappings = [
                                'jpg|jpeg|png|gif|bmp|webp' => ['fa-file-image', 'text-green-500'],
                                'pdf' => ['fa-file-pdf', 'text-red-500'],
                                'doc|docx' => ['fa-file-word', 'text-blue-600'],
                                'xls|xlsx' => ['fa-file-excel', 'text-green-600'],
                                'ppt|pptx' => ['fa-file-powerpoint', 'text-orange-500'],
                                'zip|rar|7z|tar|gz' => ['fa-file-archive', 'text-yellow-500'],
                                'mp3|wav|aac|flac|ogg' => ['fa-file-audio', 'text-purple-500'],
                                'mp4|avi|mov|3gp|mkv|wmv|flv' => ['fa-file-video', 'text-pink-500'],
                            ];

                            foreach ($iconMappings as $pattern => [$matchedIcon, $matchedColor]) {
                                if (!empty($extension) && preg_match("/{$pattern}/", $extension)) {
                                    $icon = $matchedIcon;
                                    $iconColor = $matchedColor;
                                    break;
                                }
                            }
                        @endphp

                        <div class="flex items-center justify-between p-3 bg-gray-50 border border-gray-200 rounded-lg">
                            <div class="flex items-center space-x-3 flex-1 min-w-0">
                                <i class="fas {{ $icon }} {{ $iconColor }} text-xl flex-shrink-0"></i>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-medium text-gray-900 truncate">
                                        {{ $fileName }}
                                        <span class="text-xs text-green-600 ml-1">(Existing)</span>
                                    </p>
                                    <div class="flex items-center space-x-2 text-xs text-gray-500 mt-1">
                                        <span>{{ round($fileSize / 1024, 2) }} KB</span>
                                        <span>•</span>
                                        <span>{{ !empty($extension) ? strtoupper($extension) : 'UNKNOWN' }}</span>
                                        @if(!empty($filePath))
                                        <span>•</span>
                                        <a href="{{ asset('storage/' . $filePath) }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                                            Lihat File
                                        </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <button type="button" 
                                    wire:click="removeFileCore('{{ $fileModel }}', {{ $index }}, 'existing')"
                                    class="text-red-500 hover:text-red-700 transition-colors p-1 rounded-full hover:bg-red-100 ml-2 flex-shrink-0"
                                    title="Hapus file">
                                <i class="fas fa-trash text-sm"></i>
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
        
        <!-- Tampilkan file baru yang valid -->
        @if ($newFileCount > 0)
            <div class="mt-4">
                <h4 class="text-sm font-medium text-gray-700 mb-2">File baru yang akan diunggah:</h4>
                <div class="space-y-2">
                    @foreach ($newFiles as $index => $file)
                        @php
                            $fileName = $file->getClientOriginalName();
                            $fileSize = $file->getSize();
                            $extension = strtolower($file->getClientOriginalExtension());
                            
                            $icon = 'fa-file';
                            $iconColor = 'text-blue-500';

                            $iconMappings = [
                                'jpg|jpeg|png|gif|bmp|webp' => ['fa-file-image', 'text-green-500'],
                                'pdf' => ['fa-file-pdf', 'text-red-500'],
                                'doc|docx' => ['fa-file-word', 'text-blue-600'],
                                'xls|xlsx' => ['fa-file-excel', 'text-green-600'],
                                'ppt|pptx' => ['fa-file-powerpoint', 'text-orange-500'],
                                'zip|rar|7z|tar|gz' => ['fa-file-archive', 'text-yellow-500'],
                                'mp3|wav|aac|flac|ogg' => ['fa-file-audio', 'text-purple-500'],
                                'mp4|avi|mov|3gp|mkv|wmv|flv' => ['fa-file-video', 'text-pink-500'],
                            ];

                            foreach ($iconMappings as $pattern => [$matchedIcon, $matchedColor]) {
                                if (!empty($extension) && preg_match("/{$pattern}/", $extension)) {
                                    $icon = $matchedIcon;
                                    $iconColor = $matchedColor;
                                    break;
                                }
                            }
                        @endphp

                        <div class="flex items-center justify-between p-3 bg-white border border-gray-200 rounded-lg shadow-sm">
                            <div class="flex items-center space-x-3 flex-1 min-w-0">
                                <i class="fas {{ $icon }} {{ $iconColor }} text-xl flex-shrink-0"></i>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-medium text-gray-900 truncate">
                                        {{ $fileName }}
                                    </p>
                                    <div class="flex items-center space-x-2 text-xs text-gray-500 mt-1">
                                        <span>{{ round($fileSize / 1024, 2) }} KB</span>
                                        <span>•</span>
                                        <span>{{ strtoupper($extension) }}</span>
                                    </div>
                                </div>
                            </div>
                            <button type="button" 
                                    wire:click="removeFileCore('{{ $fileModel }}', {{ $index }}, 'new')"
                                    class="text-red-500 hover:text-red-700 transition-colors p-1 rounded-full hover:bg-red-100 ml-2 flex-shrink-0"
                                    title="Hapus file">
                                <i class="fas fa-trash text-sm"></i>
                            </button>
                        </div>
                    @endforeach
                </div>
                
                @if($isMultiple && $newFileCount > 0)
                <div class="mt-2 text-xs text-blue-600 font-medium text-right">
                    Total: {{ $newFileCount }} file • {{ round($totalSize / 1024 / 1024, 2) }} MB
                </div>
                @endif
            </div>
        @endif
        
        <!-- Info total files -->
        @if ($totalFileCount > 0)
            <div class="mt-2 text-sm text-gray-600">
                Total file: {{ $totalFileCount }} ({{ $existingFileCount }} existing + {{ $newFileCount }} baru)
            </div>
        @endif
    </div>

    @error($errorField)
        <div class="text-red-600 text-sm mt-2 animate-shake flex items-center bg-red-50 p-2 rounded border border-red-200">
            <i class="fas fa-exclamation-circle mr-2 text-xs"></i>
            {{ $fieldMessages[$message] ?? $message }}
        </div>
    @enderror

    @error($errorField)
        <div class="text-red-600 text-sm mt-2 animate-shake flex items-center bg-red-50 p-2 rounded border border-red-200">
            <i class="fas fa-exclamation-circle mr-2 text-xs"></i>
            {{ $fieldMessages[$message] ?? $message }}
        </div>
    @enderror








    
                                       @elseif ($field['type'] === 'text-editor')
    @php
        // Tentukan property berdasarkan model
        $propertyName = str_replace('form.', '', $field['model']);
        $contentValue = $this->$propertyName ?? '';
    @endphp

    <div class="relative" wire:ignore>
        <livewire:rich-text-editor 
            :model="$propertyName"
            :content="$contentValue"
            placeholder="{{ $field['placeholder'] ?? 'Ketik sesuatu...' }}"
            height="200px"
            toolbar="full"
            key="editor-{{ $propertyName }}-{{ $this->form['id'] ?? 'new' }}"
        />
    </div>
    
    @error($propertyName)
        <div class="text-red-600 text-sm mt-2 animate-shake flex items-center bg-red-50 p-2 rounded border border-red-200">
            <i class="fas fa-exclamation-circle mr-2 text-xs"></i>
            {{ $fieldMessages[$message] ?? $message }}
        </div>
    @enderror
    

                                        @else ($field['type'] === 'text')
                                        <div class="relative">
                                            <input type="text" wire:model="{{ $field['model'] }}"
                                                class="w-full rounded-lg border p-3 bg-white text-gray-900 focus:border-[rgb(0,111,188)] focus:ring-2 focus:ring-[rgb(0,111,188)] shadow-sm transition-all duration-300 {{ $hasError ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-gray-300' }}"
                                                placeholder="{{ $field['placeholder'] ?? '' }}"
                                                {{ $field['disabled'] ?? false ? 'disabled' : '' }}>
                                        </div>
                                        <!-- ERROR MESSAGE DI BAWAH INPUT TEXT -->
                                        @error($errorField)
                                            <div
                                                class="text-red-600 text-sm mt-2 animate-shake flex items-center bg-red-50 p-2 rounded border border-red-200">
                                                <i class="fas fa-exclamation-circle mr-2 text-xs"></i>
                                                {{ $fieldMessages[$message] ?? $message }}
                                            </div>
                                        @enderror

                                    @endif

                                    <!-- HELPER TEXT BOTTOM -->
                                    @if ($field['helper_bottom'] ?? false)
                                        <p class="text-xs text-gray-500 mt-1 {{ $hasError ? 'mt-3' : '' }}">
                                            {{ $field['helper_bottom'] }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <!-- JavaScript untuk toggle password -->


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

<script>
    function togglePassword(button) {
        const input = button.parentElement.querySelector('input');
        const icon = button.querySelector('i');

        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'fas fa-eye-slash';
        } else {
            input.type = 'password';
            icon.className = 'fas fa-eye';
        }
    }
</script>
