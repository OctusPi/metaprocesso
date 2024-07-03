<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CatalogItem extends BaseModel
{
    use HasFactory;

    // catalog origins
    public const CAT_ORIGIN_FEDERAL = 1;
    public const CAT_ORIGIN_LOCAL = 2;

    // catalog types
    public const CAT_TYPE_MATERIAL = 1;
    public const CAT_TYPE_SERVICO = 2;

    //catalog categories
    public const CAT_CAT_MATERIAL = 1;
    public const CAT_CAT_SERVICO = 2;
    public const CAT_CAT_OBRAS = 3;
    public const CAT_CAT_ENG = 4;
    public const CAT_CAT_TIC = 5;
    public const CAT_CAT_LOC = 6;
    public const CAT_CAT_ALI = 7;
    public const CAT_CAT_OBSE = 8;

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
        return $this->hasOne(Organ::class, 'id', 'organ');
    }

    public function catalog(): HasOne
    {
        return $this->hasOne(Catalog::class, 'id', 'catalog');
    }

    public function subcategory(): HasOne
    {
        return $this->hasOne(CatalogSubCategoryItem::class, 'id', 'subcategory');
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
            'status'         => 'required',
        ];
    }

    public static function validateMsg():array
    {
        return [
            'required' => 'Campo obrigatório não informado!'
        ];
    }

    public static function list_origins(): array
    {
        return [
            ['id' => self::CAT_ORIGIN_FEDERAL, 'title' => 'Catálogo Federal'],
            ['id' => self::CAT_ORIGIN_LOCAL, 'title' => 'Catalogo Local']
        ];
    }

    public static function list_types(): array
    {
        return [
            ['id' => self::CAT_TYPE_MATERIAL, 'title' => 'Material'],
            ['id' => self::CAT_TYPE_SERVICO, 'title' => 'Serviço']
        ];
    }

    public static function list_categories(?int $key = null): string|array
    {
        $data = [
            ['id' => self::CAT_CAT_MATERIAL, 'title' => 'Material'],
            ['id' => self::CAT_CAT_SERVICO, 'title' => 'Serviço'],
            ['id' => self::CAT_CAT_OBRAS, 'title' => 'Obras'],
            ['id' => self::CAT_CAT_ENG, 'title' => 'Serviços de Engenharia'],
            ['id' => self::CAT_CAT_TIC, 'title' => 'Soluções de TIC'],
            ['id' => self::CAT_CAT_LOC, 'title' => 'Locação de Imóveis'],
            ['id' => self::CAT_CAT_ALI, 'title' => 'Alienação/Concessão/Permissão'],
            ['id' => self::CAT_CAT_OBSE, 'title' => 'Obras e Serviços de Engenharia'],
        ];

        if (!is_null($key)) {
            return $data[$key] ?? '';
        }

        return $data;
    }

    public static function list_status(): array
    {
        return [
            ['id' => parent::S_INACTIVE, 'title' => 'Inativo'],
            ['id' => parent::S_ACTIVE, 'title' => 'Ativo'],
            ['id' => parent::S_SUSPENDED, 'title' => 'Suspenso'],
            ['id' => parent::S_ANALYSIS, 'title' => 'Em Análise']
        ];
    }
}
