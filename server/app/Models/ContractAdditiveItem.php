<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractAdditiveItem extends Model
{
    use HasFactory;

    protected $table = 'contractadditiveitems';

    protected $fillable = [
        'contract_id',
        'contractadditive_id',
        'item_id',
        'quantity',
        'unitary_value',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class, 'contract_id');
    }

    public function contractAdditive(): BelongsTo
    {
        return $this->belongsTo(ContractAdditive::class, 'contractadditive_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}
