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
        Schema::create('tracer_works', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tracer_submission_id')->constrained('tracer_submissions')->cascadeOnDelete();
            $table->enum('location_scale', ['dalam_kota', 'luar_kota']);
            $table->enum('location_country', ['dalam_negeri', 'luar_negeri']);
            $table->string('field_of_work');
            $table->enum('salary_range', ['<_umr', 'umr_-_5_juta', '5_-_10_juta', '>_10_juta']);
            $table->string('company_name');
            $table->string('position');
            $table->date('start_date');
            $table->boolean('is_linear');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracer_works');
    }
};
