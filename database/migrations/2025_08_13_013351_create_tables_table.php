<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tables', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('number')->unique();
            $table->integer('seats');
            $table->string('location');
            $table->enum('status', ['available', 'occupied', 'reserved', 'maintenance'])->default('available');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('qr_code_url')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tables');
    }
};
