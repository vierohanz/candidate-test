<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('clt_layers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('layup_id')
                ->constrained('clt_layups')
                ->cascadeOnDelete();
            $table->unsignedInteger('layer_order');
            $table->decimal('thickness', 10, 2);
            $table->decimal('width', 10, 2);
            $table->decimal('angle', 7, 2);
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['layup_id', 'layer_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clt_layers');
    }
};
