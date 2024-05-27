<?php

namespace App\Models;

use App\Utils\Dates;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Dfd extends Model
{
    use HasFactory;

    protected $table = 'dfds';

    protected $fillable = [
        'organ',
        'unit',
        'demandant',
        'ordinator',
        'comission',
        'cod',
        'date_ini',
        'category',
        'obj',
        'description',
    ];

    public function dfditem():BelongsTo
    {
        return $this->belongsTo(DfdItem::class);
    }

    public function organ(): HasOne
    {
        return $this->hasOne(Organ::class, 'id');
    }

    public function unit(): HasOne
    {
        return $this->hasOne(Unit::class, 'id');
    }

    public function demandant(): HasOne
    {
        return $this->hasOne(Demandant::class, 'id');
    }

    public function ordinator(): HasOne
    {
        return $this->hasOne(Ordinator::class, 'id');
    }

    public function comission(): HasOne
    {
        return $this->hasOne(Comission::class, 'id');
    }

    public function dateIni(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => Dates::convert($value, Dates::UTC, Dates::PTBR),
            set: fn (string $value) => Dates::convert($value, Dates::PTBR, Dates::UTC)
        );
    }
}
