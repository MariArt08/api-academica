<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Estudiante extends Model
{
    protected $fillable = ['user_id', 'carrera_id', 'carnet', 'nombre', 'activo'];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function carrera(): BelongsTo
    {
        return $this->belongsTo(Carrera::class);
    }
}
