<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'suppliers';

    protected $fillable = [
        'id',
        'name',
        'cnpj',
        'agent',
        'cpf',
        'phone',
        'email',
        'address',
        'modality',
        'size',
    ];

    public function rules(): array
    {
        return [
            'name' => 'required',
            'cnpj' => 'required',
            'address' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'modality' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'Campo obrigatório não informado!'
        ];
    }

    public static function list_modalitys(): array
    {
        return [
            ['id' => 1, 'title' => 'Microempreendedor Individual (MEI)'],
            ['id' => 2, 'title' => 'Microempresa (ME)'],
            ['id' => 3, 'title' => 'Empresa de Pequeno Porte (EPP)'],
            ['id' => 4, 'title' => 'Empresa de Grande Porte (EGP)'],
        ];
    }

    public static function list_sizes(): array
    {
        return [
            ['id' => 1, 'title' => 'Porte 1'],
            ['id' => 2, 'title' => 'Porte 2'],
            ['id' => 3, 'title' => 'Porte 3'],
        ];
    }
}
