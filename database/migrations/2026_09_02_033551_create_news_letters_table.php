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
        Schema::create('newsletter_topics', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestampsTz();
        });

        Schema::create('newsletter_subscribers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('email')->unique();
            $table->boolean('active')->default(false);
            $table->string('frequency')->default('weekly');
            $table->timestampTz('verified_at')->nullable();
            $table->string('verification_token', 64)->nullable();
            $table->string('unsubscribe_token', 64)->unique();
            $table->timestampsTz();
        });

        Schema::create('newsletter_subscriber_topic', function (Blueprint $table) {
            $table->foreignId('subscriber_id')->constrained('newsletter_subscribers')->cascadeOnDelete();
            $table->foreignId('topic_id')->constrained('newsletter_topics')->cascadeOnDelete();
            $table->primary(['subscriber_id', 'topic_id']);
        });

        Schema::create('newsletters', function (Blueprint $table) {
            $table->id();
            $table->string('subject');
            $table->foreignId('topic_id')->nullable()->constrained('newsletter_topics')->nullOnDelete();
            $table->text('content');
            $table->timestampTz('scheduled_for')->nullable();
            $table->timestampTz('sent_at')->nullable();
            $table->timestampsTz();
        });

        Schema::create('newsletter_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('newsletter_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subscriber_id')->constrained('newsletter_subscribers')->cascadeOnDelete();
            $table->timestampTz('sent_at');
            $table->timestampTz('opened_at')->nullable();
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('newsletter_logs');
        Schema::dropIfExists('newsletters');
        Schema::dropIfExists('newsletter_subscriber_topic');
        Schema::dropIfExists('newsletter_subscribers');
        Schema::dropIfExists('newsletter_topics');
    }
};
