<?php

namespace App\Models;

use App\Utils\Dates;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Dfd extends Model
{
    use HasFactory;

    protected $table = 'dfds';

    protected $fillable = [
        'protocol',
        'ip',
        'organ',
        'unit',
        'demandant',
        'ordinator',
        'comission',
        'comission_members',
        'code',
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
        'price-taking',
        'status',
        'author'
    ];

    protected $casts = [
        'comission_members' => Json::class
    ];

    public function organ(): HasOne
    {
        return $this->hasOne(Organ::class, 'id', 'organ');
    }

    public function unit(): HasOne
    {
        return $this->hasOne(Unit::class, 'id', 'unit');
    }

    public function demandant(): HasOne
    {
        return $this->hasOne(Demandant::class, 'id', 'demandant');
    }

    public function ordinator(): HasOne
    {
        return $this->hasOne(Ordinator::class, 'id', 'ordinator');
    }

    public function comission(): HasOne
    {
        return $this->hasOne(Comission::class, 'id', 'comission');
    }

    public function author(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'author');
    }

    public function dfditem():BelongsTo
    {
        return $this->belongsTo(DfdItem::class);
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

    public static function list_priority():array
    {
        return [
            ['id' => 1, 'title' => 'Baixa'],
            ['id' => 2, 'title' => 'Média'],
            ['id' => 3, 'title' => 'Alta']
        ];
    }

    public static function list_hirings():array
    {
        return [
            [
                'id' => 1, 
                'title' => 'Concorrência',
                'description' => 'Modalidade utilizada para contratações de maior vulto. É aberta a qualquer interessado que, na fase inicial de habilitação preliminar, comprove possuir os requisitos mínimos de qualificação exigidos no edital para a execução do objeto.'
            ],
            [
                'id' => 2, 
                'title' => 'Concurso',
                'description' => 'Modalidade destinada à escolha de trabalho técnico, científico ou artístico, mediante a instituição de prêmios ou remuneração aos vencedores, conforme critérios constantes do edital publicado na imprensa oficial com antecedência mínima de 45 dias.'
            ],
            [
                'id' => 3, 
                'title' => 'Leilão',
                'description' => 'Modalidade destinada à venda de bens móveis inservíveis para a administração ou de produtos legalmente apreendidos ou penhorados, bem como para a alienação de bens imóveis oriundos de procedimentos judiciais ou de dação em pagamento.'
            ],
            [
                'id' => 4, 
                'title' => 'Pregão - Eletrônico/Presencial',
                'description' => 'Modalidade destinada à aquisição de bens e serviços comuns, qualquer que seja o valor estimado da contratação. O pregão pode ser realizado na forma eletrônica ou presencial.'
            ],
            [
                'id' => 5, 
                'title' => 'Diálogo Competitivo',
                'description' => 'Modalidade destinada à contratação de obras, serviços e compras, em que a administração pública realiza diálogos com licitantes previamente selecionados mediante critérios objetivos, com o objetivo de desenvolver uma ou mais alternativas capazes de atender às suas necessidades.'
            ],
            [
                'id' => 6, 
                'title' => 'Dispensa de Licitação',
                'description' => 'A dispensa de licitação para a contratação direta de serviços pode ocorrer em situações específicas determinadas pela Lei nº 14.133/2021'
            ]
            ,
            [
                'id' => 7, 
                'title' => 'Inexigibilidade',
                'description' => 'A inexigibilidade é regulamentada pela Lei 8.666/93 e se dá quando não é viável ou necessário um processo licitatório.'
            ]
        ];
    }

    public static function list_acquisitions():array
    {
        return [
            ['id' => 1, 'title' => 'Material de Consumo'],
            ['id' => 2, 'title' => 'Material Permanente / Equipamento'],
            ['id' => 3, 'title' => 'Serviço'],
            ['id' => 4, 'title' => 'Obras'],
            ['id' => 5, 'title' => 'Serviços de Engenharia'],
            ['id' => 6, 'title' => 'Soluções de TIC'],
            ['id' => 7, 'title' => 'Locação de Imóveis'],
            ['id' => 8, 'title' => 'Alienação/Concessão/Permissão'],
            ['id' => 9, 'title' => 'Obras e Serviços de Engenharia'],
        ];
    }

    public static function list_bonds():array
    {
        return [
            ['id' => 1, 'title' => 'Sim Possui Dependencia'],
            ['id' => 2, 'title' => 'Não Possui']
        ];
    }

    public function list_status():array
    {
        return [
            ['id' => 1, 'title' => 'Rascunho'],
            ['id' => 2, 'title' => 'Enviado'],
            ['id' => 3, 'title' => 'Pendente'],
            ['id' => 4, 'title' => 'Bloqueado'],
            ['id' => 5, 'title' => 'Processado']
        ];
    }
}
