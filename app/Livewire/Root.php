<?php

namespace App\Livewire;

use App\Helpers\FileHelper;
use App\Models\Audit as AuditLog;
use App\Models\Combo;
use App\Models\Comment;
use App\Models\LogApproval;
use App\Models\Owner;
use App\Models\Pengaduan;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Laravel\Jetstream\Role;
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
    public $RolesList = []; // child dapat override dengan property
    public $saluranList = []; // child dapat override dengan property
    public $direktoratList = []; // child dapat override dengan property
    public $tahunPengaduanList = []; // child dapat override dengan property



    // Properties untuk file upload di chat
    public $fileUpload = null;
    public $fileDescription = '';
    public $uploadedFiles = [];
    public $lampiran = [];
    public $attachFile = null;
    public $isAdmin = false;
    public $pelapor = false;


    // ================== MOUNT =====================
    public function mount()
    {
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
        $this->userInfo();
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
        $combo = Combo::select('id', 'data_id', 'data_en', 'param_int', 'param_str')->whereIn('param_int', json_decode($role->sts, true))->get()->toarray();
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
            // Build query menggunakan method query() yang sudah ada
            $query = $this->query();

            // Apply sorting
            $query->orderBy($this->sortField, $this->sortDirection);

            // Get paginated results
            $this->_records = $query->paginate($this->perPage);

            // Apply custom formatting jika method formatRecords ada
            if (method_exists($this, 'formatRecords')) {
                $this->formatRecords();
            }
        } catch (\Exception $e) {
            \Log::error('Error loading records in Root: ' . $e->getMessage());
            // Fallback ke empty pagination
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
        // can_any([strtolower($this->modul).'.export']);

        $data = $this->query()->get();

        if ($type === 'excel') {
            $this->notify('success', 'Data berhasil diexport ke Excel.');
        } elseif ($type === 'pdf') {
            $this->notify('success', 'Data berhasil diexport ke PDF.');
        }

        $this->dispatch('exportCompleted', ['type' => $type]);
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

        $this->tahunPengaduanList = Pengaduan::selectRaw('YEAR(tanggal_pengaduan) as tahun')
            ->whereNotNull('tanggal_pengaduan')
            ->groupBy('tahun')
            ->orderBy('tahun', 'desc')
            ->pluck('tahun', 'tahun') // Konversi ke array [tahun => tahun]
            ->toArray();

        $this->saluranList = Combo::where('kelompok', 'aduan')
            ->select('id', 'data_id', 'data_en', 'data')
            ->where('is_active', true)
            ->orderBy('data_id')
            ->get();

        $this->direktoratList = Owner::where('is_active', 1)
            ->select('id', 'owner_name', 'owner_name_1', 'parent_id')
            ->orderBy('owner_name')
            ->get();
    }



    public function removeLampiran($index)
    {
        if (isset($this->lampiran[$index])) {
            unset($this->lampiran[$index]);
            $this->lampiran = array_values($this->lampiran);
        }
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
        $statusInfo = Combo::where('kelompok', 'sts-aduan')
            ->where('param_int', $statusId)
            ->first();

        if (!$statusInfo) {
            $color = 'gray';
            $text = 'Menunggu Review';
        } else {
            $color = $statusInfo->param_str ?? 'gray';
            $text = $statusInfo->data_id;
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
        $r = $this->model::findOrFail($i);

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
      $statusInfo = Combo::where('kelompok', 'sts-aduan')
            ->where('param_int', $status)
            ->first();
        if (!$statusInfo) {
            $color = 'gray';
            $text = 'Menunggu Review';
            $text1 = 'in_progress';
        } else {
            $color = $statusInfo->param_str ?? 'gray';
            $text = $statusInfo->data_id;
            $text1 = $statusInfo->param_str_1;
        }
        return ['text' =>$text , 'color' => $color, 'text1'=>$text1];
    }
public function getPengaduanById($id){
        $record = $this->model::where('id',$id)->first();
        $statusInfo = Combo::where('kelompok', 'sts-aduan')
            ->where('param_int', $record->status)
            ->first();
            $this->pengaduan_id=$id;
        $this->detailData = [
            'Kode Tracking' => $record->code_pengaduan,
            'Perihal' => $record->perihal,
            'Jenis Pelanggaran' => $this->getJenisPelanggaran($record),
            'Tanggal Aduan' => $record->tanggal_pengaduan->format('d/m/Y H:i'),
            'Status' => $statusInfo->data_id ?? 'Menunggu Review',
            'Status Color' => $statusInfo->param_str ?? 'gray',
            'Lokasi Kejadian' => $record->alamat_kejadian ?? '-',
            'Deskripsi' => $record->uraian ?? '-',
        ];
        $this->detailTitle = "Detail Pengaduan - " . $record->code_pengaduan;
    }

    
}
