UPDATE config SET conf_valor_data=CURRENT_DATE WHERE conf_chave='DATA_INSTALACAO_SAUDE';
UPDATE config SET conf_valor_string='3.26.25' WHERE conf_chave='VERSAO_SAUDE';

UPDATE config SET conf_valor_data=CURRENT_DATE WHERE conf_chave='DATA_INSTALACAO_SOCIAL';
UPDATE config SET conf_valor_string='3.16.14' WHERE conf_chave='VERSAO_SOCIAL';

UPDATE config SET conf_valor_data=CURRENT_DATE WHERE conf_chave='DATA_INSTALACAO_COMUM';
UPDATE config SET conf_valor_string='3.12.4' WHERE conf_chave='VERSAO_COMUM';