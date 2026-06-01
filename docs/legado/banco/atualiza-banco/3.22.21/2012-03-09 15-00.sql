DROP VIEW mov_naoconsolid;
DROP VIEW mov_naoconsolid2;
DROP VIEW mov_naoconsolid_antiga;
DROP VIEW mov_naoconsolid_rel;
DROP VIEW req_atendido;
DROP VIEW req_dispensado;
DROP VIEW req_naoconsolid;
DROP VIEW reqtransf_atendido;
DROP VIEW reqtransf_dispensado;
DROP VIEW reqtransf_naoconsolid;
DROP VIEW v_consumo;
DROP VIEW v_consumo_tp_mov;
DROP VIEW v_inventario;
DROP VIEW v_movimentacao_all;
DROP VIEW v_produto_centroestoc;
DROP VIEW v_movimentacao;
ALTER TABLE produto
   ALTER COLUMN pro_nome TYPE character varying(255);




 CREATE OR REPLACE VIEW v_movimentacao AS 
         SELECT to_char(mov.mov_data::timestamp with time zone, 'DD/MM/YYYY'::text) AS data, mov.mov_data, mov.mov_nr_nota, get_setor(mov.set_saida) AS setor, mov.mov_saida AS tipomovim, it.pro_codigo, produto.pro_nome, mov.mov_codigo, mov.set_saida AS codsetor, mov.mov_tipo, mov.mov_saida AS operacao, '-'::text AS sinal, mov.set_entrada AS codsetorsolicit, get_setor(mov.set_entrada) AS nomesetorsolicit, 
                CASE
                    WHEN mov.mov_saida = 'S'::bpchar THEN 'SAIDA DE REQUISICAO PARA '::text || get_setor(mov.set_saida)::text
                    WHEN mov.mov_saida = 'D'::bpchar THEN ('DISPENSACAO PARA PACIENTE '::text || ' - '::text) || (( SELECT substr(usuario.usu_nome::text, 1, 20) AS substr
                       FROM usuario
                      WHERE usuario.usu_codigo = mov.usu_codigo))
                    WHEN mov.mov_saida = 'P'::bpchar THEN 'SAIDA DE PERMUTA'::text
                    WHEN mov.mov_saida = 'I'::bpchar THEN 'SAIDA POR INVENTARIO'::text
                    WHEN mov.mov_saida = 'A'::bpchar THEN 'SAIDA POR AJUSTE'::text
                    WHEN mov.mov_saida = 'R'::bpchar THEN 'SAIDA POR PERDAS'::text
                    WHEN mov.mov_saida = 'O'::bpchar THEN 'OUTRAS SAIDAS'::text
                    WHEN mov.mov_saida = 'M'::bpchar THEN 'SAIDAS DE EMPRESTIMOS'::text
                    WHEN mov.mov_saida = 'T'::bpchar THEN 'SAIDA POR TRANSFERENCIA PARA '::text || get_setor(mov.set_saida)::text
                    ELSE NULL::text
                END AS desc_movimentacao, it.ite_quantidade, it.ite_vlrunit, mov.age_codigo, mov.usu_codigo
           FROM movimento mov, itens_movimento it, produto
          WHERE mov.mov_codigo = it.mov_codigo AND it.pro_codigo = produto.pro_codigo AND (mov.mov_tipo = 'S'::bpchar OR mov.mov_tipo = 'T'::bpchar) AND it.ite_consolidado = 'S'::bpchar
UNION ALL 
         SELECT to_char(mov.mov_data::timestamp with time zone, 'DD/MM/YYYY'::text) AS data, mov.mov_data, mov.mov_nr_nota, get_setor(mov.set_entrada) AS setor, mov.mov_entrada AS tipomovim, it.pro_codigo, produto.pro_nome, mov.mov_codigo, mov.set_entrada AS codsetor, mov.mov_tipo, mov.mov_entrada AS operacao, '+'::text AS sinal, mov.set_saida AS codsetorsolicit, get_setor(mov.set_saida) AS nomesetorsolicit, 
                CASE
                    WHEN mov.mov_entrada = 'E'::bpchar THEN 'ENTRADA DE NOTA FISCAL'::text
                    WHEN mov.mov_entrada = 'A'::bpchar THEN 'ENTRADA DE AJUSTE '::text
                    WHEN mov.mov_entrada = 'M'::bpchar THEN 'ENTRADA DE EMPRESTIMO'::text
                    WHEN mov.mov_entrada = 'I'::bpchar THEN 'ENTRADA DE INVENTARIO'::text
                    WHEN mov.mov_entrada = 'D'::bpchar THEN 'ENTRADA DE DOACAO'::text
                    WHEN mov.mov_entrada = 'P'::bpchar THEN 'ENTRADA DE PERMUTA'::text
                    WHEN mov.mov_entrada = 'O'::bpchar THEN 'OUTRAS ENTRADAS'::text
                    WHEN mov.mov_entrada = 'T'::bpchar THEN 'ENTRADA POR TRANSFERENCIA DE'::text || get_setor(mov.set_entrada)::text
                    WHEN mov.mov_entrada = 'v'::bpchar THEN 'DEVOLUCAO DE SETORES'::text
                    ELSE NULL::text
                END AS desc_movimentacao, it.ite_quantidade, it.ite_vlrunit, mov.age_codigo, mov.usu_codigo
           FROM movimento mov, itens_movimento it, produto
          WHERE mov.mov_codigo = it.mov_codigo AND it.pro_codigo = produto.pro_codigo AND (mov.mov_tipo = 'E'::bpchar OR mov.mov_tipo = 'T'::bpchar) AND it.ite_consolidado = 'S'::bpchar
  ORDER BY 2, 10;

