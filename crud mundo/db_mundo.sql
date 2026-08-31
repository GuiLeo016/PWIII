drop database if exists db_mundo;
create database if not exists db_mundo;
use db_mundo;

create table if not exists tb_continentes(
	id_continente INT PRIMARY KEY AUTO_INCREMENT,
    nome_continente VARCHAR(45) NOT NULL,
    populacao_continente INT NOT NULL,
    area_continente DECIMAL(12,3) NOT NULL,
    total_paises INT NOT NULL
);

create table if not exists tb_governantes(
	id_governante INT PRIMARY KEY AUTO_INCREMENT,
	nome_governante VARCHAR(80) NOT NULL,
    partido_politico VARCHAR(80) NOT NULL,
    data_nascimento DATE NOT NULL,
    idade INT NOT NULL,
    data_inicio_mandato DATE NOT NULL,
    data_final_mandato DATE NOT NULL
);

create table if not exists tb_paises(
	id_pais INT PRIMARY KEY AUTO_INCREMENT,
    nome_pais VARCHAR(50) NOT NULL,
    populacao_pais INT NOT NULL,
    area_pais DECIMAL(12,3),
    idioma VARCHAR(20) NOT NULL,
    clima_pais VARCHAR(20) NOT NULL,
    regime_politico VARCHAR(40) NOT NULL,
    moeda VARCHAR(25) NOT NULL,
    governante_pais INT NOT NULL,
    continente_pais INT NOT NULL,
    
    FOREIGN KEY (governante_pais) REFERENCES tb_governantes (id_governante),
    FOREIGN KEY (continente_pais) REFERENCES tb_continentes (id_continente)
);

create table if not exists tb_cidades(
	id_cidade INT PRIMARY KEY AUTO_INCREMENT,
    nome_cidade VARCHAR(80) NOT NULL,
    populacao_cidade INT NOT NULL,
    area_cidade DECIMAL(12,3) NOT NULL,
    clima_cidade VARCHAR(20) NOT NULL,
    data_fundacao DATE NOT NULL,
    pais_cidade INT NOT NULL,
    governante_cidade INT NOT NULL,
    
    FOREIGN KEY (pais_cidade) REFERENCES tb_paises (id_pais),
    FOREIGN KEY (governante_cidade) REFERENCES tb_governantes (id_governante)
);

create table if not exists tb_usuario(
    id_usuario INT PRIMARY KEY AUTO_INCREMENT,
    login_usuario VARCHAR(30) NOT NULL,
    senha_usuario VARCHAR(255) NOT NULL,
    tipo_usuario CHAR(1) NOT NULL
);

create table if not exists tb_log (
    id_log INT PRIMARY KEY AUTO_INCREMENT,
    usuario_log INT,
    acao_log VARCHAR(30) NOT NULL,
    tabela_log VARCHAR(50),
    id_registro INT,
    descricao_log VARCHAR(255),
    data_log DATE NOT NULL,
    hora_log TIME NOT NULL,

    FOREIGN KEY (usuario_log) REFERENCES tb_usuario(id_usuario)
);
