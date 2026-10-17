<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('project_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->unsignedInteger('priority');
            $table->timestamp('created_at');
            $table->timestamp('updated_at');

            // Not unique: a reorder swaps priorities row by row, see docs/architecture-decisions.md.
            $table->index(['project_id', 'priority']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
