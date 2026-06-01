<script>
  function verifica_campos_inserir() {
 
	   if(document.form_adicao.med_nome.value == '') {
		alert("Por favor Preencha o Nome do Hospital");
		document.form_adicao.med_nome.focus();
		return false;
	   }
	   if(document.form_adicao.cid_codigo.value == '--- Escolha uma Cidade ---') {
		alert("Por favor Escolha a Cidade");
		document.form_adicao.cid_codigo.focus();
		return false;
	   }	   
	   if(document.form_adicao.med_cnpj.value == '') {
		alert("Por favor Preencha o CNPJ");
		document.form_adicao.med_cnpj.focus();
		return false;
	   }

	return true;
	
}
</script>
<?

/**
@Modulo: Hospital
@Tabelas: medico
@Acao: Adiciona os Hospitais.
*/ 

//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>

	session_start();
	include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
	verauth($id_login);
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();
//------------------------------------------------------------------>


//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
//------------------------------------------------------------------>

 if(empty($acao)) {

//
//-> Botoes
//<td width=156><a href=medico_especialidade.php?acao=form_med_esp><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/medico_especialidade_on.jpg border=0></a></td>
  echo "<fieldset>
	    <legend>Opções</legend>

			<a href=aih.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a>		
			".ChmodBtn($id_login,'adicionar','hospital.php?acao=form_add&id_login=$id_login')."
	   		
			<table width=100% align=center cellspacing=3 cellpadding=0 border=0>
				<tr>";
				if(chmodbtn($id_login, 'procurar_if', 'hospital.php'))
				{
					echo "<form method='post' action='$PHP_SELF?$_SERVER[QUERY_STRING]'>";
				}
					echo "<input type=hidden name=acao value=busca>
						<td width=30>Buscar:</td>
						<td width=120><input type='text' name=palavra_chave class=box onBlur=\"javascript:this.value=this.value.toUpperCase();\"></td>
						<td>".ChmodBtn($id_login,'procurar','hospital.php')."</td>
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
	    <legend>Listando Hospitais Cadastrados</legend>
	     <table width='100%' align='center' cellspacing='2' cellpadding='4' border='0' class='lista'>
	      <tr bgcolor=FFFFFF>
		   <th width='*'>Nome do Hospital</th>
		   <th width='110'>CNPJ</th>
		   <th width='100'>CNES</th>
		   <th width='65' align='center'>&nbsp;</th>
		  </tr>";
	if(chmodbtn($id_login, "listar_if", "hospital.php"))
	{
	   $sql		=	"SELECT med_codigo, med_nome, med_cnes, med_cnpj ".
	   				"FROM medico ".
					"WHERE prestador_servico='H' ".
					"ORDER BY med_nome asc";
	   $query 	= 	db_query($sql);
	}
	   
	   while($row=pg_fetch_array($query)) {

	   echo "<tr>
			   <td width='*'>$row[med_nome]</td>
			   <td width='110'>$row[med_cnpj]</td>
			   <td width='100'>$row[med_cnes]</td>
			   <td width='65' align='center'>".
			   chmodbtn($id_login, "editar", "hospital.php?id_login=$id_login&acao=form_edit&med_codigo=$row[med_codigo]")
				."</td>
			 </tr>";
		 }
		 
	   echo "</table>
	   </fieldset>
	  </td>
	 </tr>
      </table>";
}


