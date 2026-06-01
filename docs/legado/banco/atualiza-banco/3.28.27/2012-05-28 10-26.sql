ALTER TABLE hiperdia ADD COLUMN uni_codigo bigint;
ALTER TABLE hiperdia ADD FOREIGN KEY (uni_codigo) REFERENCES unidade (uni_codigo) ON UPDATE NO ACTION ON DELETE NO ACTION;

ALTER TABLE hiperdia_acompanhamentos ADD COLUMN uni_codigo bigint;
ALTER TABLE hiperdia_acompanhamentos ADD FOREIGN KEY (uni_codigo) REFERENCES unidade (uni_codigo) ON UPDATE NO ACTION ON DELETE NO ACTION;
