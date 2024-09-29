<?php

namespace App\Models;

use App\Utils\Dates;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Demandant extends Model
{
    use HasFactory;

    protected $table = 'demandants';

    protected $fillable = [
        'id',
        'organ_id',
        'unit_id',
        'sector_id',
        'name',
        'cpf',
        'registration',
        'document',
        'start_term',
        'end_term',
        'status'
    ];

    public function rules():array
    {
        return [
            'organ_id' => 'required',
            'unit_id' => 'required',
            'name' => 'required',
            'cpf' => [
                'required',
                Rule::unique('demandants', 'cpf')->where(function ($query) {
                    return $query->where('unit_id', $this->unit_id);
            })->ignore($this->id)],
            'start_term' => 'required',
            'status'     => 'required',
        ];
    }

    public function messages():array
    {
        return [
            'required' => 'Campo obrigatório não informado!',
            'unique'   => 'Demandante já registrado no sistema para essa unidade!'
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

    public static function list_status(): array
    {
        return [
            ['id'=>0,'title'=> 'Desligado'],
            ['id'=>1,'title'=> 'Ativo']
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

    public function sector(): BelongsTo
    {
        return $this->belongsTo(Sector::class);
    }

    public function dfds():HasMany
    {
        return $this->hasMany(Dfd::class);
    }
}
