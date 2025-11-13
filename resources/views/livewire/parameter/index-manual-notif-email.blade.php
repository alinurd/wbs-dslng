<div>
    <h2 class="text-lg font-semibold text-gray-800">{{ $title ?? '' }}</h2>
<div class="container-fluid py-4">

    <!-- Header dengan Search dan Add Button -->
    <div class="w-full">
        <!-- All in One Row Layout -->
        <div class="mb-4 flex flex-col sm:flex-row items-center gap-3">
            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto order-2 sm:order-1">
                @if ($permissions['create'] ?? false)
                    <button wire:click="create"
                        class="inline-flex items-center px-3 py-2 text-xs font-medium text-white bg-[rgb(0,111,188)] border border-[rgb(0,111,188)] rounded-md hover:bg-[rgb(0,95,160)] focus:z-10 focus:ring-1 focus:ring-[rgb(0,111,188)] focus:text-white transition-all duration-200 transform hover:scale-[1.02] active:scale-95">
                        <i class="fas fa-plus mr-1.5 text-xs"></i>
                        <span class="whitespace-nowrap">Tambah Data</span>
                    </button>
                @endif
                <div class="flex items-center gap-1.5">
                    <button wire:click="export('excel')"
                        class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-white bg-green-600 border border-green-600 rounded-md hover:bg-green-700 focus:z-10 focus:ring-1 focus:ring-green-600 focus:text-white transition-all duration-200 transform hover:scale-[1.02] active:scale-95">
                        <i class="fas fa-file-excel mr-1.5 text-xs"></i>
                        <span class="whitespace-nowrap">Excel</span>
                    </button>

                    <button wire:click="export('pdf')"
                        class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-white bg-red-600 border border-red-600 rounded-md hover:bg-red-700 focus:z-10 focus:ring-1 focus:ring-red-600 focus:text-white transition-all duration-200 transform hover:scale-[1.02] active:scale-95">
                        <i class="fas fa-file-pdf mr-1.5 text-xs"></i>
                        <span class="whitespace-nowrap">PDF</span>
                    </button>
                </div>

                <!-- Bulk Action inline -->
                <div
                    class="flex items-center gap-2 transition-all duration-300 {{ empty($selectedItems) ? 'opacity-0 invisible w-0' : 'opacity-100 visible w-auto' }}">
                    @if ($permissions['delete'] ?? false && !empty($selectedItems))
                        <button wire:click="deleteBulk"
                            wire:confirm="Apakah Anda yakin menghapus {{ count($selectedItems) }} data yang dipilih?"
                            class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-white bg-red-600 border border-red-600 rounded-md hover:bg-red-700 focus:z-10 focus:ring-1 focus:ring-red-600 focus:text-white transition-all duration-200 transform hover:scale-[1.02] active:scale-95">
                            <i class="fas fa-trash mr-1.5 text-xs"></i>
                            <span class="whitespace-nowrap">Hapus {{ count($selectedItems) }} Data</span>
                        </button>
                    @endif
                </div>
            </div>

            <!-- Search & Filter -->
            <div class="flex items-center gap-2  sm:flex-1 order-1 sm:order-2">
                <div class="flex-1 relative">
                    <div class="w-full sm:w-auto">
                        <div class="flex items-center">
                            <span class="text-gray-700 me-2 text-sm">Show:</span>
                            <select wire:model.live="perPage"
                                class="rounded-lg border  border-gray-300 bg-white text-gray-900 focus:border-[rgb(0,111,188)] focus:ring-[rgb(0,111,188)] shadow-sm transition-all duration-300 text-sm">
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                                <option value="10000">all</option>
                            </select>
                            <span class="text-gray-700 ms-2 text-sm">entries</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-1">

                    <div class="flex-1 relative">
                        <input type="text" wire:model.live="search"
                            class=" pl-9 pr-3 py-1.5 rounded-md border border-gray-300 bg-white text-gray-900 focus:border-[rgb(0,111,188)] focus:ring-1 focus:ring-[rgb(0,111,188)] shadow-sm transition-all duration-200 text-xs"
                            placeholder="Cari kelompok, data...">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-2.5">
                            <i class="fas fa-search h-3.5 w-3.5 text-gray-400"></i>
                        </div>
                    </div>


                    <button wire:click="openFilter"
                        class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:z-10 focus:ring-1 focus:ring-[rgb(0,111,188)] focus:text-[rgb(0,111,188)] transition-all duration-200 transform hover:scale-[1.02] active:scale-95">
                        <i class="fas fa-filter mr-1.5 text-xs"></i>
                        <span class="whitespace-nowrap">Filter</span>
                    </button>

                    @if ($filterMode)
                        <button wire:click="resetFilter"
                            class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:z-10 focus:ring-1 focus:ring-gray-400 focus:text-gray-800 transition-all duration-200 transform hover:scale-[1.02] active:scale-95">
                            <i class="fas fa-refresh mr-1.5 text-xs"></i>
                            <span class="whitespace-nowrap">Reset</span>
                        </button>
                    @endif
                </div>
            </div>
        </div>



        <!-- Table -->
        <div class="overflow-x-auto border border-gray-200 rounded-lg bg-white shadow-sm transition-all duration-300">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-[rgb(0,111,188)]">
                    <tr>
                        <th
                            class="px-4 py-3 text-center text-sm font-medium text-white uppercase tracking-wider border-b border-[rgb(0,95,160)] w-12">
                            {{-- <input 
                                type="checkbox" 
                                wire:model.live="selectAll"
                                 class="h-4 w-4 rounded border-gray-300 text-[rgb(0,111,188)] focus:ring-[rgb(0,111,188)] transition-all duration-200"
                            > --}}
                            #
                        </th>
                        <th
                            class="px-4 py-3 text-center text-sm font-medium text-white uppercase tracking-wider border-b border-[rgb(0,95,160)] w-16">
                            No
                        </th>
                        <th wire:click="sortBy('kelompok')"
                            class="px-4 py-3 text-left text-sm font-medium text-white uppercase tracking-wider border-b border-[rgb(0,95,160)] cursor-pointer group transition-all duration-200">
                            <div class="group inline-flex items-center gap-x-2 hover:text-gray-200">
                                Kelompok
                                <i
                                    class="fas {{ $this->getSortIcon('kelompok') }} text-gray-300 group-hover:text-gray-200 transition-all duration-200"></i>
                            </div>
                        </th>
                        <th wire:click="sortBy('data')"
                            class="px-4 py-3 text-left text-sm font-medium text-white uppercase tracking-wider border-b border-[rgb(0,95,160)] cursor-pointer group transition-all duration-200">
                            <div class="group inline-flex items-center gap-x-2 hover:text-gray-200">
                                Email
                                <i
                                    class="fas {{ $this->getSortIcon('data') }} text-gray-300 group-hover:text-gray-200 transition-all duration-200"></i>
                            </div>
                        </th>
                        
                         
                        <th wire:click="sortBy('is_active')"
                            class="px-4 py-3 text-center text-sm font-medium text-white uppercase tracking-wider border-b border-[rgb(0,95,160)] cursor-pointer group transition-all duration-200">
                            <div class="group inline-flex items-center gap-x-2 hover:text-gray-200">
                                Status
                                <i
                                    class="fas {{ $this->getSortIcon('is_active') }} text-gray-300 group-hover:text-gray-200 transition-all duration-200"></i>
                            </div>
                        </th>
                        <th wire:click="sortBy('created_at')"
                            class="px-4 py-3 text-left text-sm font-medium text-white uppercase tracking-wider border-b border-[rgb(0,95,160)] cursor-pointer group transition-all duration-200">
                            <div class="group inline-flex items-center gap-x-2 hover:text-gray-200">
                                Dibuat Pada
                                <i
                                    class="fas {{ $this->getSortIcon('created_at') }} text-gray-300 group-hover:text-gray-200 transition-all duration-200"></i>
                            </div>
                        </th>
                        <th
                            class="px-4 py-3 text-center text-sm font-medium text-white uppercase tracking-wider border-b border-[rgb(0,95,160)]">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($combos as $index => $combo)
                        <tr class="hover:bg-blue-50 transition-all duration-200 animate-fade-in">
                            <td class="px-4 py-3 whitespace-nowrap text-center border-b border-gray-100">
                                <input wire:model.live="selectedItems" type="checkbox" value="{{ $combo->id }}"
                                    class="h-4 w-4 rounded border-gray-300 text-[rgb(0,111,188)] focus:ring-[rgb(0,111,188)] transition-all duration-200">
                            </td>
                            <td
                                class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 text-center border-b border-gray-100">
                                {{ $combos->firstItem() + $index }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 border-b border-gray-100">
                                {{ $combo->kelompok }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 border-b border-gray-100">
                                {{ $combo->data }}
                            </td>
                             
                            
                            <td class="px-4 py-3 whitespace-nowrap text-center text-sm border-b border-gray-100">
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $combo->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} transition-all duration-200">
                                    {{ $combo->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 border-b border-gray-100">
                                {{ $combo->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-center border-b border-gray-100">
                                <div class="flex justify-center gap-1">
                                    @if ($permissions['view'] ?? false)
                                        <button wire:click="view({{ $combo->id }})"
                                            class="inline-flex items-center px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition-all duration-200 transform hover:scale-110 text-xs"
                                            title="View">
                                            <i class="fas fa-eye w-3 h-3"></i>
                                        </button>
                                    @endif

                                    @if ($permissions['edit'] ?? false)
                                        <button wire:click="edit({{ $combo->id }})"
                                            class="inline-flex items-center px-2 py-1 bg-green-500 text-white rounded hover:bg-green-600 transition-all duration-200 transform hover:scale-110 text-xs"
                                            title="Edit">
                                            <i class="fas fa-edit w-3 h-3"></i>
                                        </button>
                                    @endif

                                    @if ($permissions['delete'] ?? false)
                                        <button wire:click="delete({{ $combo->id }})"
                                            wire:confirm="Apakah Anda yakin menghapus data ini?"
                                            class="inline-flex items-center px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition-all duration-200 transform hover:scale-110 text-xs"
                                            title="Hapus">
                                            <i class="fas fa-trash w-3 h-3"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center bg-white">
                                <div class="flex flex-col items-center justify-center animate-pulse">
                                    <i class="fas fa-inbox text-4xl text-gray-300 mb-4"></i>
                                    <p class="text-gray-500 text-base font-medium">Tidak ada data ditemukan</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination & Per Page -->
        <div
            class="mt-6 flex flex-col sm:flex-row items-center justify-between px-4 py-3 bg-white border-t border-gray-200 rounded-b-lg gap-4 transition-all duration-300">


            <!-- Per Page Select -->


            <!-- Pagination Text -->
            <div class="text-sm text-gray-700">
                Showing {{ $combos->firstItem() }} to {{ $combos->lastItem() }} of {{ $combos->total() }} results
            </div>

            <!-- Pagination Buttons -->
            <div class="flex space-x-2">
                <!-- Previous Button -->
                @if ($combos->onFirstPage())
                    <span
                        class="px-4 py-2 text-sm text-gray-400 bg-gray-100 border border-gray-300 rounded cursor-not-allowed transition-all duration-300">

                        <i class="fas fa-arrow-left mr-1.5 text-xs"></i> Previous
                    </span>
                @else
                    <button wire:click="previousPage"
                        class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:z-10 focus:ring-1 focus:ring-[rgb(0,111,188)] focus:text-[rgb(0,111,188)] transition-all duration-200 transform hover:scale-[1.02] active:scale-95">
                        <i class="fas fa-arrow-left mr-1.5 text-xs"></i>
                        <span class="whitespace-nowrap">Previous</span>
                    </button>
                @endif

                <!-- Next Button -->
                @if ($combos->hasMorePages())
                    <button wire:click="nextPage"
                        class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:z-10 focus:ring-1 focus:ring-[rgb(0,111,188)] focus:text-[rgb(0,111,188)] transition-all duration-200 transform hover:scale-[1.02] active:scale-95">
                        <span class="whitespace-nowrap">Next</span>
                        <i class="fas fa-arrow-right ml-1.5 text-xs"></i>
                    </button>
                @else
                    <span
                        class="px-4 py-2 text-sm text-gray-400 bg-gray-100 border border-gray-300 rounded cursor-not-allowed transition-all duration-300">
                        Next
                        <i class="fas fa-arrow-right ml-1.5 text-xs"></i>
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Create/Edit Modal -->
    @if ($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto animate-fade-in" style="background-color: rgba(0,0,0,0.5)">
            <div class="flex min-h-full items-center justify-center p-4">
                <div
                    class="modal-content bg-white rounded-lg shadow-xl w-full max-w-2xl transform transition-all duration-300 scale-95 animate-scale-in">
                    <div class="modal-header bg-[rgb(0,111,188)] text-white rounded-t-lg px-6 py-4">
                        <h5 class="modal-title text-lg font-semibold">
                            <i class="fas {{ $updateMode ? 'fa-edit' : 'fa-plus' }} me-2"></i>
                            {{ $updateMode ? 'Edit Combo' : 'Tambah Combo' }}
                        </h5>
                        <button type="button" wire:click="closeModal"
                            class="btn-close btn-close-white bg-transparent border-0 text-white text-xl hover:scale-110 transition-transform duration-200">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <form wire:submit="save">
                        <div class="modal-body p-6">
                            <div class="grid grid-cols-1  gap-4">
                                <div class="mb-3">
                                    <label class="form-label font-medium text-gray-700">Email<span
                                            class="text-red-500">*</span></label>
                                    <input type="text" wire:model="data"
                                        class="w-full rounded-lg border p-2 border-gray-300 bg-white text-gray-900 focus:border-[rgb(0,111,188)] focus:ring-[rgb(0,111,188)] shadow-sm transition-all duration-300 @error('data') border-red-500 @enderror"
                                        placeholder="Masukkan data">
                                    @error('data')
                                        <div class="text-red-600 text-sm mt-1 animate-shake">{{ $message }}</div>
                                    @enderror
                                </div>
                                

                            </div>

                            <div class="mb-3 mt-4">
                                <div class="flex items-center">
                                    <input type="checkbox" wire:model="is_active"
                                        class="h-5 w-5 rounded border-gray-300 text-[rgb(0,111,188)] focus:ring-[rgb(0,111,188)] transition-all duration-300"
                                        id="is_active">
                                    <label class="form-check-label font-medium text-gray-700 ms-3" for="is_active">
                                        Status Aktif
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-t border-gray-200 px-6 py-4 flex justify-end gap-3">
                            <button type="button" wire:click="closeModal"
                                class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-all duration-300 transform hover:scale-105 font-medium">
                                <i class="fas fa-times me-2"></i>Batal
                            </button>
                            <button type="submit"
                                class="px-4 py-2 text-white bg-[rgb(0,111,188)] rounded-lg hover:bg-[rgb(0,95,160)] transition-all duration-300 transform hover:scale-105 font-medium">
                                <i class="fas {{ $updateMode ? 'fa-save' : 'fa-plus' }} me-2"></i>
                                {{ $updateMode ? 'Update' : 'Simpan' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Filter Modal -->
    @if ($showFilterModal)
        <div class="fixed inset-0 z-50 overflow-y-auto animate-fade-in" style="background-color: rgba(0,0,0,0.5)">
            <div class="flex min-h-full items-center justify-center p-4">
                <div
                    class="modal-content bg-white rounded-lg shadow-xl w-full max-w-md transform transition-all duration-300 scale-95 animate-scale-in">
                    <div class="modal-header bg-[rgb(0,111,188)] text-white rounded-t-lg px-6 py-4">
                        <h5 class="modal-title text-lg font-semibold">
                            <i class="fas fa-filter me-2"></i>
                            Filter Custom
                        </h5>
                        <button type="button" wire:click="closeFilterModal"
                            class="btn-close btn-close-white bg-transparent border-0 text-white text-xl hover:scale-110 transition-transform duration-200">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body p-6">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Filter Kelompok</label>
                                <input type="text" wire:model="filterKelompok"
                                    class="w-full rounded-lg border p-2 border-gray-300 bg-white text-gray-900 focus:border-[rgb(0,111,188)] focus:ring-[rgb(0,111,188)] shadow-sm transition-all duration-300"
                                    placeholder="Cari kelompok...">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Filter Status</label>
                                <select wire:model="filterStatus"
                                    class="w-full rounded-lg border p-2 border-gray-300 bg-white text-gray-900 focus:border-[rgb(0,111,188)] focus:ring-[rgb(0,111,188)] shadow-sm transition-all duration-300">
                                    <option value="">Semua Status</option>
                                    <option value="1">Aktif</option>
                                    <option value="0">Nonaktif</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-t border-gray-200 px-6 py-4 flex justify-end gap-3">
                        <button type="button" wire:click="resetFilter"
                            class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-all duration-300 transform hover:scale-105 font-medium">
                            <i class="fas fa-refresh me-2"></i>Reset
                        </button>
                        <button type="button" wire:click="applyFilter"
                            class="px-4 py-2 text-white bg-[rgb(0,111,188)] rounded-lg hover:bg-[rgb(0,95,160)] transition-all duration-300 transform hover:scale-105 font-medium">
                            <i class="fas fa-check me-2"></i>Submit
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Flash Message -->
    @if (session()->has('message'))
        <div class="fixed top-4 right-4 z-50 animate-slide-in-right">
            <div class="bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg flex items-center gap-3">
                <i class="fas fa-check-circle text-white"></i>
                <span class="font-medium">{{ session('message') }}</span>
                <button type="button" onclick="this.parentElement.remove()"
                    class="text-white hover:text-gray-200 transition-colors duration-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="fixed top-4 right-4 z-50 animate-slide-in-right">
            <div class="bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg flex items-center gap-3">
                <i class="fas fa-exclamation-circle text-white"></i>
                <span class="font-medium">{{ session('error') }}</span>
                <button type="button" onclick="this.parentElement.remove()"
                    class="text-white hover:text-gray-200 transition-colors duration-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    @script
        <script>
            // Handle detail modal dengan SweetAlert
            Livewire.on('showDetailModal', (data) => {
                let detailText = '';
                for (const [key, value] of Object.entries(data.data)) {
                    detailText += `<strong>${key}:</strong> ${value || '-'}<br>`;
                }

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: data.title,
                        html: detailText,
                        icon: 'info',
                        confirmButtonText: 'Tutup',
                        confirmButtonColor: '#006fbc',
                        customClass: {
                            popup: 'rounded-lg shadow-xl animate-scale-in'
                        }
                    });
                } else {
                    alert(data.title + '\n\n' + detailText.replace(/<br>/g, '\n').replace(/<[^>]*>/g, ''));
                }
            });

            // Animasi untuk berbagai event
            Livewire.on('modalOpened', () => {
                // Trigger animation for modal opening
                console.log('Modal opened with animation');
            });

            Livewire.on('selectionUpdated', () => {
                // Animation for selection update
                const bulkAction = document.querySelector('.flex.items-center.gap-3');
                if (bulkAction) {
                    bulkAction.classList.add('animate-pulse');
                    setTimeout(() => {
                        bulkAction.classList.remove('animate-pulse');
                    }, 500);
                }
            });

            Livewire.on('filterApplied', () => {
                // Animation for filter applied
                const table = document.querySelector('table');
                if (table) {
                    table.classList.add('animate-pulse');
                    setTimeout(() => {
                        table.classList.remove('animate-pulse');
                    }, 1000);
                }
            });

            // Auto hide flash message
            document.addEventListener('DOMContentLoaded', function() {
                const flashMessage = document.querySelector('.fixed.top-4.right-4');
                if (flashMessage) {
                    setTimeout(() => {
                        flashMessage.style.transform = 'translateX(100%)';
                        flashMessage.style.opacity = '0';
                        setTimeout(() => {
                            flashMessage.remove();
                        }, 300);
                    }, 5000);
                }
            });
        </script>
    @endscript

    <style>
        /* Custom Animations */
        .animate-fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }

        .animate-scale-in {
            animation: scaleIn 0.3s ease-in-out;
        }

        .animate-slide-in-right {
            animation: slideInRight 0.3s ease-in-out;
        }

        .animate-shake {
            animation: shake 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes scaleIn {
            from {
                transform: scale(0.95);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-5px);
            }

            75% {
                transform: translateX(5px);
            }
        }

        .transition-all {
            transition: all 0.3s ease-in-out;
        }
    </style>
</div>

</div>