<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->unique();
            $table->string('password');
            $table->timestamps();
        });

        Schema::create('officers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->unique();
            $table->string('password');
            $table->enum('role', ['loket', 'gate']);
            $table->timestamps();
        });

        Schema::create('concerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->string('name');
            $table->string('artist');
            $table->text('description')->nullable();
            $table->string('venue');
            $table->date('date');
            $table->time('time');
            $table->string('poster')->nullable();
            $table->unsignedInteger('price');
            $table->unsignedInteger('stock');
            $table->string('seat_zone')->nullable();
            $table->enum('status', ['aktif', 'selesai', 'dibatalkan'])->default('aktif');
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('concert_id')->constrained()->cascadeOnDelete();
            $table->string('order_code')->unique();
            $table->dateTime('order_date');
            $table->unsignedInteger('ticket_quantity');
            $table->unsignedInteger('total_price');
            $table->enum('order_status', ['pending', 'paid', 'cancelled', 'expired'])->default('pending');
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('payment_method')->default('transfer_manual');
            $table->unsignedInteger('total_amount');
            $table->enum('payment_status', ['pending', 'success', 'failed'])->default('pending');
            $table->dateTime('payment_date')->nullable();
            $table->string('payment_proof')->nullable();
            $table->timestamps();
        });

        Schema::create('e_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('concert_id')->constrained()->cascadeOnDelete();
            $table->string('ticket_code')->unique();
            $table->string('ticket_qr_code')->nullable();
            $table->enum('ticket_status', ['belum_ditukar', 'sudah_ditukar', 'invalid'])->default('belum_ditukar');
            $table->dateTime('issued_at');
            $table->dateTime('exchanged_at')->nullable();
            $table->timestamps();
        });

        Schema::create('wristbands', function (Blueprint $table) {
            $table->id();
            $table->foreignId('e_ticket_id')->unique()->constrained('e_tickets')->cascadeOnDelete();
            $table->foreignId('concert_id')->constrained()->cascadeOnDelete();
            $table->string('wristband_code')->unique();
            $table->string('wristband_qr_code')->nullable();
            $table->enum('wristband_status', ['aktif', 'sudah_masuk', 'invalid'])->default('aktif');
            $table->dateTime('activated_at');
            $table->dateTime('entered_at')->nullable();
            $table->timestamps();
        });

        Schema::create('scan_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('officer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('e_ticket_id')->nullable()->constrained('e_tickets')->nullOnDelete();
            $table->foreignId('wristband_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('scan_type', ['scan_eticket', 'scan_gelang']);
            $table->enum('scan_result', ['berhasil', 'gagal']);
            $table->string('message');
            $table->dateTime('scanned_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scan_histories');
        Schema::dropIfExists('wristbands');
        Schema::dropIfExists('e_tickets');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('concerts');
        Schema::dropIfExists('officers');
        Schema::dropIfExists('admins');
    }
};
