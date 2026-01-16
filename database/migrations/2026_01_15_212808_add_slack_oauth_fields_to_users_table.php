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
            $table->string('slack_id')->nullable()->unique()->after('email');
            $table->text('slack_access_token')->nullable()->after('slack_id');
            $table->text('slack_refresh_token')->nullable()->after('slack_access_token');
            $table->string('avatar_url')->nullable()->after('slack_refresh_token');
            $table->string('password')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn(['slack_id', 'slack_access_token', 'slack_refresh_token', 'avatar_url']);
            $table->string('password')->nullable(false)->change();
        });
    }
};
