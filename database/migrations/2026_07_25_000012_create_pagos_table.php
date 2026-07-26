<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->nullable()->constrained('ventas')->cascadeOnDelete();
            $table->foreignId('caja_id')->nullable()->constrained('cajas')->nullOnDelete();
            $table->string('metodo_pago')->nullable(); // EFECTIVO, QR, TRANSFERENCIA, TARJETA
            $table->decimal('monto', 12, 2)->nullable();
            $table->string('referencia_transaccion')->nullable();
            $table->dateTime('fecha_pago')->nullable();
            $table->unsignedBigInteger('usuario_creador_id')->nullable();
            $table->unsignedBigInteger('usuario_modificador_id')->nullable();
            $table->unsignedBigInteger('usuario_eliminador_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
