<form wire:submit.prevent="save" class="space-y-4">
    @foreach ($fields as $field)
        @php
            $type = $field['type'] ?? 'text';
            $name = $field['field_name'];
            $label = $field['label'] ?? ucfirst($name);
            $disabled = isset($permissions['can_edit']) && !$permissions['can_edit'];
        @endphp

        <div class="form-group">
            <label class="block font-medium text-gray-700 mb-1">{{ $label }}</label>

            @if ($type === 'textarea')
                <textarea
                    wire:model.defer="formData.{{ $name }}"
                    class="w-full rounded-lg border-gray-300"
                    @disabled($disabled)
                ></textarea>

            @elseif ($type === 'select')
                <select wire:model.defer="formData.{{ $name }}"
                    class="w-full rounded-lg border-gray-300"
                    @disabled($disabled)>
                    <option value="">-- Pilih --</option>
                    @foreach (json_decode($field['options'] ?? '[]', true) as $opt)
                        <option value="{{ $opt['value'] }}">{{ $opt['label'] }}</option>
                    @endforeach
                </select>

            @elseif ($type === 'checkbox')
                <input
                    type="checkbox"
                    wire:model.defer="formData.{{ $name }}"
                    @disabled($disabled)
                >

            @elseif ($type === 'file')
                <input
                    type="file"
                    wire:model="formData.{{ $name }}"
                    class="w-full"
                    @disabled($disabled)
                >

            @else
                <input
                    type="{{ $type }}"
                    wire:model.defer="formData.{{ $name }}"
                    class="w-full rounded-lg border-gray-300"
                    @disabled($disabled)
                >
            @endif

            @error("formData.$name")
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>
    @endforeach

    <div class="pt-4">
        <button type="submit"
            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
            Simpan
        </button>
    </div>
</form>
