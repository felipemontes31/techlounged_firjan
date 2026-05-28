<?php

require_once("../config/app.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Captura a função/perfil real do usuário logado na sessão. 
// Se não houver ninguém logado, assume 'Visitante'.
$perfilReal = $_SESSION['usuario']['id_funcao'] ?? $_SESSION['usuario']['funcao'] ?? 'Visitante';

// Mapeia caso seu sistema salve como ID ou texto (Ex: se id_funcao for 1 = Administrador, etc.)
// Se na sua sessão você já guarda o nome da função ('Administrador', 'Bibliotecário'), use direto:
if (in_array($perfilReal, ['Administrador', 'Bibliotecário'])) {
    $perfilJS = 'Administrador';
} elseif ($perfilReal !== 'Visitante') {
    $perfilJS = 'Comum';
} else {
    $perfilJS = 'Visitante';
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel de Eventos da Biblioteca</title>

<style>
    body {
        font-family: Segoe UI, sans-serif;
        margin: 20px;
        background: #F4F5F7;
        color: #004B87;
    }

    .grid-eventos {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }

    .card {
        background: #FFFFFF;
        border: 1px solid #E2EBF4;
        border-radius: 10px;
        padding: 15px;
        box-shadow: 0 3px 8px rgba(0, 75, 135, 0.08);
        position: relative;
        transition: 0.2s ease-in-out;
    }

    .card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 14px rgba(0, 75, 135, 0.15);
    }

    .card img {
        width: 100%;
        height: 150px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid #E2EBF4;
    }

    .badge {
        display: inline-block;
        padding: 4px 8px;
        font-size: 11px;
        font-weight: bold;
        border-radius: 4px;
        background: #E2EBF4;
        color: #004B87;
    }

    .badge-privado {
        background: #708090;
        color: #FFFFFF;
    }

    .filtro-barra {
        background: #FFFFFF;
        padding: 15px;
        border-radius: 10px;
        border: 1px solid #E2EBF4;
        display: flex;
        gap: 15px;
        align-items: flex-end;
        margin-bottom: 25px;
        box-shadow: 0 2px 6px rgba(0, 75, 135, 0.05);
    }

    /* Estilos dos Modais */
    .modal {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: #FFFFFF;
        border: 1px solid #E2EBF4;
        padding: 20px;
        z-index: 100;
        max-height: 85vh;
        overflow-y: auto;
        width: 500px;
        box-shadow: 0 6px 18px rgba(0, 75, 135, 0.2);
        border-radius: 10px;
    }

    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 75, 135, 0.45);
        z-index: 99;
    }

    /* Formulários dentro do modal */
    .form-group {
        margin-bottom: 12px;
    }

    .form-group label {
        display: block;
        font-weight: bold;
        font-size: 13px;
        margin-bottom: 4px;
        color: #004B87;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 8px;
        box-sizing: border-box;
        border: 1px solid #708090;
        border-radius: 6px;
        background: #F4F5F7;
        transition: border 0.2s ease;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        border-color: #0072CE;
        outline: none;
        background: #FFFFFF;
    }

    .btn-principal {
        background: #0072CE;
        color: white;
        border: none;
        padding: 8px 16px;
        cursor: pointer;
        border-radius: 6px;
        font-weight: bold;
        transition: 0.2s ease;
    }

    .btn-principal:hover {
        background: #004B87;
    }

    .btn-sucesso {
        background: #004B87;
        color: white;
        border: none;
        padding: 8px 16px;
        cursor: pointer;
        border-radius: 6px;
        font-weight: bold;
        transition: 0.2s ease;
    }

    .btn-sucesso:hover {
        background: #0072CE;
    }