//------------------------------------------------------------------>
//-> Mostrando o resultado da busca
//------------------------------------------------------------------>

 if($acao=="busca") {

	$palavra_chave = strtoupper($palavra_chave);
//-> Subistituindo o + por porcentagem na busca
	$str = str_replace("+","%",$palavra_chave);
	$pos = strpos($palavra_chave,"+");

	if($pos=="0") {
	 $v1=1;
	} else {
	 $v1=2;
	}

//-> Botoes
  echo "<fieldset>
	    <legend>Opções</legend>
		  
			".ChmodBtn($id_login,'adicionar','hospital.php?acao=form_add&id_login=$id_login')."
	   		
			<table width=100% align=center cellspacing=3 cellpadding=0 border=0>
				<tr>";
				if(chmodbtn($id_login, 'procurar_if', 'hospital.php'))
				{
					echo "<form method='post' action='$PHP_SELF?$_SERVER[QUERY_STRING]'>";
				}
					echo "<input type=hidden name=acao value=busca>
						<td width=30>Buscar:</td>
						<td width=120><input type='text' name=palavra_chave class=box onBlur=\"javascript:this.value=this.value.toUpperCase();\"></td>
						<td>".ChmodBtn($id_login,'procurar','hospital.php')."</td>
					</form>
				</tr>
			</table>
		 
	   </fieldset>
	  <br>";

if(chmodbtn($id_login, "listar_if", "hospital.php"))
{
	$sql	=	"SELECT med_codigo, med_nome, med_cnes, med_cnpj ".
				"FROM medico ".
				"WHERE prestador_servico='H' ".
				"AND (med_nome like '%$palavra_chave%')";
	$query 	=	db_query($sql);
}
	$num	=	pg_num_rows($query);

	if($num=="0") { $resp = "Nenhum Registro encontrado com \"$palavra_chave\""; }
	if($num=="1") { $resp = "Encontrado <b>$num</b> Registro com \"$palavra_chave\""; }
	if($num>"1") { $resp = "Encontrados <b>$num</b> Registros com \"$palavra_chave\""; }

echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>".$resp."</legend>
	     <table width='100%' align='center' cellspacing='2' cellpadding='4' border='0' class='lista'>
	      <tr bgcolor=FFFFFF>
		   <th width='*'>Nome do Hospital</th>
		   <th width='110'>CNPJ</th>
		   <th width='100'>CNES</th>
		   <th width='65' align='center'>&nbsp;</th>
		  </tr>";

	   while($row=pg_fetch_array($query)) {

	   echo "<tr>
			   <td width='*'>$row[med_nome]</td>
			   <td width='110'>$row[med_cnpj]</td>
			   <td width='100'>$row[med_cnes]</td>
			   <td width='65' align='center'>".
			   chmodbtn($id_login, "editar", "hospital.php?id_login=$id_login&acao=form_edit&med_codigo=$row[med_codigo]")
				."</td>
			 </tr>";
		 }
		 
	   echo "</table>
	   </fieldset>
	  </td>
	 </tr>
      </table>";
}

//------------------------------------------------------------------>
//-> Formulario de Adicao de Conteudo
//------------------------------------------------------------------>

 if($acao=="form_add") {

//
//-> Abaixo sao os botoes de voltar / cadastro simples / cadastro completo

  echo "<fieldset>
	    <legend>Opções de Cadastro</legend>
	       <a href=hospital.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a>
	    </fieldset>
	  <br>";


  echo "<form name='form_adicao' method='post' action='$PHP_SELF?id_login=$id_login' onSubmit='return verifica_campos_inserir()'>
		<input type=hidden name=acao value=add>

	   <fieldset>
	    <legend>Cadastro de Hospital</legend>
	     <table width='100%' align='center' cellspacing='3' cellpadding='0' border='0'>
	      <tr>
			<td>Nome:</td>
			<td><input type='text' name='med_nome' class='box' size='69' maxlength='60'></td>
	      </tr>
	      <tr>
			<td>Nome Fantasia:</td>
			<td><input type='text' name='med_nome_fantasia' class='box' size='69' maxlength='60'></td>
	      </tr>
	      <tr>
		<td width=70>E-mail: </td>
		<td><input type='text' name='med_email' class='box' size='69' maxlength='60'></td>
	      </tr>
	      <tr>
		<td width=70>Endere&ccedil;o:</td>
		<td><input type='text' name='med_endereco' class='box' size='69' maxlength='60'></td>
	      </tr>
	      <tr>
		<td width=70>Num:</td>
		<td><input type='text' name='med_end_numero' class='box' size='69' maxlength='60'></td>
	      </tr>
	      <tr>
		<td width=70>Comlemento:</td>
		<td><input type='text' name='med_end_complemento' class='box' size='69' maxlength='60'></td>
	      </tr>
	      <tr>
		<td width=70>Bairro:</td>
		<td><input type='text' name='med_end_bairro' class='box' size='69' maxlength='60'></td>
	      </tr>
	      <tr>
		<td width=70>CEP:</td>
		<td><input type='text' name='med_end_cep' class='box' size='69' maxlength='60'></td>
	      </tr>
	      <tr>
		<td width=70>Telefone:</td>
		<td><input type='text' name='med_end_telefone' class='box' size='69' maxlength='60'></td>
	      </tr>
	      <tr>
		<td width=70>Celular:</td>
		<td><input type='text' name='med_end_celular' class='box' size='69' maxlength='60'></td>
	      </tr>
	      <tr>
	        <td>CNES:</td>
	        <td><input type='text' name='med_cnes' class='box' size='69' maxlength='7' /></td>
          </tr>
		<tr>
			<td width='70'>CRM:</td>
			<td>
				<input type='text' name='med_crm' class='box' size='10' />
			</td>
		</tr>
	<tr>
		<td>Estado CRM:</td>
		<td>
		<select name='uf_codigo_crm' class='box'>";
	//
	//-> SQL do Estado
	$stmt = "SELECT * FROM estado ORDER BY uf_sigla";
	$query = db_query($stmt);
	while($uf=pg_fetch_array($query))
	{
		echo ($uf['uf_codigo']==$row['uf_codigo_crm']) ? 
			"<option value='$uf[uf_codigo]' selected>$uf[uf_sigla]</option>":
			"<option value='$uf[uf_codigo]'>$uf[uf_sigla]</option>";
	}
	echo "
		</select>
		</td>
	</tr>		
	      <tr>
			<td width=70>Cidade:</td>
			<td>
			 <select name='cid_codigo' class='box'>";
			
			//-> SQL da Cidade
			$query = pg_query("SELECT cid_codigo, cid_nome FROM cidade WHERE uf_codigo = 18 ORDER BY cid_nome ");
			
					echo "<option selected>--- Escolha uma Cidade ---</option>";

			  while($cidade=pg_fetch_array($query)) {
			  
					echo "<option value='$cidade[cid_codigo]'>$cidade[cid_nome]</option>";
					
			  }
			  
	   echo "</select>		    
	   </td>
	      </tr>
	      <tr>
		<td width=70>CNPJ:</td>
		<td><input type='text' name='med_cnpj' class='box' size='24' maxlength='16'/></td>
	      </tr>
	      <tr>
		<td width=70>Mantenedora:</td>
		<td><input type='text' name='med_mantenedora' class='box' size='69' maxlength='60'/></td>
	      </tr>
	      <tr>
		<td width=70>Regional:</td>
		<td><input type='text' name='med_regional' class='box' size='69' maxlength='60'/></td>
	      </tr>		  
      	    <tr>
			<td>Logradouro:</td>
			<td>
			<select name='logra_codigo' class='box'>";
			
	    $query = pg_query("SELECT logra_codigo, logra_logradouro FROM logradouro ORDER BY logra_logradouro");
	    while($logra_logradouro=pg_fetch_array($query)) {
	       echo "<option value='$logra_logradouro[logra_codigo]'>$logra_logradouro[logra_logradouro]</option>";
	      }			
			echo "</select>			
			</td>	    
	    </tr>		  
	      <tr>
	       <td>&nbsp;</td>
	       <td><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg></td>
	      </tr>
		  
	     </table>
	   </fieldset>
	   <br />
		</form>";

}

