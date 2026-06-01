ALTER TABLE agendamento_externo
   ALTER COLUMN med_codigo DROP NOT NULL;
ALTER TABLE agendamento_externo ADD COLUMN med_codigo_solicitante bigint;
