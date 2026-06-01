DROP VIEW public.agendamentoteste;

ALTER TABLE aih DROP COLUMN med_solicitante_proc;
ALTER TABLE aih DROP COLUMN med_autorizador;
ALTER TABLE aih DROP COLUMN aih_cid_cod_princ;
ALTER TABLE aih DROP COLUMN aih_cid_cod_secun;
ALTER TABLE aih DROP COLUMN aih_cid_cod_terc;
ALTER TABLE aih DROP COLUMN aih_desc_proc_soli;

ALTER TABLE aih ADD COLUMN med_solicitante_proc bigint;
ALTER TABLE aih ADD COLUMN med_autorizador bigint;
ALTER TABLE aih ADD COLUMN aih_cid_cod_princ bigint;
ALTER TABLE aih ADD COLUMN aih_cid_cod_secun bigint;
ALTER TABLE aih ADD COLUMN aih_cid_cod_terc bigint;
ALTER TABLE aih ADD COLUMN aih_desc_proc_soli bigint;


CREATE OR REPLACE VIEW agendamentoteste AS 
 SELECT y.data, y.consultas, y.medicamentos, y.exame, y.aih, y.apac, COALESCE(y.consultas, 0::numeric) + COALESCE(y.medicamentos, 0::numeric) + COALESCE(y.exame, 0::numeric) + COALESCE(y.aih, 0::numeric) + COALESCE(y.apac, 0::numeric) AS valordetudo
   FROM ( SELECT ( SELECT sum(z.custosim) AS custosim
                   FROM ( SELECT sum(e.esp_custo) AS custosim, 
                                CASE
                                    WHEN a.age_atendido = 'A'::bpchar THEN 'SIM'::text
                                    WHEN a.age_atendido = 'S'::bpchar THEN 'SIM'::text
                                    ELSE NULL::text
                                END AS atendido
                           FROM agendamento a
                      JOIN especialidade e ON a.esp_codigo = e.esp_codigo
                 JOIN usuario u ON a.usu_codigo = u.usu_codigo
            JOIN medico m ON a.med_codigo = m.med_codigo
       JOIN unidade un ON u.uni_origem = un.uni_codigo
      WHERE a.age_item <> 'EX'::bpchar AND to_char(a.age_data::timestamp with time zone, 'mm/yyyy'::text) = x.data AND (a.age_atendido = ANY (ARRAY['A'::bpchar, 'S'::bpchar]))
      GROUP BY a.age_atendido) z
                  GROUP BY z.atendido) AS consultas, ( SELECT sum(faltosos.valor) AS somatudo
                   FROM ( SELECT sum(verifica_preco(xsw.codigo, xsw.set_saida, xsw.mov_data) * xsw.qtde) AS valor, to_char(xsw.mov_data::timestamp with time zone, 'mm/yyyy'::text) AS mes
                           FROM ( SELECT p.pro_codigo AS codigo, im.ite_quantidade AS qtde, m.mov_data, m.set_saida
                                   FROM movimento m
                              JOIN itens_movimento im ON m.mov_codigo = im.mov_codigo
                         JOIN produto p ON p.pro_codigo = im.pro_codigo
                        WHERE m.mov_saida = 'D'::bpchar AND to_char(m.mov_data::timestamp with time zone, 'mm/yyyy'::text) = x.data) xsw
                          GROUP BY to_char(xsw.mov_data::timestamp with time zone, 'mm/yyyy'::text)) faltosos) AS medicamentos, ( SELECT valoratendido.custosim AS somaexame
                   FROM ( SELECT sum(p.proc_valor) AS custosim, to_char(ael.agexl_data::timestamp with time zone, 'mm/yyyy'::text) AS datas
                           FROM agendamento_exame ae
                      JOIN agendamento_exame_lista ael ON ae.agex_codigo = ael.agex_codigo
                 JOIN procedimento p ON p.proc_codigo = ael.proc_codigo
            JOIN usuario u ON ae.usu_codigo = u.usu_codigo
       JOIN unidade un ON u.uni_origem = un.uni_codigo
      WHERE to_char(ael.agexl_data::timestamp with time zone, 'mm/yyyy'::text) = x.data
      GROUP BY to_char(ael.agexl_data::timestamp with time zone, 'mm/yyyy'::text)) valoratendido) AS exame, ( SELECT valor.valor
                   FROM ( SELECT to_char(xe.datainicial::timestamp with time zone, 'mm/yyyy'::text) AS dataaih, sum(xe.valorhospitalar + xe.servicoprofissional) AS valor
                           FROM ( SELECT a.aih_dataini AS datainicial, h.proc_vlsa AS valorhospitalar, h.proc_vlsp AS servicoprofissional
                                   FROM aih a
                              JOIN procedimento h ON a.aih_desc_proc_soli::bigint = h.proc_codigo
                         JOIN medico m ON a.med_codigo_solicitante = m.med_codigo
                    JOIN usuario u ON a.usu_codigo = u.usu_codigo
                   WHERE to_char(a.aih_dataini::timestamp with time zone, 'mm/yyyy'::text) = x.data) xe
                          GROUP BY to_char(xe.datainicial::timestamp with time zone, 'mm/yyyy'::text)) valor) AS aih, ( SELECT valor.valor
                   FROM ( SELECT to_char(xe.apac_dt_cadastro::timestamp with time zone, 'mm/yyyy'::text) AS dataapac, sum(xe.valorhospitalar + xe.servicoprofissional) AS valor
                           FROM ( SELECT a.apac_dt_cadastro, p.proc_vlsa AS valorhospitalar, p.proc_vlsp AS servicoprofissional
                                   FROM apac a
                              JOIN usuario u ON u.usu_codigo = a.pac_codigo
                         JOIN apac_procedimento ap ON a.apac_codigo = ap.apac_codigo
                    JOIN procedimento p ON ap.proc_codigo = p.proc_codigo
               JOIN unidade uni ON a.uni_sol_codigo = uni.uni_codigo
          JOIN medico m ON a.med_sol_codigo = m.med_codigo
         WHERE to_char(a.apac_dt_cadastro::timestamp with time zone, 'mm/yyyy'::text) = x.data) xe
                          GROUP BY to_char(xe.apac_dt_cadastro::timestamp with time zone, 'mm/yyyy'::text)) valor) AS apac, x.data
           FROM ( SELECT DISTINCT to_char(a.age_data::timestamp with time zone, 'mm/yyyy'::text) AS data
                   FROM agendamento a
                  WHERE a.age_item <> 'EX'::bpchar AND to_char(a.age_data::timestamp with time zone, 'yyyy'::text) = '2011'::text AND a.age_data >= '2011-01-01'::date AND a.age_data <= '2011-12-31'::date AND a.age_atendido <> 'T'::bpchar
                  ORDER BY to_char(a.age_data::timestamp with time zone, 'mm/yyyy'::text)) x) y;

ALTER TABLE agendamentoteste
  OWNER TO postgres;

