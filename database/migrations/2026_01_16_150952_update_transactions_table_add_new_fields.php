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
        Schema::table("transactions", function (Blueprint $table) {
            $table
                ->foreignId("to_account_id")
                ->nullable()
                ->constrained("accounts")
                ->onDelete("set null");
            $table->string("reference_number")->nullable();
            $table
                ->enum("status", ["completed", "pending", "failed"])
                ->default("completed");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("transactions", function (Blueprint $table) {
            $table->dropColumn(["to_account_id", "reference_number", "status"]);
        });
    }
};
