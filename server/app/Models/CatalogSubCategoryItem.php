<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CatalogSubCategoryItem extends Model
{
    use HasFactory;

    protected $table = 'catalog_items_subcategories';
    protected $fillable = [
        'organ_id',
        'name'
    ];

    public function organ(): BelongsTo
    {
        return $this->belongsTo(Organ::class, 'organ_id');
    }
}
