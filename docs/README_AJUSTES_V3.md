# Ajustes V3 - TechLounged

Este pacote acrescenta três melhorias principais:

1. `views/minhas_inscricoes.php` agora permite cancelar inscrição em eventos que ainda não ocorreram e ainda estão com status `Planejado`.
2. `services/auth/atualizar_perfil.php` foi criado para salvar nome, sobrenome, sexo, e-mail e matrícula do usuário logado.
3. A página pública/admin de eventos (`views/eventos.php`) ganhou botão `Panfleto PDF` para administradores em eventos públicos.

## Arquivos principais para substituir/adicionar

Substitua:

- `services/registro_atividade_controle.php`
- `views/eventos.php`
- `views/minhas_inscricoes.php`
- `views/perfil.php`

Adicione:

- `services/auth/atualizar_perfil.php`
- `views/admin/panfleto_evento.php`

## Sobre a desinscrição

A nova rota é:

```txt
services/registro_atividade_controle.php?acao=desinscrever
```

Ela espera `POST id_inscricao` e só remove a inscrição quando:

- a inscrição pertence ao usuário logado;
- o evento ainda não ocorreu;
- o evento está com status `Planejado`.

## Sobre o panfleto PDF

O botão `Panfleto PDF` aparece somente para administradores/bibliotecários e somente quando `eh_publico = 1`.

A página gerada é:

```txt
views/admin/panfleto_evento.php?id=ID_DO_REGISTRO
```

Ela abre um panfleto visual e aciona `window.print()`, permitindo salvar como PDF pelo navegador.

O QR Code aponta para:

```txt
/views/eventos.php?id_evento=ID_DO_REGISTRO
```

O controller agora aceita o filtro `f_id_registro`, então a página de eventos consegue abrir já filtrada pelo evento do QR Code.

## Observação sobre QR Code

O QR Code usa o serviço público:

```txt
https://api.qrserver.com/v1/create-qr-code/
```

Se o computador estiver sem internet, o panfleto abre normalmente, mas a imagem do QR Code pode não carregar.
