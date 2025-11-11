<div class="p-6 bg-white rounded shadow">
    <h2 class="text-lg font-semibold mb-4">Edit Role</h2>

    @if (session()->has('message'))
        <div class="p-2 bg-green-100 text-green-700 rounded mb-3">{{ session('message') }}</div>
    @endif

    <div class="mb-4">
        <label class="font-bold">Name*</label>
        <input type="text" wire:model="name" class="border rounded w-full p-2">
    </div>

    <div class="mb-4">
        <label class="font-bold">Show Data*</label>
        <select wire:model="show_data" class="border rounded w-full p-2">
            <option value="All">All</option>
            <option value="Only Owner">Only Owner</option>
        </select>
    </div>

    <div class="overflow-x-auto mt-4">
        <table class="min-w-full text-sm border-collapse border border-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="border px-3 py-2 text-left">Module</th>
                    <th class="border px-3 py-2 text-center">MANAGE</th>
                    <th class="border px-3 py-2 text-center">CREATE</th>
                    <th class="border px-3 py-2 text-center">EDIT</th>
                    <th class="border px-3 py-2 text-center">DELETE</th>
                    <th class="border px-3 py-2 text-center">VIEW</th>
                    <th class="border px-3 py-2 text-center">PROPOSE</th>
                    <th class="border px-3 py-2 text-center">APPROVE</th>
                    <th class="border px-3 py-2 text-center">RESET</th>
                </tr>
            </thead>
            <tbody>
                @foreach($modules as $moduleName => $permissions)
                    <tr class="bg-gray-100 font-bold">
                        <td class="border px-3 py-2">{{ $moduleName }}</td>
                        @foreach(['manage','create','edit','delete','view','propose','approve','reset'] as $action)
                            <td class="border text-center">
                                @php
                                    $permName = strtolower($moduleName).'.'.$action;
                                @endphp
                                <input type="checkbox"
                                       wire:click="togglePermission('{{ $permName }}')"
                                       @checked(in_array($permName, $assigned))
                                >
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4 flex gap-2">
        <button wire:click="save" class="bg-blue-500 text-white px-4 py-2 rounded">Save</button>
        <a href="/roles" class="bg-gray-300 px-4 py-2 rounded">Back</a>
    </div>
</div>
