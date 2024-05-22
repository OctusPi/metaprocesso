<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CatalogItem extends Model
{
    use HasFactory;

    public const ENDPOINT_CATMAT = 'https://compras.dados.gov.br/materiais/v1/materiais.json?descricao_item=$';
    public const ENDPOINT_CATSER = 'https://compras.dados.gov.br/servicos/v1/servicos.json?descricao=$';

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


}
