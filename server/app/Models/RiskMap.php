<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Casts\Attribute;
use \App\Casts\Json;
use App\Utils\Dates;

class RiskMap extends Model
{
    use HasFactory;

    protected $fillable = [
        'process',
        'comission',
        'author',
        'date_version',
        'version',
        'phase',
        'description',
        'comission_members',
        'riskiness',
        'accompaniment',
    ];

    protected $casts = [
        'comission_members' => Json::class,
        'riskiness' => Json::class,
        'accompaniment' => Json::class,
    ];

    public static function validateFields(?int $id = null): array
    {
        return [
            'process' => 'required',
            'comission' => 'required',
            'author' => 'required',
            'date_version' => 'required',
            'version' => 'required',
            'phase' => 'required',
            'description' => 'required',
            'comission_members' => 'required',
            'riskiness' => 'required',
            'accompaniment' => 'required',
        ];
    }

    public static function validateMsg(): array
    {
        return [
            'required' => 'Campo obrigatório não informado!'
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
            ['id' => 1, 'title' => '(PCTIC) Planejamento da Contratação'],
            ['id' => 2, 'title' => '(SFTIC) Seleção de Fornecedores'],
            ['id' => 3, 'title' => '(GCTIC) Gestão do Contrato'],
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
}
