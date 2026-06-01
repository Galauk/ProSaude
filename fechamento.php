<script language="JavaScript" type="text/javascript" src="funcoes.js"></script>
<script>
function abre_movim(id_login, mov_nr_nota)
{
// O Parametro que esta sendo passado para este valor e o mov_codigo e nao foi alterado para continuar funcionando ok
// Marco Aurelio - 20/12/2006
 window.open('/relatorio/MovExibir.php?acao=form_edit&id_login='+id_login+'&mov_nr_nota='+mov_nr_nota,null,"height=400,width=750,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");

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
	 reglog($id_login,"Entrando em Fechamento de Estoque");
//------------------------------------------------------------------>





 if (empty($acao) OR ($acao == 'form_fechamento')) {

//
//-> Botoes


  echo "<fieldset>
	    <legend>Opções de Cadastro</legend>
	      <a href=materiais.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a>
	   </fieldset>
	   <br>";

//
//-> Este if esta apontando quando a acao for vazia ele vai mostrar o cadastro simples
//   no cadastro completo vc vai ter que passar a variavel acao para completo

  echo "<form method=post action=$PHP_SELF>
	<input type=hidden name=acao value=add>
	<input type=hidden name=id_login value=$id_login>
	<input type=hidden name=type value=simples>
	<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Dados do fechamento do Mês </legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>";
     echo "
	      <tr>
		<td width=110>Fechamento Definitivo:</td>
		<td>
		 <select name=tipo_fechamento class=box>
		  <option value=S>Sim</option>
		  <option selected value=N>Nao</option>
		 </select>
	      </tr>";
	   echo "
	      <tr>
		<td width=70>Centro Estocador:</td>
		<td>
		 <select name=centro_estocador class=box>";
	    //
	    //-> SQL da Unidade
	    $query = pg_query("select * from setor
                           where set_estoque = 'S' order by set_nome");
	      while($setor=pg_fetch_array($query)) {
	       echo "<option value='$setor[set_codigo]'>$setor[set_nome]</option>";
	      }
	   echo "</select>
	        </td>
	      </tr>";
     $sqldata_hora = pg_query("select to_char(current_date, 'dd/mm/yyyy') as data, extract(hour from current_time) || ':' || extract(minute from current_time) as hora");
     $rowdata_hora = pg_fetch_array($sqldata_hora);
     $data = $rowdata_hora['data'];
     $hora = $rowdata_hora['hora'];
          echo "
	     <tr>
     		<td width=40>Data do Fechamento:</td>
    		<td><input type=text name=data_fechamento class=box size=20  onKeypress=\"return Ajusta_Data(this, event);\"></td>
         </tr>
	      <tr>
	       <td>&nbsp;</td>
	       <td><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/add_on.gif></td>
	      </tr>

	  </td>
	 </tr>
        </table><br></form>";
 }//fechamento do if - acao = simples

//------------------------------------------------------------------>
//-> SQL's
//------------------------------------------------------------------>
//
//-> ADD <---------------------------------------------------------->

if($acao=="add") {
	$ultfech = pg_fetch_array(pg_query("select set_ultimo_fechamento from setor
                             where set_codigo = $centro_estocador"));
	if ($ultfech['set_ultimo_fechamento'] == ''){ 
		$dataultfech = '2006-10-27';       
	}
 	else{
 		$dataultfech = $ultfech['set_ultimo_fechamento']; 
 	}

	if ($tipo_fechamento == 'S') {
 		reglog($id_login,"Realizando Fechamento Mensal Definitivo");
 		$sql = pg_query("UPDATE setor SET " .
 								($data_fechamento ? "set_ultimo_fechamento = '$data_fechamento' " : "set_ultimo_fechamento = null ").
            			 "WHERE cest_codigo='$centro_estocador'");

 	}
 	$sqlfechamento = "SELECT DISTINCT pro_codigo,
							 pro_nome from v_movimentacao
					   WHERE mov_data > '$dataultfech' 
					     AND mov_data <= '$data_fechamento'
						 AND setor = $centro_estocador";
 	vSQL($sqlfechamento, '1');

 	while ($row=pg_fetch_array($sqlfechamento)) {
 		$selecionaestoque = "SELECT calcula_estoque('$row[pro_codigo]', $centro_estocador, '$data_fechamento')";
 		$estoque = pg_fetch_array(pg_query($selecionaestoque));
 		
 		$precomedio = pg_fetch_array(pg_query("SELECT calcula_preco('$row[pro_codigo]', $centro_estocador, '$data_fechamento')"));
 		//         $rowcount = pg_fetch_array(pg_query("select count(*) as conta from saldo
 		$rowcount = pg_fetch_array(pg_query("SELECT count(*) as conta from saldo
                                              WHERE pro_codigo = '$row[pro_codigo]'
                                                AND sal_data = '$data_fechamento'
                                                AND set_codigo = $centro_estocador"));

 		if ($rowcount['conta'] == 0) {
 			$sqlinclui = pg_query("INSERT INTO saldo
                                   		(sal_data, pro_codigo, set_codigo, sal_qtde, sal_custo)
                                   VALUES
                                   		('$data_fechamento', '$row[pro_codigo]', $centro_estocador, $estoque, $precomedio)");
 		}
 		else {
 			$sqlaltera = pg_query("UPDATE saldo
                                      SET sal_qtde = $estoque, sal_custo = $precomedio
                                    WHERE pro_codigo = '$row[pro_codigo]'
                                  	  AND sal_data = '$data_fechamento'
                                  	  AND set_codigo = $centro_estocador");
 		}
 	}
 }
 ?>
