<!-- resources/views/livewire/components/table.blade.php -->
@props([
    'records' => [],
    'selectedItems' => [],
    'permissions' => [],
    'columns' => [],
    'sortBy' => '',
    'getSortIcon' => '',
    'onSort' => '',
    'onView' => '',
    'onEdit' => '',
    'onDelete' => '',
    'onSelectItem' => '',
    'firstItem' => 0,
    'emptyMessage' => 'Tidak ada data ditemukan',
    'extraActions' => [],
])

{{-- {{dd($modul)}} --}}
@php
    if ($modul == 'p_tracking') {
        $permissions['delete'] = false;
        $permissions['edit'] = false;
        $permissions['comment'] = true;
    }

    if ($modul == 'complien') {
        $permissions['delete'] = false;
        $permissions['edit'] = false;
        $permissions['comment'] = true;
        $permissions['act_complien'] = true;
    }

@endphp
<div class="overflow-x-auto border border-gray-200 rounded-lg bg-white shadow-sm transition-all duration-300">
    @if (session()->has('success') || session()->has('error'))
        <div class="fixed top-4 right-4 animate-fade-in z-50">
            <div
                class="p-4 rounded-xl shadow-lg 
        {{ session()->has('success') ? 'bg-green-500' : 'bg-red-500' }}
        text-white">
                {{ session('success') ?? session('error') }}
            </div>
        </div>
    @endif

    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-[rgb(0,111,188)]">
            <tr>
                <th
                    class="px-4 py-3 text-center text-sm font-medium text-white uppercase tracking-wider border-b border-[rgb(0,95,160)] w-12">
                    #
                </th>
                <th
                    class="px-4 py-3 text-center text-sm font-medium text-white uppercase tracking-wider border-b border-[rgb(0,95,160)] w-16">
                    No
                </th>

                @foreach ($columns as $column => $label)
                    <th wire:click="{{ $onSort }}('{{ $column }}')"
                        class="px-4 py-3 text-left text-sm font-medium text-white uppercase tracking-wider border-b border-[rgb(0,95,160)] cursor-pointer group transition-all duration-200">
                        <div class="group inline-flex items-center gap-x-2 hover:text-gray-200">
                            {{ $label }}
                            <i
                                class="fas {{ $this->getSortIcon($column) }} text-gray-300 group-hover:text-gray-200 transition-all duration-200"></i>
                        </div>
                    </th>
                @endforeach

                <th
                    class="px-4 py-3 text-center text-sm font-medium text-white uppercase tracking-wider border-b border-[rgb(0,95,160)]">
                    Aksi
                </th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
    @forelse($records as $index => $record)
        <tr class="hover:bg-blue-50 transition-all duration-200 animate-fade-in">
            <td class="px-4 py-3 whitespace-nowrap text-center border-b border-gray-100">
                <input wire:model.live="{{ $onSelectItem }}" type="checkbox" value="{{ $record->id }}"
                    class="h-4 w-4 rounded border-gray-300 text-[rgb(0,111,188)] focus:ring-[rgb(0,111,188)] transition-all duration-200">
            </td>
            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 text-center border-b border-gray-100">
                {{ $firstItem + $index }}
            </td>

            <!-- Dynamic Columns -->
            @foreach ($columns as $column => $label)
                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 border-b border-gray-100">
                    @if ($column === 'is_active')
                        <span
                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $record->$column ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} transition-all duration-200">
                            {{ $record->$column ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    @elseif(str_contains($column, 'created_at') ||
                            str_contains($column, 'updated_at') ||
                            str_contains($column, 'tanggal_pengaduan'))
                        {{-- {{ $record->$column ? \Carbon\Carbon::parse($record->$column)->format('d/m/Y H:i') : '-' }} --}}
                    @elseif($column === 'complien_progress')
                        {!! $record->complien_progress_html ?? '<span class="text-gray-400">-</span>' !!}
                    @elseif($column === 'aprv_cco')
                        {!! $record->aprv_cco_html ?? '<span class="text-gray-400">-</span>' !!}
                    @else
                        {{ $record->$column ?? '-' }}
                    @endif
                </td>
            @endforeach

            <td class="px-4 py-3 whitespace-nowrap text-center border-b border-gray-100">
                <div class="flex justify-center gap-1">
                    @if ($modul == 'roles')
                        <a href="{{ route('roles.permissions', $record->id) }}"
                            class="inline-flex items-center px-3 py-1.5 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-all duration-200 text-xs font-medium">
                            <i class="fas fa-key mr-1.5 text-xs"></i>
                            <span class="whitespace-nowrap">Set Akses</span>
                        </a>
                    @else
                        <!-- Main Actions Dropdown -->
                        <div class="relative inline-block text-left" x-data="{ open: false }">
                            <button type="button" @click="open = !open"
                                class="inline-flex items-center px-3 py-1.5 bg-gray-100 border border-gray-300 rounded-lg text-xs font-medium text-gray-700 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200"
                                id="main-actions-{{ $record->id }}" aria-haspopup="true"
                                :aria-expanded="open">
                                Options
                                <i class="fas fa-chevron-down w-3 h-3 ml-2 transition-transform duration-200"
                                    :class="{ 'rotate-180': open }"></i>
                            </button>

                            <!-- Dropdown menu -->
                            <div x-show="open" x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95" @click.away="open = false"
                                class="origin-top-right absolute right-0 mt-1 w-48 rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-20"
                                role="menu" aria-orientation="vertical"
                                aria-labelledby="main-actions-{{ $record->id }}" x-cloak>
                                <div class="py-1" role="none">

                                    <!-- View Action -->
                                    @if ($permissions['view'] ?? false)
                                        <button wire:click="{{ $onView }}({{ $record->id }})"
                                            @click="open = false"
                                            class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-all duration-150 group"
                                            role="menuitem">
                                            <i
                                                class="fas fa-eye w-4 h-4 text-gray-400 mr-3 group-hover:text-blue-500"></i>
                                            <span>View</span>
                                        </button>
                                    @endif

                                    <!-- Edit & Duplicate Section -->
                                    @if ($permissions['edit'] ?? false)
                                        <div class="border-t border-gray-100 my-1"></div>
                                        <button wire:click="{{ $onEdit }}({{ $record->id }})"
                                            @click="open = false"
                                            class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-all duration-150 group"
                                            role="menuitem">
                                            <i
                                                class="fas fa-edit w-4 h-4 text-gray-400 mr-3 group-hover:text-green-500"></i>
                                            <span>Edit</span>
                                        </button>
                                    @endif

                                    @if (count($extraActions) > 0)
                                        <div class="border-t border-gray-100 my-1"></div>
                                        @foreach ($extraActions as $action)
                                            @php
                                                $hasPermission =
                                                    !isset($action['permission']) ||
                                                    ($action['permission'] &&
                                                        ($permissions[$action['permission']] ?? true));
                                            @endphp
                                            @if ($hasPermission)
                                                <button
                                                    wire:click="{{ $action['method'] }}({{ $record->id }})"
                                                    @click="open = false"
                                                    class="flex items-center w-full px-4 py-2 text-sm {{ $action['class'] ?? 'text-gray-700 hover:bg-gray-100' }} transition-all duration-150 group"
                                                    role="menuitem">
                                                    <i
                                                        class="{{ $action['icon'] ?? 'fas fa-cog' }} w-4 h-4 mr-3"></i>
                                                    <span>{{ $action['label'] }}</span>
                                                </button>
                                            @endif
                                        @endforeach
                                    @endif

                                    <!-- Comment & Notes Section -->
                                    @if ($permissions['comment'] ?? false)
                                        <div class="border-t border-gray-100 my-1"></div>
                                        <button wire:click="comment({{ $record->id }})" @click="open = false"
                                            class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-all duration-150 group"
                                            role="menuitem">
                                            <i
                                                class="fas fa-comments w-4 h-4 text-gray-400 mr-3 group-hover:text-teal-500"></i>
                                            <span>Pesan</span>
                                        </button>
                                    @endif

                                    @if ($permissions['act_complien'] ?? false)
                                        <button wire:click="addNote({{ $record->id }})" @click="open = false"
                                            class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-all duration-150 group"
                                            role="menuitem">
                                            <i
                                                class="fas fa-sticky-note w-4 h-4 text-gray-400 mr-3 group-hover:text-stone-500"></i>
                                            <span>Catatan</span>
                                        </button>
                                    @endif

                                    <!-- Status Actions Section -->
                                    @if ($permissions['act_complien'] ?? false)
                                        <div class="border-t border-gray-100 my-1"></div>
                                        <button wire:click="updateStatus({{ $record->id }}, '1')"
                                            @click="open = false"
                                            class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-all duration-150 group"
                                            role="menuitem">
                                            <i
                                                class="fas fa-check w-4 h-4 text-gray-400 mr-3 group-hover:text-lime-500"></i>
                                            <span>Lengkap [Ex]</span>
                                        </button>
                                        <button wire:click="updateStatus({{ $record->id }}, '0')"
                                            @click="open = false"
                                            class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-all duration-150 group"
                                            role="menuitem">
                                            <i
                                                class="fas fa-times w-4 h-4 text-gray-400 mr-3 group-hover:text-red-500"></i>
                                            <span>Tidak Lengkap [Ex]</span>
                                        </button>
                                    @endif

                                    <!-- Delete Action -->
                                    @if ($permissions['delete'] ?? false)
                                        <div class="border-t border-gray-100 my-1"></div>
                                        <button wire:click="{{ $onDelete }}({{ $record->id }})"
                                            wire:confirm="Apakah Anda yakin menghapus data ini?"
                                            @click="open = false"
                                            class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-all duration-150 group"
                                            role="menuitem">
                                            <i class="fas fa-trash w-4 h-4 text-red-400 mr-3"></i>
                                            <span>Delete</span>
                                        </button>
                                    @endif

                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="{{ count($columns) + 3 }}" class="px-6 py-12 text-center bg-white">
                <div class="flex flex-col items-center justify-center animate-pulse">
                    <i class="fas fa-inbox text-4xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 text-base font-medium">{{ $emptyMessage }}</p>
                </div>
            </td>
        </tr>
    @endforelse
</tbody>
    </table>
    @include('livewire.components.pagination', ['records' => $records])
</div>
