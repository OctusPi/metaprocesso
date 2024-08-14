<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Casts\Json;
use App\Utils\Dates;
use Illuminate\Validation\Rule;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public const PRF_ADMIN = 1;
    public const PRF_GESTOR = 2;
    public const PRF_TECNICO = 3;
    public const PRF_AGENTE = 4;
    public const PRF_AUTIDOR = 5;

    public const MOD_INI = ['id' => 0, 'module' => 'dashboard', 'title' => 'Acesso Inicial'];
    public const MOD_MANAGEMENT = ['id' => 1, 'module' => 'management', 'title' => 'Gestão Administrativa'];
    public const MOD_CATALOGS = ['id' => 2, 'module' => 'catalogs', 'title' => 'Catálogos'];
    public const MOD_DFDS = ['id' => 3, 'module' => 'dfds', 'title' => 'DFDs'];
    public const MOD_ETPS = ['id' => 4, 'module' => 'etps', 'title' => 'ETPs'];
    public const MOD_PRICERECORDS = ['id' => 5, 'module' => 'pricerecords', 'title' => 'Registro de Preços'];
    public const MOD_REFTERM = ['id' => 6, 'module' => 'refterms', 'title' => 'Termos de Referência'];
    public const MOD_RISKINESS = ['id' => 7, 'module' => 'riskiness', 'title' => 'Mapas de Risco'];
    public const MOD_PROCCESS = ['id' => 8, 'module' => 'processes', 'title' => 'Processos'];
    public const MOD_TRANSMISSION = ['id' => 9, 'module' => 'trasmission', 'title' => 'Publicação e Transmissão'];
    public const MOD_REPORTS = ['id' => 10, 'module' => 'reports', 'title' => 'Relatórios'];
    public const MOD_USERS = ['id' => 11, 'module' => 'users', 'title' => 'Gestão de Usuários'];
    public const MOD_ORGANS = ['id' => 12, 'module' => 'organs', 'title' => 'Gestão de Orgãos'];
    public const MOD_UNITS = ['id' => 13, 'module' => 'units', 'title' => 'Gestão de Unidades'];
    public const MOD_SECTORS = ['id' => 14, 'module' => 'sectors', 'title' => 'Gestão de Setores'];
    public const MOD_ORDINATORS = ['id' => 15, 'module' => 'ordinators', 'title' => 'Gestão de Ordenadores'];
    public const MOD_DEMANDANTS = ['id' => 16, 'module' => 'demandants', 'title' => 'Gestão de Demandantes'];
    public const MOD_COMISSIONS = ['id' => 17, 'module' => 'comissions', 'title' => 'Gestão de Comissões'];
    public const MOD_PROGRAMS = ['id' => 18, 'module' => 'programs', 'title' => 'Gestão de Programas'];
    public const MOD_DOTATIONS = ['id' => 19, 'module' => 'dotations', 'title' => 'Gestão de Dotações'];
    public const MOD_ATTACHMENT = ['id' => 20, 'module' => 'attachment', 'title' => 'Controle de Anexos'];

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

    public function rules(): array
    {
        return [
            'name'    => 'required',
            'email'   => ['required', 'email', Rule::unique('users', 'email')->ignore($this->id)],
            'profile' => 'required',
            'status'  => 'required'
        ];
    }

    public static function messages(): array
    {
        return [
            'required'  => 'Campo obrigatório não informado!',
            'email'     => 'Informe um email válido!',
            'unique'    => 'Usuário já registrado no sistema!'
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
            self::MOD_INI,
            self::MOD_MANAGEMENT,
            self::MOD_USERS,
            self::MOD_ORGANS,
            self::MOD_UNITS,
            self::MOD_SECTORS,
            self::MOD_ORDINATORS,
            self::MOD_DEMANDANTS,
            self::MOD_COMISSIONS,
            self::MOD_PROGRAMS,
            self::MOD_DOTATIONS,
            self::MOD_CATALOGS,
            self::MOD_DFDS,
            self::MOD_ETPS,
            self::MOD_PRICERECORDS,
            self::MOD_REFTERM,
            self::MOD_RISKINESS,
            self::MOD_PROCCESS,
            self::MOD_TRANSMISSION,
            self::MOD_REPORTS
        ];
    }

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

    public function riskmap():BelongsTo
    {
        return $this->belongsTo(RiskMap::class);
    }

}
