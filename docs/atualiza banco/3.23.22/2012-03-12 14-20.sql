update config set conf_label='Data de instalção Social', conf_chave='DATA_INSTALACAO_SOCIAL' WHERE conf_codigo=11

UPDATE config SET conf_valor_string='3.14.12' WHERE conf_chave='VERSAO_SOCIAL';
UPDATE config SET conf_valor_data=CURRENT_DATE WHERE conf_chave='DATA_INSTALACAO_SOCIAL';