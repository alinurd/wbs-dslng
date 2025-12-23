<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasRoles;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'name',
        'email',
        'password',
        'ref_pertanyaan',
        'jawaban',
        'no_identitas',
        'telepon',
        'jenis_pelapor',
        'alamat',
        'is_active',
        'status',
        'fwd_id',
        'must_change_password',
        'insert_datetime',
        'update_datetime',
        'code_verif',
        'email_verified_at',
        'blocked_at',
        'count_try_login',
    ];

 

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
         'jawaban'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function notifications()
{
    return $this->hasMany(Notification::class, 'to');
}

   public function isBlocked(): bool
    {
        if (!$this->blocked_at) {
            return false;
        }
        
        $blockedAt = Carbon::parse($this->blocked_at);
        $now = Carbon::now();
        
        return $now->diffInMinutes($blockedAt) < 30;
    }

public function getRemainingBlockTime(): int
    {
        if (!$this->blocked_at) {
            return 0;
        }
        
        $blockedAt = Carbon::parse($this->blocked_at);
        $now = Carbon::now();
        $minutesPassed = $now->diffInMinutes($blockedAt);
        
        $remaining = max(0, 30 - $minutesPassed);
        return (int) ceil($remaining);
    }
    

    public function resetLoginAttempts(): void
    {
        $this->count_try_login = 0;
        $this->blocked_at = null;
        $this->save();
    }
}
