<?php

namespace App\Models;

use App\Utils\Dates;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attachment extends Model
{
    use HasFactory;

    public const PROCESS = User::MOD_PROCESSES['id'];
    public const ETP = User::MOD_ETPS['id'];

    protected $table = 'attachments';
    protected $fillable = [
        'id',
        'organ_id',
        'origin',
        'protocol',
        'type',
        'document',
    ];

    public function rules(): array
    {
        return [
            'origin' => 'required',
            'protocol' => 'required',
            'type' => 'required',
            'document' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'Campo obrigatório não informado!',
        ];
    }

    public function updatedAt(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => Dates::convert($value, Dates::TZ, Dates::PTBR),
        );
    }

    public function organ(): BelongsTo
    {
        return $this->belongsTo(Organ::class);
    }
}
