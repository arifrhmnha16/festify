<?php

namespace App\Models;

use Database\Factories\UserFactory;
use App\Notifications\FestifyResetPassword;
use App\Notifications\FestifyVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
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

    public function orders() { return $this->hasMany(Order::class); }
    public function eTickets() { return $this->hasMany(ETicket::class); }

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new FestifyVerifyEmail);
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new FestifyResetPassword($token));
    }
}
