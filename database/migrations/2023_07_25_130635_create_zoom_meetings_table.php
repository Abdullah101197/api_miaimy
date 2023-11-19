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
        Schema::create('zoom_meetings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('appointment_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('meeting_id');
            $table->string('uuid')->nullable();
            $table->string('host_id', 1000)->nullable();
            $table->string('host_email')->nullable();
            $table->string('topic')->nullable();
            $table->integer('type')->nullable();
            $table->string('status')->nullable();
            $table->timestamp('start_time')->nullable();
            $table->integer('duration')->nullable();
            $table->string('timezone')->nullable();
            $table->string('start_url', 1000)->nullable();
            $table->string('join_url', 1000)->nullable();
            $table->string('password')->nullable();
            $table->string('h323_password')->nullable();
            $table->string('pstn_password')->nullable();
            $table->string('encrypted_password')->nullable();
            $table->json('settings')->nullable();
            $table->boolean('pre_schedule')->nullable();
            $table->timestamps();
            $table->foreign('appointment_id')->references('id')->on('appointments')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zoom_meetings');
    }
};
