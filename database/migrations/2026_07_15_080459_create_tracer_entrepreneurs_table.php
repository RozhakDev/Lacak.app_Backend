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
        Schema::create('tracer_entrepreneurs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tracer_submission_id')->constrained('tracer_submissions')->cascadeOnDelete();
            $table->enum('ownership_type', ['sendiri', 'orang_tua']);
            $table->integer('employee_count')->default(0);
            $table->enum('monthly_omset_range', ['<_5_juta', '5_-_15_juta', '>_15_juta']);
            $table->string('business_type');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracer_entrepreneurs');
    }
};
