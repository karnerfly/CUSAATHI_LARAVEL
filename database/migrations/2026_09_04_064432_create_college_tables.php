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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('college.colleges');
        Schema::dropIfExists('college.notices');
        Schema::dropIfExists('college.locations');
    }
};
