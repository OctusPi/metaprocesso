<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Program extends Model
{
    use HasFactory;

    const S_INACTIVE = 0;
    const S_ACTIVE = 1;

    protected $table = 'programs';

    protected $fillable = [
        'id',
        'organ_id',
        'unit_id',
        'name',
        'law',
        'description',
        'status',
    ];

    public function rules(): array
    {
        return [
            'name' => 'required',
            'organ_id' => 'required',
            'unit_id' => 'required',
            'status' => 'required'
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'Campo obrigatório não informado!'
        ];
    }

    public static function list_status(): array
    {
        return [
            ['id' => self::S_INACTIVE, 'title' => 'Inativo'],
            ['id' => self::S_ACTIVE, 'title' => 'Ativo']
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

    public function dfdItems():HasMany
    {
        return $this->hasMany(DfdItem::class);
    }

}
