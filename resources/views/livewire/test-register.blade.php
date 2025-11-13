<div>
    <form wire:submit.prevent="register">
        <div>
            <label>Username</label>
            <input type="text" wire:model="username">
        </div>
        <div>
            <label>Email</label>
            <input type="email" wire:model="email">
        </div>
        <div>
            <label>Password</label>
            <input type="password" wire:model="password">
        </div>
        <button type="submit">Register</button>
    </form>
</div>
