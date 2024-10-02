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
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comission extends Model
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_SUSPENDED = 2;
    const STATUS_EXTINGUED = 3;

    const TYPE_PLANNING = 1;
    const TYPE_DEMANDANT = 2;
    const TYPE_CONTRACT = 3;
    const TYPE_SELECTION = 3;
    const TYPE_FISCAL = 4;
    const TYPE_MANAGEMENT = 5;
    const TYPE_OTHER = 6;

    use HasFactory;

    protected $table = 'comissions';

    protected $fillable = [
        'id',
        'organ_id',
        'unit_id',
        'name',
        'type',
        'document',
        'description',
        'start_term',
        'end_term',
        'status'
    ];

    public function rules(): array
    {
        return [
            'organ_id' => 'required',
            'unit_id' => 'required',
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

    public static function list_types(): array
    {
        return [
            ['id' => self::TYPE_PLANNING, 'title' => 'Comissão de Planejamento e Contratação'],
            ['id' => self::TYPE_DEMANDANT, 'title' => 'Comissão Demandante'],
            ['id' => self::TYPE_CONTRACT, 'title' => 'Comissão de Contratação'],
            ['id' => self::TYPE_SELECTION, 'title' => 'Comissão de Seleção'],
            ['id' => self::TYPE_FISCAL, 'title' => 'Comissão de Fiscalização de Contratos'],
            ['id' => self::TYPE_MANAGEMENT, 'title' => 'Comissão de Gestão de Contratos'],
            ['id' => self::TYPE_OTHER, 'title' => 'Outro Tipo'],
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

    public function organ(): BelongsTo
    {
        return $this->belongsTo(Organ::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function comissionMembers(): HasMany
    {
        return $this->hasMany(ComissionMember::class);
    }

    public function comissionEnds(): HasMany
    {
        return $this->hasMany(ComissionEnd::class);
    }

    public function catalogs(): HasMany
    {
        return $this->hasMany(Catalog::class);
    }

    public function dfds():HasMany
    {
        return $this->hasMany(Dfd::class);
    }

    public function processes():HasMany
    {
        return $this->hasMany(Process::class);
    }
    
    public function etps(): HasMany
    {
        return $this->hasMany(Etp::class);
    }

    public function priceRecords(): HasMany
    {
        return $this->hasMany(PriceRecord::class);
    }

    public function riskMaps(): HasMany
    {
        return $this->hasMany(RiskMap::class);
    }

    public function refTerms(): HasMany
    {
        return $this->hasMany(RefTerm::class);
    }
}
