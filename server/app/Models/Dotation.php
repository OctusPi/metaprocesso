<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dotation extends Model
{
    use HasFactory;

    protected $table = 'dotations';

    protected $fillable = [
        'name',
        'organ',
        'unit',
        'law',
        'description',
        'status',
    ];

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
