<?php

namespace App\Models;

use App\Models\Dfd;
use App\Models\Etp;
use App\Models\Unit;
use App\Utils\Dates;
use App\Models\Organ;
use App\Models\Catalog;
use App\Models\Process;
use App\Models\RiskMap;
use App\Models\PriceRecord;
use App\Models\ComissionEnd;
use App\Models\ComissionMember;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comission extends Model
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_SUSPENDED = 2;
    const STATUS_EXTINGUED = 3;

    use HasFactory;

    protected $table = 'comissions';

    protected $fillable = [
        'id',
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

    public function rules(?int $id = null): array
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

    public function messages(): array
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

    public static function get_type(int $type): string
    {
        foreach (self::list_types() as $t) {
            if ($t['id'] == $type) {
                return $t['title'];
            }
        }

        return '';
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

    public static function get_status(int $type): string
    {
        foreach (self::list_status() as $t) {
            if ($t['id'] == $type) {
                return $t['title'];
            }
        }

        return '';
    }

    public function organ(): HasOne
    {
        return $this->hasOne(Organ::class, 'id', 'organ');
    }

    public function unit(): HasOne
    {
        return $this->hasOne(Unit::class, 'id', 'unit');
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

    public function comissionmembers(): HasMany
    {
        return $this->hasMany(ComissionMember::class, 'comission', 'id');
    }

    public function etp(): BelongsTo
    {
        return $this->belongsTo(Etp::class);
    }

    public function proccess(): BelongsTo
    {
        return $this->belongsTo(Process::class);
    }

    public function pricerecord():BelongsTo
    {
        return $this->belongsTo(PriceRecord::class);
    }

    public function riskmaps(): BelongsTo
    {
        return $this->belongsTo(RiskMap::class);
    }
}
