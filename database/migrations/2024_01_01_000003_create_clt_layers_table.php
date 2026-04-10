<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clt_layers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('layup_id')
                ->constrained('clt_layups')
                ->cascadeOnDelete();
            $table->unsignedInteger('layer_order');
            $table->decimal('thickness', 10, 4); // mm, up to 6 integer digits + 4 decimal
            $table->decimal('width', 10, 4);     // mm
            $table->decimal('angle', 7, 4);      // degrees, e.g. -90.0000 to 90.0000
            $table->timestamps();
            $table->softDeletes();

            // layer_order must be unique within a layup
            $table->unique(['layup_id', 'layer_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clt_layers');
    }
};
