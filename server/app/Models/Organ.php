<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    public function units():HasMany
    {
        return $this->hasMany(Unit::class);
    }

    public function sectors():HasMany
    {
        return $this->hasMany(Sector::class);
    }

    public function programs():HasMany
    {
        return $this->hasMany(Program::class);
    }

    public function dotations():HasMany
    {
        return $this->hasMany(Dotation::class);
    }

    public function suppliers():HasMany
    {
        return $this->hasMany(Supplier::class);
    }

    public function demandants():HasMany
    {
        return $this->hasMany(Demandant::class);
    }

    public function ordinators():HasMany
    {
        return $this->hasMany(Ordinator::class);
    }

    public function comissions():HasMany
    {
        return $this->hasMany(Comission::class);
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

    public function catalogSubCategoryItems(): HasMany
    {
        return $this->hasMany(CatalogSubCategoryItem::class);
    }

    public function catalogItems():HasMany
    {
        return $this->hasMany(CatalogItem::class);
    }

    public function dfds():HasMany
    {
        return $this->hasMany(Dfd::class);
    }

    public function attachments():HasMany
    {
        return $this->hasMany(Attachment::class);
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

    public function proposals(): HasMany
    {
        return $this->hasMany(Proposal::class);
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
