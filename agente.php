<SCRIPT LANGUAGE="JavaScript">

function changeLocation(menuObj)
{
   var i = menuObj.selectedIndex;

   if(i > 0)
   {
      window.location = menuObj.options[i].value;
   }
}


function handleResponse ()
{
   if (http.readyState == 4) {
      var response = http.responseText;
      exibeUnidade(response);
   }
}

function exibeUnidade(response)
{
    var ap = document.getElementById('coduni');
        ap.innerHTML = response;
}

function sndUnidade(unidade)
{
    var end = 'leunidade.php?unidade=' + unidade;
    http.open('get', end, true);
    http.onreadystatechange = handleResponse;
    http.send(null);
}

function completaUnidade(unidade)
{
    document.coduni = unidade;
}


</script>

<?php
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>
	session_start();
	include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
	verauth($id_login);
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();


reglog($id_login,"Acessando Agentes");
//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
//------------------------------------------------------------------>

 if(empty($acao)) {

//
//-> Botoes
  echo "<fieldset>
    <legend>Opções</legend>
        ".ChmodBtn($id_login,'adicionar','agente.php?acao=form_add')."

		<table width=100% align=center cellspacing=3 cellpadding=0 border=0>
			<tr>";
					if(chmodbtn($id_login, "procurar_if", "agente.php"))
					{
					  echo "<form method=post action=$PHP_SELF>";
					}
					echo "<input type=hidden name=acao value=busca>
					<input type=hidden name=id_login value=$id_login>
					<td width=200>Buscar - por descricao: </td>
					<td width=120><input type=text name=palavra_chave class=box onBlur=\"javascript:this.value=this.value.toUpperCase();\"> </td>
					<td>".ChmodBtn($id_login,'procurar','agente.php')."</td>
				</form>
			</tr>
		</table>

   </fieldset>
  <br>";

if(chmodbtn($id_login, "listar_if", "agente.php"))
{
	$sql = pg_query("select *from agente order by agt_codigo");
}
echo "
<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
 <tr>
  <td>
   <fieldset>
    <legend>Listando <b>".pg_num_rows($sql)."</b> Agentes Cadastrados</legend>
     <table width=100% align=center cellspacing=2 cellpadding=4 border=0>
      <tr bgcolor=F9f9f9>
		<td width=40  style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Codigo</td>
		<td width=30  style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Numero</td>
		<td width=30  style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Unidade</td>
		<td width=30  style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Descricao</td>
		<td width=80 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Responsavel</td>
		<td colspan=2 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td>";
        while(@$row=pg_fetch_array($sql)) {
        echo "<tr>
	     <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[agt_codigo]</td>
	     <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[agt_numero]</td>
	     <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[uni_codigo]</td>
	     <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[agt_descricao]</td>
	     <td align=left style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[agt_responsavel]</td>
             <td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'editar','agente.php?acao=form_edit&agt_codigo='.$row[agt_codigo])."</td>
	     <td width=66 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'apagar','agente.php?acao=del&agt_codigo='.$row[agt_codigo])."</td>
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
reglog($id_login,"Buscando Agente: $palavra_chave");
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
//
//-> Botoes
  echo "<fieldset>
	    <legend>Opções</legend>
         ".ChmodBtn($id_login,'adicionar','agente.php?acao=form_add')."
   
			<table width=100% align=center cellspacing=3 cellpadding=0 border=0>
				<tr>";
					if(chmodbtn($id_login, "procurar_if", "agente.php"))
					{
					  echo "<form method=post action=$PHP_SELF>";
					}
					echo "<input type=hidden name=acao value=busca>
						<input type=hidden name=id_login value=$id_login>
						<td width=150>Buscar - por descricao: </td>
						<td width=120><input type=text name=palavra_chave class=box onBlur=\"javascript:this.value=this.value.toUpperCase();\"> </td>
						<td>".ChmodBtn($id_login,'procurar','agente.php')."</td>
					</form>
				</tr>
			</table>
	
	   </fieldset>
        <br>";
	
//ativado por lucio em 23/11
 /* $sql=pg_query("SELECT A.agt_codigo, A.agt_numero, B.uni_desc, A.agt_responsavel , A.uni_codigo, A.agt_descricao ".
                "FROM agente A INNER JOIN  Unidade B ".
                "ON   A.uni_codigo = B.uni_codigo ".
                "and  (agt_descricao like '%$palavra_chave%')");  */



