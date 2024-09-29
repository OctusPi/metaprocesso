<?php

namespace App\Models;

use App\Utils\Dates;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ComissionMember extends Model
{
    use HasFactory;

    protected $table = 'comissions_members';

    protected $fillable = [
        'id',
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
        'number_doc'
    ];

    public function rules(): array
    {
        return [
            'name' => 'required',
            'responsibility' => 'required',
            'start_term' => 'required',
            'status' => 'required',
            'organ_id' => 'required',
            'unit_id' => 'required',
            'comission_id' => 'required'
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

    public function organ(): BelongsTo
    {
        return $this->belongsTo(Organ::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function comission(): BelongsTo
    {
        return $this->belongsTo(Comission::class);
    }
}
