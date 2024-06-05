<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Program extends Model
{
    use HasFactory;

    protected $table = 'programs';

    protected $fillable = [
        'organ',
        'unit',
        'name',
        'law',
        'description',
        'status',
    ];

    public function organ(): HasOne
    {
        return $this->hasOne(Organ::class, 'id', 'organ');
    }

    public function unit(): HasOne
    {
        return $this->hasOne(Unit::class, 'id', 'unit');
    }

    public function dfditem():BelongsTo
    {
        return $this->belongsTo(DfdItem::class);
    }

    public static function validateFields(?int $id = null):array
    {
        return [
            'name'     => 'required',
            'organ' => 'required',
            'unit'  => 'required',
            'status'   => 'required'
        ];
    }

    public static function validateMsg():array
    {
        return [
            'required' => 'Campo obrigatório não informado!'
        ];
    }
}
