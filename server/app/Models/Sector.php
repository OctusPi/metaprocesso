<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sector extends Model
{
    use HasFactory;

    protected $table = 'sectors';

    protected $fillable = [
        'id',
        'organ_id',
        'unit_id',
        'name',
        'description'
    ];

    public function rules():array
    {
        return [
            'name'  => 'required',
            'organ_id' => 'required',
            'unit_id'  => 'required'
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

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function demandants():HasMany
    {
        return $this->hasMany(Demandant::class);
    }
}
