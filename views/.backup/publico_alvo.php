<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Público Alvo</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; }
        table { width: 50%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background: #f4f4f4; }
        form { margin-bottom: 20px; }
        .btn-cancelar { background-color: #f44336; color: white; border: none; padding: 5px 10px; cursor: pointer; }
    </style>
</head>
<body>

    <h2>Gerenciar Público Alvo</h2>

    <form id="formPublicoAlvo">
        <input type="hidden" id="id" name="id">
        <label for="nome_publico">Nome do Público:</label>
        <input type="text" id="nome_publico" name="nome_publico" required placeholder="Ex: Jovens, Adultos...">
        <button type="submit" id="btnSalvar">Salvar</button>
        <button type="button" id="btnCancelar" class="btn-cancelar" style="display:none;" onclick="resetarFormulario()">Cancelar Edição</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Público Alvo</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody id="tabelaCorpo"></tbody>
    </table>

    <script>
        const urlController = '../services/publico_alvo_controle.php';

        // Inicializa a listagem ao carregar o documento
        document.addEventListener("DOMContentLoaded", listar);

        // Submissão do formulário
        document.getElementById('formPublicoAlvo').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const id = document.getElementById('id').value;
            const acao = id ? 'atualizar' : 'criar';
            const formData = new FormData(this);

            fetch(`${urlController}?acao=${acao}`, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(res => {
                alert(res.mensagem);
                if(res.sucesso) {
                    resetarFormulario();
                    listar();
                }
            });
        });

        // Função para Listar os Registros
        function listar() {
            fetch(`${urlController}?acao=listar`)
            .then(res => res.json())
            .then(res => {
                const corpo = document.getElementById('tabelaCorpo');
                corpo.innerHTML = '';
                
                if(res.sucesso) {
                    res.dados.forEach(item => {
                        corpo.innerHTML += `
                            <tr>
                                <td>${item.id}</td>
                                <td>${item.nome_publico}</td>
                                <td>
                                    <button onclick="editar(${item.id})">Editar</button>
                                    <button onclick="deletar(${item.id})">Excluir</button>
                                </td>
                            </tr>
                        `;
                    });
                }
            });
        }

        // Busca o registro e preenche o formulário para edição
        function editar(id) {
            fetch(`${urlController}?acao=buscar&id=${id}`)
            .then(res => res.json())
            .then(res => {
                if(res.sucesso) {
                    document.getElementById('id').value = res.dados.id;
                    document.getElementById('nome_publico').value = res.dados.nome_publico;
                    document.getElementById('btnCancelar').style.display = 'inline';
                    document.getElementById('btnSalvar').innerText = 'Atualizar';
                } else {
                    alert(res.mensagem);
                }
            });
        }

        // Função para Deletar um Registro
        function deletar(id) {
            if(confirm("Tem certeza que deseja remover este público alvo?")) {
                fetch(`${urlController}?acao=deletar&id=${id}`)
                .then(res => res.json())
                .then(res => {
                    alert(res.mensagem);
                    if(res.sucesso) listar();
                });
            }
        }

        // Reseta o estado do formulário de volta para o modo de criação
        function resetarFormulario() {
            document.getElementById('id').value = '';
            document.getElementById('nome_publico').value = '';
            document.getElementById('btnCancelar').style.display = 'none';
            document.getElementById('btnSalvar').innerText = 'Salvar';
        }
    </script>
</body>
</html>