<?php

namespace App\Livewire;

use App\Helpers\FileHelper;
use App\Models\Audit as AuditLog;
use App\Models\Combo;
use App\Models\Comment;
use App\Models\LogApproval;
use App\Models\Owner;
use App\Models\Pengaduan;
use App\Services\EmailService;
use App\Services\ExcelExportService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;


use Livewire\Component;
use Livewire\WithPagination;

abstract class Root extends Component
{
    use WithPagination;

    // ================= PROPERTIES ==================
    public $title = 'Title';
    public $modul = 'combo';
    public $views = 'index';
    public $model;

    public $search = '';
    public $perPage = 10;
    public $page = 1;
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    public $showModal = false;
    public $showFilterModal = false;
    public $updateMode = false;
    public $showDetailModal = false;
    public $showDetailModal1 = false; 
    public $showComment = false;
    public $ShowNote = false;
    public $showuUdateStatus = false;
    public $detailTitle = '-';
    public $filterMode = false;

    public $form = [];
    public $userInfo = [];
    public $formDefault = [];
    public $filters = [];
    public $selectedItems = [];
    public $detailData = [];
    public $selectAll = false;

    public $rules = []; // child dapat override dengan property
    public $locale;

    public $jenisPengaduanList = []; // child dapat override dengan property
    public $stsPengaduanList = []; // child dapat override dengan property
    public $RolesList = []; // child dapat override dengan property
    public $saluranList = []; // child dapat override dengan property
    public $fwdList = []; // child dapat override dengan property
    public $direktoratList = []; // child dapat override dengan property
    public $tahunPengaduanList = []; // child dapat override dengan property
    public $bulanList = []; // child dapat override dengan property
    public $pengaduanAll = []; // child dapat override dengan property



    // Properties untuk file upload di chat
    public $fileUpload = null;
    public $fileDescription = '';
    public $uploadedFiles = [];
    public $lampiran = [];
    public $attachFile = null;
    public $isAdmin = false;
    public $pelapor = false;


    //verif
    public $verification_code;
    public $user;
    public $isVerif;
    public $canResend = true;
    public $countdown = 0;
    public $showCountdown = false;



    public $previewData = [];
public $previewTotal = 0;
public $previewMonth = '';
public $showPreviewModal = false;


    // ================== MOUNT =====================
    public function mount()
    {
        $this->export();
        // Simpan default form
        $this->formDefault = is_array($this->form) ? $this->form : [];
        // Title otomatis jika tidak didefinisikan
        $this->title = $this->title ?: class_basename($this->model);
        // Hak akses
        $this->userInfo();
        if($this->userInfo['role']['id'] === 1){
            $this->isAdmin=true;
        }
        if($this->userInfo['role']['id'] === 3){
            $this->pelapor=true;
        }
        $this->isVerif = !is_null($this->userInfo['user']['email_verified_at']);
        $this->checkResendCooldown();
        can_any([strtolower($this->modul) . '.view']);

        // Locale
        $this->locale = Session::get('locale', config('app.locale'));
        App::setLocale($this->locale);
    }

    public function userInfo()
    {
        $user = \auth()->user();
        $role = $user->roles()->first();


        // if (!empty($stsArray)) {
        $combo = Combo::select('id', 'data_id', 'data_en', 'param_int', 'param_str')->where('kelompok', 'sts-aduan')->whereIn('param_int', json_decode($role->sts, true))->get()->toarray();
        // }

        $this->userInfo = [
            'user' => $user,
            'role' => $role,
            // 'sts_array' => $stsArray,
            'sts' => $combo ?? null
        ];
    }
    // ================ QUERY BUILDER =================
    public function query()
    {
        $query = ($this->model)::query();

        // Filter default jika ada
        if (method_exists($this, 'filterDefault')) {
            $filterDefault = $this->filterDefault();
            if (is_array($filterDefault) && count($filterDefault)) {
                $query->where(function ($q) use ($filterDefault) {
                    foreach ($filterDefault as $col) {
                        if (!empty($col['f'])) {
                            $q->Where($col['f'], $col['v']);
                        }
                    }
                });
            }
        }

        // Search
        if ($this->search && method_exists($this, 'columns')) {

            $columns = $this->columns();
            if (is_array($columns) && count($columns)) {
                $query->where(function ($q) use ($columns) {
                    foreach ($columns as $col) {
                        $q->orWhere($col, 'like', "%{$this->search}%");
                    }
                });
            }
        }

        // Additional filters
        if (is_array($this->filters)) {
            // dd($this->filters);
            foreach ($this->filters as $key => $val) {
                if ($val !== '' && $val !== null) {
                    $query->where($key, 'like', "%$val%");
                }
            }
        }

        return $query;
    }

    public function loadRecords()
    {
        try { 
            $query = $this->query();
 
            $query->orderBy($this->sortField, $this->sortDirection);
 
            $this->_records = $query->paginate($this->perPage);
 
            if (method_exists($this, 'formatRecords')) {
                $this->formatRecords();
            }
        } catch (\Exception $e) { 
            $this->_records = $this->model::query()->paginate($this->perPage);
        }
    }

    protected function formatRecords()
    {
        // Default implementation - child classes can override
        if ($this->_records && method_exists($this, 'formatData')) {
            $formattedCollection = $this->formatData($this->_records->getCollection());
            $this->_records->setCollection($formattedCollection);
        }
    }


