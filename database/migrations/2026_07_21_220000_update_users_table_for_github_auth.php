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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'github_id')) {
                $table->string('github_id')->nullable()->unique()->after('email');
            }
            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable()->after('github_id');
            }
            if (!Schema::hasColumn('users', 'auth_provider')) {
                $table->string('auth_provider')->default('email')->after('avatar');
            }
            $table->string('password')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['github_id', 'avatar', 'auth_provider']);
            $table->string('password')->nullable(false)->change();
        });
    }
};
