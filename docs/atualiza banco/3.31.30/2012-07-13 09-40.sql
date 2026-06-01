CREATE TABLE raiox
(
  rai_codigo bigserial NOT NULL,
  rai_imagem oid,
  rai_data_insert date,
  usr_codigo bigint,
  CONSTRAINT raiox_pkey PRIMARY KEY (rai_codigo )
)
WITH (
  OIDS=FALSE
);