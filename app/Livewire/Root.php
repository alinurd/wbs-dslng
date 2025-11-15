<?php

namespace App\Livewire;

use App\Models\Audit as AuditLog;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
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


    // ================== MOUNT =====================
    public function mount()
    {
        // Simpan default form
        $this->formDefault = is_array($this->form) ? $this->form : [];

        // Title otomatis jika tidak didefinisikan
        $this->title = $this->title ?: class_basename($this->model);
         // Hak akses
        can_any([strtolower($this->modul).'.view']);

        // Locale
        $this->locale = Session::get('locale', config('app.locale'));
        App::setLocale($this->locale);
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


    // ===================== RENDER ======================
    public function render()
    {
        $items = $this->query()
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view($this->viewPath(), [
            '_records'    => $items,
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
        can_any([strtolower($this->modul).'.create']);
        $this->resetForm();
        $this->updateMode = false;
        $this->showModal = true;

        $this->dispatch('modalOpened');
    }


    public function edit($id)
    {
        can_any([strtolower($this->modul).'.edit']);

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
        can_any([strtolower($this->modul).'.edit']);
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
        can_any([strtolower($this->modul).'.create']);
        $record = $modelClass::create($payload);
        
        // Log audit trail
        $this->logAudit($action, $record, $payload);
    }

    // Call saved hook - FIXED
    if (method_exists($this, 'saved')) {
        $this->saved($record, $action);
    }

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
    can_any([strtolower($this->modul).'.delete']);
    
    $record = ($this->model)::findOrFail($id);
    
    // Simpan data lengkap record sebelum dihapus
    $oldData = $record->toArray();
    
    // Log audit trail SEBELUM menghapus dengan data lengkap
    $this->logAudit('delete', $record, ['deleted_data' => $oldData]);
    
    $record->delete();
    
    // NOTIFIKASI SUDAH DIHANDLE OLEH logAudit
    $this->resetPage();
    $this->dispatch('dataDeleted');
}
public function deleteBulk()
{
    can_any([strtolower($this->modul).'.delete']);

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
    $this->dispatch('bulkDeleteCompleted');
}

    // ====================== FILTER ========================
    public function openFilter()
    {
        $this->showFilterModal = true;
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
        can_any([strtolower($this->modul).'.view']);
        $record = ($this->model)::findOrFail($id);

        $this->dispatch('showDetailModal', [
            'title' => "Detail " . $this->title,
            'data'  => $record->toArray()
        ]);
    }


    // =================== SUPPORT =======================
    public function closeModal()
    {
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

     
    public function notify($type, $message)
{
    $this->dispatch('notify', [
        'type' => $type,
        'message' => $message
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
            
        default:
            return 'Aksi ' . $action . ' berhasil dilakukan.';
    }
}


}