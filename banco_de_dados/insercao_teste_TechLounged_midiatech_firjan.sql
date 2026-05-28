-- ====================================================================
-- CARGA DE CONFIGURAÇÕES E PERFIS
-- ====================================================================

-- 1. Funções (IDs gerados: 1, 2, 3, 4)
INSERT INTO funcao (nome_funcao, descricao) VALUES 
('Administrador', 'Acesso total a configurações, usuários e relatórios do sistema.'),
('Bibliotecário', 'Gestão operacional de atividades, espaços e registros da biblioteca.'),
('Auxiliar', 'Inclusão de registros operacionais sob supervisão.'),
('Aluno/Público', 'Consulta e visualização de projetos marcados como públicos.');

-- 2. Usuários (IDs gerados: 1, 2, 3)
INSERT INTO usuario (id_funcao, nome, email, senha_hash) VALUES 
(1, 'Carlos Silva (Admin)', 'carlos.admin@techlounge.com', '$2y$10$xIwvwx6xXAV9n/8PV08k5ukNBHkr9xyqwlT7zcAW4v.3ASv15Yfc.'),
(2, 'Ana Costa (Bibliotecária)', 'ana.biblioteca@techlounge.com', '$2y$10$xIwvwx6xXAV9n/8PV08k5ukNBHkr9xyqwlT7zcAW4v.3ASv15Yfc.'),
(4, 'João Silva (Aluno)', 'joao.aluno@techlounge.com', '$2y$10$xIwvwx6xXAV9n/8PV08k5ukNBHkr9xyqwlT7zcAW4v.3ASv15Yfc.');

-- 3. Eixos (IDs gerados: 1, 2, 3)
INSERT INTO eixo (nome_eixo, observacao) VALUES 
('COMPETÊNCIA EM INFORMAÇÃO', 'Ações voltadas para o desenvolvimento de habilidades informacionais e pesquisa.'),
('AÇÕES CULTURAIS E DE CONVIVÊNCIA', 'Eventos culturais, celebrações e integração comunitária.'),
('GESTÃO DA BIBLIOTECA', 'Rotinas internas, relatórios e mapeamento de processos.');

-- 4. Periodicidades (IDs gerados: 1 a 7)
INSERT INTO periodicidade (descricao) VALUES 
('Pontual'),          -- ID 1
('Ao longo do ano'),   -- ID 2
('Anual'),             -- ID 3
('Semanal'),           -- ID 4
('Quinzenal'),         -- ID 5
('Mensal'),            -- ID 6
('Sob Demanda');       -- ID 7

-- 5. Públicos-Alvo (IDs gerados: 1 a 7)
INSERT INTO publico_alvo (nome_publico) VALUES 
('Professores'),           -- ID 1
('Interno'),               -- ID 2
('Equipe da Biblioteca'),  -- ID 3
('Alunos Fundamental I'),  -- ID 4
('Alunos Fundamental II'), -- ID 5
('Ensino Médio'),          -- ID 6
('Público Geral');         -- ID 7

-- 6. Espaços (IDs gerados: 1, 2, 3)
INSERT INTO espaco (nome_espaco, capacidade_maxima) VALUES 
('TechLounge Central', 45),
('Mini Auditório A', 30),
('Espaço Multimídia', 20);

-- ====================================================================
-- 2. INSERÇÃO DAS ATIVIDADES BASE (PROJETOS)
-- ====================================================================

-- Criando explicitamente a Atividade ID 1 (Cine Biblioteca) que estava faltando para os registros posteriores
INSERT INTO atividade (id, id_eixo, id_periodicidade, id_publico_alvo, nome_projeto, objetivo, observacoes_gerais, eh_publico, criado_por, atualizado_por) VALUES 
(1, 2, 6, 7, 'Cine Biblioteca', 'Exibição de curtas e debates.', NULL, TRUE, 2, 2);

-- Correção dos IDs de Eixos (ajustados para a faixa de 1 a 3 existente)
INSERT INTO atividade (id, id_eixo, id_periodicidade, id_publico_alvo, nome_projeto, objetivo, observacoes_gerais, eh_publico, criado_por, atualizado_por) VALUES 
(3, 1, 6, 5, 'De frente com a biblio', 'Apresentação sobre uso do espaço da biblioteca e suas potencialidades.', 'Sempre que abrir turma.', TRUE, 2, 2),
(4, 1, 3, 4, 'Mural Digital', 'Mural Virtual abordando Presença Feminina na Tecnologia.', NULL, TRUE, 2, 2),
(5, 1, 6, 5, 'Projeto Integrador', 'Apresentar os objetivos e as regras de P.I aos alunos de aprendizagem.', 'Projeto vai correr ao longo do ano, sempre ao iniciar uma nova turma de Aprendizagem na instituição.', TRUE, 2, 2),
(6, 1, 7, 5, 'Grand Prix SENAI 2026', 'Apresentar e incentivar a participação dos alunos.', 'Sempre que o GrandPrix for aberto.', TRUE, 2, 2),
(7, 3, 6, 5, 'Conhecendo você e seus interesses - Biblioteca do CRTI - SENAI SAPUCAÍ', 'Apresentação do Formulário de Estudo de Usuários da biblioteca.', 'Sempre que abrir turma.', FALSE, 2, 2),
(8, 3, 7, 2, 'Plano de Atividades Anual', 'Atividades macro da biblioteca.', 'Entregar no prazo estipulado pela gerência.', FALSE, 2, 2),
(9, 1, 6, 5, 'Pesquisa Acadêmica Avançada', 'Capacitação em bases de dados e fontes confiáveis.', 'Foco em turmas de nível técnico e superior.', TRUE, 2, 2),
(10, 1, 6, 5, 'Ética em Trabalhos Acadêmicos', 'Abordar plágio, citações corretas e combate a fake news.', NULL, TRUE, 2, 2),
(11, 1, 6, 5, 'Consumo Saudável de Redes Sociais', 'Discutir o impacto das redes e a curadoria de informação digital.', NULL, TRUE, 2, 2),
(12, 1, 6, 5, 'Currículo 4.0', 'Oficina de criação de portfólio e currículo digital.', 'Checklist de qualidade; análise de antes e depois.', TRUE, 2, 2),
(13, 2, 5, 4, 'Dia do Profissional de TI', 'Ação cultural voltada à valorização da área de Tecnologia da Informação.', NULL, TRUE, 2, 2),
(14, 3, 1, 5, 'Pílulas de Estudo Semanais', 'Dicas práticas semanais de como engajar in rotinas de estudos.', NULL, TRUE, 2, 2),
(15, 3, 3, 5, 'Destaques Mensais', 'Exposição dos livros técnicos e recreativos mais lidos no mês.', NULL, TRUE, 2, 2),
(16, 3, 6, 5, 'Clubes de Leitura', 'Rodas de conversa e debates sobre o mundo literário.', NULL, TRUE, 2, 2);

