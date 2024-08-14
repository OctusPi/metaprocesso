<?php

namespace App\Models;

use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DfdItem extends Model
{
    use HasFactory;

    protected $table = 'dfds_items';

    protected $fillable = [
        'id',
        'dfd',
        'item',
        'quantity',
        'program',
        'dotation',
    ];

    public function rules():array
    {
        return [
            'dfd' => 'required',
            'quantity' => 'required',
            'item' => [
                'required',
                Rule::unique('dfds_items', 'item')->where(function ($query) {
                    return $query->where('dfd', $this->dfd);
            })->ignore($this->id)],
        ];
    }

    public function messages():array
    {
        return [
            'required' => 'Campo obrigatório não informado!',
            'unique'   => 'Item já registrado no DFD!'
        ];
    }

    public function dfd():HasOne
    {
        return $this->hasOne(Dfd::class, 'id', 'dfd');
    }

    public function item():HasOne
    {
        return $this->hasOne(CatalogItem::class, 'id', 'item');
    }

    public function program():HasOne
    {
        return $this->hasOne(Program::class, 'id', 'program');
    }

    public function dotation():HasOne
    {
        return $this->hasOne(Dotation::class, 'id', 'dotation');
    }
}
