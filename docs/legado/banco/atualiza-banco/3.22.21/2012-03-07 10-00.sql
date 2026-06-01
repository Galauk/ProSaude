ALTER TABLE controlefracionado DROP COLUMN ite_dose;
ALTER TABLE controlefracionado ADD COLUMN cont_perda integer;
ALTER TABLE controlefracionado ADD COLUMN cont_perda_motivo integer;

COMMENT ON COLUMN controlefracionado.cont_perda_motivo IS '1: Quebra de frasco
2: Falta de energia
3: Falha no equipamento
4: Validade vencida
5: Procedimento inadequado
6: Falha no transporte
7: Outros motivos';