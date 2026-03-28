<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_analytics_sessions', function (Blueprint $table) {
            $table->id();
            $table->uuid('visitor_token')->index();
            $table->uuid('session_token')->unique();
            $table->string('entry_path', 255)->nullable();
            $table->string('entry_title', 255)->nullable();
            $table->string('entry_referrer', 255)->nullable();
            $table->string('entry_referrer_host', 190)->nullable()->index();
            $table->string('browser', 100)->nullable()->index();
            $table->string('device', 50)->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->unsignedInteger('page_views_count')->default(0)->index();
            $table->timestamp('first_seen_at')->nullable()->index();
            $table->timestamp('last_seen_at')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('site_analytics_page_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_analytics_session_id')->constrained('site_analytics_sessions')->cascadeOnDelete();
            $table->uuid('visitor_token')->index();
            $table->string('route_name', 190)->nullable()->index();
            $table->string('page_title', 255)->nullable()->index();
            $table->string('page_path', 255)->index();
            $table->string('full_url', 512)->nullable();
            $table->string('referrer_url', 512)->nullable();
            $table->string('referrer_host', 190)->nullable()->index();
            $table->string('browser', 100)->nullable()->index();
            $table->string('device', 50)->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_analytics_page_views');
        Schema::dropIfExists('site_analytics_sessions');
    }
};
