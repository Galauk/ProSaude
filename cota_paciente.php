<?

	include_once "authlib.inc.php";
	verauth($id_login);
	
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();
	include_once "lib/debug.inc.php";
?>
<script language="JavaScript" type="text/javascript" src="funcoes.js"></script>
<script language="JavaScript" type="text/javascript" src="ajax_motor.js"></script>
<script type="text/javascript">
function callback_prg( text ){
	var ProArr = ( eval(text) );
	var ProSel = document.getElementById("pro_codigo");
	
	ProSel.length = 0;
	for( var i=0; i < ProArr.length; i++ )
	{
		ProArr[ i ].pro_nome = unescape( ProArr[ i ].pro_nome );
		ProSel.options[ ProSel.options.length ]=new Option( ProArr[ i ].pro_nome,  ProArr[ i ].prgp_codigo );
	}
}

function edita(ctp_codigo, ctp_quantidade, ctp_periodo, prg_codigo,prgp_codigo){
	document.cotaSetor.ctp_codigo.value = ctp_codigo;
	document.cotaSetor.ctp_quantidade.value = ctp_quantidade;
	document.cotaSetor.ctp_periodo.value = ctp_periodo;
	document.cotaSetor.prg_codigo.value = prg_codigo;
	ajax_tudo( "op_programa_produto.php?prg="+prg_codigo, callback_prg )
}

function verifica() {
   if(document.paciente.usu_nome.value == '') {
	alert("Por favor Preencha o Nome");
	return false;
   }

   if(document.paciente.usu_mae.value == '') {
	alert("Por favor Preencha o Nome da Mae");
	return false;
   }

   if(document.paciente.usu_datanasc.value == '') {
	alert("Por favor Preencha a Data de Nascimento");
	return false;
   }

   if(document.paciente.usu_end_rua.value == '') {
	alert("Por favor Preencha a Rua");
	return false;
   }

   if(document.paciente.usu_end_nr.value == '') {
	alert("Por favor Preencha o Numero");
	return false;
   }

   if(document.paciente.usu_end_bairro.value == '') {
	alert("Por favor Preencha o Bairro");
	return false;
   }

   if(document.paciente.usu_end_cidade.value == '') {
	alert("Por favor Preencha a Cidade");
	return false;
   }


 return true;
}

function validaCampos(){
	
	var quantidade = document.getElementById('ctp_quantidade');
	var pro_codigo = document.getElementById('pro_codigo');
	var programa_codigo = document.getElementById('prg_codigo');
	var periodo = document.getElementById('periodo');
	if(quantidade.value == ""){
		alert("Preencha o campo quantidade");
		quantidade.focus();
		return false;
	} 	
	if(programa_codigo.value == 0){pro_codigo
		alert("Preencha o campo Programa Atendimento");
		programa_codigo.focus();
		return false;
	}
	if(periodo.value == ""){
		alert("Preencha o campo quantidade");
		periodo.focus();
		return false;
	}
	if(pro_codigo.value == "Selecione"){
		alert("Preencha o campo Produto");
		pro_codigo.focus();
		return false;
	}
	document.cotaSetor.ctp_codigo.value='';
	document.cotaSetor.submit();
	return true;
	
}
function confirma(cod,usu){	
	if (window.confirm (" Deseja Apagar Esse Registro ? ")) {	
		location.replace("cota_paciente.php?acao=add&del="+cod+"&usu_codigo="+usu);
	} else { 
		return false;
	}
}
</script>
<?

//------------------------------------------------------------------>


//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
	 reglog($id_login,"Entrando em PACIENTE");
