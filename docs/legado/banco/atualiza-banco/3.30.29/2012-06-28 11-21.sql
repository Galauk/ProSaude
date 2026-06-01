CREATE TABLE raiox
(
   rai_codigo bigserial, 
   usu_codigo bigint, 
   ate_codigo bigint, 
   "rai_dataUpload" date, 
   rai_img bytea, 
   usr_codigo bigint, 
   CONSTRAINT fk_usr_codigo FOREIGN KEY (usr_codigo) REFERENCES usuarios (usr_codigo) ON UPDATE NO ACTION ON DELETE NO ACTION, 
   CONSTRAINT fk_ate_codigo FOREIGN KEY (ate_codigo) REFERENCES atendimento (ate_codigo) ON UPDATE NO ACTION ON DELETE NO ACTION, 
   CONSTRAINT fk_usu_codigo FOREIGN KEY (usu_codigo) REFERENCES usuario (usu_codigo) ON UPDATE NO ACTION ON DELETE NO ACTION
) 
WITH (
  OIDS = FALSE
)
;
