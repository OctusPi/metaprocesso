<x-mail::message>

# Recuperação de Senha

Olá, {{ $name }},

Recebemos uma solicitação de recuperação de senha para sua conta no {{ $system }}.

Para redefinir sua senha, clique no botão abaixo:

<x-mail::button :url="$resetUrl">
    Recuperar Senha
</x-mail::button>

Este link expirará em {{ $expiration }}.

Se você não solicitou uma recuperação de senha, ignore este e-mail.

Atenciosamente,<br>

{{ $sender }}

</x-mail::message>
