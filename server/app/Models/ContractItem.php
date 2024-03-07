<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractItem extends Model
{
    use HasFactory;

    protected $table = 'contractitems';

    protected $fillable = [
        'contract',
        'item',
        'quantity',
        'unitary_value',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
