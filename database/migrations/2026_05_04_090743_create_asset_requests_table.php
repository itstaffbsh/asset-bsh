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
        Schema::create('asset_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->foreignId('hr_manager_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('criticality_level', ['top priority', 'urgent', 'normal'])->default('normal');
            $table->text('reason');
            $table->enum('status', ['pending_hr', 'pending_dept', 'pending_it', 'pending_md', 'approved', 'rejected'])->default('pending_hr');
            $table->text('hr_comment')->nullable();
            $table->text('dept_comment')->nullable();
            $table->text('it_comment')->nullable();
            $table->text('md_comment')->nullable();
            $table->text('admin_notes')->nullable(); // For Super Admin executor notes
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_requests');
    }
};
