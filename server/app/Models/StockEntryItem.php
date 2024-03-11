<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockEntryItem extends Model
{
    use HasFactory;

    protected $table = 'stockentryitems';

    protected $fillable = [
        'stockentry_id',
        'item_id',
        'quantity',
        'current_value',
    ];

    public function stockentry(): BelongsTo
    {
        return $this->belongsTo(StockEntry::class, 'stockentry_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}
