<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Etiqueta extends Model
{
    protected $fillable = ['nome'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tarefas(): BelongsToMany
    {
        return $this->belongsToMany(Tarefa::class);
    }
}
