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
        Schema::create('product_histories', function (Blueprint $table) {
            $table->id();
            $table->string('batch_id')->nullable();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('dioper_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('diterima_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->string('jenis_transaksi')->default('meminjam');
            $table->integer('quantity')->default(1);
            $table->foreignId('pihak_pertama_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('tanggal_pinjam')->nullable();
            $table->timestamp('lend_date')->nullable();
            $table->timestamp('returned_date')->nullable();
            $table->text('description')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_histories');
    }
};
