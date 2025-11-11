<div class="space-y-4">
    <h2 class="text-2xl font-bold">Management {{ $title }}</h2>

    @if(session()->has('message'))
        <div class="p-2 bg-green-200 text-green-800 rounded">{{ session('message') }}</div>
    @endif

    {{-- Tombol tambah --}}
    @if(!$showModal)
        <x-button wire:click="openModal" class="bg-blue-600 text-white">
            Tambah {{ $title }}
        </x-button>
    @endif

    {{-- Modal --}}
    @if($showModal)
        <div class="bg-white p-4 rounded shadow mt-4 border border-gray-200">
            @include('livewire.combo.form-fields')
            <div class="flex gap-3 mt-4">
                @if($updateMode)
                    <x-button wire:click="update" class="bg-yellow-500 text-white">Update</x-button>
                @else
                    <x-button wire:click="store" class="bg-blue-600 text-white">Simpan</x-button>
                @endif
                <x-button wire:click="closeModal" class="bg-gray-300">Batal</x-button>
            </div>
        </div>
    @endif

    {{-- Data List --}}
    <div class="bg-white p-4 rounded shadow border border-gray-200">
        <div class="mb-4">
            <x-input type="text" wire:model.debounce.500ms="search"
                     placeholder="Cari Kelompok, Data, Param Str..."
                     class="w-full md:w-1/3" />
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full table-auto border-collapse border border-gray-200">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="p-2 border">No</th>
                        <th class="p-2 border">Kelompok</th>
                        <th class="p-2 border">Data</th>
                        <th class="p-2 border">Param Int</th>
                        <th class="p-2 border">Param Str</th>
                        <th class="p-2 border">Aktif</th>
                        <th class="p-2 border">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dataList as $index => $p)
                    <tr class="hover:bg-gray-50">
                        <td class="border p-2 text-center">{{ $dataList->firstItem() + $index }}</td>
                        <td class="border p-2">{{ $p->kelompok }}</td>
                        <td class="border p-2">{{ $p->data }}</td>
                        <td class="border p-2 text-center">{{ $p->param_int ?? '-' }}</td>
                        <td class="border p-2">{{ $p->param_str ?? '-' }}</td>
                        <td class="border p-2 text-center">{{ $p->is_active ? '✔️' : '❌' }}</td>
                        <td class="border p-2 text-center space-x-2">
                            <x-button wire:click="openModal({{ $p->id }})" class="text-blue-600  hover:underline p-0">Edit</x-button>
                            <x-button wire:click="delete({{ $p->id }})" class="text-red-600  hover:underline p-0">Hapus</x-button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center p-4">Tidak ada data.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $dataList->links() }}
        </div>
    </div>
</div>