<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categorias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->nullable();
            $table->string('descripcion')->nullable();
            $table->unsignedBigInteger('usuario_creador_id')->nullable();
            $table->unsignedBigInteger('usuario_modificador_id')->nullable();
            $table->unsignedBigInteger('usuario_eliminador_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('marcas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->nullable();
            $table->string('descripcion')->nullable();
            $table->unsignedBigInteger('usuario_creador_id')->nullable();
            $table->unsignedBigInteger('usuario_modificador_id')->nullable();
            $table->unsignedBigInteger('usuario_eliminador_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marcas');
        Schema::dropIfExists('categorias');
    }
};
