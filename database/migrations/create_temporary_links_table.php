<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('temporary_links', function (Blueprint $table) {
            $table->id();
            $table->string('token', 64)->unique();
            $table->nullableMorphs('linkable');
            $table->string('path')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('device_signature')->nullable();
            $table->boolean('single_use')->default(false);
            $table->boolean('is_used')->default(false);
            $table->integer('access_count')->default(0);
            $table->timestamp('last_accessed_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            $table->index('token');
        });
    }

    public function down()
    {
        Schema::dropIfExists('temporary_links');
    }
};