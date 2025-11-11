<div>
    <h2 class="text-xl font-bold mb-4">User Management</h2>

    @if (session()->has('message'))
        <div class="p-2 bg-green-200 text-green-800 rounded mb-3">
            {{ session('message') }}
        </div>
    @endif

    <div class="mb-3">
        <input type="text" wire:model="name" placeholder="Nama" class="border p-2 rounded">
        <input type="email" wire:model="email" placeholder="Email" class="border p-2 rounded">
    </div>

    <div class="mb-3">
        <label class="font-bold">Roles:</label>
        @foreach($roles as $role)
            <label class="ml-2">
                <input type="checkbox" wire:model="userRoles" value="{{ $role->name }}">
                {{ $role->name }}
            </label>
        @endforeach
    </div>

    <div class="mb-3">
        <label class="font-bold">Permissions:</label>
        @foreach($permissions as $perm)
            <label class="ml-2">
                <input type="checkbox" wire:model="userPermissions" value="{{ $perm->name }}">
                {{ $perm->name }}
            </label>
        @endforeach
    </div>

    @if($updateMode)
        <button wire:click="update" class="bg-yellow-500 text-white px-3 py-1 rounded">Update</button>
    @else
        <button wire:click="store" class="bg-blue-500 text-white px-3 py-1 rounded">Simpan</button>
    @endif

    <hr class="my-4">

    <table class="table-auto w-full border">
        <thead>
            <tr class="bg-gray-100">
                <th class="p-2 border">Nama</th>
                <th class="p-2 border">Email</th>
                <th class="p-2 border">Roles</th>
                <th class="p-2 border">Permissions</th>
                <th class="p-2 border">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                    <td class="border p-2">{{ $user->name }}</td>
                    <td class="border p-2">{{ $user->email }}</td>
                    <td class="border p-2">
                        @foreach($user->roles as $r)
                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-sm">{{ $r->name }}</span>
                        @endforeach
                    </td>
                    <td class="border p-2">
                        @foreach($user->permissions as $p)
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-sm">{{ $p->name }}</span>
                        @endforeach
                    </td>
                    <td class="border p-2">
                        <button wire:click="edit({{ $user->id }})" class="text-blue-600">Edit</button> |
                        <button wire:click="delete({{ $user->id }})" class="text-red-600">Hapus</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
