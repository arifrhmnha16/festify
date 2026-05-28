<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Concert extends Model
{
    protected $fillable = ['admin_id', 'name', 'artist', 'description', 'venue', 'date', 'time', 'poster', 'price', 'stock', 'seat_zone', 'status', 'is_featured'];

    protected function casts(): array
    {
        return ['date' => 'date', 'price' => 'integer', 'stock' => 'integer', 'is_featured' => 'boolean'];
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function ticketZones()
    {
        return $this->hasMany(TicketZone::class)->orderBy('position');
    }
}
