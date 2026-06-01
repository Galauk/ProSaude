CREATE TABLE controlefracionado_reserva ( 
	cfr_codigo bigserial NOT NULL,
	cont_codigo bigint NOT NULL,
	cfr_quantidade integer NOT NULL,
	cfr_data_hora timestamp DEFAULT NOW() NOT NULL,
	lgra_codigo bigint
)
;

ALTER TABLE controlefracionado_reserva ADD CONSTRAINT PK_controlefracionado_reserva 
	PRIMARY KEY (cfr_codigo)
;


ALTER TABLE controlefracionado_reserva ADD CONSTRAINT FK_controlefracionado_reserva_controlefracionado 
	FOREIGN KEY (cont_codigo) REFERENCES controlefracionado (cont_codigo)
;

ALTER TABLE controlefracionado_reserva ADD CONSTRAINT FK_controlefracionado_reserva_leito_grade 
	FOREIGN KEY (lgra_codigo) REFERENCES leito_grade (lgra_codigo)
;