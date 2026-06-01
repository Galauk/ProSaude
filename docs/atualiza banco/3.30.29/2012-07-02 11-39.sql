ALTER TABLE procedimentos_sisprenatal ADD COLUMN proc_sispn_sisprenatal boolean;
ALTER TABLE compra_produto_itens DROP COLUMN pro_codigo;
ALTER TABLE compra_produto_itens ADD COLUMN pro_nome character varying(200);

