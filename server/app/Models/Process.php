<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Utils\Dates;
use App\Casts\Json;
use Illuminate\Validation\Rule;

class Process extends Model
{
    use HasFactory;

    public const S_BUILD = 0;
    public const S_ABERTO = 1;
    public const S_ADIADO = 2;
    public const S_ANULADO = 3;
    public const S_CANCELADO = 4;
    public const S_DESERTO = 5;
    public const S_FINALIZADO = 6;
    public const S_FRACASSADO = 7;
    public const S_REVOGADO = 8;
    public const S_SUSPENDED = 9;
    public const S_PUBLISHED = 10;

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

    public const ACQUISITION_SERVICE = 1;
    public const ACQUISITION_BENS = 2;
    public const ACQUISITION_OBRAS = 3;

    public const INSTALLMENT_LOT = 1;
    public const INSTALLMENT_ITEM = 2;

    public const SUMMONS_EDITAL = 1; //Edital
    public const SUMMONS_NOTICE = 2; // Aviso de Contratação Direta
    public const SUMMONS_MINUTE = 3; // Ato que autoriza a Contratação Direta

    protected $table = 'processes';

    protected $fillable = [
        'id',
        'protocol',
        'ip',
        'date_hour_ini',
        'year_pca',
        'type',
        'modality',
        'organ_id',
        'units',
        'ordinators',
        'comission_id',
        'comission_members',
        'comission_address',
        'author_id',
        'description',
        'status',
        'initial_value',
        'winner_value',
        'dfds',
        'acquisition',
        'acquisition_type',
        'installment_justification',
        'installment_type',
        'category',
        'dispute',
        'benefit'
    ];

    protected $casts = [
        'units' => Json::class,
        'dfds' => Json::class,
        'ordinators' => Json::class,
        'comission_members' => Json::class,
    ];

    public function rules(): array
    {
        return [
            'protocol' => [
                'required',
                Rule::unique('processes', 'protocol')
                    ->where('organ_id', $this->organ_id)
                    ->ignore($this->id)
            ],
            'ip' => 'required',
            'date_hour_ini' => 'required',
            'year_pca' => 'required',
            'type' => 'required',
            'modality' => 'required',
            'organ_id' => 'required',
            'units' => 'required',
            'ordinators' => 'required',
            'comission_id' => 'required',
            'comission_members' => 'required',
            'comission_address' => 'required',
            'author_id' => 'required',
            'description' => 'required',
            'status' => 'required',
            'dfds' => 'required',
            'acquisition' => 'required',
            'acquisition_type' => 'required',
            'installment_type' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'Campo obrigatório não informado!',
            'unique' => 'Proccess já existente com o protocolo informado...'
        ];
    }

    public function dateHourIni(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => Dates::convert($value, Dates::UTC_TIME, Dates::PTBR_DATEH),
            set: fn(?string $value) => Dates::convert($value, Dates::PTBR_DATEH, Dates::UTC_TIME)
        );
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

    public static function list_categories(): array
    {
        return [
            ['id' => 1, 'title' => 'Cessão'],
            ['id' => 2, 'title' => 'Compras'],
            ['id' => 3, 'title' => 'Informática (TIC)'],
            ['id' => 4, 'title' => 'Internacional'],
            ['id' => 5, 'title' => 'Locação Imóveis'],
            ['id' => 6, 'title' => 'Mão de Obra'],
            ['id' => 7, 'title' => 'Obras'],
            ['id' => 8, 'title' => 'Serviços'],
            ['id' => 9, 'title' => 'Serviços de Engenharia'],
            ['id' => 10, 'title' => 'Serviços de Saúde'],
            ['id' => 11, 'title' => 'Alienação de bens móveis/imóveis'],
        ];
    }

    public static function list_acquisitions(): array
    {
        return [
            ['id' => self::ACQUISITION_SERVICE, 'title' => 'Serviço'],
            ['id' => self::ACQUISITION_BENS, 'title' => 'Bens'],
            ['id' => self::ACQUISITION_OBRAS, 'title' => 'Obras'],
        ];
    }

