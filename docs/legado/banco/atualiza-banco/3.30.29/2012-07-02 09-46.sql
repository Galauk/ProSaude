CREATE TABLE compra_produto
(
  comp_codigo bigserial NOT NULL,
  for_codigo bigint NOT NULL,
  usu_codigo bigint NOT NULL,
  usr_codigo bigint NOT NULL,
  comp_data date,
  CONSTRAINT pk_compra_produto PRIMARY KEY (comp_codigo ),
  CONSTRAINT fk_fornecedor FOREIGN KEY (for_codigo)
      REFERENCES fornecedor (for_codigo) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT fk_usuario FOREIGN KEY (usu_codigo)
      REFERENCES usuario (usu_codigo) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT fk_usuarios FOREIGN KEY (usr_codigo)
      REFERENCES usuarios (usr_codigo) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (
  OIDS=FALSE
);


CREATE TABLE compra_produto_itens
(
  compi_codigo bigserial NOT NULL,
  pro_codigo bigint NOT NULL,
  compi_quantidade bigint NOT NULL,
  compi_valor numeric(10,2),
  comp_codigo bigint NOT NULL,
  CONSTRAINT pk_compra_produto_itens PRIMARY KEY (compi_codigo ),
  CONSTRAINT comp_codigo FOREIGN KEY (comp_codigo)
      REFERENCES compra_produto (comp_codigo) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT fk_produto FOREIGN KEY (pro_codigo)
      REFERENCES produto (pro_codigo) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (
  OIDS=FALSE
);

ALTER TABLE compra_produto_itens DROP COLUMN pro_codigo;
ALTER TABLE compra_produto_itens ADD COLUMN pro_nome character(180);
