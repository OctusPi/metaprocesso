<?php

namespace App\Models;

use App\Casts\Json;
use App\Utils\Dates;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
    public const MOD_MANAGEMENT = 1;
    public const MOD_CATALOGS = 2;
    public const MOD_DFDS = 3;
    public const MOD_ETPS = 4;
    public const MOD_PRICERECORDS = 5;
    public const MOD_REFTERM = 6;
    public const MOD_RISKINESS = 7;
    public const MOD_PROCCESS = 8;
    public const MOD_TRANSMISSION = 9;
    public const MOD_REPORTS = 10;
    public const MOD_USERS = 11;
    public const MOD_ORGANS = 12;
    public const MOD_UNITS = 13;
    public const MOD_SECTORS = 14;
    public const MOD_ORDINATORS = 15;
    public const MOD_DEMANDANTS = 16;
    public const MOD_COMISSIONS = 17;
    public const MOD_PROGRAMS = 18;
    public const MOD_DOTATIONS = 19;

    protected $table = 'users';

    protected $fillable = [
        'id',
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

    public function dfd(): BelongsTo
    {
        return $this->belongsTo(Dfd::class);
    }

    public function etp(): BelongsTo
    {
        return $this->belongsTo(Etp::class);
    }

    public function proccess(): BelongsTo
    {
        return $this->belongsTo(Process::class);
    }

    public function pricerecord():BelongsTo
    {
        return $this->belongsTo(PriceRecord::class);
    }

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

    public static function validateFields(?int $id = null): array
    {
        return [
            'name' => 'required',
            'email' => ['required', 'email', Rule::unique('users')->ignore($id)],
            'profile' => 'required',
            'status' => 'required'
        ];
    }

    public static function validateMsg(): array
    {
        return [
            'required' => 'Campo obrigatório não informado!',
            'email' => 'Informe um email válido!',
            'unique' => 'Usuário já registrado no sistema!'
        ];
    }

    public static function list_profiles(): array
    {
        return [
            self::PRF_ADMIN => 'Administrador',
            self::PRF_GESTOR => 'Gestor',
            self::PRF_TECNICO => 'Técnico',
            self::PRF_AGENTE => 'Agente',
            self::PRF_AUTIDOR => 'Auditor'
        ];
    }

    public static function list_modules(): array
    {
        return [
            ['id' => self::MOD_INI, 'module' => 'dashboard', 'title' => 'Acesso Inicial'],
            ['id' => self::MOD_MANAGEMENT, 'module' => 'management', 'title' => 'Gestão Administrativa'],
            ['id' => self::MOD_USERS, 'module' => 'users', 'title' => 'Gestão de Usuários'],
            ['id' => self::MOD_ORGANS, 'module' => 'organs', 'title' => 'Gestão de Orgãos'],
            ['id' => self::MOD_UNITS, 'module' => 'units', 'title' => 'Gestão de Unidades'],
            ['id' => self::MOD_SECTORS, 'module' => 'sectors', 'title' => 'Gestão de Setores'],
            ['id' => self::MOD_ORDINATORS, 'module' => 'ordinators', 'title' => 'Gestão de Ordenadores'],
            ['id' => self::MOD_DEMANDANTS, 'module' => 'demandants', 'title' => 'Gestão de Demandantes'],
            ['id' => self::MOD_COMISSIONS, 'module' => 'comissions', 'title' => 'Gestão de Comissões'],
            ['id' => self::MOD_PROGRAMS, 'module' => 'programs', 'title' => 'Gestão de Programas'],
            ['id' => self::MOD_DOTATIONS, 'module' => 'dotations', 'title' => 'Gestão de Dotações'],
            ['id' => self::MOD_CATALOGS, 'module' => 'catalogs', 'title' => 'Catálogos'],
            ['id' => self::MOD_DFDS, 'module' => 'dfds', 'title' => 'DFDs'],
            ['id' => self::MOD_ETPS, 'module' => 'etps', 'title' => 'ETPs'],
            ['id' => self::MOD_PRICERECORDS, 'module' => 'pricerecords', 'title' => 'Registro de Preços'],
            ['id' => self::MOD_REFTERM, 'module' => 'refterms', 'title' => 'Termos de Referência'],
            ['id' => self::MOD_RISKINESS, 'module' => 'riskiness', 'title' => 'Mapas de Risco'],
            ['id' => self::MOD_PROCCESS, 'module' => 'process', 'title' => 'Processos'],
            ['id' => self::MOD_TRANSMISSION, 'module' => 'trasmission', 'title' => 'Publicação e Transmissão'],
            ['id' => self::MOD_REPORTS, 'module' => 'reports', 'title' => 'Relatórios']
        ];
    }

    public static function build_modules(?array $data = null): array
    {
        $modules = [];

        if(!is_null($data)){
            foreach(self::list_modules() as $mod){
                foreach ($data as $dat) {
                    if($mod['id'] == $dat){
                        $modules[] = $mod;
                    }
                }
            }
        }

        return $modules;
    }
}
