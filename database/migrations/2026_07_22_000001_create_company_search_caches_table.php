<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_search_caches', function (Blueprint $table) {
            $table->id();
            $table->string('search_hash')->unique();
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->integer('radius');
            $table->string('category')->nullable();
            $table->json('response_data');
            $table->timestamp('consulted_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_search_caches');
    }
};
