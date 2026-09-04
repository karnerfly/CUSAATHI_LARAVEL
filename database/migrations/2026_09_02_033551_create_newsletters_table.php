<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('newsletter.topics', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('newsletter.subscribers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('public.users')->nullOnDelete();
            $table->string('email')->unique();
            $table->boolean('active')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->string('verification_token', 64)->nullable();
            $table->string('unsubscribe_token', 64)->unique();
            $table->timestamps();
        });

        Schema::create('newsletter.subscriber_topic', function (Blueprint $table) {
            $table->foreignId('subscriber_id')->constrained('newsletter.subscribers')->cascadeOnDelete();
            $table->foreignId('topic_id')->constrained('newsletter.topics')->cascadeOnDelete();
            $table->primary(['subscriber_id', 'topic_id']);
        });

        Schema::create('newsletter.newsletters', function (Blueprint $table) {
            $table->id();
            $table->string('subject');
            $table->foreignId('topic_id')->nullable()->constrained('newsletter.topics')->nullOnDelete();
            $table->text('content');
            $table->timestamp('scheduled_for')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });

        Schema::create('newsletter.logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('newsletter_id')->constrained('newsletter.newsletters')->cascadeOnDelete();
            $table->foreignId('subscriber_id')->constrained('newsletter.subscribers')->cascadeOnDelete();
            $table->timestamp('sent_at');
            $table->timestamp('opened_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('newsletter.topics');
        Schema::dropIfExists('newsletter.subscribers');
        Schema::dropIfExists('newsletter.newsletters');
        Schema::dropIfExists('newsletter.subscriber_topic');
        Schema::dropIfExists('newsletter.logs');
    }
};
