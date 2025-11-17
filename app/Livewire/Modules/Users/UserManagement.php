<?php

namespace App\Livewire\Modules\Users;

use App\Livewire\Root;
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
    
    // Properties untuk roles
    public $RolesList = [];
    public $selectedRoles = [];

    // Form configuration
    public $form = [
        'name' => '', 
        'email' => '', 
        'password' => '',
        'is_active' => true
    ];

    public $formDefault = [
        'name' => '', 
        'email' => '', 
        'password' => '',
        'is_active' => true
    ];

    public $filters = [
        'search' => '',
        'role_id' => '',
        'is_active' => ''
    ];

    // Rules untuk validasi form
    public function rules()
    {
        $rules = [
            'form.name' => 'required|string|max:255', 
            'form.email' => 'required|email|max:255', 
            'form.is_active' => 'boolean',
            'selectedRoles' => 'required|array|min:1',
            'selectedRoles.*' => 'exists:roles,id'
        ];

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
        'selectedRoles.required' => 'Role wajib dipilih',
        'selectedRoles.min' => 'Pilih minimal satu role'
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
            ->select(['id', 'name', 'email', 'is_active', 'created_at', 'updated_at'])
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

    
    // Override method saving untuk handle password
    public function saving($payload)
    {
        // Hash password jika diisi
        if (!empty($payload['password'])) {
            $payload['password'] = Hash::make($payload['password']);
        } else {
            // Hapus password dari payload jika kosong (saat update)
            unset($payload['password']);
        }

        return $payload;
    }

    // Override method saved untuk handle roles dengan error handling
    public function saved($record, $action)
    {
        try {
            // Pastikan selectedRoles adalah array of integers
            $roleIds = array_map('intval', $this->selectedRoles ?? []);
            
            // Validasi role IDs sebelum sync
            $validRoleIds = Role::whereIn('id', $roleIds)->pluck('id')->toArray();
            
            if (!empty($validRoleIds)) {
                $record->syncRoles($validRoleIds);
            }

        } catch (\Exception $e) {
            \Log::error("Error syncing roles for user {$record->id}: " . $e->getMessage());
            $this->dispatch('show-toast', type: 'error', message: 'Error assigning roles: ' . $e->getMessage());
        }

        // Reset selectedRoles setelah save
        $this->selectedRoles = [];
    }

    // Override method create untuk reset selectedRoles
    public function create()
    {
        can_any([strtolower($this->modul).'.create']);
        $this->reset(['form', 'selectedRoles']);
        $this->form = $this->formDefault;
        $this->updateMode = false;
        $this->showModal = true;
    }

    // Override method edit untuk load roles
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
            'password' => '', // Kosongkan password saat edit
            'is_active' => $record->is_active
        ];
        
        // Load roles user - pastikan sebagai array of integers
        $this->selectedRoles = $record->roles->pluck('id')->map(function($id) {
            return (int)$id;
        })->toArray();
        
        $this->updateMode = true;
        $this->showModal = true;
    }

    public function view($id)
    {
        can_any([strtolower($this->modul).'.view']);
        
        $record = $this->model::with('roles')->findOrFail($id);
        $roles = $record->getRoleNames()->implode(', ');
        
        $this->detailData = [
            'Name' => $record->name,
            'Email' => $record->email, 
            'Roles' => $roles ?: 'No Role', 
            // 'Status' => $record->is_active,
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
            
            $status = $user->is_active ?1 : 0;
            $this->dispatch('show-toast', type: 'success', message: "User berhasil $status");
            
            // Refresh data
            $this->loadRecords();
        } catch (\Exception $e) {
            $this->dispatch('show-toast', type: 'error', message: 'Gagal mengubah status: ' . $e->getMessage());
        }
    }

    // Override closeModal untuk reset selectedRoles
    public function closeModal()
    {
        parent::closeModal();
        $this->selectedRoles = [];
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
}