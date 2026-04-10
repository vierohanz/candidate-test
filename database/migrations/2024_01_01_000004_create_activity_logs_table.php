<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();

            // What happened
            $table->string('action');          // created | updated | deleted | exported | imported

            // Which entity was affected
            $table->string('entity_type');     // supplier | clt_layup | clt_layer
            $table->unsignedBigInteger('entity_id')->nullable(); // null for bulk actions (import/export)

            // Snapshot of data for audit trail
            $table->json('before')->nullable(); // state before the action
            $table->json('after')->nullable();  // state after the action

            // Context
            $table->string('ip_address', 45)->nullable();
            $table->text('description')->nullable(); // human-readable summary

            $table->timestamp('created_at')->useCurrent();

            $table->index(['entity_type', 'entity_id']);
            $table->index('action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
