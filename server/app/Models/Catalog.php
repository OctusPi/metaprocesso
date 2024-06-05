<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Catalog extends Model
{
    use HasFactory;

    protected $table = 'catalogs';
    protected $fillable = [
        'organ',
        'comission',
        'name',
        'description'
    ];

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

    public static function validateFields(?int $id = null):array
    {
        return [
            'name'      => 'required',
            'organ'     => 'required',
            'comission' => 'required'
        ];
    }

    public static function validateMsg():array
    {
        return [
            'required' => 'Campo obrigatório não informado!'
        ];
    }

}
