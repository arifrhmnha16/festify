<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_zones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('concert_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->unsignedInteger('price');
            $table->unsignedInteger('stock');
            $table->string('color')->default('#ea580c');
            $table->unsignedTinyInteger('position')->default(1);
            $table->timestamps();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('ticket_zone_id')->nullable()->after('concert_id')->constrained('ticket_zones')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('ticket_zone_id');
        });

        Schema::dropIfExists('ticket_zones');
    }
};
