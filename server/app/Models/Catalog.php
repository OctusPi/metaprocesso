<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

    public function organ(): BelongsTo
    {
        return $this->belongsTo(Organ::class, 'organ_id');
    }

    public function comission(): BelongsTo
    {
        return $this->belongsTo(Comission::class, 'comission_id');
    }

    public static function validateFields(?int $id = null):array
    {
        return [
            'name'     => 'required',
            'organ_id' => 'required',
            'comission_id'  => 'required'
        ];
    }

    public static function validateMsg():array
    {
        return [
            'required' => 'Campo obrigatório não informado!'
        ];
    }

}
