<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Eixos</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; }
        table { width: 70%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background: #f4f4f4; }
        form { margin-bottom: 20px; }
        .form-group { margin-bottom: 10px; }
        label { display: inline-block; width: 120px; vertical-align: top; }
        textarea { width: 300px; height: 60px; }
    </style>
</head>
<body>

    <h2>Gerenciar Eixos do Projeto</h2>

    <form id="formEixo">
        <input type="hidden" id="id" name="id">
        
        <div class="form-group">
            <label for="nome_eixo">Nome do Eixo:</label>
            <input type="text" id="nome_eixo" name="nome_eixo" required placeholder="Ex: Audiovisual, Tecnologia...">
        </div>

        <div class="form-group">
            <label for="observacao">Observação:</label>
            <textarea id="observacao" name="observacao" placeholder="Notas adicionais sobre o eixo (opcional)"></textarea>
        </div>

        <button type="submit" id="btnSalvar">Salvar</button>
        <button type="button" id="btnCancelar" style="display:none;" onclick="resetarFormulario()">Cancelar Edição</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome do Eixo</th>
                <th>Observações</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody id="tabelaCorpo"></tbody>
    </table>

    <script>
        const urlController = '../services/eixo_controle.php';

        document.addEventListener("DOMContentLoaded", listar);

        // Dispara ao salvar o formulário
        document.getElementById('formEixo').addEventListener('submit', function(e) {
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

        // Buscar todos os registros
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
                                <td>${item.nome_eixo}</td>
                                <td>${item.observacao ? item.observacao : '<i>Nenhuma observação</i>'}</td>
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

        // Seleciona um eixo para edição e joga para o formulário
        function editar(id) {
            fetch(`${urlController}?acao=buscar&id=${id}`)
            .then(res => res.json())
            .then(res => {
                if(res.sucesso) {
                    document.getElementById('id').value = res.dados.id;
                    document.getElementById('nome_eixo').value = res.dados.nome_eixo;
                    document.getElementById('observacao').value = res.dados.observacao ? res.dados.observacao : '';
                    document.getElementById('btnCancelar').style.display = 'inline';
                    document.getElementById('btnSalvar').innerText = 'Atualizar';
                } else {
                    alert(res.mensagem);
                }
            });
        }

        // Remove um eixo do sistema
        function deletar(id) {
            if(confirm("Tem certeza que deseja apagar este eixo?")) {
                fetch(`${urlController}?acao=deletar&id=${id}`)
                .then(res => res.json())
                .then(res => {
                    alert(res.mensagem);
                    if(res.sucesso) listar();
                });
            }
        }

        // Limpa campos e volta botão para estado original
        function resetarFormulario() {
            document.getElementById('id').value = '';
            document.getElementById('nome_eixo').value = '';
            document.getElementById('observacao').value = '';
            document.getElementById('btnCancelar').style.display = 'none';
            document.getElementById('btnSalvar').innerText = 'Salvar';
        }
    </script>
</body>
</html>