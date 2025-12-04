<div>
    <h2 class="text-lg font-semibold text-gray-800">{{ $title ?? '' }}</h2>
    <div class="container-fluid py-4">
        <div class="container mx-auto">

           {{-- HAPUS dd($_records->get()) dan ganti dengan: --}}
@php
    // Process records untuk menambahkan kolom custom
    $processedRecords = $_records->map(function ($record) {
        return (object) array_merge(
            $record->toArray(),
            [
                // Override kolom relationship dengan data yang sudah diformat
                'user_id' => $this->getNamaUser($record),
                'jenis_pengaduan_id' => $this->getJenisPelanggaran($record),
                'perihal' =>strlen($record->perihal) > 40 ? substr($record->perihal, 0, 40) . '...' : $record->perihal,
             
                'tanggal_pengaduan' => $record->tanggal_pengaduan ? $record->tanggal_pengaduan->format('d/m/Y H:i') : '-',
                // Tambahkan kolom custom sebagai property
                'complien_progress_html' => $this->getComplienProgress($record),
                'aprv_cco_html' => $this->getAprvCco($record),
            ]
        );
    });

    // Buat paginator baru dengan data yang sudah diproses
    $finalRecords = new \Illuminate\Pagination\LengthAwarePaginator(
        $processedRecords,
        $_records->total(),
        $_records->perPage(),
        $_records->currentPage(),
        ['path' => request()->url(), 'pageName' => 'page']
    );
@endphp

@include('livewire.components.table-wrapper', [
    'records' => $finalRecords, // Gunakan $finalRecords yang sudah diproses
    'columns' => [
        'code_pengaduan' => 'Kode Tracking',
        'user_id' => 'Username/Nama',
        'perihal' => 'Perihal',
        'jenis_pengaduan_id' => 'Jenis Pelanggaran',
        'tanggal_pengaduan' => 'Tanggal Aduan',
        'complien_progress' => 'Progress Status',
        'aprv_cco' => 'Persetujuan CCO',
    ],
    'selectedItems' => $selectedItems,
    'permissions' => $permissions,

    // State
    'perPage' => $perPage,
    'search' => $search,
    'filterMode' => $filterMode,
    'firstItem' => $_records->firstItem(),

    // Actions
    'onCreate' => 'create',
    'onExportExcel' => "export('excel')",
    'onExportPdf' => "export('pdf')",
    'onDeleteBulk' => 'deleteBulk',
    'onPerPageChange' => 'perPage',
    'onSearch' => 'search',
    'onOpenFilter' => 'openFilter',
    'onResetFilter' => 'resetFilter',
    'onSort' => 'sortBy',
    'onView' => 'view',
    'onEdit' => 'edit',
    'onDelete' => 'delete',
    'onSelectItem' => 'selectedItems',
    'title' => 'selectedItems',
])
        </div>

        <!-- Form Modal -->

        <!-- Filter Modal -->
        @include('livewire.components.form-filtering', [
            'showFilterModal' => $showFilterModal,
            'filters' => [
                [
                    'type' => 'text',
                    'label' => 'Kode',
                    'model' => 'filters.code_pengaduan',
                    'placeholder' => 'Cari Kode Tracking...',
                ],
                [
                    'type' => 'text',
                    'label' => 'Perihal',
                    'model' => 'filters.perihal',
                    'placeholder' => 'Cari Perihal Tracking...',
                ],
                [
                    'type' => 'select',
                    'label' => 'Jenis Pelanggaran',
                    'model' => 'filters.jenis_pengaduan_id',
                     'options' => collect($jenisPengaduanList)->mapWithKeys(function ($p) {
                                 return [
                                     $p->id => $p->data ?? $p->data_id ?? $p->data_en ?? 'No Data'
                                    ];
                                })->toArray(),
                    'placeholder' => 'Semua Jenis Pelanggaran', 
                ],
                [
                'type' => 'select',
                'label' => 'Tahun Pengaduan',
                'model' => 'filters.tahun',
                'options' => [ 
                ] + $this->tahunPengaduanList,
                'placeholder' => 'Pilih Tahun',
            ],
            ],
            'onClose' => 'closeFilterModal',
            'onReset' => 'resetFilter',
            'onApply' => 'applyFilter',
        ])

        <!-- Detail Modal -->
        @include('livewire.components.detail-modal', [
            'show' => $showDetailModal,
            'title' => $detailTitle,
            'data' => $detailData,
            'onClose' => 'closeDetailModal',
        ]) 
        @include('livewire.components.detail-modal', [
            'show' => $showDetailModal1,
            'title' => $detailTitle,
            'data' => $detailData,
            'onClose' => 'closeViewDetail',
        ]) 

        @include('livewire.components.comment', [
            'show' => $showComment,
            'title' => $detailTitle,
            'data' => $detailData,
            'onClose' => 'closeDetailModal',
        ])

        @include('livewire.components.note', [
            'show' => $ShowNote,
            'title' => $detailTitle,
            'data' => $detailData,
            'onClose' => 'closeDetailModal',
        ])
        @include('livewire.components.update-status', [
            'show' => $showuUdateStatus,
            'title' => $detailTitle,
            'data' => $detailData,
            'onClose' => 'closeDetailModal',
        ])

    </div>
</div>