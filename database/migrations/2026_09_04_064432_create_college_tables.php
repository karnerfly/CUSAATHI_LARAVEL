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
        Schema::create('college.colleges', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200);
            $table->text('description')->nullable();
            $table->string('slug', 200)->unique();
            $table->string('type')->index();
            $table->text('thumbnail_url')->nullable();
            $table->text('website_url');
            $table->integer('established_year');
            $table->string('accreditation_body', 10);
            $table->string('accreditation_grade', 10)->index();
            $table->integer('accreditation_year');
            $table->double('accreditation_value');
            $table->longText('contact_details')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('notices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('college_id')->nullable()->constrained('college.colleges')->cascadeOnDelete();
            $table->string('title', 200);
            $table->text('description')->nullable();
            $table->string('curriculum', 10);
            $table->string('category', 10);
            $table->integer('semester');
            $table->text('resource_url');
            $table->date('published_date');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('college.locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('college_id')->unique()->constrained('college.colleges')->cascadeOnDelete();
            $table->string('address_line_1', 255);
            $table->string('address_line_2', 255)->nullable();
            $table->string('pincode', 10);
            $table->string('district', 100);
            $table->string('area_zone', 20);
            $table->string('locality_tag', 20);
            $table->text('google_map_url');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('college.images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('college_id')->constrained('college.colleges')->cascadeOnDelete();
            $table->string('group', 20);
            $table->text('url');
            $table->string('alt_text', 100);
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('college.course_types', function (Blueprint $table) {
            $table->id();
            $table->string('label', 20);
            $table->string('slug', 20);
        });

        Schema::create('college.courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_type_id')->constrained('college.course_types')->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('code', 20);
        });

        Schema::create('college.streams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('college.courses')->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('slug', 100);
        });

        Schema::create('college.college_stream', function (Blueprint $table) {
            $table->id();
            $table->foreignId('college_id')->constrained('college.colleges')->cascadeOnDelete();
            $table->foreignId('stream_id')->constrained('college.streams')->cascadeOnDelete();
            $table->string('eligibility', 255);
            $table->integer('duration');
            $table->boolean('active')->default(true);

            $table->unique(['college_id', 'stream_id']);
        });

        Schema::create('college.facilities', function (Blueprint $table) {
            $table->id();
            $table->string('type', 100);
            $table->string('label', 100);
            $table->text('note');
        });

        Schema::create('college.college_facility', function (Blueprint $table) {
            $table->foreignId('college_id')->constrained('college.colleges')->cascadeOnDelete();
            $table->foreignId('facility_id')->constrained('college.facilities')->cascadeOnDelete();

            $table->unique(['college_id', 'facility_id']);
        });

        Schema::create('college.college_stream_fee_structures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('college_stream_id')->unique()->constrained('college.college_stream')->cascadeOnDelete();
            $table->integer('fee_year');
            $table->double('admission_fee');
            $table->double('total_fee');
            $table->timestamp('verified_at')->nullable();
        });

        Schema::create('college.college_stream_cutoffs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('college_stream_id')->constrained('college.college_stream')->cascadeOnDelete();
            $table->string('category', 10);
            $table->double('marks');
            $table->timestamp('published_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('college.colleges');
        Schema::dropIfExists('notices');
        Schema::dropIfExists('college.locations');
        Schema::dropIfExists('college.images');
        Schema::dropIfExists('college.course_types');
        Schema::dropIfExists('college.courses');
        Schema::dropIfExists('college.streams');
        Schema::dropIfExists('college.college_stream');
        Schema::dropIfExists('college.facilities');
        Schema::dropIfExists('college.college_facility');
        Schema::dropIfExists('college.college_stream_fee_structures');
        Schema::dropIfExists('college.college_stream_cutoffs');
    }
};
