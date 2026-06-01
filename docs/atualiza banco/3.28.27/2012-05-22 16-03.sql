ALTER TABLE encaminhamento ADD COLUMN enc_internacao boolean NOT NULL DEFAULT 'F';
ALTER TABLE encaminhamento ADD COLUMN enc_urgencia boolean NOT NULL DEFAULT 'F';