<?php

namespace App\Models;

use App\Utils\Dates;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Comission extends Model
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_SUSPENDED = 2;
    const STATUS_EXTINGUED = 3;

    use HasFactory;

    protected $table = 'comissions';

    protected $fillable = [
        'organ',
        'unit',
        'name',
        'type',
        'document',
        'description',
        'start_term',
        'end_term',
        'status'
    ];

    public function organ(): HasOne
    {
        return $this->hasOne(Organ::class, 'id');
    }

    public function unit(): HasOne
    {
        return $this->hasOne(Unit::class, 'id');
    }

    public function catalog(): BelongsTo
    {
        return $this->belongsTo(Catalog::class);
    }

    public function dfd(): BelongsTo
    {
        return $this->belongsTo(Dfd::class);
    }

    public function comissionend(): BelongsTo
    {
        return $this->belongsTo(ComissionEnd::class);
    }

    public function comissionmember(): BelongsTo
    {
        return $this->belongsTo(ComissionMember::class);
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

    public static function validateFields(?int $id = null): array
    {
        return [
            'organ' => 'required',
            'unit' => 'required',
            'name' => 'required',
            'type' => 'required',
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

    public static function list_types(): array
    {
        return [
            ['id' => 0, 'title' => 'Comissão de Planejamento e Contratação'],
            ['id' => 1, 'title' => 'Comissão de Licitação'],
            ['id' => 2, 'title' => 'Comissão de Gestão e Fiscalização de Contratos'],
            ['id' => 3, 'title' => 'Comissão de Auditoria de Processo de Contratação']
        ];
    }

    public static function list_status(): array
    {
        return [
            ['id' => self::STATUS_INACTIVE, 'title' => 'Inativa'],
            ['id' => self::STATUS_ACTIVE, 'title' => 'Ativa'],
            ['id' => self::STATUS_SUSPENDED, 'title' => 'Suspensa'],
            ['id' => self::STATUS_EXTINGUED, 'title' => 'Finalizada']
        ];
    }
}
