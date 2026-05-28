<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wristband extends Model
{
    protected $fillable = ['e_ticket_id', 'concert_id', 'wristband_code', 'wristband_qr_code', 'wristband_status', 'activated_at', 'entered_at'];

    protected function casts(): array
    {
        return ['activated_at' => 'datetime', 'entered_at' => 'datetime'];
    }

    public function eTicket() { return $this->belongsTo(ETicket::class, 'e_ticket_id'); }
    public function concert() { return $this->belongsTo(Concert::class); }
}
