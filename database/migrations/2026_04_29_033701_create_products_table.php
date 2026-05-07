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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->text('description')->nullable();
            $table->foreignId('classification_id')->constrained('classifications')->onDelete('cascade');
            $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');
            $table->string('nomor_unik');
            $table->string('full_nomor_unik')->nullable();
            $table->string('url_token')->unique();
            $table->string('serial_number')->nullable();
            $table->string('processor')->nullable();
            $table->string('ram')->nullable();
            $table->string('new_ram')->nullable();
            $table->string('ssd')->nullable();
            $table->string('new_ssd')->nullable();
            $table->string('os')->nullable();
            $table->string('screen_id')->nullable();
            $table->string('imei')->nullable();
            $table->string('mac_address')->nullable();
            $table->string('device_class')->nullable();
            $table->bigInteger('harga')->default(0);
            $table->string('deletion_reason')->nullable();
            $table->bigInteger('selling_price')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
