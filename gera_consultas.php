<?php
  session_start();
require_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
require_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
  
  
  $sql = pg_query("select usr.usr_codigo,uni_desc,usr_tipo_medico,proc.proc_codigo,proc_nome,ate.ate_codigo,count(*) from atendimento as ate 
left join  procedimento_atendimento as pat on pat.ate_codigo = ate.ate_codigo 
left join procedimento as proc on proc.proc_codigo = pat.proc_codigo
left join usuarios as usr on usr.usr_codigo = ate.med_codigo
left join unidade as uni on uni.uni_codigo = ate.uni_codigo
where  ate_data >= '".$_REQUEST['data']."'
and proc_nome is null
group by usr.usr_codigo,uni_desc,usr_tipo_medico,proc.proc_codigo,proc_nome,ate.ate_codigo
order by usr_codigo") or die(pg_last_error());
  
  while($rr = pg_fetch_array($sql)) {
	  if(($rr[usr_tipo_medico]=='M' AND $rr[cnes_tp_unid_id]=="05")) {
		  $proc = "5442";
	  }
	  if(($rr[usr_tipo_medico]=='M' AND $rr[cnes_tp_unid_id]!="05")) {
		  $proc = "5441";
	  }
	  if(($rr[usr_tipo_medico]=='E' AND $rr[cnes_tp_unid_id]=="05")) {
		  $proc = "5439";
	  }
	  if(($rr[usr_tipo_medico]=='E' AND $rr[cnes_tp_unid_id]!="05")) {
		  $proc = "5438";
	  }
	  if($proc!="") {
	  $ins = "INSERT INTO procedimento_atendimento (ate_codigo,proc_codigo,usr_codigo) values ('$rr[ate_codigo]','$proc','$rr[usr_codigo]');"; 
	  echo $ins."<br>";
	   }
  }
  
?>
