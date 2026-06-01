<script>
function edita(cts_codigo, cts_quantidade, cts_periodo, pro_codigo)
{
	document.cotaSetor.cts_codigo.value = cts_codigo;
	document.cotaSetor.cts_quantidade.value = cts_quantidade;
	document.cotaSetor.cts_periodo.value = cts_periodo;
	document.cotaSetor.pro_codigo.value = pro_codigo;
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
if ($_POST['envia'])
{
	$_POST['pro_codigo'] != "Selecione" ? $pc = intval($pro_codigo) : $pc = 'NULL';
	if($_POST['cts_codigo']=="")
	{
		reglog($id_login,"Adicionando Registro em SETOR");
		 $sql = pg_query("INSERT INTO cota_setor ( 
				cts_quantidade, 
				cts_periodo, 
				set_codigo, 
				pro_codigo
				 ) VALUES ( 
				".floatval($cts_quantidade).", 
				'".trim(strtoupper(substr($cts_periodo,0,10)))."', 
				".intval($set_codigo).", 
				".$pc." )");
		reglog($id_login,"Adicionando Setor $set_nome ");
	}
	else if ($_POST['cts_codigo']!="")
	{
		 $sql = pg_query("UPDATE cota_setor SET 
			cts_quantidade = ".floatval($cts_quantidade).", 
			cts_periodo = '".trim(strtoupper(substr($cts_periodo,0,10)))."', 
			set_codigo = ".intval($set_codigo).", 
			pro_codigo = ".$pc."
			WHERE cts_codigo = ".intval($cts_codigo));
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
	    <legend>Opções de Cadastro</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
	       <td width=79><a href=setor.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
	       <td>&nbsp;</td>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br>";
//
//-> Pegando as informcoes do banco pra mostrar no formulario
   

 $sql =  "select * from setor where set_codigo='$set_codigo'";
 $setor=pg_fetch_array(pg_query($sql));
 

  echo "<form name=\"cotaSetor\" method=\"POST\" action=''>
	<input type=hidden name=id_login value=$id_login>
	<input type=hidden name=set_codigo value=$set_codigo>
	<input type=hidden name=cts_codigo value=$cts_codigo>
	<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Cadastro de Cotas/Setor</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
		<td width=110>Descricao do Setor:</td>
		<td><input type=text readonly name=set_nome class=box size=70 value='$setor[set_nome]'></td>
	      </tr>
	      <tr>
		<td width=110>Quantidade:</td>
		<td><input type=text name=cts_quantidade class=box size=70 value='$row[cts_quantidade]'></td>
	      </tr>
	      <tr>
		<td width=110>Per&iacute;odo:</td>
		<td>
			<select name='cts_periodo'>
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
		<td width=110>Per&iacute;odo:</td>
		<td>
			<select name='pro_codigo'>
				<option>Selecione</option>";
				$sql  = "select produto.pro_codigo, pro_nome
							from produto, produto_setor
							where produto.pro_codigo = produto_setor.pro_codigo
							and   set_codigo = $set_codigo";
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
		<img  src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg onclick=\"document.cotaSetor.cts_codigo.value='';cotaSetor.submit();\" >
		<input type=\"image\"  src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.jpg name=excluir  value='del'></td>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br></form>";
      if(isset($_POST['excluir'])=='del'){ 

	$sql = "delete from cota_setor where cts_codigo = {$_POST['cts_codigo']}";
         $exec_sql = pg_query($sql);
	
      }        
?>
<fieldset>
<legend>Cotas</legend>
<table>
<tr><td>PRODUTOS</td></tr>
<? 
	$sql = pg_query("SELECT * FROM cota_setor, produto WHERE cota_setor.set_codigo = $set_codigo AND cota_setor.pro_codigo = produto.pro_codigo");
	while ($dados = pg_fetch_array($sql))
	{
		$dados[pro_codigo]=="" ? $cod_prod = 'NULL' : "";
		echo "<tr><td>".$dados['pro_nome']."</td><td><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg onclick=\"edita($dados[cts_codigo],$dados[cts_quantidade],'$dados[cts_periodo]',$cod_prod);\"></td></tr>";
	}
?>
</table>
</fieldset>

