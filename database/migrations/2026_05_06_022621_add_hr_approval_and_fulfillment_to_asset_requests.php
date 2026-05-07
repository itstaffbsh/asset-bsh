<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('asset_request_items', function (Blueprint $table) {
            $table->boolean('hr_approval')->default(true)->after('qty');
        });

        // Update enum status in asset_requests table
        DB::statement("ALTER TABLE asset_requests MODIFY COLUMN status ENUM('pending_hr', 'pending_dept', 'pending_it', 'pending_md', 'pending_fulfillment', 'approved', 'rejected') DEFAULT 'pending_hr'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asset_request_items', function (Blueprint $table) {
            $table->dropColumn('hr_approval');
        });
        
        DB::statement("ALTER TABLE asset_requests MODIFY COLUMN status ENUM('pending_hr', 'pending_dept', 'pending_it', 'pending_md', 'approved', 'rejected') DEFAULT 'pending_hr'");
    }
};
