<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailConfig extends Model
{
    protected $table = 'email_configs';
    
    protected $fillable = [
        'mailer',
        'host',
        'port', 
        'encryption',
        'username',
        'password',
        'from_address',
        'from_name',
        'active'
    ];

    protected $casts = [
        'active' => 'boolean',
        'port' => 'integer'
    ];
}