ALTER TABLE secretaria ADD COLUMN tipo_secretaria character(3);
ALTER TABLE usuario ADD COLUMN usu_nome_resp character(250);


ALTER TABLE compra_produto_itens DROP COLUMN pro_codigo;
ALTER TABLE compra_produto_itens ADD COLUMN pro_nome character varying(250);
