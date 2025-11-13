<div>
    <form wire:submit.prevent="saveForm">
        @foreach ($schema as $field)
            <div class="mb-3">
                <label class="form-label">{{ $field['label'] ?? ucfirst($field['name']) }}</label>
                @switch($field['type'])
                    @case('text')
                        <input type="text" wire:model="formData.{{ $field['name'] }}" class="form-control" />
                        @break

                    @case('select')
                        <select wire:model="formData.{{ $field['name'] }}" class="form-select">
                            <option value="">Pilih...</option>
                            @foreach ($field['options'] ?? [] as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @break

                    @case('textarea')
                        <textarea wire:model="formData.{{ $field['name'] }}" class="form-control"></textarea>
                        @break
                @endswitch
                @error("formData.{$field['name']}") <small class="text-danger">{{ $message }}</small> @enderror
            </div>
        @endforeach

        <button type="submit" class="btn btn-primary" 
            @if(!$permissions['can_edit']) disabled @endif>
            Simpan
        </button>
    </form>
</div>
