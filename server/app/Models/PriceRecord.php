<?php

namespace App\Models;

use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PriceRecord extends Model
{
    use HasFactory;

    const S_START = 1;
    const S_PENDING = 2;
    const S_FINISHED = 3;

    protected $table = 'price_records';

    protected $fillable = [
        'id',
        'protocol',
        'ip',
        'date_ini',
        'date_fin',
        'process',
        'organ',
        'comission',
        'comission_members',
        'suppliers',
        'suppliers_justification',
        'author',
        'status'
    ];

    public function rules():array
    {
        return [
            'protocol'  => 'required',
            'ip'        => 'required',
            'date_ini'  => 'required',
            'process'   => ['required', Rule::unique('price_records', 'process')->ignore($this->id)],
            'organ'     => 'required',
            'comission' => 'required',
            'author'    => 'required',
            'status'    => 'required',
        ];
    }

    public function messages():array
    {
        return [
            'required' => 'Campo obrigatório não informado!',
            'unique'   => 'Registro de preço já iniciada para o processo...'
        ];
    }

    public static function list_status():array
    {
        return [
            ['id' => self::S_START, 'title' => 'Iniciada'],
            ['id' => self::S_PENDING, 'title' => 'Pendente'],
            ['id' => self::S_FINISHED, 'title' => 'Finalizada']
        ];
    }

    public function organ(): HasOne
    {
        return $this->hasOne(Organ::class, 'id', 'organ');
    }

    public function process():HasOne
    {
        return $this->hasOne(Process::class, 'id', 'process');
    }

    public function comission(): HasOne
    {
        return $this->hasOne(Comission::class, 'id', 'comission');
    }

    public function author(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'author');
    }

    public function proposal():BelongsTo
    {
        return $this->belongsTo(Proposal::class);
    }
}
