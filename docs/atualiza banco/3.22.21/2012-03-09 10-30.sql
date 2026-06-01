CREATE TABLE config
(
   conf_codigo bigserial, 
   conf_chave character varying(100) NOT NULL, 
   conf_label character varying(100), 
   conf_readonly boolean NOT NULL DEFAULT 't',
   conf_tipo integer NOT NULL DEFAULT 1, 
   conf_valor_string character varying(255), 
   conf_valor_bool boolean, 
   conf_valor_int integer, 
   conf_valor_data date
) 
WITH (
  OIDS = FALSE
)
;


ALTER TABLE config ADD CONSTRAINT PK_config 
	PRIMARY KEY (conf_codigo)
;

INSERT INTO config (conf_chave, conf_label, conf_readonly, conf_tipo,conf_valor_string) VALUES('NOME_CIDADE','Nome da cidade','f',1,'ELOTECH');
INSERT INTO config (conf_chave, conf_label, conf_readonly, conf_tipo,conf_valor_bool) VALUES('PRONTUARIO_ATENDIMENTO_TEXTAREAUNICO','Textarea único?','f',2,'t');
INSERT INTO config (conf_chave, conf_label, conf_readonly, conf_tipo,conf_valor_int) VALUES('PRONTUARIO_ATENDIMENTO_TEMPOPARAREABRIR','Tempo para reabrir','f',3,20);
INSERT INTO config (conf_chave, conf_label, conf_readonly, conf_tipo,conf_valor_bool) VALUES('GOOGLEANALYTICS','Habilitar Google Analytics?','f',2,'f');
INSERT INTO config (conf_chave, conf_label, conf_readonly, conf_tipo,conf_valor_bool) VALUES('FARMACIA_DISPENSACAO_LISTARSOMENTECOMSALDO','Listar produto sem saldo?','f',2,'t');
INSERT INTO config (conf_chave, conf_label, conf_tipo,conf_valor_string) VALUES('VERSAO_COMUM','Versão Comum',1,'3.9.2');
INSERT INTO config (conf_chave, conf_label, conf_tipo,conf_valor_data) VALUES('DATA_INSTALACAO_COMUM','Data de instalação Comum',4,CURRENT_DATE);
INSERT INTO config (conf_chave, conf_label, conf_tipo,conf_valor_string) VALUES('VERSAO_SAUDE','Versão Saúde',1,'3.22.21');
INSERT INTO config (conf_chave, conf_label, conf_tipo,conf_valor_data) VALUES('DATA_INSTALACAO_SAUDE','Data de instalação Saúde',4,CURRENT_DATE);
INSERT INTO config (conf_chave, conf_label, conf_tipo,conf_valor_string) VALUES('VERSAO_SOCIAL','Versão Social',1,NULL);
INSERT INTO config (conf_chave, conf_label, conf_tipo,conf_valor_data) VALUES('DATA_INSTALACAO_COMUM','Data de instalçai Social',4,NULL);