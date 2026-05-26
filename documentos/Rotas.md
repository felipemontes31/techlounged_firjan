# Backend Routes - Documentação (Markdown)
## Base: Todas as rotas usam ?acao=NOME via GET/POST. Retorno sempre JSON.
### 1. Usuários (/services/usuario_controle.php)

?acao=listar → Lista todos (admin apenas)
?acao=buscar&id=ID → Busca por ID (admin ou próprio usuário)
?acao=login (POST) → Login
?acao=cadastrar (POST) → Cadastro

### 2. Registros de Atividade (/services/registro_atividade_controle.php)

?acao=listar → Lista registros
?acao=buscar&id=ID → Busca por ID

Padrão dos demais (pastas: atividade, auth, cine_blibioteca, eixo, funcao):
?acao=listar | buscar | cadastrar | editar | deletar

Acesse os arquivos em /services/ para parâmetros exatos.