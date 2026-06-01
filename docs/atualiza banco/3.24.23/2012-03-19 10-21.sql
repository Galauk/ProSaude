CREATE TABLE leito_grade_categoria ( 
	lgc_codigo bigserial NOT NULL,
	lgc_descricao varchar(50) NOT NULL
);

ALTER TABLE leito_grade_categoria ADD CONSTRAINT PK_leito_grade_categoria 
	PRIMARY KEY (lgc_codigo);

CREATE TABLE leito_grade_modelo ( 
	lgm_codigo bigserial NOT NULL,
	lgc_codigo bigint NOT NULL,
	lgm_descricao varchar(50) NOT NULL,
	lgm_intervalo integer NOT NULL,
	lgm_dias integer NOT NULL
)
;

ALTER TABLE leito_grade_modelo ADD CONSTRAINT PK_leito_grade_modelo 
	PRIMARY KEY (lgm_codigo)
;


ALTER TABLE leito_grade_modelo ADD CONSTRAINT FK_leito_grade_modelo_leito_grade_categoria 
	FOREIGN KEY (lgc_codigo) REFERENCES leito_grade_categoria (lgc_codigo)
;

	
CREATE TABLE leito_itens_grade_modelo ( 
	ligm_codigo bigserial NOT NULL,
	lgm_codigo bigint NOT NULL,
	pro_codigo bigint NOT NULL,
	ligm_quantidade integer NOT NULL
);

ALTER TABLE leito_itens_grade_modelo ADD CONSTRAINT PK_leito_itens_grade_modelo 
	PRIMARY KEY (ligm_codigo);

ALTER TABLE leito_itens_grade_modelo ADD CONSTRAINT FK_leito_itens_grade_modelo_leito_grade_modelo 
	FOREIGN KEY (lgm_codigo) REFERENCES leito_grade_modelo (lgm_codigo);
	
	
CREATE TABLE leito_grade ( 
	lgra_codigo bigserial NOT NULL,
	usu_codigo bigint NOT NULL,
	lei_codigo bigint NOT NULL,
	lgra_intervalo integer NOT NULL,
	lgra_dias integer NOT NULL,
	lgra_data timestamp without time zone,
	lgra_hora char(5),
	lgra_proximo timestamp without time zone
);

ALTER TABLE leito_grade ADD CONSTRAINT PK_leito_grade 
	PRIMARY KEY (lgra_codigo);

CREATE TABLE leito_dispensacao ( 
	ldis_codigo bigserial NOT NULL,
	lgra_codigo bigint NOT NULL,
	usr_codigo bigint NOT NULL,
	ldis_data timestamp without time zone NOT NULL,
	ldis_hora char(5) NOT NULL
);

ALTER TABLE leito_dispensacao ADD CONSTRAINT PK_leito_grade2 
	PRIMARY KEY (ldis_codigo);

ALTER TABLE leito_dispensacao ADD CONSTRAINT FK_leito_dispensacao_leito_grade2 
	FOREIGN KEY (lgra_codigo) REFERENCES leito_grade (lgra_codigo);
	
CREATE TABLE leito_itens_dispensacao ( 
	lid_codigo bigserial NOT NULL,
	ldis_codigo bigint NOT NULL,
	cont_codigo bigint,
	lid_quantidade integer
);

ALTER TABLE leito_itens_dispensacao ADD CONSTRAINT PK_leito_itens_dispensacao 
	PRIMARY KEY (lid_codigo);

ALTER TABLE leito_itens_dispensacao ADD CONSTRAINT FK_leito_itens_dispensacao_controlefracionado 
	FOREIGN KEY (cont_codigo) REFERENCES controlefracionado (cont_codigo);

ALTER TABLE leito_itens_dispensacao ADD CONSTRAINT FK_leito_itens_dispensacao_leito_dispensacao 
	FOREIGN KEY (ldis_codigo) REFERENCES leito_dispensacao (ldis_codigo);
	
CREATE TABLE leito_itens_grade ( 
	lig_codigo bigserial NOT NULL,
	lgra_codigo bigint NOT NULL,
	pro_codigo bigint NOT NULL,
	lig_quantidade integer NOT NULL
);

ALTER TABLE leito_itens_grade ADD CONSTRAINT PK_leito_itens_grade 
	PRIMARY KEY (lig_codigo);

ALTER TABLE leito_itens_grade ADD CONSTRAINT FK_leito_itens_grade_leito_grade 
	FOREIGN KEY (lgra_codigo) REFERENCES leito_grade (lgra_codigo);

ALTER TABLE leito_itens_grade ADD CONSTRAINT FK_leito_itens_grade_produto 
	FOREIGN KEY (pro_codigo) REFERENCES produto (pro_codigo);

