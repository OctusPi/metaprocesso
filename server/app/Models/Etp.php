<?php

namespace App\Models;

use App\Utils\Dates;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Etp extends Model
{
    use HasFactory;

    protected $table = 'etps';

    protected $fillable = [
        'id',
        'ip',
        'protocol',
        'emission',
        'status',
        'process_id',
        'organ_id',
        'comission_id',
        'author_id',
        'object_description',
        'object_classification',
        'necessity',
        'contract_forecast',
        'contract_requirements',
        'market_survey',
        'contract_calculus_memories',
        'contract_expected_price',
        'solution_full_description',
        'solution_parcel_justification',
        'correlated_contracts',
        'contract_alignment',
        'expected_results',
        'contract_previous_actions',
        'ambiental_impacts',
        'viability_declaration',
        'installment_type',
        'installment_justification',
    ];

    public function rules(): array
    {
        return [
            'process_id' => [
                'required',
                Rule::unique('etps', 'process_id')
                    ->ignore($this->id)
            ],
            'protocol' => [
                'required',
                Rule::unique('etps', 'protocol')
                    ->where('organ_id', $this->organ_id)
                    ->ignore($this->id)
            ],
            'ip' => 'required',
            'organ_id' => 'required',
            'comission_id' => 'required',
            'author_id' => 'required',
            'object_description' => 'required',
            'object_classification' => 'required',
            'necessity' => 'required',
            'contract_forecast' => 'required',
            'contract_requirements' => 'required',
            'market_survey' => 'required',
            'contract_calculus_memories' => 'required',
            'contract_expected_price' => 'required',
            'solution_full_description' => 'required',
            'solution_parcel_justification' => 'required',
            'correlated_contracts' => 'required',
            'contract_alignment' => 'required',
            'expected_results' => 'required',
            'contract_previous_actions' => 'required',
            'ambiental_impacts' => 'required',
            'viability_declaration' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'Campo obrigatório não informado!',
            'unique' => 'ETP já registrado no sistema!'
        ];
    }

    public const S_DRAFT = 0;
    public const S_WRITING = 1;
    public const S_PENDENT = 2;
    public const S_FINISHED = 3;
    public const S_BLOCKED = 4;


    public static function list_status(): array
    {
        return [
            ['id' => self::S_DRAFT, 'title' => 'Rascunho'],
            ['id' => self::S_WRITING, 'title' => 'Em Preenchimento'],
            ['id' => self::S_PENDENT, 'title' => 'Pendente'],
            ['id' => self::S_FINISHED, 'title' => 'Finalizado'],
            ['id' => self::S_BLOCKED, 'title' => 'Bloqueado'],
        ];
    }

    public function emission(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => Dates::convert($value, Dates::UTC, Dates::PTBR),
            set: fn(?string $value) => Dates::convert($value, Dates::PTBR, Dates::UTC)
        );
    }

    public function process(): BelongsTo
    {
        return $this->belongsTo(Process::class);
    }

    public function organ(): BelongsTo
    {
        return $this->belongsTo(Organ::class);
    }

    public function comission(): BelongsTo
    {
        return $this->belongsTo(Comission::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function refTerms(): HasMany
    {
        return $this->hasMany(RefTerm::class);
    }
}
