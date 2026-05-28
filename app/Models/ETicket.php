<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ETicket extends Model
{
    protected $table = 'e_tickets';
    protected $fillable = ['order_id', 'user_id', 'concert_id', 'ticket_code', 'ticket_qr_code', 'ticket_status', 'issued_at', 'exchanged_at'];

    protected function casts(): array
    {
        return ['issued_at' => 'datetime', 'exchanged_at' => 'datetime'];
    }

    public function order() { return $this->belongsTo(Order::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function concert() { return $this->belongsTo(Concert::class); }
    public function wristband() { return $this->hasOne(Wristband::class, 'e_ticket_id'); }
}
