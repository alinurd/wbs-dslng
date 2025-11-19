@props([
    'show' => false,
    'title' => 'Detail Data',
    'data' => [],
    'onClose' => '',
])

@if ($show)
    <div class="fixed inset-0 z-50 overflow-y-auto animate-fade-in" style="background-color: rgba(0,0,0,0.5)">
        <div class="flex min-h-full items-center justify-center p-4">
            <div
                class="modal-content bg-white rounded-lg shadow-xl w-full  transform transition-all duration-300 scale-95 animate-scale-in">
                <!-- Header -->
                <div
                    class="modal-header bg-gradient-to-r from-[rgb(0,111,188)] to-[rgb(0,95,160)] text-white rounded-t-lg px-6 py-5 shadow-lg">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="flex items-center justify-center w-10 h-10 bg-white/20 rounded-full">
                                <i class="fas fa-eye"></i>
                            </div>
                            <div>
                                <h5 class="modal-title text-xl font-bold tracking-tight">
                                    {{ $title }}
                                </h5>
                                <p class="text-white/80 text-sm">
                                    Jika Aduan {{ $data['status_ex'] }} Berikan Catatan
                                </p>
                            </div>
                        </div>
                        <button type="button" wire:click="{{ $onClose }}"
                            class="flex items-center justify-center w-9 h-9 rounded-full hover:bg-white/20 transition-all duration-300 hover:rotate-90">
                            <i class="fas fa-times text-base"></i>
                        </button>
                    </div>
                </div>

                <!-- Body -->
                <div class="modal-body p-0 max-h-[80vh] overflow-hidden flex">
                    <!-- Sidebar Informasi Pengaduan -->
                    <div class="w-1/3 border-r border-gray-200 bg-gray-50 flex flex-col">
                        <div class="p-6 flex-1 flex flex-col">
                            <h6 class="font-semibold text-gray-700 mb-4 flex items-center">
                                <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                                Informasi Pengaduan
                                {{-- <button wire:click="{{ view(13) }}"
                                                    @click="open = false"
                                                    class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-all duration-150 group"
                                                    role="menuitem">
                                                    <i
                                                        class="fas fa-eye w-4 h-4 text-gray-400 mr-3 group-hover:text-blue-500"></i>
                                                    <span>View</span>
                                                </button> --}}

                            </h6>

                            <!-- Komponen log dengan height yang sesuai -->
                            <div class="flex-1">
                                @include('livewire.components.log', [
                                    'data' => [
                                        [
                                            'id' => '7-DTRPOG',
                                            'judul_pengaduan' => 'Pelanggaran Etika - Penyalahgunaan Wewenang',
                                            'status_akhir' => 'Menunggu Approval CCO',
                                            'progress' => 70,
                                            'log_approval' => [
                                                [
                                                    'step' => 1,
                                                    'role' => 'Pelapor',
                                                    'nama' => 'Ahmad Santoso',
                                                    'status' => 'completed',
                                                    'status_text' => 'Disubmit',
                                                    'waktu' => '17/11/2024 10:30',
                                                    'catatan' => 'Laporan awal telah disampaikan dengan lengkap',
                                                    'file' => ['bukti_1.pdf', 'foto_1.jpg'],
                                                    'warna' => 'green',
                                                ],
                                                [
                                                    'step' => 2,
                                                    'role' => 'WBS Eksternal',
                                                    'nama' => 'dr. Sari Wijaya',
                                                    'status' => 'completed',
                                                    'status_text' => 'Approved',
                                                    'waktu' => '18/11/2024 14:15',
                                                    'catatan' => 'Dokumen sudah lengkap dan memenuhi syarat',
                                                    'file' => ['review_wbs_eksternal.pdf'],
                                                    'warna' => 'green',
                                                ],
                                                [
                                                    'step' => 3,
                                                    'role' => 'WBS internal',
                                                    'nama' => 'dr. santoso',
                                                    'status' => 'completed',
                                                    'status_text' => 'Approved',
                                                    'waktu' => '18/11/2024 14:15',
                                                    'catatan' => 'Dokumen sudah lengkap dan memenuhi syarat',
                                                    'file' => ['review_wbs_internal.pdf'],
                                                    'warna' => 'green',
                                                ],
                                                [
                                                    'step' => 4,
                                                    'role' => 'WBS Forward',
                                                    'nama' => 'Tim Investigasi',
                                                    'status' => 'in_progress',
                                                    'status_text' => 'Dalam Proses',
                                                    'waktu' => '20/11/2024 11:20',
                                                    'catatan' => 'Sedang dilakukan investigasi lebih lanjut',
                                                    'file' => [],
                                                    'warna' => 'yellow',
                                                ],
                                                [
                                                    'step' => 5,
                                                    'role' => 'CCO',
                                                    'nama' => '-',
                                                    'status' => 'pending',
                                                    'status_text' => 'Menunggu',
                                                    'waktu' => '-',
                                                    'catatan' => 'Menunggu hasil investigasi dari WBS Forward',
                                                    'file' => [],
                                                    'warna' => 'gray',
                                                ],
                                            ],
                                        ],
                                    ],
                                ])
                            </div>
                        </div>
                    </div>

                    <!-- Main Content Area -->
                    <div class="w-2/3 flex flex-col">
                        <!-- Header -->
                        <div class="border-b border-gray-200 px-6 py-4 bg-white">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                                    <h6 class="font-semibold text-gray-700">Berikan Catatan Anda</h6>
                                </div>
                                <div class="flex items-center space-x-3">
            <div class="flex items-center space-x-2 px-3 py-1.5 bg-yellow-50 rounded-lg border border-yellow-200">
                <div class="w-2 h-2 bg-yellow-500 rounded-full animate-pulse"></div>
                <span class="text-sm font-medium text-yellow-700">Menunggu Review</span>
            </div>
        </div>
                            </div>
                        </div>

                        <!-- Form Input -->
                        <div class="flex-1 overflow-y-auto p-6 bg-white">
                            <div class="space-y-6">
                                <!-- Textarea untuk Catatan -->
                                <div>
                                    <label for="catatan" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-edit mr-2 text-blue-500"></i>
                                        Catatan / Komentar
                                    </label>
                                    <textarea id="catatan" name="catatan" wire:model="catatan" rows="6"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out"
                                        placeholder="Tulis catatan atau komentar Anda di sini..."></textarea>
                                    @error('catatan')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Input File -->
                                <div>
                                    <label for="file_upload" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-paperclip mr-2 text-blue-500"></i>
                                        Lampiran File
                                    </label>
                                    <div
                                        class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-gray-400 transition duration-150 ease-in-out">
                                        <div class="space-y-1 text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor"
                                                fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                                <path
                                                    d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <div class="flex text-sm text-gray-600">
                                                <label for="file_upload"
                                                    class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                                    <span>Upload file</span>
                                                    <input id="file_upload" name="file_upload" type="file"
                                                        wire:model="file_upload" class="sr-only" multiple>
                                                </label>
                                                <p class="pl-1">atau drag and drop</p>
                                            </div>
                                            <p class="text-xs text-gray-500">
                                                PNG, JPG, PDF, DOCX maksimal 10MB
                                            </p>
                                        </div>
                                    </div>

                                    <!-- File Preview -->
                                    @if ($file_upload)
                                        <div class="mt-3">
                                            <p class="text-sm font-medium text-gray-700">File terpilih:</p>
                                            <div
                                                class="mt-1 flex items-center justify-between bg-gray-50 px-3 py-2 rounded-lg">
                                                <div class="flex items-center">
                                                    <i class="fas fa-file mr-2 text-gray-400"></i>
                                                    <span
                                                        class="text-sm text-gray-600">{{ $file_upload->getClientOriginalName() }}</span>
                                                </div>
                                                <button type="button" wire:click="removeFile"
                                                    class="text-red-500 hover:text-red-700">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endif

                                    @error('file_upload')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Info Tambahan -->
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-info-circle text-blue-400"></i>
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-medium text-blue-800">
                                                Informasi Penting
                                            </h3>
                                            <div class="mt-2 text-sm text-blue-700">
                                                <p>• Catatan yang Anda berikan akan tercatat dalam history pengaduan</p>
                                                <p>• File yang diupload akan disimpan sebagai lampiran</p>
                                                <p>• Pastikan informasi yang diberikan sudah benar sebelum submit</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                
                

                

                
                <!-- Footer -->
