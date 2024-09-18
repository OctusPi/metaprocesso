<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Validation\Rule;
use App\Utils\Dates;

class RefTerm extends Model
{
    use HasFactory;

    protected $table = 'refterms';

    protected $fillable = [
        'id',
        'protocol',
        'organ',
        'comission',
        'process',
        'etp',
        'necessity',
        'contract_forecast',
        'contract_requirements',
        'contract_expected_price',
        'market_survey',
        'solution_full_description',
        'ambiental_impacts',
        'correlated_contracts',
        'object_execution_model',
        'contract_management_model',
        'payment_measure_criteria',
        'supplier_selection_criteria',
        'funds_suitability',
        'parts_obligation',
        'emission',
        'type',
    ];

    public function rules(): array
    {
        return [
            'process' => [
                'required',
                Rule::unique('refterms', 'process')
                    ->where('organ', $this->organ)
                    ->ignore($this->id)
            ],
            'etp' => [
                'required',
                Rule::unique('refterms', 'etp')
                    ->where('organ', $this->organ)
                    ->ignore($this->id)
            ],
            'protocol' => [
                'required',
                Rule::unique('refterms', 'protocol')
                    ->where('organ', $this->organ)
                    ->ignore($this->id)
            ],
            'organ' => 'required',
            'comission' => 'required',
            'necessity' => 'required',
            'contract_forecast' => 'required',
            'contract_requirements' => 'required',
            'contract_expected_price' => 'required',
            'market_survey' => 'required',
            'solution_full_description' => 'required',
            'ambiental_impacts' => 'required',
            'correlated_contracts' => 'required',
            'object_execution_model' => 'required',
            'contract_management_model' => 'required',
            'payment_measure_criteria' => 'required',
            'supplier_selection_criteria' => 'required',
            'funds_suitability' => 'required',
            'parts_obligation' => 'required',
            'emission' => 'required',
            'type' => 'required'
        ];
    }

    public static function list_types(): array
    {
        return [
            ['id' => 1, 'title' => 'Contratação de Serviço'],
            ['id' => 2, 'title' => 'Aquisição de Materiais'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'Campo obrigatório não informado!',
            'unique' => 'Termo já registrado no sistema!'
        ];
    }

    public function emission(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => Dates::convert($value, Dates::UTC, Dates::PTBR),
            set: fn(?string $value) => Dates::convert($value, Dates::PTBR, Dates::UTC)
        );
    }

    public function process(): HasOne
    {
        return $this->hasOne(Process::class, 'id', 'process');
    }

    public function organ(): HasOne
    {
        return $this->hasOne(Organ::class, 'id', 'organ');
    }

    public function comission(): HasOne
    {
        return $this->hasOne(Comission::class, 'id', 'comission');
    }

    public function etp(): HasOne
    {
        return $this->hasOne(Etp::class, 'id', 'etp');
    }
}
