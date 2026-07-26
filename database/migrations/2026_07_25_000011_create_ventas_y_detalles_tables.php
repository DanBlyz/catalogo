<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->string('numero_recibo')->nullable();
            $table->foreignId('sucursal_id')->nullable()->constrained('sucursales')->nullOnDelete();
            $table->foreignId('caja_id')->nullable()->constrained('cajas')->nullOnDelete();
            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $table->decimal('subtotal', 12, 2)->nullable();
            $table->decimal('descuento_general', 12, 2)->nullable()->default(0.00);
            $table->decimal('total', 12, 2)->nullable();
            $table->decimal('monto_pagado', 12, 2)->nullable();
            $table->decimal('cambio', 12, 2)->nullable()->default(0.00);
            $table->string('metodo_pago_principal')->nullable(); // EFECTIVO, QR, TRANSFERENCIA, TARJETA, MIXTO
            $table->string('estado')->nullable()->default('COMPLETADA'); // COMPLETADA, ANULADA
            $table->dateTime('fecha_venta')->nullable();
            $table->unsignedBigInteger('usuario_creador_id')->nullable();
            $table->unsignedBigInteger('usuario_modificador_id')->nullable();
            $table->unsignedBigInteger('usuario_eliminador_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('detalle_ventas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->nullable()->constrained('ventas')->cascadeOnDelete();
            $table->foreignId('producto_id')->nullable()->constrained('productos')->nullOnDelete();
            $table->decimal('cantidad', 12, 2)->nullable();
            $table->decimal('precio_unitario', 12, 2)->nullable();
            $table->decimal('descuento_unitario', 12, 2)->nullable()->default(0.00);
            $table->decimal('subtotal', 12, 2)->nullable();
            $table->unsignedBigInteger('usuario_creador_id')->nullable();
            $table->unsignedBigInteger('usuario_modificador_id')->nullable();
            $table->unsignedBigInteger('usuario_eliminador_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalle_ventas');
        Schema::dropIfExists('ventas');
    }
};
