<?php

namespace App\Models;

use App\Models\Organ;
use App\Models\Comission;
use App\Models\CatalogItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Catalog extends Model
{
    use HasFactory;

    protected $table = 'catalogs';
    protected $fillable = [
        'id',
        'organ_id',
        'comission_id',
        'name',
        'description'
    ];

    public function rules():array
    {
        return [
            'name'      => 'required',
            'organ_id'     => 'required',
            'comission_id' => 'required'
        ];
    }

    public function messages():array
    {
        return [
            'required' => 'Campo obrigatório não informado!'
        ];
    }

    public function organ(): BelongsTo
    {
        return $this->belongsTo(Organ::class);
    }

    public function comission(): BelongsTo
    {
        return $this->belongsTo(Comission::class);
    }

    public function catalogItems():HasMany
    {
        return $this->hasMany(CatalogItem::class);
    }

}
