<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Process extends Model
{
    use HasFactory;

    public const S_ABERTA = 0;
    public const S_ADIADA = 1;
    public const S_ANULADO = 2;
    public const S_CANCELADO = 3;
    public const S_DESERTA = 4;
    public const S_FINALIZADA = 5;
    public const S_FRACASSADA = 6;
    public const S_REVOGADA = 7;

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

    public function pricerecord():BelongsTo
    {
        return $this->belongsTo(PriceRecord::class);
    }

    public function proposal():BelongsTo
    {
        return $this->belongsTo(Proposal::class);
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
            ['id' => self::S_ABERTA, 'title' => 'Aberta'],
            ['id' => self::S_ADIADA, 'title' => 'Adiada'],
            ['id' => self::S_ANULADO, 'title' => 'Anulado'],
            ['id' => self::S_CANCELADO, 'title' => 'Cancelado'],
            ['id' => self::S_DESERTA, 'title' => 'Deserta'],
            ['id' => self::S_FINALIZADA, 'title' => 'Finalizada'],
            ['id' => self::S_FRACASSADA, 'title' => 'Fracassada'],
            ['id' => self::S_REVOGADA, 'title' => 'Revogada'],
        ];
    }

    public function list_types(): array
    {
        return [
            ['id' => 0, 'title' => 'Menor preço'],
            ['id' => 1, 'title' => 'Maior desconto'],
            ['id' => 2, 'title' => 'Melhor técnica ou conteúdo artístico'],
            ['id' => 3, 'title' => 'Técnica e preço'],
            ['id' => 4, 'title' => 'Maior lance, no caso de leilão'],
            ['id' => 5, 'title' => 'Maior retorno econômico'],
        ];
    }
}
