<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_barras')->nullable();
            $table->string('sku')->nullable();
            $table->string('nombre')->nullable();
            $table->text('descripcion')->nullable();
            $table->foreignId('categoria_id')->nullable()->constrained('categorias')->nullOnDelete();
            $table->foreignId('marca_id')->nullable()->constrained('marcas')->nullOnDelete();
            $table->foreignId('proveedor_id')->nullable()->constrained('proveedores')->nullOnDelete();
            $table->decimal('precio_compra', 12, 2)->nullable()->default(0.00);
            $table->decimal('precio_venta', 12, 2)->nullable()->default(0.00);
            $table->decimal('stock_minimo', 12, 2)->nullable()->default(5.00);
            $table->string('unidad_medida')->nullable()->default('UNIDAD');
            $table->string('imagen')->nullable();
            $table->boolean('estado')->nullable()->default(true);
            $table->unsignedBigInteger('usuario_creador_id')->nullable();
            $table->unsignedBigInteger('usuario_modificador_id')->nullable();
            $table->unsignedBigInteger('usuario_eliminador_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
