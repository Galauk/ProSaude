UPDATE config SET conf_valor_string='3.23.22' WHERE conf_chave='VERSAO_SAUDE';
UPDATE config SET conf_valor_data=CURRENT_DATE WHERE conf_chave='DATA_INSTALACAO_SAUDE';

UPDATE config SET conf_valor_string='3.10.3' WHERE conf_chave='VERSAO_COMUM';
UPDATE config SET conf_valor_data=CURRENT_DATE WHERE conf_chave='DATA_INSTALACAO_COMUM';