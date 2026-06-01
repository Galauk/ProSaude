 INSERT INTO config(conf_chave,
					  conf_label,
					  conf_readonly,
					  conf_tipo,
					  conf_valor_string)
			   VALUES ('NOME_DEPARTAMENTO_SOCIAL',
					   'Nome da instituição do Social',
					   'F',
					   1,
					   'Fundo Municipal de Assistência Social');
					   
 INSERT INTO config(conf_chave,
	     conf_label,
	     conf_readonly,
	     conf_tipo,
	     conf_valor_int)
      VALUES ('FARMACIA_TEMPO_HISTORICO',
	      'Tempo para Historico do paciente',
	      'F',
	      3,
	      30);