<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Etp extends Model
{
    use HasFactory;

    protected $table = 'etps';

    protected $fillable = [
        'dfds',
        'number',
        'general_info',
        'object_description',
        'object_classification',
        'necessity',
        'contract_forecast',
        'contract_requirements',
        'market_survey',
        'contract_calculus_memories',
        'contract_calculus_memories_file',
        'contract_expected_price',
        'contract_expected_price_file',
        'solution_full_description',
        'solution_parcel_justification',
        'correlated_contracts',
        'contract_alignment',
        'expected_results',
        'contract_previous_actions',
        'ambiental_impacts',
        'viability_declaration',
    ];

    protected $casts = [
        'dfds' => Json::class,
    ];

    public static function list_status(): array
    {
        return [
            ['id'=>0,'title'=> 'Rascunho'],
            ['id'=>1,'title'=> 'Concluído'],
        ];
    }
}
