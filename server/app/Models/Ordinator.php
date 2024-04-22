<?php

namespace App\Models;

use App\Models\Unit;
use App\Utils\Dates;
use App\Models\Organ;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ordinator extends Model
{
    use HasFactory;

    protected $table = 'ordinators';

    protected $fillable = [
        'organ_id',
        'unit_id',
        'name',
        'cpf',
        'registration',
        'document',
        'start_term',
        'end_term',
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

    public function startTerm(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => Dates::convert($value, Dates::UTC_TIME, Dates::PTBR_TIME),
            set: fn(?string $value) => Dates::convert($value, Dates::PTBR_TIME, Dates::UTC_TIME)
        );
    }

    public function endTerm(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => Dates::convert($value, Dates::UTC_TIME, Dates::PTBR_TIME),
            set: fn(?string $value) => Dates::convert($value, Dates::PTBR_TIME, Dates::UTC_TIME)
        );
    }

    public static function list_status(): array
    {
        return [
            ['id'=>0,'title'=> 'Desligado'],
            ['id'=>1,'title'=> 'Ativo']
        ];
    }
}
