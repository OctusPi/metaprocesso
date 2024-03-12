<?php

namespace App\Models;

use App\Utils\Dates;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractAdditive extends Model
{
    use HasFactory;

    protected $table = 'contractadditives';

    protected $fillable = [
        'contract_id',
        'date_ini',
        'obj',
        'description',
    ];

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class, 'contract_id');
    }

    public function dateIni(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => Dates::utcToPtbr($value),
            set: fn (string $value) => Dates::ptbrToUtc($value)
        );
    }
}
