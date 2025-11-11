<div>
    <h2 class="text-xl font-bold mb-4">Permission Management</h2>

    @if (session()->has('message'))
        <div class="p-2 bg-green-200 text-green-800 rounded mb-3">
            {{ session('message') }}
        </div>
    @endif

    <div class="mb-3">
        <input type="text" wire:model="name" placeholder="Nama Permission" class="border p-2 rounded">
        @if($updateMode)
            <button wire:click="update" class="bg-yellow-500 text-white px-3 py-1 rounded">Update</button>
        @else
            <button wire:click="store" class="bg-blue-500 text-white px-3 py-1 rounded">Simpan</button>
        @endif
    </div>

    <table class="table-auto w-full border">
        <thead>
            <tr class="bg-gray-100">
                <th class="p-2 border">Nama Permission</th>
                <th class="p-2 border">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($permissions as $perm)
                <tr>
                    <td class="border p-2">{{ $perm->name }}</td>
                    <td class="border p-2">
                        <button wire:click="edit({{ $perm->id }})" class="text-blue-600">Edit</button> |
                        <button wire:click="delete({{ $perm->id }})" class="text-red-600">Hapus</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
