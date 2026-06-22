<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'gateway_order_id',
        'payment_method',
        'total_amount',
        'payment_status',
        'payment_date',
        'payment_proof',
        'snap_token',
        'snap_redirect_url',
        'midtrans_transaction_id',
        'midtrans_payment_type',
        'midtrans_payload',
    ];

    protected function casts(): array
    {
        return [
            'payment_date' => 'datetime',
            'midtrans_payload' => 'array',
        ];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