ALTER TABLE v_movimentacao
  OWNER TO postgres;
GRANT ALL ON TABLE v_movimentacao TO postgres; 

CREATE OR REPLACE VIEW v_produto_centroestoc AS 
        ( SELECT DISTINCT v_movimentacao.codsetor, v_movimentacao.setor AS nomesetor, v_movimentacao.pro_codigo, v_movimentacao.pro_nome
           FROM v_movimentacao
          ORDER BY v_movimentacao.codsetor, v_movimentacao.setor, v_movimentacao.pro_codigo, v_movimentacao.pro_nome)
UNION 
        ( SELECT DISTINCT saldo.set_codigo AS codsetor, get_setor(saldo.set_codigo) AS nomesetor, saldo.pro_codigo, get_produto(saldo.pro_codigo) AS pro_nome
           FROM saldo
          ORDER BY saldo.set_codigo, get_setor(saldo.set_codigo), saldo.pro_codigo, get_produto(saldo.pro_codigo));

ALTER TABLE v_produto_centroestoc
  OWNER TO postgres;
GRANT ALL ON TABLE v_produto_centroestoc TO postgres;
GRANT ALL ON TABLE v_produto_centroestoc TO public;


CREATE OR REPLACE VIEW mov_naoconsolid AS 
         SELECT to_char(mov.mov_data::timestamp with time zone, 'DD/MM/YYYY'::text) AS data, mov.mov_data, mov.mov_nr_nota, get_setor(mov.set_saida) AS setor, mov.mov_saida AS tipomovim, it.pro_codigo, produto.pro_nome, mov.mov_codigo, mov.set_saida AS codsetor, mov.mov_tipo, mov.mov_saida AS operacao, '-'::text AS sinal, mov.set_entrada AS codsetorsolicit, get_setor(mov.set_entrada) AS nomesetorsolicit, it.ite_codigo, 
                CASE
                    WHEN mov.mov_saida = 'S'::bpchar THEN 'SAIDA DE REQUISICAO PARA '::text || get_setor(mov.set_saida)::text
                    WHEN mov.mov_saida = 'D'::bpchar THEN ('DISPENSACAO PARA PACIENTE '::text || ' - '::text) || (( SELECT substr(usuario.usu_nome::text, 1, 20) AS substr
                       FROM usuario
                      WHERE usuario.usu_codigo = mov.usu_codigo))
                    WHEN mov.mov_saida = 'P'::bpchar THEN 'SAIDA DE PERMUTA'::text
                    WHEN mov.mov_saida = 'I'::bpchar THEN 'SAIDA POR INVENTARIO'::text
                    WHEN mov.mov_saida = 'A'::bpchar THEN 'SAIDA POR AJUSTE'::text
                    WHEN mov.mov_saida = 'R'::bpchar THEN 'SAIDA POR PERDAS'::text
                    WHEN mov.mov_saida = 'O'::bpchar THEN 'OUTRAS SAIDAS'::text
                    WHEN mov.mov_saida = 'M'::bpchar THEN 'SAIDA POR EMPRESTIMO'::text
                    WHEN mov.mov_saida = 'T'::bpchar THEN ('SAIDA POR TRANSFERENCIA PARA '::text || ' '::text) || get_setor(mov.set_entrada)::text
                    ELSE NULL::text
                END AS desc_movimentacao, it.ite_quantidade, it.ite_qtde_solicitada
           FROM movimento mov, itens_movimento it, produto
          WHERE mov.mov_codigo = it.mov_codigo AND it.pro_codigo = produto.pro_codigo AND (mov.mov_tipo = 'S'::bpchar OR mov.mov_tipo = 'T'::bpchar) AND it.ite_consolidado = 'N'::bpchar
UNION 
         SELECT to_char(mov.mov_data::timestamp with time zone, 'DD/MM/YYYY'::text) AS data, mov.mov_data, mov.mov_nr_nota, get_setor(mov.set_entrada) AS setor, mov.mov_entrada AS tipomovim, it.pro_codigo, produto.pro_nome, mov.mov_codigo, mov.set_entrada AS codsetor, mov.mov_tipo, mov.mov_entrada AS operacao, '+'::text AS sinal, mov.set_saida AS codsetorsolicit, get_setor(mov.set_saida) AS nomesetorsolicit, it.ite_codigo, 
                CASE
                    WHEN mov.mov_entrada = 'E'::bpchar THEN 'ENTRADA DE NOTA FISCAL'::text
                    WHEN mov.mov_entrada = 'A'::bpchar THEN 'ENTRADA DE AJUSTE '::text
                    WHEN mov.mov_entrada = 'M'::bpchar THEN 'ENTRADA DE EMPRESTIMO'::text
                    WHEN mov.mov_entrada = 'I'::bpchar THEN 'ENTRADA DE INVENTARIO'::text
                    WHEN mov.mov_entrada = 'D'::bpchar THEN 'ENTRADA DE DOACAO'::text
                    WHEN mov.mov_entrada = 'P'::bpchar THEN 'ENTRADA DE PERMUTA'::text
                    WHEN mov.mov_entrada = 'O'::bpchar THEN 'OUTRAS ENTRADAS'::text
                    WHEN mov.mov_entrada = 'T'::bpchar THEN 'ENTRADA POR TRANSFERENCIA DE'::text || get_setor(mov.set_entrada)::text
                    ELSE NULL::text
                END AS desc_movimentacao, it.ite_quantidade, it.ite_qtde_solicitada
           FROM movimento mov, itens_movimento it, produto
          WHERE mov.mov_codigo = it.mov_codigo AND it.pro_codigo = produto.pro_codigo AND mov.mov_tipo = 'E'::bpchar AND it.ite_consolidado = 'N'::bpchar;

