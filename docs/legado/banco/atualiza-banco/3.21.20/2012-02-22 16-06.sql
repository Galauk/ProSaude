
ALTER TABLE posto_enfermagem ADD PRIMARY KEY (pe_codigo);
ALTER TABLE procedimento_atendimento ADD COLUMN pc_codigo integer;
ALTER TABLE procedimento_atendimento ADD COLUMN pe_codigo integer;
ALTER TABLE procedimento_atendimento ADD COLUMN usr_codigo integer;
ALTER TABLE procedimento_atendimento ADD FOREIGN KEY (pc_codigo) REFERENCES pre_consulta (pc_codigo) ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE procedimento_atendimento ADD FOREIGN KEY (pe_codigo) REFERENCES posto_enfermagem (pe_codigo) ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE procedimento_atendimento ADD FOREIGN KEY (usr_codigo) REFERENCES usuarios (usr_codigo) ON UPDATE NO ACTION ON DELETE NO ACTION;

