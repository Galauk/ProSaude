<?php 
session_start();
echo "<script type='text/javascript' src='$_SESSION[linkroot]$_SESSION[comum]library/js/tiny_mce/tiny_mce.js'></script>";
?>
<script type="text/javascript" src="<?=$_SESSION[linkroot].$_SESSION[comum]?>library/js/funcoes.js"></script>
<script type="text/javascript" src="<?=$_SESSION[linkroot].$_SESSION[comum]?>library/js/procedimento.js"></script>
<script type="text/javascript" src="<?=$_SESSION[linkroot].$_SESSION[comum]?>library/js/ajax_motor.js"></script>
<script type="text/javascript" src="<?=$_SESSION[linkroot].$_SESSION[comum]?>library/js/atalhos.js"></script>
<script type="text/javascript">window.print();</script>
<?php
	
	require_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
require_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
				
	$common = new commonClass();
	$form = new classForm();
	$table = new tableClass();
	  echo "<center>".$common->commonButton("Imprimir", null, "print.png", "onclick=\"javascript:this.style.display='none';window.print();\"")."</center>";
   
   echo "<br /><table class=table style='font-size:14px;font-family:verdana' border=0>
			<tr>
				<th colspan=7 style='font-size:16px;text-align:center'>
					Lista de Convidados do Evento.
				</th>
			</tr>		
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
		</table>";
	//echo $common->incJquery();
		//echo $common->menuTab(array("Dispensar produtos do evento"));
		echo $common->bodyTab('1');
		$eve_codigo = $_GET[eve_codigo];
		echo $common->bodyTab('<h1>Teste</h1>');
		echo $common->bodyTab('<h1>Teste</h1>');
				//echo $_SESSION[linkroot].$_SESSION[comum];
		//echo "<pre>"; var_dump($_SESSION);
			echo $form->openForm("$PHP_SELF","post","formu");
				//echo $form->hiddenForm("acao", "salvar");
				//echo $form->hiddenForm("usu_codigo", null);
				//echo $form->hiddenForm("gruate_codigo", $_GET['gruate_codigo']);
			echo $table->openTable('lista');
				echo $table->criaLinha(array("Prontuário","Nome","Data de nascimento","Tel.","Nome da mãe","A&ccedil;&atilde;o"),array(10,230,50,50,230,150),null,"S");
				$selectCidadao = " SELECT   u.usu_codigo, 
											usu_nome, 
											TO_CHAR(u.usu_datanasc,'DD/MM/YYYY') as usu_datanasc, 
											usu_mae,
											usu_prontuario,
											usu_celular 
										   FROM evento e 
										   JOIN grupo_atendimento_usuario gau 
										     ON gau.eve_codigo = e.eve_codigo
										   JOIN usuario u 
										     ON u.usu_codigo = gau.usu_codigo 
										  WHERE e.eve_codigo = $eve_codigo";
		$query = pg_query($selectCidadao);
		//$res = pg_fetch_array($query);
				while($res = pg_fetch_array($query)){
				echo $table->criaLinha(array($res[usu_prontuario]?$res[usu_prontuario]:"-",$res[usu_nome],$res[usu_datanasc],$res[usu_celular],$res[usu_mae],"[ ] Presença"));
				/*	
				if($_SESSION[modulo]!="WebSocialSocial/"){
					array_push($res,$common->commonButton("Dispensar", null,"distribuir.png","onClick=\"listaMedicamentos('$_SESSION[linkroot]','$_SESSION[comum]',$eve_codigo,$res[0]);\""));
					}else{
					array_push($res,$common->commonButton("Presença", null,"distribuir.png","onClick=\"listaMedicamentos('$_SESSION[linkroot]','$_SESSION[comum]',$eve_codigo,$res[0]);\""));
						
					}
					
					
					
					
					
					if($_SESSION[modulo]!="WebSocialSocial/"){
					if($a == 0){
						echo $table->criaLinha(array($res[codigo],$res[usu_nome],$res[usu_datanasc],$res[usu_celular],$res[usu_mae],$common->commonButton("Dispensar", null,"distribuir.png","onClick=\"listaMedicamentos('$_SESSION[linkroot]','$_SESSION[comum]',$eve_codigo,$res[0]);\"")));
					}else{						
						echo $table->criaLinha(array("<font color=blue>".$res[usu_nis]."</font>","<font color=blue>".$res[usu_nome]."</font>","<font color=blue>".$res[usu_datanasc]."</font>","<font color=blue>".$res[usu_mae]."</font>",$common->commonButton("Dispensar", null,"distribuir.png","onClick=\"listaMedicamentos('$_SESSION[linkroot]','$_SESSION[comum]',$eve_codigo,$res[0]);\"")));
					}
					}else{
					if($a2 == 0){
						echo $table->criaLinha(array($res[usu_nis],$res[usu_nome],$res[usu_datanasc],$res[usu_celular],$res[usu_mae],$common->commonButton("Presença", null,"distribuir.png","onClick=\"salvaPresenca('$_SESSION[linkroot]','$_SESSION[comum]',$eve_codigo,$res[0]);\"")));
					}else{						
						echo $table->criaLinha(array("<font color=blue>".$res[usu_nis]."</font>","<font color=blue>".$res[usu_nome]."</font>","<font color=blue>".$res[usu_datanasc]."</font>","<font color=blue>".$res[usu_celular]."</font>","<font color=blue>".$res[usu_mae]."</font>",$common->commonButton("Presença", null,"distribuir.png","onClick=\"salvaPresenca('$_SESSION[linkroot]','$_SESSION[comum]',$eve_codigo,$res[0]);\"")));
					}
						
					}
					
				}*/
	}
	echo $table->closeTable();
	echo "<div id='listagem'></div>";
	echo $common->closeTab();	
	
?>