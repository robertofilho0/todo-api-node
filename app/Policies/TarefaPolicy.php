<?php

namespace App\Policies;

use App\Models\Tarefa;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TarefaPolicy
{
    /** Só o dono acessa a tarefa. Para os demais, ela "não existe" (404). */
    public function manage(User $user, Tarefa $tarefa): Response
    {
        return $user->id === $tarefa->user_id ? Response::allow() : Response::denyAsNotFound('Tarefa não encontrada.');
    }
}
