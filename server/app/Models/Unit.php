<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Unit extends Model
{
    use HasFactory;

    protected $table = 'units';

    protected $fillable = [
        'organ',
        'name',
        'cnpj',
        'phone',
        'email',
        'address',
    ];

    public function organ(): HasOne
    {
        return $this->hasOne(Organ::class, 'id');
    }

    public function dfd(): BelongsTo
    {
        return $this->belongsTo(Dfd::class);
    }

    public function comission(): BelongsTo
    {
        return $this->belongsTo(Comission::class);
    }

    public function comissionend(): BelongsTo
    {
        return $this->belongsTo(ComissionEnd::class);
    }

    public function comissionmember(): BelongsTo
    {
        return $this->belongsTo(ComissionMember::class);
    }

    public function demandant(): BelongsTo
    {
        return $this->belongsTo(Demandant::class);
    }

    public function ordinator(): BelongsTo
    {
        return $this->belongsTo(Ordinator::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function sector(): BelongsTo
    {
        return $this->belongsTo(Sector::class);
    }

    public static function validateFields(?int $id = null): array
    {
        return [
            'organ' => 'required',
            'name' => 'required',
            'cnpj' => 'required'
        ];
    }

    public static function validateMsg(): array
    {
        return [
            'required' => 'Campo obrigatório não informado!'
        ];
    }
}
