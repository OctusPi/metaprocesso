<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractAdditive extends Model
{
    use HasFactory;

    protected $table = 'contractadditives';

    protected $fillable = [
        'contract',
        'date_ini',
        'obj',
        'description',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }
}
