<?php
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>

	session_start();
	include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
	verauth($id_login);
	
	require_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
	require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	 
  $common = new commonClass();
  $form = new classForm();
  echo $common->incJquery();
//------------------------------------------------------------------>


//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
	 reglog($id_login,"Entrando em COPIAR PERMISSOES");
	//echo "<fieldset><legend>COPIAR PERMISS�ES</legend>";
	 // echo "<pre>";print_r($_POST);die();
	 if($_POST)
	{ 
		$sql = pg_query("SELECT * FROM usuarios_permissoes WHERE usr_codigo = $_POST[usr_mestre] order by perm_codigo");
		$traz = pg_query("SELECT * FROM usuarios_permissoes WHERE usr_codigo = $_POST[usr_normal]");
		
		$buscarDadosMenuSuperior = pg_query("SELECT * FROM permissao_menu_superior WHERE usr_codigo = $_POST[usr_mestre]");
		$recuperaDadosMenuSuperio = pg_fetch_object($buscarDadosMenuSuperior);
		
		$buscarDadosMenuInferior = pg_query("SELECT * FROM permissao_menu_inferior WHERE usr_codigo = $_POST[usr_mestre]");
		$recuperaDadosMenuInferior = pg_fetch_object($buscarDadosMenuInferior);

		if (pg_num_rows($traz)>0){
			$sql2 = pg_query("DELETE FROM usuarios_permissoes WHERE usr_codigo = $_POST[usr_normal]");
		}

		while ($dados = pg_fetch_array($sql)){
			pg_query("begin");
			$insert = pg_query("
				INSERT INTO usuarios_permissoes 
					(usr_codigo,perm_set,nivel_i,nivel_a, nivel_d, nivel_l, nivel_b, perm_codigo) 
				VALUES 
					($_POST[usr_normal],'$dados[perm_set]','$dados[nivel_i]','$dados[nivel_a]','$dados[nivel_d]','$dados[nivel_l]','$dados[nivel_b]','$dados[perm_codigo]')");
			pg_query("commit");
		}

		pg_query("INSERT INTO permissao_menu_superior (	menu_cadastro ,menu_atendimentos ,menu_agendamentos ,menu_laboratorios ,menu_internacao ,menu_materiais ,menu_farmacia ,menu_administrativo ,menu_transporte ,menu_programas_federais , usr_codigo) VALUES ('$recuperaDadosMenuSuperio->menu_cadastro', '$recuperaDadosMenuSuperio->menu_atendimentos', '$recuperaDadosMenuSuperio->menu_agendamentos', '$recuperaDadosMenuSuperio->menu_laboratorios', '$recuperaDadosMenuSuperio->menu_internacao', '$recuperaDadosMenuSuperio->menu_materiais', '$recuperaDadosMenuSuperio->menu_farmacia', '$recuperaDadosMenuSuperio->menu_administrativo', '$recuperaDadosMenuSuperio->menu_transporte', '$recuperaDadosMenuSuperio->menu_programas_federais', '$_POST[usr_normal]')") or die(pg_last_error());

		pg_query("INSERT INTO permissao_menu_inferior (	menu_paciente ,menu_esf ,menu_laboratorio ,menu_internacao ,menu_agendamento ,menu_farmacia ,menu_materiais ,menu_vacinas ,menu_relatorios ,menu_prontuario , menu_usuarios , menu_email , menu_chat ,usr_codigo ) VALUES ('$recuperaDadosMenuInferior->menu_paciente', '$recuperaDadosMenuInferior->menu_esf', '$recuperaDadosMenuInferior->menu_laboratorio', '$recuperaDadosMenuInferior->menu_internacao', '$recuperaDadosMenuInferior->menu_agendamento', '$recuperaDadosMenuInferior->menu_farmacia', '$recuperaDadosMenuInferior->menu_materiais', '$recuperaDadosMenuInferior->menu_vacinas', '$recuperaDadosMenuInferior->menu_relatorios', '$recuperaDadosMenuInferior->menu_prontuario', '$recuperaDadosMenuInferior->menu_usuarios', '$recuperaDadosMenuInferior->menu_email','$recuperaDadosMenuInferior->menu_chat','$_POST[usr_normal]' )") or die(pg_last_error());

		?>
		<div id='sucesso' style='margin-top:-160px;'><?php msg($id_login,"add",$sql); ?></div>
	<?php	
	}
?>
<?php 
//$sql = "SELECT usr_codigo,usr_login FROM usuarios  order by usr_login";
//	echo $common->menuTab(array("Copia de permissoes"));
//	echo $common->bodyTab('1');
//		echo$form->openForm("form");
//			echo $form->inputSelect("usr_mestre",null,"De:",$sql,null,null,null,"style=width:150px");
//			echo $form->inputSelect("usr_normal",null,"Para:",$sql,null,null,null,"style=width:150px");
//			echo $common->commonButton("Voltar", "permissoes_usuarios.php?id_login=$id_login");
//			echo $common->commonButton("Adicionar","copiar_permissoes.php","".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg","onclick='document.form.submit()'");
//		echo $form->closeForm();
//	echo $common->closeTab();
//?>
<html>
<head>
<title>Copia de permissoes</title>
</head>
<body>
<fieldset>
<legend>Op��es</legend>
      <a href="permissoes_usuarios.php?id_login=<?=$id_login?>"><img src="<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/voltar_on.gif" border="0"></a>
</fieldset>      
<fieldset>
<legend>Escolha os usu&aacute;rios</legend>
<form name="form" action="" method="POST">
<table>
	<tr>
		<td width="30">De: </td>
		<td>
			<select name="usr_mestre" style="width:auto;">
			<?
				$sql = pg_query("SELECT usr_codigo,usr_login,usr_nome FROM usuarios order by usr_login");
				while ($dados = pg_fetch_array($sql))
				{
					echo "<option value=\"".$dados['usr_codigo']."\">".$dados['usr_nome']."</option>";
				}
			?>
			</select>
		</td>
	</tr>
	<tr>
		<td>Para: </td>
		<td>
			<select name="usr_normal" style="width:auto;">
			<?
				$sql = pg_query("SELECT usr_codigo,usr_login,usr_nome FROM usuarios order by usr_nome");
				while ($dados = pg_fetch_array($sql))
				{
					echo "<option value=\"".$dados['usr_codigo']."\">".$dados['usr_nome']."</option>";
				}
			?>
			</select>
		</td>
	</tr>
	<tr>
		<td colspan="2">&nbsp;</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><img src="<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/adicionar_on.jpg" onclick="document.form.submit();"></td>
	</tr>
</table>
</form>
</fieldset>
</body>
</html>
</fieldset>
