<x-mail::message>

Olá,

Solicitamos o seu cadastro para ser forncedor do sistema {{ $system }}.

Esperamos que você aproveite ao máximo todas as funcionalidades e recursos que oferecemos.

Se surgir alguma dúvida ou se precisar de ajuda, não hesite em entrar em contato conosco.

<x-mail::button :url="$form_url">
    Acessar Formulário
</x-mail::button>

Atenciosamente, <br>

{{ $sender }}

</x-mail::message>
