<div class="p-4 bg-white rounded-lg shadow">
    <h2 class="text-lg font-semibold mb-4">{{ $title ?? 'Data Table' }}</h2>

    {{-- Tombol Aksi --}}
    <div class="flex items-center gap-3 mb-3">
        @if($permissions['create'] ?? false)
            <x-button wire:click="openModal" class="bg-blue-600 text-white">
                Tambah {{ $title }}
            </x-button>
        @endif

        <x-button wire:click="openFilterModal" class="bg-gray-500 text-white">
            Filter Custom
        </x-button>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table id="datatable" class="min-w-full text-sm text-left text-gray-500 border">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-center">No</th>
                    @foreach ($columns as $key => $label)
                        <th class="px-4 py-2">{{ $label }}</th>
                    @endforeach
                    @if (!empty($permissions))
                        <th class="px-4 py-2 text-center">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

{{-- Modal Filter --}}
@if ($showFilterModal)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 relative">
            <h3 class="text-lg font-semibold mb-4">Filter {{ $title }}</h3>

            <div class="grid grid-cols-2 gap-4 mb-4">
                @foreach ($filters as $field => $config)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ ucfirst($field) }}</label>

                        @if ($config['type'] === 'text')
                            <input type="text" wire:model.debounce.500ms="filterValues.{{ $field }}" 
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" />
                        @elseif ($config['type'] === 'select')
                            <select wire:model="filterValues.{{ $field }}" 
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">-- Pilih --</option>
                                @foreach ($config['data'] ?? [] as $option)
                                    <option value="{{ $option }}">{{ $option }}</option>
                                @endforeach
                            </select>
                        @elseif ($config['type'] === 'radio')
                            <div class="flex gap-3">
                                @foreach ($config['data'] ?? [] as $option)
                                    <label class="flex items-center gap-1">
                                        <input type="radio" wire:model="filterValues.{{ $field }}" value="{{ $option }}">
                                        <span>{{ $option }}</span>
                                    </label>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="flex justify-end gap-3">
                <x-button wire:click="resetFilters" class="bg-gray-400 text-white">Reset</x-button>
                <x-button wire:click="applyFilters" class="bg-blue-600 text-white">Terapkan</x-button>
                <x-button wire:click="closeFilterModal" class="bg-red-500 text-white">Tutup</x-button>
            </div>
        </div>
    </div>
@endif

<script>
document.addEventListener('livewire:load', function () {
    let filters = {};
    let table = $('#datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: @json($dataUrl),
            data: function (d) {
                d.filters = filters;
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            @foreach ($columns as $key => $label)
                { data: '{{ $key }}', name: '{{ $key }}' },
            @endforeach
            @if (!empty($permissions))
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                className: 'text-center'
            }
            @endif
        ]
    });

    // Apply filter dari Livewire
    Livewire.on('apply-filters', (filterValues) => {
        filters = filterValues;
        table.ajax.reload(null, false);
    });

    // Refresh datatable manual
    Livewire.on('refresh-datatable', () => {
        table.ajax.reload(null, false);
    });
});
</script>