<div class="modal-footer border-t border-gray-200 px-6 py-4 bg-white rounded-b-lg">
    <div class="flex items-center justify-between w-full">
        <!-- Info Status -->
        <div class="flex items-center space-x-2 text-sm text-gray-600">
            
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center space-x-3">
            <!-- Button Lengkap -->
            <button type="button" 
                    wire:click="approveSubmission"
                    class="px-5 py-2.5 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-all duration-300 transform hover:scale-105 font-medium flex items-center space-x-2 shadow-sm">
                <i class="fas fa-check-circle me-1"></i>
                <span>Lengkap [EX]</span>
            </button>

            <!-- Button Tidak Lengkap -->
            <button type="button" 
                    wire:click="rejectSubmission"
                    class="px-5 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-all duration-300 transform hover:scale-105 font-medium flex items-center space-x-2 shadow-sm">
                <i class="fas fa-times-circle me-1"></i>
                <span>Tidak Lengkap [EX]</span>
            </button>

            <!-- Button Tutup -->
            <button type="button" 
                    wire:click="{{ $onClose }}"
                    class="px-5 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition-all duration-300 transform hover:scale-105 font-medium flex items-center space-x-2">
                <i class="fas fa-times me-1"></i>
                <span>Tutup</span>
            </button>
        </div>
    </div>
</div>

            </div>
        </div>
    </div>
@endif

<style>
    .animate-fade-in {
        animation: fadeIn 0.3s ease-in-out;
    }

    .animate-scale-in {
        animation: scaleIn 0.3s ease-in-out;
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
</style>
