<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimientos_inventario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sucursal_id')->nullable()->constrained('sucursales')->nullOnDelete();
            $table->foreignId('producto_id')->nullable()->constrained('productos')->nullOnDelete();
            $table->string('tipo_movimiento')->nullable(); // INGRESO, SALIDA, VENTA, COMPRA, AJUSTE_ENTRADA, AJUSTE_SALIDA
            $table->decimal('cantidad', 12, 2)->nullable();
            $table->decimal('precio_compra', 12, 2)->nullable();
            $table->decimal('precio_venta', 12, 2)->nullable();
            $table->decimal('stock_anterior', 12, 2)->nullable();
            $table->decimal('stock_nuevo', 12, 2)->nullable();
            $table->string('motivo')->nullable();
            $table->string('referencia_tipo')->nullable(); // VENTA, COMPRA, AJUSTE
            $table->unsignedBigInteger('referencia_id')->nullable();
            $table->dateTime('fecha_movimiento')->nullable();
            $table->unsignedBigInteger('usuario_creador_id')->nullable();
            $table->unsignedBigInteger('usuario_modificador_id')->nullable();
            $table->unsignedBigInteger('usuario_eliminador_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos_inventario');
    }
};