    // ===================== RENDER ======================
    public function render()
    {
        $this->loadRecords();

        return view($this->viewPath(), [
            '_records'    => $this->_records,

            'title'       => $this->title,
            'permissions' => module_permissions(strtolower($this->modul))['can'] ?? []
        ]);
    }


    protected function viewPath()
    {
        return 'livewire.' . strtolower($this->views);
    }


    // ======================= CRUD =======================

    public function create()
    {
        can_any([strtolower($this->modul) . '.create']);
        $this->resetForm();
        $this->updateMode = false;
        $this->showModal = true;

        $this->dispatch('modalOpened');
    }


    public function edit($id)
    {
        can_any([strtolower($this->modul) . '.edit']);

        $record = ($this->model)::findOrFail($id);

        foreach ($this->formDefault as $key => $default) {
            $this->form[$key] = $record->$key ?? $default;
        }

        $this->form['id'] = $id;

        $this->updateMode = true;
        $this->showModal = true;

        $this->dispatch('modalOpened');
    }

    // Di App\Livewire\Root class, perbaiki method save()
    public function save()
    {
        // Validasi - prioritaskan method rules(), lalu property $rules
        $validationRules = [];

        if (method_exists($this, 'rules')) {
            $validationRules = $this->rules();
        } elseif (!empty($this->rules)) {
            $validationRules = $this->rules;
        }

        if (!empty($validationRules)) {
            $this->validate($validationRules);
        }

        $modelClass = $this->model;
        $action = 'unknown';
        $record = null;

        // Payload hanya akan mengambil field yang ada di formDefault
        $payload = collect($this->form)
            ->only(array_keys($this->formDefault))
            ->toArray();

        // Call saving hook untuk modifikasi payload - FIXED
        if (method_exists($this, 'saving')) {
            $payload = $this->saving($payload);
        }

        if ($this->updateMode) {
            can_any([strtolower($this->modul) . '.edit']);
            $record = $modelClass::findOrFail($this->form['id']);

            // Simpan data lama untuk audit trail
            $oldData = $record->getOriginal();

            $record->update($payload);
            $action = 'update';

            // Log audit trail
            $this->logAudit($action, $record, [
                'new_data' => $payload,
                'old_data' => $oldData
            ]);
        } else {
            $action = 'create';
            can_any([strtolower($this->modul) . '.create']);
            $record = $modelClass::create($payload);

            // Log audit trail
            $this->logAudit($action, $record, $payload);
        }

        // Call saved hook - FIXED
        if (method_exists($this, 'saved')) {
            $this->saved($record, $action);
        }

        $this->loadRecords();

        $this->closeModal();
        $this->resetPage();
        $this->dispatch('dataSaved');
    }
    protected function saved($record, $action)
    {
        // NOTIFIKASI SUDAH DIHANDLE OLEH logAudit
        // Hanya reset form/lampiran jika perlu
        if (method_exists($this, 'resetLampiran')) {
            $this->resetLampiran();
        }
    }




    public function delete($id)
    {
        can_any([strtolower($this->modul) . '.delete']);

        $record = ($this->model)::findOrFail($id);

        // Simpan data lengkap record sebelum dihapus
        $oldData = $record->toArray();

        // Log audit trail SEBELUM menghapus dengan data lengkap
        $this->logAudit('delete', $record, ['deleted_data' => $oldData]);

        $record->delete();

        // NOTIFIKASI SUDAH DIHANDLE OLEH logAudit
        $this->resetPage();
        $this->loadRecords();
        $this->dispatch('dataDeleted');
    }
    public function deleteBulk()
    {
        can_any([strtolower($this->modul) . '.delete']);

        if (count($this->selectedItems)) {
            // Ambil data sebelum dihapus untuk audit trail
            $records = ($this->model)::whereIn('id', $this->selectedItems)->get();

            // Log audit trail untuk setiap record dengan data lengkap
            foreach ($records as $record) {
                $oldData = $record->toArray();
                $this->logAudit('delete', $record, ['deleted_data' => $oldData]);
            }

            // Hapus records
            ($this->model)::whereIn('id', $this->selectedItems)->delete();

            // Notifikasi untuk bulk delete
            $this->notify('success', $this->getAuditMessage('bulk_delete', null, []));
        }

        $this->selectedItems = [];

        $this->loadRecords();
        $this->dispatch('bulkDeleteCompleted');
    }

    // ====================== FILTER ========================
    public function openFilter()
    {
        $this->showFilterModal = true;
    }
    public function closeFilterModal()
    {
        $this->showFilterModal = false;
    }


    public function applyFilter()
    {
        $this->filterMode = true;
        $this->showFilterModal = false;
        $this->resetPage();
        $this->notify('success', 'Filter diterapkan.');
    }


    public function resetFilter()
    {
        foreach ($this->filters as $key => $val) {
            $this->filters[$key] = '';
        }

        $this->filterMode = false;
        $this->showFilterModal = false;

        $this->notify('success', 'Filter direset.');
    }


    // ================= VIEW MODAL ======================
    public function view($id)
    {
        can_any([strtolower($this->modul) . '.view']);
        $record = ($this->model)::findOrFail($id);

        $this->dispatch('showDetailModal', [ 
            'title' => "Detail " . $this->title,
            'data'  => $record->toArray()
        ]);
    }
    public function comment($id)
    {
        // can_any([strtolower($this->modul).'.view']);
        $record = ($this->model)::findOrFail($id);

        $this->dispatch('showComment', [
            'title' => "Comment " . $this->title,
            'data'  => $record->toArray()
        ]);
    }

