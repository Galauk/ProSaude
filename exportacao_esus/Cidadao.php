<?php
    namespace esus\banco_cidadao;
    include "../../WebSocialComum/global.php";
    
    class BancoCidadao {
        public function getDadosCidadao(){
            $sql = "SELECT 
                            usu_codigo,
                            (CASE WHEN usu_cpf = '' THEN ''
                                  ELSE replace(replace(replace(replace(usu_cpf, '.', ''),'-',''), '.', ''),'-','') END) as usu_cpf,
                            (CASE WHEN usu_cartao_sus = '' THEN ''
                                  WHEN usu_cartao_sus is null THEN ''
                                  ELSE usu_cartao_sus END) AS usu_cartao_sus,
                            usu_datanasc,
                            usu_email,
                            retira_acentuacao(usu_mae) as usu_mae,
                            '2' as usu_escolaridade,
                            psf_area,
                            rua_bairro,
                            retira_acentuacao(dom_complemento) as dom_complemento,
                            'null' as usu_bairro_dne,
                            cid_codigo_ibge,
                            psf_micro_area,
                            dom_numero,
                            uf_sigla,
                            retira_acentuacao(rua_nome) as rua_nome,
                            (CASE WHEN usu_estado_civil = '0' THEN 'null'
                                  WHEN usu_estado_civil = '9' THEN 'null'
                                  ELSE usu_estado_civil END) AS usu_estado_civil,
                            (CASE 
                                  WHEN pais_codigo = '' THEN 'f' 
  				  WHEN pais_codigo = '0' THEN 'f'
 				  WHEN pais_codigo = '010' THEN 'f'
				  ELSE 't' END) estrangeiro,
			    pais_codigo,
                            (CASE WHEN rac_codigo = '5' THEN etn_codigo
                                  ELSE null END) as etn_codigo,
                            (CASE WHEN usu_obito = 'S' THEN 'true' 
			 	  WHEN usu_obito = 'N' THEN 'false' 
				  WHEN usu_obito is null THEN 'false'
				  WHEN usu_obito = ' ' THEN 'false'
				  WHEN usu_obito = 'f' THEN 'false' 
				  WHEN usu_obito = 't' THEN 'true' 
				  else usu_obito END) usu_obito,
                            rua_cep,
                            'null' as muni_cd_cod_ibge_nasc,
                            'null' as usu_mun_nasc_dne,
                            retira_acentuacao(replace(usu_nome,'.','')) as usu_nome,
                            usu_pis_pasep,
                            'null' as usu_localidade_dne,
                            'null' as usu_logradouro_dne,
                            usu_prontuario,
                            (CASE WHEN rac_codigo = '9'THEN '1'
                                  WHEN rac_codigo = '' THEN '1'
                                  WHEN rac_codigo is null THEN '1'
                                  ELSE rac_codigo END) as rac_codigo,
                            (CASE WHEN usu_sexo = 'F' THEN '1'
                                  WHEN usu_sexo = 'M' THEN '0'
                                  WHEN usu_sexo = '' THEN '3' END) AS usu_sexo,
                            usu_celular,
                            usu_fone,
                            usu_fone_recado,
                            dom_telefone,
                            usu_tipo_sanguineo,
                            null as usu_cbo_r
                    FROM usuario usu
                    LEFT JOIN domicilio dom ON usu.dom_codigo=dom.dom_codigo
                    LEFT JOIN rua ON rua.rua_codigo=dom.rua_codigo
                    LEFT JOIN psf ON psf.dom_codigo=dom.dom_codigo
                    LEFT JOIN cidade cid ON cid.cid_codigo=rua.cid_codigo
                    WHERE 
                       dom.dom_codigo is not null
                       and usu_ativacao = 'S' OR usu_ativacao = ''
                       ORDER BY usu_codigo ";
            
            
            $query = pg_query($sql) or die(pg_last_error());
            return pg_fetch_all($query);
        }
    }
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
