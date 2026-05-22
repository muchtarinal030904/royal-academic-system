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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('nim')->unique();
            $table->string('major');
            $table->string('faculty');
            $table->integer('admission_year');
            $table->decimal('gpa', 3, 2);
            $table->string('status')->default('Aktif'); // Aktif, Lulus, Cuti, DropOut
            $table->string('certificate_status')->default('Belum Cetak'); // Belum Cetak, Antrean Cetak, Sudah Cetak
            $table->string('certificate_number')->unique()->nullable();
            $table->date('graduation_date')->nullable();
            $table->string('degree')->nullable();
            $table->string('photo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
