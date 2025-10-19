<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_integrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->string('provider');  
            $table->string('provider_id')->nullable(); 
            $table->string('status')->default('inactive');
            $table->json('metadata')->nullable(); 
            $table->text('credentials')->nullable(); 
            $table->boolean('is_configured')->default(false);
            $table->boolean('is_enabled')->default(false);
            $table->timestamps();

            // Ensure one integration per provider per store
            $table->unique(['store_id', 'provider']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_integrations');
    }
};