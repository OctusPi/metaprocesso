<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Utils\Dates;

class Dfd extends Model
{
    use HasFactory;

    protected $table = 'dfds';

    protected $fillable = [
        'cod',
        'date_ini',
        'category',
        'obj',
        'description',
        'organ_id',
        'unit_id',
    ];

    public function organ(): BelongsTo
    {
        return $this->belongsTo(Organ::class, 'organ_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function dateIni(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => Dates::utcToPtbr($value),
            set: fn (string $value) => Dates::ptbrToUtc($value)
        );
    }
}
