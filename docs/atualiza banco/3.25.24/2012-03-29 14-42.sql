ALTER TABLE pre_consulta ADD COLUMN esp_codigo bigint;

COMMENT ON COLUMN pre_consulta.esp_codigo IS 'Um enfermeiro pode ter mais de uma especialidade, ou a especialidade pode ser alterada. O histórico deve exibir a especialidade que o usr estava logado no momento do atendimento.';