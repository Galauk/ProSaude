<?php

	require_once '../global.php';
	
	$usu_codigo = $_GET['usu_codigo'];
	
?><html>
<head>
<?php

	$common = new commonClass();
	$table = new tableClass();
	echo "<link type=\"text/css\" href=\"".LINKSAUDE."/portadeentrada/estilo.css\" rel=\"stylesheet\"/>";
	echo $common->incJquery();
	
	$queryConsulta = pg_query("select *,to_char(aih_dataini,'dd/mm/yyyy') as aih_dataini2,to_char(aih_dt_cadastro,'dd/mm/yyyy') as aih_dt_cadastro2 from aih where usu_codigo = $usu_codigo");
?>
</head>
	<body style="margin:0;padding:0">
		<?=$common->menuTab(array("Autorizacao Hospitalar"));?>
		<?=$common->bodyTab('1'); ?>
		<div style="height:100px;overflow:auto;">
			<?php if(pg_num_rows($queryConsulta)): ?>
			<table class="grid ui-widget ui-widget-content ui-corner-all" width="100%">
				<tr class="ui-widget-header">
					<th>Data Aut.</th>
					<th>Num Aut.</th>
					<th>Medico</th>
					<th>Dt. Cadastro</th>
					<th>&nbsp;</th>
				</tr>
				<?php while($r = pg_fetch_array($queryConsulta)):
					$m = pg_fetch_array(pg_query("select *from medico where med_codigo = '$r[med_solicitante_proc]'"));
				?>
				<tr class="situacao<?=$r['age_atendido'];?>">
					<td class="ui-widget ui-widget-content"><?=$r['aih_dataini2'];?></td>
					<td class="ui-widget ui-widget-content"><?=$r['aih_numero_aih'];?></td>
					<td class="ui-widget ui-widget-content"><?=$m['med_nome'];?></td>
					<td class="ui-widget ui-widget-content"><?=$r['aih_dt_cadastro2'];?></td>
					<td class="ui-widget ui-widget-content" align='center'><?=$common->commonButton(null,"aih_historico.php?act=&acao=addinter&aih_codigo=$r[aih_codigo]&lei_codigo=$lei_codigo&usu_codigo=$usu_codigo","adicionar.png",null,null);?></td>
				</tr>				
				<?php endwhile; ?>
			</table>
			<?php else: ?>
			<em>Não possui autorização para internação.<Br><?=$common->commonButton("Internar Sem Autorizacao","aih_historico.php?act=&acao=addinter&aih_codigo=$r[aih_codigo]&lei_codigo=$lei_codigo&usu_codigo=$usu_codigo","adicionar.png",null,null);?></em>
			<?php endif;?>
		</div>
<?php 		
if($acao == "addinter") {
	if(empty($aih_codigo)) { $aih_codigo = 0; }
	if($act=="") {
			echo $common->modalConfirm("Deseja realmente Confirmar a Internacao?", "$PHP_SELF?act=ok&acao=addinter&aih_codigo=$aih_codigo&lei_codigo=$lei_codigo&usu_codigo=$usu_codigo","aih_historico.php?act=n&acao=addinter&aih_codigo=$r[aih_codigo]&lei_codigo=$lei_codigo&usu_codigo=$usu_codigo");
	}
    if($act == "ok") {
			$dt = date("d/m/Y h:i:s");
		$user = pg_fetch_array(pg_query("select *from usuarios where usr_codigo='$_SESSION[id_login]'"));
 $sql = "INSERT INTO paciente_leito(
            usu_codigo, lei_codigo, pac_dtinternacao, 
            usr_codigo_internacao, aih_codigo)
    VALUES ('$usu_codigo', '$lei_codigo', '$dt', 
            '".$_SESSION['id_login']."', '$aih_codigo')";
            $q = pg_query($sql) or die(pg_last_error());
	    $msg = "Inserido com sucesso";
		$msgErr = "Erro ao Inserir";
	if($q){
			echo $common->modalMsg("OK", "$msg",$PHP_SELF);	
			echo "<script> setTimeout('window.top.location.href=window.top.location.href',1500);</script>";					
		}else{
			echo $common->modalMsg("ERRO", "$msgErr",$PHP_SELF,$stmt);
	  }
	}            
}		
?>