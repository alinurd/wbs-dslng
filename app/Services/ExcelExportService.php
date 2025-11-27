<?php

namespace App\Services;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;

class ExcelExportService
{
    public function exportToExcel($data, $view, $filename, $additionalData = [])
    {
        try {
            \Log::info('=== EXCEL EXPORT SERVICE DEBUG START ===');
            \Log::info('Parameters:', [
                'view' => $view,
                'filename' => $filename,
                'data_count' => $data->count(),
                'additional_data_keys' => array_keys($additionalData)
            ]);

            // Check if view exists
            if (!View::exists($view)) {
                \Log::error('View not found:', ['view' => $view]);
                throw new \Exception("View {$view} tidak ditemukan");
            }

            // Prepare data for view
            $viewData = array_merge([
                'data' => $data,
                'getNamaUser' => function($item) {
                    $name = $item->pelapor->name ?? $item->user->name ?? 'N/A';
                    \Log::debug('getNamaUser in service:', ['item_id' => $item->id ?? 'unknown', 'result' => $name]);
                    return $name;
                },
                'getDirektoratName' => function($direktoratId) {
                    if (!$direktoratId) {
                        \Log::debug('getDirektoratName: empty direktoratId');
                        return '-';
                    }
                    $direktorat = \App\Models\Owner::find($direktoratId);
                    $result = $direktorat->owner_name ?? $direktoratId;
                    \Log::debug('getDirektoratName:', ['direktorat_id' => $direktoratId, 'result' => $result]);
                    return $result;
                },
                'getStatusInfo' => function($status, $sts_final) {
                    $statusInfo = \App\Models\Combo::where('kelompok', 'sts-aduan')
                        ->where('param_int', $status)
                        ->first();
                    if (!$statusInfo) {
                        \Log::debug('getStatusInfo: status info not found', ['status' => $status]);
                        return ['text' => 'Open', 'color' => 'gray'];
                    }
                    $result = ['text' => $statusInfo->data_id, 'color' => $statusInfo->param_str ?? 'gray'];
                    \Log::debug('getStatusInfo:', ['status' => $status, 'result' => $result]);
                    return $result;
                },
                'getJenisPelanggaran' => function($item) {
                    $result = $item->jenisPengaduan->data_id ?? 'Tidak diketahui';
                    \Log::debug('getJenisPelanggaran:', ['item_id' => $item->id ?? 'unknown', 'result' => $result]);
                    return $result;
                }
            ], $additionalData);

            \Log::info('View data prepared');

            // Render view ke HTML
            $html = View::make($view, $viewData)->render();
            \Log::info('View rendered successfully', ['html_length' => strlen($html)]);

            // Clean HTML untuk Excel
            $html = $this->cleanHtmlForExcel($html);
            \Log::info('HTML cleaned for Excel');

            // Headers untuk Excel
            $headers = [
                'Content-Type' => 'application/vnd.ms-excel',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Pragma' => 'no-cache',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Expires' => '0'
            ];

            \Log::info('Headers prepared:', $headers);
            \Log::info('=== EXCEL EXPORT SERVICE DEBUG END ===');

            // Return response dengan headers Excel
            return Response::make($html, 200, $headers);

        } catch (\Exception $e) {
            \Log::error('Excel Export Service Error:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new \Exception('Export service failed: ' . $e->getMessage());
        }
    }

    protected function cleanHtmlForExcel($html)
    {
        // Remove unnecessary tags dan clean up HTML
        $html = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $html);
        $html = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $html);
        $html = preg_replace('/<!--(.|\s)*?-->/', '', $html);
        
        return $html;
    }
}