<?php

namespace App\Models;

use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DfdItem extends Model
{
    use HasFactory;

    protected $table = 'dfds_items';

    protected $fillable = [
        'id',
        'dfd_id',
        'item_id',
        'quantity',
        'program_id',
        'dotation_id',
    ];

    public function rules():array
    {
        return [
            'dfd_id' => 'required',
            'quantity' => 'required',
            'item_id' => [
                'required',
                Rule::unique('dfds_items', 'item_id')->where(function ($query) {
                    return $query->where('dfd_id', $this->dfd_id);
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

    public function dfd(): BelongsTo
    {
        return $this->belongsTo(Dfd::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(CatalogItem::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function dotation(): BelongsTo
    {
        return $this->belongsTo(Dotation::class);
    }
}
