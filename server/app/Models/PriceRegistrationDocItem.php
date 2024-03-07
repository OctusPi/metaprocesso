<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceRegistrationDocItem extends Model
{
    use HasFactory;

    protected $table = 'priceregistrationdocitems';

    protected $fillable = [
        'priceregistrationdoc',
        'item',
        'quantity',
        'unitary_value',
    ];

    public function priceregistrationdoc()
    {
        return $this->belongsTo(PriceRegistrationDoc::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
