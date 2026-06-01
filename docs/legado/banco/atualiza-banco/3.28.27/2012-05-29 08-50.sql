UPDATE config SET conf_valor_data=CURRENT_DATE WHERE conf_chave='DATA_INSTALACAO_SAUDE';
UPDATE config SET conf_valor_string='3.27.26' WHERE conf_chave='VERSAO_SAUDE';

UPDATE config SET conf_valor_data=CURRENT_DATE WHERE conf_chave='DATA_INSTALACAO_SOCIAL';
UPDATE config SET conf_valor_string='3.17.15' WHERE conf_chave='VERSAO_SOCIAL';

UPDATE config SET conf_valor_data=CURRENT_DATE WHERE conf_chave='DATA_INSTALACAO_COMUM';
UPDATE config SET conf_valor_string='3.16.6' WHERE conf_chave='VERSAO_COMUM';