    public function updateStatus($id, $status)
    {
        // can_any([strtolower($this->modul).'.view']);
        $record = ($this->model)::findOrFail($id);

        $this->dispatch('showuUdateStatus', [
            'title' => "Update Status " . $this->title,
            'data'  => $record->toArray(),
        ]);
    }
    public function addNote($id)
    {
        // can_any([strtolower($this->modul).'.view']);
        $record = ($this->model)::findOrFail($id);

        $this->dispatch('ShowNote', [
            'title' => "Note " . $this->title,
            'data'  => $record->toArray()
        ]);
    }


    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->showDetailModal1 = false;
        $this->showComment = false;
        $this->showuUdateStatus = false;
        $this->ShowNote = false;
        $this->detailData = [];
        $this->detailTitle = '';
    }

    // =================== SUPPORT =======================
    public function closeModal()
    {
        $this->showDetailModal = false;
        $this->showDetailModal1 = false;
        $this->detailData = [];
        $this->detailTitle = '';

        $this->showModal = false;
        $this->resetForm();
        $this->dispatch('modalClosed');
    }


    protected function resetForm()
    {
        $this->form = $this->formDefault;
        $this->resetErrorBag();
    }


    // =================== SORTING =======================
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        $this->dispatch('tableSorted');
    }


    public function getSortIcon($field)
    {
        if ($this->sortField !== $field) return 'fa-sort';
        return $this->sortDirection === 'asc' ? 'fa-sort-up' : 'fa-sort-down';
    }


    // =================== SELECT ALL =====================
    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedItems = $this->query()->pluck('id')->toArray();
        } else {
            $this->selectedItems = [];
        }
    }


    // =================== PAGINATION =====================
    public function gotoPage($page)
    {
        $this->setPage($page);
    }

    public function previousPage()
    {
        $this->setPage(max($this->page - 1, 1));
    }

    public function nextPage()
    {
        $this->setPage($this->page + 1); // PERBAIKAN: $this->page + 1
    }

    public function resetPage()
    {
        $this->setPage(1);
    }


    // =================== EXPORT =========================
public function export($type = 'excel')
{
    try {
        $currentMonth = date('m');
        $currentYear = date('Y');

        // Hitung data pengaduan
        $dataPengaduan = Pengaduan::whereYear('tanggal_pengaduan', $currentYear)
            ->whereMonth('tanggal_pengaduan', $currentMonth)
            ->count();

        // Ambil data untuk export
        $data = $this->query()->get();

        // Validasi jika tidak ada data
        if ($data->isEmpty()) {
            $this->notify('warning', 'Tidak ada data untuk diexport.');
            return;
        }

        switch ($type) {
            case 'excelReportFull':
                return $this->exportToExcel($data);
            case 'excelReportComplien':
                return $this->exportToExcelComplien($data);
            case 'excelReportJenis':
                return $this->exportToExcelJenis();
                
            case 'excel': 
                $this->notify('info', 'Fitur export Excel sedang dalam pengembangan.');
                return;
            case 'pdf': 
                $this->notify('info', 'Fitur export PDF sedang dalam pengembangan.');
                return;
                
            case 'preview':
                return $this->showPreview($data);
            case 'previewJenis':
                return $this->previewJenis();
                
            default:
                $this->notify('error', 'Jenis export tidak valid.');
                return;
        }

    } catch (\Exception $e) {
        $this->notify('error', 'Terjadi kesalahan saat export: ' . $e->getMessage());
    }
}

private function showPreview($data)
{
    try {
        $this->previewData = $data;
        $this->previewTotal = $data->count();
        $this->previewMonth = $this->getPeriodInfo();
        $this->showPreviewModal = true;
        $this->notify('success', "Preview data berhasil ({$this->previewTotal} records)");
    } catch (\Exception $e) {
        $this->notify('error', "Preview gagal: " . $e->getMessage());
    }
}

// Tambahkan method ini di class Root
public function closePreviewModal()
{
    $this->showPreviewModal = false;
    $this->previewData = [];
    $this->previewTotal = 0;
    $this->previewMonth = '';
}

protected function exportExcel($data, $view, $filename = null, $additionalData = [])
{
    try {
        $filename = $filename ?: 'export-' . date('Y-m-d-H-i-s') . '.xls';

         $total = is_array($data) ? count($data) : $data->count();

        $viewData = array_merge([
            'data' => $data, // kita kirim sebagai rows, bukan data
            'periode' => $this->previewMonth,
            'exportTime' => now()->format('d/m/Y H:i'),
            'totalRecords' => $total
        ], $additionalData);

        $html = view($view, $viewData)->render();

        // strip script/style/comment
        $html = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $html);
        $html = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $html);
        $html = preg_replace('/<!--(.|\s)*?-->/', '', $html);

        return response()->streamDownload(
            fn() => print($html),
            $filename,
            ['Content-Type' => 'application/vnd.ms-excel']
        );

    } catch (\Exception $e) {
        \Log::error('Export Excel Error: ' . $e->getMessage());
        $this->notify('error', 'Export Excel gagal: ' . $e->getMessage());
        return null;
    }
}





