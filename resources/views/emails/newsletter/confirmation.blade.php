<x-mail::message>
# Bem-vindo(a) à nossa Newsletter!

Olá {{ $subscriber->name ?? 'Membro' }},

É uma alegria ter você conosco! Sua inscrição na newsletter da **{{ $siteName }}** foi realizada com sucesso.

A partir de agora, você receberá diretamente em sua caixa de entrada:
*   Notícias e avisos importantes da nossa comunidade.
*   Agenda de eventos e programações especiais.
*   Devocionais e estudos bíblicos exclusivos.

<x-mail::button :url="config('app.url')">
Acessar nosso Portal
</x-mail::button>

Se você não solicitou esta inscrição, sinta-se à vontade para ignorar este e-mail ou clicar no link de cancelamento no rodapé.

Que Deus te abençoe!

Atenciosamente,<br>
Equipe {{ $siteName }}
</x-mail::message>
