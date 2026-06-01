<script type="text/javascript" src="funcoes.js"></script>
<script type="text/javascript" src="ajax_motor.js"></script>
<script language='JavaScript' type='text/javascript' src='paciente.js'></script>
<script>
function carregaEspecialidade(){
	var med_codigo = document.getElementById("med_codigo").value;
	url = "carregaEspecialidade.php?med_codigo="+med_codigo;
	//alert(url);
	ajax_tudo(url,retorno);
}
function retorno(txt){
	
	document.getElementById('oculta1').style.display = '';
	var x = document.getElementById('oculta2');
	x.innerHTML = txt;
}

</script>
<?
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>

	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
	verauth($id_login);
	cabecario();
//------------------------------------------------------------------>


//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
	 reglog($id_login,"Entrando em MEDICO_ESPECIALIDADE");
//------------------------------------------------------------------>

// if(empty($acao)) {
 if(empty($acao) OR $acao=='form_med_esp') {

//
//-> Botoes
  echo "<fieldset>
	    <legend>Opções</legend>
	       <a href=medico.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a>
	       ".ChmodBtn($id_login,'adicionar','medico_especialidade.php?acao=form_add&id_login=$id_login')."

			<table width=100% align=center cellspacing=3 cellpadding=0 border=0>
				<tr>";
					if(chmodbtn($id_login, "procurar_if", "medico_especialidade.php"))
					{
					  echo "<form method=post action=$PHP_SELF>";
					}
						echo "<input type=hidden name=acao value=busca>
						<input type=hidden name=id_login value=$id_login>
						<td width=30>Buscar:</td>
						<td width=120><input type=text name=palavra_chave class=box onBlur=\"javascript:this.value=this.value.toUpperCase();\"></td>
						<td>".ChmodBtn($id_login,'procurar','medico_especialidade.php')."</td>
					</form>
				</tr>
			</table>

	   </fieldset>
	  <br>";

//
//-> Listando
  
  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Listando Especialidades/Medico Cadastradas</legend>
	     <table width=100% align=center cellspacing=2 cellpadding=4 border=0>
	      <tr bgcolor=F9f9f9>
		<td width=40 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Médico</td>
		<td width=270 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Especialidade</td>
		<td colspan=2 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td>";
if(chmodbtn($id_login, "listar_if", "medico_especialidade.php"))
{
   $sql=pg_query("  SELECT mesp.mes_codigo,
					       usr_nome,
					       esp_nome
					  FROM especialidade as esp
					  JOIN medico_especialidade as mesp
					    ON esp.esp_codigo = mesp.esp_codigo
					  JOIN usuarios as usr
					    on usr.usr_codigo = mesp.med_codigo");
}
     while(@$row=pg_fetch_array($sql)) {
       echo "<tr>
	       <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[usr_nome]</td>
	       <td width=270 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[esp_nome]</td>
	       <td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'editar','medico_especialidade.php?acao=form_edit&mes_codigo='.$row[mes_codigo])."</td>
	       <td width=66 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'apagar','medico_especialidade.php?acao=del&mes_codigo='.$row[mes_codigo])."</td>
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
//-> Mostrando o resultado da busca
//------------------------------------------------------------------>

 if($acao=="busca") {
//
//-> Verificando Busca
 reglog($id_login,"Buscando em MEDICO_ESPECIALIDADE: $palavra_chave ");

if(strlen($palavra_chave)<="3") {
        echo "<br><br><br><br><br><br><br><br><br><br><br><br><br>
                <table height=100 width=50% align=center cellspacing=0 cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>
                 <tr bgcolor=f9f9f9>
                   <td align=center><font color=red size=2><b>ERRO</b></font><br>Busca com menos de <b>3</b> caracteres não permitida</td>
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
//echo $v1;
//
//-> Botoes
  echo "<fieldset>
	    <legend>Opções</legend>
	       ".ChmodBtn($id_login,'adicionar','medico_especialidade.php?acao=form_add')."

			<table width=100% align=center cellspacing=3 cellpadding=0 border=0>
				<tr>";
					if(chmodbtn($id_login, "procurar_if", "medico_especialidade.php"))
					{
					  echo "<form method=post action=$PHP_SELF>";
					}
						echo "<input type=hidden name=acao value=busca>
						<input type=hidden name=id_login value=$id_login>
						<td width=30>Buscar:</td>
						<td width=120><input type=text name=palavra_chave class=box onBlur=\"javascript:this.value=this.value.toUpperCase();\"></td>
						<td>".ChmodBtn($id_login,'procurar','medico_especialidade.php')."</td>
					</form>
				</tr>
			</table>

	   </fieldset>
	  <br>";
if(chmodbtn($id_login, "listar_if", "medico_especialidade.php"))
{
$sql=pg_query("select mes_codigo, usr_nome, esp_nome 
               from medico_especialidade as medesp, 
                    usuarios as usr, especialidade as esp
               where medesp.med_codigo = usr.usr_codigo
               and   medesp.esp_codigo = esp.esp_codigo
               and  (usr.usr_nome ilike '%$palavra_chave%')");
}
@$num=pg_num_rows($sql);
  if(@$num=="0") { $resp = "Nenhum Registro encontrado com \"$palavra_chave\""; }
  if(@$num=="1") { $resp = "Encontrado <b>$num</b> Registro com \"$palavra_chave\""; }
  if(@$num>"1") { $resp = "Encontrados <b>$num</b> Registros com \"$palavra_chave\""; }

  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>$resp</legend>
	     <table width=100% align=center cellspacing=2 cellpadding=4 border=0>
	      <tr bgcolor=F9f9f9>
		<td width=40 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Codigo</td>
		<td width=270 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Nome</td>
		<td colspan=2 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td>";

     while(@$row=pg_fetch_array($sql)) {
       echo "<tr>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[usr_nome]</td>
	       <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[esp_nome]</td>
	       <td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'editar','medico_especialidade.php?acao=form_edit&mes_codigo='.$row[mes_codigo])."</td>
	       <td width=66 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'apagar','medico_especialidade.php?acao=del&mes_codigo='.$row[mes_codigo])."</td>
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
	 reglog($id_login,"Formulario de ADICAO MEDICO_ESPECIALIDADE");


//
//-> Abaixo sao os botoes de voltar / cadastro simples / cadastro completo

  echo "<fieldset>
	    <legend>Opções de Cadastro</legend>
	       <a href=medico.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a>
	   </fieldset>
	  <br>";
//
//-> Este if esta apontando quando a acao for vazia ele vai mostrar o cadastro simples
//   no cadastro completo vc vai ter que passar a variavel acao para completo

 if(($type=="" OR $acao=="simples")) {
  echo "<form method=post action=$PHP_SELF>
	<input type=hidden name=acao value=add>
	<input type=hidden name=id_login value=$id_login>
	<input type=hidden name=type value=simples>
	<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Cadastro de Especialidades/Medico</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
	      	<td width=110>Nome do Profissional :</td>
				<td>
				 <select name=med_codigo id='med_codigo' class=box onChange=\"carregaEspecialidade(); \">";
			    //
			    //-> SQL especialidade 
			    $query = pg_query("SELECT * 
									 FROM medico									
					 				ORDER BY med_nome");
			    echo "<option>SELECIONE UM PROFISSIONAL...</option>";
			      while($usr=pg_fetch_array($query)) {
			      	
			       echo ($usr[usr_codigo]==$row[med_codigo])?"<option value='$usr[med_codigo]' selected>$usr[med_nome]</option>":"<option value='$usr[med_codigo]'>$usr[med_nome]</option>";
			      }
			   echo "</select>
			        </td>
			     </tr>
			      	<td id='oculta1' align='right' style=\"display:none\">
			      		Especialidade
			      	</td>
			      	<td id='oculta2'>
	      			</td>
		  </tr>
	      <tr>
	       <td>&nbsp;</td>
	       <td><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg></td>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br></form>";
 }//fechamento do if
}
//}

//------------------------------------------------------------------>
//-> Formulario de Edicao de Conteudo
//------------------------------------------------------------------>

 if($acao=="form_edit") {
	 reglog($id_login,"Formulario de EDICAO MEDICO_ESPECIALIDADE");

//
//-> Formulario de edicao do cadastro SIMPLES

//
//-> Abaixo sao os botoes de voltar / cadastro simples / cadastro completo

  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Opções de Cadastro</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
	       <td width=79><a href=$PHP_SELF?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
	       <td>&nbsp;</td>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br>";

//
//-> Pegando as informcoes do banco pra mostrar no formulario
 $sqlmedico_especialidade =  "select * from medico_especialidade where mes_codigo='$mes_codigo'";
 $row=pg_fetch_array(pg_query($sqlmedico_especialidade));

  echo "<form method=post action=$PHP_SELF>
	<input type=hidden name=acao value=edit>
	<input type=hidden name=id_login value=$id_login>
	<input type=hidden name=mes_codigo value=$mes_codigo>
	<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Cadastro de Especialidade/Medico</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
		<td width=110>Nome do Medico :</td>
		<td>
		 <select name=med_codigo class=box>";
	    //
	    //-> SQL especialidade 
	    $query = pg_query("SELECT * 
							 FROM usuarios
							WHERE usr_tipo_medico 
							   IN ('M','A','F','E','C','D')");
	      while($usr=pg_fetch_array($query)) {
	       echo ($usr[usr_codigo]==$row[med_codigo])?"<option value='$usr[usr_codigo]' selected>$usr[usr_nome]</option>":"<option value='$usr[usr_codigo]'>$usr[usr_nome]</option>";
	      }
	   echo "</select>
	        </td>
	      </tr>
	      <tr>
		<td width=110>Nome da Especialidade:</td>
		<td>
		 <select name=esp_codigo class=box>";
	    //
	    //-> SQL especialidade 
	    $query = pg_query("select esp_codigo, esp_nome from especialidade order by esp_nome ");
	      while($espec=pg_fetch_array($query)) {
	       echo ($espec[esp_codigo]==$row[esp_codigo])?"<option value='$espec[esp_codigo]' selected>$espec[esp_nome]</option>":"<option value='$espec[esp_codigo]'>$espec[esp_nome]</option>";
	      }
	   echo "</select>
	        </td>
	      </tr>
	      <tr>
	       <td>&nbsp;</td>
	       <td><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg></td>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br></form>";
}

//------------------------------------------------------------------>
//-> SQL's
//------------------------------------------------------------------>
//
//-> ADD <---------------------------------------------------------->

 if($acao=="add") {
	 reglog($id_login,"Adicionando Registro em MEDICO_ESPECIALIDADE");

    $sql = pg_query("insert into medico_especialidade ( " .
            "med_codigo, " .
            "esp_codigo " .
            ") values ( " .
            ($med_codigo ? "'$med_codigo'" : "null") . ", " .
            ($esp_codigo ? "'$esp_codigo'" : "null") . "  " .
            ")");
            
msg($id_login,$acao,$sql);
}

//
//-> EDIT <--------------------------------------------------------->

 if($acao=="edit") {
	 reglog($id_login,"Editando MEDICO_ESPECIALIDADE $mes_codigo");

  $sql = pg_query("update medico_especialidade set " .
            ($med_codigo ? "med_codigo='$med_codigo'" : "med_codigo=null") . "," .
            ($esp_codigo ? "esp_codigo='$esp_codigo'" : "esp_codigo=null") . " " .
            "where mes_codigo ='$mes_codigo '");
#            echo $sql;
#            exit(0);

msg($id_login,$acao,$sql);
}

//
//-> DEL <---------------------------------------------------------->

 if($acao=="del") {
	 reglog($id_login,"Exluindo Registro de MEDICO_ESPECIALIDADE $mes_codigo");

  $sql = pg_query("delete from medico_especialidade where mes_codigo='$mes_codigo'");
msg($id_login,$acao,$sql);
}

?>