-- ====================================================================
-- 3. INSERÇÃO DOS REGISTROS DE ATIVIDADE (CALENDÁRIO EXECUÇÃO 2026)
-- ====================================================================

INSERT INTO registro_atividade (id, id_atividade, id_espaco, data_execucao, data_finalizacao, tema_especifico, status, publico_previsto, publico_realizado, criado_por, atualizado_por) VALUES 
(2, 3, 1, '2026-05-11', '2026-05-11', 'Integração de Novos Alunos', 'Concluído', 40, 38, 2, 2),
(3, 4, 1, '2026-03-06', '2026-03-06', 'Presença Feminina na Tecnologia', 'Concluído', 30, 25, 2, 2),
(4, 5, 1, '2026-03-30', '2026-03-30', 'Regras Básicas do P.I.', 'Concluído', 45, 42, 2, 2),
(5, 6, 1, '2026-03-30', '2026-03-30', 'Incentivo de Inscrições', 'Concluído', 45, 45, 2, 2),
(6, 7, 1, '2026-03-20', '2026-03-20', 'Mapeamento de Perfil Literário', 'Concluído', 35, 31, 2, 2),
(7, 8, 1, '2026-04-16', '2026-04-16', 'Planejamento Estratégico', 'Concluído', NULL, NULL, 2, 2),
(8, 9, 1, '2026-05-18', '2026-05-18', 'Fontes de Informação Científica', 'Planejado', 30, NULL, 2, 2),
(9, 10, 1, '2026-06-01', '2026-06-01', 'Plágio e Fake News', 'Planejado', 30, NULL, 2, 2),
(10, 11, 1, '2026-07-15', '2026-07-15', 'Curadoria de Conteúdo Digital', 'Planejado', 25, NULL, 2, 2),
(11, 12, 1, '2026-08-20', '2026-08-20', 'Oficina de Portfólio Digital', 'Planejado', 20, NULL, 2, 2),
(12, 13, 1, '2026-10-19', '2026-10-19', 'Evento Integrado de Tecnologia', 'Planejado', 50, NULL, 2, 2),
-- Execuções vinculadas à Atividade ID 1
(13, 1, 1, '2026-05-08', '2026-05-08', 'Sessão Animada / Literatura', 'Concluído', 30, 28, 2, 2),
(14, 1, 1, '2026-05-22', '2026-05-22', 'Sessão Quarta Revolução Industrial', 'Concluído', 35, 30, 2, 2),
(15, 1, 1, '2026-06-05', '2026-06-05', 'Sessão Sustentabilidade e Inclusão', 'Planejado', 30, NULL, 2, 2);

-- ====================================================================
-- 4. DETALHAMENTO DO CINE BIBLIOTECA (MÍDIAS EXIBIDAS)
-- ====================================================================

INSERT INTO cine_biblioteca (id, id_registro_atividade, titulo_curta, link, detalhes_controle, criado_por, atualizado_por) VALUES 
(2, 13, 'Os Fantásticos Livros Voadores do Sr. Morris Lessmore', 'https://www.youtube.com/watch?v=LjkdEvMM5xs&t=1s', 'Vencedor do Oscar de melhor curta animado de 2012. Mostra o poder dos livros sobre nós e a reconstrução através da leitura pós-furacão Katrina.', 2, 2),
(3, 14, 'Indústria 4.0', 'https://www.deloitte.com/br/pt/our-thinking/mundo-corporativo/videocasts/industria-4-0-antes-e-depois-da-crise.html', 'Documentário patrocinado pela Deloitte que analisa os impactos da Quarta Revolução Industrial e a transformação digital acelerada.', 2, 2),
(4, 15, 'Caminho dos gigantes', 'https://www.youtube.com/watch?v=YE1WeW_QIa8', 'Discussão sobre ciclos da vida, natureza e conexões coletivas.', 2, 2),
(5, 15, 'Float', 'https://www.disneyplus.com/pt-br/browse/entity-a9cfbd87-83de-45bb-8412-c53863f8106a', 'Curta que aborda de forma sensível a inclusão, o respeito às diferenças e as relações familiares.', 2, 2),
(6, 15, 'Tamara', 'https://www.youtube.com/watch?v=SNRFDkKEqhk', 'História emocionante de uma menina surda que sonha em ser bailarina, trabalhando superação e empatia.', 2, 2);