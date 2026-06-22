<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('gateway_order_id')->nullable()->unique()->after('order_id');
            $table->string('snap_token')->nullable()->after('payment_proof');
            $table->string('snap_redirect_url')->nullable()->after('snap_token');
            $table->string('midtrans_transaction_id')->nullable()->after('snap_redirect_url');
            $table->string('midtrans_payment_type')->nullable()->after('midtrans_transaction_id');
            $table->json('midtrans_payload')->nullable()->after('midtrans_payment_type');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'gateway_order_id',
                'snap_token',
                'snap_redirect_url',
                'midtrans_transaction_id',
                'midtrans_payment_type',
                'midtrans_payload',
            ]);
        });
    }
};
