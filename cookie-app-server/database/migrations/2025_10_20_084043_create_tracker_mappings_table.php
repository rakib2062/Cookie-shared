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
        Schema::create('tracker_mappings', function (Blueprint $table) {
            $table->id();
            $table->uuid('tracker_id')->unique();
            $table->string('origin')->nullable();          // initial origin (siteA/siteB)
            $table->string('site_user_id')->nullable();    // optional user id from origin site (hashed or token)
            $table->ipAddress('first_ip')->nullable();
            $table->ipAddress('last_ip')->nullable();
            $table->text('first_user_agent')->nullable();
            $table->text('last_user_agent')->nullable();
            $table->timestamp('first_seen')->nullable();
            $table->timestamp('last_seen')->nullable();
            $table->unsignedInteger('visit_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracker_mappings');
    }
};
