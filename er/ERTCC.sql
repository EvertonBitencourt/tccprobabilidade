
/* Drop Tables */

DROP TABLE IF EXISTS historico;
DROP TABLE IF EXISTS pergunta_usuario;
DROP TABLE IF EXISTS resposta_sistema;
DROP TABLE IF EXISTS tipo_entrada;
DROP TABLE IF EXISTS usuario;




/* Create Tables */

CREATE TABLE historico
(
	id_historico serial NOT NULL,
	mensagem varchar NOT NULL,
	data_hora time with time zone NOT NULL,
	id_usuario int NOT NULL,
	PRIMARY KEY (id_historico)
) WITHOUT OIDS;


CREATE TABLE pergunta_usuario
(
	id_tipo_entrada serial NOT NULL,
	texto varchar NOT NULL,
	PRIMARY KEY (id_tipo_entrada)
) WITHOUT OIDS;


CREATE TABLE resposta_sistema
(
	id_tipo_resposta serial NOT NULL,
	mensagem varchar NOT NULL,
	PRIMARY KEY (id_tipo_resposta)
) WITHOUT OIDS;


CREATE TABLE tipo_entrada
(
	id_tipo_entrada serial NOT NULL,
	nome_tipo_entrada varchar NOT NULL,
	PRIMARY KEY (id_tipo_entrada)
) WITHOUT OIDS;


CREATE TABLE usuario
(
	id_usuario serial NOT NULL,
	nome varchar NOT NULL,
	PRIMARY KEY (id_usuario)
) WITHOUT OIDS;



/* Create Foreign Keys */

ALTER TABLE historico
	ADD FOREIGN KEY (id_usuario)
	REFERENCES usuario (id_usuario)
	ON UPDATE RESTRICT
	ON DELETE RESTRICT
;



