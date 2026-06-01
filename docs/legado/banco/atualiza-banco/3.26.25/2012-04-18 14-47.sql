CREATE TABLE grupo_doencas
(
   gd_codigo bigserial, 
   gd_descricao character varying(50),
   PRIMARY KEY (gd_codigo) 
) 
WITH (
  OIDS = FALSE
)
;


CREATE TABLE grupos_cid
(
   gc_codigo bigserial, 
   gd_codigo bigint, 
   cd10_codigo bigint, 
    PRIMARY KEY (gc_codigo), 
    FOREIGN KEY (cd10_codigo) REFERENCES cid10 (cd10_codigo) ON UPDATE NO ACTION ON DELETE NO ACTION, 
    FOREIGN KEY (gd_codigo) REFERENCES grupo_doencas (gd_codigo) ON UPDATE NO ACTION ON DELETE NO ACTION
) 
WITH (
  OIDS = FALSE
)
;
COMMENT ON COLUMN grupos_cid.gd_codigo IS 'Chave da tabela grupo_doencas';
COMMENT ON COLUMN grupos_cid.cd10_codigo IS 'chave da tabela cid10';
