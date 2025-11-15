<div>
    <h2 class="text-lg font-semibold text-gray-800">{{ $title ?? '' }}</h2>
    <div class="container-fluid py-4">
        <div class="container mx-auto">
            {{-- {{dd($jenisPengaduanList)}} --}}

            @include('livewire.components.table-wrapper', [
                'records' => $_records,
                'columns' => [
                    'code_pengaduan' => 'Kode Tracking',
                    'user_id' => 'Username/Nama',
                    'perihal' => 'Perihal',
                    'jenis_pengaduan_id' => 'Jenis Pelanggaran',
                    'tanggal_pengaduan' => 'Tanggal Aduan',
                    'status' => 'Status',
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
            ])
        </div>

        <!-- Form Modal -->
        @include('livewire.components.form', [
            'showModal' => $showModal,
            'updateMode' => $updateMode,
            'form' => $form,
            'onClose' => 'closeModal',
            'onSave' => 'save',
            'title' => $title,
            'size' => 'xl',
            'cols' => 1,
            'fields' => [
                [
                    'type' => 'email',
                    'model' => 'form.data',
                    'label' => 'Email',
                    'required' => true,
                    'placeholder' => 'Masukan Data....',
                    'error' => 'form.data',
                    'messages' => [
                        'required' => 'Email wajib diisi',
                    ]
                ],
               
                [
                    'type' => 'switch-single',
                    'label' => 'Status Aktif',
                    'model' => 'form.is_active',
                    'error' => 'form.is_active',
                    'on_label' => 'AKTIF',
                    'off_label' => 'NONAKTIF',
                ],
            ],
        ])

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
    </div>
</div>