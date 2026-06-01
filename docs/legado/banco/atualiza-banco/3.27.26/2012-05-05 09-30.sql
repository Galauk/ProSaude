ALTER TABLE convenio_itens ADD COLUMN uni_codigo_coleta bigint;
ALTER TABLE agenda ADD COLUMN med_codigo bigint;
ALTER TABLE agenda ADD COLUMN ate_codigo bigint;
ALTER TABLE agenda ADD CONSTRAINT fk_ate_codigo FOREIGN KEY (ate_codigo) REFERENCES atendimento (ate_codigo) ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE agenda ADD COLUMN usr_codigo_medico bigint;
ALTER TABLE agenda ADD CONSTRAINT fk_med_codigo FOREIGN KEY (med_codigo) REFERENCES medico (med_codigo) ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE agenda ADD CONSTRAINT fk_usr_codigo FOREIGN KEY (usr_codigo_medico) REFERENCES usuarios (usr_codigo) ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE agenda_itens ADD COLUMN med_codigo_coleta bigint;
ALTER TABLE agenda_itens ADD CONSTRAINT fk_uni_codigo FOREIGN KEY (uni_codigo_coleta) REFERENCES unidade (uni_codigo) ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE agenda_itens ADD CONSTRAINT fk_med_codigo FOREIGN KEY (med_codigo_coleta) REFERENCES medico (med_codigo) ON UPDATE NO ACTION ON DELETE NO ACTION;
CREATE TABLE coleta ( 
	col_codigo bigserial NOT NULL,
	agei_codigo bigint NOT NULL,
	col_data_entrega date NOT NULL,
	col_data_coleta date NOT NULL
)
;

ALTER TABLE coleta ADD CONSTRAINT PK_coleta 
	PRIMARY KEY (col_codigo)
;
ALTER TABLE coleta ADD CONSTRAINT fk_agei_codigo FOREIGN KEY (agei_codigo) REFERENCES agenda_itens (agei_codigo) ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE coleta
   ALTER COLUMN col_data_entrega DROP NOT NULL;





