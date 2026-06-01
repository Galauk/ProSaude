<script language="Javascript" type='text/javascript' src="funcoes.js"></script>
<script>
	function validaForm(){
		var area  = document.getElementById('area').value;
		if(area == ""){
			alert('preencha o campo area');
		} else {
			var acao = document.getElementById('acao').value;
			if(acao == "edit"){
				document.forms["formedit"].submit();
			} else {
				document.forms["formadd"].submit();
			}
		}
	}
</script>
<?
	//------------------------------------------------------------------>
	// -> Inclusao principal para montagem do sistema
	//------------------------------------------------------------------>
	session_start();
	require_once $_SESSION['root'].$_SESSION['comum']."library/php/funcoes.inc.php";
	include_once $_SESSION['root'].$_SESSION['modulo']."authlib.inc.php";
	verauth($id_login);
	cabecario();
	//------------------------------------------------------------------>
	error_reporting(E_ALL);
	//header('Content-Type: text/html; charset=utf-8');
	//------------------------------------------------------------------>
	//-> Secao Vazia, mostrando registros e botoes
	reglog($id_login,"Entrando em MICROAREA");
	//------------------------------------------------------------------>
	

if(empty($acao)) {
	echo "
	<fieldset>
		<legend>Op��es</legend>
		".ChmodBtn($id_login,'adicionar','microarea.php?acao=form_add')."";
		echo "<table width=100% align=center cellspacing=3 cellpadding=0 border=0>	
		<tr>";
		if(chmodbtn($id_login, "procurar_if", "microarea.php")){
		echo "<form method=post action=$PHP_SELF>";
		}
		echo "<input type=hidden name=id_login value=$id_login>
					<input type=hidden name=acao value=busca>
					<td width=30 align=right>Buscar:</td>
					<td width=120><input type=text name=palavra_chave class=box onBlur=\"javascript:this.value=this.value.toUpperCase();\"></td>
					<td>".ChmodBtn($id_login,'procurar','microarea.php')."</td>
				</form>
		</tr>
		</table>
	</fieldset>
<br>";

//
//-> Listando
  
echo "
<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	<tr>
		<td>
			<fieldset>
				<legend>Listando �ltimas 15 <b>Microareas</b> Cadastradas</legend>
				<table width=100% align=center cellspacing=2 cellpadding=4 border=0>
					<tr bgcolor=F9f9f9 align=center>
						<td width=20  style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Codigo</td>
						<td width=80  style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Microarea</td>
						<td width=20  style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Area</td>
						<td colspan=2 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td>";
						$sql="select * from microarea";
						$queryListagem = pg_query($queryListagem);             
					
					//echo "SQL->".$sql;
					//exit();
					if(chmodbtn($id_login, "listar_if", "microarea.php")){
						$sql="select * from microarea as mic join area as ar on mic.area_codigo = ar.area_codigo";
						$queryListagem = pg_query($sql);
					}

					while($row=pg_fetch_array($queryListagem)) {
						echo "<tr>
							<td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[mic_codigo]</td>
							<td align=left   style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[mic_descricao]</td>
							<td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[area_desc]</td>
							<td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;' align='right'>
								".ChmodBtn($id_login,'editar',"microarea.php?acao=form_edit&mic_codigo=$row[mic_codigo]")
								.ChmodBtn($id_login,'apagar',"microarea.php?acao=del&mic_codigo=$row[mic_codigo]")."
							</td>
						</tr>";
					}
echo "
					</tr>
				</table>
			</fieldset>
		</td>
	</tr>
</table>";
}


