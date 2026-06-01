ALTER TABLE leito_grade RENAME lgra_dias  TO lgra_repeticoes;
ALTER TABLE leito_grade_modelo RENAME lgm_dias  TO lgm_repeticoes;
ALTER TABLE leito_grade ADD COLUMN lgra_status smallint NOT NULL DEFAULT 1;

COMMENT ON COLUMN leito_grade.lgra_status IS 'ATIVO = 1;
CONCLUIDO = 2;
CANCELADO = 0;';

ALTER TABLE leito_dispensacao DROP COLUMN ldis_hora;
ALTER TABLE leito_dispensacao RENAME ldis_data  TO ldis_datahora;
