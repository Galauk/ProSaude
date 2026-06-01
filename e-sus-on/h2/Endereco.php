<?php
    namespace esus\banco_endereco;
    include "Bd.php"; 
    define(CONN, $connection);
    define(CONN_PG, $connectionPg);
    ini_set('display_errors', true);
    
    class BancoEndereco {
        public function importaPaisesH2(){
            $sqlH2 = "select * from tb_pais";
            $queryH2 = pg_query(CONN,$sqlH2) or die(pg_last_error());
            $error = 0;
            pg_query(CONN_PG,"BEGIN TRANSACTION;");
            while($reg = pg_fetch_array($queryH2)){
                $insert = "INSERT INTO tb_pais(co_pais,
                                               sg_pais_2,
                                               sg_pais_3,
                                               no_pais_portugues,
                                               no_pais_ingles,
                                               no_pais_frances)
                                        VALUES ($reg[co_pais],
                                                '$reg[sg_pais_2]',
                                                '$reg[sg_pais_3]',
                                                '".  str_replace ("'","",  utf8_decode($reg[no_pais_portugues]))."',
                                                retira_acentos('".str_replace ("'","",utf8_encode($reg[no_pais_ingles]))."'),
                                                retira_acentos('".str_replace ("'","",utf8_decode($reg[no_pais_frances]))."'))";
                
                $queryInsert = pg_query(CONN_PG,$insert) or die($insert);
                if(!$queryInsert){
                    $error += 1; 
                }
            }
            if($error > 0){
                pg_query(CONN_PG,"ROLLBACK");
            }else{
                pg_query(CONN_PG,"COMMIT");
            }
            
        }
        
        public function importUf(){
            $sqlH2 = "select * from tb_uf";
            $queryH2 = pg_query(CONN,$sqlH2) or die(pg_last_error());
            $error = 0;
            pg_query(CONN_PG,"BEGIN TRANSACTION;");
            while($reg = pg_fetch_array($queryH2)){
                $insert = "INSERT INTO tb_uf(co_uf,
                                               co_pais,
                                               sg_uf,
                                               co_cep,
                                               co_dne,
                                               no_uf)
                                        VALUES ($reg[co_uf],
                                                $reg[co_pais],
                                                '$reg[sg_uf]',
                                                '$reg[co_cep]',
                                                '$reg[co_dne]',
                                                retira_acentos('".str_replace ("'","",utf8_decode($reg[no_uf]))."'))";
                
                $queryInsert = pg_query(CONN_PG,$insert) or die($insert);
                if(!$queryInsert){
                    $error += 1; 
                }
            }
            if($error > 0){
                pg_query(CONN_PG,"ROLLBACK");
            }else{
                pg_query(CONN_PG,"COMMIT");
            }
        }
       
        public function importSituacaoLocalidade(){
            $sqlH2 = "select * from tb_situacao_localidade ";
            $queryH2 = pg_query(CONN,$sqlH2) or die(pg_last_error());
            $error = 0;
            pg_query(CONN_PG,"BEGIN TRANSACTION;");
            while($reg = pg_fetch_array($queryH2)){
                $insert = "INSERT INTO tb_situacao_localidade (co_situacao_localidade,
                                                               nm_situacao_localidade,
                                                               sg_situacao_localidade)
                                                         VALUES ($reg[co_situacao_localidade],
                                                                 '$reg[nm_situacao_localidade]',
                                                                 '$reg[sg_situacao_localidade]')";
                
                $queryInsert = pg_query(CONN_PG,$insert) or die($insert);
                if(!$queryInsert){
                    $error += 1; 
                }
            }
            if($error > 0){
                pg_query(CONN_PG,"ROLLBACK");
            }else{
                pg_query(CONN_PG,"COMMIT");
            }
        }

        public function importLocalidade(){
            $sqlH2 = "select * from tb_localidade";
            $queryH2 = pg_query(CONN,$sqlH2) or die(pg_last_error());
            $error = 0;
            pg_query(CONN_PG,"BEGIN TRANSACTION;");
            while($reg = pg_fetch_array($queryH2)){
                $insert = "INSERT INTO tb_localidade(co_localidade,
                                               co_uf,
                                               co_cep,
                                               co_dne,
                                               no_localidade,
                                               nu_cep8,
                                               no_localidade_abreviatura,
                                               tp_localidade,
                                               co_situacao_localidade,
                                               no_localidade_filtro)
                                        VALUES ($reg[co_localidade],
                                                $reg[co_uf],
                                                '$reg[co_cep]',
                                                '$reg[co_dne]',
                                                retira_acentos('".str_replace ("'","",utf8_decode($reg[no_localidade]))."'),
                                                '$reg[nu_cep8]',
                                                retira_acentos('".str_replace ("'","",utf8_decode($reg[no_localidade_abreviatura]))."'),
                                                $reg[tp_localidade],
                                                $reg[co_situacao_localidade],
                                                retira_acentos('".str_replace ("'","",utf8_decode($reg[no_localidade_filtro]))."'))";
                
                $queryInsert = pg_query(CONN_PG,$insert) or die($insert);
                if(!$queryInsert){
                    $error += 1; 
                }
            }
            if($error > 0){
                pg_query(CONN_PG,"ROLLBACK");
            }else{
                pg_query(CONN_PG,"COMMIT");
            }
        }
    
        public function importBairro(){
            $sqlH2 = "select * from tb_bairro";
            $queryH2 = pg_query(CONN,$sqlH2) or die(pg_last_error());
            $error = 0;
            pg_query(CONN_PG,"BEGIN TRANSACTION;");
            while($reg = pg_fetch_array($queryH2)){
                $insert = "INSERT INTO tb_bairro(co_bairro,
                                               co_localidade,
                                               co_cep,
                                               co_dne,
                                               no_bairro,
                                               no_bairro_abreviatura,
                                               no_bairro_filtro)
                                        VALUES ($reg[co_bairro],
                                                $reg[co_localidade],
                                                '$reg[co_cep]',
                                                '$reg[co_dne]',
                                                retira_acentos('".str_replace ("'","",utf8_decode($reg[no_bairro]))."'),
                                                retira_acentos('".str_replace ("'","",utf8_decode($reg[no_bairro_abreviatura]))."'),
                                                retira_acentos('".str_replace ("'","",utf8_decode($reg[no_bairro_filtro]))."'))";
                
                $queryInsert = pg_query(CONN_PG,$insert) or die($insert);
                if(!$queryInsert){
                    $error += 1; 
                }
            }
            if($error > 0){
                pg_query(CONN_PG,"ROLLBACK");
            }else{
                pg_query(CONN_PG,"COMMIT");
            }
        }
        
        public function importEndereco(){
            $sqlH2 = "select * from tb_endereco";
            $queryH2 = pg_query(CONN,$sqlH2) or die(pg_last_error());
            $error = 0;
            pg_query(CONN_PG,"BEGIN TRANSACTION;");
            while($reg = pg_fetch_array($queryH2)){
                $insert = "INSERT INTO tb_endereco(co_seq_endereco,
                                               ds_complemento,
                                               nu_numero,
                                               ds_ponto_referencia,
                                               co_seq_bairro,
                                               ds_logradouro,
                                               st_sem_numero)
                                        VALUES ($reg[co_seq_endereco],
                                                retira_acentos('".str_replace ("'","",utf8_decode($reg[ds_complemento]))."'),
                                                '$reg[nu_numero]',
                                                retira_acentos('".str_replace ("'","",utf8_decode($reg[ds_ponto_referencia]))."'),
                                                $reg[co_seq_bairro],
                                                retira_acentos('".str_replace ("'","",utf8_decode($reg[ds_logradouro]))."'),
                                                retira_acentos('".str_replace ("'","",utf8_decode($reg[st_sem_numero]))."'))";
                
                $queryInsert = pg_query(CONN_PG,$insert) or die($insert);
                if(!$queryInsert){
                    $error += 1; 
                }
            }
            if($error > 0){
                pg_query(CONN_PG,"ROLLBACK");
            }else{
                pg_query(CONN_PG,"COMMIT");
            }
        }
        
         public function importEscolaridade(){
            pg_query(CONN_PG,"delete from escolaridade") ;
            $sqlH2 = "select * from tb_escolaridade";
            $queryH2 = pg_query(CONN,$sqlH2) or die(pg_last_error());
            $error = 0;
            pg_query(CONN_PG,"BEGIN TRANSACTION;");
            while($reg = pg_fetch_array($queryH2)){
                $insert = "INSERT INTO escolaridade(ecd_codigo,
                                               ecd_descricao)
                                        VALUES ($reg[co_escolaridade],
                                                '$reg[no_escolaridade]');";
                
                $queryInsert = pg_query(CONN_PG,$insert) or die($insert);
                if(!$queryInsert){
                    $error += 1; 
                }
            }
            if($error > 0){
                pg_query(CONN_PG,"ROLLBACK");
            }else{
                pg_query(CONN_PG,"COMMIT");
            }
        }
        
        //importTipoSanguineo
        public function importTipoSanguineo(){
            
            $sqlH2 = "select * from tb_tipo_sanguineo";
            $queryH2 = pg_query(CONN,$sqlH2) or die(pg_last_error());
            $error = 0;
            pg_query(CONN_PG,"BEGIN TRANSACTION;");
            while($reg = pg_fetch_array($queryH2)){
                $insert = "INSERT INTO tb_tipo_sanguineo(co_tipo_sanguineo,
                                               no_tipo_sanguineo)
                                        VALUES ($reg[co_tipo_sanguineo],
                                                '$reg[no_tipo_sanguineo]');";
                
                $queryInsert = pg_query(CONN_PG,$insert) or die($insert);
                if(!$queryInsert){
                    $error += 1; 
                }
            }
            if($error > 0){
                pg_query(CONN_PG,"ROLLBACK");
            }else{
                pg_query(CONN_PG,"COMMIT");
            }
        }
        
        public function tipoPergunta(){
            $sqlH2 = "select * from TB_TIPO_PERGUNTA";
            $queryH2 = pg_query(CONN,$sqlH2) or die(pg_last_error());
            $error = 0;
            pg_query(CONN_PG,"BEGIN TRANSACTION;");
            while($reg = pg_fetch_array($queryH2)){
                $insert = "INSERT INTO tb_tipo_pergunta(co_tipo_pergunta,
                                               ds_tipo_pergunta)
                                        VALUES ($reg[co_tipo_pergunta],
                                                '$reg[ds_tipo_pergunta]');";
                
                $queryInsert = pg_query(CONN_PG,$insert) or die($insert);
                if(!$queryInsert){
                    $error += 1; 
                }
            }
            if($error > 0){
                pg_query(CONN_PG,"ROLLBACK");
            }else{
                pg_query(CONN_PG,"COMMIT");
            }
        }
        
        public function perguntacontexto(){
            $sqlH2 = "select * from TB_CONTEXTO_PERGUNTA";
            $queryH2 = pg_query(CONN,$sqlH2) or die(pg_last_error());
            $error = 0;
            pg_query(CONN_PG,"BEGIN TRANSACTION;");
            while($reg = pg_fetch_array($queryH2)){
                $insert = "INSERT INTO TB_CONTEXTO_PERGUNTA(co_contexto_pergunta,
                                               ds_contexto_pergunta)
                                        VALUES ($reg[co_contexto_pergunta],
                                                '$reg[ds_contexto_pergunta]');";
                
                $queryInsert = pg_query(CONN_PG,$insert) or die($insert);
                if(!$queryInsert){
                    $error += 1; 
                }
            }
            if($error > 0){
                pg_query(CONN_PG,"ROLLBACK");
            }else{
                pg_query(CONN_PG,"COMMIT");
            }
        }
        
        public function perguntaDetalhe(){
            $sqlH2 = "select * from TB_PERGUNTA_DETALHE";
            $queryH2 = pg_query(CONN,$sqlH2) or die(pg_last_error());
            $error = 0;
            pg_query(CONN_PG,"BEGIN TRANSACTION;");
            while($reg = pg_fetch_array($queryH2)){
                $insert = "INSERT INTO TB_PERGUNTA_DETALHE(co_pergunta_detalhe,
                                               co_pergunta,
                                               ds_local,
                                               ds_pergunta_detalhe,
                                               no_identificador)
                                        VALUES ($reg[co_pergunta_detalhe],
                                                $reg[co_pergunta], 
                                                '$reg[ds_local]',
                                                '$reg[ds_pergunta_detalhe]',
                                                '$reg[no_identificador]')";
                
                $queryInsert = pg_query(CONN_PG,$insert) or die($insert);
                if(!$queryInsert){
                    $error += 1; 
                }
            }
            if($error > 0){
                pg_query(CONN_PG,"ROLLBACK");
            }else{
                pg_query(CONN_PG,"COMMIT");
            }
        }
        
        public function importaPergunta(){
            $sqlH2 = "select * from TB_PERGUNTA";
            $queryH2 = pg_query(CONN,$sqlH2) or die(pg_last_error());
            $error = 0;
            pg_query(CONN_PG,"BEGIN TRANSACTION;");
            while($reg = pg_fetch_array($queryH2)){
                $insert = "INSERT INTO TB_PERGUNTA(co_seq_pergunta,
                                               ds_local,
                                               ds_pergunta,
                                               co_pergunta_pai,
                                               co_contexto_pergunta,
                                               tp_pergunta)
                                        VALUES ($reg[co_seq_pergunta],
                                                '$reg[ds_local]', 
                                                '$reg[ds_pergunta]',
                                                ".($reg[co_pergunta_pai] ? $reg[co_pergunta_pai] : "NULL").",
                                                ".($reg[co_contexto_pergunta] ? $reg[co_contexto_pergunta] : "NULL").",
                                                '$reg[tp_pergunta]')";
                
                $queryInsert = pg_query(CONN_PG,$insert) or die($insert);
                if(!$queryInsert){
                    $error += 1; 
                }
            }
            if($error > 0){
                pg_query(CONN_PG,"ROLLBACK");
            }else{
                pg_query(CONN_PG,"COMMIT");
            }
        }
        
        public function visitaDesfecho(){
            $sqlH2 = "select * from TB_CDS_VISITA_DOM_DESFECHO";
            $queryH2 = pg_query(CONN,$sqlH2) or die(pg_last_error());
            $error = 0;
            pg_query(CONN_PG,"BEGIN TRANSACTION;");
            while($reg = pg_fetch_array($queryH2)){
                $insert = "INSERT INTO tb_cds_visita_dom_desfecho (co_cds_visita_dom_desfecho,
                                               no_cds_visita_dom_desfecho,
                                               no_identificador)
                                        VALUES ($reg[co_cds_visita_dom_desfecho],
                                                '$reg[no_cds_visita_dom_desfecho]', 
                                                '$reg[no_identificador]')";
                
                $queryInsert = pg_query(CONN_PG,$insert) or die($insert);
                if(!$queryInsert){
                    $error += 1; 
                }
            }
            if($error > 0){
                pg_query(CONN_PG,"ROLLBACK");
            }else{
                pg_query(CONN_PG,"COMMIT");
            }
        }
        
        public function visitaMotivo(){
           $sqlH2 = "select * from tb_cds_visita_dom_motivo";
            $queryH2 = pg_query(CONN,$sqlH2) or die(pg_last_error());
            $error = 0;
            pg_query(CONN_PG,"BEGIN TRANSACTION;");
            while($reg = pg_fetch_array($queryH2)){
                $insert = "INSERT INTO tb_cds_visita_dom_motivo (CO_CDS_VISITA_DOM_MOTIVO,
                                               NO_CDS_VISITA_DOM_MOTIVO ,
                                               no_identificador)
                                        VALUES ($reg[co_cds_visita_dom_motivo],
                                                '$reg[no_cds_visita_dom_motivo]', 
                                                '$reg[no_identificador]')";
                
                $queryInsert = pg_query(CONN_PG,$insert) or die($insert);
                if(!$queryInsert){
                    $error += 1; 
                }
            }
            if($error > 0){
                pg_query(CONN_PG,"ROLLBACK");
            }else{
                pg_query(CONN_PG,"COMMIT");
            } 
        }
        
        public function importaCiap(){
           $sqlH2 = "select * from tb_ciap";
            $queryH2 = pg_query(CONN,$sqlH2) or die(pg_last_error());
            $error = 0;
            pg_query(CONN_PG,"BEGIN TRANSACTION;");
            while($reg = pg_fetch_array($queryH2)){
                $insert = "INSERT INTO tb_ciap (co_seq_ciap,
                                               co_ciap ,
                                               ds_ciap,
                                               ds_ciap_filtro,
                                               co_sexo,
                                               st_filtro_padrao)
                                        VALUES ($reg[co_seq_ciap],
                                                '$reg[co_ciap]', 
                                                '$reg[ds_ciap]',
                                                '$reg[ds_ciap_filtro]',
                                                '$reg[co_sexo]',   
                                                '$reg[st_filtro_padrao]')";
                
                $queryInsert = pg_query(CONN_PG,$insert) or die($insert);
                if(!$queryInsert){
                    $error += 1; 
                }
            }
            if($error > 0){
                pg_query(CONN_PG,"ROLLBACK");
            }else{
                pg_query(CONN_PG,"COMMIT");
            } 
        }
        
        public function tipoEquipe(){
            $sqlH2 = "select * from tb_tipo_equipe";
            $queryH2 = pg_query(CONN,$sqlH2) or die(pg_last_error());
            $error = 0;
            pg_query(CONN_PG,"BEGIN TRANSACTION;");
            while($reg = pg_fetch_array($queryH2)){
                $insert = "INSERT INTO tb_tipo_equipe (co_seq_tipo_equipe,
                                               sg_tipo_equipe ,
                                               no_tipo_equipe,
                                               sg_tipo_equipe_filtro,
                                               no_tipo_equipe_filtro,
                                               tp_emad,
                                               tp_emap,
                                               nu_ms,
                                               no_identificador)
                                        VALUES ($reg[co_seq_tipo_equipe],
                                                '$reg[sg_tipo_equipe]', 
                                                '$reg[no_tipo_equipe]',
                                                '$reg[sg_tipo_equipe_filtro]',
                                                '$reg[no_tipo_equipe_filtro]',   
                                                '$reg[tp_emad]',
                                                 '$reg[tp_emap]',
                                                '$reg[nu_ms]',
                                                '$reg[no_identificador]')";
                
                $queryInsert = pg_query(CONN_PG,$insert) or die($insert);
                if(!$queryInsert){
                    $error += 1; 
                }
            }
            if($error > 0){
                pg_query(CONN_PG,"ROLLBACK");
            }else{
                pg_query(CONN_PG,"COMMIT");
            } 
        }
   
    }

?>
