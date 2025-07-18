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
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->string('divisi', 10)->index(); // DIVISI
            $table->string('material_sap', 50)->index(); // Material SAP
            $table->text('material_description'); // Material Description Maximo
            $table->string('base_unit_measure', 10); // Base Unit of Measure
            $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE'); // Status
            $table->string('lokasi_sistem', 50)->nullable(); // Lokasi Sistem
            $table->string('lokasi_fisik', 50)->nullable(); // Lokasi Fisik
            $table->string('store_room', 50)->nullable(); // StoreRoom
            $table->string('penempatan_alat', 100)->nullable(); // Penempatan pada Alat
            $table->text('deskripsi_penempatan')->nullable(); // Deskripsi Penempatan
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['divisi', 'status']);
            $table->index('penempatan_alat');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
