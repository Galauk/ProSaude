-- Copia o atendimento.med_codigo para procedimento_atendimento.usr_codigo

UPDATE procedimento_atendimento 
   SET usr_codigo=(
	SELECT med_codigo
	  FROM atendimento AS ate
	 WHERE ate.ate_codigo=procedimento_atendimento.ate_codigo
   )
 WHERE ate_codigo IS NOT NULL
   AND pe_codigo IS NULL
   AND pc_codigo IS NULL