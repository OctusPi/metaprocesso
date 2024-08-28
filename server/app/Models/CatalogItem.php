<?php

namespace App\Models;

use App\Models\DfdItem;
use App\Models\CatalogSubCategoryItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Validation\Rule;

class CatalogItem extends Model
{
    use HasFactory;

    // catalog status
    const S_INACTIVE = 0;
    const S_ACTIVE = 1;
    const S_SUSPENDED = 2;
    const S_ANALYSIS = 3;

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
        'id',
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

    public function rules(): array
    {
        return [
            'organ' => 'required',
            'catalog' => 'required',
            'code' => [
                'required',
                Rule::unique('catalog_items', 'code')->where(function ($query) {
                    return $query->where('organ', $this->organ)->where('catalog', $this->catalog);
            })->ignore($this->id)],
            'name' => 'required',
            'description' => 'required',
            'und' => 'required',
            'origin' => 'required',
            'type' => 'required',
            'category' => 'required',
            'status' => 'required',
        ];
    }

    public static function messages(): array
    {
        return [
            'required' => 'Campo obrigatório não informado!',
            'unique'   => 'Item já existente no catálogo'
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
            ['id' => self::S_INACTIVE, 'title' => 'Inativo'],
            ['id' => self::S_ACTIVE, 'title' => 'Ativo'],
            ['id' => self::S_SUSPENDED, 'title' => 'Suspenso'],
            ['id' => self::S_ANALYSIS, 'title' => 'Em Análise']
        ];
    }

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

    public function dfditem(): BelongsTo
    {
        return $this->belongsTo(DfdItem::class);
    }
}
