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
        'id',
        'organ',
        'unit',
        'comission',
        'name',
        'responsibility',
        'document',
        'description',
        'start_term',
        'end_term',
        'status',
        'number_doc'
    ];

    public function rules(): array
    {
        return [
            'name' => 'required',
            'responsibility' => 'required',
            'start_term' => 'required',
            'status' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'Campo obrigatório não informado!',
        ];
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

    public function organ(): HasOne
    {
        return $this->hasOne(Organ::class, 'id', 'organ');
    }

    public function unit(): HasOne
    {
        return $this->hasOne(Unit::class, 'id', 'unit');
    }

    public function comission(): HasOne
    {
        return $this->hasOne(Comission::class, 'id', 'comission');
    }

    public static function list_responsabilities(): array
    {
        return [
            ['id' => 0, 'title' => 'Presidente'],
            ['id' => 1, 'title' => 'Membro'],
        ];
    }

    public static function list_status(): array
    {
        return [
            ['id' => 0, 'title' => 'Inativo'],
            ['id' => 1, 'title' => 'Ativo'],
        ];
    }
}