//------------------------------------------------------------------>
//-> Mostrando o resultado da busca
//------------------------------------------------------------------>

 if($acao=="busca") {
//
//-> Verificando Busca
 reglog($id_login,"Buscando em MICROAREA: $palavra_chave ");

if(strlen($palavra_chave)<"3") {
        echo "<br><br><br><br><br><br><br><br><br><br><br><br><br>
                <table height=100 width=50% align=center cellspacing=0 cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>
                 <tr bgcolor=f9f9f9>
                   <td align=center><font color=red size=2><b>ERRO</b></font><br>Busca com menos de <b>3</b> caracteres n�o permitida</td>
                 </tr>
                </table><br>";
        echo "<SCRIPT LANGUAGE=\"JavaScript\">
                  setTimeout(\"location='$PHP_SELF?id_login=$id_login'\", 2000);
              </SCRIPT>";
 exit;
}

//
//-> Subistituindo o + por porcentagem na busca
   $str = str_replace("+","%",$palavra_chave);
   $pos = strpos($palavra_chave,"+");
  if($pos=="0") {
     $v1=1;
  } else {
     $v1=2;
  }
//
//-> Botoes
  echo "<fieldset>
	    <legend>Op��es</legend>
	       <a href=familia.php?id_login=$id_login><img src=".$_SESSION['linkroot'].$_SESSION['comum']."imgs/voltar_on.gif border=0></a>";
	       echo ChmodBtn($id_login,'microarea','microarea.php?acao=form_add');
                if(SelPerm($id_login,'area.php') != "0")
                {
                     echo ChmodBtn($id_login,'area','area.php?acao=');
                } else {
                     echo "<img src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/area_off.jpg' />";
                }
               echo "<table width=100% align=center cellspacing=3 cellpadding=0 border=0>
				<tr>";
					if(chmodbtn($id_login, "procurar_if", "microarea.php"))
					{
					  echo "<form method=post action=$PHP_SELF>";
					}
					echo "<input type='hidden' name='id_login' value='$id_login'>
						<input type='hidden' name='acao' value='busca'>
						<td width='30' align='right'>Buscar:</td>
						<td width='120'><input type='text' name='palavra_chave' class='box' onBlur=\"javascript:this.value=this.value.toUpperCase();\"></td>
						<td>".ChmodBtn($id_login,'procurar','microarea.php')."</td>
					</form>
				</tr>
			</table>
	   </fieldset>
	  <br>";
if(chmodbtn($id_login, "listar_if", "microarea.php")){
  $sql=pg_query("SELECT A.MIC_CODIGO, A.MIC_DESCRICAO, A.AREA_CODIGO, A.AGT_CODIGO,
				B.AREA_DESC, C.AGT_RESPONSAVEL
				FROM MICROAREA A, AREA B, AGENTE C
				WHERE A.AREA_CODIGO = B.AREA_CODIGO
				AND A.AGT_CODIGO = C.AGT_CODIGO
				AND A.MIC_DESCRICAO ILIKE '%$str%'");
}
  $num=pg_num_rows($sql);
  if($num=="0") { $resp="Nenhum Registro encontrado com \"$palavra_chave\""; }
  if($num=="1") { $resp="Encontrado <b>$num</b> Registro com \"$palavra_chave\""; }
  if($num> "1") { $resp="Encontrados <b>$num</b> Registros com \"$palavra_chave\""; }

  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>$resp</legend>
	     <table width=100% align=center cellspacing=2 cellpadding=4 border=0>
	      <tr bgcolor=F9f9f9 align=center>
		   <td width=10  style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>C�digo</td>
		   <td width=40  style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Microarea</td>
		   <td width=10  style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>�rea</td>
		   <td width=10  style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Agente</td>
		   <td colspan=2 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td>";
     while($row=pg_fetch_array($sql)) {
       echo "<tr>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[mic_codigo]</td>
	       <td align=left   style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[mic_descricao]</td>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[area_codigo]</td>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[agt_codigo]</td>
	       <td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'editar','microarea.php?acao=form_edit&mic_codigo=$row[mic_codigo]')."</td>
	       <td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'apagar','microarea.php?acao=del&mic_codigo=$row[mic_codigo]')."</td>
	     </tr>";
     }
	echo "</tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
	</table>";
}

//------------------------------------------------------------------>
//-> Formulario de Adicao de Conteudo
//------------------------------------------------------------------>