private function exportToExcel($data)
{
    // Tinggal panggil function di atas dengan parameter yang sesuai
    return $this->exportExcel(
        $data, 
        'exports.pengaduan', // view template
        'laporan-pengaduan-' . date('Y-m-d-H-i-s') . '.xls', // filename
        [ // additional data untuk view
            'periodInfo' => $this->getPeriodInfo(),
            'filterData' => $this->getFilterData(),
            'getNamaUser' => function($item) {
                return $item->pelapor->name ?? $item->user->name ?? 'N/A';
            },
            'getDirektoratName' => function($direktoratId) {
                if (!$direktoratId) return '-';
                $direktorat = \App\Models\Owner::find($direktoratId);
                return $direktorat->owner_name ?? $direktoratId;
            },
            'getStatusInfo' => function($status, $sts_final) {
                $statusInfo = \App\Models\Combo::where('kelompok', 'sts-aduan')
                    ->where('param_int', $status)
                    ->first();
                if (!$statusInfo) return ['text' => 'Open', 'color' => 'gray'];
                return ['text' => $statusInfo->data_id, 'color' => $statusInfo->param_str ?? 'gray'];
            },
            'getJenisPelanggaran' => function($item) {
                return $item->jenisPengaduan->data_id ?? 'Tidak diketahui';
            }
        ]
    );
}

private function exportToExcelComplien($data)
{
    // Tinggal panggil function di atas dengan parameter yang sesuai
    return $this->exportExcel(
        $data, 
        'exports.pengaduan-complien', // view template
        'laporan-pengaduan-' . date('Y-m-d-H-i-s') . '.xls', // filename
        [ // additional data untuk view
            'periodInfo' => $this->getPeriodInfo(),
            'filterData' => $this->getFilterData(),
            'getNamaUser' => function($item) {
                return $item->pelapor->name ?? $item->user->name ?? 'N/A';
            },
            'getDirektoratName' => function($direktoratId) {
                if (!$direktoratId) return '-';
                $direktorat = \App\Models\Owner::find($direktoratId);
                return $direktorat->owner_name ?? $direktoratId;
            },
            'getStatusInfo' => function($status, $sts_final) {
                $statusInfo = \App\Models\Combo::where('kelompok', 'sts-aduan')
                    ->where('param_int', $status)
                    ->first();
                if (!$statusInfo) return ['text' => 'Open', 'color' => 'gray'];
                return ['text' => $statusInfo->data_id, 'color' => $statusInfo->param_str ?? 'gray'];
            },
            'getJenisPelanggaran' => function($item) {
                return $item->jenisPengaduan->data_id ?? 'Tidak diketahui';
            }
        ]
    );
}



