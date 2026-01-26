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
        Schema::table("accounts", function (Blueprint $table) {
            $table
                ->enum("account_type", ["checking", "savings", "business"])
                ->default("checking");
            $table
                ->enum("status", ["active", "inactive", "frozen"])
                ->default("active");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("accounts", function (Blueprint $table) {
            $table->dropColumn(["account_type", "status"]);
        });
    }
};
