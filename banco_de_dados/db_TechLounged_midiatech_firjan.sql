CREATE DATABASE techlounged;

USE techlounged;

-- ====================================================================
-- 1. TABELAS DE CONFIGURAÇÃO, ACESSO E AUXILIARES
-- ====================================================================

CREATE TABLE funcao (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome_funcao VARCHAR(50) NOT NULL UNIQUE,
    descricao TEXT
);

CREATE TABLE usuario (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_funcao INT NOT NULL,
    nome VARCHAR(30) NOT NULL,
    sobrenome VARCHAR(100) DEFAULT NULL,
    sexo varchar(20) DEFAULT 'Prefiro não informar' CHECK (sexo IN ('Masculino', 'Feminino', 'Prefiro não informar')),
    email VARCHAR(100) NOT NULL UNIQUE,
    matricula VARCHAR(10) DEFAULT NULL UNIQUE,
    senha_hash VARCHAR(255) NOT NULL,
    ativo BOOLEAN DEFAULT TRUE,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_usuario_funcao FOREIGN KEY (id_funcao) REFERENCES funcao(id)
);

CREATE TABLE eixo (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome_eixo VARCHAR(100) NOT NULL UNIQUE,
    observacao TEXT
);

CREATE TABLE periodicidade (
    id INT PRIMARY KEY AUTO_INCREMENT,
    descricao VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE publico_alvo (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome_publico VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE espaco (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome_espaco VARCHAR(100) NOT NULL UNIQUE,
    capacidade_maxima INT
);

-- ====================================================================
-- 2. TABELAS OPERACIONAIS COM TRILHA DE AUDITORIA
-- ====================================================================

CREATE TABLE atividade (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_eixo INT NOT NULL,
    id_periodicidade INT NOT NULL,
    id_publico_alvo INT NOT NULL,
    nome_projeto VARCHAR(150) NOT NULL,
    objetivo TEXT NOT NULL,
    observacoes_gerais TEXT,
    eh_publico BOOLEAN DEFAULT TRUE,
    url_imagem VARCHAR(512) NULL,
    
    -- Campos de Auditoria solicitados
    criado_por INT NOT NULL,
    atualizado_por INT NOT NULL,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    CONSTRAINT fk_atividade_eixo FOREIGN KEY (id_eixo) REFERENCES eixo(id),
    CONSTRAINT fk_atividade_periodo FOREIGN KEY (id_periodicidade) REFERENCES periodicidade(id),
    CONSTRAINT fk_atividade_publico FOREIGN KEY (id_publico_alvo) REFERENCES publico_alvo(id),
    CONSTRAINT fk_atividade_criador FOREIGN KEY (criado_por) REFERENCES usuario(id),
    CONSTRAINT fk_atividade_editor FOREIGN KEY (atualizado_por) REFERENCES usuario(id)
);

CREATE TABLE registro_atividade (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_atividade INT NOT NULL,
    id_espaco INT NOT NULL,
    data_execucao DATE NOT NULL,
    data_finalizacao DATE,
    tema_especifico VARCHAR(255),
    status VARCHAR(20) DEFAULT 'Planejado' CHECK (status IN ('Planejado', 'Concluído', 'Cancelado')),
    publico_realizado INT CHECK (publico_realizado >= 0),
    publico_previsto INT CHECK (publico_previsto >= 0),
    url_imagem VARCHAR(512) NULL,
    confirm_auto BOOLEAN DEFAULT TRUE,
    
    -- Campos de Auditoria solicitados
    criado_por INT NOT NULL,
    atualizado_por INT NOT NULL,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    CONSTRAINT fk_registro_atividade FOREIGN KEY (id_atividade) REFERENCES atividade(id) ON DELETE CASCADE,
    CONSTRAINT fk_registro_espaco FOREIGN KEY (id_espaco) REFERENCES espaco(id),
    CONSTRAINT fk_registro_criador FOREIGN KEY (criado_por) REFERENCES usuario(id),
    CONSTRAINT fk_registro_editor FOREIGN KEY (atualizado_por) REFERENCES usuario(id)
);

CREATE TABLE cine_biblioteca (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_registro_atividade INT NOT NULL,
    titulo_curta VARCHAR(150) NOT NULL,
    link VARCHAR(255),
    detalhes_controle TEXT,
    
    -- Campos de Auditoria solicitados
    criado_por INT NOT NULL,
    atualizado_por INT NOT NULL,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    CONSTRAINT fk_cine_registro FOREIGN KEY (id_registro_atividade) REFERENCES registro_atividade(id) ON DELETE CASCADE,
    CONSTRAINT fk_cine_criador FOREIGN KEY (criado_por) REFERENCES usuario(id),
    CONSTRAINT fk_cine_editor FOREIGN KEY (atualizado_por) REFERENCES usuario(id)
);

CREATE TABLE inscricao (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_registro_atividade INT NOT NULL,
    id_usuario_inscrito INT NOT NULL,
    tipo_inscricao VARCHAR(20) DEFAULT 'Pensando' CHECK (tipo_inscricao IN ('Confirmado', 'Pensando')),
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    status_inscricao VARCHAR(20) DEFAULT 'Pendente' CHECK(status_inscricao IN('Confirmado','Pendente','Recusada')),

    CONSTRAINT fk_inscricao_registro FOREIGN KEY (id_registro_atividade) REFERENCES registro_atividade(id) ON DELETE CASCADE,
    CONSTRAINT fk_inscricao_usuario FOREIGN KEY (id_usuario_inscrito) REFERENCES usuario(id)
);

-- ====================================================================
-- 3. Indices inportantes
-- ====================================================================

-- Usuário
CREATE INDEX idx_usuario_email
ON usuario(email);

-- Atividade
CREATE INDEX idx_atividade_eixo
ON atividade(id_eixo);

CREATE INDEX idx_atividade_publico
ON atividade(id_publico_alvo);

-- Registro
CREATE INDEX idx_registro_data
ON registro_atividade(data_execucao);