//desativado pro lucio
if(chmodbtn($id_login, "listar_if", "agente.php"))
{
	$sql = pg_query("select ag.agt_codigo,
							agt_numero,
							ag.uni_codigo,
							agt_responsavel,
							agt_descricao,
							uni_desc 
					   from agente as ag
					   join unidade as uni
					     on ag.uni_codigo = uni.uni_codigo
					  WHERE agt_descricao 
					   like upper('%$palavra_chave%') 
					     OR agt_responsavel 
					   like upper('%$palavra_chave%') 
				   order by agt_codigo
						");
	//$sql = pg_query("select * from agente WHERE agt_descricao like upper('%$palavra_chave%') OR agt_responsavel like upper('%$palavra_chave%')  order by agt_codigo");
}

   @$num=pg_num_rows($sql);
   if(@$num=="0") { $resp = "Nenhum Registro encontrado com \"       $palavra_chave\""; }
   if(@$num=="1") { $resp = "Encontrado <b>$num</b> Registro com \"  $palavra_chave\""; }
   if(@$num>"1")  { $resp = "Encontrados <b>$num</b> Registros com \"$palavra_chave\""; }
		echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>$resp</legend>
	     <table width=100% align=center cellspacing=2 cellpadding=4 border=0>
	      <tr bgcolor=F9f9f9>
	    	<td width=40  style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Codigo</td>
    		<td width=30  style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Numero</td>
    		<td width=180  style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Unidade</td>
    		<td width=80 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Descricao</td>
    		<td width=80 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Responsavel</td>
    		<td colspan=2 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td>";

     while(@$row=pg_fetch_array($sql)) {
        echo "<tr>
	     <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[agt_codigo]</td>
	     <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[agt_numero]</td>
	     <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[uni_desc]</td>
	     <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[agt_descricao]</td>
	     <td align=left style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[agt_responsavel]</td>
             <td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'editar','agente.php?acao=form_edit&agt_codigo='.$row[agt_codigo])."</td>
	     <td width=66 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'apagar','agente.php?acao=del&agt_codigo='.$row[agt_codigo])."</td>
	     </tr>";
//        echo "<tr>
//	     <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[agt_codigo]</td>
//	     <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[agt_numero]</td>
//	     <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[uni_desc]</td>
//	     <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[agt_responsavel]</td>
//	     <td align=left style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[agt_descricao]</td>
//             <td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'editar','agente.php?acao=form_edit&agt_codigo='.$row[agt_codigo])."</td>
//	     <td width=66 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'apagar','agente.php?acao=del&agt_codigo='.$row[agt_codigo])."</td>
//	     </tr>";	     
	     
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
reglog($id_login,"Acessando Formulario de Adicao de Agente");
//
//-> Abaixo sao os botoes de voltar / cadastro simples / cadastro completo

  echo "<fieldset>
	    <legend>Opções</legend>
	       <a href=agente.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a>
	   </fieldset>
	  <br>";
//
//-> Este if esta apontando quando a acao for vazia ele vai mostrar o cadastro simples
//   no cadastro completo vc vai ter que passar a variavel acao para completo

  if(($type=="" OR $acao=="simples")) {
    echo "<form name=form method=post action=$PHP_SELF>
  	  <input type=hidden name=acao value=add>
  	  <input type=hidden name=id_login value=$id_login>
	  <input type=hidden name=type value=simples>
	  <table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	   <tr>
	    <td>
	     <fieldset>
	      <legend>Cadastro de Agente</legend>
	       <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	        <tr>
		       <td width=70>Numero:</td>
		       <td><input type=text name=agt_numero class=box size=10 value=$agt_numero></td>
	        </tr>
            <tr>
	    </table>";
	echo "<table width=100% align=center cellspacing=3 cellpadding=0 border=0>
               <td width=70>Unidade:</td>
               <td>
               <select name=uni_codigo class=boxr onchange='sndUnidade(this.value)'>";
               $sql = pg_query("select * from unidade order by uni_desc");
	echo "<option value=''>.........</option>";
       while($uni=pg_fetch_array($sql)) {
        echo "<option value='$uni[uni_codigo]'> $uni[uni_desc]</option>";
       }
     echo "</select>
               </td>
            </tr>
	        <tr>
		       <td width=70>Descricao:</td>
               <td><input type=text name=agt_descricao class=box size=70></td>
            </tr></table>";
echo "<table width=100% align=center cellspacing=3 cellpadding=0 border=0>

	        <tr>
		       <td width=70>Responsavel:</td>
               <td><input type=text name=agt_responsavel class=box size=70></td>
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
  }
 }


