<script language="JavaScript" type="text/javascript" src="funcoes.js"></script>
<script>
function edita(ctp_codigo, ctp_quantidade, ctp_periodo, pro_codigo)
{
	document.cotaSetor.ctp_codigo.value = ctp_codigo;
	document.cotaSetor.ctp_quantidade.value = ctp_quantidade;
	document.cotaSetor.ctp_periodo.value = ctp_periodo;
	document.cotaSetor.pro_codigo.value = pro_codigo;
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
</script>
<?
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
	 reglog($id_login,"Entrando em PACIENTE");
//------------------------------------------------------------------>
//acao vazia vai pro buscar
 if(empty($_REQUEST['acao'])) {
	$_REQUEST['acao'] = 'busca';
}
if ($_REQUEST['acao'] == "del"){
	if ($prg_codigo)
	{
		$sql = pg_query("DELETE FROM programa_atendimento WHERE prg_codigo = $prg_codigo");
	}
	$_REQUEST['acao'] = "busca";
}
//------------------------------------------------------------------>
//-> Mostrando o resultado da busca
//------------------------------------------------------------------>

	if ($_REQUEST['acao']=='adds')
	{
	///	die('asdfasdfasdf');
			$sql = pg_query("INSERT INTO programa_atendimento (prg_nome) VALUES ('".$_POST['prg_nome']."')");
			msg($_REQUEST['id_login'],'add',$sql,'Cadastrado Com Sucesso');
	}
	if ($_REQUEST['acao']=='edits') {
			$sql = pg_query("UPDATE programa_atendimento SET prg_nome = '".$_POST['prg_nome']."' WHERE prg_codigo = $prg_codigo");
			msg($_REQUEST['id_login'],'edit',$sql,'Editado Com Sucesso');
	}
	
 if($_REQUEST['acao']=="busca") {
	 
	 echo "<table width='98%' align='center' cellspacing='0' cellpadding='0' border='0'>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Op&ccedil;&otilde;es</legend>
	     <table width='100%' align='center' cellspacing='3' cellpadding='0' border='0'>
		<tr>
			<td colspan='2'>
                        ".chmodbtn($id_login,"adicionar","programa_atendimento.php?acao=add")."
<!--			<a href='programa_atendimento.php?acao=add&id_login=$id_login'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg border='0'></a> -->
			</td>
		</tr>
	      <tr>";
echo "<form method=post action='".$_SERVER['PHP_SELF']."'>
		<input type='hidden' name='acao' value=busca>
		<input type='hidden' name=id_login value=$id_login>
		<td width='10'>Buscar:</td>
		<td width='200'>
			<input type='text' name=palavra_chave class='box' onBlur=\"javascript:this.value=this.value.toUpperCase();\">
		</td>
		<td>".ChmodBtn($id_login,"procurar","programa_atendimento.php")."</td>
	  </form>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br>";

//
//-> Listando

    if (chmodbtn($id_login,"listar_if","programa_atendimento.php"))
    {
            echo "<table width='98%' align='center' cellspacing='0' cellpadding='0' border='0'>
                   <tr>
                    <td>
                     <fieldset>
                      <legend>Programas de Atendimento</legend>
                       <table width='100%' align='center' cellspacing=2 cellpadding=4 border='0'>
                        <tr bgcolor=F9f9f9>
                          <td width='5%' style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Código</td>
                          <td width='75%' style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Descri&ccedil;&atilde;o</td>
                          <td width='20%' style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td>";
          
                  $sql="SELECT prg_codigo,prg_nome FROM programa_atendimento";
                  if ($palavra_chave){
                          $sql.= " WHERE ((prg_nome LIKE UPPER('%$palavra_chave%'))                          
                          			 ".(is_numeric($palavra_chave) ? "
									OR (prg_codigo = $palavra_chave)": "").") 
                          		   ORDER BY prg_nome";
                  } else
                          $sql.=" ORDER BY prg_nome LIMIT 15";
                  $sql = pg_query($sql);	
                
               while($row=pg_fetch_array($sql)) {
                 echo "<tr>
                         <td align='center' style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[prg_codigo]</td>
                         <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[prg_nome]</td>
                         <td width='66' style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>"
                          .chmodbtnjs($id_login,"editar","programa_atendimento.php","location.replace('programa_atendimento.php?id_login=$id_login&acao=edit&prg_codigo=$row[prg_codigo]')")."&nbsp;"
                          .chmodbtnjs($id_login,"apagar","programa_atendimento.php","location.replace('programa_atendimento.php?id_login=$id_login&acao=del&prg_codigo=$row[prg_codigo]')")."
                   </td>
          
                       </tr>";
               }
    }               
	echo "</tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table>"; 
}
if ($_REQUEST['acao'] == "add" || $_REQUEST['acao'] == "edit")
{
//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
	 reglog($id_login,"Entrando em Program. de Atend.");
//------------------------------------------------------------------>
//------------------------------------------------------------------>
//-> Formulario de Edicao de Conteudo
//------------------------------------------------------------------>


	 reglog($id_login,"Formulario de EDICAO Program de Atend");

  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Opções de Cadastro</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
	       <td width=79><a href=programa_atendimento.php?acao=&id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
	       <td>&nbsp;</td>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br>";
//
//-> Pegando as informcoes do banco pra mostrar no formulario
 $sql = pg_query("SELECT prg_nome FROM programa_atendimento WHERE prg_codigo = '".$_REQUEST['prg_codigo']."'");
$nome = pg_fetch_array($sql);
  echo "<form name=\"progAtend\" method=\"POST\" action='".$_SERVER['PHP_SELF']."'>";
if(!empty($_REQUEST['prg_codigo'])) {
	echo "<input type=hidden name=acao value='edits'>";
} else {
	echo "<input type=hidden name=acao value='adds'>";	
}

echo "<input type=hidden name=id_login value='".$_REQUEST['id_login']."'>
	<input type=hidden name=usu_codigo value='".$_REQUEST['usu_codigo']."'>
	<input type=hidden name=prg_codigo value=$prg_codigo>
	<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Cadastro de Programas de Atendimento</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
		<td width=110>Nome do Programa:</td>
		<td><input type=text  name=prg_nome class=box size=70 value='$nome[prg_nome]'></td>
	      </tr>
	      <tr>
	       <td>&nbsp;</td>
	       <td><input  type='hidden' name='envia' value='e'/>
		<img  src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg onclick=\"progAtend.submit();\" >
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br></form>";
}
?>
