<?php

namespace App\Models;

use App\Casts\Json;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'suppliers';

    protected $fillable = [
        'id',
        'name',
        'cnpj',
        'agent',
        'cpf',
        'phone',
        'email',
        'address',
        'services',
        'modality',
        'size',
        'organ_id'
    ];

    protected $casts = [
        'services' => Json::class,
    ];

    public function rules(): array
    {
        return [
            'name' => 'required',
            'cnpj' => [
                'required',
                Rule::unique('suppliers', 'cnpj')->where(function ($query) {
                    return $query->where('organ_id', $this->organ_id);
                })->ignore($this->id)
            ],
            'address' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'modality' => 'required',
            'organ_id' => 'required'
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'Campo obrigatório não informado!',
            'unique' => 'Fornecedor já registrado...'
        ];
    }

    public static function list_modalitys(): array
    {
        return [
            ['id' => 1, 'title' => 'Microempreendedor Individual (MEI)'],
            ['id' => 2, 'title' => 'Microempresa (ME)'],
            ['id' => 3, 'title' => 'Empresa de Pequeno Porte (EPP)'],
            ['id' => 4, 'title' => 'Empresa de Grande Porte (EGP)'],
        ];
    }

    public static function list_sizes(): array
    {
        return [
            ['id' => 1, 'title' => 'Porte 1'],
            ['id' => 2, 'title' => 'Porte 2'],
            ['id' => 3, 'title' => 'Porte 3'],
        ];
    }

    public function organ(): BelongsTo
    {
        return $this->belongsTo(Organ::class);
    }

    public function proposal(): HasOne
    {
        return $this->hasOne(Proposal::class);
    }
}
