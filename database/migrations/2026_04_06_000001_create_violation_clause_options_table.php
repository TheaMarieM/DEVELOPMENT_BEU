<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('violation_clause_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('violation_clause_id')->constrained('violation_clauses')->onDelete('cascade');
            $table->string('label');
            $table->text('description');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('violation_clause_options');
    }
};
