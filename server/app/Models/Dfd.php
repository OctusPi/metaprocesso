<?php

namespace App\Models;

use App\Casts\Json;
use App\Utils\Dates;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Dfd extends Model
{
    public const PRIORITY_BAIXA = 1;
    public const PRIORITY_MEDIA = 2;
    public const PRIORITY_ALTA = 3;

    public const STATUS_RASCUNHO = 1;
    public const STATUS_ENVIADO = 2;
    public const STATUS_PENDENTE = 3;
    public const STATUS_BLOQUEADO = 4;
    public const STATUS_PROCESSADO = 5;

    public const HIRING_CONCORRENCIA = 1;
    public const HIRING_CONCURSO = 2;
    public const HIRING_LEILAO = 3;
    public const HIRING_PREGAO = 4;
    public const HIRING_DIALOGO_COMPETITIVO = 5;
    public const HIRING_DISPENSA = 6;
    public const HIRING_INEXIGIBILIDADE = 7;

    public const ACQUISITION_MATERIAL_CONSUMO = 1;
    public const ACQUISITION_MATERIAL_PERMANENTE = 2;
    public const ACQUISITION_SERVICO = 3;
    public const ACQUISITION_OBRAS = 4;
    public const ACQUISITION_SERVICOS_ENGENHARIA = 5;
    public const ACQUISITION_SOLUCOES_TIC = 6;
    public const ACQUISITION_LOCACAO_IMOVEIS = 7;
    public const ACQUISITION_ALIENACAO_CONCESSAO = 8;
    public const ACQUISITION_OBRAS_SERVICOS_ENGENHARIA = 9;

    use HasFactory;

    protected $table = 'dfds';

    protected $fillable = [
        'id',
        'protocol',
        'ip',
        'organ_id',
        'unit_id',
        'demandant_id',
        'ordinator_id',
        'comission_id',
        'comission_members',
        'date_ini',
        'year_pca',
        'acquisition_type',
        'suggested_hiring',
        'description',
        'justification',
        'justification_quantity',
        'estimated_value',
        'estimated_date',
        'priority',
        'bonds',
        'price_taking',
        'status',
        'author_id'
    ];

    protected $casts = [
        'comission_members' => Json::class
    ];

    public function rules():array
    {
        return [
            'ip' => 'required',
            'protocol' => [
                'required',
                Rule::unique('dfds', 'protocol')->where(function ($query) {
                    return $query->where('unit_id', $this->unit_id);
            })->ignore($this->id)],
            'organ_id'     => 'required',
            'unit_id'      => 'required',
            'demandant_id' => 'required',
            'ordinator_id' => 'required',
            'comission_id' => 'required',
            'priority'  => 'required',
            'date_ini'  => 'required',
            'year_pca'  => 'required',
            'acquisition_type'  => 'required',
            'suggested_hiring'  => 'required',
            'description'       => 'required',
            'justification'     => 'required',
            'estimated_value'   => 'required',
            'estimated_date'    => 'required'
        ];
    }

    public function messages():array
    {
        return [
            'required' => 'Campo obrigatório não informado!',
            'email'    => 'Informe um email válido!',
            'unique'   => 'Protocolo já existente no sistema!'
        ];
    }

    public function dateIni(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => Dates::convert($value, Dates::UTC, Dates::PTBR),
            set: fn (string $value) => Dates::convert($value, Dates::PTBR, Dates::UTC)
        );
    }

    public function estimatedDate(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => Dates::convert($value, Dates::UTC, Dates::PTBR),
            set: fn (string $value) => Dates::convert($value, Dates::PTBR, Dates::UTC)
        );
    }

    public function bonds():Attribute
    {
        return Attribute::make(
            get: fn($value) => boolval($value),
            set: fn($value) => boolval($value)
        );
    }

    public function priceTaking():Attribute
    {
        return Attribute::make(
            get: fn($value) => boolval($value),
            set: fn($value) => boolval($value)
        );
    }

    public static function list_priority(): array
    {
        return [
            ['id' => self::PRIORITY_BAIXA, 'title' => 'Baixa'],
            ['id' => self::PRIORITY_MEDIA, 'title' => 'Média'],
            ['id' => self::PRIORITY_ALTA, 'title' => 'Alta']
        ];
    }

    public static function list_hirings(): array
    {
        return [
            [
                'id' => self::HIRING_CONCORRENCIA,
                'title' => 'Concorrência',
                'description' => 'Modalidade utilizada para contratações de maior vulto. É aberta a qualquer interessado que, na fase inicial de habilitação preliminar, comprove possuir os requisitos mínimos de qualificação exigidos no edital para a execução do objeto.'
            ],
            [
                'id' => self::HIRING_CONCURSO,
                'title' => 'Concurso',
                'description' => 'Modalidade destinada à escolha de trabalho técnico, científico ou artístico, mediante a instituição de prêmios ou remuneração aos vencedores, conforme critérios constantes do edital publicado na imprensa oficial com antecedência mínima de 45 dias.'
            ],
            [
                'id' => self::HIRING_LEILAO,
                'title' => 'Leilão',
                'description' => 'Modalidade destinada à venda de bens móveis inservíveis para a administração ou de produtos legalmente apreendidos ou penhorados, bem como para a alienação de bens imóveis oriundos de procedimentos judiciais ou de dação em pagamento.'
            ],
            [
                'id' => self::HIRING_PREGAO,
                'title' => 'Pregão - Eletrônico/Presencial',
                'description' => 'Modalidade destinada à aquisição de bens e serviços comuns, qualquer que seja o valor estimado da contratação. O pregão pode ser realizado na forma eletrônica ou presencial.'
            ],
            [
                'id' => self::HIRING_DIALOGO_COMPETITIVO,
                'title' => 'Diálogo Competitivo',
                'description' => 'Modalidade destinada à contratação de obras, serviços e compras, em que a administração pública realiza diálogos com licitantes previamente selecionados mediante critérios objetivos, com o objetivo de desenvolver uma ou mais alternativas capazes de atender às suas necessidades.'
            ],
            [
                'id' => self::HIRING_DISPENSA,
                'title' => 'Dispensa de Licitação',
                'description' => 'A dispensa de licitação para a contratação direta de serviços pode ocorrer em situações específicas determinadas pela Lei nº 14.133/2021'
            ],
            [
                'id' => self::HIRING_INEXIGIBILIDADE,
                'title' => 'Inexigibilidade',
                'description' => 'A inexigibilidade é regulamentada pela Lei 8.666/93 e se dá quando não é viável ou necessário um processo licitatório.'
            ]
        ];
    }

    public static function list_acquisitions(): array
    {
        return [
            ['id' => self::ACQUISITION_MATERIAL_CONSUMO, 'title' => 'Material de Consumo'],
            ['id' => self::ACQUISITION_MATERIAL_PERMANENTE, 'title' => 'Material Permanente / Equipamento'],
            ['id' => self::ACQUISITION_SERVICO, 'title' => 'Serviço'],
            ['id' => self::ACQUISITION_OBRAS, 'title' => 'Obras'],
            ['id' => self::ACQUISITION_SERVICOS_ENGENHARIA, 'title' => 'Serviços de Engenharia'],
            ['id' => self::ACQUISITION_SOLUCOES_TIC, 'title' => 'Soluções de TIC'],
            ['id' => self::ACQUISITION_LOCACAO_IMOVEIS, 'title' => 'Locação de Imóveis'],
            ['id' => self::ACQUISITION_ALIENACAO_CONCESSAO, 'title' => 'Alienação/Concessão/Permissão'],
            ['id' => self::ACQUISITION_OBRAS_SERVICOS_ENGENHARIA, 'title' => 'Obras e Serviços de Engenharia'],
        ];
    }

    public static function list_status(): array
    {
        return [
            ['id' => self::STATUS_RASCUNHO, 'title' => 'Rascunho'],
            ['id' => self::STATUS_ENVIADO, 'title' => 'Enviado'],
            ['id' => self::STATUS_PENDENTE, 'title' => 'Pendente'],
            ['id' => self::STATUS_BLOQUEADO, 'title' => 'Aprovisionado'],
            ['id' => self::STATUS_PROCESSADO, 'title' => 'Processado']
        ];
    }

    public static function make_details(): array
    {
        return [
            'dfds_status' => Dfd::list_status(),
            'prioritys_dfd' => Dfd::list_priority(),
            'hirings_dfd' => Dfd::list_hirings(),
            'acquisitions_dfd' => Dfd::list_acquisitions(),
            'modalities' => Process::list_modalitys(),
            'responsibilitys' => ComissionMember::list_responsabilities(),
            'items_types' => CatalogItem::list_types()
        ];
    }

    public function organ(): BelongsTo
    {
        return $this->belongsTo(Organ::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function demandant(): BelongsTo
    {
        return $this->belongsTo(Demandant::class);
    }

    public function ordinator(): BelongsTo
    {
        return $this->belongsTo(Ordinator::class);
    }

    public function comission(): BelongsTo
    {
        return $this->belongsTo(Comission::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function dfdItems():HasMany
    {
        return $this->hasMany(DfdItem::class);
    }
}
