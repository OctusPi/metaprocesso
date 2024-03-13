<?php

namespace App\Models;

use App\Utils\Dates;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contract extends Model
{
    use HasFactory;

    protected $table = 'contracts';

    protected $fillable = [
        'cod',
        'category',
        'obj',
        'description',
        'organ_id',
        'unit_id',
        'date_ini',
        'date_fin',
        'estimated_value',
        'approved_value',
        'supplier_id',
        'additive',
        'status',
    ];

    public function organ(): BelongsTo
    {
        return $this->belongsTo(Organ::class, 'organ_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function dateIni(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => Dates::convert($value, Dates::UTC, Dates::PTBR),
            set: fn (string $value) => Dates::convert($value, Dates::PTBR, Dates::UTC)
        );
    }

    public function dateFin(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => Dates::convert($value, Dates::UTC, Dates::PTBR),
            set: fn (string $value) => Dates::convert($value, Dates::PTBR, Dates::UTC)
        );
    }
}
