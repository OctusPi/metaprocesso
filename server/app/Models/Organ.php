<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Organ extends Model
{
    use HasFactory;

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

    public static function validateFields(?int $id = null): array
    {
        return [
            'name' => 'required',
            'cnpj' => ['required', Rule::unique('organs')->ignore($id)],
            'phone' => 'required',
            'email' => 'required|email',
            'address' => 'required',
            'postalcity' => 'required',
            'postalcode' => 'required',
            'status' => 'required'
        ];
    }

    public static function validateMsg(): array
    {
        return [
            'required' => 'Campo obrigatório não informado!',
            'email' => 'Informe um email válido!',
            'unique' => 'Orgão já registrado no sistema!'
        ];
    }

}
