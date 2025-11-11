<?php 
namespace App\Livewire;

use App\Models\User;
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

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => bcrypt('password'),
        ]);

        $user->syncRoles($this->userRoles);
        $user->syncPermissions($this->userPermissions);

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
