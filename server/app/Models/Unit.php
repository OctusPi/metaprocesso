<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Unit extends Model
{
    use HasFactory;

    protected $table = 'units';

    protected $fillable = [
        'id',
        'organ_id',
        'name',
        'cnpj',
        'phone',
        'email',
        'address',
    ];

    public static function list_status(): array
    {
        return [
            ['id' => 0, 'title' => 'Inativo'],
            ['id' => 1, 'title' => 'Ativo']
        ];
    }

    public function rules(): array
    {
        return [
            'organ_id' => 'required',
            'name' => 'required',
            'cnpj' => 'required'
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'Campo obrigatório não informado!'
        ];
    }

    public function organ(): BelongsTo
    {
        return $this->belongsTo(Organ::class);
    }

    public function sectors():HasMany
    {
        return $this->hasMany(Sector::class);
    }

    public function programs():HasMany
    {
        return $this->hasMany(Program::class);
    }

    public function dotations():HasMany
    {
        return $this->hasMany(Dotation::class);
    }

    public function demandants():HasMany
    {
        return $this->hasMany(Demandant::class);
    }

    public function ordinators():HasMany
    {
        return $this->hasMany(Ordinator::class);
    }

    public function comissions():HasMany
    {
        return $this->hasMany(Comission::class);
    }

    public function comissionMembers(): HasMany
    {
        return $this->hasMany(ComissionMember::class);
    }

    public function comissionEnds(): HasMany
    {
        return $this->hasMany(ComissionEnd::class);
    }

    public function dfds():HasMany
    {
        return $this->hasMany(Dfd::class);
    }
}
