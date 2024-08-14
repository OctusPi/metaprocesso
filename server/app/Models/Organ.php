<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Organ extends Model
{
    use HasFactory;

    const S_INACTIVE = 0;
    const S_ACTIVE = 1;

    protected $table = 'organs';

    protected $fillable = [
        'id',
        'name',
        'cnpj',
        'phone',
        'email',
        'address',
        'postalcity',
        'postalcode',
        'status',
        'logomarca'
    ];

    public function rules(): array
    {
        return [
            'name' => 'required',
            'cnpj' => ['required', Rule::unique('organs', 'cnpj')->ignore($this->id)],
            'phone' => 'required',
            'email' => 'required|email',
            'address' => 'required',
            'postalcity' => 'required',
            'postalcode' => 'required',
            'status' => 'required'
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'Campo obrigatório não informado!',
            'email' => 'Informe um email válido!',
            'unique' => 'Orgão já registrado no sistema!'
        ];
    }

    public static function list_status(): array
    {
        return [
            ['id' => self::S_INACTIVE, 'title' => 'Inativo'],
            ['id' => self::S_ACTIVE, 'title' => 'Ativo']
        ];
    }

    public function dfd(): BelongsTo
    {
        return $this->belongsTo(Dfd::class);
    }

    public function demandant(): BelongsTo
    {
        return $this->belongsTo(Demandant::class);
    }

    public function comission(): BelongsTo
    {
        return $this->belongsTo(Comission::class);
    }

    public function comissionend(): BelongsTo
    {
        return $this->belongsTo(ComissionEnd::class);
    }

    public function comissionmember(): BelongsTo
    {
        return $this->belongsTo(ComissionMember::class);
    }

    public function catalog(): BelongsTo
    {
        return $this->belongsTo(Catalog::class);
    }

    public function catalogitem(): BelongsTo
    {
        return $this->belongsTo(CatalogItem::class);
    }

    public function catalogsubcategoryitem(): BelongsTo
    {
        return $this->belongsTo(CatalogSubCategoryItem::class);
    }

    public function ordinator(): BelongsTo
    {
        return $this->belongsTo(Ordinator::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function dotation(): BelongsTo
    {
        return $this->belongsTo(Dotation::class);
    }

    public function sector(): BelongsTo
    {
        return $this->belongsTo(Sector::class);
    }

    public function etp(): BelongsTo
    {
        return $this->belongsTo(Etp::class);
    }

    public function proccess(): BelongsTo
    {
        return $this->belongsTo(Process::class);
    }

    public function pricerecord(): BelongsTo
    {
        return $this->belongsTo(PriceRecord::class);
    }

    public function proposal(): BelongsTo
    {
        return $this->belongsTo(Proposal::class);
    }

    public function riskmap(): BelongsTo
    {
        return $this->belongsTo(RiskMap::class);
    }
}