if($acao=="form_add") {
	reglog($id_login,"Formulario de ADICAO MICROAREA");

	echo "
		<table width='98%' align='center' cellspacing='0' cellpadding='0' border='0'>
	 		<tr>
	  		<td>
	   			<fieldset>
	    			<legend>Op��es de Cadastro</legend>
	     			<table width='100%' align='center' cellspacing='3' cellpadding='0' border='0'>
	      			<tr>
	       				<td width='79'><a href=microarea.php?id_login=$id_login><img src=".$_SESSION['linkroot'].$_SESSION['comum']."imgs/voltar_on.gif border=0></a></td>
	       				<td>&nbsp;</td>
	      			</tr>
	     			</table>
	   			</fieldset>
			  </td>
	 		</tr>
		</table>
		<br>
	";
		
	//-> Este if esta apontando quando a acao for vazia ele vai mostrar o cadastro simples
	//   no cadastro completo vc vai ter que passar a variavel acao para completo

	if(($type=="" OR $acao=="simples")) {
		echo "
		<form method='post' action='microarea.php' name='formadd'>
			<input type='hidden' name='acao' id='acao' value='add'>
			<input type='hidden' name='id_login' value='$id_login'>
			<input type='hidden' name='type' value='simples'>
			<table width='98%' align='center' cellspacing='0' cellpadding='0' border='0'>
				<tr>
					<td>
						<fieldset>
							<legend>Cadastro de Microarea</legend>
							<table width='100%' align='center' cellspacing='3' cellpadding='0' border='0'>
								
								<tr>
									<td width='70'>Descricao:</td>
									<td><input type='text' name='mic_descricao' class='box' size='40'></td>
								</tr>

								<tr>
									<td width='70'>Area:</td>
									<td>
										<select name='area' id='area' class='box'>
											<option> </option>";
											$sqlArea = "SELECT * FROM area";
											$qryArea = pg_query($sqlArea);
											while($umaLinha = pg_fetch_array($qryArea)){
												echo "<option value='$umaLinha[area_codigo]'>$umaLinha[area_desc]</option>";	
											}
											echo "
										</select>
									</td>
								</tr>

								<tr>
									<td width='70'>Nu_ine:</td>
									<td><input type='text' name='nu_ine' class='box' size='40'></td>

								</tr>

								<tr>
									<td width='70'>Tipo Equipe:</td>
									<td><input type='text' name='tp_equipe' class='box' size='40'></td>

								</tr>

								<tr>
									<td width='70'>Numero da Area:</td>
									<td><input type='text' name='nu_area' class='box' size='40'></td>

								</tr>

								<tr>
									<td width='70'>Nome Equipe:</td>
									<td><input type='text' name='nome_equipe' class='box' size='40'></td>

								</tr>

								<tr>
									<td width='70'>Unidade:</td>
										<td>
											<select name='uni_codigo' id='uni_codigo' class='box'>
												<option> </option>";
												$unidade = "SELECT uni_codigo, uni_desc FROM unidade";
												$qryUnidade = pg_query($unidade);
												while($umaLinha = pg_fetch_array($qryUnidade)){
													echo "<option value='$umaLinha[uni_codigo]'>$umaLinha[uni_desc]</option>";	
												}
												echo "
											</select>
										</td>
								</tr>
								

								<tr>
									<td>&nbsp;</td>
									<td><input type='image' src='".$_SESSION['linkroot'].$_SESSION['comum']."imgs/adicionar_on.jpg'></td>
								</tr>
								
							</table>
						</fieldset>
					</td>
				</tr>
			</table>
			<br>
		</form>";
 	}
}
//}

//------------------------------------------------------------------>
//-> Formulario de Edicao de Conteudo
//------------------------------------------------------------------>

