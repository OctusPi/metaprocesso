<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Validation\Rule;
use App\Casts\Json;
use App\Utils\Dates;

class Etp extends Model
{
    use HasFactory;

    protected $table = 'etps';

    protected $fillable = [
        'ip',
        'protocol',
        'emission',
        'status',
        'dfds',
        'organ',
        'comission',
        'user',
        'object_description',
        'object_classification',
        'necessity',
        'contract_forecast',
        'contract_requirements',
        'market_survey',
        'contract_calculus_memories',
        'contract_expected_price',
        'solution_full_description',
        'solution_parcel_justification',
        'correlated_contracts',
        'contract_alignment',
        'expected_results',
        'contract_previous_actions',
        'ambiental_impacts',
        'viability_declaration',
    ];

    protected $casts = [
        'dfds' => Json::class,
    ];

    public function organ(): HasOne
    {
        return $this->hasOne(Organ::class, 'id', 'organ');
    }

    public function comission(): HasOne
    {
        return $this->hasOne(Comission::class, 'id', 'comission');
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user');
    }

    public static function validateFields(?int $id = null): array
    {
        return [
            'dfds' => 'required',
            'protocol' => ['required', Rule::unique('etps')->ignore($id)],
            'ip' => 'required',
            'organ' => 'required',
            'comission' => 'required',
            'user' => 'required',
            'object_description' => 'required',
            'object_classification' => 'required',
            'necessity' => 'required',
            'contract_forecast' => 'required',
            'contract_requirements' => 'required',
            'market_survey' => 'required',
            'contract_calculus_memories' => 'required',
            'contract_expected_price' => 'required',
            'solution_full_description' => 'required',
            'solution_parcel_justification' => 'required',
            'correlated_contracts' => 'required',
            'contract_alignment' => 'required',
            'expected_results' => 'required',
            'contract_previous_actions' => 'required',
            'ambiental_impacts' => 'required',
            'viability_declaration' => 'required',
        ];
    }

    public static function validateMsg(): array
    {
        return [
            'required' => 'Campo obrigatório não informado!',
            'unique' => 'ETP já registrado no sistema!'
        ];
    }

    public static function list_status(): array
    {
        return [
            ['id' => 0, 'title' => 'Rascunho'],
            ['id' => 1, 'title' => 'Em Preenchimento'],
            ['id' => 2, 'title' => 'Pendente'],
            ['id' => 3, 'title' => 'Finalizado'],
            ['id' => 4, 'title' => 'Bloqueado'],
        ];
    }

    public function emission(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => Dates::convert($value, Dates::UTC, Dates::PTBR),
            set: fn(?string $value) => Dates::convert($value, Dates::PTBR, Dates::UTC)
        );
    }
}