ALTER TABLE mov_naoconsolid
  OWNER TO postgres;
GRANT ALL ON TABLE mov_naoconsolid TO postgres;


CREATE OR REPLACE VIEW mov_naoconsolid2 AS 
         SELECT to_char(mov.mov_data::timestamp with time zone, 'DD/MM/YYYY'::text) AS data, mov.mov_data, mov.mov_nr_nota, get_setor(mov.set_saida) AS setor, mov.mov_saida AS tipomovim, it.pro_codigo, produto.pro_nome, mov.mov_codigo, mov.set_saida AS codsetor, mov.mov_tipo, mov.mov_saida AS operacao, '-'::text AS sinal, mov.set_entrada AS codsetorsolicit, get_setor(mov.set_entrada) AS nomesetorsolicit, it.ite_codigo, 
                CASE
                    WHEN mov.mov_saida = 'S'::bpchar THEN 'SAIDA DE REQUISICAO PARA '::text || get_setor(mov.set_saida)::text
                    WHEN mov.mov_saida = 'D'::bpchar THEN ('DISPENSACAO PARA PACIENTE '::text || ' - '::text) || (( SELECT substr(usuario.usu_nome::text, 1, 20) AS substr
                       FROM usuario
                      WHERE usuario.usu_codigo = mov.usu_codigo))
                    WHEN mov.mov_saida = 'P'::bpchar THEN 'SAIDA DE PERMUTA'::text
                    WHEN mov.mov_saida = 'I'::bpchar THEN 'SAIDA POR INVENTARIO'::text
                    WHEN mov.mov_saida = 'A'::bpchar THEN 'SAIDA POR AJUSTE'::text
                    WHEN mov.mov_saida = 'R'::bpchar THEN 'SAIDA POR PERDAS'::text
                    WHEN mov.mov_saida = 'O'::bpchar THEN 'OUTRAS SAIDAS'::text
                    WHEN mov.mov_saida = 'M'::bpchar THEN 'SAIDA POR EMPRESTIMO'::text
                    WHEN mov.mov_saida = 'T'::bpchar THEN ('SAIDA POR TRANSFERENCIA PARA '::text || ' '::text) || get_setor(mov.set_entrada)::text
                    ELSE NULL::text
                END AS desc_movimentacao, it.ite_quantidade, it.ite_qtde_solicitada
           FROM movimento mov, itens_movimento it, produto
          WHERE mov.mov_codigo = it.mov_codigo AND it.pro_codigo = produto.pro_codigo AND (mov.mov_tipo = 'S'::bpchar OR mov.mov_tipo = 'T'::bpchar) AND it.ite_consolidado = 'N'::bpchar
UNION 
         SELECT to_char(mov.mov_data::timestamp with time zone, 'DD/MM/YYYY'::text) AS data, mov.mov_data, mov.mov_nr_nota, get_setor(mov.set_entrada) AS setor, mov.mov_entrada AS tipomovim, it.pro_codigo, produto.pro_nome, mov.mov_codigo, mov.set_entrada AS codsetor, mov.mov_tipo, mov.mov_entrada AS operacao, '+'::text AS sinal, mov.set_saida AS codsetorsolicit, get_setor(mov.set_saida) AS nomesetorsolicit, it.ite_codigo, 
                CASE
                    WHEN mov.mov_entrada = 'E'::bpchar THEN 'ENTRADA DE NOTA FISCAL'::text
                    WHEN mov.mov_entrada = 'A'::bpchar THEN 'ENTRADA DE AJUSTE '::text
                    WHEN mov.mov_entrada = 'M'::bpchar THEN 'ENTRADA DE EMPRESTIMO'::text
                    WHEN mov.mov_entrada = 'I'::bpchar THEN 'ENTRADA DE INVENTARIO'::text
                    WHEN mov.mov_entrada = 'D'::bpchar THEN 'ENTRADA DE DOACAO'::text
                    WHEN mov.mov_entrada = 'P'::bpchar THEN 'ENTRADA DE PERMUTA'::text
                    WHEN mov.mov_entrada = 'O'::bpchar THEN 'OUTRAS ENTRADAS'::text
                    WHEN mov.mov_entrada = 'T'::bpchar THEN 'ENTRADA POR TRANSFERENCIA DE'::text || get_setor(mov.set_entrada)::text
                    ELSE NULL::text
                END AS desc_movimentacao, it.ite_quantidade, it.ite_qtde_solicitada
           FROM movimento mov, itens_movimento it, produto
          WHERE mov.mov_codigo = it.mov_codigo AND it.pro_codigo = produto.pro_codigo AND (mov.mov_tipo = 'E'::bpchar OR mov.mov_tipo = 'T'::bpchar) AND it.ite_consolidado = 'N'::bpchar;

ALTER TABLE mov_naoconsolid2
  OWNER TO postgres;
GRANT ALL ON TABLE mov_naoconsolid2 TO postgres;

