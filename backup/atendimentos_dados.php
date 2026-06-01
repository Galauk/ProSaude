<?
session_start();
require_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
require_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";

$sql = pg_query("select *from atendimento where med_codigo = '".$_REQUEST['usr']."' and ate_data >= '".$_REQUEST['dti']."' and ate_data <= '".$_REQUEST['dtf']."' ") or die(pg_last_error());

while($rr=pg_fetch_array($sql)) {
	echo "INSERT INTO social.atendimento(
            ate_codigo, ate_data, ate_hora, med_codigo, usu_codigo, ate_descatend, 
            ate_observacao, age_codigo, ate_valor_proc, ate_datafinal, uni_codigo, 
            ate_horafinal, ate_pressao, ate_temperatura, ate_diagnostico, 
            cd10_codigo, ate_encaminhamento, ate_acidentetrab, hos_codigo, 
            ate_reclamacao, ate_exame_fisico, ate_tratamento, esp_codigo_encaminhamento, 
            ate_curativo, ate_curativos, ate_peso, ate_altura, ate_finalizado, 
            sispn_codigo, ate_tipo_consulta_prenatal, ate_observacao_prenatal, 
            ate_classificacao_prenatal, ate_puericultura, ate_pre_natal, 
            ate_cancer, ate_dst, ate_diabetes, ate_hipertensao, ate_hanseniase, 
            ate_tuberculose, ate_atendido, ate_outros, cd10_codigos, cd10_codigot, 
            ate_data_insert, gd_codigo, ate_simplificado, ate_tipo, co_local_atend, 
            ate_somente_procedimento, ate_nasf_aval, ate_nasf_proc, ate_nasf_presc, 
            ate_estratificacao_risco_g1, ate_inter_data, ate_inter_motivo, 
            ate_estratificacao_risco_g2, ate_idade_gest, turno, neces_especial, 
            gestante)
    VALUES (?, ?, ?, ?, ?, ?, 
            ?, ?, ?, ?, ?, 
            ?, ?, ?, ?, 
            ?, ?, ?, ?, 
            ?, ?, ?, ?, 
            ?, ?, ?, ?, ?, 
            ?, ?, ?, 
            ?, ?, ?, 
            ?, ?, ?, ?, ?, 
            ?, ?, ?, ?, ?, 
            ?, ?, ?, ?, ?, 
            ?, ?, ?, ?, 
            ?, ?, ?, 
            ?, ?, ?, ?, 
            ?);";

}

?>