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

}
