<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CatalogItem extends Model
{
    use HasFactory;

    protected $table = 'catalog_items';
    protected $fillable = [
        'organ',
        'catalog',
        'code',
        'name',
        'description',
        'und',
        'volume',
        'origin',
        'type',
        'category',
        'subcategory',
        'status',
    ];

    public function organ(): HasOne
    {
        return $this->hasOne(Organ::class, 'id');
    }

    public function catalog(): HasOne
    {
        return $this->hasOne(Catalog::class, 'id');
    }

    public function subcategory(): HasOne
    {
        return $this->hasOne(CatalogSubCategoryItem::class, 'id');
    }

    public function dfditem():BelongsTo
    {
        return $this->belongsTo(DfdItem::class);
    }

    public static function validateFields(?int $id = null):array
    {
        return [
            'organ'       => 'required',
            'catalog'     => 'required',
            'code'           => 'required',
            'name'           => 'required',
            'description'    => 'required',
            'und'            => 'required',
            'origin'         => 'required',
            'type'           => 'required',
            'category'       => 'required',
            'subcategory' => 'required',
            'status'         => 'required',
        ];
    }

    public static function validateMsg():array
    {
        return [
            'required' => 'Campo obrigatório não informado!'
        ];
    }

    public static function list_origem(): array
    {
        return [
            1 => 'Catálogo Federal',
            2 => 'Catalogo Local'
        ];
    }

    public static function list_tipo(): array
    {
        return [
            1 => 'Material',
            2 => 'Serviço'
        ];
    }

    public static function list_categoria(?int $key = null): string|array
    {
        $data = [
            1 => 'Material',
            2 => 'Serviço',
            3 => 'Obras',
            4 => 'Serviços de Engenharia',
            5 => 'Soluções de TIC',
            6 => 'Locação de Imóveis',
            7 => 'Alienação/Concessão/Permissão',
            8 => 'Obras e Serviços de Engenharia',
        ];

        if (!is_null($key)) {
            return $data[$key] ?? '';
        }

        return $data;
    }

    public static function list_status(): array
    {
        return [
            1 => 'Em avaliação',
            2 => 'Deferido',
            3 => 'Indeferido'
        ];
    }

    public static function list_unds(): array{
        return [
            [ "siglaUnidadeFornecimento"=> "AMPOLA", "nomeUnidadeFornecimento"=> "AMPOLA" ],
            [ "siglaUnidadeFornecimento"=> "BALDE", "nomeUnidadeFornecimento"=> "BALDE" ],
            [ "siglaUnidadeFornecimento"=> "BANDEJ", "nomeUnidadeFornecimento"=> "BANDEJA" ],
            [ "siglaUnidadeFornecimento"=> "BARRA", "nomeUnidadeFornecimento"=> "BARRA" ],
            [ "siglaUnidadeFornecimento"=> "BISNAG", "nomeUnidadeFornecimento"=> "BISNAGA" ],
            [ "siglaUnidadeFornecimento"=> "BLOCO", "nomeUnidadeFornecimento"=> "BLOCO" ],
            [ "siglaUnidadeFornecimento"=> "BOBINA", "nomeUnidadeFornecimento"=> "BOBINA" ],
            [ "siglaUnidadeFornecimento"=> "BOMBEAR", "nomeUnidadeFornecimento"=> "BOMBONA" ],
            [ "siglaUnidadeFornecimento"=> "CÁPSULAS", "nomeUnidadeFornecimento"=> "CÁPSULA" ],
            [ "siglaUnidadeFornecimento"=> "CARRINHO", "nomeUnidadeFornecimento"=> "CARTELA" ],
            [ "siglaUnidadeFornecimento"=> "CENTO", "nomeUnidadeFornecimento"=> "CENTO" ],
            [ "siglaUnidadeFornecimento"=> "CJ", "nomeUnidadeFornecimento"=> "CONJUNTO" ],
            [ "siglaUnidadeFornecimento"=> "CM", "nomeUnidadeFornecimento"=> "CENTÍMETRO" ],
            [ "siglaUnidadeFornecimento"=> "CM2", "nomeUnidadeFornecimento"=> "CENTIMETRO QUADRADO" ],
            [ "siglaUnidadeFornecimento"=> "CX", "nomeUnidadeFornecimento"=> "CAIXA" ],
            [ "siglaUnidadeFornecimento"=> "CX2", "nomeUnidadeFornecimento"=> "CAIXA COM 2 UNIDADES" ],
            [ "siglaUnidadeFornecimento"=> "CX3", "nomeUnidadeFornecimento"=> "CAIXA COM 3 UNIDADES" ],
            [ "siglaUnidadeFornecimento"=> "CX5", "nomeUnidadeFornecimento"=> "CAIXA COM 5 UNIDADES" ],
            [ "siglaUnidadeFornecimento"=> "CX10", "nomeUnidadeFornecimento"=> "CAIXA COM 10 UNIDADES" ],
            [ "siglaUnidadeFornecimento"=> "CX15", "nomeUnidadeFornecimento"=> "CAIXA COM 15 UNIDADES" ],
            [ "siglaUnidadeFornecimento"=> "CX20", "nomeUnidadeFornecimento"=> "CAIXA COM 20 UNIDADES" ],
            [ "siglaUnidadeFornecimento"=> "CX25", "nomeUnidadeFornecimento"=> "CAIXA COM 25 UNIDADES" ],
            [ "siglaUnidadeFornecimento"=> "CX50", "nomeUnidadeFornecimento"=> "CAIXA COM 50 UNIDADES" ],
            [ "siglaUnidadeFornecimento"=> "CX100", "nomeUnidadeFornecimento"=> "CAIXA COM 100 UNIDADES" ],
            [ "siglaUnidadeFornecimento"=> "DISP", "nomeUnidadeFornecimento"=> "EXIBIÇÃO" ],
            [ "siglaUnidadeFornecimento"=> "DUZIA", "nomeUnidadeFornecimento"=> "DUZIA" ],
            [ "siglaUnidadeFornecimento"=> "EMBAL", "nomeUnidadeFornecimento"=> "EMBALAGEM" ],
            [ "siglaUnidadeFornecimento"=> "FARDO", "nomeUnidadeFornecimento"=> "FARDO" ],
            [ "siglaUnidadeFornecimento"=> "FOLHA", "nomeUnidadeFornecimento"=> "FOLHA" ],
            [ "siglaUnidadeFornecimento"=> "FRASCO", "nomeUnidadeFornecimento"=> "FRASCO" ],
            [ "siglaUnidadeFornecimento"=> "GALAO", "nomeUnidadeFornecimento"=> "GALÃO" ],
            [ "siglaUnidadeFornecimento"=> "GF", "nomeUnidadeFornecimento"=> "GARRAFA" ],
            [ "siglaUnidadeFornecimento"=> "GRAMAS", "nomeUnidadeFornecimento"=> "GRAMAS" ],
            [ "siglaUnidadeFornecimento"=> "JOGO", "nomeUnidadeFornecimento"=> "JOGO" ],
            [ "siglaUnidadeFornecimento"=> "KG", "nomeUnidadeFornecimento"=> "QUILOGRAMA" ],
            [ "siglaUnidadeFornecimento"=> "KIT", "nomeUnidadeFornecimento"=> "KIT" ],
            [ "siglaUnidadeFornecimento"=> "LATA", "nomeUnidadeFornecimento"=> "LATA" ],
            [ "siglaUnidadeFornecimento"=> "LITRO", "nomeUnidadeFornecimento"=> "LITRO" ],
            [ "siglaUnidadeFornecimento"=> "M", "nomeUnidadeFornecimento"=> "METRO" ],
            [ "siglaUnidadeFornecimento"=> "M2", "nomeUnidadeFornecimento"=> "METRO QUADRADO" ],
            [ "siglaUnidadeFornecimento"=> "M3", "nomeUnidadeFornecimento"=> "METRO CÚBICO" ],
            [ "siglaUnidadeFornecimento"=> "MILHEI", "nomeUnidadeFornecimento"=> "MILHEIRO" ],
            [ "siglaUnidadeFornecimento"=> "ML", "nomeUnidadeFornecimento"=> "MILILITRO" ],
            [ "siglaUnidadeFornecimento"=> "MWH", "nomeUnidadeFornecimento"=> "MEGAWATT HORA" ],
            [ "siglaUnidadeFornecimento"=> "PCT", "nomeUnidadeFornecimento"=> "PACOTE" ],
            [ "siglaUnidadeFornecimento"=> "PALETE", "nomeUnidadeFornecimento"=> "PALETE" ],
            [ "siglaUnidadeFornecimento"=> "PARES", "nomeUnidadeFornecimento"=> "PARES" ],
            [ "siglaUnidadeFornecimento"=> "PC", "nomeUnidadeFornecimento"=> "PEÇA" ],
            [ "siglaUnidadeFornecimento"=> "AMIGO", "nomeUnidadeFornecimento"=> "AMIGO" ],
            [ "siglaUnidadeFornecimento"=> "K", "nomeUnidadeFornecimento"=> "QUILATE" ],
            [ "siglaUnidadeFornecimento"=> "RESMA", "nomeUnidadeFornecimento"=> "ROLO" ],
            [ "siglaUnidadeFornecimento"=> "ROLO", "nomeUnidadeFornecimento"=> "CAIXA" ],
            [ "siglaUnidadeFornecimento"=> "SACO", "nomeUnidadeFornecimento"=> "SACO" ],
            [ "siglaUnidadeFornecimento"=> "SACOLA", "nomeUnidadeFornecimento"=> "SACOLA" ],
            [ "siglaUnidadeFornecimento"=> "TAMBOR", "nomeUnidadeFornecimento"=> "TAMBOR" ],
            [ "siglaUnidadeFornecimento"=> "TANQUE", "nomeUnidadeFornecimento"=> "TANQUE" ],
            [ "siglaUnidadeFornecimento"=> "TON", "nomeUnidadeFornecimento"=> "TONELADA" ],
            [ "siglaUnidadeFornecimento"=> "TUBO", "nomeUnidadeFornecimento"=> "TUBO" ],
            [ "siglaUnidadeFornecimento"=> "UN", "nomeUnidadeFornecimento"=> "UNIDADE" ],
            [ "siglaUnidadeFornecimento"=> "VASIL", "nomeUnidadeFornecimento"=> "VASILHAME" ],
            [ "siglaUnidadeFornecimento"=> "VIDRO", "nomeUnidadeFornecimento"=> "VIDRO" ]
        ];
    }

}
