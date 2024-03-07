<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockEntryItem extends Model
{
    use HasFactory;

    protected $table = 'stockentryitems';

    protected $fillable = [
        'stockentry',
        'item',
        'quantity',
        'current_value',
    ];

    public function stockentry()
    {
        return $this->belongsTo(StockEntry::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
