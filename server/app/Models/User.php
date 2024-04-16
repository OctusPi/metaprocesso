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

    public const PRF_ADMIN = 1;
    public const PRF_GESTOR = 2;
    public const PRF_TECNICO = 3;
    public const PRF_AGENTE = 4;
    public const PRF_AUTIDOR = 5;

    public const MOD_INI = 0;
    public const MOD_CATALOGS = 1;
    public const MOD_SUPPLIERS = 2;
    public const MOD_DFDS = 3;
    public const MOD_PRICERECORDS = 4;
    public const MOD_CONTRACTS = 5;
    public const MOD_PURCHASES = 6;
    public const MOD_STOCK = 7;
    public const MOD_OCURRENCYS = 8;
    public const MOD_CONSTRUCTIONS = 9;
    public const MOD_LIGHTINGS = 10;
    public const MOD_TRASHCOLECTS = 11;
    public const MOD_SANCTIONS = 12;
    public const MOD_REPORTS = 13;
    public const MOD_MANAGEMENT = 14;

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

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'organs'  => Json::class,
        'units'   => Json::class,
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

    public static function validateFields(?int $id = null):array
    {
        return [
            'name'   => 'required',
            'email'  => 'required|email|unique:users'.($id ? ',id,'.$id : ''),
            'profile' => 'required',
            'status' => 'required'
        ];
    }

    public static function validateMsg():array
    {
        return [
            'required' => 'Campo obrigatório não informado!',
            'email'    => 'Informe um email válido!',
            'unique'   => 'Usuário já registrado no sistema!'
        ];
    }

    public static function list_profiles(): array
    {
        return [
            self::PRF_ADMIN   => 'Administrador', 
            self::PRF_GESTOR  => 'Gestor', 
            self::PRF_TECNICO => 'Técnico', 
            self::PRF_AGENTE  => 'Agente', 
            self::PRF_AUTIDOR => 'Auditor'
        ];
    }
    

    public static function list_modules(): array
    {
        return [
            ['id' => self::MOD_INI, 'module' => 'dashboard', 'title' => 'Acesso Inicial'],
            ['id' => self::MOD_MANAGEMENT, 'module' => 'management', 'title' => 'Gestão Administrativa'],
            ['id' => self::MOD_CATALOGS, 'module' => 'catalogs', 'title' => 'Catálogos'],
            ['id' => self::MOD_SUPPLIERS, 'module' => 'suppliers', 'title' => 'Fornecedores'], 
            ['id' => self::MOD_DFDS, 'module' => 'dfds', 'title' => 'DFDs'], 
            ['id' => self::MOD_PRICERECORDS, 'module' => 'pricerecords', 'title' => 'Registro de Preços'], 
            ['id' => self::MOD_CONTRACTS, 'module' => 'contracts', 'title' => 'Contratos'], 
            ['id' => self::MOD_PURCHASES, 'module' => 'purchases', 'title' => 'Ordens de Compras'],
            ['id' => self::MOD_STOCK, 'module' => 'stock', 'title' => 'Estoque'],
            ['id' => self::MOD_OCURRENCYS, 'module' => 'ocurrencys', 'title' => 'Ocorrências'],
            ['id' => self::MOD_CONSTRUCTIONS, 'module' => 'constructions', 'title' => 'Fical de Obras'],
            ['id' => self::MOD_LIGHTINGS, 'module' => 'lightings', 'title' => 'Iluminação Pública'],
            ['id' => self::MOD_TRASHCOLECTS, 'module' => 'trashcollect', 'title' => 'Coleta e Limpeza de Lixo'],
            ['id' => self::MOD_SANCTIONS, 'module' => 'sanctions', 'title' => 'Processos de Sanções'],
            ['id' => self::MOD_REPORTS, 'module' => 'reports', 'title' => 'Relatórios'],
        ];
    }

    

}
