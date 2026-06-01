CREATE TABLE mais_acessados
(
   ma_codigo bigserial, 
   ma_url character varying(250) NOT NULL, 
   ma_contador integer NOT NULL DEFAULT 0
) 
WITH (
  OIDS = FALSE
)
;
ALTER TABLE mais_acessados ADD PRIMARY KEY (ma_codigo);
ALTER TABLE mais_acessados ADD COLUMN ma_print character varying(50);
