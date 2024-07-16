<?php

namespace App\Models;

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
            Common::PRF_ADMIN => 'Administrador',
            Common::PRF_GESTOR => 'Gestor',
            Common::PRF_TECNICO => 'Técnico',
            Common::PRF_AGENTE => 'Agente',
            Common::PRF_AUTIDOR => 'Auditor'
        ];
    }

    public static function list_modules(): array
    {
        return [
            Common::MOD_INI,
            Common::MOD_MANAGEMENT,
            Common::MOD_USERS,
            Common::MOD_ORGANS,
            Common::MOD_UNITS,
            Common::MOD_SECTORS,
            Common::MOD_ORDINATORS,
            Common::MOD_DEMANDANTS,
            Common::MOD_COMISSIONS,
            Common::MOD_PROGRAMS,
            Common::MOD_DOTATIONS,
            Common::MOD_CATALOGS, 
            Common::MOD_DFDS,
            Common::MOD_ETPS,
            Common::MOD_PRICERECORDS,
            Common::MOD_REFTERM,
            Common::MOD_RISKINESS,
            Common::MOD_PROCCESS,
            Common::MOD_TRANSMISSION,
            Common::MOD_REPORTS
        ];
    }

    
}
