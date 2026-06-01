ALTER TABLE grupo_doencas ADD COLUMN gd_chave character(3);
ALTER TABLE usuario ADD COLUMN uni_codigo_obito bigint;

INSERT INTO grupo_doencas(gd_descricao,gd_chave) VALUES('VALVULOPATIAS REUMÁTICAS','VAL');
INSERT INTO grupos_cid (gd_codigo,cd10_codigo) VALUES ((select gd_codigo from grupo_doencas where gd_chave = 'VAL'),3669);
INSERT INTO grupos_cid (gd_codigo,cd10_codigo) VALUES ((select gd_codigo from grupo_doencas where gd_chave = 'VAL'),3675);
INSERT INTO grupos_cid (gd_codigo,cd10_codigo) VALUES ((select gd_codigo from grupo_doencas where gd_chave = 'VAL'),3681);
INSERT INTO grupos_cid (gd_codigo,cd10_codigo) VALUES ((select gd_codigo from grupo_doencas where gd_chave = 'VAL'),3687);
INSERT INTO grupos_cid (gd_codigo,cd10_codigo) VALUES ((select gd_codigo from grupo_doencas where gd_chave = 'VAL'),3694);

INSERT INTO grupo_doencas(gd_descricao,gd_chave) VALUES('ACIDENTE VASCULAR CEREBRAL','AVC');
INSERT INTO grupos_cid (gd_codigo,cd10_codigo) VALUES ((select gd_codigo from grupo_doencas where gd_chave = 'AVC'),3917);
INSERT INTO grupos_cid (gd_codigo,cd10_codigo) VALUES ((select gd_codigo from grupo_doencas where gd_chave = 'AVC'),3941);
INSERT INTO grupos_cid (gd_codigo,cd10_codigo) VALUES ((select gd_codigo from grupo_doencas where gd_chave = 'AVC'),3957);

INSERT INTO grupo_doencas(gd_descricao,gd_chave) VALUES('INFARTO AGUDO NO MIOCÁRDIO','IAM');
INSERT INTO grupos_cid (gd_codigo,cd10_codigo) VALUES ((select gd_codigo from grupo_doencas where gd_chave = 'IAM'),3723);

INSERT INTO grupo_doencas(gd_descricao,gd_chave) VALUES('DHEG (forma grave)','DHE');
INSERT INTO grupos_cid (gd_codigo,cd10_codigo) VALUES ((select gd_codigo from grupo_doencas where gd_chave = 'DHE'),6479);
INSERT INTO grupos_cid (gd_codigo,cd10_codigo) VALUES ((select gd_codigo from grupo_doencas where gd_chave = 'DHE'),6480);
INSERT INTO grupos_cid (gd_codigo,cd10_codigo) VALUES ((select gd_codigo from grupo_doencas where gd_chave = 'DHE'),6484);
INSERT INTO grupos_cid (gd_codigo,cd10_codigo) VALUES ((select gd_codigo from grupo_doencas where gd_chave = 'DHE'),6489);

INSERT INTO grupo_doencas(gd_descricao,gd_chave) VALUES('DOENÇA HEMOLÍTICA PERINATAL','DHP');
INSERT INTO grupos_cid (gd_codigo,cd10_codigo) VALUES ((select gd_codigo from grupo_doencas where gd_chave = 'DHP'),7120);

INSERT INTO grupo_doencas(gd_descricao,gd_chave) VALUES('FRATURAS DE COLO DE FÊMUR','FCF');
INSERT INTO grupos_cid (gd_codigo,cd10_codigo) VALUES ((select gd_codigo from grupo_doencas where gd_chave = 'FCF'),8866);
INSERT INTO grupos_cid (gd_codigo,cd10_codigo) VALUES ((select gd_codigo from grupo_doencas where gd_chave = 'FCF'),8867);

INSERT INTO grupo_doencas(gd_descricao,gd_chave) VALUES('MENINGITE E TUBERCULOSE','MET');
INSERT INTO grupos_cid (gd_codigo,cd10_codigo) VALUES ((select gd_codigo from grupo_doencas where gd_chave = 'MET'),90);
INSERT INTO grupos_cid (gd_codigo,cd10_codigo) VALUES ((select gd_codigo from grupo_doencas where gd_chave = 'MET'),2838);

