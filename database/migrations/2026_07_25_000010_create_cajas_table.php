<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cajas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sucursal_id')->nullable()->constrained('sucursales')->nullOnDelete();
            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('monto_apertura', 12, 2)->nullable();
            $table->decimal('monto_cierre', 12, 2)->nullable();
            $table->decimal('ventas_efectivo', 12, 2)->nullable()->default(0.00);
            $table->decimal('ventas_digital', 12, 2)->nullable()->default(0.00);
            $table->decimal('total_esperado', 12, 2)->nullable();
            $table->decimal('diferencia', 12, 2)->nullable();
            $table->string('estado')->nullable()->default('ABIERTA'); // ABIERTA, CERRADA
            $table->dateTime('fecha_apertura')->nullable();
            $table->dateTime('fecha_cierre')->nullable();
            $table->text('observaciones')->nullable();
            $table->unsignedBigInteger('usuario_creador_id')->nullable();
            $table->unsignedBigInteger('usuario_modificador_id')->nullable();
            $table->unsignedBigInteger('usuario_eliminador_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cajas');
    }
};