CREATE OR REPLACE VIEW mov_naoconsolid_antiga AS 
         SELECT to_char(mov.mov_data::timestamp with time zone, 'DD/MM/YYYY'::text) AS data, mov.mov_data, mov.mov_nr_nota, get_setor(mov.set_saida) AS setor, mov.mov_saida AS tipomovim, it.pro_codigo, produto.pro_nome, mov.mov_codigo, mov.set_saida AS codsetor, mov.mov_tipo, mov.mov_saida AS operacao, '-'::text AS sinal, mov.set_entrada AS codsetorsolicit, get_setor(mov.set_entrada) AS nomesetorsolicit, it.ite_codigo, 
                CASE
                    WHEN mov.mov_saida = 'S'::bpchar THEN 'SAIDA DE REQUISICAO PARA '::text || get_setor(mov.set_saida)::text
                    WHEN mov.mov_saida = 'D'::bpchar THEN ('DISPENSACAO PARA PACIENTE '::text || ' - '::text) || (( SELECT substr(usuario.usu_nome::text, 1, 20) AS substr
                       FROM usuario
                      WHERE usuario.usu_codigo = mov.usu_codigo))
                    WHEN mov.mov_saida = 'P'::bpchar THEN 'SAIDA DE PERMUTA'::text
                    WHEN mov.mov_saida = 'I'::bpchar THEN 'SAIDA POR INVENTARIO'::text
                    WHEN mov.mov_saida = 'A'::bpchar THEN 'SAIDA POR AJUSTE'::text
                    WHEN mov.mov_saida = 'R'::bpchar THEN 'SAIDA POR PERDAS'::text
                    WHEN mov.mov_saida = 'O'::bpchar THEN 'OUTRAS SAIDAS'::text
                    WHEN mov.mov_saida = 'M'::bpchar THEN 'SAIDA POR EMPRESTIMO'::text
                    WHEN mov.mov_saida = 'T'::bpchar THEN ('SAIDA POR TRANSFERENCIA PARA '::text || ' '::text) || get_setor(mov.set_entrada)::text
                    ELSE NULL::text
                END AS desc_movimentacao, it.ite_quantidade, it.ite_qtde_solicitada
           FROM movimento mov, itens_movimento it, produto
          WHERE mov.mov_codigo = it.mov_codigo AND it.pro_codigo = produto.pro_codigo AND (mov.mov_tipo = 'S'::bpchar OR mov.mov_tipo = 'T'::bpchar) AND it.ite_consolidado = 'N'::bpchar
UNION 
         SELECT to_char(mov.mov_data::timestamp with time zone, 'DD/MM/YYYY'::text) AS data, mov.mov_data, mov.mov_nr_nota, get_setor(mov.set_entrada) AS setor, mov.mov_entrada AS tipomovim, it.pro_codigo, produto.pro_nome, mov.mov_codigo, mov.set_entrada AS codsetor, mov.mov_tipo, mov.mov_entrada AS operacao, '+'::text AS sinal, mov.set_saida AS codsetorsolicit, get_setor(mov.set_saida) AS nomesetorsolicit, it.ite_codigo, 
                CASE
                    WHEN mov.mov_entrada = 'E'::bpchar THEN 'ENTRADA DE NOTA FISCAL'::text
                    WHEN mov.mov_entrada = 'A'::bpchar THEN 'ENTRADA DE AJUSTE '::text
                    WHEN mov.mov_entrada = 'M'::bpchar THEN 'ENTRADA DE EMPRESTIMO'::text
                    WHEN mov.mov_entrada = 'I'::bpchar THEN 'ENTRADA DE INVENTARIO'::text
                    WHEN mov.mov_entrada = 'D'::bpchar THEN 'ENTRADA DE DOACAO'::text
                    WHEN mov.mov_entrada = 'P'::bpchar THEN 'ENTRADA DE PERMUTA'::text
                    WHEN mov.mov_entrada = 'O'::bpchar THEN 'OUTRAS ENTRADAS'::text
                    WHEN mov.mov_entrada = 'T'::bpchar THEN 'ENTRADA POR TRANSFERENCIA DE'::text || get_setor(mov.set_entrada)::text
                    ELSE NULL::text
                END AS desc_movimentacao, it.ite_quantidade, it.ite_qtde_solicitada
           FROM movimento mov, itens_movimento it, produto
          WHERE mov.mov_codigo = it.mov_codigo AND it.pro_codigo = produto.pro_codigo AND mov.mov_tipo = 'E'::bpchar AND it.ite_consolidado = 'N'::bpchar;

ALTER TABLE mov_naoconsolid_antiga
  OWNER TO postgres;
GRANT ALL ON TABLE mov_naoconsolid_antiga TO postgres;



CREATE OR REPLACE VIEW mov_naoconsolid_rel AS 
         SELECT to_char(mov.mov_data::timestamp with time zone, 'DD/MM/YYYY'::text) AS data, mov.mov_data, mov.mov_nr_nota, get_setor(mov.set_saida) AS setor, mov.mov_saida AS tipomovim, it.pro_codigo, produto.pro_nome, mov.mov_codigo, mov.set_saida AS codsetor, mov.mov_tipo, mov.mov_saida AS operacao, '-'::text AS sinal, mov.set_entrada AS codsetorsolicit, get_setor(mov.set_entrada) AS nomesetorsolicit, it.ite_codigo, 
                CASE
                    WHEN mov.mov_saida = 'S'::bpchar THEN 'SAIDA DE REQUISICAO PARA '::text || get_setor(mov.set_saida)::text
                    WHEN mov.mov_saida = 'D'::bpchar THEN ('DISPENSACAO PARA PACIENTE '::text || ' - '::text) || (( SELECT substr(usuario.usu_nome::text, 1, 20) AS substr
                       FROM usuario
                      WHERE usuario.usu_codigo = mov.usu_codigo))
                    WHEN mov.mov_saida = 'P'::bpchar THEN 'SAIDA DE PERMUTA'::text
                    WHEN mov.mov_saida = 'I'::bpchar THEN 'SAIDA POR INVENTARIO'::text
                    WHEN mov.mov_saida = 'A'::bpchar THEN 'SAIDA POR AJUSTE'::text
                    WHEN mov.mov_saida = 'R'::bpchar THEN 'SAIDA POR PERDAS'::text
                    WHEN mov.mov_saida = 'O'::bpchar THEN 'OUTRAS SAIDAS'::text
                    WHEN mov.mov_saida = 'M'::bpchar THEN 'SAIDA POR EMPRESTIMO'::text
                    WHEN mov.mov_saida = 'T'::bpchar THEN ('SAIDA POR TRANSFERENCIA PARA '::text || ' '::text) || get_setor(mov.set_entrada)::text
                    ELSE NULL::text
                END AS desc_movimentacao, it.ite_quantidade, it.ite_qtde_solicitada
           FROM movimento mov, itens_movimento it, produto
          WHERE mov.mov_codigo = it.mov_codigo AND it.pro_codigo = produto.pro_codigo AND (mov.mov_tipo = 'S'::bpchar OR mov.mov_tipo = 'T'::bpchar) AND it.ite_consolidado = 'N'::bpchar
