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
        Schema::create('user_infos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('home_address');
            $table->string('father_name')->nullable();
            $table->string('phone')->nullable();
            $table->date('date_of_birth');
            $table->string('nic')->nullable();
            $table->date('nic_expire_date')->nullable();
            $table->string('passport_no')->nullable();
            $table->date('passport_expire_date')->nullable();
            $table->string('religion')->nullable();
            $table->string('profile_picture')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_infos');
    }
};
