<?
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
	require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>ProSaude</title>
<link type="text/css" href="estilo.css" rel="stylesheet"/>

</head>

<body topmargin="0" leftmargin="0" onload='document.form.cod_age_codigo.focus();'>
<?	
	$common = new commonClass();
	$form = new classForm();
	echo $common->incJquery();
	if($acao=="") {
		echo $common->menuTab(array("Leitor de Código de Barras"));
		echo $common->bodyTab('1');
		echo $form->openForm("recepcao_pacientes.php", "POST", "form");
			echo $form->hiddenForm("acao", "ok");
			echo $form->inputText("cod_age_codigo", null, "C&oacute;digo do Agendamento", '60');
		echo $form->closeForm();
		echo $common->closeTab();
 	}
	if($_REQUEST['acao']=="ok") {
	 
	#$num="0000417293";
	$num=$_REQUEST['cod_age_codigo'];
//	die($num);
	#$num="0002005046";
	
	if(substr($num,0,1)!=0) { $cd1=substr($num,0,1); } 
	if(substr($num,1,1)!=0) { $cd2=substr($num,1,1); } 
	if(substr($num,2,1)!=0) { $cd3=substr($num,2,1); } 
	if(substr($num,3,1)!=0) { $cd4=substr($num,3,1); } 
	if(substr($num,4,1)!=0) { $cd5=substr($num,4,1); }
	if(substr($num,5,1)!=0) { $cd6=substr($num,5,1); }
	$cd7=substr($num,6,1);
	$cd8=substr($num,7,1); 
	$cd9=substr($num,8,1); 
	$cd10=substr($num,9,1); 
	$cd11=substr($num,10,1); 
	$cd12=substr($num,11,1); 

	$num_geral = $cd1.$cd2.$cd3.$cd4.$cd5.$cd6.$cd7.$cd8.$cd9.$cd10.$cd11.$cd12;
	$selectAgendamento = "SELECT med_codigo,
					  			 esp_codigo,
								 usu_codigo,
								 uni_codigo,
								 age_hora,
								 age_paciente,
								 age_atendido,
								 to_char(age_data,'DD/MM/YYYY') AS age_data 
				 			FROM agendamento 
						   WHERE age_codigo = '$num_geral'";
//die($selectAgendamento);						   
    $row = pg_fetch_array(pg_query($selectAgendamento));
    $selectUsuario = "SELECT usu_mae,
    						 to_char(usu_datanasc,'DD/MM/YYYY') AS usu_datanasc 
    					FROM usuario 
    				   WHERE usu_codigo = '$row[usu_codigo]'";
    $usu=pg_fetch_array(pg_query($selectUsuario));
    $selectEspecialidade = "SELECT * 
    						  FROM especialidade 
    						 WHERE esp_codigo = '$row[esp_codigo]'";
    $esp=pg_fetch_array(pg_query($selectEspecialidade));
    $selectMedico = "SELECT *
    				   FROM usuarios 
    				  WHERE usr_codigo = '$row[med_codigo]'";
    $med=pg_fetch_array(pg_query($selectMedico));
    $uni = pg_fetch_array(pg_query("select *from unidade where uni_codigo = '$row[uni_codigo]'"));
	
	
    $recepcionado = ($row['age_atendido'] == 'S' ? "N" : "S");
    if ($row['age_atendido'] != 'S'){
	    $updateAgendamento = "UPDATE agendamento 
	    						 SET age_atendido = 'S' 
	    					   WHERE age_codigo = '$num_geral'";
	    $up = pg_query($updateAgendamento);
    }else{
    	echo $common->modalMsg("OK", "Este paciente já foi recepcionado!");
    }

    echo $common->menuTab(array("Paciente Recepcionado"));
    echo $common->bodyTab('1');
    
    	echo "
		<table class='lista' width=100% cellspacing=3 cellpadding=0 border=0>
			<tr>
				<th width=20%>Data/Hora da Consulta:</th>
				<th>$row[age_data] - $row[age_hora]</font></th>
			</tr>
			<tr>
				<td align=right><b>Nome:</b></td>
				<td>".($row[age_paciente] == null ? "&nbsp;" : $row[age_paciente])."</td>
			</tr>
			<tr>
				<td align=right><b>Mãe:</b></td>
				<td>".($usu[usu_mae] == null ? "&nbsp;" : $usu[usu_mae])."</td>
			</tr>
			<tr>
				<td align=right><b>Dt. Nasc.:</b></td>
				<td>".($usu[usu_datanasc] == null ? "&nbsp;" : $usu[usu_datanasc])."</td>
			</tr>
			<tr>
				<th colspan=2>Dados da Consulta</td>
			</tr>
			<tr>
				<td align=right><b>Especialidade:</b></td>
				<td>".($esp[esp_nome] == null ? "&nbsp;" : utf8_encode($esp[esp_nome]))."</td>
			</tr>
			<tr>
				<td align=right><b>Profissional:</b></td>
				<td>".($med[usr_nome] == null ? "&nbsp;" : $med[usr_nome])."</td>
			</tr>
			<tr>
				<td align=right><b>Dados da Unidade:</b></td>
				<td>
				".($uni[uni_desc] == null ? "&nbsp;<b>" : $uni[uni_desc])."</b><br>
				".($uni[uni_endereco] == null ? "&nbsp;" : $uni[uni_endereco])."<br>
				".($uni[uni_cep] == null ? "&nbsp;" : $uni[uni_cep])."
				</td>
			</tr>
		</table>";
    	echo "<div align='center'>".$common->commonButton("VOLTAR", "recepcao_pacientes.php?id_login=$id_login", "voltar.png")."</div>";
	echo "
		<SCRIPT LANGUAGE=\"JavaScript\">
			setTimeout(\"location='recepcao_pacientes.php?id_login=$id_login'\", 9000);
		</SCRIPT>";
    
    echo $common->closeTab();
}
?>
</body>
</html>
