DROP TABLE PACIENTE_LEITO;
DROP TABLE LEITO;
DROP TABLE QUARTO;
DROP TABLE ATENDIMENTO_INTERNACAO;

CREATE TABLE quarto
(
  qua_codigo bigserial NOT NULL,
  qua_quarto character varying(50),
  qua_desc character varying,
  med_codigo integer,
  apt_codigo character varying(100),
  qua_numero integer,
  set_codigo integer,
  qua_andar integer,
  CONSTRAINT quarto_pkey PRIMARY KEY (qua_codigo),
  CONSTRAINT quarto_set_codigo_fkey FOREIGN KEY (set_codigo)
      REFERENCES setor (set_codigo) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (
  OIDS=FALSE
);

CREATE TABLE leito
(
  lei_codigo bigserial NOT NULL,
  qua_codigo bigint NOT NULL,
  lei_observacao character varying,
  lei_ativo character(1),
  lei_temporario character(1),
  lei_numero character varying,
  CONSTRAINT lei_codigo PRIMARY KEY (lei_codigo),
  CONSTRAINT leito_qua_codigo_fkey FOREIGN KEY (qua_codigo)
      REFERENCES quarto (qua_codigo) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (
  OIDS=FALSE
);
ALTER TABLE leito
  OWNER TO postgres;

CREATE TABLE paciente_leito
(
  pac_leito bigserial NOT NULL,
  lei_codigo integer,
  pac_dtentrada_leito timestamp with time zone,
  usr_codigo integer,
  io_codigo bigint,
  CONSTRAINT "pacienteLeito_pkey" PRIMARY KEY (pac_leito),
  CONSTRAINT paciente_leito_lei_codigo_fkey FOREIGN KEY (lei_codigo)
      REFERENCES leito (lei_codigo) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT paciente_leito_lei_codigo_fkey1 FOREIGN KEY (lei_codigo)
      REFERENCES leito (lei_codigo) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (
  OIDS=FALSE
);

CREATE TABLE atendimento_internacao
(
  atin_codigo bigserial NOT NULL,
  ate_codigo integer,
  io_codigo bigint,
  CONSTRAINT atendimento_internacao_pkey PRIMARY KEY (atin_codigo),
  CONSTRAINT atendimento_internacao_ate_codigo_fkey FOREIGN KEY (ate_codigo)
      REFERENCES atendimento (ate_codigo) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT atendimento_internacao_io_codigo_fkey FOREIGN KEY (io_codigo)
      REFERENCES internacao_observacao (io_codigo) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (
  OIDS=FALSE
);
ALTER TABLE atendimento_internacao
  OWNER TO postgres;
