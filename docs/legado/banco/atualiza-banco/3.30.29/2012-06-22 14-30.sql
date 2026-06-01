CREATE TABLE veiculos
(
  vei_codigo bigserial NOT NULL,
  vei_lotacao integer,
  vei_marca character(200),
  vei_modelo character(200),
  vei_ano integer,
  vei_conservacao character(200),
  vei_combustivel character(200),
  vei_mediacombustivel character(4),
  vei_obs text,
  vei_placa character(10),
  vei_km character(20),
  CONSTRAINT veiculos_pkey PRIMARY KEY (vei_codigo )
)
WITH (
  OIDS=FALSE
);

CREATE TABLE roteiro
(
   rot_codigo bigserial, 
   rot_munorigem integer, 
   rot_mundestino integer, 
   rot_distanciakm integer, 
    PRIMARY KEY (rot_codigo)
) 
WITH (
  OIDS = FALSE
)
;


CREATE TABLE viagem
(
   via_codigo bigserial, 
   vei_codigo integer, 
   rot_codigo integer, 
   med_codigo integer, 
   via_hrsaida timestamp with time zone, 
   via_hrchegada timestamp with time zone, 
   via_localemb character(200), 
   via_localdesemb character(200), 
    PRIMARY KEY (via_codigo)
) 
WITH (
  OIDS = FALSE
)
;

CREATE TABLE paciente_viagem
(
   pcu_codigo bigserial, 
   usu_codigo integer, 
   via_codigo integer, 
   proc_codigo integer, 
   agee_codigo integer, 
   pcu_dthrcad timestamp with time zone, 
   usr_codigo integer, 
    PRIMARY KEY (pcu_codigo)
) 
WITH (
  OIDS = FALSE
)
;

CREATE TABLE despesas_viagem
(
   dev_codigo bigserial, 
   dev_descricao character(200), 
   dev_valor numeric(9,2), 
   med_codigo integer, 
   usu_codigo integer, 
   usr_codigo integer, 
   tipo character(2), 
   pcu_codigo integer, 
   via_codigo integer, 
    PRIMARY KEY (dev_codigo)
) 
WITH (
  OIDS = FALSE
)
;
COMMENT ON COLUMN despesas_viagem.tipo IS 'AD => Adiantamento de Viagem
AM => Almoco
JT => Janta
CM => Combustivel
DV => Devolucao';
