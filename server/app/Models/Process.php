<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Process extends Model
{
    use HasFactory;

    protected $table = 'processes';

    protected $fillable = [
        'protocol',
        'ip',
        'date_ini',
        'hour_ini',
        'year_pca',
        'type',
        'modality',
        'organ',
        'units',
        'ordinators',
        'comission',
        'comission_members',
        'comission_address',
        'author',
        'object',
        'situation',
        'initial_value',
        'winner_value',
        'dfds',
    ];

    public function organ(): HasOne
    {
        return $this->hasOne(Organ::class, 'id', 'organ');
    }

    public function comission(): HasOne
    {
        return $this->hasOne(Comission::class, 'id', 'comission');
    }

    public function author(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'author');
    }

    public static function list_modalitys(): array
    {
        return [
            ['id' => 0, 'title' => 'Pregão'],
            ['id' => 1, 'title' => 'Tomada de Preço'],
            ['id' => 2, 'title' => 'Convite'],
            ['id' => 3, 'title' => 'Concorrência'],
            ['id' => 4, 'title' => 'Registro de Preço'],
            ['id' => 5, 'title' => 'Leilão'],
            ['id' => 6, 'title' => 'Concurso'],
            ['id' => 7, 'title' => 'Dispensa'],
            ['id' => 8, 'title' => 'Inexigibiliadade'],
            ['id' => 9, 'title' => 'Carona'],
            ['id' => 10, 'title' => 'Chamada'],
        ];
    }

    public static function list_situations(): array
    {
        return [
            ['id' => 0, 'title' => 'Aberta'],
            ['id' => 1, 'title' => 'Adiada'],
            ['id' => 2, 'title' => 'Anulado'],
            ['id' => 3, 'title' => 'Cancelado'],
            ['id' => 4, 'title' => 'Deserta'],
            ['id' => 5, 'title' => 'Finalizada'],
            ['id' => 6, 'title' => 'Fracassada'],
            ['id' => 7, 'title' => 'Revogada'],
        ];
    }

    public static function list_types(): array
    {
        return [
            ['id' => 0, 'title' => 'Menor Preço'],
            ['id' => 1, 'title' => 'Melhor Técnica'],
            ['id' => 2, 'title' => 'Preço e Técnica'],
            ['id' => 3, 'title' => 'Outro'],
        ];
    }
}
