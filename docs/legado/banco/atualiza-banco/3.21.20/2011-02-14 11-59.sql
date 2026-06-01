CREATE TABLE posto_enfermagem
(
   pe_codigo bigserial NOT NULL, 
   pe_descricao text NOT NULL, 
   ate_codigo integer NOT NULL
) 
WITH (
  OIDS = FALSE
)
;
ALTER TABLE posto_enfermagem ADD COLUMN pe_status character(1);