private function downloadExcelFile($response, $filename)
{
    try { 
        $content = $response->getContent(); 
        return response()->streamDownload(function () use ($content) {
            echo $content;
        }, $filename, [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);

    } catch (\Exception $e) {
        \Log::error('Download file error:', ['message' => $e->getMessage()]);
        throw $e;
    }
}

    public function notify($type, $message, $errMessage = '')
    {
        // \dd($errMessage);
        $this->dispatch('notify', [
            'type' => $type,
            'message' => $message,
            'errMessage' => $errMessage
        ]);
    }


    public function logAudit($action, $record, $data = [], $table_name = null)
    {
        try {
            $user = auth()->user();

            // Jika table_name tidak diberikan, gunakan nama model
            if (empty($table_name)) {
                if ($record) {
                    $table_name = $record->getTable();
                } else {
                    // Untuk kasus delete, kita bisa dapatkan table_name dari model class
                    $table_name = strtolower(class_basename($this->model)) . 's'; // contoh: 'combos'
                }
            }

            // Handle record_id dan old_values berdasarkan action
            $record_id = null;
            $old_values = null;

            if ($record) {
                $record_id = $record->id;

                // Untuk update, ambil original data
                if ($action === 'update') {
                    $old_values = $record->getOriginal();
                }
                // Untuk delete, data sudah ada di $data['deleted_data']
                elseif ($action === 'delete' && isset($data['deleted_data'])) {
                    $old_values = $data['deleted_data'];
                }
            }

            // Untuk create, new_values adalah data yang dibuat
            // Untuk update, new_values adalah payload
            // Untuk delete, new_values adalah null (karena data dihapus)
            $new_values = null;
            if ($action === 'create' || $action === 'update') {
                $new_values = $data;
            }

            // Create audit log
            AuditLog::create([
                'user_id' => $user ? $user->id : null,
                'action' => $action,
                'table_name' => $table_name,
                'record_id' => $record_id,
                'old_values' => $old_values ? json_encode($old_values) : null,
                'new_values' => $new_values ? json_encode($new_values) : null,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now()
            ]);

            // NOTIFIKASI OTOMATIS BERDASARKAN ACTION
            $message = $this->getAuditMessage($action, $record, $data);
            $this->notify('success', $message);
        } catch (\Exception $e) {
            // Log error tetapi jangan hentikan proses
            \Log::error('Audit log failed: ' . $e->getMessage());
            $this->notify('error', 'Terjadi kesalahan saat menyimpan audit trail.');
        }
    }

    /**
     * Generate message berdasarkan action
     */
    protected function getAuditMessage($action, $record, $data)
    {
        $modelName = class_basename($this->model);

        switch ($action) {
            case 'create':
                if (isset($record->code_pengaduan)) {
                    return 'Pengaduan berhasil dibuat dengan nomor: ' . $record->code_pengaduan;
                }
                return 'Data  berhasil ditambahkan.';

            case 'update':
                return 'Data  berhasil diperbarui.';

            case 'delete':
                return 'Data  berhasil dihapus.';

            case 'bulk_delete':
                return 'Beberapa data  berhasil dihapus.';

            case 'error':
                return 'Terjadi kesalahan';

            case 'upStsSuccess':
                return 'Berhasil update status';
            case 'upStsErr':
                return 'gagal update status';

            default:
                return 'Aksi ' . $action . ' berhasil dilakukan.';
        }
    }

    public function loadDropdownData()
    {
         $this->jenisPengaduanList = Combo::where('kelompok', 'jenis')
            ->select('data_id', 'data_en', 'data', 'id')
            ->where('is_active', true)
            ->orderBy('data_id')
            ->get();
            
        $this->stsPengaduanList = Combo::where('kelompok', 'sts-aduan')
            ->select('data_id', 'data_en', 'data', 'id')
            ->where('is_active', true)
            ->orderBy('data_id')
            ->get();

       $this->bulanList = [
    [ 'id'=>1,  'sort'=>'Jan', 'full'=>'January' ],
    [ 'id'=>2,  'sort'=>'Feb', 'full'=>'February' ],
    [ 'id'=>3,  'sort'=>'Mar', 'full'=>'March' ],
    [ 'id'=>4,  'sort'=>'Apr', 'full'=>'April' ],
    [ 'id'=>5,  'sort'=>'May', 'full'=>'May' ],
    [ 'id'=>6,  'sort'=>'Jun', 'full'=>'June' ],
    [ 'id'=>7,  'sort'=>'Jul', 'full'=>'July' ],
    [ 'id'=>8,  'sort'=>'Aug', 'full'=>'August' ],
    [ 'id'=>9,  'sort'=>'Sep', 'full'=>'September' ],
    [ 'id'=>10, 'sort'=>'Oct', 'full'=>'October' ],
    [ 'id'=>11, 'sort'=>'Nov', 'full'=>'November' ],
    [ 'id'=>12, 'sort'=>'Dec', 'full'=>'December' ],
];


        $this->tahunPengaduanList = Pengaduan::selectRaw('YEAR(tanggal_pengaduan) as tahun')
            ->whereNotNull('tanggal_pengaduan')
            ->groupBy('tahun')
            ->orderBy('tahun', 'desc')
            ->pluck('tahun', 'tahun') // Konversi ke array [tahun => tahun]
            ->toArray();

                $this->pengaduanAll = Pengaduan::get()->toArray();


        $this->saluranList = Combo::where('kelompok', 'aduan')
            ->select('id', 'data_id', 'data_en', 'data')
            ->where('is_active', true)
            ->orderBy('data_id')
            ->get()->toArray();

        $this->fwdList = Combo::where('kelompok', 'wbs-forward')
            ->select('id', 'data_id', 'data_en', 'data')
            ->where('is_active', true)
            ->orderBy('data_id')
            ->get()->toArray();

        $this->direktoratList = Owner::where('is_active', 1)
            ->select('id', 'owner_name', 'owner_name_1', 'parent_id')
            ->orderBy('owner_name')
            ->get()->toArray();
    }

 
public function removeFileCore($model, $index){
    $files = data_get($this, $model);
    
    if ($files === null) {
        return;
    }
    
    // Handle single file (bukan array)
    if (!is_array($files)) {
        data_set($this, $model, null);
        return;
    }
    
    if (isset($files[$index])) {
        unset($files[$index]);
        $files = array_values($files); 
         
        if (empty($files)) {
            data_set($this, $model, null);
        } else {
            data_set($this, $model, $files);
        }
    }
}
    public function removeLampiran($index)
    {
        if (isset($this->lampiran[$index])) {
            unset($this->lampiran[$index]);
            $this->lampiran = array_values($this->lampiran);
        }
    }

    // Di Livewire component
protected function isValidFileFormat($file, $allowedFormats)
{
    if (empty($file)) {
        return false;
    }
    
    $fileName = is_object($file) ? $file->getClientOriginalName() : $file;
    $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
    return in_array($extension, $allowedFormats);
}

protected function getAcceptAttribute($allowedFormats)
{
    $mimeTypes = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'webp' => 'image/webp',
        'pdf' => 'application/pdf',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'xls' => 'application/vnd.ms-excel',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'ppt' => 'application/vnd.ms-powerpoint',
        'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'zip' => 'application/zip',
        'rar' => 'application/vnd.rar',
        'mp3' => 'audio/mpeg',
        'mp4' => 'video/mp4',
        'avi' => 'video/x-msvideo',
    ];
    
    $acceptTypes = [];
    foreach ($allowedFormats as $format) {
        if (isset($mimeTypes[$format])) {
            $acceptTypes[] = $mimeTypes[$format];
        }
    }
    
    return implode(',', $acceptTypes);
}

    //files
    public function uploadFile()
    {
        $this->validate([
            'fileUpload' => 'required|file|max:10240|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,zip,rar',
            'fileDescription' => 'nullable|string|max:255',
        ]);

        if (!$this->trackingId) return;

        $uploadedFile = FileHelper::upload(
            $this->fileUpload,
            'pengaduan/attachments',
            'public'
        );

        // PERBAIKAN: Pastikan struktur data konsisten
        $fileInfo = [
            'id' => uniqid(),
            'name' => $uploadedFile['original_name'], // Pastikan key 'name' ada
            'path' => $uploadedFile['path'],
            'size' => $uploadedFile['size'],
            'type' => $uploadedFile['mime_type'],
            'description' => $this->fileDescription,
            'uploaded_at' => now()->format('d/m/Y H:i'),
            'uploaded_by' => auth()->user()->name,
            'formatted_size' => FileHelper::formatSize($uploadedFile['size']), // Tambahkan formatted_size
            'icon' => FileHelper::getFileIcon(pathinfo($uploadedFile['original_name'], PATHINFO_EXTENSION)), // Tambahkan icon
        ];

        // Tambahkan ke list uploaded files
        $this->uploadedFiles[] = $fileInfo;

        // Reset form
        $this->fileUpload = null;
        $this->fileDescription = '';

        // Tampilkan notifikasi sukses
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'File berhasil diupload'
        ]);
    }

    public function loadUploadedFiles()
    {
        if (!$this->trackingId) return;

        $this->uploadedFiles = [];
    }

    public function downloadFile($filePath, $originName)
    {
        if ($filePath && FileHelper::exists($filePath)) {
            return response()->download( storage_path('app/public/' . $filePath), $originName);
        }
        $this->dispatch('notify', ['type' => 'error', 'message' => 'File tidak ditemukan: ' . $originName, 'errMessage'=> 'patchFile:'.$filePath ]);
        return back();
    }

    public function deleteFile($fileId)
    {
        $file = collect($this->uploadedFiles)->firstWhere('id', $fileId);

        if ($file) {
            // Hapus dari storage
            FileHelper::delete($file['path']);

            // Hapus dari list
            $this->uploadedFiles = collect($this->uploadedFiles)
                ->reject(function ($item) use ($fileId) {
                    return $item['id'] === $fileId;
                })
                ->values()
                ->toArray();

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'File berhasil dihapus'
            ]);
        }
    }



    public function closeChat()
    {
        parent::closeChat(); // Panggil parent dari HasChat
        $this->uploadedFiles = []; // Reset uploaded files spesifik untuk Tracking
    }


    public function downloadMessageFile($messageId)
    {
        $message = Comment::find($messageId);

        if ($message && $message->file_data) {
            $fileData = json_decode($message->file_data, true);

            if ($fileData && FileHelper::exists($fileData['path'])) {
                return response()->download(
                    storage_path('app/public/' . $fileData['path']),
                    $fileData['original_name'] // Pastikan key 'original_name' ada
                );
            }
        }

        $this->dispatch('notify', [
            'type' => 'error',
            'message' => 'File tidak ditemukan'
        ]);
    }

    public function getFileInfo($file)
    {
        return [
            'name' => $file['name'] ?? $file['original_name'] ?? 'Unknown File',
            'size' => $file['formatted_size'] ?? FileHelper::formatSize($file['size'] ?? 0),
            'icon' => $file['icon'] ?? FileHelper::getFileIcon(pathinfo($file['name'] ?? $file['original_name'] ?? '', PATHINFO_EXTENSION)),
            'description' => $file['description'] ?? '',
        ];
    }

    public function getNamaUser($record)
    {
        return $record->pelapor->name ?? $record->user->name ?? 'N/A';
    }
    public function getDirektoratName($id)
    {
        $p= Owner::where('id', $id)
            ->first();
        return $p->owner_name  ?? 'N/A';
    }

    public function getJenisPelanggaran($record)
    {
        return $record->jenisPengaduan->data_id ?? 'Tidak diketahui';
    }
    public function getComplienProgress($record)
    {
        $progress = $this->calculateProgress($record);
        return "<span class='inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-500'>
                 <i class='fas fa-check' ></i>
            </span>";
    }

    public function getAprvCco($record)
    {

        $sts = $this->getStatusBadge($record->status);
        if ($record->sts_final == 0 && $record->status !== 3) {
            $sts .= $this->getStatusBadge(12);
        }
        return $sts;
    }

    public function getStatusBadge($statusId)
    {
        $user = \auth()->user();
        $role = $user->roles()->first();
        $statusInfo = Combo::where('kelompok', 'sts-aduan')
            ->where('param_int', $statusId)
            ->first();

        if (!$statusInfo) {
            $color = 'gray';
            $text = 'Open';
        } else {
            $color = $statusInfo->param_str ?? 'gray';
            // $text = $statusInfo->data_id;
             $text = ($role->id==3) ?$statusInfo->param_str_2 :$statusInfo->data_id;

        }

        return "
            <span class='inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{$color}-100 text-{$color}-800'>
                <span class='w-1.5 h-1.5 bg-{$color}-500 rounded-full mr-1.5'></span>
                {$text}
            </span>
        ";
    }
    public function calculateProgress($pengaduan)
    {
        $logCount = LogApproval::where('pengaduan_id', $pengaduan->id)->count();
        $totalSteps = 5;
        return min(100, (($logCount + 1) / $totalSteps) * 100);
    }
    // Method untuk mendapatkan dropdown options (jika perlu di view)
    public function getForwardOptions()
    {
        return Combo::where('kelompok', 'wbs-forward')
            ->where('is_active', true)
            ->orderBy('data_id')
            ->get();
    }

    public function progressDashboard($status, $sts_final)
    {
        if ($sts_final == 1) {
            return 100;
        }

        switch ($status) {
            case 0:
                return 10;   // Menunggu
            case 1:
                return 30;   // Dalam Proses
            case 2:
                return 50;   // Diteruskan
            case 3:
                return 100;  // Ditolak (final)
            case 4:
                return 20;   // Diterima
            case 5:
                return 40;   // Diproses
            case 6:
                return 60;   // Ditindaklanjuti
            case 7:
                return 100;  // Ditutup (final)
            case 8:
                return 25;   // Perlu Klarifikasi
            default:
                return 0;
        }
    }
    public function getComboById($i)
    {
        $r = Combo::findOrFail($i);

        return $r->data_id ?? $r->data_en;
    }


    public function getDataFAQ()
    {
        // Ambil pertanyaan dengan jawaban menggunakan relationship
        $q = Combo::where('kelompok', 'pertanyaan')
            ->where('is_active', 1)
            ->where('param_int', 1)
            ->with(['jawaban' => function ($query) {
                $query->where('is_active', 1)
                    ->orderBy('created_at');
            }])
            ->orderBy('created_at')
            ->get();

        return $q->map(function ($pertanyaan) {
            return [
                'pertanyaan' => $pertanyaan,
                'jawaban' => $pertanyaan->jawaban
            ];
        })->toArray();
    }

    public function updatedLampiran($value)
    {
        $validation = FileHelper::validateMultipleFiles(
            $this->lampiran,
            FileHelper::getAllowedPengaduanExtensions(),
            FileHelper::getMaxPengaduanSize()
        );

        if (!$validation['is_valid']) {
            foreach ($validation['errors'] as $filename => $errors) {
                foreach ($errors as $error) {
                    session()->flash('error', $error);
                }
                // Remove invalid files
                $this->removeLampiranByName($filename);
            }
        }

        $this->resetErrorBag('lampiran.*');
    }


    
