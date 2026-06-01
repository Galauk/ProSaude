<script type="text/javascript">
	function validaForm(){
		set_nome = document.getElementById('set_nome');
		uni_codigo = document.getElementById('uni_codigo');
		set_estoque = document.getElementById('set_estoque');
		set_farmacia = document.getElementById('set_farmacia');
		set_distribuidor = document.getElementById('set_distribuidor');
		set_transferencia = document.getElementById('set_transferencia');

		if (set_nome.value == ''){
			alert('O campo Descrição do Setor não pode ser vazio.');
			set_nome.focus();
			return false;
		}
		if (uni_codigo.value == ''){
			alert('O campo Unidade não pode ser vazio.');
			uni_codigo.focus();
			return false;
		}
		if (set_estoque.value == ''){
			alert('O campo Centro Estocador não pode ser vazio.');
			set_estoque.focus();
			return false;
		}
		if (set_farmacia.value == ''){
			alert('O campo Dispensação não pode ser vazio.');
			set_farmacia.focus();
			return false;
		}
		if (set_distribuidor.value == ''){
			alert('O campo Distribuidor não pode ser vazio.');
			set_distribuidor.focus();
			return false;
		}
		if (set_transferencia.value == ''){
			alert('O campo Liberado Transferência não pode ser vazio.');
			set_transferencia.focus();
			return false;
		}
		document.formulario.submit();
	}
</script>
<?php
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>

session_start();
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
verauth($id_login);
require_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/debug.inc.php";

cabecario();

$common = new commonClass();
echo $common->incJquery();
$form = new classForm();
//------------------------------------------------------------------>


//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
	 reglog($id_login,"Entrando em SETOR");
//------------------------------------------------------------------>


$stmt = "SELECT uni_codigo FROM usuarios WHERE usr_codigo = $id_login";
$stmt = db_query($stmt);
$dados = pg_fetch_array($stmt);


