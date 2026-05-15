<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('committees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            // Self-referencing parent
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('committees')
                ->nullOnDelete();

            // Updated by (UUID FK to users.user_id)
            $table->foreignUuid('updated_by')
                ->nullable()
                ->references('user_id')->on('users')
                ->nullOnDelete();

            $table->timestamps();
            $table->index('parent_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('committees');
    }
};
