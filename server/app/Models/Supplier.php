<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'suppliers';

    protected $fillable = [
        'name',
        'cnpj',
        'agent',    
        'cpf',
        'phone',
        'email',
        'address',
    ];

    public static function validateFields(?int $id = null):array
    {
        return [
            'name'     => 'required',
            'cnpj' => 'required',
            'phone'  => 'required',
            'email'  => 'required',
            'address'  => 'required',
        ];
    }

    public static function validateMsg():array
    {
        return [
            'required' => 'Campo obrigatório não informado!'
        ];
    }
}
