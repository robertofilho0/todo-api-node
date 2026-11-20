<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tarefas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('titulo', 120);
            $table->text('descricao')->nullable();
            $table->string('status', 20)->default('pendente');
            $table->string('prioridade', 10)->default('media');
            $table->unsignedTinyInteger('peso_prioridade')->default(2);
            $table->date('data_limite')->nullable();
            $table->timestamp('concluida_em')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'data_limite']);
        });

        Schema::create('etiquetas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('nome', 30);
            $table->timestamps();

            $table->unique(['user_id', 'nome']);
        });

        Schema::create('etiqueta_tarefa', function (Blueprint $table) {
            $table->foreignId('etiqueta_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tarefa_id')->constrained()->cascadeOnDelete();
            $table->primary(['etiqueta_id', 'tarefa_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('etiqueta_tarefa');
        Schema::dropIfExists('etiquetas');
        Schema::dropIfExists('tarefas');
    }
};
