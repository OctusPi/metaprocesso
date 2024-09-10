<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Casts\Attribute;
use \App\Casts\Json;
use App\Utils\Dates;
use Illuminate\Validation\Rule;

class RiskMap extends Model
{
    use HasFactory;

    protected $table = 'risk_maps';

    protected $fillable = [
        'id',
        'process',
        'comission',
        'organ',
        'author',
        'date_version',
        'version',
        'phase',
        'description',
        'comission_members',
        'riskiness',
        'accompaniments',
    ];

    protected $casts = [
        'comission_members' => Json::class,
        'riskiness' => Json::class,
        'accompaniments' => Json::class,
    ];

    public function rules(): array
    {
        return [
            'process' => ['required', Rule::unique('risk_maps', 'process')->ignore($this->id)],
            'comission' => 'required',
            'organ' => 'required',
            'author' => 'required',
            'date_version' => 'required',
            'version' => 'required',
            'phase' => 'required',
            'description' => 'required',
            'comission_members' => 'required',
            'riskiness' => 'required',
            'accompaniments' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'Campo obrigatório não informado!',
            'unique' => 'Já existe um mapa de risco vinculado ao processo...'
        ];
    }

    public function dateVersion(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => Dates::convert($value, Dates::UTC, Dates::PTBR),
            set: fn(?string $value) => Dates::convert($value, Dates::PTBR, Dates::UTC)
        );
    }

    static function list_phases(): array
    {
        return [
            ['id' => 1, 'title' => 'Planejamento da Contratação (PCTIC)', 'code' => 'PCTIC'],
            ['id' => 2, 'title' => 'Seleção de Fornecedores (SFTIC)', 'code' => 'SFTIC'],
            ['id' => 3, 'title' => 'Gestão do Contrato (GCTIC)', 'code' => 'GCTIC'],
        ];
    }

    static function list_impacts(): array
    {
        return [
            ['id' => 1, 'title' => 'Baixo', 'value' => 5],
            ['id' => 2, 'title' => 'Médio', 'value' => 10],
            ['id' => 3, 'title' => 'Alto', 'value' => 15],
        ];
    }

    static function list_probabilities(): array
    {
        return [
            ['id' => 1, 'title' => 'Baixa', 'value' => 5],
            ['id' => 2, 'title' => 'Média', 'value' => 10],
            ['id' => 3, 'title' => 'Alta', 'value' => 15],
        ];
    }

    static function list_actions(): array
    {
        return [
            ['id' => 1, 'title' => 'Ação Preventiva', 'code' => 'P'],
            ['id' => 2, 'title' => 'Ação de Contingência', 'code' => 'C'],
        ];
    }

    public function process(): HasOne
    {
        return $this->hasOne(Process::class, 'id', 'process');
    }

    public function comission(): HasOne
    {
        return $this->hasOne(Comission::class, 'id', 'comission');
    }

    public function author(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'author');
    }

    public function organ(): HasOne
    {
        return $this->hasOne(Organ::class, 'id', 'organ');
    }
}
