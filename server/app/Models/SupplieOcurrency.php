<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplieOcurrency extends Model
{
    use HasFactory;

    protected $table = 'supplieoccurrences';

    protected $fillable = [
        'purchaseorder',
        'contract',
        'supplier',
        'status',
        'description',
        'user',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