//}

//------------------------------------------------------------------>
//-> Formulario de Edicao de Conteudo
//------------------------------------------------------------------>


 if($acao=="form_edit") {
//
//-> Botoes
  echo "<fieldset>
	    <legend>Opções</legend>
	        
			<a href=hospital.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a>
			<a href=$PHP_SELF?acao=form_add&id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg border=0></a>
		 
	   </fieldset>
	  <br>";

//-> Pegando as informcoes do banco pra mostrar no formulario
 $sqlmedico = "SELECT * FROM medico WHERE prestador_servico='H' AND med_codigo='$med_codigo'";
 $row=pg_fetch_array(pg_query($sqlmedico));



  echo "<form name='form_adicao' method='post' action='$PHP_SELF?id_login=$id_login' onSubmit='return verifica_campos_inserir()'>
		<input type=hidden name=acao value=edit>
		<input type=hidden name=med_codigo value=$med_codigo>

	   <fieldset>
	    <legend>Alteração de Hospital</legend>
	     <table width='100%' align='center' cellspacing='3' cellpadding='0' border='0'>
	      <tr>
			<td>Nome:</td>
			<td><input type='text' name='med_nome' class='box' size='69' maxlength='60' value='$row[med_nome]' /></td>
	      </tr>
	      <tr>
			<td>Nome Fantasia:</td>
			<td><input type='text' name='med_nome_fantasia' class='box' size='69' maxlength='60' $row[med_nome_fantasia]></td>
	      </tr>
	      <tr>
		<td width=70>E-mail: </td>
		<td><input type='text' name='med_email' class='box' size='69' maxlength='60' value='$row[med_email]' /></td>
	      </tr>
	      <tr>
		<td width=70>Endere&ccedil;o:</td>
		<td><input type='text' name='med_endereco' class='box' size='69' maxlength='60' value='$row[med_endereco]' /></td>
	      </tr>
	      <tr>
		<td width=70>Num:</td>
		<td><input type='text' name='med_end_numero' class='box' size='69' maxlength='60' value='$row[med_end_numero]' /></td>
	      </tr>
	      <tr>
		<td width=70>Comlemento:</td>
		<td><input type='text' name='med_end_complemento' class='box' size='69' maxlength='60' value='$row[med_end_complemento]' /></td>
	      </tr>
	      <tr>
		<td width=70>Bairro:</td>
		<td><input type='text' name='med_end_bairro' class='box' size='69' maxlength='60' value='$row[med_end_bairro]' /></td>
	      </tr>
	      <tr>
		<td width=70>CEP:</td>
		<td><input type='text' name='med_end_cep' class='box' size='69' maxlength='60' value='$row[med_end_cep]' ></td>
	      </tr>
	      <tr>
		<td width=70>Telefone:</td>
		<td><input type='text' name='med_end_telefone' class='box' size='69' maxlength='60' /value='$row[med_end_telefone]' /></td>
	      </tr>
	      <tr>
		<td width=70>Celular:</td>
		<td><input type='text' name='med_end_celular' class='box' size='69' maxlength='60' value='$row[med_end_celular]' /></td>
	      </tr>
	      <tr>
	        <td>CNES:</td>
	        <td><input type='text' name='med_cnes' class='box' size='69' maxlength='7' value='$row[med_cnes]' /></td>
          </tr>
		<tr>
			<td width='70'>CRM:</td>
			<td>
				<input type='text' name='med_crm' class='box' size='20' value='$row[med_crm]' />
			</td>
		</tr>
	<tr>
		<td>Estado CRM:</td>
		<td>
		<select name='uf_codigo_crm' class='box'>";
	//
	//-> SQL do Estado
	$stmt = "SELECT * FROM estado ORDER BY uf_sigla";
	$query = db_query($stmt);
	while($uf=pg_fetch_array($query))
	{
		echo ($uf['uf_codigo']==$row['uf_codigo_crm']) ? 
			"<option value='$uf[uf_codigo]' selected>$uf[uf_sigla]</option>":
			"<option value='$uf[uf_codigo]'>$uf[uf_sigla]</option>";
	}
	echo "
		</select>
		</td>
	</tr>
	      <tr>
			<td width=70>Cidade:</td>
			<td>
			 <select name='cid_codigo' class='box'>";
			
			//-> SQL da Cidade
			$query = pg_query("SELECT cid_codigo, cid_nome FROM cidade WHERE uf_codigo = 18 ORDER BY cid_nome ");
			
			  while($cidade=pg_fetch_array($query)) {
			  
	       			echo ($cidade[cid_codigo]==$row[cid_codigo])?"<option value='$cidade[cid_codigo]' selected>$cidade[cid_nome]</option>":"<option value='$cidade[cid_codigo]'>$cidade[cid_nome]</option>";
					
			  }
			  
	   echo "</select>		    </td>
	      </tr>
	      <tr>
		<td width=70>CNPJ:</td>
		<td><input type='text' name='med_cnpj' class='box' size='24' maxlength='16' value='$row[med_cnpj]' /></td>
	      </tr>
	      <tr>
		<td width=70>Mantenedora:</td>
		<td><input type='text' name='med_mantenedora' class='box' size='69' maxlength='60' value='$row[med_mantenedora]' /></td>
	      </tr>
	      <tr>
		<td width=70>Regional:</td>
		<td><input type='text' name='med_regional' class='box' size='69' maxlength='60' value='$row[med_regional]' /></td>
	      </tr>		  
      	    <tr>
			<td>Logradouro:</td>
			<td>
			<select name='logra_codigo' class='box'>";
			
	    $query = pg_query("SELECT logra_codigo, logra_logradouro FROM logradouro ORDER BY logra_logradouro");
	    while($logra_logradouro=pg_fetch_array($query)) {
	       echo "<option value='$logra_logradouro[logra_codigo]'>$logra_logradouro[logra_logradouro]</option>";
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
	   <br />
		</form>";

}

//------------------------------------------------------------------>
//-> SQL's
//------------------------------------------------------------------>
//
//-> ADD <---------------------------------------------------------->

 if($acao=="add") {
 
		$sql = "INSERT INTO medico ( " .
				"med_nome, " .
				"med_email, ".
				"med_endereco, ".
				"cid_codigo, ".
				"med_cnpj, ".
				"med_cnes, ". 
				"prestador_servico, ".	
				"med_crm, ".		
				"uf_codigo_crm, ".
				"med_nome_fantasia, ".
				"med_end_numero, ".
				"med_end_complemento, ".
				"med_end_bairro, ".
				"med_end_cep, ".
				"med_end_telefone, ".
				"med_end_celular, ".
				"med_mantenedora, ".
  				"med_regional, ".
  				"logra_codigo ".
            ") VALUES ( " .
				"upper('$med_nome') " .
				", ".( trim($med_email) 	==	'' ? 'null' : " '$med_email' ") . 			
				", ".( trim($med_endereco) 	==	'' ? 'null' : " upper('$med_endereco') ") .
				", ".( trim($cid_codigo) 	==	'' ? 'null' : " '$cid_codigo' ") .
				", '$med_cnpj' ".
				", ".( trim($med_cnes)		==	'' ? 'null' : " '$med_cnes' ").
				", 'H' ".
				", ".( trim($med_crm)  == '' ? 'null' : " '$med_crm' ").
				", ".( trim($uf_codigo_crm) == '' ? 'null' : " '$uf_codigo_crm' ").
				", ".( trim($med_nome_fantasia) == '' ? 'null' : " '$med_nome_fantasia' ").
				", ".( trim($med_end_numero) == '' ? 'null' : " '$med_end_numero' ").
				", ".( trim($med_end_complemento) == '' ? 'null' : " '$med_end_complemento' ").
				", ".( trim($med_end_bairro) == '' ? 'null' : " '$med_end_bairro' ").
				", ".( trim($med_end_cep) == '' ? 'null' : " '$med_end_cep' ").
				", ".( trim($med_end_telefone) == '' ? 'null' : " '$med_end_telefone' ").
				", ".( trim($med_end_celular) == '' ? 'null' : " '$med_end_celular' "). 
				", ".( trim($med_mantenedora) == '' ? 'null' : " '$med_mantenedora' "). 
				", ".( trim($med_regional) == '' ? 'null' : " '$med_regional' "). 
				", ".( trim($logra_codigo) == '' ? 'null' : " '$logra_codigo' "). 
				"	)";

	$query = db_query($sql);
	msg($id_login,$acao,$sql);
	
 }

//
//-> EDIT <--------------------------------------------------------->

 if($acao=="edit") {
 	
 
	$sql = 	"UPDATE medico SET " .
         	"med_nome=upper('$med_nome')" .
         	", med_email=" .( trim($med_email) 										== '' ? 'null' : " '$med_email' ") . 
         	", med_endereco=" .( trim($med_endereco) 								== '' ? 'null' : " upper('$med_endereco') ") . 
         	", cid_codigo=" .( trim($cid_codigo) 											== '' ? 'null' : " '$cid_codigo' ") . 
         	", med_cnpj=" .( trim($med_cnpj) 												== '' ? 'null' : " '$med_cnpj' ") . 
         	", med_cnes=" .( trim($med_cnes) 											== '' ? 'null' : " '$med_cnes' ") .
         	", med_crm=" .( trim($med_crm)												== '' ? 'null' : " '$med_crm' ") . 
         	", uf_codigo_crm=" .( trim($uf_codigo_crm)								== '' ? 'null' : " '$uf_codigo_crm' ") . 
         	", med_nome_fantasia=" .( trim($med_nome_fantasia)				== '' ? 'null' : " '$med_end_numero' ") . 
         	", med_end_numero=" .( trim($med_end_numero)						== '' ? 'null' : " '$med_cnes' ") . 
         	", med_end_complemento=" .( trim($med_end_complemento)		== '' ? 'null' : " '$med_end_complemento' ") . 
         	", med_end_bairro=" .( trim($med_end_bairro)							== '' ? 'null' : " '$med_end_bairro' ") . 
         	", med_end_cep=" .( trim($med_end_cep)									== '' ? 'null' : " '$med_end_cep' ") . 
         	", med_end_telefone=" .( trim($med_end_telefone)					== '' ? 'null' : " '$med_end_telefone' ") . 
         	", med_end_celular=" .( trim($med_end_celular)						== '' ? 'null' : " '$med_end_celular' ") . 	
         	", med_mantenedora=" .( trim($med_mantenedora)					== '' ? 'null' : " '$med_mantenedora' ") . 	
         	", med_regional=" .( trim($med_regional)									== '' ? 'null' : " '$med_regional' ") . 	
         	", logra_codigo=" .( trim($logra_codigo)									== '' ? 'null' : " '$logra_codigo' ") . 	
         	" where med_codigo='$med_codigo'" ;
			
	$query = db_query($sql);
	msg($id_login,$acao,$sql);
}

?>
