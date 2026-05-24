<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courier_services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 30)->unique();
            $table->boolean('is_active')->default(true);
            $table->string('api_key')->nullable();
            $table->string('api_password')->nullable();
            $table->string('api_base_url')->nullable();
            $table->string('tracking_url_template')->nullable()->comment('{tracking_number} placeholder');
            $table->json('settings')->nullable()->comment('Extra courier-specific config');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courier_services');
    }
};
