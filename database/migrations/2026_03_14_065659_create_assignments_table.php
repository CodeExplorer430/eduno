<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_section_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->longText('instructions')->nullable();
            $table->timestamp('due_at')->nullable();
            $table->decimal('max_score', 7, 2)->default(100);
            $table->boolean('allow_resubmission')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['course_section_id', 'due_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
