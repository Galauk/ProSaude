CREATE TABLE usuario_doencas
(
  usud_codigo bigint NOT NULL DEFAULT nextval(('seq_usud_codigo'::text)::regclass),
  co_pergunta_detalhe bigint NOT NULL,
  usu_codigo bigint NOT NULL,
  CONSTRAINT pk_usud_codigo2 PRIMARY KEY (usud_codigo),
  CONSTRAINT fk_co_pergunta_detalhe2 FOREIGN KEY (co_pergunta_detalhe)
      REFERENCES tb_pergunta_detalhe (co_pergunta_detalhe) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT fk_usu_codigo2 FOREIGN KEY (usu_codigo)
      REFERENCES usuario (usu_codigo) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (
  OIDS=FALSE
);
ALTER TABLE usuario_doencas
  OWNER TO postgres;
