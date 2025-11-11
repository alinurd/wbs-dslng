<div class="bg-white shadow rounded p-6">
    <h2 class="text-2xl font-semibold mb-4">{{ $menuId ? 'Edit Menu' : 'Tambah Menu' }}</h2>

    <form wire:submit.prevent="save" class="space-y-4">
        <div>
            <label class="block text-sm font-semibold mb-1">Nama Menu</label>
            <input wire:model="name" type="text" class="w-full border rounded px-3 py-2" placeholder="Contoh: Risk Management">
            @error('name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-semibold mb-1">Slug</label>
            <input wire:model="slug" type="text" class="w-full border rounded px-3 py-2" placeholder="Contoh: risk">
            @error('slug') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold mb-1">Icon</label>
                <input wire:model="icon" type="text" class="w-full border rounded px-3 py-2" placeholder="fa-solid fa-list">
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1">Route</label>
                <input wire:model="route" type="text" class="w-full border rounded px-3 py-2" placeholder="/risk">
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold mb-1">Parent Menu</label>
            <select wire:model="parent_id" class="w-full border rounded px-3 py-2">
                <option value="">— Tidak Ada —</option>
                @foreach($parents as $parent)
                    <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex items-center space-x-3">
            <label><input type="checkbox" wire:model="is_active" class="mr-1"> Aktif</label>
            <div class="flex items-center">
                <label class="mr-2">Urutan</label>
                <input wire:model="order" type="number" class="w-20 border rounded px-2 py-1">
            </div>
        </div>

        <div class="flex justify-between mt-6">
            <a href="{{ route('menus.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300">Kembali</a>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Simpan</button>
        </div>
    </form>
</div>
