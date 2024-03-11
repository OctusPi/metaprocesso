<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceRegistrationDocItem extends Model
{
    use HasFactory;

    protected $table = 'priceregistrationdocitems';

    protected $fillable = [
        'priceregistrationdoc_id',
        'item_id',
        'quantity',
        'unitary_value',
    ];

    public function priceRegistrationDoc(): BelongsTo
    {
        return $this->belongsTo(PriceRegistrationDoc::class, 'priceregistrationdoc_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}