// if(empty($acao)) {
 if(empty($acao) OR $acao=='form_setor') {
//
//-> Botoes
  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Opções</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
	       <td width=79><a href='cadastros_materiais.php' style='cursor:pointer'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
	       <td width=200>".ChmodBtn($id_login,'adicionar','setor.php?acao=form_add')."</td>
	       <form method=post action=$PHP_SELF>
		<input type=hidden name=id_login value=$id_login>
		<input type=hidden name=acao value=busca>
	<input type=hidden name=id_login value=$id_login>
	       <td width=30>Buscar:</td>
	       <td width=120><input type=text name=palavra_chave class=box onBlur=\"javascript:this.value=this.value.toUpperCase();\"></td>
	       <td>".ChmodBtn($id_login,'procurar','setor.php')."</td></form>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br>";

//
//-> Listando
  
  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Listar Setores Cadastrados</legend>
	     <table class='lista' align=center cellspacing=2 cellpadding=4 border=0>
	      <tr>
		<th width=10>Codigo</th>
		<th>Unidade</th>
		<th>Setor</th>
		<th colspan=2>&nbsp;</th>";

   $sql=pg_query("SELECT * 
   					FROM setor as s join unidade as u on u.uni_codigo=s.uni_codigo
   				   ORDER BY u.uni_codigo DESC 
   				   ") or die(pg_last_error());
     while($row=pg_fetch_array($sql)) {
       echo "<tr>
	       <td width=10>$row[set_codigo]</td>
	       <td>$row[uni_desc]</td>
	       <td>$row[set_nome]</td>	       
	       <td width=60>".ChmodBtn($id_login,'editar','setor.php?acao=form_edit&set_codigo='.$row[set_codigo])."</td>
	       <td width=66>".ChmodBtn($id_login,'apagar','setor.php?acao=del&set_codigo='.$row[set_codigo])."</td>
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
 reglog($id_login,"Buscando em SETOR: $palavra_chave ");

//
//-> Subistituindo o + por porcentagem na busca
   $str = str_replace("+","%",$palavra_chave);
   $pos = strpos($palavra_chave,"+");
  if($pos=="0") {
     $v1=1;
     $v1=2;
  }
//
//-> Botoes
  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Opções</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
	       <td width=79><a href='setor.php' style='cursor:pointer'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
	       <td width=200>".ChmodBtn($id_login,'adicionar','setor.php?acao=form_add')."</td>
	       <form method=post action=$PHP_SELF>
		<input type=hidden name=id_login value=$id_login>
		<input type=hidden name=acao value=busca>
	<input type=hidden name=id_login value=$id_login>
	       <td width=30>Buscar:</td>
	       <td width=120><input type=text name=palavra_chave class=box onBlur=\"javascript:this.value=this.value.toUpperCase();\"></td>
	       <td>".ChmodBtn($id_login,'procurar','setor.php')."</td></form>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br>";

	$sql = "SELECT *
			  FROM setor
			 WHERE ((set_nome LIKE UPPER('%$palavra_chave%')) 
			".(is_numeric($palavra_chave) ? "
				OR (set_codigo = $palavra_chave)": "").") "
			.($dados[0] == "" ? "" : " 
			   AND uni_codigo = ".$dados[0]).
		   " ORDER BY set_codigo";
	$sql = pg_query($sql);
	$num = pg_num_rows($sql);
  if($num == "0") { $resp = "Nenhum Registro encontrado com \"$palavra_chave\""; }
  if($num == "1") { $resp = "Encontrado <b>$num</b> Registro com \"$palavra_chave\""; }
  if($num > "1") { $resp = "Encontrados <b>$num</b> Registros com \"$palavra_chave\""; }

  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>$resp</legend>
	     <table class='lista' align=center cellspacing=2 cellpadding=4 border=0>
	      <tr>
		<th width=10>Codigo</th>
		<th>Descricao</th>
		<th colspan=2>&nbsp;</th>";

     while($row=pg_fetch_array($sql)) {
       echo "<tr>
	       <td align=center>$row[set_codigo]</td>
	       <td>$row[set_nome]</td>
	       <td width=60>".ChmodBtn($id_login,'editar','setor.php?acao=form_edit&set_codigo='.$row[set_codigo])."</td>
	       <td width=66>".ChmodBtn($id_login,'apagar','setor.php?acao=del&set_codigo='.$row[set_codigo])."</td>
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
	 reglog($id_login,"Formulario de ADICAO SETOR");
	echo $common->menuTab(array("Adicionar Setor"));
	echo $common->bodyTab('1');

//
//-> Este if esta apontando quando a acao for vazia ele vai mostrar o cadastro simples
//   no cadastro completo vc vai ter que passar a variavel acao para completo

if(($type=="" OR $acao=="simples")) {
	echo $form->openForm($PHP_SELF, "POST", "formulario");
		echo $form->hiddenForm("acao", "add");
		echo $form->hiddenForm("id_login", $id_login);
		echo $form->hiddenForm("type", "simples");

		echo $form->inputText("set_nome", null, "Descri&ccedil;&atilde;o do Setor", 70, 60);
		$selectUnidade = "SELECT uni_codigo,
								 uni_desc 
							FROM unidade 
						   ORDER BY uni_desc";
		echo $form->inputSelect("uni_codigo", null, "Unidade", $selectUnidade, null, null, null, "style=width:364px;");
		$opcoes = array("S"=>"SIM", "N"=>"N&Atilde;O");
		echo $form->inputSelect("set_estoque", $opcoes, "Centro Estocador", null, null, null, "");
		echo $form->inputSelect("set_farmacia", $opcoes, "Dispensa&ccedil;&atilde;o");
		echo $form->inputSelect("set_distribuidor", $opcoes, "Distribuidor");
		echo $form->inputSelect("set_transferencia", $opcoes, "Liberado Transfer&ecirc;ncia");
		
		echo "<table style='clear:both;' width=570>
				<tr>
					<td align=right>
						".$common->commonButton("VOLTAR", "setor.php", "voltar.png", $js)."
					</td>
					<td>
						".$common->commonButton("ADICIONAR", null, "adicionar.png","onClick=\"return validaForm();\"")."
					</td>
				</tr>
			</table>";
	echo $form->closeForm();
 }//fechamento do if
 echo $common->closeTab();
}
//}

//------------------------------------------------------------------>
//-> Formulario de Edicao de Conteudo
//------------------------------------------------------------------>
if($acao=="form_edit")
{
	 reglog($id_login,"Formulario de EDICAO SETOR");
	 	echo $common->menuTab(array("Adicionar Setor"));
	echo $common->bodyTab('1');
	echo $form->openForm($PHP_SELF, "POST", "formulario");
		echo $form->hiddenForm("acao", "edit");
		echo $form->hiddenForm("id_login", $id_login);
		echo $form->hiddenForm("set_codigo", $set_codigo);
		$sqlsetor = "SELECT *  
			   FROM setor s
			   JOIN unidade u
			     ON s.uni_codigo = u.uni_codigo 
			  WHERE set_codigo = '$set_codigo'";
		$row = pg_fetch_array(pg_query($sqlsetor));
		echo $form->inputText("set_nome",  $row['set_nome'], "Descri&ccedil;&atilde;o do Setor", 70, 60);
		
		$selectUnidade = "SELECT uni_codigo,
								 uni_desc 
							FROM unidade 
						   ORDER BY uni_desc";
		echo $form->inputSelect("uni_codigo", null, "Unidade", $selectUnidade, null, null,$row['uni_codigo'], "style=width:364px;");
		
		$opcoes = array("S"=>"SIM", "N"=>"N&Atilde;O");
		
		
		echo $form->inputSelect("set_estoque", $opcoes, "Centro Estocador", null, null, null, $row['set_estoque']);
		echo $form->inputSelect("set_farmacia", $opcoes, "Dispensa&ccedil;&atilde;o", null, null, null, $row['set_farmacia']);
		echo $form->inputSelect("set_distribuidor", $opcoes, "Distribuidor", null, null, null, $row['set_distribuidor']);
		echo $form->inputSelect("set_transferencia", $opcoes, "Liberado Transfer&ecirc;ncia", null, null, null, $row['set_transferencia']);
		
		echo "<table style='clear:both;' width=570>
				<tr>
					<td align=right>
						".$common->commonButton("VOLTAR", "setor.php", "voltar.png", $js)."
					</td>
					<td>
						".$common->commonButton("editar", null, "editar_on.png","onClick=\"return validaForm();\"")."
					</td>
				</tr>
			</table>";
	echo $form->closeForm();
 //fechamento do if
	echo $common->closeTab();

 
 
/*//
//-> Pegando as informcoes do banco pra mostrar no formulario
	$sqlsetor = "SELECT *  
				   FROM setor 
				  WHERE set_codigo = '$set_codigo'";
	$row = pg_fetch_array(pg_query($sqlsetor));
	
	debug($row, $origem, $id_login);
	
	if ($row['set_estoque'] == 'S' ) {
		$vlest1 = "selected='selected'";
		$vlest2 = '';
	}
	else {
		$vlest1 = '';
		$vlest2 = "selected='selected'";
	}
	if ($row['set_farmacia'] == 'S' ) {
		$vlfar1 = "selected='selected'";
		$vlfar2 = '';
	}
	else {
		$vlfar1 = '';
		$vlfar2 = "selected='selected'";
	}
	if($row['set_distribuidor'] == 'S' )
	{
		$vldist1 = "selected='selected'";
		$vldist2 = '';
	} else {
		$vldist1 = '';
		$vldist2 = "selected='selected'";
	}
	if($row['set_transferencia'] == 'S' )
	{
		$vltransf1 = "selected='selected'";
		$vltransf2 = '';
	} else {
		$vltransf1 = '';
		$vltransf2 = "selected='selected'";
	}
	

  echo "<form method=post action=$PHP_SELF onSubmit='return validaForm();'>
	<input type=hidden name=acao value=edit>
	<input type=hidden name=id_login value=$id_login>
	<input type=hidden name=set_codigo value=$set_codigo>
	<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
		<td width=110>Descricao do Setor:</td>
		<td><input type=text name=set_nome id=set_nome class=box size=70 value='$row[set_nome]'></td>
	      </tr>
        <tr>
               <td width=70>Unidade:</td>
               <td>
                   <select name=uni_codigo id=uni_codigo class=boxr>";
                   $sql = pg_query("select * from unidade order by uni_desc");
                   echo "<option value=''>.........</option>";
                   while($unidade=pg_fetch_array($sql)) {
	                    echo 
						"<option value='$unidade[uni_codigo]' ".(($unidade[uni_codigo] == $row[uni_codigo]) ? "selected='selected'" : '').">$unidade[uni_desc]</option>";
                   }
                  echo "</select>
              </td>
        </tr>
	    <tr>
		    <td width=110>Centro Estocador:</td>
		    <td>
		        <select name=set_estoque id=set_estoque class=box>
		        	<option value='' disabled='disabled'>SELECIONE</option>
          	         <option value=S $vlest1>Sim</option>
    		         <option value=N $vlest2>Nao</option>
		        </select>
            </td>    
	    </tr>
	    <tr>
		    <td width=110>Dispensa&ccedil;&atilde;o:</td>
		    <td>
		        <select name=set_farmacia id=set_farmacia class=box>
		        	<option value='' disabled='disabled'>SELECIONE</option>
          	         <option value=S $vlfar1>Sim</option>
    		         <option value=N $vlfar2>Nao</option>
		        </select>
            </td>    
	    </tr>
		<tr>
		    <td width='110'>Distribuidor:</td>
		    <td>
		        <select name='set_distribuidor' id=set_distribuidor class='box'>
		        	<option value='' disabled='disabled'>SELECIONE</option>
          	         <option value='S' $vldist1>Sim</option>
    		         <option value='N' $vldist2>Nao</option>
		        </select>
            </td>    
	    </tr>
		<tr>
		    <td width='110'>Liberado Transferencia:</td>
		    <td>
		        <select name='set_transferencia' id=set_transferencia class='box'>
		        	<option value='' disabled='disabled'>SELECIONE</option>
          	         <option value='S' $vltransf1>Sim</option>
    		         <option value='N' $vltransf2>Nao</option>
		        </select>
            </td>    
	    </tr>
	      <tr>
	       <td>&nbsp;</td>
	       <td><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg></td>
	      </tr>
	     </table>
	  </td>
	 </tr>
        </table><br></form>";*/
}
//------------------------------------------------------------------>
//-> SQL's
//------------------------------------------------------------------>
//
//-> ADD <---------------------------------------------------------->
else if($acao=="add")
{
  reglog($id_login,"Adicionando Registro em SETOR");
  
  //print
  $stmt =
	"insert into setor (
	  set_nome,
	  set_estoque,
	  uni_codigo,
	  set_farmacia,
	  set_distribuidor,
	  set_transferencia
	) values (
	  upper('$set_nome'),
	  upper('$set_estoque'), " .
	  ($uni_codigo  ? "'$uni_codigo'"   : "null") . ", " .
	  "upper('$set_farmacia') ,
	  upper('$set_distribuidor'),
	  upper('$set_transferencia')
	)";

  $sql = db_query($stmt);
  
  reglog($id_login,"Adicionando Setor $set_nome ");
  msg($id_login,$acao,$sql);
}
//
//-> EDIT <--------------------------------------------------------->
else if($acao=="edit")
{
  //print
  $stmt =
	"UPDATE setor SET
	set_nome=upper('$set_nome'),
	set_estoque=upper('$set_estoque'),
	set_farmacia=upper('$set_farmacia'),
	set_transferencia=upper('$set_transferencia'),
	set_distribuidor=upper('$set_distribuidor'),
	uni_codigo=".intval($uni_codigo)."
	WHERE set_codigo='$set_codigo'";
  
  $sql = db_query($stmt);

  reglog($id_login,"Alterando Setor $set_nome ");
  msg($id_login,$acao,$sql);
}

//
//-> DEL <---------------------------------------------------------->
else if($acao=="del")
{
  reglog($id_login,"Exluindo Registro de SETOR $set_codigo");

  $stmt = "DELETE FROM setor where set_codigo='$set_codigo'";
  $sql = db_query($stmt);
  msg($id_login,$acao,$sql);
}

?>
