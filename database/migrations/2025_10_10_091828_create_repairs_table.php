<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('repairs', function (Blueprint $table) {
            $table->id();
            $table->morphs('repairable'); // สร้างคอลัมน์ repairable_id และ repairable_type
            $table->text('description')->nullable();
            $table->date('warantee')->nullable();
            $table->text('caseno')->nullable();
            $table->timestamp('claimnotidate');
            $table->timestamp('completed_at')->nullable();
            $table->string('status')->default('pending'); // กำหนดค่าเริ่มต้น
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repairs');
    }
};