if($acao=="form_edit") {
	reglog($id_login,"Formul�rio de EDI��O MICROAREA");

	//-> Formulario de edicao do cadastro SIMPLES

  echo "<table width='98%' align='center' cellspacing='0' cellpadding='0' border='0'>
					<tr>
          	<td>
           		<fieldset>
            		<legend>Op��es de Cadastro</legend>
             		<table width='100%' align='center' cellspacing='3' cellpadding='0' border='0'>
              		<tr>
               			<td width='79'><a href=microarea.php?id_login=$id_login><img src=".$_SESSION['linkroot'].$_SESSION['comum']."imgs/voltar_on.gif border=0></a></td>
               			<td>&nbsp;</td>
              		</tr>
             		</table>
           		</fieldset>
          	</td>
         	</tr>
        </table><br>";

			//-> Pegando as informcoes do banco pra mostrar no formulario
 			$sqlMicroarea = "SELECT * FROM microarea WHERE mic_codigo=$mic_codigo";
 			$row=pg_fetch_array(pg_query($sqlMicroarea));
		 
			 echo "<br><br><form method='post' action='$_SERVER[PHP_SELF]' name='formedit'>
	<input type='hidden' name='acao' id='acao' value='edit'>
	<input type='hidden' name='id_login' value='$id_login'>
	<input type='hidden' name='mic_codigo' value='$mic_codigo'>
	<table width='98%' align='center' cellspacing='0' cellpadding='0' border='0'>
		<tr>
	  	<td>
				<fieldset>
	    		<legend>Cadastro de Microarea</legend>
	     		<table width='100%' align='center' cellspacing='3' cellpadding='0' border='0'>
						<tr>
							<td width='70'>Descri��o:</td>
							<td><input type='text' name='mic_descricao' class='box' size='40' value='$row[mic_descricao]'></td>
						</tr>
						<tr>
							<td width='70'>�rea:</td>
							<td>
								<select name='area' id='area' class='box'>
									<option> </option>";
									$sqlArea = "select * from area";
									$qryArea = pg_query($sqlArea);
									while($umaLinha = pg_fetch_array($qryArea)){
										echo "<option value=\"$umaLinha[area_codigo]\" ".($row['area_codigo'] == $umaLinha['area_codigo'] ? "selected='selected'" :'' ).">$umaLinha[area_desc]</option>";	
									}
								echo "
								</select>
							</td>
						</tr>
						<tr>
							<td width='70'>Equipe:</td>
							<td>
								<select name='co_seq_equipe' class='box'>";
									$sqlEquipe = "SELECT DISTINCT no_equipe AS equipe, (SELECT co_seq_equipe FROM tb_equipe WHERE no_equipe = te.no_equipe LIMIT 1) id FROM tb_equipe te;";
									$qryEquipe = pg_query($sqlEquipe);
									while($umaLinha = pg_fetch_array($qryEquipe)){
										echo "<option value='$umaLinha[id]' ".($row['co_seq_equipe'] == $umaLinha['ine'] ? "selected='selected'" : '').">$umaLinha[equipe]</option>";	
									}
								echo "</select>
							</td>
						</tr>
						<tr>
							<td>Ativo:</td>
							<td>
								<input type='checkbox' name='ativo' ".($row[ativo] == 't' ? 'checked' : '')." style='padding: 0; margin: 0'>
							</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td><input type='image' src=".$_SESSION['linkroot'].$_SESSION['comum']."imgs/editar_on.jpg></td>
						</tr>
	     		</table>
	   		</fieldset>
	  	</td>
	 	</tr>
	</table><br>
</form>";
}

//------------------------------------------------------------------>
//-> SQL's
//------------------------------------------------------------------>
//
//-> ADD <---------------------------------------------------------->

if($acao=="add") {
	
	reglog($id_login,"Adicionando Registro em MICROAREA");

	$sql = "update tb_equipe set nu_ine = $nu_ine , tb_equipe = $tp_equipe, ds_area = '$ds_area', no_equipe = '$no_equipe', no_equipe_filtro = '$nome_equipe', uni_codigo = $uni_codigo, co_unidade_saude =$uni_codigo where nu_ine = '$nu_ine' ";

	die($sql);

	$query = pg_query($sql);
	msg($id_login,$acao,$sql);
}

//
//-> EDIT <--------------------------------------------------------->

if($acao=="edit") {
	reglog($id_login,"Editando MICROAREA $mic_codigo");
	// print_r($_POST);
	// die();
  $sql = "UPDATE microarea SET mic_descricao =UPPER('$mic_descricao'), area_codigo = '$area', co_seq_equipe = '$co_seq_equipe', ativo = ".($ativo == 'on' ? 'true' : 'false')." WHERE mic_codigo = $mic_codigo";
  $query = pg_query($sql);
	msg($id_login,$acao,$sql);
	exit;
}

//
//-> DEL <---------------------------------------------------------->

if($acao=="del") {
	reglog($id_login,"Exluindo Registro de MICROAREA $mic_codigo");

  $sql = pg_query("DELETE FROM Microarea WHERE mic_codigo='$mic_codigo'");
 
 	msg($id_login,$acao,$sql);
}

?>

