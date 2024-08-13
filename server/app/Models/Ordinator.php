<?php

namespace App\Models;

use App\Models\Unit;
use App\Utils\Dates;
use App\Models\Organ;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ordinator extends Model
{
    use HasFactory;

    protected $table = 'ordinators';

    protected $fillable = [
        'organ',
        'unit',
        'name',
        'cpf',
        'registration',
        'document',
        'start_term',
        'end_term',
        'status',
    ];

    public function organ(): HasOne
    {
        return $this->hasOne(Organ::class, 'id', 'organ');
    }

    public function unit(): HasOne
    {
        return $this->hasOne(Unit::class, 'id', 'unit');
    }

    public function dfd():BelongsTo
    {
        return $this->belongsTo(Dfd::class);
    }

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

    public static function validateFields(?int $id = null):array
    {
        return [
            'organ'      => 'required',
            'unit'       => 'required',
            'name'       => 'required',
            'cpf'        => ['required', Rule::unique('ordinators')->ignore($id)],
            'start_term' => 'required',
            'status'     => 'required',
        ];
    }

    public static function validateMsg():array
    {
        return [
            'required' => 'Campo obrigatório não informado!',
            'unique'   => 'Ordenador já registrado no sistema!'
        ];
    }

    public static function list_status(): array
    {
        return [
            ['id'=>0,'title'=> 'Desligado'],
            ['id'=>1,'title'=> 'Ativo']
        ];
    }
}
