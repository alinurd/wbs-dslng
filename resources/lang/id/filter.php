<?php

return [
    'title' => 'Filter Data',
    'reset' => 'Reset Filter',
    'reset_icon' => 'undo',
    
    'fields' => [
        'year' => 'Tahun',
        'complaint_type' => 'Jenis Pengaduan',
        'directorate' => 'Direktorat',
        'status' => 'Status',
        'forward_to' => 'Diteruskan Ke',
        'complaint_code' => 'Code Pengaduan',
    ],
    
    'placeholders' => [
        'year' => 'Semua Tahun',
        'type' => 'Semua Jenis',
        'directorate' => 'Semua Direktorat',
        'status' => 'Semua Status',
        'forward_to' => 'Semua',
        'complaint_code' => 'Masukkan code pengaduan...',
        'year_option' => 'Tahun :year',
        'all_years' => 'Semua Tahun',
    ],
    
    'filter_badges' => [
        'year' => 'Tahun: :value',
        'type' => 'Jenis: :value',
        'directorate' => 'Direktorat: :value',
        'status' => 'Status: :value',
        'forward_to' => 'Forward: :value',
        'complaint_code' => 'Code: :value',
    ],
    
    'messages' => [
        'no_active_filters' => 'Tidak ada filter aktif',
        'active_filters' => 'Filter Aktif',
        'apply_filters' => 'Terapkan Filter',
        'clear_all' => 'Hapus Semua',
    ],
];