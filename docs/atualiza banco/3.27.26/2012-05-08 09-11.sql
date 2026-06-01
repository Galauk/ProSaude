ALTER TABLE hiperdia RENAME med_codigo  TO usr_codigo;
ALTER TABLE grade_dia ADD COLUMN grad_alterada boolean DEFAULT 'f';
ALTER TABLE grade_mes ADD COLUMN gram_alterada boolean DEFAULT 'f';
