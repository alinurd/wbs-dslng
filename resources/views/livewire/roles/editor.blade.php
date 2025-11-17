<div>
    <div class="bg-white shadow rounded p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold">Edit Role: {{ $role->name }}</h2>
            
            <div class="w-64">
                <input 
                    type="text" 
                    wire:model.live="search"
                    placeholder="Search permissions..." 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
            </div>
        </div>

        <!-- Statistics -->
        <div class="grid grid-cols-4 gap-4 mb-6">
            <div class="bg-blue-50 p-4 rounded-lg">
                <div class="text-blue-600 font-semibold">Total Permissions</div>
                <div class="text-2xl font-bold">{{ count($permissions) }}</div>
            </div>
            <div class="bg-green-50 p-4 rounded-lg">
                <div class="text-green-600 font-semibold">Selected</div>
                <div class="text-2xl font-bold">{{ count($selectedPermissions) }}</div>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
                <div class="text-gray-600 font-semibold">Modules</div>
                <div class="text-2xl font-bold">{{ count($groupedPermissions) }}</div>
            </div>
            <div class="bg-purple-50 p-4 rounded-lg">
                <div class="text-purple-600 font-semibold">Coverage</div>
                <div class="text-2xl font-bold">
                    {{ count($permissions) > 0 ? round((count($selectedPermissions) / count($permissions)) * 100) : 0 }}%
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="table-auto w-full border-collapse border border-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border border-gray-200 px-4 py-3 text-left">Module</th>
                        <th class="border border-gray-200 px-4 py-3 text-center">VIEW</th>
                        <th class="border border-gray-200 px-4 py-3 text-center">CREATE</th>
                        <th class="border border-gray-200 px-4 py-3 text-center">EDIT</th>
                        <th class="border border-gray-200 px-4 py-3 text-center">DELETE</th>
                        <th class="border border-gray-200 px-4 py-3 text-center">INDEX</th>
                        <th class="border border-gray-200 px-4 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($groupedPermissions as $group)
                        @php
                            $module = $group['module'];
                            $modulePermissions = $group['permission_names'];
                            $selectedCount = count(array_intersect($modulePermissions, $selectedPermissions));
                            $totalCount = count($modulePermissions);
                            $isAllSelected = $selectedCount === $totalCount;
                            $isPartialSelected = $selectedCount > 0 && $selectedCount < $totalCount;
                        @endphp
                        
                        <tr class="hover:bg-gray-50 {{ $isAllSelected ? 'bg-green-50' : '' }}">
                            <td class="border border-gray-200 px-4 py-3 font-semibold text-gray-700">
                                <div class="flex items-center justify-between">
                                    <span>{{ ucfirst(str_replace('-', ' ', $module)) }}</span>
                                    @if($isPartialSelected)
                                        <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded">Partial</span>
                                    @endif
                                </div>
                            </td>

                            @foreach (['view', 'create', 'edit', 'delete', 'index'] as $action)
                                @php
                                    $permName = $module . '.' . $action;
                                    $permissionExists = in_array($permName, $modulePermissions);
                                @endphp
                                <td class="border border-gray-200 px-4 py-3 text-center">
                                    @if ($permissionExists)
                                        <input 
                                            type="checkbox" 
                                            wire:click="togglePermission('{{ $permName }}')" 
                                            @checked(in_array($permName, $selectedPermissions))
                                            class="h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                        >
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                            @endforeach

                            <td class="border border-gray-200 px-4 py-3 text-center">
                                <div class="flex justify-center space-x-2">
                                    @if(!$isAllSelected)
                                        <button 
                                            wire:click="toggleModule('{{ $module }}', 'select-all')"
                                            class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm font-medium"
                                        >
                                            Select All
                                        </button>
                                    @endif
                                    @if($isAllSelected || $isPartialSelected)
                                        <button 
                                            wire:click="toggleModule('{{ $module }}', 'deselect-all')"
                                            class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm font-medium"
                                        >
                                            Deselect All
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if(count($permissions) === 0)
            <div class="text-center py-8 text-gray-500">
                No permissions found matching your search.
            </div>
        @endif

        <div class="mt-6 flex justify-between items-center">
            <a href="{{ route('roles.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold px-6 py-2 rounded">
                ← Kembali ke Daftar Role
            </a>
            
            <div class="flex space-x-3">
                <button wire:click="$refresh" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded">
                    Refresh
                </button>
                
                <a href="{{ route('roles.index') }}" class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2 rounded">
                    Selesai
                </a>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div x-data="{ show: false, message: '' }" 
         x-on:permission-updated.window="show = true; message = 'Permissions updated successfully'; setTimeout(() => show = false, 3000)"
         x-on:error.window="show = true; message = $event.detail.message; setTimeout(() => show = false, 5000)"
         x-show="show"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform translate-y-2"
         class="fixed bottom-4 right-4 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg"
         style="display: none;">
        <span x-text="message"></span>
    </div>
</div>