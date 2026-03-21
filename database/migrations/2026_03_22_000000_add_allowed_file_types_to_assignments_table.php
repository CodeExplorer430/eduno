<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('assignments', function (Blueprint $table): void {
            $table->json('allowed_file_types')->nullable()->after('allow_resubmission');
        });
    }

    public function down(): void
    {
        Schema::table('assignments', function (Blueprint $table): void {
            $table->dropColumn('allowed_file_types');
        });
    }
};