//------------------------------------------------------------------>
 if(empty($acao)) {
	$acao = "busca"; 
}
//------------------------------------------------------------------>
//-> Mostrando o resultado da busca
//------------------------------------------------------------------>

 if($acao=="busca") {
//
//-> Verificando Busca
 reglog($id_login,"Buscando em PACIENTE: $palavra_chave ");
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
echo "<fieldset><legend>COTAS PROD. POR PACIENTE</legend>";
  echo "<table width='98%' align='center' cellspacing='0' cellpadding='0' border='0'>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Op&ccedil;&otilde;es</legend>
	     <table width='100%' align='center' cellspacing='3' cellpadding='0' border='0'>
	      <tr>";
	//		<td width='200'>".ChmodBtn($id_login,'adicionar','paciente.php?acao=form_add&type=s')."</td>
        if (chmodbtn($id_login,"procurar_if","cota_paciente.php"))
        {
              echo "<form method=post action=$PHP_SELF>";
        }
       echo   "<input type='hidden' name='acao' value=busca>
		<input type='hidden' name=id_login value=$id_login>
	       <td width='30'>Buscar:</td>
	       <td width='80'><input type='text' name=palavra_chave class='box' onBlur=\"javascript:this.value=this.value.toUpperCase();\"></td>
	       <td width='40'>
	       <select name=tpbusca class='box'>
			   <option value=dt>Data de Nascimento</option>
			   <option value=sbr>Sobrenome</option>
			   <option value=pr>Prontuario</option>
			   <option value=n selected>Nome</option>
			   <option value=nm>Nome da M&atilde;e</option>
		   </select></td>
	       <td>".ChmodBtn($id_login,'procurar','cota_paciente.php')."</td>
		</form>
              <td width='107'><a href='farmacia.php?id_login=$id_login'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border='0'></a></td>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br>";
$palavra_chave = strtoupper($palavra_chave);
$tpbusca = $_POST['tpbusca'];

if($tpbusca=="dt") {	
   $sql="select usu_prontuario, usu_codigo,usu_nome,to_char(usu_datanasc,'DD/MM/YYYY') as usu_datanasc,usu_mae,usu_sexo from usuario where to_char(usu_datanasc,'DD/MM/YYYY') like '%$palavra_chave%'";
  
}

if($tpbusca=="sbr") {
   $sql="select usu_prontuario, usu_codigo,usu_nome,to_char(usu_datanasc,'DD/MM/YYYY') as usu_datanasc,usu_mae,usu_sexo from usuario where usu_nome like '%$palavra_chave%'";
}

if($tpbusca=="pr") {
   $sql="select usu_prontuario, usu_codigo,usu_nome,to_char(usu_datanasc,'DD/MM/YYYY') as usu_datanasc,usu_mae,usu_sexo from usuario where usu_prontuario like '%$palavra_chave%'";
}

if($tpbusca=="n") {
   $sql="select usu_prontuario, usu_codigo,usu_nome,to_char(usu_datanasc,'DD/MM/YYYY') as usu_datanasc,usu_mae,usu_sexo from usuario where usu_nome like '%$palavra_chave%'";
}

if($tpbusca=="nm") {
   $sql="select usu_prontuario, usu_codigo,usu_nome,to_char(usu_datanasc,'DD/MM/YYYY') as usu_datanasc,usu_mae,usu_sexo from usuario where usu_mae like '%$palavra_chave%'";
}
if($palavra_chave == ""){
	   $sql="select usu_prontuario, usu_codigo,usu_nome,to_char(usu_datanasc,'DD/MM/YYYY') as usu_datanasc,usu_mae,usu_sexo from usuario order by usu_codigo desc limit 15";   
}

       if (chmodbtn($id_login,"listar_if","cota_paciente.php")) // faz funcionar a permiss�o "listagem" cota paciente
       {
                     $sql = pg_query($sql);
                     
                     $num=pg_num_rows($sql);
                       if($num=="0") { $resp = "Nenhum Registro encontrado com \"$palavra_chave\""; }
                       if($num=="1") { $resp = "Encontrado <b>$num</b> Registro com \"$palavra_chave\""; }
                       if($num>"1") { $resp = "Encontrados <b>$num</b> Registros com \"$palavra_chave\""; }
                     
                       echo "<table width='98%' align='center' cellspacing='0' cellpadding='0' border='0'>
                              <tr>
                               <td>
                                <fieldset>
                                <fieldset>
                                 <legend>Pacientes</legend>
                                  <table width='100%' align='center' cellspacing=2 cellpadding=4 border='0'>
                                   <tr bgcolor=F9f9f9>
                                     <td width='40' style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>C&oacute;digo</td>
                                     <td width='40' style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Prontu&aacute;rio</td>
                                     <td width='270' style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Nome</td>
                                     <td width='20' style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Sexo</td>
                                     <td width='50' style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Dt. Nasc.</td>
                                     <td width='270' style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>N. M&atilde;e</td>
                                     <td colspan='6' style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td>";
                     
                          while($row=pg_fetch_array($sql)) {
                            echo "<tr>
                                    <td align='center' style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[usu_codigo]</td>
                                    <td align='center' style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[usu_prontuario]</td>
                                    <td width='270' style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[usu_nome]</td>
                                    <td align='center' style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[usu_sexo]</td>
                                    <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[usu_datanasc]</td>
                                    <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[usu_mae]&nbsp;</td>";
                     
                            echo "<td width='66' style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".chmodbtnjs($id_login,"adicionar","cota_paciente.php","location.replace('cota_paciente.php?id_login=$id_login&acao=add&usu_codigo=$row[usu_codigo]')")."</td>";
//                     echo "<td width='66' style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'><img  src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg onclick=\"location.replace('cota_paciente.php?id_login=$id_login&acao=add&usu_codigo=$row[usu_codigo]')\"></td>
                            echo "</tr>";
                          }
       }                          
	echo "</tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table>";
}
if ($acao == "add")
{
	if ($_POST['envia'])
	{
		$_POST['pro_codigo'] != "Selecione" ? $pc = intval($pro_codigo) : $pc = 'NULL';
		if($_POST['ctp_codigo']=="")
		{
			reglog($id_login,"Adicionando Registro em SETOR");
			 $sql = "INSERT INTO cota_paciente (
					ctp_quantidade,
					ctp_periodo,
					usu_codigo,
					prgp_codigo
					 ) VALUES (
					".floatval($ctp_quantidade).",
					'".trim(strtoupper(substr($ctp_periodo,0,10)))."',
					".intval($usu_codigo).",
					".$prgp_codigo.")";
			$sql =	pg_query($sql);
			reglog($id_login,"Adicionando Setor $set_nome ");
		}
		else if ($_POST['ctp_codigo']!="")
		{
			 $sql = pg_query("UPDATE cota_paciente SET
				ctp_quantidade = ".floatval($ctp_quantidade).",
				ctp_periodo = '".trim(strtoupper(substr($ctp_periodo,0,10)))."',
				usu_codigo = ".intval($usu_codigo).",
				prgp_codigo = ".$prgp_codigo.",
				WHERE ctp_codigo = ".intval($ctp_codigo));
			reglog($id_login,"Alterando Setor $set_nome ");
		}
	}
//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
	 reglog($id_login,"Entrando em SETOR");
//------------------------------------------------------------------>
//------------------------------------------------------------------>
//-> Formulario de Edicao de Conteudo
//------------------------------------------------------------------>


	 reglog($id_login,"Formulario de EDICAO SETOR");
//
//-> Formulario de edicao do cadastro SIMPLES

//
//-> Abaixo sao os botoes de voltar / cadastro simples / cadastro completo

  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Op&ccedil;&otilde;es de Cadastro</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
	       <td width=79><a href=cota_paciente.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
	       <td>&nbsp;</td>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br>";
//
//-> Pegando as informcoes do banco pra mostrar no formulario
 $sql = pg_query("SELECT usu_nome FROM usuario WHERE usu_codigo = $usu_codigo");
$nome = pg_fetch_array($sql);

  echo "<form name=\"cotaSetor\" method=\"POST\" action=''>";
	debug($_REQUEST, $PHP_SELF, $id_login);
  echo"<input type=hidden name=id_login value=$id_login>
	<input type=hidden name=usu_codigo value=$usu_codigo>
	<input type=hidden name=ctp_codigo value=$ctp_codigo>
	<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Cadastro de Cotas/Setor</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
		<td width=110>Paciente:</td>
		<td><input type=text readonly name='usu_nome' class=box size=70 value='$nome[usu_nome]'></td>
	      </tr>
	      <tr>
		<td width=110>Quantidade:</td>
		<td><input type=text id=ctp_quantidade name=ctp_quantidade class=box size=70 value=''></td>
	      </tr>
	      <tr>
		<td width=110>Per&iacute;odo:</td>
		<td>
			<select class='box'id='periodo' name='ctp_periodo'>
				<option value='SEMANAL'>Semanal</option>
				<option value='MENSAL'>Mensal</option>
				<option value='BIMESTRAL'>Bimestral</option>
				<option value='TRIMESTRAL'>Trimestral</option>
				<option value='SEMESRAL'>Semestral</option>
				<option value='ANUAL'>Anual</option>
			</select>
		</td>
	      </tr>
	      <tr>
		<td width=110>Programa Atendimento:</td>
		<td>
			<select class='box' id='prg_codigo' name='prg_codigo'>
				<option value=0>Selecione</option>";
				$sql  = "select * from programa_atendimento order by prg_nome";
				$exec_sql = pg_query($sql);
				while ($dados = pg_fetch_array($exec_sql))
				{
					echo "<option id=$dados[prg_codigo] value=$dados[prg_codigo]>$dados[prg_nome]</option>";
				}
echo "		</select>
		</td>
	      </tr>
	      <tr>
		<td width=110>Produtos:</td>
		<td>
			<select class='box' id='pro_codigo' name='prgp_codigo'>
				<option>Selecione</option>";
echo "		</select>
		</td>
	      </tr>
	      <tr>
	       <td>&nbsp;</td>
	       <td><input  type='hidden' name='envia' value='e'/>
		<img  src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg onclick=\"return validaCampos();\" >
		</td>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br></form>";

      if(isset($_GET['del'])!=''){

		$sql = "delete from cota_paciente where ctp_codigo = {$_GET['del']}";
         $exec_sql = pg_query($sql);
         
         if($exec_sql){
         	echo"<script>
         		alert(Registro Deletado!);
         	</script>";
         }else{
         	echo"<script>
         		alert(Erro ao Apagar!);
         	</script>";
         }

      }
?>
<fieldset>
<legend>Cotas</legend>
<table class="lista" width='100%'>
<tr>
	<th>PRODUTOS</th>
	<th>PROGRAMAS</th>
	<th>&nbsp;</th>
</tr>
<?
	/*
	$sql = "SELECT a.*,c.prg_codigo,c.prg_nome,d.pro_nome,b.prgp_codigo 
			FROM cota_paciente a, programa_produto b, programa_atendimento c, produto d
			WHERE a.usu_codigo = $usu_codigo 
			AND b.pro_codigo = d.pro_codigo 
			AND b.prg_codigo = c.prg_codigo 
			AND a.prgp_codigo = b.prgp_codigo";
	*/
	$sql = "SELECT pro_nome,prg_nome, * FROM cota_paciente  cp
			 JOIN programa_produto pp
			   ON cp.prgp_codigo = pp.prgp_codigo
			 JOIN produto p
			   ON p.pro_codigo = pp.pro_codigo
			 JOIN programa_atendimento pa
			   ON pa.prg_codigo = pp.prg_codigo
		              WHERE usu_codigo = ".$usu_codigo;
	//rint $sql;
	$sql = pg_query($sql);
	while ($dados = pg_fetch_array($sql))
	{
		echo"<tr>
				<td>$dados[pro_nome]</td>
				<td>$dados[prg_nome]</td>
				<td><input type=\"image\"  src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.jpg name=excluir  value='del' onclick=\"return confirma($dados[ctp_codigo],$usu_codigo); \"></td>
			</tr>";
		//location.replace('cota_paciente.php?acao=add&del=$dados[ctp_codigo]&usu_codigo=$usu_codigo')
	  /*    $sql_produto = "SELECT pa.prg_nome, pp.prgp_codigo FROM programa_produto AS pp
                            LEFT JOIN programa_atendimento AS pa ON pa.prg_codigo = pp.prg_codigo
                            WHERE pp.pro_codigo = ".$dados[0];
              $res_produto = pg_query($sql_produto);
              
              if( pg_num_rows($res_produto) > 0 )
              {
                     while( $row_produto = pg_fetch_array($res_produto) )
                     {
                            $sql_ctp = "SELECT ctp_codigo FROM cota_paciente WHERE prgp_codigo = ".$row_produto[1]."
                                          AND usu_codigo = ".$usu_codigo;
                            //echo "<pre>".$sql_ctp."</pre>";
                            if( $row_ctp = db_get($sql_ctp) )
                            {
                                   echo "<tr><td>".$dados['pro_nome']."</td><td>".$row_produto[0]."</td>
                                   <td><input type=\"image\"  src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.jpg name=excluir  value='del' onclick=\"location.replace('cota_paciente.php?acao=add&del=$row_ctp&usu_codigo=$usu_codigo')\"></td></tr>";
                            }
                     }
              }*/
	}
?>
</table>
</fieldset>
<script>
	document.getElementById("prg_codigo").onchange = function()
	{
		var Pro = document.getElementById("pro_codigo");
		Pro.length = 1;
		Pro.options[0].value = 0;
		Pro.options[0].text = "...carregando..." ;
		ajax_tudo( "op_programa_produto.php?prg="+this.value, callback_prg );
	}
</script>
<?}?>
</fieldset>
