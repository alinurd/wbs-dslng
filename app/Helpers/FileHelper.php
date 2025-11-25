<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileHelper
{
    /**
     * Upload file ke storage
     *
     * @param UploadedFile $file
     * @param string $folder
     * @param string $disk
     * @return array
     */
    public static function upload(UploadedFile $file, string $folder = 'uploads', string $disk = 'public'): array
{
    try {
        \Log::info('FileHelper upload started', [
            'file' => $file->getClientOriginalName(),
            'folder' => $folder,
            'disk' => $disk
        ]);

        // Generate unique filename
        $extension = $file->getClientOriginalExtension();
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $filename = Str::slug($originalName) . '_' . time() . '_' . Str::random(6) . '.' . $extension;
        
        \Log::info('Generated filename', ['filename' => $filename]);

        // Store file
        $path = $file->storeAs($folder, $filename, $disk);
        \Log::info('File stored successfully', ['path' => $path]);

        return [
            'path' => $path,
            'url' => Storage::disk($disk)->url($path),
            'filename' => $filename,
            'original_name' => $file->getClientOriginalName(),
            'extension' => $extension,
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'uploaded_at' => now()->toDateTimeString()
        ];
        
    } catch (\Exception $e) {
        \Log::error('FileHelper upload failed', [
            'error' => $e->getMessage(),
            'file' => $file->getClientOriginalName(),
            'trace' => $e->getTraceAsString()
        ]);
        throw $e;
    }
}
    /**
     * Upload multiple files
     *
     * @param array $files
     * @param string $folder
     * @param string $disk
     * @return array
     */
    public static function uploadMultiple(array $files, string $folder = 'uploads', string $disk = 'public'): array
    {
        $uploadedFiles = [];
        
        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $uploadedFiles[] = self::upload($file, $folder, $disk);
            }
        }
        
        return $uploadedFiles;
    }

    /**
     * Validasi file
     *
     * @param UploadedFile $file
     * @param array $allowedExtensions
     * @param int $maxSize MB
     * @return array
     */
    public static function validateFile(UploadedFile $file, array $allowedExtensions = [], int $maxSize = 10): array
    {
        $errors = [];
        
        // Check extension
        $extension = strtolower($file->getClientOriginalExtension());
        if (!empty($allowedExtensions) && !in_array($extension, $allowedExtensions)) {
            $errors[] = "Format file tidak diizinkan. Format yang didukung: " . implode(', ', $allowedExtensions);
        }
        
        // Check size (convert MB to bytes)
        $maxSizeBytes = $maxSize * 1024 * 1024;
        if ($file->getSize() > $maxSizeBytes) {
            $errors[] = "Ukuran file terlalu besar. Maksimal {$maxSize}MB.";
        }
        
        return [
            'is_valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Validasi multiple files
     *
     * @param array $files
     * @param array $allowedExtensions
     * @param int $maxSize
     * @return array
     */
    public static function validateMultipleFiles(array $files, array $allowedExtensions = [], int $maxSize = 10): array
    {
        $allValid = true;
        $errors = [];
        
        foreach ($files as $index => $file) {
            if ($file instanceof UploadedFile) {
                $validation = self::validateFile($file, $allowedExtensions, $maxSize);
                if (!$validation['is_valid']) {
                    $allValid = false;
                    $errors[$file->getClientOriginalName()] = $validation['errors'];
                }
            }
        }
        
        return [
            'is_valid' => $allValid,
            'errors' => $errors
        ];
    }

    /**
     * Get file icon berdasarkan extension
     *
     * @param string $extension
     * @return array
     */
    public static function getFileIcon(string $extension): array
    {
        $extension = strtolower($extension);
        
        $icons = [
            // Images
            'jpg' => ['icon' => 'fas fa-image', 'color' => 'text-green-500'],
            'jpeg' => ['icon' => 'fas fa-image', 'color' => 'text-green-500'],
            'png' => ['icon' => 'fas fa-image', 'color' => 'text-green-500'],
            'gif' => ['icon' => 'fas fa-image', 'color' => 'text-green-500'],
            'bmp' => ['icon' => 'fas fa-image', 'color' => 'text-green-500'],
            'svg' => ['icon' => 'fas fa-image', 'color' => 'text-green-500'],
            
            // Documents
            'pdf' => ['icon' => 'fas fa-file-pdf', 'color' => 'text-red-500'],
            'doc' => ['icon' => 'fas fa-file-word', 'color' => 'text-blue-600'],
            'docx' => ['icon' => 'fas fa-file-word', 'color' => 'text-blue-600'],
            'xls' => ['icon' => 'fas fa-file-excel', 'color' => 'text-green-500'],
            'xlsx' => ['icon' => 'fas fa-file-excel', 'color' => 'text-green-500'],
            'ppt' => ['icon' => 'fas fa-file-powerpoint', 'color' => 'text-orange-500'],
            'pptx' => ['icon' => 'fas fa-file-powerpoint', 'color' => 'text-orange-500'],
            'txt' => ['icon' => 'fas fa-file-alt', 'color' => 'text-gray-500'],
            
            // Archives
            'zip' => ['icon' => 'fas fa-file-archive', 'color' => 'text-yellow-500'],
            'rar' => ['icon' => 'fas fa-file-archive', 'color' => 'text-yellow-500'],
            '7z' => ['icon' => 'fas fa-file-archive', 'color' => 'text-yellow-500'],
            'tar' => ['icon' => 'fas fa-file-archive', 'color' => 'text-yellow-500'],
            
            // Media
            'mp3' => ['icon' => 'fas fa-file-audio', 'color' => 'text-purple-500'],
            'wav' => ['icon' => 'fas fa-file-audio', 'color' => 'text-purple-500'],
            'mp4' => ['icon' => 'fas fa-file-video', 'color' => 'text-red-400'],
            'avi' => ['icon' => 'fas fa-file-video', 'color' => 'text-red-400'],
            'mov' => ['icon' => 'fas fa-file-video', 'color' => 'text-red-400'],
            '3gp' => ['icon' => 'fas fa-file-video', 'color' => 'text-red-400'],
            
            // Default
            'default' => ['icon' => 'fas fa-file', 'color' => 'text-gray-500']
        ];
        
        return $icons[$extension] ?? $icons['default'];
    }

    /**
     * Format file size ke readable format
     *
     * @param int $bytes
     * @return string
     */
    public static function formatSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Delete file dari storage
     *
     * @param string $path
     * @param string $disk
     * @return bool
     */
    public static function delete(string $path, string $disk = 'public'): bool
    {
        if (Storage::disk($disk)->exists($path)) {
            return Storage::disk($disk)->delete($path);
        }
        
        return false;
    }

    /**
     * Delete multiple files
     *
     * @param array $paths
     * @param string $disk
     * @return array
     */
    public static function deleteMultiple(array $paths, string $disk = 'public'): array
    {
        $results = [];
        
        foreach ($paths as $path) {
            $results[$path] = self::delete($path, $disk);
        }
        
        return $results;
    }

    /**
     * Get file URL
     *
     * @param string $path
     * @param string $disk
     * @return string|null
     */
    public static function getUrl(string $path, string $disk = 'public'): ?string
    {
        if (Storage::disk($disk)->exists($path)) {
            return Storage::disk($disk)->url($path);
        }
        
        return null;
    }

    /**
     * Check if file exists
     *
     * @param string $path
     * @param string $disk
     * @return bool
     */
    public static function exists(string $path, string $disk = 'public'): bool
    {
        return Storage::disk($disk)->exists($path);
    }

    /**
     * Get allowed extensions untuk pengaduan
     *
     * @return array
     */
    public static function getAllowedPengaduanExtensions(): array
    {
        return [
            'zip', 'rar', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 
            'pdf', 'jpg', 'jpeg', 'png', 'avi', 'mp4', '3gp', 'mp3'
        ];
    }

    /**
     * Get max size untuk pengaduan (dalam MB)
     *
     * @return int
     */
    public static function getMaxPengaduanSize(): int
    {
        return 100; // 100MB
    }


    public static function getSafeFileInfo(array $fileData): array
{
    return [
        'name' => $fileData['name'] ?? $fileData['original_name'] ?? 'Unknown File',
        'size' => $fileData['size'] ?? 0,
        'formatted_size' => self::formatSize($fileData['size'] ?? 0),
        'type' => $fileData['type'] ?? $fileData['mime_type'] ?? 'unknown',
        'extension' => pathinfo($fileData['name'] ?? $fileData['original_name'] ?? '', PATHINFO_EXTENSION),
        'icon' => self::getFileIcon(pathinfo($fileData['name'] ?? $fileData['original_name'] ?? '', PATHINFO_EXTENSION)),
    ];
}
}