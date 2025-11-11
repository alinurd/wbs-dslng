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
           Schema::create('combos', function (Blueprint $table) {
            $table->id();
            $table->string('name');           
            $table->string('kelompok')->unique(); 
            $table->string('data')->nullable();
            $table->string('param_int')->nullable();
            $table->string('param_str')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('combos');
    }
};
