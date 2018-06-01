
/* Drop Tables */

DROP TABLE IF EXISTS dado;
DROP TABLE IF EXISTS problema;
DROP TABLE IF EXISTS categoria;
DROP TABLE IF EXISTS historico;
DROP TABLE IF EXISTS objeto;
DROP TABLE IF EXISTS usuario;




/* Create Tables */

CREATE TABLE categoria
(
	id_categoria bigint NOT NULL,
	nome varchar NOT NULL,
	definicao varchar NOT NULL,
	PRIMARY KEY (id_categoria)
) WITHOUT OIDS;


CREATE TABLE dado
(
	id_problema bigint NOT NULL,
	valor text NOT NULL,
	id int NOT NULL
) WITHOUT OIDS;


CREATE TABLE historico
(
	id_historico bigint NOT NULL,
	mensagem varchar,
	data_hora timestamp,
	id_origem bigint NOT NULL,
	id_destino bigint NOT NULL,
	PRIMARY KEY (id_historico)
) WITHOUT OIDS;


CREATE TABLE objeto
(
	id_objeto serial NOT NULL,
	nome varchar NOT NULL,
	faces int NOT NULL,
	PRIMARY KEY (id_objeto)
) WITHOUT OIDS;


CREATE TABLE problema
(
	id_problema bigint NOT NULL,
	id_usuario bigint NOT NULL,
	id_categoria bigint NOT NULL,
	data_hora timestamp,
	PRIMARY KEY (id_problema)
) WITHOUT OIDS;


CREATE TABLE usuario
(
	id_usuario bigserial NOT NULL,
	nome varchar,
	etapa double precision,
	PRIMARY KEY (id_usuario)
) WITHOUT OIDS;



/* Create Foreign Keys */

ALTER TABLE problema
	ADD FOREIGN KEY (id_categoria)
	REFERENCES categoria (id_categoria)
	ON UPDATE RESTRICT
	ON DELETE RESTRICT
;


ALTER TABLE dado
	ADD FOREIGN KEY (id_problema)
	REFERENCES problema (id_problema)
	ON UPDATE RESTRICT
	ON DELETE RESTRICT
;


ALTER TABLE historico
	ADD FOREIGN KEY (id_origem)
	REFERENCES usuario (id_usuario)
	ON UPDATE RESTRICT
	ON DELETE RESTRICT
;


ALTER TABLE historico
	ADD FOREIGN KEY (id_destino)
	REFERENCES usuario (id_usuario)
	ON UPDATE RESTRICT
	ON DELETE RESTRICT
;


ALTER TABLE problema
	ADD FOREIGN KEY (id_usuario)
	REFERENCES usuario (id_usuario)
	ON UPDATE RESTRICT
	ON DELETE RESTRICT
;



/* Comments */

COMMENT ON TABLE historico IS 'id_historico bigint NOT NULL DEFAULT nextval(''historico_id_historico_seq''::regclass),
  mensagem character varying,
  data_hora timestamp without time zone,
  id_origem bigint,
  id_destino bigint,';
COMMENT ON TABLE problema IS 'id_categoria bigint,
  data_hora timestamp without time zone,
  CONSTRAINT problema_pkey PRIMARY KEY (id_problema)';



