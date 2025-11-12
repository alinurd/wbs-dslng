<div class="p-4 bg-white rounded-lg shadow">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold">{{ $title ?? 'Data Table' }}</h2>

        <div class="space-x-2">
            {{-- Tombol Tambah --}}
            @if ($permissions['create'] ?? false)
                <x-button wire:click="$emit('openCreateModal')" class="bg-blue-600 text-white">
                    Tambah {{ $title }}
                </x-button>
            @endif

            {{-- Tombol Filter (hanya untuk yang punya hak manage) --}}
            @if ($permissions['manage'] ?? false)
                <x-button wire:click="$emit('openFilterModal')" class="bg-green-600 text-white">
                    Filter Custom
                </x-button>
            @endif
        </div>
    </div>

    <table class="min-w-full text-sm text-left text-gray-700 border">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
            <tr>
                @foreach ($columns as $key => $label)
                    <th scope="col" class="px-4 py-2">{{ $label }}</th>
                @endforeach
                @if (($permissions['edit'] ?? false) || ($permissions['delete'] ?? false))
                    <th scope="col" class="px-4 py-2 text-center">Aksi</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse ($dataList as $row)
                <tr class="border-b hover:bg-gray-50">
                    @foreach ($columns as $key => $label)
                        <td class="px-4 py-2">{{ $row->$key }}</td>
                    @endforeach

                    @if (($permissions['edit'] ?? false) || ($permissions['delete'] ?? false))
                        <td class="px-4 py-2 text-center space-x-2">
                            {{-- Edit --}}
                            @if ($permissions['edit'] ?? false)
                                <button wire:click="$emit('editData', {{ $row->id }})"
                                        class="text-blue-600 hover:underline">
                                    Edit
                                </button>
                            @endif

                            {{-- Delete --}}
                            @if ($permissions['delete'] ?? false)
                                <button wire:click="$emit('deleteData', {{ $row->id }})"
                                        class="text-red-600 hover:underline">
                                    Hapus
                                </button>
                            @endif
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($columns) + 1 }}" class="text-center py-4 text-gray-500">
                        Tidak ada data
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4">
        {{ $dataList->links() }}
    </div>
</div>
