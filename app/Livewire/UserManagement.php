<?php 
namespace App\Livewire;

use App\Models\Audit as AuditLog;
use App\Models\User;
use App\Services\EmailService;
use Illuminate\Support\Str;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;


class UserManagement extends Component
{
    public $users, $roles, $permissions;
    public $name, $email, $password, $userId;
    public $userRoles = [], $userPermissions = [];
    public $updateMode = false;

    public function render()
    {
        $this->users = User::with('roles', 'permissions')->get();
        $this->roles = Role::all();
        $this->permissions = Permission::all();
        return view('livewire.user-management');
    }

    public function resetInput()
    {
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->userRoles = [];
        $this->userPermissions = [];
    }

    public function store()
    {
        $this->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
        ]);
    $codeVerif =  str::random(8);

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => bcrypt('password'),
            
    'code_verif' => $codeVerif,
        ]);
        $user->assignRole('user'); 
        // $user->syncRoles($this->userRoles);
        // $user->syncPermissions($this->userPermissions);
        $emailService = new EmailService();

$emailSent = $emailService->setUserId($user->id)
                             ->sendVerificationEmail($this->email, $codeVerif, $this->full_name);
                                                     
AuditLog::create([
        'user_id' => $user->id,
        'action' => 'add',
        'table_name' => 'users',
        'record_id' => $user->id,
        'old_values' => null,
        'new_values' => json_encode([
             'name' => $this->name,
            'email' => $this->email,
            'code_verif' => $codeVerif,
            'email_verification_sent' => $emailSent,
         ]),
        'ip_address' => request()->ip(),
        'user_agent' => request()->userAgent(),
        'created_at' => now()
    ]);
    
        session()->flash('message', 'User berhasil dibuat!');
        $this->resetInput();
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->userRoles = $user->roles->pluck('name')->toArray();
        $this->userPermissions = $user->permissions->pluck('name')->toArray();
        $this->updateMode = true;
    }

    public function update()
    {
        $this->validate([
            'name' => 'required',
            'email' => 'required|email',
        ]);

        $user = User::find($this->userId);
        $user->update([
            'name' => $this->name,
            'email' => $this->email,
        ]);

        $user->syncRoles($this->userRoles);
        $user->syncPermissions($this->userPermissions);

        $this->updateMode = false;
        session()->flash('message', 'User berhasil diperbarui!');
        $this->resetInput();
    }

    public function delete($id)
    {
        User::find($id)->delete();
        session()->flash('message', 'User berhasil dihapus!');
    }
}
