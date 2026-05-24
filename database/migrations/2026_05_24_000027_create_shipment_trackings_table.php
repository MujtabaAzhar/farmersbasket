<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipment_trackings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained()->cascadeOnDelete();
            $table->string('status', 60);
            $table->string('description');
            $table->string('location')->nullable();
            $table->datetime('event_time');
            $table->enum('source', ['api', 'manual'])->default('manual');
            $table->json('raw_data')->nullable();
            $table->timestamps();

            $table->index(['shipment_id', 'event_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipment_trackings');
    }
};
