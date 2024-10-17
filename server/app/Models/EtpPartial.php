<?php

namespace App\Models;

use Illuminate\Validation\Rule;

class EtpPartial extends Etp
{
  public function rules(): array
  {
    return [
      'comission_id',
      'status',
      'emission',
      'process_id' => [
        'required',
        Rule::unique('etps', 'process_id')
          ->ignore($this->id)
      ],
      'protocol' => [
        'required',
        Rule::unique('etps', 'protocol')
          ->where('organ_id', $this->organ_id)
          ->ignore($this->id)
      ],
    ];
  }
}