INSERT INTO grupo_doencas(gd_descricao,gd_chave) VALUES('PNEUMONIA','PNE');
INSERT INTO grupos_cid (gd_codigo,cd10_codigo) VALUES ((select gd_codigo from grupo_doencas where gd_chave = 'PNE'),4149);
INSERT INTO grupos_cid (gd_codigo,cd10_codigo) VALUES ((select gd_codigo from grupo_doencas where gd_chave = 'PNE'),4177);

INSERT INTO grupo_doencas(gd_descricao,gd_chave) VALUES('CITOLOGIA ONCOTICA','CO');
INSERT INTO grupos_cid (gd_codigo,cd10_codigo) VALUES ((select gd_codigo from grupo_doencas where gd_chave = 'VAL'),1189);

INSERT INTO grupo_doencas(gd_descricao,gd_chave) VALUES('HANSENIASE','HAN');
INSERT INTO grupos_cid (gd_codigo,cd10_codigo) VALUES ((select gd_codigo from grupo_doencas where gd_chave = 'HAN'),166);
INSERT INTO grupos_cid (gd_codigo,cd10_codigo) VALUES ((select gd_codigo from grupo_doencas where gd_chave = 'HAN'),168);
INSERT INTO grupos_cid (gd_codigo,cd10_codigo) VALUES ((select gd_codigo from grupo_doencas where gd_chave = 'HAN'),170);
INSERT INTO grupos_cid (gd_codigo,cd10_codigo) VALUES ((select gd_codigo from grupo_doencas where gd_chave = 'HAN'),172);
INSERT INTO grupos_cid (gd_codigo,cd10_codigo) VALUES ((select gd_codigo from grupo_doencas where gd_chave = 'HAN'),174);
INSERT INTO grupos_cid (gd_codigo,cd10_codigo) VALUES ((select gd_codigo from grupo_doencas where gd_chave = 'HAN'),167);
INSERT INTO grupos_cid (gd_codigo,cd10_codigo) VALUES ((select gd_codigo from grupo_doencas where gd_chave = 'HAN'),171);
INSERT INTO grupos_cid (gd_codigo,cd10_codigo) VALUES ((select gd_codigo from grupo_doencas where gd_chave = 'HAN'),173);
INSERT INTO grupos_cid (gd_codigo,cd10_codigo) VALUES ((select gd_codigo from grupo_doencas where gd_chave = 'HAN'),872);
INSERT INTO grupos_cid (gd_codigo,cd10_codigo) VALUES ((select gd_codigo from grupo_doencas where gd_chave = 'HAN'),169);

INSERT INTO grupo_doencas(gd_descricao,gd_chave) VALUES('DIARREIA','DIA');
INSERT INTO grupos_cid (gd_codigo,cd10_codigo) VALUES ((select gd_codigo from grupo_doencas where gd_chave = 'DIA'),4684);
INSERT INTO grupos_cid (gd_codigo,cd10_codigo) VALUES ((select gd_codigo from grupo_doencas where gd_chave = 'DIA'),68);
INSERT INTO grupos_cid (gd_codigo,cd10_codigo) VALUES ((select gd_codigo from grupo_doencas where gd_chave = 'DIA'),4685);
INSERT INTO grupos_cid (gd_codigo,cd10_codigo) VALUES ((select gd_codigo from grupo_doencas where gd_chave = 'DIA'),4688);
INSERT INTO grupos_cid (gd_codigo,cd10_codigo) VALUES ((select gd_codigo from grupo_doencas where gd_chave = 'DIA'),7202);

INSERT INTO grupo_doencas(gd_descricao,gd_chave) VALUES('INFECÇÃO RESPIRATÓRIA','IRE');
INSERT INTO grupos_cid (gd_codigo,cd10_codigo) VALUES ((select gd_codigo from grupo_doencas where gd_chave = 'IRE'),4268);

INSERT INTO grupo_doencas(gd_descricao,gd_chave) VALUES('VIOLÊNCIA','VIO');
INSERT INTO grupos_cid (gd_codigo,cd10_codigo) VALUES ((select gd_codigo from grupo_doencas where gd_chave = 'VIO'),8120);

ALTER TABLE avaliacao_puerperal
   ALTER COLUMN ava_peso TYPE numeric;

