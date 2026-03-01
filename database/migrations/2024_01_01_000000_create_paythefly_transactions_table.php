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
        Schema::create('paythefly_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('project_id', 64)->index();
            $table->string('serial_no', 128)->unique();
            $table->string('chain_symbol', 16);
            $table->string('tx_hash', 128)->nullable()->index();
            $table->string('wallet', 64)->nullable();
            $table->decimal('value', 36, 18)->default(0);
            $table->decimal('fee', 36, 18)->default(0);
            $table->enum('tx_type', ['payment', 'withdrawal'])->default('payment');
            $table->boolean('confirmed')->default(false);
            $table->string('token_address', 64)->nullable();
            $table->string('payment_url', 2048)->nullable();
            $table->timestamp('deadline_at')->nullable();
            $table->string('signature', 132)->nullable();
            $table->timestamp('webhook_received_at')->nullable();
            $table->timestamps();

            $table->index(['serial_no', 'confirmed']);
            $table->index('tx_hash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paythefly_transactions');
    }
};
