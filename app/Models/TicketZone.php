<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketZone extends Model
{
    protected $fillable = ['concert_id', 'name', 'price', 'stock', 'color', 'position'];

    public function concert()
    {
        return $this->belongsTo(Concert::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
