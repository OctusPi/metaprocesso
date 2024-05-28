<?php

namespace App\Models;

use App\Utils\Dates;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ComissionEnd extends Model
{
    use HasFactory;

    protected $table = 'comissions_ends';

    protected $fillable = [
        'organ',
        'unit',
        'comission',
        'document',
        'description',
        'end_term',
    ];

    public function endTerm(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => Dates::convert($value, Dates::UTC, Dates::PTBR),
            set: fn(?string $value) => Dates::convert($value, Dates::PTBR, Dates::UTC)
        );
    }

    public static function validateFields(?int $id = null): array
    {
        return [
            'organ' => 'required',
            'unit' => 'required',
            'comission' => 'required',
            'end_term' => 'required',
        ];
    }

    public static function validateMsg(): array
    {
        return [
            'required' => 'Campo obrigatório não informado!',
        ];
    }

    public function organ(): HasOne
    {
        return $this->hasOne(Organ::class, 'id');
    }

    public function unit(): HasOne
    {
        return $this->hasOne(Unit::class, 'id');
    }

    public function comission(): HasOne
    {
        return $this->hasOne(Comission::class, 'id');
    }
}
