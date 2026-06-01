-- Function: public.pattobpa()

-- DROP FUNCTION public.pattobpa();

CREATE OR REPLACE FUNCTION public.pattobpa()
  RETURNS trigger AS
$BODY$
DECLARE
  linha RECORD;
  ci_codigo bigint;  
  codigo bigint; -- ate_codigo, pc_codigo ou pe_codigo
    
BEGIN

	-- Posto de Enfermagem?
	IF NEW.pe_codigo IS NOT NULL THEN
	
		 SELECT INTO linha
					 uni_codigo,
					 usr_codigo,
					 usu_codigo,
					 ate_data AS bpa_data,
					 proc.proc_codigo,
					 proc_bpa_tipo AS bpa_tipo,
					 pat.cd10_codigo AS bpa_cd10_codigo,
					 'procedimento_atendimetno' AS bpa_origem,
					 pat.pat_codigo AS bpa_origem_codigo
			    FROM procedimento_atendimento AS pat
			    JOIN posto_enfermagem AS pe
				  ON pe.pe_codigo=pat.pe_codigo
			    JOIN atendimento AS ate
				  ON ate.ate_codigo=pe.ate_codigo
			    JOIN procedimento AS proc
				  ON proc.proc_codigo=pat.proc_codigo
			   WHERE pat.pat_codigo=NEW.pat_codigo;
			 
	-- Pré-consulta?
	ELSIF NEW.pc_codigo IS NOT NULL THEN
				
		 SELECT INTO linha
					 uni_codigo,
					 pat.usr_codigo,
					 age.usu_codigo,
					 age_data AS bpa_data,
					 proc.proc_codigo,
					 proc_bpa_tipo AS bpa_tipo,
					 pat.cd10_codigo AS bpa_cd10_codigo,
					 'procedimento_atendimetno' AS bpa_origem,
					 pat.pat_codigo AS bpa_origem_codigo
				FROM procedimento_atendimento AS pat
				JOIN pre_consulta AS pc
				  ON pc.pc_codigo=pat.pc_codigo
				JOIN agendamento AS age
				  ON age.age_codigo=pc.age_codigo
				JOIN procedimento AS proc
				  ON proc.proc_codigo=pat.proc_codigo
			   WHERE pat.pat_codigo=NEW.pat_codigo;
			  
	-- Atendimento?
	ELSIF NEW.ate_codigo IS NOT NULL THEN	
		SELECT INTO linha
					uni_codigo,
					pat.usr_codigo,
					ate.usu_codigo,
					ate_data AS bpa_data,
					proc.proc_codigo,
					proc_bpa_tipo AS bpa_tipo,
					pat.cd10_codigo AS bpa_cd10_codigo,
					'procedimento_atendimetno' AS bpa_origem,
					pat.pat_codigo AS bpa_origem_codigo
			   FROM procedimento_atendimento AS pat
			   JOIN atendimento AS ate
				 ON ate.ate_codigo=pat.ate_codigo
			   JOIN procedimento AS proc
				 ON proc.proc_codigo=pat.proc_codigo
			  WHERE pat.pat_codigo=NEW.pat_codigo;
			  
	END IF;					

	-- verificar acidente de trabalho
	SELECT ci.ci_codigo FROM ci WHERE ci_descricao='Eletivo' AND ci_ativo='S' INTO ci_codigo;
	
	INSERT INTO BPA (uni_codigo,
					 usr_codigo,
					 usu_codigo,
					 bpa_data,
					 proc_codigo,
					 ci_codigo,
					 bpa_tipo,
					 bpa_cd10_codigo,
					 bpa_origem,
					 bpa_origem_codigo)
			 VALUES (linha.uni_codigo,
					 linha.usr_codigo,
					 linha.usu_codigo,
					 linha.bpa_data,
					 linha.proc_codigo,
					 ci_codigo,
					 linha.bpa_tipo,
					 linha.bpa_cd10_codigo,
					 'procedimento_atendimento',
					 NEW.pat_codigo);						
						
	RAISE NOTICE 'Um registro na tabela ''bpa'' foi gerado devido uma inserção na tabela ''procedimento_atendimento''.';
				
	RETURN NEW;

END
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION public.pattobpa()
  OWNER TO postgres;
