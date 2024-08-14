<?php

namespace App\Models;

use App\Models\Organ;
use App\Models\Comission;
use App\Models\CatalogItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Catalog extends Model
{
    use HasFactory;

    protected $table = 'catalogs';
    protected $fillable = [
        'id',
        'organ',
        'comission',
        'name',
        'description'
    ];

    public function rules():array
    {
        return [
            'name'      => 'required',
            'organ'     => 'required',
            'comission' => 'required'
        ];
    }

    public function messages():array
    {
        return [
            'required' => 'Campo obrigatório não informado!'
        ];
    }

    public function organ(): HasOne
    {
        return $this->hasOne(Organ::class, 'id', 'organ');
    }

    public function comission(): HasOne
    {
        return $this->hasOne(Comission::class, 'id', 'comission');
    }

    public function catalogitem():BelongsTo
    {
        return $this->belongsTo(CatalogItem::class);
    }

}
