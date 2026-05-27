<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Espaços</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; }
        table { width: 60%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background: #f4f4f4; }
        form { margin-bottom: 20px; }
        .form-group { margin-bottom: 10px; }
        label { display: inline-block; width: 140px; }
    </style>
</head>
<body>

    <h2>Gerenciar Espaços</h2>

    <form id="formEspaco">
        <input type="hidden" id="id" name="id">
        
        <div class="form-group">
            <label for="nome_espaco">Nome do Espaço:</label>
            <input type="text" id="nome_espaco" name="nome_espaco" required placeholder="Ex: Laboratório A, Auditório...">
        </div>

        <div class="form-group">
            <label for="capacidade_maxima">Capacidade Máxima:</label>
            <input type="number" id="capacidade_maxima" name="capacidade_maxima" min="1" required placeholder="Ex: 30">
        </div>

        <button type="submit" id="btnSalvar">Salvar</button>
        <button type="button" id="btnCancelar" style="display:none;" onclick="resetarFormulario()">Cancelar Edição</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome do Espaço</th>
                <th>Capacidade Máxima</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody id="tabelaCorpo"></tbody>
    </table>

    <script>
        const urlController = '../services/espaco_controle.php';

        document.addEventListener("DOMContentLoaded", listar);

        // Intercepta o envio do formulário
        document.getElementById('formEspaco').addEventListener('submit', function(e) {
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

        // Listar Espaços
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
                                <td>${item.nome_espaco}</td>
                                <td>${item.capacidade_maxima} pessoas</td>
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

        // Buscar dados do Espaço para Edição
        function editar(id) {
            fetch(`${urlController}?acao=buscar&id=${id}`)
            .then(res => res.json())
            .then(res => {
                if(res.sucesso) {
                    document.getElementById('id').value = res.dados.id;
                    document.getElementById('nome_espaco').value = res.dados.nome_espaco;
                    document.getElementById('capacidade_maxima').value = res.dados.capacidade_maxima;
                    document.getElementById('btnCancelar').style.display = 'inline';
                    document.getElementById('btnSalvar').innerText = 'Atualizar';
                } else {
                    alert(res.mensagem);
                }
            });
        }

        // Excluir Espaço
        function deletar(id) {
            if(confirm("Deseja realmente remover este espaço?")) {
                fetch(`${urlController}?acao=deletar&id=${id}`)
                .then(res => res.json())
                .then(res => {
                    alert(res.mensagem);
                    if(res.sucesso) listar();
                });
            }
        }

        // Limpa o formulário e altera o estado do botão para o padrão
        function resetarFormulario() {
            document.getElementById('id').value = '';
            document.getElementById('nome_espaco').value = '';
            document.getElementById('capacidade_maxima').value = '';
            document.getElementById('btnCancelar').style.display = 'none';
            document.getElementById('btnSalvar').innerText = 'Salvar';
        }
    </script>
</body>
</html>