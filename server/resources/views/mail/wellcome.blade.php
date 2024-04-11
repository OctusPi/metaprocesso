<x-mail::message>

# Bem-vindo ao Nosso Sistema!

Olá, {{ $name }},

Seja bem-vindo ao {{ $system }}! Estamos muito felizes em tê-lo(a) conosco.

Esperamos que você aproveite ao máximo todas as funcionalidades e recursos que oferecemos.

Se surgir alguma dúvida ou se precisar de ajuda, não hesite em entrar em contato conosco.

--------------------------------
| Usuário: **{{ $username }}** |
| Senha: **{{ $pass }}**       |
--------------------------------

<x-mail::button :url="$system_url">
    Acessar Sistema
</x-mail::button>

Atenciosamente, <br>

{{ $sender }}

</x-mail::message>
