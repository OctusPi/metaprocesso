<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'origin',
        'protocol',
        'type',
        'file',
    ];

    public static function validateFields(?int $id = null): array
    {
        return [
            'origin' => 'required',
            'protocol' => 'required',
            'type' => 'required',
            'file' => 'required',
        ];
    }

    public static function validateMsg(): array
    {
        return [
            'required' => 'Campo obrigatório não informado!',
            'unique' => 'ETP já registrado no sistema!'
        ];
    }

}