UNION 
         SELECT to_char(mov.mov_data::timestamp with time zone, 'DD/MM/YYYY'::text) AS data, mov.mov_data, mov.mov_nr_nota, get_setor(mov.set_entrada) AS setor, mov.mov_entrada AS tipomovim, it.pro_codigo, produto.pro_nome, mov.mov_codigo, mov.set_entrada AS codsetor, mov.mov_tipo, mov.mov_entrada AS operacao, '+'::text AS sinal, mov.set_saida AS codsetorsolicit, get_setor(mov.set_saida) AS nomesetorsolicit, it.ite_codigo, 
                CASE
                    WHEN mov.mov_entrada = 'E'::bpchar THEN 'ENTRADA DE NOTA FISCAL'::text
                    WHEN mov.mov_entrada = 'A'::bpchar THEN 'ENTRADA DE AJUSTE '::text
                    WHEN mov.mov_entrada = 'M'::bpchar THEN 'ENTRADA DE EMPRESTIMO'::text
                    WHEN mov.mov_entrada = 'I'::bpchar THEN 'ENTRADA DE INVENTARIO'::text
                    WHEN mov.mov_entrada = 'D'::bpchar THEN 'ENTRADA DE DOACAO'::text
                    WHEN mov.mov_entrada = 'P'::bpchar THEN 'ENTRADA DE PERMUTA'::text
                    WHEN mov.mov_entrada = 'O'::bpchar THEN 'OUTRAS ENTRADAS'::text
                    WHEN mov.mov_entrada = 'T'::bpchar THEN 'ENTRADA POR TRANSFERENCIA DE'::text || get_setor(mov.set_entrada)::text
                    ELSE NULL::text
                END AS desc_movimentacao, it.ite_quantidade, it.ite_qtde_solicitada
           FROM movimento mov, itens_movimento it, produto
          WHERE mov.mov_codigo = it.mov_codigo AND it.pro_codigo = produto.pro_codigo AND (mov.mov_tipo = 'E'::bpchar OR mov.mov_tipo = 'T'::bpchar) AND it.ite_consolidado = 'N'::bpchar;

ALTER TABLE mov_naoconsolid_rel
  OWNER TO postgres;
GRANT ALL ON TABLE mov_naoconsolid_rel TO postgres;


CREATE OR REPLACE VIEW req_atendido AS 
 SELECT to_char(req.req_data::timestamp with time zone, 'DD/MM/YYYY'::text) AS data, req.req_data, req.req_nr_nota, get_setor(req.set_saida) AS setor, req.req_saida AS tipomovim, it.pro_codigo, produto.pro_nome, req.req_codigo, req.set_saida AS codsetor, req.req_tipo, req.req_saida AS operacao, '-' AS sinal, req.set_entrada AS codsetorsolicit, get_setor(req.set_entrada) AS nomesetorsolicit, it.ireq_codigo, 
        CASE
            WHEN req.req_saida = 'S'::bpchar THEN 'SAIDA DE REQUISICAO PARA '::text || get_setor(req.set_saida)::text
            ELSE NULL::text
        END AS desc_movimentacao, it.ireq_quantidade, it.ireq_qtde_solicitada, 
        CASE
            WHEN it.ireq_consolidado = 'R'::bpchar THEN 'REQUISITADO '::text
            WHEN it.ireq_consolidado = 'D'::bpchar THEN 'DISPENSADO  '::text
            WHEN it.ireq_consolidado = 'A'::bpchar THEN 'ATENDIDO    '::text
            WHEN it.ireq_consolidado = 'C'::bpchar THEN 'CANCELADO   '::text
            ELSE NULL::text
        END AS desc_status, it.ireq_consolidado
   FROM requisicao req, itens_requisicao it, produto
  WHERE req.req_codigo = it.req_codigo AND it.pro_codigo = produto.pro_codigo AND req.req_tipo = 'S'::bpchar AND it.ireq_consolidado = 'A'::bpchar;

ALTER TABLE req_atendido
  OWNER TO postgres;
GRANT ALL ON TABLE req_atendido TO postgres;


  CREATE OR REPLACE VIEW req_dispensado AS 
 SELECT to_char(req.req_data::timestamp with time zone, 'DD/MM/YYYY'::text) AS data, req.req_data, req.req_nr_nota, get_setor(req.set_saida) AS setor, req.req_saida AS tipomovim, it.pro_codigo, produto.pro_nome, req.req_codigo, req.set_saida AS codsetor, req.req_tipo, req.req_saida AS operacao, '-' AS sinal, req.set_entrada AS codsetorsolicit, get_setor(req.set_entrada) AS nomesetorsolicit, it.ireq_codigo, 
        CASE
            WHEN req.req_saida = 'S'::bpchar THEN 'SAIDA DE REQUISICAO PARA '::text || get_setor(req.set_saida)::text
            ELSE NULL::text
        END AS desc_movimentacao, it.ireq_quantidade, it.ireq_qtde_solicitada, 
        CASE
            WHEN it.ireq_consolidado = 'R'::bpchar THEN 'REQUISITADO '::text
            WHEN it.ireq_consolidado = 'D'::bpchar THEN 'DISPENSADO  '::text
            WHEN it.ireq_consolidado = 'A'::bpchar THEN 'ATENDIDO    '::text
            WHEN it.ireq_consolidado = 'C'::bpchar THEN 'CANCELADO   '::text
            ELSE NULL::text
        END AS desc_status, it.ireq_consolidado, it.ireq_status
   FROM requisicao req, itens_requisicao it, produto
  WHERE req.req_codigo = it.req_codigo AND it.pro_codigo = produto.pro_codigo AND req.req_tipo = 'S'::bpchar AND it.ireq_consolidado = 'D'::bpchar;