public function countComentFileByPengaduan($pengaduanId)
{
    $logs = LogApproval::where('pengaduan_id', $pengaduanId)->get();
    
    $totalComments = 0;
    $totalFiles = 0;
    
    foreach ($logs as $log) { 
        if (!empty(trim($log->catatan))) {
            $totalComments++;
        }
         
        $fileData = $log->file ? json_decode($log->file, true) : [];
        if (!empty($fileData) && is_array($fileData)) {
            $totalFiles += count($fileData);
        }
    }
    return [
        'comments' => $totalComments,
        'files' => $totalFiles,
        'aktivitas' => $logs->count(),
    ];
}

public function getTimeAgo($datetime)
    {
        if (!$datetime) return 'Baru saja';
        
        $time = strtotime($datetime);
        $now = time();
        $diff = $now - $time;

        if ($diff < 60) {
            return 'Baru saja';
        } elseif ($diff < 3600) {
            $minutes = floor($diff / 60);
            return $minutes . ' menit lalu';
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            return $hours . ' jam lalu';
        } elseif ($diff < 2592000) {
            $days = floor($diff / 86400);
            return $days . ' hari lalu';
        } else {
            return date('d/m/Y', $time);
        }
    }

public function getStatusInfo($status, $sts_final)
    {
 
         $user = \auth()->user();
        $role = $user->roles()->first();
      $statusInfo = Combo::where('kelompok', 'sts-aduan')
            ->where('param_int', $status)
            ->first();
        if (!$statusInfo) {
            $color = 'gray';
            $text = 'Open';
            $text1 = 'in_progress';
        } else {
            $color = $statusInfo->param_str ?? 'gray';
            $text = ($role->id==3) ?$statusInfo->param_str_2 :$statusInfo->data_id;
            $text1 = $statusInfo->param_str_1;
        }
        return ['text' =>$text , 'color' => $color, 'text1'=>$text1];
    }
