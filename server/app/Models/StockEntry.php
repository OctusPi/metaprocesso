<?php

namespace App\Models;

use App\Utils\Dates;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockEntry extends Model
{
    use HasFactory;

    protected $table = 'stockentries';

    protected $fillable = [
        'date_ini',
        'invoice',
        'danfe',
        'purchaseorder_id',
        'contract_id',
        'quantity',
        'current_value',
    ];

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchaseorder_id');
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class, 'contract_id');
    }

    public function dateIni(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => Dates::convert($value, Dates::UTC, Dates::PTBR),
            set: fn (string $value) => Dates::convert($value, Dates::PTBR, Dates::UTC)
        );
    }
}
