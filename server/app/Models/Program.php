<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
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
        'organ',
        'unit',
        'name',
        'law',
        'description',
        'status',
    ];

    public function rules(): array
    {
        return [
            'name' => 'required',
            'organ' => 'required',
            'unit' => 'required',
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

    public function organ(): HasOne
    {
        return $this->hasOne(Organ::class, 'id', 'organ');
    }

    public function unit(): HasOne
    {
        return $this->hasOne(Unit::class, 'id', 'unit');
    }

    public function dfditem(): BelongsTo
    {
        return $this->belongsTo(DfdItem::class);
    }


}
