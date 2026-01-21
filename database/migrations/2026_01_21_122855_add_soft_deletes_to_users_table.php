<?php

declare(strict_types = 1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Only add deleted_at if it doesn't exist
        if (! Schema::hasColumn('users', 'deleted_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Migrate users with status='inactive' to soft deleted
        DB::table('users')
            ->where('status', 'inactive')
            ->update(['deleted_at' => now(), 'status' => 'active']);

        // Update enum to remove 'inactive' option (MySQL only)
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN status ENUM('pending', 'active') DEFAULT 'pending' NOT NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore enum with 'inactive' option (MySQL only)
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN status ENUM('pending', 'active', 'inactive') DEFAULT 'pending' NOT NULL");
        }

        // Migrate soft deleted users back to status='inactive'
        DB::table('users')
            ->whereNotNull('deleted_at')
            ->update(['status' => 'inactive', 'deleted_at' => null]);

        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
