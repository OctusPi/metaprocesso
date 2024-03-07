<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $table = 'purchaseorders';

    protected $fillable = [
        'cod',
        'date_ini',
        'organ',
        'unit',
        'contract',
        'status',
    ];

    public function organ()
    {
        return $this->belongsTo(Organ::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }
}