ALTER TABLE req_dispensado
  OWNER TO postgres;
GRANT ALL ON TABLE req_dispensado TO postgres;


  CREATE OR REPLACE VIEW req_naoconsolid AS 
 SELECT to_char(req.req_data::timestamp with time zone, 'DD/MM/YYYY'::text) AS data, req.req_data, req.req_nr_nota, get_setor(req.set_saida) AS setor, req.req_saida AS tipomovim, it.pro_codigo, produto.pro_nome, req.req_codigo, req.set_saida AS codsetor, req.req_tipo, req.req_saida AS operacao, '-' AS sinal, req.set_entrada AS codsetorsolicit, get_setor(req.set_entrada) AS nomesetorsolicit, it.ireq_codigo, 
        CASE
            WHEN req.req_saida = 'S'::bpchar THEN 'SAIDA DE REQUISICAO PARA '::text || get_setor(req.set_saida)::text
            ELSE NULL::text
        END AS desc_movimentacao, it.ireq_quantidade, it.ireq_qtde_solicitada, 
        CASE
            WHEN it.ireq_consolidado = 'R'::bpchar THEN 'REQUISITADO '::text
            WHEN it.ireq_consolidado = 'D'::bpchar THEN 'DISPENSADO  '::text
            WHEN it.ireq_consolidado = 'A'::bpchar THEN 'ATENDIDO    '::text
            WHEN it.ireq_consolidado = 'C'::bpchar THEN 'CANCELADO   '::text
            ELSE NULL::text
        END AS desc_status, it.ireq_consolidado
   FROM requisicao req, itens_requisicao it, produto
  WHERE req.req_codigo = it.req_codigo AND it.pro_codigo = produto.pro_codigo AND req.req_tipo = 'S'::bpchar AND it.ireq_consolidado = 'R'::bpchar;

ALTER TABLE req_naoconsolid
  OWNER TO postgres;
GRANT ALL ON TABLE req_naoconsolid TO postgres;


  CREATE OR REPLACE VIEW reqtransf_atendido AS 
 SELECT to_char(req.req_data::timestamp with time zone, 'DD/MM/YYYY'::text) AS data, req.req_data, req.req_nr_nota, get_setor(req.set_saida) AS setor, req.req_saida AS tipomovim, it.pro_codigo, produto.pro_nome, req.req_codigo, req.set_saida AS codsetor, req.req_tipo, req.req_saida AS operacao, '-' AS sinal, req.set_entrada AS codsetorsolicit, get_setor(req.set_entrada) AS nomesetorsolicit, it.ireq_codigo, 
        CASE
            WHEN req.req_saida = 'T'::bpchar THEN (('REQUISICAO DE TRANSFERENCIA '::text || get_setor(req.set_saida)::text) || ' PARA '::text) || get_setor(req.set_entrada)::text
            ELSE NULL::text
        END AS desc_movimentacao, it.ireq_quantidade, it.ireq_qtde_solicitada, 
        CASE
            WHEN it.ireq_consolidado = 'R'::bpchar THEN 'REQUISITADO '::text
            WHEN it.ireq_consolidado = 'D'::bpchar THEN 'DISPENSADO  '::text
            WHEN it.ireq_consolidado = 'A'::bpchar THEN 'ATENDIDO    '::text
            WHEN it.ireq_consolidado = 'C'::bpchar THEN 'CANCELADO   '::text
            ELSE NULL::text
        END AS desc_status, it.ireq_consolidado
   FROM requisicao req, itens_requisicao it, produto
  WHERE req.req_codigo = it.req_codigo AND it.pro_codigo = produto.pro_codigo AND req.req_tipo = 'T'::bpchar AND it.ireq_consolidado = 'A'::bpchar;

ALTER TABLE reqtransf_atendido
  OWNER TO postgres;
GRANT ALL ON TABLE reqtransf_atendido TO postgres;


  CREATE OR REPLACE VIEW reqtransf_dispensado AS 
 SELECT to_char(req.req_data::timestamp with time zone, 'DD/MM/YYYY'::text) AS data, req.req_data, req.req_nr_nota, get_setor(req.set_saida) AS setor, req.req_saida AS tipomovim, it.pro_codigo, produto.pro_nome, req.req_codigo, req.set_saida AS codsetor, req.req_tipo, req.req_saida AS operacao, '-' AS sinal, req.set_entrada AS codsetorsolicit, get_setor(req.set_entrada) AS nomesetorsolicit, it.ireq_codigo, 
        CASE
            WHEN req.req_saida = 'T'::bpchar THEN (('REQUISICAO DE TRANSFERENCIA '::text || get_setor(req.set_saida)::text) || ' PARA '::text) || get_setor(req.set_entrada)::text
            ELSE NULL::text
        END AS desc_movimentacao, it.ireq_quantidade, it.ireq_qtde_solicitada, 
        CASE
            WHEN it.ireq_consolidado = 'R'::bpchar THEN 'REQUISITADO '::text
            WHEN it.ireq_consolidado = 'D'::bpchar THEN 'DISPENSADO  '::text
            WHEN it.ireq_consolidado = 'A'::bpchar THEN 'ATENDIDO    '::text
            WHEN it.ireq_consolidado = 'C'::bpchar THEN 'CANCELADO   '::text
            ELSE NULL::text
        END AS desc_status, it.ireq_consolidado
   FROM requisicao req, itens_requisicao it, produto
  WHERE req.req_codigo = it.req_codigo AND it.pro_codigo = produto.pro_codigo AND req.req_tipo = 'T'::bpchar AND it.ireq_consolidado = 'D'::bpchar;

