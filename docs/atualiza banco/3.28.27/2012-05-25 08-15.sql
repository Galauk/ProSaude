CREATE TABLE pma2 ( 
	pma_codigo bigserial NOT NULL,
	pma_seguimento char(2),
	uni_codigo bigint NOT NULL,
	area_codigo bigint NOT NULL,
	pma_mes date NOT NULL,    -- Mês/ano do cabeçalho do PMA2 
	pma_data timestamp without time zone DEFAULT NOW() NOT NULL,    -- Quando foi gerado o PMA2 
	usr_codigo bigint NOT NULL    -- USR que criou o registro 
)
;
COMMENT ON COLUMN pma2.pma_mes
    IS 'Mês/ano do cabeçalho do PMA2'
;
COMMENT ON COLUMN pma2.pma_data
    IS 'Quando foi gerado o PMA2'
;
COMMENT ON COLUMN pma2.usr_codigo
    IS 'USR que criou o registro'
;

ALTER TABLE pma2 ADD CONSTRAINT PK_pma2 
	PRIMARY KEY (pma_codigo)
;


ALTER TABLE pma2 ADD CONSTRAINT FK_pma2_usuarios 
	FOREIGN KEY (usr_codigo) REFERENCES usuarios (usr_codigo)
;



CREATE TABLE pma2_atributos ( 
	pmaa_codigo bigserial NOT NULL,
	pmaa_chave varchar(50) NOT NULL
)
;

ALTER TABLE pma2_atributos ADD CONSTRAINT PK_pma2_atributos 
	PRIMARY KEY (pmaa_codigo)
;


CREATE TABLE pma2_relacao ( 
	pmar_codigo bigserial NOT NULL,
	pma_codigo bigint NOT NULL,
	pmaa_codigo bigint NOT NULL,
	pmar_valor_sistema bigint DEFAULT 0 NOT NULL,    -- Valor gerado pelo sistema 
	pmar_valor_digitado bigint,    -- Valor final, digitado pelo usuário 
	usr_codigo bigint
)
;
COMMENT ON COLUMN pma2_relacao.pmar_valor_sistema
    IS 'Valor gerado pelo sistema'
;
COMMENT ON COLUMN pma2_relacao.pmar_valor_digitado
    IS 'Valor final, digitado pelo usuário'
;

ALTER TABLE pma2_relacao ADD CONSTRAINT PK_pma_relacao 
	PRIMARY KEY (pmar_codigo)
;


ALTER TABLE pma2_relacao ADD CONSTRAINT FK_pma_relacao_pma2 
	FOREIGN KEY (pma_codigo) REFERENCES pma2 (pma_codigo)
;

ALTER TABLE pma2_relacao ADD CONSTRAINT FK_pma_relacao_pma2_atributos 
	FOREIGN KEY (pmaa_codigo) REFERENCES pma2_atributos (pmaa_codigo)
;

ALTER TABLE pma2_relacao ADD CONSTRAINT FK_pma_relacao_usuarios 
	FOREIGN KEY (usr_codigo) REFERENCES usuarios (usr_codigo)
;

