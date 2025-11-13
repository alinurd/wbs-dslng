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
        Schema::create('form_meta', function (Blueprint $table) {
            $table->id();

            // Nama model atau modul (posts, users, products)
            $table->string('model');

            // Optional category group
            $table->string('category')->nullable();

            // Kolom yang ingin dibuatkan field
            $table->string('field_name');
            $table->string('label')->nullable();

            // text, textarea, number, select, radio, file
            $table->string('type')->default('text');

            // Validasi Laravel: required|string|max:255
            $table->string('rules')->nullable();

            // JSON untuk select/radio
            $table->json('options')->nullable();

            // Field RBAC
            $table->json('visible_roles')->nullable();
            $table->json('editable_roles')->nullable();

            // Urutan tampil
            $table->integer('order')->default(0);

            // Apakah aktif
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // Index untuk mempercepat query
            $table->index(['model', 'category']);
            $table->index('field_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_meta');
    }
};
