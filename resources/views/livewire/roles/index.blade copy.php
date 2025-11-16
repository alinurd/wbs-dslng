<div class="p-6 bg-white rounded shadow">
    <div class="flex justify-between mb-4">
        <h2 class="text-lg font-bold">Role Management</h2>
        <a href="{{ route('roles.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded">+ Add Role</a>
    </div>

    @if (session()->has('message'))
        <div class="p-2 bg-green-100 text-green-700 rounded mb-3">{{ session('message') }}</div>
    @endif

    <table class="w-full border text-sm">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-2 py-1 text-left">#</th>
                <th class="border px-2 py-1 text-left">Name</th>
                <th class="border px-2 py-1 text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($roles as $role)
                <tr>
                    <td class="border px-2 py-1">{{ $loop->iteration }}</td>
                    <td class="border px-2 py-1">{{ $role->name }}</td>
                    <td class="border px-2 py-1 text-center space-x-1">
                        <a href="{{ route('roles.edit', $role->id) }}" class="text-blue-500">Edit</a> |
                        <a href="{{ route('roles.permissions', $role->id) }}" class="text-indigo-500">Permissions</a> |
                        <button wire:click="delete({{ $role->id }})" class="text-red-500">Delete</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
