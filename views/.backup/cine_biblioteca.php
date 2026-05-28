<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cine Biblioteca</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 14px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #f4f4f4; }
        form { background: #f9f9f9; padding: 15px; border: 1px solid #ddd; margin-bottom: 20px; border-radius: 5px; }
        .filtro-container { background: #eef2f7; padding: 15px; border: 1px solid #b4c6e7; margin-bottom: 20px; border-radius: 5px; display: flex; gap: 15px; align-items: flex-end;}
        .form-group { margin-bottom: 10px; }
        .filtro-group { display: flex; flex-direction: column; font-size: 13px; font-weight: bold; }
        label { display: inline-block; width: 180px; font-weight: bold; }
        input[type="text"], textarea, select, input[type="date"] { width: 450px; padding: 5px; }
        .filtro-container input, .filtro-container select { width: 180px; }
        textarea { height: 60px; }
    </style>
</head>
<body>

    <h2>Gerenciar Filmes / Curtas (Cine Biblioteca)</h2>

    <form id="formCine">
        <input type="hidden" id="id" name="id">
        <div class="form-group">
            <label for="id_registro_atividade">Registro de Atividade *</label>
            <select id="id_registro_atividade" name="id_registro_atividade" required><option value="">Carregando...</option></select>
        </div>
        <div class="form-group">
            <label for="titulo_curta">Título do Curta-Metragem *</label>
            <input type="text" id="titulo_curta" name="titulo_curta" required>
        </div>
        <div class="form-group">
            <label for="link">Link da Mídia / Vídeo</label>
            <input type="text" id="link" name="link">
        </div>
        <div class="form-group">
            <label for="detalhes_controle">Detalhes de Controle</label>
            <textarea id="detalhes_controle" name="detalhes_controle"></textarea>
        </div>
        <button type="submit" id="btnSalvar">Salvar Registro</button>
        <button type="button" id="btnCancelar" style="display:none;" onclick="resetarFormulario()">Cancelar Edição</button>
    </form>

    <div class="filtro-container">
        <div class="filtro-group">
            <label for="f_data_execucao">Data Execução:</label>
            <input type="date" id="f_data_execucao">
        </div>
        <div class="filtro-group">
            <label for="f_data_finalizacao">Data Finalização:</label>
            <input type="date" id="f_data_finalizacao">
        </div>
        <div class="filtro-group">
            <label for="f_status">Status:</label>
            <select id="f_status">
                <option value="">Todos</option>
                <option value="Planejado">Planejado</option>
                <option value="Concluído">Concluído</option>
                <option value="Cancelado">Cancelado</option>
            </select>
        </div>
        <button onclick="aplicarFiltros()" style="padding: 6px 15px; background: #007bff; color: white; border: none; border-radius: 3px; cursor: pointer;">Filtrar</button>
        <button onclick="limparFiltros()" style="padding: 6px 15px; background: #6c757d; color: white; border: none; border-radius: 3px; cursor: pointer;">Limpar</button>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Título do Curta</th>
                <th>Projeto / Atividade Executada</th>
                <th>Status Reg.</th>
                <th>Link</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody id="tabelaCorpo"></tbody>
    </table>

    <script>
        const urlController = '../services/cinebiblioteca_controle.php';

        document.addEventListener("DOMContentLoaded", () => {
            carregarAtividades();
            listar();
        });

        // Captura os valores digitados na barra de filtros e monta a query string
        function obterQueryStringFiltros() {
            const de = document.getElementById('f_data_execucao').value;
            const df = document.getElementById('f_data_finalizacao').value;
            const status = document.getElementById('f_status').value;

            return `&f_data_execucao=${de}&f_data_finalizacao=${df}&f_status=${status}`;
        }

        function aplicarFiltros() {
            carregarAtividades(); // Atualiza o select baseado nos filtros escolhidos
            listar();             // Atualiza a tabela baseada nos filtros escolhidos
        }

        function limparFiltros() {
            document.getElementById('f_data_execucao').value = '';
            document.getElementById('f_data_finalizacao').value = '';
            document.getElementById('f_status').value = '';
            aplicarFiltros();
        }

        // Carrega as atividades executadas (Agora aceita filtros na requisição)
        function carregarAtividades() {
            fetch(`${urlController}?acao=auxiliares${obterQueryStringFiltros()}`)
            .then(res => res.json())
            .then(res => {
                if(res.sucesso) {
                    const select = document.getElementById('id_registro_atividade');
                    // Guarda o ID selecionado atualmente para não perdê-lo ao filtrar
                    const valorAtual = select.value;

                    select.innerHTML = '<option value="">Selecione uma execução...</option>';
                    res.dados.registros_atividades.forEach(item => {
                        const infoContexto = `${item.nome_projeto} (${item.tema_especifico || 'Sem tema'}) - ${item.data_execucao} [${item.status}]`;
                        select.innerHTML += `<option value="${item.id}">${infoContexto}</option>`;
                    });

                    // Restaura o valor se ele ainda existir na lista filtrada
                    select.value = valorAtual;
                }
            });
        }

        // Listar Registros Cadastrados (Agora aceita filtros na requisição)
        function listar() {
            fetch(`${urlController}?acao=listar${obterQueryStringFiltros()}`)
            .then(res => res.json())
            .then(res => {
                const corpo = document.getElementById('tabelaCorpo');
                corpo.innerHTML = '';
                if(res.sucesso) {
                    res.dados.forEach(item => {
                        const linkHtml = item.link ? `<a href="${item.link}" target="_blank">Acessar Link</a>` : '<i>Nenhum</i>';
                        const contexto = `<b>${item.nome_projeto}</b><br><small>${item.tema_especifico || 'Sem tema'} (${item.data_execucao})</small>`;
                        
                        corpo.innerHTML += `
                            <tr>
                                <td>${item.id}</td>
                                <td><b>${item.titulo_curta}</b></td>
                                <td>${contexto}</td>
                                <td><span class="badge">${item.status}</span></td>
                                <td>${linkHtml}</td>
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

        // Envio do formulário (Mantém igual)
        document.getElementById('formCine').addEventListener('submit', function(e) {
            e.preventDefault();
            const id = document.getElementById('id').value;
            const acao = id ? 'atualizar' : 'criar';
            const formData = new FormData(this);

            fetch(`${urlController}?acao=${acao}`, { method: 'POST', body: formData })
            .then(res => res.json())
            .then(res => {
                alert(res.mensagem);
                if(res.sucesso) { resetarFormulario(); aplicarFiltros(); }
            });
        });

        function editar(id) {
            fetch(`${urlController}?acao=buscar&id=${id}`)
            .then(res => res.json())
            .then(res => {
                if(res.sucesso) {
                    document.getElementById('id').value = res.dados.id;
                    document.getElementById('id_registro_atividade').value = res.dados.id_registro_atividade;
                    document.getElementById('titulo_curta').value = res.dados.titulo_curta;
                    document.getElementById('link').value = res.dados.link || '';
                    document.getElementById('detalhes_controle').value = res.dados.detalhes_controle || '';
                    
                    document.getElementById('btnCancelar').style.display = 'inline';
                    document.getElementById('btnSalvar').innerText = 'Atualizar Registro';
                }
            });
        }

        function deletar(id) {
            if(confirm("Deseja realmente remover esta mídia?")) {
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
            document.getElementById('formCine').reset();
            document.getElementById('btnCancelar').style.display = 'none';
            document.getElementById('btnSalvar').innerText = 'Salvar Registro';
        }
    </script>
</body>
</html>