</style>
```

</head>
<body>

    <div style="background: #e9ecef; padding: 15px; border-radius: 8px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
        <h2>Eventos e Atividades Culturais</h2>
        
        <?php if ($perfilJS === 'Administrador'): ?>
            <button class="btn-sucesso" onclick="abrirModalCadastro()">+ Criar Novo Registro</button>
        <?php endif; ?>
        <button class="btn-sucesso" onclick="fazerLogout()">Sair</button>
    </div>

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
        <button onclick="carregarDadosDoPainel()" class="btn-principal">Filtrar</button>
    </div>

    <div id="subTituloSessao" style="font-weight: bold; color: #555;"></div>
    <div class="grid-eventos" id="containerEventos"></div>


    <div class="modal-overlay" id="overlay" onclick="fecharModais()"></div>
    
    <div class="modal" id="modalRegistroAtividade">
        <h3 id="modalTitulo">Criar Registro de Atividade</h3>
        <form id="formRegistro">
            <input type="hidden" id="id" name="id"> <div class="form-group">
                <label for="id_atividade">Atividade (Matriz do Projeto) *</label>
                <select id="id_atividade" name="id_atividade" required><option value="">Carregando...</option></select>
            </div>

            <div class="form-group">
                <label for="id_espaco">Espaço Físico / Local *</label>
                <select id="id_espaco" name="id_espaco" required><option value="">Carregando...</option></select>
            </div>

            <div class="form-group">
                <label for="tema_especifico">Tema Específico da Execução</label>
                <input type="text" id="tema_especifico" name="tema_especifico" placeholder="Ex: Oficina de Robótica Básica">
            </div>

            <div class="form-group">
                <label for="data_execucao">Data de Execução *</label>
                <input type="date" id="data_execucao" name="data_execucao" required>
            </div>

            <div class="form-group">
                <label for="data_finalizacao">Data de Finalização</label>
                <input type="date" id="data_finalizacao" name="data_finalizacao">
            </div>

            <div class="form-group">
                <label for="status">Status do Evento</label>
                <select id="status" name="status">
                    <option value="Planejado">Planejado</option>
                    <option value="Concluído">Concluído</option>
                    <option value="Cancelado">Cancelado</option>
                </select>
            </div>

            <div class="form-group">
                <label for="publico_previsto">Público Previsto (Vagas)</label>
                <input type="number" id="publico_previsto" name="publico_previsto" min="0">
            </div>

            <div class="form-group">
                <label for="publico_realizado">Público Realizado (Pós-evento)</label>
                <input type="number" id="publico_realizado" name="publico_realizado" min="0" value="0">
            </div>

            <div class="form-group">
                <label for="url_imagem">URL da Imagem Especial (Opcional)</label>
                <input type="text" id="url_imagem" name="url_imagem" placeholder="http://...">
            </div>

            <div class="form-group">
                <label style="display:inline-flex; align-items:center; font-weight:normal;">
                    <input type="checkbox" id="confirm_auto" name="confirm_auto" value="1" checked style="width:auto; margin-right:8px;">
                    Confirmar Inscrições Automaticamente se houver vagas?
                </label>
            </div>

            <button type="submit" class="btn-sucesso" id="btnSalvarRegistro">Salvar Registro</button>
            <button type="button" onclick="fecharModais()" style="padding:8px; border-radius:4px; border:1px solid #ccc; cursor:pointer;">Cancelar</button>
        </form>
    </div>


    <div class="modal" id="modalInscritos">
        <h3>Gerenciamento de Inscritos</h3>
        <div id="estatisticasEvento" style="font-weight: bold; margin-bottom: 10px; color: #555;"></div>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background:#eee;">
                    <th style="padding:5px; text-align:left; font-size:13px;">Nome/Matrícula</th>
                    <th style="padding:5px; text-align:left; font-size:13px;">Tipo</th>
                    <th style="padding:5px; text-align:left; font-size:13px;">Status</th>
                </tr>
            </thead>
            <tbody id="listaInscritosCorpo"></tbody>
        </table>
        <br><button onclick="fecharModais()" style="padding:6px 12px; cursor:pointer;">Fechar</button>
    </div>

    <script>
        const controllerPainel = '<?= BASE_URL ?>/services/registro_atividade_controle.php';

        // Altere para a rota correspondente da sua controller de CRUD de Atividade se for separada
        const urlCrudRegistro  = '<?= BASE_URL ?>/services/atividade_controle.php';

        // O PHP injeta o perfil real do usuário diretamente na constante do JavaScript
        const perfilUsuarioLogado = "<?php echo $perfilJS; ?>";

        document.addEventListener("DOMContentLoaded", () => {
            carregarDadosDoPainel();
            if (perfilUsuarioLogado === 'Administrador') {
                carregarSelectsAuxiliares();
            }
        });
        
        document.addEventListener("DOMContentLoaded", carregarDadosDoPainel);

        function obterQueryFiltros() {
            return `&f_data_execucao=${document.getElementById('f_data_execucao').value}&f_data_finalizacao=${document.getElementById('f_data_finalizacao').value}&f_status=${document.getElementById('f_status').value}`;
        }

        function carregarDadosDoPainel() {
            const perfil = perfilUsuarioLogado; 
            let rota = 'listar_publico';

            if (perfil === 'Comum') rota = 'listar_disponiveis_comum';
            if (perfil === 'Administrador') rota = 'listar_admin';

            document.getElementById('subTituloSessao').innerText = `Painel de visualização ativo para: ${perfil}`;

            fetch(`${controllerPainel}?acao=${rota}${obterQueryFiltros()}`)
            .then(res => res.json())
            .then(res => {
                const container = document.getElementById('containerEventos');
                container.innerHTML = '';

                if (res.sucesso && res.dados.length > 0) {
                    res.dados.forEach(evento => {
                        let acaoBotao = '';
                        let botaoEditarAdmin = '';

                        // Se for administrador, renderiza o botão flutuante de Edição no card
                        if (perfil === 'Administrador') {
                            botaoEditarAdmin = `
                                <button onclick="editarRegistro(${evento.id})" style="position:absolute; top:10px; right:10px; background:#ffc107; border:none; padding:4px 8px; font-size:11px; font-weight:bold; border-radius:4px; cursor:pointer; box-shadow:0 1px 3px rgba(0,0,0,0.2);">
                                    ✏ Editar Info
                                </button>
                            `;

                            acaoBotao = `
                                <div style="margin-top:10px; background:#f1f3f5; padding:8px; border-radius:4px; text-align:center; cursor:pointer; border: 1px solid #ced4da;" onclick="abrirGerenciadorInscritos(${evento.id}, ${evento.capacidade_maxima})">
                                    <span style="color:#007bff; font-weight:bold; font-size:13px;">Total Inscritos: ${parseInt(evento.total_confirmados) + parseInt(evento.total_pensando)}</span><br>
                                    <small style="color:green;">✔ Confirmados: ${evento.total_confirmados} / 🗪 Pensando: ${evento.total_pensando}</small>
                                </div>
                            `;
                        } else if (perfil === 'Comum') {
                            acaoBotao = `
                                <div style="margin-top:10px; display:flex; gap:5px;">
                                    <button onclick="inscrever(${evento.id}, 'Confirmado')" style="flex:1; background:#28a745; color:white; border:none; padding:7px; cursor:pointer; border-radius:4px; font-size:12px;">Inscrever (Confirmado)</button>
                                    <button onclick="inscrever(${evento.id}, 'Pensando')" style="flex:1; background:#ffc107; border:none; padding:7px; cursor:pointer; border-radius:4px; font-size:12px;">Marcar Pensando</button>
                                </div>
                            `;
                        } else if (perfil === 'Visitante') {
                            acaoBotao = `<button onclick="redirecionarLogin()" style="width:100%; margin-top:10px; background:#007bff; color:white; border:none; padding:8px; cursor:pointer; border-radius:4px;">Quero me inscrever (Faça Login)</button>`;
                        }

                        const badgePublico = evento.eh_publico == 1 ? '<span class="badge">Aberto ao Público</span>' : '<span class="badge badge-privado">Restrito Interno (Matrícula Obrigatória)</span>';

                        container.innerHTML += `
                            <div class="card">
                                ${botaoEditarAdmin}
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
                    container.innerHTML = '<p style="grid-column: 1/-1; text-align:center; color:#666;">Nenhum evento registrado para os critérios selecionados.</p>';
                }
            });
        }

        // Carrega e popula as opções de Atividades e Espaços no formulário
        function carregarSelectsAuxiliares() {
            // Aponta direto para a controller do painel usando a nova rota criada acima
            fetch(`${controllerPainel}?acao=carregar_selects_cadastro`)
            .then(res => res.json())
            .then(res => {
                if(res.sucesso) {
                    const selectAtiv = document.getElementById('id_atividade');
                    const selectEsp = document.getElementById('id_espaco');

                    // Preenche Atividades
                    selectAtiv.innerHTML = '<option value="">Selecione uma Atividade Matriz...</option>';
                    res.dados.atividades.forEach(ativ => {
                        selectAtiv.innerHTML += `<option value="${ativ.id}">${ativ.nome_projeto}</option>`;
                    });

                    // Preenche Espaços
                    selectEsp.innerHTML = '<option value="">Selecione o Local...</option>';
                    res.dados.espacos.forEach(esp => {
                        selectEsp.innerHTML += `<option value="${esp.id}">${esp.nome_espaco} (Máx: ${esp.capacidade_maxima})</option>`;
                    });
                }
            });
        }

        function abrirModalCadastro() {
            document.getElementById('formRegistro').reset();
            document.getElementById('id').value = '';
            document.getElementById('modalTitulo').innerText = "Criar Registro de Atividade";
            
            // Requisição rápida para alimentar Atividades e Espaços
            // Você pode criar um case 'auxiliares_registro' na sua RegistroEventosController retornando SELECT id, nome_projeto FROM atividade e SELECT id, nome_espaco FROM espaco
            
            document.getElementById('modalRegistroAtividade').style.display = 'block';
            document.getElementById('overlay').style.display = 'block';
        }

        // FORMULÁRIO SUBMIT (CRIAÇÃO OU ATUALIZAÇÃO)
        document.getElementById('formRegistro').addEventListener('submit', function(e) {
            e.preventDefault();
            const id = document.getElementById('id').value;
            // Se tem ID vai para atualizar, senão vai para criar
            const acao = id ? 'atualizar' : 'criar'; 
            
            const formData = new FormData(this);

            fetch(`${controllerPainel}?acao=${acao}`, { method: 'POST', body: formData })
            .then(res => res.json())
            .then(res => {
                alert(res.mensagem);
                if(res.sucesso) {
                    fecharModais();
                    carregarDadosDoPainel();
                }
            });
        });

        // BUSCA O REGISTRO E PREENCHE O FORMULÁRIO PARA EDIÇÃO
        function editarRegistro(idRegistro) {
            fetch(`${controllerPainel}?acao=buscar&id=${idRegistro}`)
            .then(res => res.json())
            .then(res => {
                if(res.sucesso) {
                    document.getElementById('modalTitulo').innerText = "Editar Registro de Atividade";
                    
                    document.getElementById('id').value = res.dados.id;
                    document.getElementById('id_atividade').value = res.dados.id_atividade;
                    document.getElementById('id_espaco').value = res.dados.id_espaco;
                    document.getElementById('tema_especifico').value = res.dados.tema_especifico || '';
                    document.getElementById('data_execucao').value = res.dados.data_execucao;
                    document.getElementById('data_finalizacao').value = res.dados.data_finalizacao || '';
                    document.getElementById('status').value = res.dados.status;
                    document.getElementById('publico_previsto').value = res.dados.publico_previsto || '';
                    document.getElementById('publico_realizado').value = res.dados.publico_realizado || 0;
                    document.getElementById('url_imagem').value = res.dados.url_imagem || '';
                    document.getElementById('confirm_auto').checked = res.dados.confirm_auto == 1;

                    document.getElementById('modalRegistroAtividade').style.display = 'block';
                    document.getElementById('overlay').style.display = 'block';
                } else {
                    alert("Erro ao buscar dados do registro.");
                }
            });
        }

        function inscrever(idRegistro, tipo) {
            const formData = new FormData();
            formData.append('id_registro_atividade', idRegistro);
            formData.append('tipo_inscricao', tipo);

            fetch(`${controllerPainel}?acao=inscrever`, { method: 'POST', body: formData })
            .then(res => res.json())
            .then(res => {
                alert(res.mensagem);
                carregarDadosDoPainel();
            });
        }

        function abrirGerenciadorInscritos(idRegistro, capacidade) {
            document.getElementById('modalInscritos').style.display = 'block';
            document.getElementById('overlay').style.display = 'block';

            fetch(`${controllerPainel}?acao=listar_inscritos_evento&id_registro=${idRegistro}`)
            .then(res => res.json())
            .then(res => {
                const corpo = document.getElementById('listaInscritosCorpo');
                corpo.innerHTML = '';
                document.getElementById('estatisticasEvento').innerText = `Capacidade Máxima do Local: ${capacidade} vagas`;

                if(res.sucesso && res.dados.length > 0) {
                    res.dados.forEach(insc => {
                        // Criamos o elemento select programaticamente ou garantimos que os IDs passem de forma limpa como inteiros
                        const idInscricao = parseInt(insc.id);

                        corpo.innerHTML += `
                            <tr>
                                <td style="padding:6px; border-bottom:1px solid #ddd; font-size:13px;">
                                    ${insc.nome} (<small>${insc.matricula || 'Sem Matrícula'}</small>)
                                </td>
                                <td style="padding:6px; border-bottom:1px solid #ddd; font-size:13px;">
                                    ${insc.tipo_inscricao}
                                </td>
                                <td style="padding:6px; border-bottom:1px solid #ddd;">
                                    <select onchange="mudarStatusInscricaoAdmin(${idInscricao}, this.value)" style="font-size:12px; padding:2px;">
                                        <option value="Pendente" ${insc.status_inscricao === 'Pendente' ? 'selected' : ''}>Pendente</option>
                                        <option value="Confirmado" ${insc.status_inscricao === 'Confirmado' ? 'selected' : ''}>Confirmado</option>
                                        <option value="Recusada" ${insc.status_inscricao === 'Recusada' ? 'selected' : ''}>Recusada</option>
                                    </select>
                                </td>
                            </tr>
                        `;
                    });
                } else {
                    corpo.innerHTML = '<tr><td colspan="3" style="text-align:center; padding:10px; color:#777;">Ninguém inscrito ainda.</td></tr>';
                }
            })
            .catch(err => console.error("Erro ao carregar inscritos:", err));
        }

        function mudarStatusInscricaoAdmin(idInscricao, novoStatus) {
            // TESTE DE ENTRADA: Abre o F12 no navegador, aba Console, e veja se isso printa ao mudar o select
            console.log("onchange disparado com sucesso! ID:", idInscricao, "Status:", novoStatus);    
            const formData = new FormData();
            formData.append('id_inscricao', idInscricao);
            formData.append('status_inscricao', novoStatus);

            // Explicitamos a ação na URL da requisição
            fetch(`${controllerPainel}?acao=modificar_status_admin`, { 
                method: 'POST', 
                body: formData 
            })
            .then(res => res.json())
            .then(res => {
                alert(res.mensagem);
                if (res.sucesso) {
                    // Recarrega o painel de fundo para atualizar os contadores numéricos de confirmados/pensando
                    carregarDadosDoPainel(); 
                    // DICA: Re-chama a listagem do modal para atualizar os dados na tabela do próprio modal sem precisar fechar
                    abrirGerenciadorInscritos(document.getElementById('id').value || idInscricao, 0);
                }
            })
            .catch(err => console.error("Erro na requisição:", err));
        }

        function redirecionarLogin() {
            window.location.href = "<?= BASE_URL ?>/views/login.php";
        }

        function fecharModais() {
            document.getElementById('modalInscritos').style.display = 'none';
            document.getElementById('modalRegistroAtividade').style.display = 'none';
            document.getElementById('overlay').style.display = 'none';
        }

        function fecharModal() {
            document.getElementById('modalInscritos').style.display = 'none';
            document.getElementById('overlay').style.display = 'none';
        }

        function fazerLogout() {
            window.location.href = "<?= BASE_URL ?>/services/auth/logout.php"
        }
    </script>
</body>
</html>