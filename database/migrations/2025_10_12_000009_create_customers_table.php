<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('email');
            $table->string('password')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();

            $table->string('phone')->nullable();

            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('country')->nullable();

            $table->foreignId('store_id')->constrained()->onDelete('cascade');

            $table->timestamps();

            // Prevent duplicate emails within the same store
            $table->unique(['store_id', 'email'], 'customers_store_email_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('customers');
    }
};
