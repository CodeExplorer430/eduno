<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->longText('content')->nullable();
            $table->string('type')->default('text');
            $table->unsignedSmallInteger('order_no')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['module_id', 'order_no']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
