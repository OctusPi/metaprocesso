@component('mail::message')
# Solicitação de Cotação de Preço {{ $system }}

Prezado(a) **{{ $supplier['nome'] }}**,

Estamos solicitando uma cotação de preço para a aquisição dos itens descritos abaixo, conforme os dados do processo:

- **Protocolo**: {{ $process['protocol'] }}
- **Data da Solicitação**: {{ $process['date_ini'] }}
- **Requisitante**: {{ $process['organ'] }}
- **Objeto**: {{ $process['description'] }}

## Dados do Fornecedor

- **Nome**: {{ $supplier['name'] }}
- **CNPJ**: {{ $supplier['cnpj'] }}
- **Endereço**: {{ $supplier['address'] }}

Para validar a proposta e realizar o preenchimento da cotação, utilize o código de validação abaixo:

- **Código de Validação**: {{ $code_validation }}

@component('mail::button', ['url' => $proposal_url])
Preencher Proposta
@endcomponent

Aguardamos sua resposta até a data de vencimento: {{ $date_fin }}.

Atenciosamente,<br>
**{{ $sender }}**

@endcomponent
