<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Officer extends Authenticatable
{
    use Notifiable;

    protected $fillable = ['name', 'username', 'password', 'role'];
    protected $hidden = ['password'];

    protected function casts(): array
    {
        return ['password' => 'hashed'];
    }

    public function scanHistories()
    {
        return $this->hasMany(ScanHistory::class);
    }
}
