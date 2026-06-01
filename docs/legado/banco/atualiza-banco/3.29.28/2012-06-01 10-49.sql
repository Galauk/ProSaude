CREATE TABLE rl_procedimento_registro
(
   co_procedimento character(10), 
   co_registro smallint, 
   dt_competencia character(6)
) 
WITH (
  OIDS = FALSE
)
;

COMMENT ON TABLE bpa
  IS 'A partir da versão 3.29.28 o BPA irá olhar o tipo (C ou I) na tabela rl_procedimento_registro, pois quarda um historio (dt_competencia)';