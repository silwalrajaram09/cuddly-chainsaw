<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_committees', function (Blueprint $table) {
            $table->id();

            $table->foreignUuid('user_id')
                ->references('user_id')->on('users')
                ->cascadeOnDelete();

            $table->foreignId('committee_id')
                ->constrained('committees')
                ->cascadeOnDelete();

           // e.g. president, secretary, member
            $table->float('order_no')->default(0.0);
           
            $table->foreignUuid('updated_by')
                ->nullable()
                ->references('user_id')->on('users')
                ->nullOnDelete();

            $table->timestamps();
            $table->unique(['user_id', 'committee_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_committees');
    }
};