public function getPengaduanById($id){
        $record = Pengaduan::where('id',$id)->first(); 
        $this->detailData = [
            'Kode Tracking' => $record->code_pengaduan,
            'Perihal' => $record->perihal,
            'Jenis Pelanggaran' => $this->getJenisPelanggaran($record),
            'Tanggal Aduan' => $record->tanggal_pengaduan->format('d/m/Y H:i'),
            'Status Pengaduan' => $this->getStatusBadge($record->status) ?? 'Open', 
            'Lokasi Kejadian' => $record->alamat_kejadian ?? '-',
            'Deskripsi' => $record->uraian ?? '-', 
            'Files' => json_decode($record->lampiran, true)  ?? [],
        ];
        $this->detailTitle = "Detail Pengaduan - " . $record->code_pengaduan;
    }


      public function verifyEmail()
    {

        $user = \auth()->user(); 
        $key = 'email-verification-attempts:' . $user->id;
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            

            $this->addError('verification_code', "Terlalu banyak percobaan. Silakan coba lagi dalam {$seconds} detik.");
            return;
        }

        RateLimiter::hit($key);

        // Verifikasi kode
        if ($user->code_verif === $this->verification_code) {
            // Update user
            $user->update([
                'email_verified_at' => now(),
                'status' => 1,
                'code_verif' => null
            ]);

            // Kirim email welcome
            $emailService = new EmailService();
            $emailService->setUserId($user->id)
                        ->sendWelcomeEmail($user->email, $user->name);

            // Audit log
            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'email_verified',
                'table_name' => 'users',
                'record_id' => $user->id,
                'old_values' => json_encode(['email_verified_at' => null]),
                'new_values' => json_encode([
                    'email_verified_at' => now()->toDateTimeString(),
                    'status' => 1
                ]),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now()
            ]);

            $this->isVerif = true;
            $this->notify('success', 'Email berhasil diverifikasi! halaman akan di refresh');
            
            return redirect()->intended('/dashboard');
         } else {
            $this->addError('verification_code', 'Kode verifikasi tidak valid. Silakan coba lagi.');
        }
    }
    
        public function resendVerificationCode()
    {
        // Rate limiting untuk pengiriman ulang
        $user = \auth()->user();
        $key = 'email-verification-resend:' . $user->id;
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            
            $this->notify('error', "Terlalu banyak permintaan. Silakan coba lagi dalam {$seconds} detik."); 
            return;
        }

        RateLimiter::hit($key, 180);

        // Generate kode baru
        $newCode = Str::random(8);
        
        $user->update([
            'code_verif' => $newCode
        ]);

        // Kirim email verifikasi baru
        $emailService = new EmailService();
        $emailSent = $emailService->setUserId($user->id)
                                 ->sendVerificationEmail($user->email, $newCode, $user->name);

        // Set cooldown
        $this->canResend = false;
        $this->countdown = 60;
        $this->showCountdown = true;

        if ($emailSent) {
             $this->notify('success', "Kode verifikasi baru telah dikirim ke email Anda."); 
            //  $this->addError('verification_code', 'Kode verifikasi baru telah dikirim ke email Anda.');
 
        } else {
             $this->notify('error', "Gagal mengirim kode verifikasi. Silakan coba lagi.");  
        }

        // Start countdown
        $this->dispatch('start-countdown');
    }

    private function checkResendCooldown()
    {
        $user = \auth()->user();
        $key = 'email-verification-resend:' . $user->id;
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $this->canResend = false;
            $this->countdown = RateLimiter::availableIn($key);
            $this->showCountdown = true;
        }
    }
 
    public function decreaseCountdown()
    {
        if ($this->countdown > 0) {
            $this->countdown--;
            
            if ($this->countdown === 0) {
                $this->canResend = true;
                $this->showCountdown = false;
            }
        }
    }
    
    
