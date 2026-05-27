<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel de Eventos da Biblioteca</title>
    <style>
        body { font-family: Segoe UI, sans-serif; margin: 20px; background: #f8f9fa; }
        .grid-eventos { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; margin-top: 20px; }
        .card { background: white; border: 1px solid #ddd; border-radius: 8px; padding: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .card img { width: 100%; height: 150px; object-fit: cover; border-radius: 4px; }
        .badge { display: inline-block; padding: 4px 8px; font-size: 11px; font-weight: bold; border-radius: 4px; background: #e3f2fd; color: #0d47a1; }
        .badge-privado { background: #ffebee; color: #c62828; }
        .filtro-barra { background: #fff; padding: 15px; border-radius: 8px; border: 1px solid #ddd; display: flex; gap: 15px; align-items: flex-end; margin-bottom: 25px; }
        .modal { display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; border: 1px solid #000; padding: 20px; z-index: 100; max-height: 80% ; overflow-y: auto; width: 500px; box-shadow: 0 4px 10px rgba(0,0,0,0.3); }
        .modal-overlay { display: none; position: fixed; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.5); z-index: 99;}
    </style>
</head>
<body>

    <div style="background: #333; color: #fff; padding: 10px; margin-bottom: 20px; border-radius: 4px;">
        <label>Visualizar como Perfil: </label>
        <select id="perfilSimulado" onchange="alternarVisualizacaoPerfil()">
            <option value="Visitante">Público Geral (Deslogado)</option>
            <option value="Comum">Usuário Comum (Aluno/Professor Logado)</option>
            <option value="Administrador">Administrador / Bibliotecário</option>
        </select>
    </div>

    <h2>Eventos e Atividades Culturais</h2>

    <div class="filtro-barra">
        <div>
            <label style="display:block; font-size:12px;">Data Execução</label>
            <input type="date" id="f_data_execucao">
        </div>
        <div>
            <label style="display:block; font-size:12px;">Data Finalização</label>
            <input type="date" id="f_data_finalizacao">
        </div>
        <div>
            <label style="display:block; font-size:12px;">Status</label>
            <select id="f_status">
                <option value="">Todos</option>
                <option value="Planejado">Planejado</option>
                <option value="Concluído">Concluído</option>
                <option value="Cancelado">Cancelado</option>
            </select>
        </div>
        <button onclick="carregarDadosDoPainel()" style="background: #007bff; color: white; border: none; padding: 6px 12px; cursor: pointer; border-radius: 4px;">Filtrar</button>
    </div>

    <div id="subTituloSessao"></div>
    <div class="grid-eventos" id="containerEventos"></div>

    <div class="modal-overlay" id="overlay" onclick="fecharModal()"></div>
    <div class="modal" id="modalInscritos">
        <h3>Gerenciamento de Inscritos</h3>
        <div id="estatisticasEvento" style="font-weight: bold; margin-bottom: 10px; color: #555;"></div>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background:#eee;">
                    <th style="padding:5px; text-align:left;">Nome/Matrícula</th>
                    <th style="padding:5px; text-align:left;">Tipo</th>
                    <th style="padding:5px; text-align:left;">Status</th>
                </tr>
            </thead>
            <tbody id="listaInscritosCorpo"></tbody>
        </table>
        <br><button onclick="fecharModal()">Fechar</button>
    </div>

    <script>
        const controller = '../services/registro_atividade_controle.php';

        document.addEventListener("DOMContentLoaded", carregarDadosDoPainel);

        function alternarVisualizacaoPerfil() {
            carregarDadosDoPainel();
        }

        function obterQueryFiltros() {
            return `&f_data_execucao=${document.getElementById('f_data_execucao').value}&f_data_finalizacao=${document.getElementById('f_data_finalizacao').value}&f_status=${document.getElementById('f_status').value}`;
        }

        function carregarDadosDoPainel() {
            const perfil = document.getElementById('perfilSimulado').value;
            let rota = 'listar_publico';

            if (perfil === 'Comum') rota = 'listar_disponiveis_comum';
            if (perfil === 'Administrador') rota = 'listar_admin';

            document.getElementById('subTituloSessao').innerText = `Visualizando listagem ativa para: ${perfil}`;

            fetch(`${controller}?acao=${rota}${obterQueryFiltros()}`)
            .then(res => res.json())
            .then(res => {
                const container = document.getElementById('containerEventos');
                container.innerHTML = '';

                if (res.sucesso && res.dados.length > 0) {
                    res.dados.forEach(evento => {
                        let acaoBotao = '';

                        // Tratamento de botões de acordo com as regras de negócio de cada perfil
                        if (perfil === 'Visitante') {
                            acaoBotao = `<button onclick="redirecionarLogin()" style="width:100%; margin-top:10px;">Quero me inscrever (Faça Login)</button>`;
                        } else if (perfil === 'Comum') {
                            acaoBotao = `
                                <div style="margin-top:10px; display:flex; gap:5px;">
                                    <button onclick="inscrever(${evento.id}, 'Confirmado')" style="flex:1; background:#28a745; color:white; border:none; padding:5px; cursor:pointer;">Inscrever (Confirmado)</button>
                                    <button onclick="inscrever(${evento.id}, 'Pensando')" style="flex:1; background:#ffc107; border:none; padding:5px; cursor:pointer;">Marcar Pensando</button>
                                </div>
                            `;
                        } else if (perfil === 'Administrador') {
                            acaoBotao = `
                                <div style="margin-top:10px; background:#f1f3f5; padding:5px; border-radius:4px; text-align:center; cursor:pointer;" onclick="abrirGerenciadorInscritos(${evento.id}, ${evento.capacidade_maxima})">
                                    <span style="color:#007bff; font-weight:bold;">Total Inscritos: ${parseInt(evento.total_confirmados) + parseInt(evento.total_pensando)}</span><br>
                                    <small style="color:green;">✔ Confirmados: ${evento.total_confirmados} / 🗪 Pensando: ${evento.total_pensando}</small>
                                </div>
                            `;
                        }

                        const badgePublico = evento.eh_publico == 1 ? '<span class="badge">Aberto ao Público</span>' : '<span class="badge badge-privado">Restrito Interno (Matrícula Obrigatória)</span>';

                        container.innerHTML += `
                            <div class="card">
                                <img src="${evento.imagem_exibicao || 'https://placehold.co/300x150?text=Biblioteca'}" alt="Imagem do evento">
                                <h4 style="margin:10px 0 5px 0;">${evento.nome_projeto}</h4>
                                <p style="font-size:12px; color:#666; margin:0 0 10px 0;">📍 Espaço: ${evento.nome_espaco} (Capac. ${evento.capacidade_maxima})</p>
                                <p style="font-size:13px; margin:5px 0;"><b>Tema:</b> ${evento.tema_especifico || 'Geral'}</p>
                                <p style="font-size:13px; margin:5px 0;"><b>Data:</b> ${evento.data_execucao} [${evento.status}]</p>
                                ${badgePublico}
                                ${acaoBotao}
                            </div>
                        `;
                    });
                } else {
                    container.innerHTML = '<p>Nenhum evento registrado com esses critérios para o seu perfil.</p>';
                }
            });
        }

        function redirecionarLogin() {
            alert("Redirecionando para a tela de login...");
            window.location.href = "/techlounged/views/login.php";
        }

        // Realiza a inscrição aplicando todas as regras dinâmicas do Backend
        function inscrever(idRegistro, tipo) {
            const formData = new FormData();
            formData.append('id_registro_atividade', idRegistro);
            formData.append('tipo_inscricao', tipo);

            fetch(`${controller}?acao=inscrever`, { method: 'POST', body: formData })
            .then(res => res.json())
            .then(res => {
                alert(res.mensagem);
                carregarDadosDoPainel();
            });
        }

        // Abre modal administrativo para gerenciar inscrições
        function abrirGerenciadorInscritos(idRegistro, capacidade) {
            document.getElementById('modalInscritos').style.display = 'block';
            document.getElementById('overlay').style.display = 'block';

            fetch(`${controller}?acao=listar_inscritos_evento&id_registro=${idRegistro}`)
            .then(res => res.json())
            .then(res => {
                const corpo = document.getElementById('listaInscritosCorpo');
                corpo.innerHTML = '';

                document.getElementById('estatisticasEvento').innerText = `Capacidade Máxima do Local: ${capacidade} vagas`;

                if(res.sucesso && res.dados.length > 0) {
                    res.dados.forEach(insc => {
                        corpo.innerHTML += `
                            <tr>
                                <td style="padding:5px; border-bottom:1px solid #ddd;">${insc.nome} (<small>${insc.matricula || 'Sem Matrícula'}</small>)</td>
                                <td style="padding:5px; border-bottom:1px solid #ddd;">${insc.tipo_inscricao}</td>
                                <td style="padding:5px; border-bottom:1px solid #ddd;">
                                    <select onchange="mudarStatusInscricaoAdmin(${insc.id}, this.value)">
                                        <option value="Pendente" ${insc.status_inscricao === 'Pendente'?'selected':''}>Pendente</option>
                                        <option value="Confirmado" ${insc.status_inscricao === 'Confirmado'?'selected':''}>Confirmado</option>
                                        <option value="Recusada" ${insc.status_inscricao === 'Recusada'?'selected':''}>Recusada</option>
                                    </select>
                                </td>
                            </tr>
                        `;
                    });
                } else {
                    corpo.innerHTML = '<tr><td colspan="3">Ninguém inscrito neste evento ainda.</td></tr>';
                }
            });
        }

        function mudarStatusInscricaoAdmin(idInscricao, novoStatus) {
            const formData = new FormData();
            formData.append('id_inscricao', idInscricao);
            formData.append('status_inscricao', novoStatus);

            fetch(`${controller}?acao=modificar_status_admin`, { method: 'POST', body: formData })
            .then(res => res.json())
            .then(res => {
                alert(res.mensagem);
                carregarDadosDoPainel();
            });
        }

        function fecharModal() {
            document.getElementById('modalInscritos').style.display = 'none';
            document.getElementById('overlay').style.display = 'none';
        }
    </script>
</body>
</html>