<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tarefas', function (Blueprint $table) {
            $table->id('idtarefa');
            $table->unsignedBigInteger('idusuario');
            $table->unsignedBigInteger('idcategoria');
            $table->text('descricao');
            $table->dateTime('data');
            $table->boolean('cumprida');
            $table->foreign('idusuario')->references('id')->
            on('users')->onDelete('no action')->
            onUpdate('no action');
            $table->foreign('idcategoria')->
            references('idcategoria')->on('categorias')->
            onDelete('no action')->onUpdate('no action');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tarefas');
    }
};
