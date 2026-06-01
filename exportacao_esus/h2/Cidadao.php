<?php
    /*classe pra fazer as funcionalidades do h2, foi replicado pra não misturar com as do thrift.*/
    namespace esus\banco_cidadao;
    include "Bd.php"; 
    define(CONN, $connection);
    define(CONN_PG, $connectionPg);
    
    class BancoCidadao {
        public function getDadosCidadao(){
            $sql = "SELECT usu_cpf,
                           usu_cartao_sus,
                           usu_datanasc,
                           usu_email,
                           usu_mae,
                           usu_escolaridade,
                           psf_area,
                           rua_bairro,
                           dom_complemento,
                           cid_codigo_ibge,
                           psf_micro_area,
                           dom_numero,
                           uf_sigla,
                           rua_nome,
                           usu_estado_civil,
                           (CASE 
				 WHEN pais_codigo = '' THEN 'f' 
				 WHEN pais_codigo = '0' THEN 'f'
				 WHEN pais_codigo = '010' THEN 'f'
				 ELSE 't' END) estrangeiro,
			   pais_codigo,
                           etn_codigo,
                           (CASE WHEN usu_obito = 'S' THEN '1' 
				 WHEN usu_obito = 'N' THEN '0' 
				 WHEN usu_obito = '' THEN '0' END) usu_obito,
                           rua_cep,
                           muni_cd_cod_ibge_nasc,
                           usu_nome,
                           usu_pis_pasep,
                           usu_prontuario,
                           rac_codigo,
                           (CASE WHEN usu_sexo = 'F' THEN '1' 
				 WHEN usu_sexo = 'M' THEN '0' END) usu_sexo,
                           usu_celular,
                           dom_telefone,
                           usu_fone_recado,
                           usu_tipo_sanguineo,
                           usu_cbo_r,
                           usu_dt_obito,
                           uni_cnes,
                           cnes_cod_cns,
                           usr_cpf
                      FROM usuario usu
                      LEFT JOIN domicilio dom
                        ON usu.dom_codigo=dom.dom_codigo
                      LEFT JOIN rua
                        ON rua.rua_codigo=dom.rua_codigo
                      LEFT JOIN psf
                        ON psf.dom_codigo=dom.dom_codigo
                      LEFT JOIN cidade cid
                        ON cid.cid_codigo=rua.cid_codigo
                      LEFT JOIN unidade uni
                        ON uni.uni_codigo=usu.uni_codigo
                      LEFT JOIN usuarios usr
                        ON usu.usr_cad=usr.usr_codigo
                      order by usu_codigo";
            
            /*$sql = "select '09876543210987' as usu_cartao_sus,
                            '01234567890' as usu_cpf,
                            usu_datanasc,
                            'NOME DA MAE' as usu_mae,
                            'email@docidadao.com.br' as usu_email,
                            '2L' as usu_escolaridade,
                            '1' as psf_area,
                            '000654' as usu_bairro_dne,
                            'bairro nome' as rua_bairro,
                            'complemento' as complemento,
                            '00008452' as usu_localidade_dne,
                            '420540' as cid_codigo_ibge,
                            '1' as psf_micro_area,
                            '123' as dom_numero,
                            'ponto de referência do endereço' as usu_ponto_referencia,
                            'SC' as uf_sigla,
                            '1L' as usu_estado_civil,
                            'false' as estrangeiro,
                            '65L' as etn_codigo,
                            'false' as usu_obito,
                            '00008452' as usu_mun_nasc_dne,
                            '00008452' as muni_cd_cod_ibge_nasc,
                            '88036003' as rua_cep,
                            'false' as nao_possui_cns,
                            '1234567' as usu_pis_pasep,
                            'ALEXANDRE MATTJE' as usu_nome,
                            '123456' as usu_prontuario,
                            '1234567' as usu_prontuario_cnes,
                            '4L' as rac_codigo,
                            '0' as usu_sexo,
                            '4898765432' as usu_celular,
                            '4890876543' as usu_fone_recado,
                            '4876543219' as dom_telefone,
                            '1' as usu_tipo_sanguineo,
                            'teste rua' as rua_nome
                       from usuario limit 1";*/
            
            $query = pg_query(CONN_PG,$sql) or die(pg_last_error());
            
            return pg_fetch_all($query);
        }
        
        public function inserirCidadao($cidadoes=FALSE){
            
            foreach ($cidadoes as $cidadao){

                $sql_seq_ator = "SELECT max(co_seq_ator) as co_seq_ator FROM tb_ator";
                $query_seq_ator = pg_query(CONN,$sql_seq_ator);
                $co_seq_ator = pg_fetch_array($query_seq_ator);
                $co_seq_ator = $co_seq_ator[co_seq_ator] + 1;
                $insert_tb_ator = "INSERT INTO tb_ator (co_seq_ator) VALUES ($co_seq_ator)";
                $query_tb_ator = pg_query(CONN,$insert_tb_ator) or die(pg_last_error().$insert_tb_ator);
                
                $sql_seq_ator_papel = "SELECT max(co_seq_ator_papel) as co_seq_ator_papel FROM tb_ator_papel";
                $query_seq_ator_papel = pg_query(CONN,$sql_seq_ator_papel);
                $co_seq_ator_papel = pg_fetch_array($query_seq_ator_papel);
                $co_seq_ator_papel = $co_seq_ator_papel[co_seq_ator_papel] + 1;
                $insert_ator_papel = "insert into tb_ator_papel(co_seq_ator_papel,
                                                                co_seq_ator,
                                                                nu_referencias,
                                                                no_tipo_ator_papel,
                                                                co_tipo_perfil)
                                                         values($co_seq_ator_papel,
                                                                $co_seq_ator,
                                                                0,
                                                                'CIDADAO',
                                                                null);";
                $query_tb_ator_papel = pg_query(CONN,$insert_ator_papel) or die(pg_last_error().$insert_ator_papel);
                
                $insert_pessoa_fisica = "insert into tb_pessoa_fisica(dt_nascimento,
                                                                        no_nome_mae,
                                                                        no_pai,
                                                                        co_sexo,
                                                                        co_seq_ator,
                                                                        co_raca_cor,
                                                                        no_nome,
                                                                        no_nome_filtro,
                                                                        st_faleceu,
                                                                        co_etnia,
                                                                        co_escolaridade,
                                                                        co_cbo,
                                                                        nu_cns,
                                                                        no_social)
                                                                 values('$cidadao[usu_datanasc]',
                                                                        '$cidadao[usu_mae]',
                                                                        '$cidadao[usu_pai]',
                                                                        $cidadao[usu_sexo],
                                                                        $co_seq_ator,
                                                                        $cidadao[rac_codigo],
                                                                        '$cidadao[usu_nome]',
                                                                        '$cidadao[usu_nome]',
                                                                        '".($cidadao[usu_obito] == "" ? "0" : "1")."',
                                                                        ".($cidadao[etn_codigo] == "" ? "1" : "$cidadao[etn_codigo]").",
                                                                        '$cidadao[usu_escolaridade]',
                                                                        ".($cidadao[usu_cbo_r] ? "$cidadao[usu_cbo_r]" : "null").",
                                                                        '$cidadao[usu_cartao_sus]',
                                                                        null);";
                
                $query_tb_pessoa_fisica = pg_query(CONN,$insert_pessoa_fisica) or die(pg_last_error().$insert_pessoa_fisica);
                /*localidade / tipo sanguineo / nu_area /nu_micro_area / latitude /longitude / nu_cns_responsavel/no_responsavel/dt_nascimento_responsavel/nu_cns_cuidador,
                  no_cuidador/dt_nascimento_cuidador*/
                
                $uuid_cidadao = getGUID();
                $uuid_prontuario = getGUID();
                
                $insert_tb_cidadao = "insert into tb_cidadao (co_seq_ator_papel,
                                                                co_uuid,
                                                                co_prontuario_uuid,
                                                                st_desconhece_nome_mae,
                                                                st_nao_possui_cns,
                                                                st_estrangeiro,
                                                                co_localidade,
                                                                co_tipo_sanguineo,
                                                                nu_area,
                                                                nu_micro_area,
                                                                nu_nis_pis_pasep,
                                                                dt_atualizado,
                                                                nu_latitude,
                                                                nu_longitude,
                                                                nu_cns_responsavel,
                                                                no_responsavel,
                                                                dt_nascimento_responsavel,
                                                                nu_cns_cuidador,
                                                                no_cuidador,
                                                                dt_nascimento_cuidador)
                                                         VALUES ($co_seq_ator_papel,
                                                                 '$uuid_cidadao',
                                                                 '$uuid_prontuario',
                                                                 ".($cidadao[usu_mae] != "" ? "0" : "1").",
                                                                 ".($cidadao[usu_cartao_sus] == "" ? "1" : "0").",
                                                                 0,
                                                                 NULL,
                                                                 null,
                                                                 null,
                                                                 null,
                                                                 '$cidadao[usu_pis_pasep]',
                                                                 '".date("Y-m-d")."',
                                                                 null,
                                                                 null,
                                                                 null,
                                                                 null,
                                                                 null,
                                                                 null,
                                                                 null,
                                                                 null);";
                $query_tb_cidadao = pg_query(CONN,$insert_tb_cidadao) or die(pg_last_error().$insert_tb_cidadao);
                
                $sql_seq_prontuario = "SELECT max(co_seq_prontuario) as co_seq_prontuario FROM tb_prontuario";
                $query_seq_prontuario = pg_query(CONN,$sql_seq_prontuario);
                $co_seq_prontuario = pg_fetch_array($query_seq_prontuario);
                $co_seq_prontuario = $co_seq_prontuario[co_seq_prontuario] + 1;
                $insert_prontuario = "INSERT INTO tb_prontuario (co_seq_prontuario,
                                                                 co_uuid,
                                                                 co_cidadao_uuid)
                                                           VALUES($co_seq_prontuario,
                                                                  '$uuid_prontuario',
                                                                  '$uuid_cidadao')";
                $query_prontuario = pg_query(CONN,$insert_prontuario) or die(pg_last_error().$insert_prontuario);
                
                $sqlUnidade = "SELECT * FROM tb_unidade_saude where nu_cnes = '$cidadao[uni_cnes]'";
                $queryUnidade = pg_query(CONN,$sqlUnidade);
                $regUnidade = pg_fetch_array($queryUnidade);
                
                $sqlProfissional = "select * 
                                    from tb_profissional p
                                    join tb_ator_papel tap
                                      on tap.co_seq_ator_papel=p.co_seq_ator_papel
                                    join tb_ator ta
                                      on ta.co_seq_ator=tap.co_seq_ator
                                    join tb_pessoa_fisica tpf
                                      on tpf.co_seq_ator=ta.co_seq_ator
                                   where nu_cpf = '$cidadao[usr_cpf]' OR nu_cns = '$cidadao[cnes_cod_cns]'";
                $queryProfissional = pg_query(CONN,$sqlProfissional);
                $regProfissional = pg_fetch_array($queryProfissional);
                
                
                $insert_tb_cds_cadastro_individual = "insert into tb_cds_cadastro_individual(co_seq_ator_papel,
                                                                                             co_pais,
                                                                                             co_municipio,
                                                                                             nu_pis_pasep,
                                                                                             dt_obito,
                                                                                             st_responsavel_familiar,
                                                                                             nu_cartao_sus_responsavel,
                                                                                             dt_nascimento_responsavel,
                                                                                             st_recusa_cadastro,
                                                                                             co_profissional_cadastrante,
                                                                                             co_unidade,
                                                                                             co_cnes_equipe,
                                                                                             co_microarea,
                                                                                             dt_cadastro,
                                                                                             dt_digitacao,
                                                                                             st_envio,
                                                                                             tp_cds_origem,
                                                                                             co_cbo,
                                                                                             dt_atualizacao)
                                                                                       VALUES($co_seq_ator_papel,
                                                                                              ".($cidadao[pais_codigo] ? "$cidadao[pais_codigo]" : "null").",
                                                                                              ".($cidadao[muni_cd_cod_ibge_nasc] ? "$cidadao[muni_cd_cod_ibge_nasc]" : "null").",
                                                                                              '$cidadao[usu_pis_pasep]',
                                                                                              ".($cidadao[usu_dt_obito] ? "'$cidadao[usu_dt_obito]'" : "null").",
                                                                                              1,
                                                                                              '$cidadao[usu_cns_responsavel_familiar]',
                                                                                              ".($cidadao[usu_dt_nasc_responsavel_familiar] ? "'$cidadao[usu_dt_nasc_responsavel_familiar]'" : "null").",
                                                                                              0,
                                                                                              NULL,
                                                                                              $regUnidade[co_seq_ator_papel],
                                                                                              NULL,
                                                                                              NULL,
                                                                                              ".($cidadao[usu_data_cad] ? "'$cidadao[usu_data_cad]'" : "null").",
                                                                                              ".($cidadao[usu_data_cad] ? "'$cidadao[usu_data_cad]'" : "null").",
                                                                                              0,
                                                                                              1,
                                                                                              ".($cidadao[usu_cbo_r] ? "$cidadao[usu_cbo_r]" : "null").",
                                                                                              ".($cidadao[usu_data_cad] ? "'$cidadao[usu_data_cad]'" : "null").");";
                $query_tb_cds_cadastro_individual = pg_query(CONN,$insert_tb_cds_cadastro_individual) or die(pg_last_error().$insert_tb_cds_cadastro_individual);
                
                
            }
            
        }
        
        

    }
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