ALTER TABLE reqtransf_dispensado
  OWNER TO postgres;
GRANT ALL ON TABLE reqtransf_dispensado TO postgres;


  CREATE OR REPLACE VIEW reqtransf_naoconsolid AS 
 SELECT to_char(req.req_data::timestamp with time zone, 'DD/MM/YYYY'::text) AS data, req.req_data, req.req_nr_nota, get_setor(req.set_saida) AS setor, req.req_saida AS tipomovim, it.pro_codigo, produto.pro_nome, req.req_codigo, req.set_saida AS codsetor, req.req_tipo, req.req_saida AS operacao, '-' AS sinal, req.set_entrada AS codsetorsolicit, get_setor(req.set_entrada) AS nomesetorsolicit, it.ireq_codigo, 
        CASE
            WHEN req.req_saida = 'T'::bpchar THEN (('REQUISICAO DE TRANSFERENCIA '::text || get_setor(req.set_saida)::text) || ' PARA '::text) || get_setor(req.set_entrada)::text
            ELSE NULL::text
        END AS desc_movimentacao, it.ireq_quantidade, it.ireq_qtde_solicitada, 
        CASE
            WHEN it.ireq_consolidado = 'R'::bpchar THEN 'REQUISITADO '::text
            WHEN it.ireq_consolidado = 'D'::bpchar THEN 'DISPENSADO  '::text
            WHEN it.ireq_consolidado = 'A'::bpchar THEN 'ATENDIDO    '::text
            WHEN it.ireq_consolidado = 'C'::bpchar THEN 'CANCELADO   '::text
            ELSE NULL::text
        END AS desc_status, it.ireq_consolidado
   FROM requisicao req, itens_requisicao it, produto
  WHERE req.req_codigo = it.req_codigo AND it.pro_codigo = produto.pro_codigo AND req.req_tipo = 'T'::bpchar AND it.ireq_consolidado = 'R'::bpchar;

ALTER TABLE reqtransf_naoconsolid
  OWNER TO postgres;
GRANT ALL ON TABLE reqtransf_naoconsolid TO postgres;

CREATE OR REPLACE VIEW v_consumo AS 
 SELECT get_cod_grupo(v_movimentacao.pro_codigo) AS gru_codigo, get_nome_grupo(v_movimentacao.pro_codigo) AS gru_nome, v_movimentacao.pro_codigo, v_movimentacao.pro_nome, v_movimentacao.codsetor, v_movimentacao.setor, sum(COALESCE(v_movimentacao.ite_quantidade, 0::numeric)) AS consumo, verifica_preco(v_movimentacao.pro_codigo, v_movimentacao.codsetor, v_movimentacao.mov_data) AS preco, v_movimentacao.mov_data
   FROM v_movimentacao, v_produto_centroestoc
  WHERE v_movimentacao.pro_codigo = v_produto_centroestoc.pro_codigo AND v_movimentacao.codsetor = v_produto_centroestoc.codsetor AND v_movimentacao.sinal = '-'::text
  GROUP BY get_cod_grupo(v_movimentacao.pro_codigo), get_nome_grupo(v_movimentacao.pro_codigo), v_movimentacao.pro_codigo, v_movimentacao.pro_nome, v_movimentacao.codsetor, v_movimentacao.setor, v_movimentacao.mov_data;

ALTER TABLE v_consumo
  OWNER TO postgres;
GRANT ALL ON TABLE v_consumo TO postgres;
GRANT ALL ON TABLE v_consumo TO public;

  
  
  
  CREATE OR REPLACE VIEW v_consumo_tp_mov AS 
 SELECT get_cod_grupo(v_movimentacao.pro_codigo) AS gru_codigo, get_nome_grupo(v_movimentacao.pro_codigo) AS gru_nome, v_movimentacao.pro_codigo, v_movimentacao.pro_nome, v_movimentacao.codsetor, v_movimentacao.setor, sum(COALESCE(v_movimentacao.ite_quantidade, 0::numeric)) AS consumo, verifica_preco(v_movimentacao.pro_codigo, v_movimentacao.codsetor, v_movimentacao.mov_data) AS preco, v_movimentacao.mov_data, v_movimentacao.tipomovim, v_movimentacao.sinal
   FROM v_movimentacao, v_produto_centroestoc
  WHERE v_movimentacao.pro_codigo = v_produto_centroestoc.pro_codigo AND v_movimentacao.codsetor = v_produto_centroestoc.codsetor AND v_movimentacao.sinal = '-'::text
  GROUP BY get_cod_grupo(v_movimentacao.pro_codigo), get_nome_grupo(v_movimentacao.pro_codigo), v_movimentacao.pro_codigo, v_movimentacao.pro_nome, v_movimentacao.codsetor, v_movimentacao.setor, v_movimentacao.mov_data, v_movimentacao.tipomovim, v_movimentacao.sinal;

ALTER TABLE v_consumo_tp_mov
  OWNER TO postgres;
GRANT ALL ON TABLE v_consumo_tp_mov TO postgres;
GRANT ALL ON TABLE v_consumo_tp_mov TO public;


