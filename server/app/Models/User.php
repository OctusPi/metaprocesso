<?php

namespace App\Models;

use App\Utils\Dates;
use Illuminate\Database\Eloquent\Model;
use App\Casts\Json;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Model
{
    use HasFactory;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'token',
        'organs',
        'units',
        'sectors',
        'profile',
        'modules',
        'passchange',
        'status',
        'nowlogin',
        'lastlogin'
    ];

    protected $casts = [
        'organs' => Json::class,
        'units' => Json::class,
        'sectors' => Json::class,
        'modules' => Json::class,
    ];

    public function nowlogin(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => Dates::convert($value, Dates::UTC_TIME, Dates::PTBR_TIME),
            set: fn(?string $value) => Dates::convert($value, Dates::PTBR_TIME, Dates::UTC_TIME)
        );
    }


    public function lastlogin(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => Dates::convert($value, Dates::UTC_TIME, Dates::PTBR_TIME),
            set: fn(?string $value) => Dates::convert($value, Dates::PTBR_TIME, Dates::UTC_TIME)
        );
    }

    public function profile(): Attribute
    {
        return Attribute::make(
            get: fn(?int $value) => self::list_profiles()[$value] ?? ''
        );
        
    }

    public static function list_profiles(): array
    {
        return [1 => 'Administrador', 2 => 'Gestor', 3 => 'Técnico', 4 => 'Agente', 5 => 'Auditor'];
    }
    

    public static function list_modules(): array
    {
        return [
            0 => ['module' => '/', 'title' => 'Acesso Inicial'],
            1=> ['module' => 'catalogs', 'title' => 'Catálogos'],
            2=> ['module' => 'suppliers', 'title' => 'Fornecedores'], 
            3=> ['module' => 'dfds', 'title' => 'DFDs'], 
            4=> ['module' => 'pricerecords', 'title' => 'Registro de Preços'], 
            5=> ['module' => 'contracts', 'title' => 'Contratos'], 
            6=> ['module' => 'purchases', 'title' => 'Ordens de Compras'],
            7=> ['module' => 'stock', 'title' => 'Estoque'],
            8=> ['module' => 'ocurrencys', 'title' => 'Ocorrências'],
            9=> ['module' => 'constructions', 'title' => 'Fical de Obras'],
            10=> ['module' => 'lightings', 'title' => 'Iluminação Pública'],
            11=> ['module' => 'trashcollect', 'title' => 'Coleta e Limpeza de Lixo'],
            12=> ['module' => 'sanctions', 'title' => 'Processos de Sanções'],
            13=> ['module' => 'reports', 'title' => 'Relatórios'],
            14=> ['module' => 'management', 'title' => 'Gestão Administrativa'],
        ];
    }

}
