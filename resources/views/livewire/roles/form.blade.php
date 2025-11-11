<div class="p-6 bg-white rounded shadow">
    <h2 class="text-lg font-bold mb-4">{{ $roleId ? 'Edit Role' : 'Add New Role' }}</h2>

    <div class="mb-4">
        <label class="font-bold">Role Name*</label>
        <input type="text" wire:model="name" class="border rounded w-full p-2">
        @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
    </div>

    <div class="flex gap-2">
        <button wire:click="save" class="bg-blue-500 text-white px-4 py-2 rounded">Save</button>
        <a href="{{ route('roles.index') }}" class="bg-gray-300 px-4 py-2 rounded">Back</a>
    </div>
</div>
