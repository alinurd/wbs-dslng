<?php

namespace App\Livewire\Modules\Users;

use App\Livewire\Root;
use App\Models\Combo;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserManagement extends Root
{
    public $title = "User Management";   
    public $views = "modules.users.user";

    public $model = User::class;
    public $modul = 'users';
    public $kel = 'combo';
    public $forwardDestination = '';
    public $recordId = '';
    
    // Properties untuk roles - UBAH KE SINGLE VALUE
    public $RolesList = [];
    public $selectedRole = null; // UBAH DARI selectedRoles MENJADI selectedRole (single value)

    // Form configuration
    public $form = [
        'name' => '', 
        'fwd_id' => null, 
        'email' => '', 
        'password' => '',
        'is_active' => true
    ];

    public $formDefault = [
        'name' => '', 
        'fwd_id' => null, 
        'email' => '', 
        'password' => '',
        'is_active' => true
    ];

    public $filters = [
        'search' => '',
        'role_id' => '',
        'is_active' => ''
    ];

    // Rules untuk validasi form - UBAH KE SINGLE ROLE
    public function rules()
    {
        $rules = [
            'form.name' => 'required|string|max:255', 
            'form.email' => 'required|email|max:255', 
            'form.is_active' => 'boolean',
            'selectedRole' => 'required|exists:roles,id'
        ];

        // Validasi conditional untuk fwd_id - required hanya jika role 6 dipilih
        if ($this->selectedRole == 6) {
            $rules['form.fwd_id'] = 'required|exists:combos,id';
        } else {
            // Jika bukan role 6, set fwd_id ke null
            $this->form['fwd_id'] = null;
        }

        // Untuk create, password wajib
        if (!$this->updateMode) {
            $rules['form.password'] = 'required|min:8';
        } else {
            // Untuk update, password optional
            $rules['form.password'] = 'nullable|min:8';
        }

        // Validasi email unique
        if ($this->updateMode && isset($this->form['id'])) {
            $rules['form.email'] .= '|unique:users,email,' . $this->form['id'];
        } else {
            $rules['form.email'] .= '|unique:users,email';
        }

        return $rules;
    }

    protected $messages = [
        'form.name.required' => 'Nama wajib diisi', 
        'form.email.required' => 'Email wajib diisi', 
        'form.email.email' => 'Format email tidak valid',
        'form.email.unique' => 'Email sudah digunakan',
        'form.password.required' => 'Password wajib diisi', 
        'form.password.min' => 'Password minimal 8 karakter',
        'selectedRole.required' => 'Role wajib dipilih',
        'selectedRole.exists' => 'Role tidak valid',
        'form.fwd_id.required' => 'Tujuan Forward wajib dipilih ketika memilih role WBS Forward',
        'form.fwd_id.exists' => 'Tujuan Forward tidak valid'
    ];

    public function mount()
    {
        parent::mount();
        $this->RolesList = Role::orderBy('name')->get();
    }

    public function columns()
    {
        return [
            'name' => 'Name', 
            'email' => 'Email',
            'roles' => 'Roles',
            'is_active' => 'Status', 
        ];
    }

    // Override query untuk load data dengan roles
    public function query()
    {
        return $this->model::with('roles')
            ->select(['id', 'name', 'email', 'fwd_id', 'is_active', 'created_at', 'updated_at'])
            ->when($this->filters['search'], function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('email', 'like', '%' . $search . '%');
                });
            })
            ->when($this->filters['role_id'], function ($query, $roleId) {
                $query->whereHas('roles', function ($q) use ($roleId) {
                    $q->where('id', $roleId);
                });
            })
            ->when($this->filters['is_active'] !== '', function ($query) {
                $query->where('is_active', $this->filters['is_active']);
            });
    }

    
    // Override method saving untuk handle password dan fwd_id
    public function saving($payload)
    {
        // Hash password jika diisi
        if (!empty($payload['password'])) {
            $payload['password'] = Hash::make($payload['password']);
        } else {
            // Hapus password dari payload jika kosong (saat update)
            unset($payload['password']);
        }

        // Jika bukan role 6, set fwd_id ke null
        if ($this->selectedRole != 6) {
            $payload['fwd_id'] = null;
        }

        return $payload;
    }

    // Override method saved untuk handle roles dengan error handling
    public function saved($record, $action)
    {
        try {
            // Assign single role
            if ($this->selectedRole) {
                $record->syncRoles([$this->selectedRole]);
            }

        } catch (\Exception $e) {
            \Log::error("Error syncing roles for user {$record->id}: " . $e->getMessage());
            // $this->dispatch('show-toast', type: 'error', message: 'Error assigning roles: ' . $e->getMessage());
        }

        // Reset selectedRole setelah save
        $this->selectedRole = null;
    }

    // Override method create untuk reset selectedRole
    public function create()
    {
        can_any([strtolower($this->modul).'.create']);
        $this->reset(['form', 'selectedRole']);
        $this->form = $this->formDefault;
        $this->updateMode = false;
        $this->showModal = true;
    }

    // Override method edit untuk load role
    public function edit($id)
    {
        can_any([strtolower($this->modul).'.edit']);
        
        $record = $this->model::findOrFail($id);
        $this->recordId = $id;
        
        // Set form data
        $this->form = [
            'id' => $record->id,
            'name' => $record->name,
            'email' => $record->email,
            'fwd_id' => $record->fwd_id,
            'password' => '', // Kosongkan password saat edit
            'is_active' => $record->is_active
        ];
        
        // Load single role user
        $this->selectedRole = $record->roles->first()?->id;
        
        $this->updateMode = true;
        $this->showModal = true;
    }

    public function view($id)
    {
        can_any([strtolower($this->modul).'.view']);
        
        $record = $this->model::with('roles')->findOrFail($id);
        $roles = $record->getRoleNames()->implode(', ');
        
        // Get forward destination name if exists
        $forwardDestination = $record->fwd_id ? Combo::find($record->fwd_id)?->data_id : '-';
        
        $this->detailData = [
            'Name' => $record->name,
            'Code Verifikasi' => $record->code_verif, 
            'Roles' => $roles ?: 'No Role',
            'Forward Destination' => $forwardDestination,
            'Email Verified' => $record->email_verified_at ? 
                $record->email_verified_at->format('d/m/Y H:i') : 
                'Belum diverifikasi',
            'Dibuat Pada' => $record->created_at->format('d/m/Y H:i'),
            'Diupdate Pada' => $record->updated_at->format('d/m/Y H:i'),
        ];
        
        $this->detailTitle = "Detail User: " . $record->name;
        $this->showDetailModal = true;
    }

    // Reset password - extra action
    public function resetPassword($id)
    {
        can_any([strtolower($this->modul).'.edit']);
        
        try {
            $user = $this->model::findOrFail($id);
            $user->update([
                'password' => Hash::make('$$4Dmin$$') // Default password
            ]);
            
            $this->dispatch('show-toast', type: 'success', message: 'Password berhasil direset ke: $$4Dmin$$');
        } catch (\Exception $e) {
            $this->dispatch('show-toast', type: 'error', message: 'Gagal reset password: ' . $e->getMessage());
        }
    }

    // Activate/Deactivate user - extra action
    public function toggleStatus($id)
    {
        can_any([strtolower($this->modul).'.edit']);
        
        try {
            $user = $this->model::findOrFail($id);
            $user->update([
                'is_active' => !$user->is_active
            ]);
            
            $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
            $this->dispatch('show-toast', type: 'success', message: "User berhasil $status");
            
            // Refresh data
            $this->loadRecords();
        } catch (\Exception $e) {
            $this->dispatch('show-toast', type: 'error', message: 'Gagal mengubah status: ' . $e->getMessage());
        }
    }

    // Override closeModal untuk reset selectedRole
    public function closeModal()
    {
        parent::closeModal();
        $this->selectedRole = null;
        $this->form['fwd_id'] = null;
    }

    public function loadRecords()
    {
        parent::loadRecords();
        
        // Format data setelah load
        if ($this->_records) {
            $this->_records->setCollection(
                $this->_records->getCollection()->map(function($record) {
                    return (object)[
                        'id' => $record->id,
                        'name' => $record->name,
                        'email' => $record->email,
                        'roles' => $record->relationLoaded('roles') 
                            ? $record->roles->pluck('name')->implode(', ')
                            : '-',
                        'is_active' => $record->is_active
                    ];
                })
            );
        }
    }
 
    public function onRoleChange($value)
    {
        $this->selectedRole = $value;
         
        // Jika ganti role dari 6 ke yang lain, reset fwd_id
        if ($value != 6) {
            $this->form['fwd_id'] = null;
        }
        
        // Validate realtime ketika role berubah
        $this->validateOnly('form.fwd_id');
    }
 
    public function clearRole()
    {
        $this->selectedRole = null;
        $this->form['fwd_id'] = null;
    }

    public function getForwardOptions()
    {
         return Combo::where('kelompok', 'wbs-forward')
        ->where('is_active', true)
        ->orderBy('data_id')
        ->get(); 
    }

    public function shouldShowForwardDropdown()
    {
        return $this->selectedRole == 6;
    }
}