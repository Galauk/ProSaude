<link href='css/estiloForm.css' rel='stylesheet' type='text/css' />
<link href='css/estiloCommon.css' rel='stylesheet' type='text/css' />
<script language="JavaScript" type="text/javascript" src="funcoes.js"></script>
<script>
function edita(prgp_codigo, pro_codigo)
{
	document.cotaSetor.pro_codigo.value = pro_codigo;
	document.cotaSetor.prgp_codigo.value = prgp_codigo;
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
	require_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
	require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	
	cabecario();
  
	$form = new classForm();
	$common = new commonClass();
//------------------------------------------------------------------>


//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
	 reglog($id_login,"Entrando em PACIENTE");
//------------------------------------------------------------------>

 if(empty($acao)) {
	$acao = 'busca';
}
 if($acao=="busca") {
	 
	 echo "<table width='98%' align='center' cellspacing='0' cellpadding='0' border='0'>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Opções</legend>
	     <table width='100%' align='center' cellspacing='3' cellpadding='0' border='0'>
		<tr>
			<td colspan='2'>
			<a href='farmacia.php?id_login=$id_login'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border='0'></a>
			</td>
		</tr>
	      <tr>";
                if (chmodbtn($id_login,"procurar_if","programa_produto.php"))
                {
                    echo "<form method=post action=$PHP_SELF>";
                }
                echo "
                        <input type='hidden' name='acao' value=busca>
                        <input type='hidden' name=id_login value=$id_login>
                        <td width='5%'>Buscar:</td>
                        <td width='15%'><input type='text' name=palavra_chave class='box' onBlur=\"javascript:this.value=this.value.toUpperCase();\"></td>
                        <td width='65%'>".chmodbtn($id_login,"procurar","programa_produto.php")."</td>                
	  </form>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br>";

//
//-> Listando

    if (chmodbtn($id_login,"listar_if","programa_produto.php"))
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
                  if ($palavra_chave)
                          $sql.= " WHERE prg_nome ILIKE '%$palavra_chave%' ORDER BY prg_nome";
                  else
                          $sql.=" ORDER BY prg_nome LIMIT 15";
                  $sql = pg_query($sql);	
                
               while($row=pg_fetch_array($sql)) {
                 echo "<tr>
                         <td align='center' style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[prg_codigo]</td>
                         <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[prg_nome]</td>
                         <td width='66' style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>
                         ".chmodbtnjs($id_login,"adicionar","programa_produto.php","location.replace('programa_produto.php?id_login=$id_login&acao=add&prg_codigo=$row[prg_codigo]')")."
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
if ($acao == "add")
{
	if ($_POST['envia'])
	{
		if($_POST['prgp_codigo']=="")
		{
			reglog($id_login,"Adicionando Registro em SETOR");
			 $sql = "INSERT INTO programa_produto (
					prg_codigo,
					pro_codigo
					 ) VALUES (
					".$prg_codigo.",
					".$pro_codigo.")";
			$sql = pg_query($sql);
			reglog($id_login,"Adicionando Setor $set_nome ");
		}
		else if ($_POST['prgp_codigo']!="")
		{
			$sql = pg_query("UPDATE programa_produto SET pro_codigo = ".$pro_codigo." WHERE prgp_codigo = ".$prgp_codigo);
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
	       <td width=79><a href=programa_produto.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
	       <td>&nbsp;</td>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br>";
//
//-> Pegando as informcoes do banco pra mostrar no formulario
 

  echo "<form name=\"cotaSetor\" method=\"POST\" action=''>
	<input type=hidden name=id_login value=$id_login>
	<input type=hidden name=usu_codigo value=$usu_codigo>
	<input type=hidden name=prgp_codigo value=$prgp_codigo>
	<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Produtos por Programa</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
		<td width=210>".$form->inputLabel("Produto")."</td>
		<td>
			<select name='pro_codigo' class='inputForm' style='width:350px;'>
				<option>Selecione</option>";
				$sql  = "select * from produto where pro_situacao = 'A' order by pro_nome";
				$exec_sql = pg_query($sql);
				while ($dados = pg_fetch_array($exec_sql))
				{
					echo "<option value=$dados[pro_codigo]>$dados[pro_nome]</option>";
				}
echo "		</select>
		</td>
	      </tr>
	      <tr>
	       <td>&nbsp;</td>
	       <td><input  type='hidden' name='envia' value='e'/>
	       ".//$common->commonButton("ADICIONAR", NULL,"src=".$_SESSION[linkroot].$_SESSION[comum]."imgsBotoes/adicionar_on.jpg","onclick=\"document.cotaSetor.prgp_codigo.value='';alert('Cadastrado Com Sucesso');cotaSetor.submit();");".
			"<img  src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg style='cursor:pointer;' onclick=\"document.cotaSetor.prgp_codigo.value='';alert('Cadastrado Com Sucesso');cotaSetor.submit();\" >
		</td>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br></form>";
      if(isset($_GET['del'])!=""){
      	
	$sql = "delete from programa_produto where prgp_codigo = {$_GET['del']}";
         if (!$exec_sql = pg_query($sql))
		echo "Esse produto esta sendo usado por outro programa";
      }
?>
<fieldset>
<legend>Produtos</legend>
<table class="lista">
<tr><td>PRODUTOS</td></tr>
<?
	$sql = pg_query("SELECT * FROM programa_produto, produto WHERE programa_produto.prg_codigo = $prg_codigo AND produto.pro_codigo = programa_produto.pro_codigo");
	while ($dados = pg_fetch_array($sql))
	{
		echo "<tr><td>".$dados['pro_nome']."</td><td><input type=\"image\"  src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.jpg name=excluir  value='del' onclick=\"location.replace('programa_produto.php?id_login=$id_login&del=$dados[prgp_codigo]&acao=add&prg_codigo=$prg_codigo')\"></td></tr>";
	}
?>
</table>
</fieldset>
<?}?>