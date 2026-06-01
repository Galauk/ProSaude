CREATE SEQUENCE seq_eve_codigo
   INCREMENT 1
   START 1
   MINVALUE 1
   MAXVALUE 10000000000
   CACHE 1;
   
   
ALTER TABLE evento
   ALTER COLUMN eve_codigo SET DEFAULT nextval(('seq_eve_codigo'::text)::regclass);
