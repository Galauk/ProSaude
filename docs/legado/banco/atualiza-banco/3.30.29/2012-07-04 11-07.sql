UPDATE config SET conf_valor_data=CURRENT_DATE WHERE conf_chave='DATA_INSTALACAO_SAUDE';
UPDATE config SET conf_valor_string='3.30.29' WHERE conf_chave='VERSAO_SAUDE';

UPDATE config SET conf_valor_data=CURRENT_DATE WHERE conf_chave='DATA_INSTALACAO_SOCIAL';
UPDATE config SET conf_valor_string='3.18.16' WHERE conf_chave='VERSAO_SOCIAL';

UPDATE config SET conf_valor_data=CURRENT_DATE WHERE conf_chave='DATA_INSTALACAO_COMUM';
UPDATE config SET conf_valor_string='3.18.8' WHERE conf_chave='VERSAO_COMUM';