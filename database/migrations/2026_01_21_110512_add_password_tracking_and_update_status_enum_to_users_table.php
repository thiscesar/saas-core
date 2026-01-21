<?php

declare(strict_types = 1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            // Add password tracking field
            $table->timestamp('password_set_at')->nullable()->after('password');

            // Update status field to enum with inactive option
            $table->enum('status', ['pending', 'active', 'inactive'])->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn('password_set_at');

            // Revert status field to string
            $table->string('status')->default('active')->change();
        });
    }
};
