ALTER TABLE procedimento_atendimento
   ALTER COLUMN ate_codigo DROP NOT NULL;

COMMENT ON COLUMN posto_enfermagem.pe_status IS 'A - Atendido
E - Espera';

ALTER TABLE posto_enfermagem ADD COLUMN pe_descricao_enfermagem text;

ALTER TABLE posto_enfermagem ADD COLUMN pe_observacao text;
