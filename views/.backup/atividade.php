<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Atividades</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 14px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #f4f4f4; }
        form { background: #f9f9f9; padding: 15px; border: 1px solid #ddd; margin-bottom: 20px; border-radius: 5px; }
        .form-group { margin-bottom: 10px; }
        label { display: inline-block; width: 150px; font-weight: bold; }
        input[type="text"], textarea, select { width: 400px; padding: 5px; }
        textarea { height: 50px; }
    </style>
</head>
<body>

    <h2>Gerenciar Projetos / Atividades</h2>

    <form id="formAtividade">
        <input type="hidden" id="id" name="id">
        
        <div class="form-group">
            <label for="nome_projeto">Nome do Projeto *</label>
            <input type="text" id="nome_projeto" name="nome_projeto" required>
        </div>

        <div class="form-group">
            <label for="id_eixo">Eixo Tecnológico *</label>
            <select id="id_eixo" name="id_eixo" required><option value="">Carregando...</option></select>
        </div>

        <div class="form-group">
            <label for="id_periodicidade">Periodicidade *</label>
            <select id="id_periodicidade" name="id_periodicidade" required><option value="">Carregando...</option></select>
        </div>

        <div class="form-group">
            <label for="id_publico_alvo">Público Alvo *</label>
            <select id="id_publico_alvo" name="id_publico_alvo" required><option value="">Carregando...</option></select>
        </div>

        <div class="form-group">
            <label for="objetivo">Objetivo *</label>
            <textarea id="objetivo" name="objetivo" required></textarea>
        </div>

        <div class="form-group">
            <label for="observacoes_gerais">Observações</label>
            <textarea id="observacoes_gerais" name="observacoes_gerais"></textarea>
        </div>

        <div class="form-group">
            <label for="url_imagem">URL da Imagem</label>
            <input type="text" id="url_imagem" name="url_imagem" placeholder="http://...">
        </div>

        <div class="form-group">
            <label for="eh_publico">Visível ao Público?</label>
            <input type="checkbox" id="eh_publico" name="eh_publico" value="1" checked>
        </div>

        <button type="submit" id="btnSalvar">Salvar Projeto</button>
        <button type="button" id="btnCancelar" style="display:none;" onclick="resetarFormulario()">Cancelar Edição</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Projeto</th>
                <th>Eixo</th>
                <th>Periodicidade</th>
                <th>Público Alvo</th>
                <th>Público?</th>
                <th>Modificado por</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody id="tabelaCorpo"></tbody>
    </table>

    <script>
        const urlController = '../services/atividade_controle.php';

        document.addEventListener("DOMContentLoaded", () => {
            carregarSelectsAuxiliares();
            listar();
        });

        // Preenche as tags <select> trazendo as opções dinâmicas do banco
        function carregarSelectsAuxiliares() {
            fetch(`${urlController}?acao=auxiliares`)
            .then(res => res.json())
            .then(res => {
                if(res.sucesso) {
                    populateSelect('id_eixo', res.dados.eixos, 'id', 'nome_eixo');
                    populateSelect('id_periodicidade', res.dados.periodicidades, 'id', 'descricao');
                    populateSelect('id_publico_alvo', res.dados.publicos_alvo, 'id', 'nome_publico');
                }
            });
        }

        function populateSelect(elementId, array, valueKey, textKey) {
            const select = document.getElementById(elementId);
            select.innerHTML = '<option value="">Selecione...</option>';
            array.forEach(item => {
                select.innerHTML += `<option value="${item[valueKey]}">${item[textKey]}</option>`;
            });
        }

        // Submit (Criar ou Atualizar)
        document.getElementById('formAtividade').addEventListener('submit', function(e) {
            e.preventDefault();
            const id = document.getElementById('id').value;
            const acao = id ? 'atualizar' : 'criar';
            const formData = new FormData(this);

            fetch(`${urlController}?acao=${acao}`, { method: 'POST', body: formData })
            .then(res => res.json())
            .then(res => {
                alert(res.mensagem);
                if(res.sucesso) { resetarFormulario(); listar(); }
            });
        });

        // Listagem
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
                                <td><b>${item.nome_projeto}</b></td>
                                <td>${item.nome_eixo}</td>
                                <td>${item.nome_periodicidade}</td>
                                <td>${item.nome_publico}</td>
                                <td>${item.eh_publico == 1 ? 'Sim' : 'Não'}</td>
                                <td>${item.nome_editor}</td>
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

        // Editar
        function editar(id) {
            fetch(`${urlController}?acao=buscar&id=${id}`)
            .then(res => res.json())
            .then(res => {
                if(res.sucesso) {
                    document.getElementById('id').value = res.dados.id;
                    document.getElementById('nome_projeto').value = res.dados.nome_projeto;
                    document.getElementById('id_eixo').value = res.dados.id_eixo;
                    document.getElementById('id_periodicidade').value = res.dados.id_periodicidade;
                    document.getElementById('id_publico_alvo').value = res.dados.id_publico_alvo;
                    document.getElementById('objetivo').value = res.dados.objetivo;
                    document.getElementById('observacoes_gerais').value = res.dados.observacoes_gerais || '';
                    document.getElementById('url_imagem').value = res.dados.url_imagem || '';
                    document.getElementById('eh_publico').checked = res.dados.eh_publico == 1;
                    
                    document.getElementById('btnCancelar').style.display = 'inline';
                    document.getElementById('btnSalvar').innerText = 'Atualizar Projeto';
                }
            });
        }

        // Deletar
        function deletar(id) {
            if(confirm("Tem certeza que deseja remover esta atividade?")) {
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
            document.getElementById('formAtividade').reset();
            document.getElementById('btnCancelar').style.display = 'none';
            document.getElementById('btnSalvar').innerText = 'Salvar Projeto';
        }
    </script>
</body>
</html>