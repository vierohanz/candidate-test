<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clt_layups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')
                ->constrained('suppliers')
                ->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();

            // A supplier cannot have two layups with the same name (used for conflict detection on import)
            $table->unique(['supplier_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clt_layups');
    }
};