//------------------------------------------------------------------>
//-> Formulario de Edicao de Conteudo
//------------------------------------------------------------------>

 if($acao=="form_edit") {
//
//-> Formulario de edicao do cadastro SIMPLES
  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
         <tr>
          <td>
           <fieldset>
            <legend>Opções de Cadastro</legend>
             <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
              <tr>
               <td width=79><a href=agente.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
               <td>&nbsp;</td>
              </tr>
             </table>
           </fieldset>
          </td>
         </tr>
        </table><br>";

//
//-> Pegando as informcoes do banco pra mostrar no formulario

 $sqlagente="select *from agente where agt_codigo = '$agt_codigo'";
 $row=pg_fetch_array(pg_query($sqlagente));
reglog($id_login,"Formulario de Edicao de Agente: $row[agt_descricao]");
 echo "<br><br><form method=post action=$PHP_SELF>
	<input type=hidden name=acao value=edit>
	<input type=hidden name=id_login value=$id_login>
	<input type=hidden name=agt_codigo value=$agt_codigo>
	<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Cadastro de Agentes</legend>
	      <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	        <tr>
  		      <td width=70>Numero:</td>
		      <td><input type=text name=agt_numero class=box size=10 value='$row[agt_numero]'></td>
	      </tr>";
       echo "<tr>
               <td width=70>Unidade:</td>
               <td>
               <select name=uni_codigo class=boxr>";
	echo "<option value=''>.........</option>";
               $sql = pg_query("select * from unidade order by uni_desc");
       while($unidade=pg_fetch_array($sql)) {
	       echo ($unidade[uni_codigo]==$row[uni_codigo])?"<option value='$unidade[uni_codigo]' selected>$unidade[uni_desc]</option>":"<option value='$unidade[uni_codigo]'>$unidade[uni_desc]</option>";
       }
     echo "</select>
               </td>
            </tr>
	      <tr>
		      <td width=70>Descricao:</td>
		      <td><input type=text name=agt_descricao class=box size=60 value='$row[agt_descricao]'></td>
	      </tr>";
echo "	      <tr>
		      <td width=70>Responsavel:</td>
		      <td><input type=text name=agt_responsavel class=box size=60 value='$row[agt_responsavel]'></td>
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
    $max=pg_fetch_array(pg_query("SELECT nextval('seq_agt_codigo'::regclass) AS agt_codigo"));
	$agt_codigo = $max[agt_codigo];
    $ver=pg_query("select *from agente where agt_numero='$agt_numero'");

 	
if(pg_num_rows($ver)=="0") {
    $sql = pg_query("insert into agente ( " .
            "agt_codigo , " .
            "agt_numero , " .
            "uni_codigo , " .
            "agt_descricao , " .
            "agt_responsavel".
            ") values ( " .
            ($agt_codigo        ? "'$agt_codigo'"        : "null ") . ", " .
            ($agt_numero        ? "'$agt_numero'"        : "null ") . ", " .
            ($uni_codigo        ? "$uni_codigo"        : "null ") . ", " .
            ($agt_descricao        ? "upper('$agt_descricao')"        : "null ") . ", " .
            ($agt_responsavel ? "upper('$agt_responsavel') " : "null ") .
            ")");

            
reglog($id_login,"Adicionando Agente: N.: $agt_numero Uni.: $uni_codigo Desc.: $agt_descricao Resp.: $agt_responsavel");
 msg($id_login,$acao,$sql);
} else {
    echo "<br><br><br><Br><br><br><br><br><br>
	  <br><br><br><center><font color=red size=5>ERRO</font><br><font size=4>Impossivel cadastrar mesmo NÚMERO para o agente<br>
	  por favor, certifique-se que não exista este agente.<BR><br><a href=agente.php><font size=3><b>Voltar</b></a></center>";
}
  	$qq = pg_query("select med_codigo,esp_codigo,grm_periodo,age_item from grade_mensal group by med_codigo,esp_codigo,grm_periodo,age_item");
 	while($rr=pg_fetch_array($qq)) {
 		$exec = pg_query("insert into grade_mensal (med_codigo,grm_qtde,esp_codigo,agt_codigo,grm_periodo,age_item) 
 												values 
 													('$rr[med_codigo]','0','$rr[esp_codigo]','$agt_codigo','$rr[grm_periodo]','$rr[age_item]')") or die(pg_last_error());
 	}  
}

//
//-> EDIT <--------------------------------------------------------->

 if($acao=="edit") {
  $sql = pg_query("update agente set " .
            ($agt_numero        ? "agt_numero='$agt_numero' "               : "agt_numero=null ")    . ", " .
            ($uni_codigo        ? "uni_codigo='$uni_codigo' "               : "uni_codigo=null ")    . ", " .
            ($agt_descricao ? "agt_descricao=upper('$agt_descricao') " : "agt_descricao=null ") . ", " .
            ($agt_responsavel ? "agt_responsavel=upper('$agt_responsavel') " : "agt_responsavel=null ") .
            " where agt_codigo='$agt_codigo'");

reglog($id_login,"Editando Agente: N.: $agt_numero Uni.: $uni_codigo Desc.: $agt_descricao Resp.: $agt_responsavel");
msg($id_login,$acao,$sql);
}

//
//-> DEL <---------------------------------------------------------->

 if($acao=="del") {
reglog($id_login,"Apagando Agente Cod.: $agt_codigo");
  $sql = pg_query("delete from agente where agt_codigo='$agt_codigo'");
msg($id_login,$acao,$sql);
}

?>

