<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Utils\Dates;
use App\Casts\Json;

class Process extends Model
{
    use HasFactory;

    public const S_ABERTO = 0;
    public const S_ADIADO = 1;
    public const S_ANULADO = 2;
    public const S_CANCELADO = 3;
    public const S_DESERTO = 4;
    public const S_FINALIZADO = 5;
    public const S_FRACASSADO = 6;
    public const S_REVOGADO = 7;

    public const T_PRICE = 0;
    public const T_DISCOUNT = 1;
    public const T_TECHNIQUE = 2;
    public const T_TECHNIQUE_AND_PRICE = 3;
    public const T_AUCTION = 4;
    public const T_ECONOMIC = 5;

    public const M_PROCLAMATION = 0;
    public const M_PRICE_TAKING = 1;
    public const M_INVITATION = 2;
    public const M_COMPETITION = 3;
    public const M_PRICE_REGISTRY = 4;
    public const M_AUCTION = 5;
    public const M_CONTEST = 6;
    public const M_WAIVER = 7;
    public const M_INEXIGIBILITY = 8;
    public const M_CARPOOL = 9;
    public const M_CALL = 10;

    protected $table = 'processes';

    protected $fillable = [
        'protocol',
        'ip',
        'date_hour_ini',
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
        'description',
        'status',
        'initial_value',
        'winner_value',
        'dfds'
    ];

    protected $casts = [
        'units' => Json::class,
        'dfds' => Json::class,
        'ordinators' => Json::class,
        'comission_members' => Json::class,
    ];

    public static function validateFields(?int $id = null): array
    {
        return [
            'protocol' => 'required',
            'ip' => 'required',
            'date_hour_ini' => 'required',
            'year_pca' => 'required',
            'type' => 'required',
            'modality' => 'required',
            'organ' => 'required',
            'units' => 'required',
            'ordinators' => 'required',
            'comission' => 'required',
            'comission_members' => 'required',
            'comission_address' => 'required',
            'author' => 'required',
            'description' => 'required',
            'status' => 'required',
            'dfds' => 'required'
        ];
    }

    public static function validateMsg(): array
    {
        return [
            'required' => 'Campo obrigatório não informado!'
        ];
    }

    public function dateHourIni(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => Dates::convert($value, Dates::UTC_TIME, Dates::PTBR_TIME),
            set: fn(?string $value) => Dates::convert($value, Dates::PTBR_TIME, Dates::UTC_TIME)
        );
    }

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

    public function pricerecord(): BelongsTo
    {
        return $this->belongsTo(PriceRecord::class);
    }

    public function proposal(): BelongsTo
    {
        return $this->belongsTo(Proposal::class);
    }

    public static function list_modalitys(): array
    {
        return [
            ['id' => self::M_PROCLAMATION, 'title' => 'Pregão'],
            ['id' => self::M_PRICE_TAKING, 'title' => 'Tomada de Preço'],
            ['id' => self::M_INVITATION, 'title' => 'Convite'],
            ['id' => self::M_COMPETITION, 'title' => 'Concorrência'],
            ['id' => self::M_PRICE_REGISTRY, 'title' => 'Registro de Preço'],
            ['id' => self::M_AUCTION, 'title' => 'Leilão'],
            ['id' => self::M_CONTEST, 'title' => 'Concurso'],
            ['id' => self::M_WAIVER, 'title' => 'Dispensa'],
            ['id' => self::M_INEXIGIBILITY, 'title' => 'Inexigibiliadade'],
            ['id' => self::M_CARPOOL, 'title' => 'Carona'],
            ['id' => self::M_CALL, 'title' => 'Chamada'],
        ];
    }

    public static function list_status(): array
    {
        return [
            ['id' => self::S_ABERTO, 'title' => 'Aberto'],
            ['id' => self::S_ADIADO, 'title' => 'Adiado'],
            ['id' => self::S_ANULADO, 'title' => 'Anulado'],
            ['id' => self::S_CANCELADO, 'title' => 'Cancelado'],
            ['id' => self::S_DESERTO, 'title' => 'Deserto'],
            ['id' => self::S_FINALIZADO, 'title' => 'Finalizado'],
            ['id' => self::S_FRACASSADO, 'title' => 'Fracassado'],
            ['id' => self::S_REVOGADO, 'title' => 'Revogado'],
        ];
    }

    public static function list_types(): array
    {
        return [
            ['id' => self::T_PRICE, 'title' => 'Menor preço'],
            ['id' => self::T_DISCOUNT, 'title' => 'Maior desconto'],
            ['id' => self::T_TECHNIQUE, 'title' => 'Melhor técnica ou conteúdo artístico'],
            ['id' => self::T_TECHNIQUE_AND_PRICE, 'title' => 'Técnica e preço'],
            ['id' => self::T_AUCTION, 'title' => 'Maior lance, no caso de leilão'],
            ['id' => self::T_ECONOMIC, 'title' => 'Maior retorno econômico'],
        ];
    }
}
