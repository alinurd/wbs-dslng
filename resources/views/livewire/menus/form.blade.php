<div class="bg-white shadow rounded-lg p-6">
    <h2 class="text-2xl font-semibold text-gray-800 mb-6">
        {{ $menuId ? 'Edit Menu' : 'Tambah Menu Baru' }}
    </h2>

    <form wire:submit.prevent="save" class="space-y-6">
        <!-- Nama Menu -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Nama Menu <span class="text-red-500">*</span>
            </label>
            <input 
                wire:model.defer="name" 
                type="text" 
                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                placeholder="Contoh: Risk Management"
            >
            @error('name') 
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Slug -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Slug <span class="text-red-500">*</span>
            </label>
            <input 
                wire:model.defer="slug" 
                type="text" 
                class="w-full border border-gray-300 rounded-lg px-4 py-3 {{ $menuId ? 'bg-gray-50' : '' }}"
                {{ $menuId ? 'disabled' : '' }}
                placeholder="Contoh: risk-management"
            >
            @error('slug') 
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Icon & Route -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Icon (FontAwesome)
                </label>
                <input 
                    wire:model.defer="icon" 
                    type="text" 
                    class="w-full border border-gray-300 rounded-lg px-4 py-3"
                    placeholder="fa-solid fa-shield-alt"
                >
                <p class="mt-1 text-xs text-gray-500">
                    Gunakan format FontAwesome, contoh: fa-solid fa-home
                </p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Route
                </label>
                <input 
                    wire:model.defer="route" 
                    type="text" 
                    class="w-full border border-gray-300 rounded-lg px-4 py-3"
                    placeholder="/risk-management"
                >
            </div>
        </div>

        <!-- Parent Menu -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Parent Menu
            </label>
            <select 
                wire:model.defer="parent_id" 
                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            >
                <option value="">— Tidak Ada (Menu Utama) —</option>
                @foreach($parents as $parent)
                    <option value="{{ $parent->id }}">
                        {{ $parent->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Status & Urutan -->
        <div class="flex flex-wrap items-center justify-between gap-4 p-4 bg-gray-50 rounded-lg">
            <div class="flex items-center space-x-4">
                <label class="inline-flex items-center">
                    <input 
                        wire:model.defer="is_active" 
                        type="checkbox" 
                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                        {{($is_active ? 'checked' : '')}}
                        
                    >
                    <span class="ml-2 text-sm font-medium text-gray-700">
                        Aktif
                    </span>
                </label>
            </div>

            <div class="flex items-center space-x-3">
                <label class="text-sm font-medium text-gray-700">
                    Urutan:
                </label>
                <input 
                    wire:model.defer="order" 
                    type="number" 
                    min="-1" 
                    class="w-24 border border-gray-300 rounded-lg px-3 py-2 text-center"
                >
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-between pt-6 border-t border-gray-200">
            <a 
                href="{{ route('menus.index') }}" 
                class="px-5 py-2.5 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition"
            >
                ← Kembali
            </a>
            
            <button 
                type="submit" 
                class="px-6 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition"
            >
                {{ $menuId ? 'Perbarui' : 'Simpan' }} Menu
            </button>
        </div>
    </form>
</div>