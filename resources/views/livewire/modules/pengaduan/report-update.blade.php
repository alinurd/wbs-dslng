<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <!-- Header -->
    @if ($updateMode)

    <div class="bg-gradient-to-r from-[#003B73] to-[#0077C8] px-6 py-4 rounded-t-lg">
        <h2 class="text-xl font-bold text-white flex items-center">
            <i class="fas fa-exclamation-circle mr-3"></i>
            Update Pengaduan &nbsp; <span class="font-bold underline">{{$recordCode}}</span>
        </h2>
        <p class="text-blue-100 text-sm mt-1">
            Silakan isi form berikut untuk memperbaharui pengaduan anda
        </p>
    </div>

    <!-- Form -->
    <form wire:submit.prevent="save" class="p-6 space-y-6">
        <!-- Informasi Pelapor -->
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-5">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-user-circle text-blue-500 mr-2"></i>
                Informasi Pelapor
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Jenis Pengaduan -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Jenis Pengaduan <span class="text-red-500">*</span>
                    </label>
                    <select wire:model="jenis_pengaduan_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('jenis_pengaduan_id') border-red-500 @enderror">
                        <option value="">Pilih Jenis Pengaduan</option>
                        @foreach ($jenisPengaduanList as $p)
                            <option value="{{ $p->id }}">{{ $p->data ?? $p->data_id }}</option>
                        @endforeach
                    </select>
                    @error('jenis_pengaduan_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nama Terlapor -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Terlapor <span class="text-red-500">*</span>
                    </label>
                    <input type="text" wire:model="nama_terlapor" placeholder="Masukkan nama lengkap"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nama_terlapor') border-red-500 @enderror">
                    @error('nama_terlapor')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Direktorat
                    </label>
                    <select wire:model="direktorat"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('direktorat') border-red-500 @enderror">
                        <option value="">Pilih Direktorat</option>
                        @foreach ($direktoratList as $dir)
                            <option value="{{ $dir['id'] }}">{{ $dir['owner_name'] }}</option>
                        @endforeach
                    </select>
                    @error('direktorat')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Perkiraan Waktu Kejadian <span class="text-red-500">*</span>
                    </label>
                    <input type="date" wire:model="waktu_kejadian"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('waktu_kejadian') border-red-500 @enderror">
                    @error('waktu_kejadian')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Uraian <span class="text-red-500">*</span>
                    </label>
                    <textarea wire:model="uraian" rows="3" placeholder="Jelaskan detail pengaduan Anda..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('uraian') border-red-500 @enderror"></textarea>
                    @error('uraian')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">
                        Minimal 10 karakter, maksimal 1000 karakter
                    </p>
                </div>

                <!-- Alamat Tempat Kejadian -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Alamat Tempat Kejadian <span class="text-red-500">*</span>
                    </label>
                    <textarea wire:model="alamat_kejadian" rows="3" placeholder="Masukkan alamat lengkap tempat kejadian"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('alamat_kejadian') border-red-500 @enderror"></textarea>
                    @error('alamat_kejadian')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Lampiran -->
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-5">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-paperclip text-blue-500 mr-2"></i>
                Lampiran
            </h3>

            <!-- File Input -->
            <div
                class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center transition-colors duration-200 ease-in-out hover:border-blue-400 bg-white">
                <input type="file" wire:model="lampiran" multiple id="file-input" class="hidden">

                <div class="flex flex-col items-center justify-center space-y-4">
                    <i class="fas fa-cloud-upload-alt text-5xl text-gray-400"></i>
                    <div class="space-y-2">
                        <p class="text-lg font-medium text-gray-700">Klik untuk memilih file</p>
                        <p class="text-sm text-gray-500 max-w-2xl">
                            Maksimal 100MB per file. Format yang didukung:
                             DOC, DOCX, XLS, XLSX, PPT, PPTX, PDF,
                            JPG, JPEG, PNG, AVI, MP4, 3GP, MP3
                        </p>
                    </div>
                    <button type="button" onclick="document.getElementById('file-input').click()"
                class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium flex items-center"
                wire:loading.remove wire:target="lampiran">
                <i class="fas fa-folder-open mr-2"></i>Pilih File
            </button>
            
            <!-- Tombol loading state -->
            <button type="button" disabled
                class="px-6 py-3 bg-blue-400 text-white rounded-lg font-medium flex items-center"
                wire:loading wire:target="lampiran">
                            <i class="fas fa-spinner fa-spin mr-1"></i>

                Mengunggah...
            </button>
{{--             
                    <button type="button" onclick="document.getElementById('file-input').click()"
                        class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium flex items-center">
                        <i class="fas fa-folder-open mr-2"></i>Pilih File
                    </button> --}}
                </div>
            </div>

            @error('temporaryFiles.*')
    <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
        <div class="flex items-center">
            <i class="fas fa-exclamation-triangle text-red-500 mr-3"></i>
            <span class="text-red-700">{{ $message }}</span>
        </div>
    </div>
@enderror

            <!-- Error untuk lampiran -->
            @error('lampiran.*')
                <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-red-500 mr-3"></i>
                        <span class="text-red-700">{{ $message }}</span>
                    </div>
                </div>
            @enderror



            <!-- Preview Lampiran -->
@if (isset($temporaryFiles) && count($temporaryFiles) > 0)
    <div class="mt-6">
        <h4 class="text-md font-medium text-gray-700 mb-3">
            File yang akan diunggah ({{ count($temporaryFiles) }}):
        </h4>
        <div class="space-y-3 max-h-60 overflow-y-auto">
            @foreach ($temporaryFiles as $index => $file)
                <div class="flex items-center justify-between p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
                    <div class="flex items-center space-x-4 flex-1">
                        <!-- File Icon -->
                        @php
                        // dd($file);
                            // Pastikan kita bisa mendapatkan nama file dengan benar
                            if (is_object($file)) {
                                $fileName = $file->getClientOriginalName();
                                $fileSize = $file->getSize();
                                $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                            } else {
                                // Fallback jika bukan object
                                $fileName = $file['original_name'] ?? 'Unknown';
                                $fileSize = $file['size'] ?? 0;
                                $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                            }
                            
                            $icon = 'fa-file';
                            $iconColor = 'text-blue-500';

                            if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'bmp'])) {
                                $icon = 'fa-file-image';
                                $iconColor = 'text-green-500';
                            } elseif (in_array($extension, ['pdf'])) {
                                $icon = 'fa-file-pdf';
                                $iconColor = 'text-red-500';
                            } elseif (in_array($extension, ['doc', 'docx'])) {
                                $icon = 'fa-file-word';
                                $iconColor = 'text-blue-600';
                            } elseif (in_array($extension, ['xls', 'xlsx'])) {
                                $icon = 'fa-file-excel';
                                $iconColor = 'text-green-600';
                            } elseif (in_array($extension, ['ppt', 'pptx'])) {
                                $icon = 'fa-file-powerpoint';
                                $iconColor = 'text-orange-500';
                            } elseif (in_array($extension, ['zip', 'rar'])) {
                                $icon = 'fa-file-archive';
                                $iconColor = 'text-yellow-500';
                            } elseif (in_array($extension, ['mp3', 'wav', 'aac'])) {
                                $icon = 'fa-file-audio';
                                $iconColor = 'text-purple-500';
                            } elseif (in_array($extension, ['mp4', 'avi', 'mov', '3gp'])) {
                                $icon = 'fa-file-video';
                                $iconColor = 'text-pink-500';
                            }
                        @endphp

                        <i class="fas {{ $icon }} {{ $iconColor }} text-2xl"></i>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">
                                {{ $fileName }}
                            </p>
                            <div class="flex items-center space-x-4 text-xs text-gray-500 mt-1">
                                <span class="flex items-center">
                                    <i class="fas fa-weight-hanging mr-1"></i>
                                    {{ round($fileSize / 1024, 2) }} KB
                                </span>
                                <span class="flex items-center">
                                    <i class="fas fa-expand-alt mr-1"></i>
                                    {{ strtoupper($extension) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <button type="button" wire:click="removeFile({{ $index }})"
                        class="text-red-500 hover:text-red-700 transition-colors p-2 rounded-full hover:bg-red-100 ml-4"
                        title="Hapus file">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            @endforeach
        </div>

        <!-- Total Files Info -->
        <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
            <div class="flex items-center justify-between text-sm">
                <span class="text-blue-700 font-medium">
                    Total: {{ count($temporaryFiles) }} file
                </span>
                <span class="text-blue-600">
                    @php
                        $totalSize = 0;
                        foreach($temporaryFiles as $file) {
                            if (is_object($file)) {
                                $totalSize += $file->getSize();
                            } elseif (isset($file['size'])) {
                                $totalSize += $file['size'];
                            }
                        }
                    @endphp
                    {{ round($totalSize / 1024 / 1024, 2) }} MB
                </span>
            </div>
        </div>
    </div>
@elseif ($lampiran && count($lampiran) > 0)
    <!-- Fallback untuk kompatibilitas -->
    <div class="mt-6">
        <h4 class="text-md font-medium text-gray-700 mb-3">File yang akan diunggah:</h4>
        <!-- ... kode lama untuk preview lampiran ... -->
    </div>
@endif

            <!-- Informasi Lampiran -->
            <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-yellow-500 mt-0.5 mr-3"></i>
                    <div class="text-sm text-yellow-700">
                        <p class="font-medium">CATATAN PENTING:</p>
                        <ul class="list-disc list-inside mt-1 space-y-1">
                            <li>Maksimal kapasitas file yang diperkenankan adalah 100MB per file</li>
                            <li>Tipe file yang diizinkan:  DOC, DOCX, XLS, XLSX, PPT, PPTX, PDF, JPG, JPEG,
                                PNG, AVI, MP4, 3GP, MP3</li>
                            <li>Data yang Anda berikan akan terjamin kerahasiaannya</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Konfirmasi -->
        <div class="border-t border-gray-200 pt-8">
            <div class="flex items-start space-x-4 bg-blue-50 p-6 rounded-2xl">
                <div class="flex items-center h-5 mt-1">
                    <input wire:model.live="confirmation" id="confirmation" name="confirmation" type="checkbox"
                        required
                        class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded
                                    @error('confirmation') border-red-500 ring-1 ring-red-500 @enderror">
                </div>
                <div class="flex-1">
                    <label for="confirmation" class="block text-sm font-medium text-gray-800 leading-6">
                        Saya menyatakan bahwa informasi yang saya berikan adalah benar dan dapat dipertanggungjawabkan
                    </label>
                    @error('confirmation')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row justify-between gap-3 pt-4 border-t border-gray-200">
            <button type="button" wire:click="resetForm"
                class="px-6 py-3 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                <i class="fas fa-redo mr-2"></i>Reset Form
            </button>
            @if ($confirmation)
                <div class="flex gap-3">
    <button type="submit"
        wire:click="submit"
        wire:loading.attr="disabled"
        wire:target="submit"
        class="px-8 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium flex items-center disabled:bg-blue-400">
        
        <i class="fas fa-paper-plane mr-2" wire:loading.remove wire:target="submit"></i>
        <i class="fas fa-spinner fa-spin mr-2" wire:loading wire:target="submit"></i>
        
        <span wire:loading.remove wire:target="submit">Kirim Pengaduan</span>
        <span wire:loading wire:target="submit">Memproses...</span>
    </button> 
</div>
            @endif
        </div>
    </form>
    
    @else
    
    <div class="m-2 p-4 bg-red-50 border border-red-200 rounded-lg">
    <div class="flex items-center justify-center ">
        <i class="fas fa-edit text-red-500 mr-3"></i>
        
        <div>
            <h2 class="font-semibold text-red-800">Update Laporan Tidak Diizinkan</h2>
            <p class="text-sm text-red-600"> Laporan dengan kode  <span class="font-bold underline">{{$recordCode}}</span> dalam proses peninjauan. </p>
            
        </div>
        
    </div>
</div>

@endif
</div>
