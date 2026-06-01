CREATE TABLE autentificacao
(
   aut_codigo bigserial, 
   aut_nome character varying(150) NOT NULL, 
   aut_codigo_validacao integer, 
   aut_data_validacao date, 
   aut_senha character varying(150), 
   aut_data date
) 
WITH (
  OIDS = FALSE,
  
)
;

ALTER TABLE mais_acessados
   ADD COLUMN ma_title character varying(100);
   
insert into config(conf_chave,conf_label,conf_readonly,conf_tipo,conf_valor_bool)VALUES('ATUALIZACAO','Atualização',true,2,false)


CREATE TABLE schema_version (
    version character varying(20) NOT NULL,
    description character varying(100),
    type character varying(10) NOT NULL,
    script character varying(200) NOT NULL,
    checksum integer,
    installed_by character varying(30) NOT NULL,
    installed_on timestamp without time zone DEFAULT now(),
    execution_time integer,
    state character varying(15) NOT NULL,
    current_version boolean NOT NULL
);


INSERT INTO schema_version (version, description, type, script, checksum, installed_by, installed_on, execution_time, state, current_version) VALUES ('0', '<< Flyway Init >>', 'INIT', '<< Flyway Init >>', NULL, 'postgres', '2012-03-23 13:53:09.162', 0, 'SUCCESS', false);
INSERT INTO schema_version (version, description, type, script, checksum, installed_by, installed_on, execution_time, state, current_version) VALUES ('1.init', NULL, 'SQL', 'V1_init.sql', -135793052, 'postgres', '2012-03-23 13:53:29.647', 297, 'SUCCESS', true);

   


ALTER TABLE agendamento ADD CONSTRAINT fk_usuarios FOREIGN KEY (med_codigo) REFERENCES usuarios (usr_codigo) ON UPDATE NO ACTION ON DELETE NO ACTION;