public function getFilterData()
{
    $filterInfo = [];
    
    $filterLabels = [
        'search' => 'Pencarian',
        'status' => 'Status',
        'jenis_pengaduan_id' => 'Jenis Pelanggaran', 
        'tahun' => 'Tahun',
        'bulan' => 'Bulan',
        'direktorat' => 'Direktorat',
        'saluran_id' => 'Saluran Aduan',
        'fwd_id' => 'WBS Forward',
        'nama_pelapor' => 'Nama Pelapor',
        'nama_terlapor' => 'Nama Terlapor'
    ];
    
    if (!empty($this->search)) {
        $filterInfo['Kata Kunci'] = $this->search;
    }
    
    if (!empty($this->filters) && is_array($this->filters)) {
        foreach ($this->filters as $key => $value) {
            if (!empty($value) && $value !== '' && $value !== null) {
                $label = $filterLabels[$key] ?? $this->formatFilterKey($key);
                $formattedValue = $this->formatFilterValue($key, $value);
                $filterInfo[$label] = $formattedValue;
            }
        }
    }
    
    $queryParams = request()->query();
    $commonFilterKeys = ['search', 'status', 'jenis_pengaduan_id', 'tahun', 'bulan_id', 'saluran_id', 'fwd_id'];
    
    foreach ($commonFilterKeys as $key) {
        if (isset($queryParams[$key]) && !empty($queryParams[$key]) && !isset($filterInfo[$filterLabels[$key] ?? $key])) {
            $label = $filterLabels[$key] ?? $this->formatFilterKey($key);
            $formattedValue = $this->formatFilterValue($key, $queryParams[$key]);
            $filterInfo[$label] = $formattedValue;
        }
    }
     
    if (empty($filterInfo)) {
        $filterInfo['Periode'] = 'Semua Data';
    }
    
    return $filterInfo;
}

public function formatFilterKey($key)
{
    $keyMap = [];
    
    return $keyMap[$key] ?? str_replace('_', ' ', ucwords($key, '_'));
}

public function formatFilterValue($key, $value)
{
    switch ($key) {
        case 'status':
            $statusInfo = $this->getStatusInfo($value, 0);
            return $statusInfo['text'] ?? $value;
            
        case 'jenis_pengaduan_id':
        case 'jenis_pengaduan':
            $combo = Combo::find($value);
            return $combo->data ?? $combo->data_id ?? $combo->data_en ?? $value;
            
        case 'tahun':
            return "Tahun {$value}";
            
        case 'bulan_id':
        case 'bulan':
            $months = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];
            return $months[$value] ?? $value;
            
        case 'saluran_id':
        case 'saluran':
            $combo = Combo::find($value);
            return $combo->data ?? $combo->data_id ?? $combo->data_en ?? $value;
            
        case 'fwd_id':
        case 'fwd':
            $combo = Combo::find($value);
            return $combo->data ?? $combo->data_en ?? $combo->data_id ?? $value;
            
        case 'direktorat':
            $owner = Owner::find($value);
            return $owner->owner_name ?? $value;
            
        default:
            return $value;
    }
} 

public function getPeriodInfo()
{
    $currentMonth = date('m');
    $currentYear = date('Y');
    
    $bulan = $this->filters['bulan'] ?? request('bulan', $currentMonth);
    $tahun = $this->filters['tahun'] ?? request('tahun', $currentYear);     
    $months = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];

    $monthName = $months[$bulan] ?? 'Semua Bulan';
    
    return $monthName . ' ' . $tahun;
}

public function formatFileSize($bytes)
{
    if ($bytes == 0) return '0 Bytes';
    
    $k = 1024;
    $sizes = ['Bytes', 'KB', 'MB', 'GB'];
    $i = floor(log($bytes) / log($k));
    
    return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
}

public function isJsonString($string)
{
    if (!is_string($string)) {
        return false;
    }
    
    // Cek jika string mengandung karakter JSON
    if (str_contains($string, '{') && str_contains($string, '}')) {
        return true;
    }
    
    if (str_contains($string, '[') && str_contains($string, ']')) {
        return true;
    }
    
    return false;
}
}