    public static function list_acquisition_types(): array
    {
        return Dfd::list_acquisitions();
    }

    public static function list_installments_types(): array
    {
        return [
            ['id' => self::INSTALLMENT_ITEM, 'title' => 'Parcelamento por item'],
            ['id' => self::INSTALLMENT_LOT, 'title' => 'Parcelamento por lote']
        ];
    }

    public static function list_status(): array
    {
        return [
            ['id' => self::S_BUILD, 'title' => 'Em Construção'],
            ['id' => self::S_ABERTO, 'title' => 'Aberto'],
            ['id' => self::S_ADIADO, 'title' => 'Adiado'],
            ['id' => self::S_ANULADO, 'title' => 'Anulado'],
            ['id' => self::S_CANCELADO, 'title' => 'Cancelado'],
            ['id' => self::S_DESERTO, 'title' => 'Deserto'],
            ['id' => self::S_FINALIZADO, 'title' => 'Finalizado'],
            ['id' => self::S_FRACASSADO, 'title' => 'Fracassado'],
            ['id' => self::S_REVOGADO, 'title' => 'Revogado'],
            ['id' => self::S_SUSPENDED, 'title' => 'Supenso'],
            ['id' => self::S_PUBLISHED, 'title' => 'Publicado PNCP']
        ];
    }

    public static function list_status_pncp(): array
    {
        return [
            ['id' => 3, 'title' => 'Anulado'],
            ['id' => 2, 'title' => 'Revogado'],
            ['id' => 4, 'title' => 'Supenso'],
            ['id' => 1, 'title' => 'Publicado PNCP']
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

    public static function list_summons(): array
    {
        return [
            ['id' => self::SUMMONS_EDITAL, 'title' => 'Edital', 'description' => 'Instrumento convocatório utilizado no diálogo competitivo, concurso, concorrência, pregão, manifestação de interesse, pré-qualificação e credenciamento'],
            ['id' => self::SUMMONS_NOTICE, 'title' => 'Aviso de Contratação Direta', 'description' => 'Instrumento convocatório utilizado na Dispensa com Disputa.'],
            ['id' => self::SUMMONS_MINUTE, 'title' => 'Ato que autoriza a Contratação Direta', 'description' => 'Instrumento convocatório utilizado na Dispensa sem Disputa ou na Inexigibilidade.']
        ];
    }

    public static function list_disputes(): array
    {
        return [
            ['id' => 1, 'title' => 'Aberto'],
            ['id' => 2, 'title' => 'Fechado'],
            ['id' => 3, 'title' => 'Aberto-Fechado'],
            ['id' => 4, 'title' => 'Dispensa com Disputa'],
            ['id' => 5, 'title' => 'Não se Aplica'],
            ['id' => 6, 'title' => 'Fechado-Aberto']
        ];
    }

    public static function list_benefits(): array
    {
        return [
            ['id' => 1, 'title' => 'Participação exclusiva para ME/EPP'],
            ['id' => 2, 'title' => 'Subcontratação para ME/EPP'],
            ['id' => 3, 'title' => 'Cota reservada para ME/EPP'],
            ['id' => 4, 'title' => 'Sem benefício'],
            ['id' => 5, 'title' => 'Não se Aplica']
        ];
    }

    public function organ(): BelongsTo
    {
        return $this->belongsTo(Organ::class);
    }

    public function comission(): BelongsTo
    {
        return $this->belongsTo(Comission::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function etp(): HasOne
    {
        return $this->hasOne(Etp::class);
    }

    public function priceRecord(): HasOne
    {
        return $this->hasOne(PriceRecord::class);
    }

    public function proposals(): HasMany
    {
        return $this->hasMany(Proposal::class);
    }

    public function riskMaps(): HasMany
    {
        return $this->hasMany(RiskMap::class);
    }

    public function refTerm(): HasOne
    {
        return $this->hasOne(RefTerm::class);
    }
}
