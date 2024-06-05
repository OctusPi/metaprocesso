<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CatalogSubCategoryItem extends Model
{
    use HasFactory;

    protected $table = 'catalog_items_subcategories';
    protected $fillable = [
        'organ',
        'name'
    ];

    public function organ(): HasOne
    {
        return $this->hasOne(Organ::class, 'id', 'organ');
    }

    public function catalogitem():BelongsTo
    {
        return $this->belongsTo(CatalogItem::class);
    }
}