CREATE OR REPLACE VIEW v_inventario AS 
 SELECT inventario.inv_codigo, inventario.inv_data, inventario.set_codigo, inventario.gru_codigo, inventario.inv_responsavel, inventario.inv_equipe, inventario_produto.pro_codigo, inventario_produto.invp_quantidade, setor.set_nome, produto.pro_nome, grupo.gru_nome, calcula_estoque(inventario_produto.pro_codigo, inventario.set_codigo, inventario.inv_data) AS estoqueatual, inventario_produto.invp_status
   FROM inventario, inventario_produto, setor, grupo, produto
  WHERE inventario.inv_codigo = inventario_produto.inv_codigo AND inventario.set_codigo = setor.set_codigo AND inventario.gru_codigo = grupo.gru_codigo AND inventario_produto.pro_codigo = produto.pro_codigo AND inventario_produto.invp_status = 'A'::bpchar;

ALTER TABLE v_inventario
  OWNER TO postgres;
  
 
CREATE OR REPLACE VIEW v_movimentacao_all AS 
         SELECT to_char(mov.mov_data::timestamp with time zone, 'DD/MM/YYYY'::text) AS data, mov.mov_data, mov.mov_nr_nota, get_setor(mov.set_saida) AS setor, mov.mov_saida AS tipomovim, it.pro_codigo, produto.pro_nome, mov.mov_codigo, mov.set_saida AS codsetor, mov.mov_tipo, mov.mov_saida AS operacao, '-'::text AS sinal, mov.set_entrada AS codsetorsolicit, get_setor(mov.set_entrada) AS nomesetorsolicit, 
                CASE
                    WHEN mov.mov_saida = 'S'::bpchar THEN 'SAIDA DE REQUISICAO PARA '::text || get_setor(mov.set_saida)::text
                    WHEN mov.mov_saida = 'D'::bpchar THEN ('DISPENSACAO PARA PACIENTE '::text || ' - '::text) || (( SELECT substr(usuario.usu_nome::text, 1, 20) AS substr
                       FROM usuario usuario
                      WHERE usuario.usu_codigo = mov.usu_codigo))
                    WHEN mov.mov_saida = 'P'::bpchar THEN 'SAIDA DE PERMUTA'::text
                    WHEN mov.mov_saida = 'I'::bpchar THEN 'SAIDA POR INVENTARIO'::text
                    WHEN mov.mov_saida = 'A'::bpchar THEN 'SAIDA POR AJUSTE'::text
                    WHEN mov.mov_saida = 'R'::bpchar THEN 'SAIDA POR PERDAS'::text
                    WHEN mov.mov_saida = 'O'::bpchar THEN 'OUTRAS SAIDAS'::text
                    WHEN mov.mov_saida = 'M'::bpchar THEN 'SAIDAS DE EMPRESTIMOS'::text
                    WHEN mov.mov_saida = 'T'::bpchar THEN 'SAIDA POR TRANSFERENCIA PARA '::text || get_setor(mov.set_saida)::text
                    ELSE NULL::text
                END AS desc_movimentacao, it.ite_quantidade, it.ite_vlrunit, mov.age_codigo, mov.usu_codigo
           FROM movimento mov, itens_movimento it, produto
          WHERE mov.mov_codigo = it.mov_codigo AND it.pro_codigo = produto.pro_codigo AND (mov.mov_tipo = 'S'::bpchar OR mov.mov_tipo = 'T'::bpchar) AND it.ite_consolidado = 'S'::bpchar
UNION ALL 
         SELECT to_char(mov.mov_data::timestamp with time zone, 'DD/MM/YYYY'::text) AS data, mov.mov_data, mov.mov_nr_nota, get_setor(mov.set_entrada) AS setor, mov.mov_entrada AS tipomovim, it.pro_codigo, produto.pro_nome, mov.mov_codigo, mov.set_entrada AS codsetor, mov.mov_tipo, mov.mov_entrada AS operacao, '+'::text AS sinal, mov.set_saida AS codsetorsolicit, get_setor(mov.set_saida) AS nomesetorsolicit, 
                CASE
                    WHEN mov.mov_entrada = 'E'::bpchar THEN 'ENTRADA DE NOTA FISCAL'::text
                    WHEN mov.mov_entrada = 'A'::bpchar THEN 'ENTRADA DE AJUSTE '::text
                    WHEN mov.mov_entrada = 'M'::bpchar THEN 'ENTRADA DE EMPRESTIMO'::text
                    WHEN mov.mov_entrada = 'I'::bpchar THEN 'ENTRADA DE INVENTARIO'::text
                    WHEN mov.mov_entrada = 'D'::bpchar THEN 'ENTRADA DE DOACAO'::text
                    WHEN mov.mov_entrada = 'P'::bpchar THEN 'ENTRADA DE PERMUTA'::text
                    WHEN mov.mov_entrada = 'O'::bpchar THEN 'OUTRAS ENTRADAS'::text
                    WHEN mov.mov_entrada = 'T'::bpchar THEN 'ENTRADA POR TRANSFERENCIA DE'::text || get_setor(mov.set_entrada)::text
                    WHEN mov.mov_entrada = 'v'::bpchar THEN 'DEVOLUCAO DE SETORES'::text
                    ELSE NULL::text
                END AS desc_movimentacao, it.ite_quantidade, it.ite_vlrunit, mov.age_codigo, mov.usu_codigo
           FROM movimento mov, itens_movimento it, produto
          WHERE mov.mov_codigo = it.mov_codigo AND it.pro_codigo = produto.pro_codigo AND (mov.mov_tipo = 'E'::bpchar OR mov.mov_tipo = 'T'::bpchar) AND it.ite_consolidado = 'S'::bpchar
  ORDER BY 2, 10;

ALTER TABLE v_movimentacao_all
  OWNER TO postgres;


