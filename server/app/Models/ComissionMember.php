<?php

namespace App\Models;

use App\Utils\Dates;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ComissionMember extends Model
{
    use HasFactory;

    protected $table = 'comissions_members';

    protected $fillable = [
        'organ_id',
        'unit_id',
        'comission_id',
        'name',
        'responsibility',
        'document',
        'description',
        'start_term',
        'end_term',
        'status',
    ];

    public function startTerm(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => Dates::convert($value, Dates::UTC, Dates::PTBR),
            set: fn(?string $value) => Dates::convert($value, Dates::PTBR, Dates::UTC)
        );
    }

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
            'name' => 'required',
            'responsibility' => 'required',
            'start_term' => 'required',
            'status' => 'required',
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
