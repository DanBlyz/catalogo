<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rol extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $table = 'roles';

    protected $fillable = [
        'nombre',
        'codigo',
        'descripcion',
        'usuario_creador_id',
        'usuario_modificador_id',
        'usuario_eliminador_id',
    ];

    public function usuarios(): HasMany
    {
        return $this->hasMany(User::class, 'rol_id');
    }
}
