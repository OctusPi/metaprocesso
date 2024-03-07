<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockEntry extends Model
{
    use HasFactory;

    protected $table = 'stockentries';

    protected $fillable = [
        'date_ini',
        'invoice',
        'danfe',
        'purchaseorder',
        'contract',
        'quantity',
        'current_value',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }
}
