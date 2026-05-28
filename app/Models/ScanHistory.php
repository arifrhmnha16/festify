<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScanHistory extends Model
{
    protected $fillable = ['officer_id', 'e_ticket_id', 'wristband_id', 'scan_type', 'scan_result', 'message', 'scanned_at'];

    protected function casts(): array
    {
        return ['scanned_at' => 'datetime'];
    }

    public function officer() { return $this->belongsTo(Officer::class); }
    public function eTicket() { return $this->belongsTo(ETicket::class, 'e_ticket_id'); }
    public function wristband() { return $this->belongsTo(Wristband::class); }
}
