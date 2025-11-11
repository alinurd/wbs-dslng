<div>
    <div class="bg-white shadow rounded p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-semibold">Menu Management</h2>
            <a href="{{ route('menus.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">+ Tambah Menu</a>
        </div>

        <table class="min-w-full border border-gray-200">
            <thead class="bg-gray-100 text-gray-700 uppercase text-sm">
                <tr>
                    <th class="py-2 px-3 border">Nama Menu</th>
                    <th class="py-2 px-3 border">Slug</th>
                    <th class="py-2 px-3 border">Route</th>
                    <th class="py-2 px-3 border">Parent</th>
                    <th class="py-2 px-3 border text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($menus as $menu)
                <tr class="border-b hover:bg-gray-50">
                    <td class="py-2 px-3 border font-semibold">{{ $menu->name }}</td>
                    <td class="py-2 px-3 border text-gray-600">{{ $menu->slug }}</td>
                    <td class="py-2 px-3 border text-gray-600">{{ $menu->route ?? '-' }}</td>
                    <td class="py-2 px-3 border text-gray-600">{{ $menu->parent?->name ?? '-' }}</td>
                    <td class="py-2 px-3 border text-center">
                            @if($menu->default!=1)
                            <a href="{{ route('menus.edit', $menu->id) }}" class="text-blue-600 hover:underline">Edit</a> |
                            <button wire:click="confirmDelete({{ $menu->id }})" class="text-red-600 hover:underline">Hapus</button>
                            @endif
                        </td>
                    </tr>
                    @foreach($menu->children as $child)
                    
                        <tr class="border-b hover:bg-gray-50 bg-gray-50">
                            <td class="py-2 px-3 border pl-8">↳ {{ $child->name }}</td>
                            <td class="py-2 px-3 border">{{ $child->slug }}</td>
                            <td class="py-2 px-3 border">{{ $child->route ?? '-' }}</td>
                            <td class="py-2 px-3 border">{{ $child->parent?->name }}</td>
                            <td class="py-2 px-3 border text-center">
                                @if($menu->default!=1)
                                <a href="{{ route('menus.edit', $child->id) }}" class="text-blue-600 hover:underline">Edit</a> |
                                <button wire:click="confirmDelete({{ $child->id }})" class="text-red-600 hover:underline">Hapus</button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>

        @if($confirmingDeleteId)
            <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40">
                <div class="bg-white rounded p-6 shadow-lg text-center">
                    <p class="text-gray-800 mb-4">Yakin ingin menghapus menu ini?</p>
                    <button wire:click="delete({{ $confirmingDeleteId }})" class="bg-red-600 text-white px-4 py-2 rounded mr-2">Ya</button>
                    <button wire:click="$set('confirmingDeleteId', null)" class="bg-gray-300 px-4 py-2 rounded">Batal</button>
                </div>
            </div>
        @endif
    </div>
</div>
