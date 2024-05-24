<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CatalogItem extends Model
{
    use HasFactory;

    protected $table = 'catalog_items';
    protected $fillable = [
        'organ_id',
        'catalog_id',
        'code',
        'name',
        'description',
        'und',
        'volume',
        'origin',
        'category',
        'subcategory_id',
        'status',
    ];

    public function organ(): BelongsTo
    {
        return $this->belongsTo(Organ::class, 'organ_id');
    }

    public function catalog(): BelongsTo
    {
        return $this->belongsTo(Catalog::class, 'catalog_id');
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(CatalogSubCategoryItem::class, 'subcategory_id');
    }

    public static function validateFields(?int $id = null):array
    {
        return [
            'organ_id'       => 'required',
            'catalog_id'     => 'required',
            'code'           => 'required',
            'name'           => 'required',
            'description'    => 'required',
            'und'            => 'required',
            'origin'         => 'required',
            'type'           => 'required',
            'category'       => 'required',
            'subcategory_id' => 'required',
            'status'         => 'required',
        ];
    }

    public static function validateMsg():array
    {
        return [
            'required' => 'Campo obrigatório não informado!'
        ];
    }

}
