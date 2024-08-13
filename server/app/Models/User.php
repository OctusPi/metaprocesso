<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Casts\Json;
use App\Utils\Dates;
use Illuminate\Validation\Rule;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;
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

        ];
    }

    public static function list_modules(): array
    {
        return [];
    }

    // public function dfd(): BelongsTo
    // {
    //     return $this->belongsTo(Dfd::class);
    // }

    // public function etp(): BelongsTo
    // {
    //     return $this->belongsTo(Etp::class);
    // }

    // public function proccess(): BelongsTo
    // {
    //     return $this->belongsTo(Process::class);
    // }

    // public function pricerecord():BelongsTo
    // {
    //     return $this->belongsTo(PriceRecord::class);
    // }

    // public function riskmap():BelongsTo
    // {
    //     return $this->belongsTo(RiskMap::class);
    // }

}
