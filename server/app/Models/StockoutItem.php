<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockoutItem extends Model
{
    use HasFactory;

    protected $table = 'stockoutitems';

    protected $fillable = [
        'stockout_id',
        'item_id',
        'quantity',
    ];

    public function stockout(): BelongsTo
    {
        return $this->belongsTo(StockOut::class, 'stockout_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(StockOut::class, 'stockout_id');
    }
}
