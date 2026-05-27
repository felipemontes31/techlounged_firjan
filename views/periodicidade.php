<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Periodicidades</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; }
        table { width: 50%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background: #f4f4f4; }
        form { margin-bottom: 20px; }
    </style>
</head>
<body>

    <h2>Gerenciar Periodicidades</h2>

    <form id="formPeriodicidade">
        <input type="hidden" id="id" name="id">
        <label for="descricao">Descrição:</label>
        <input type="text" id="descricao" name="descricao" required>
        <button type="submit" id="btnSalvar">Salvar</button>
        <button type="button" id="btnCancelar" style="display:none;" onclick="resetarFormulario()">Cancelar Edição</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Descrição</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody id="tabelaCorpo"></tbody>
    </table>

    <script>
        const urlController = '../services/periodicidade_controle.php';

        // Carrega os dados ao abrir a página
        document.addEventListener("DOMContentLoaded", listar);

        // Evento de Submit do Formulário
        document.getElementById('formPeriodicidade').addEventListener('submit', function(e) {
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

        // Função para Listar
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
                                <td>${item.descricao}</td>
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

        // Prepara o formulário para edição
        function editar(id) {
            fetch(`${urlController}?acao=buscar&id=${id}`)
            .then(res => res.json())
            .then(res => {
                if(res.sucesso) {
                    document.getElementById('id').value = res.dados.id;
                    document.getElementById('descricao').value = res.dados.descricao;
                    document.getElementById('btnCancelar').style.display = 'inline';
                    document.getElementById('btnSalvar').innerText = 'Atualizar';
                } else {
                    alert(res.mensagem);
                }
            });
        }

        // Função para Deletar
        function deletar(id) {
            if(confirm("Tem certeza que deseja excluir esta periodicidade?")) {
                fetch(`${urlController}?acao=deletar&id=${id}`)
                .then(res => res.json())
                .then(res => {
                    alert(res.mensagem);
                    if(res.sucesso) listar();
                });
            }
        }

        function resetarFormulario() {
            document.getElementById('id').value = '';
            document.getElementById('descricao').value = '';
            document.getElementById('btnCancelar').style.display = 'none';
            document.getElementById('btnSalvar').innerText = 'Salvar';
        }
    </script>
</body>
</html>