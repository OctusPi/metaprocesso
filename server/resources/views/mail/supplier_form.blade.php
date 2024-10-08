<x-mail::message>

Caro Fornecedor,

O sistema {{ $system }} solicita o seu cadastro para uma .

Esperamos que você aproveite ao máximo todas as funcionalidades e recursos que oferecemos.

Se surgir alguma dúvida ou se precisar de ajuda, não hesite em entrar em contato conosco.

| Usuário: **{{ $username }}**
| Senha: **{{ $pass }}**

<x-mail::button :url="$system_url">
    Acessar Sistema
</x-mail::button>

Atenciosamente, <br>

{{ $sender }}

</x-mail::message>
