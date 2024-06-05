<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Sector extends Model
{
    use HasFactory;

    protected $table = 'sectors';

    protected $fillable = [
        'organ',
        'unit',
        'name',
        'description'
    ];

    public function organ(): HasOne
    {
        return $this->hasOne(Organ::class, 'id', 'organ');
    }

    public function unit(): HasOne
    {
        return $this->hasOne(Unit::class, 'id', 'unit');
    }

    public function demandant():BelongsTo
    {
        return $this->belongsTo(Demandant::class);
    }

    public static function validateFields(?int $id = null):array
    {
        return [
            'name'     => 'required',
            'organ' => 'required',
            'unit'  => 'required'
        ];
    }

    public static function validateMsg():array
    {
        return [
            'required' => 'Campo obrigatório não informado!'
        ];
    }
}
