<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Create roles table if not exists
        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('description')->nullable();
                $table->timestamps();
            });

            DB::table('roles')->insert([
                ['name' => 'client', 'description' => 'Regular banking client', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'employee', 'description' => 'Bank employee', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'admin', 'description' => 'System administrator', 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        // Add role_id to users table if not exists
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'role_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('role_id')->default(1)->after('password')->constrained('roles');
            });
        }

        // Create accounts table
        if (!Schema::hasTable('accounts')) {
            Schema::create('accounts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->string('account_number')->unique();
                $table->decimal('balance', 15, 2)->default(0);
                $table->timestamps();
            });
        }

        // Create transactions table
        if (!Schema::hasTable('transactions')) {
            Schema::create('transactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('account_id')->constrained('accounts')->onDelete('cascade');
                $table->enum('type', ['credit', 'debit']);
                $table->decimal('amount', 15, 2);
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        // Don't drop tables in rollback to avoid data loss
        // Schema::dropIfExists('transactions');
        // Schema::dropIfExists('accounts');
        // Schema::dropIfExists('roles');
    }
};