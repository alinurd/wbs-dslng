<div class="space-y-4">

    {{-- Flash Message --}}
    <x-flash.message />

    {{-- Header --}}
    <x-table.header />

    {{-- Bulk Action --}}
    <x-table.bulk-action :selectedRows="$selectedRows" />

    {{-- Table --}}
    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="table-auto w-full">
            <thead>
                <tr class="bg-gray-100 text-gray-600">
                    <th class="px-4 py-2">
                        <input type="checkbox" wire:model="selectAll">
                    </th>
                    <th class="px-4 py-2">Nama</th>
                    <th class="px-4 py-2">Email</th>
                    <th class="px-4 py-2">Dibuat</th>
                    <th class="px-4 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $item)
                    <x-table.row :item="$item" />
                @empty
                    <x-table.empty />
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <x-table.pagination :data="$data" />

    {{-- Form Modal --}}
    <x-modal.form :openForm="$openForm" :isEdit="$isEdit">
        {{-- Slot Field Form --}}
        @include('livewire.partials.form-fields')
    </x-modal.form>

    {{-- Filter Modal --}}
    <x-modal.filter :filterOpen="$filterOpen">
        {{-- Slot Filter --}}
        @include('livewire.partials.filter-fields')
    </x-modal.filter>

</div>
