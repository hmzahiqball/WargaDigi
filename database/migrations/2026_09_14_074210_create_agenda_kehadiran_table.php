<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agenda_kehadiran', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('agenda_id')->constrained('agenda')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status_kehadiran', ['Hadir', 'Tidak Hadir', 'Ragu-ragu']);
            $table->timestamps();

            $table->unique(['agenda_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agenda_kehadiran');
    }
};
