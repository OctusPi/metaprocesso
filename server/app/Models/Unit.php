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
        'id',
        'organ',
        'name',
        'cnpj',
        'phone',
        'email',
        'address',
    ];

    public function rules(): array
    {
        return [
            'organ' => 'required',
            'name'  => 'required',
            'cnpj'  => 'required'
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'Campo obrigatório não informado!'
        ];
    }

    public function organ(): HasOne
    {
        return $this->hasOne(Organ::class, 'id', 'organ');
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

    public function riskmap(): BelongsTo
    {
        return $this->belongsTo(RiskMap::class);
    }
}
