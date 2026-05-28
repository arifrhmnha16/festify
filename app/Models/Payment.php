<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['order_id', 'payment_method', 'total_amount', 'payment_status', 'payment_date', 'payment_proof'];

    protected function casts(): array
    {
        return ['payment_date' => 'datetime'];
    }

    public function order() { return $this->belongsTo(Order::class); }
}
