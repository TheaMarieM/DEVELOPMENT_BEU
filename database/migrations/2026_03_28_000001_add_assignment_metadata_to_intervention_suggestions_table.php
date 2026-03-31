<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('intervention_suggestions', function (Blueprint $table) {
            $table->string('assigned_to')->nullable()->after('suggestion');
            $table->date('assignment_due_at')->nullable()->after('assigned_to');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('intervention_suggestions', function (Blueprint $table) {
            $table->dropColumn(['assigned_to', 'assignment_due_at']);
        });
    }
};
