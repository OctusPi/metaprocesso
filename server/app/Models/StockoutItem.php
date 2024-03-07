<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockoutItem extends Model
{
    use HasFactory;

    protected $table = 'stockoutitems';

    protected $fillable = [
        'stockout',
        'item',
        'quantity',
    ];

    public function stockOut()
    {
        return $this->belongsTo(StockOut::class);
    }
}
