<div>
    <div class="bg-white shadow rounded p-6">
        <h2 class="text-2xl font-semibold mb-6">Edit Role: {{ $role->name }}</h2>

        <div class="overflow-x-auto">
            <table class="table-auto w-full border-collapse border border-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border border-gray-200 px-4 py-2 text-left">Module</th>
                        <th class="border border-gray-200 px-4 py-2 text-center">MANAGE</th>
                        <th class="border border-gray-200 px-4 py-2 text-center">CREATE</th>
                        <th class="border border-gray-200 px-4 py-2 text-center">EDIT</th>
                        <th class="border border-gray-200 px-4 py-2 text-center">DELETE</th>
                        <th class="border border-gray-200 px-4 py-2 text-center">VIEW</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        // Kelompokkan permission berdasarkan prefix module
                        $grouped = $permissions->groupBy(function($item) {
                            return explode('.', $item->name)[0]; // contoh: dashboard.create -> dashboard
                        });
                    @endphp

                    @foreach ($grouped as $module => $perms)
                        <tr class="hover:bg-gray-50">
                            <td class="border border-gray-200 px-4 py-2 font-semibold text-gray-700">
                                {{ ucfirst($module) }}
                            </td>

                            @foreach (['manage', 'create', 'edit', 'delete', 'view',] as $action)
                                @php
                                    $permName = $module . '.' . $action;
                                @endphp
                                <td class="border border-gray-200 px-4 py-2 text-center">
                                    @if ($permissions->pluck('name')->contains($permName))
                                        <input 
                                            type="checkbox" 
                                            wire:click="togglePermission('{{ $permName }}')" 
                                            @checked(in_array($permName, $selectedPermissions))
                                            class="h-4 w-4 text-blue-600 border-gray-300 rounded"
                                        >
                                    @else
                                        -
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6 flex justify-between">
            <a href="{{ route('roles.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold px-4 py-2 rounded">
                ← Kembali
            </a>
            <button wire:click="$refresh" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded">
                Refresh
            </button>
        </div>
    </div>
</div>
