<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractAdditiveItem extends Model
{
    use HasFactory;

    protected $table = 'contractadditiveitems';

    protected $fillable = [
        'contract',
        'contractadditive',
        'item',
        'quantity',
        'unitary_value',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function contractAdditive()
    {
        return $this->belongsTo(ContractAdditive::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
