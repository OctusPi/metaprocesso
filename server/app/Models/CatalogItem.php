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

    public static function list_origem(): array
    {
        return [
            ['id' => 1, 'title' => 'Catálogo Federal'],
            ['id' => 2, 'title' => 'Catalogo Local']
        ];
    }

    public static function list_tipo(): array
    {
        return [
            ['id' => 1, 'title' => 'Material'],
            ['id' => 2, 'title' => 'Serviço']
        ];
    }

    public static function list_categoria(?int $key = null): string|array
    {
        $data = [
            ['id' => 1, 'title' => 'Material'],
            ['id' => 2, 'title' => 'Serviço'],
            ['id' => 3, 'title' => 'Obras'],
            ['id' => 4, 'title' => 'Serviços de Engenharia'],
            ['id' => 5, 'title' => 'Soluções de TIC'],
            ['id' => 6, 'title' => 'Locação de Imóveis'],
            ['id' => 7, 'title' => 'Alienação/Concessão/Permissão'],
            ['id' => 8, 'title' => 'Obras e Serviços de Engenharia'],
        ];

        if (!is_null($key)) {
            return $data[$key] ?? '';
        }

        return $data;
    }

    public static function list_status(): array
    {
        return [
            ['id' => 1, 'title' => 'Ativo'],
            ['id' => 2, 'title' => 'Suspenso'],
            ['id' => 3, 'title' => 'Em Análise']
        ];
    }
}
