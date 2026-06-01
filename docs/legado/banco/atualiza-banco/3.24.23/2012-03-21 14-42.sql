ALTER TABLE leito ADD COLUMN qua_codigo_temp bigint;

-- reza pra passar
UPDATE leito SET qua_codigo_temp = qua_codigo::bigint;

ALTER TABLE leito DROP COLUMN qua_codigo;
ALTER TABLE leito RENAME qua_codigo_temp  TO qua_codigo;
ALTER TABLE leito ALTER COLUMN qua_codigo SET NOT NULL;