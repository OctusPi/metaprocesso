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

    public function demandant():BelongsTo
    {
        return $this->belongsTo(Demandant::class);
    }

    public function organ(): HasOne
    {
        return $this->hasOne(Organ::class, 'id');
    }

    public function unit(): HasOne
    {
        return $this->hasOne(Unit::class, 'id');
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
