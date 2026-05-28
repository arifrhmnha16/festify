<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['user_id', 'concert_id', 'ticket_zone_id', 'order_code', 'order_date', 'ticket_quantity', 'total_price', 'order_status'];

    protected function casts(): array
    {
        return ['order_date' => 'datetime'];
    }

    public function user() { return $this->belongsTo(User::class); }
    public function concert() { return $this->belongsTo(Concert::class); }
    public function ticketZone() { return $this->belongsTo(TicketZone::class); }
    public function payment() { return $this->hasOne(Payment::class); }
    public function eTickets() { return $this->hasMany(ETicket::class); }
}
