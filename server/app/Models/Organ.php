<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Organ extends Model
{
    use HasFactory;

    protected $table = 'organs';

    protected $fillable = [
        'id',
        'name',
        'cnpj',
        'phone',
        'email',
        'address',
        'postalcity',
        'postalcode',
        'status'
    ];

    public static function validateFields(?int $id = null):array
    {
        return [
            'name'       => 'required',
            'cnpj'       => 'required|unique:organs'.($id ? ',id,'.$id : ''),
            'phone'      => 'required',
            'email'      => 'required|email',
            'address'    => 'required',
            'postalcity' => 'required',
            'postalcode' => 'required',
            'status'     => 'required'
        ];
    }

    public static function validateMsg():array
    {
        return [
            'required' => 'Campo obrigatório não informado!',
            'email'    => 'Informe um email válido!',
            'unique'   => 'Orgão já registrado no sistema!'
        ];
    }

}
