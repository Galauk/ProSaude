<?php

session_start();
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
verauth($id_login);
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
cabecario();
//------------------------------------------------------------------>


?>
<script language="JavaScript" type="text/javascript" src="<?= $_SESSION[linkroot].$_SESSION[comum];?>library/js/funcoes.js"></script>
<script>
var gdtInicial
var gdtFinal
var gmedico
var gespecial
var gunidade
var gTipAgenda
var gMostAgente
var gHoje
var maxDay = new Array(31,28,31,30,31,30,31,31,30,31,30,31);


function CheckDate(d,t) {
   date_array = new Array(3);
   date_array[0]=(String(d).substr(6,2))    // dia
   date_array[1]=(String(d).substr(4,2))    // mes
   date_array[2]=(String(d).substr(0,4))    // ano

   if (date_array[0] > maxDay[date_array[1]-1]) {
       alert ("Dia invalido da data " + t)
       return 1;
   }
   if (date_array[1] > 12) {
       alert ("Mes invalido da data " + t)
       return 1;
   }
   if (date_array[2] < 1990) {
       alert ("Ano invalido da data " + t)
       return 1;
   }
}


function CheckCall() {

   gdtInicial =document.frm_consulta.dt_inicial.value;
   gdtFinal   =document.frm_consulta.dt_final.value;

   if (gdtInicial == '') {
       alert ("Informe Data Inicio");
       document.frm_consulta.dt_inicial.focus();
       return false;
   }
    if (gdtFinal == '') {
       alert ("Informe Data Final");
       document.frm_consulta.dt_final.focus();
       return false;
   }
   var d1=gdtInicial;
   var d2=gdtFinal;
   for (var i = 0; i < d1.length; i++) {
        if (d1.charAt(i) == "-") {
           var dat1=parseInt(d1.split("-")[2].toString()+d1.split("-")[1].toString()+d1.split("-")[0].toString())
        }
        else
        if (d1.charAt(i) == "/") {
           var dat1=parseInt(d1.split("/")[2].toString()+d1.split("/")[1].toString()+d1.split("/")[0].toString())
        }
   }
	for (var i = 0; i < d2.length; i++) {
        if (d2.charAt(i) == "-") {
           var dat2=parseInt(d2.split("-")[2].toString()+d2.split("-")[1].toString()+d2.split("-")[0].toString())
        }
        else
        if (d2.charAt(i) == "/") {
           var dat2=parseInt(d2.split("/")[2].toString()+d2.split("/")[1].toString()+d2.split("/")[0].toString())
        }
   }
   if (CheckDate(dat1,"INICIAL")==1) {
       document.frm_consulta.dt_inicial.focus()
       return false;
   }
	if (CheckDate(dat2,"FINAL")==1) {
       document.frm_consulta.dt_final.focus()
       return false;
   }

  return true
}
</script>
<?php


reglog($id_login,"Acessando Materiais");
//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
//------------------------------------------------------------------>

$data = date("d/m/Y");

 if(empty($acao)) {

//
//-> Botoes

echo "<fieldset>
	<legend>Buscar Produtos em estoque</legend>
	  <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
         <tr>
         </tr >
	      <tr>
	       <form method=post action=$PHP_SELF>
		<input type=hidden name=acao value=busca>
		<input type=hidden name=id_login value=$id_login>
	       <td width=132 align=right>Buscar:</td>
	       <td colspan=2 width=100><input type=text name=palavra_chave class=box onBlur=\"javascript:this.value=this.value.toUpperCase();\"></td>
	       <td><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/procurar_on.jpg></td></form>
	      </tr>
	     </table>
	   </fieldset>";

//
//-> Listando
/* Marco Aurelio - 21/10/200
   Retirada a opcao de listar diretamente os dados de produtos, conforme solicitacao efetuada pela Keila - Apucarana
  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Listando os Produtos Cadastrados em Ordem Alfabetica</legend>
	     <table width=100% align=center cellspacing=2 cellpadding=4 border=0>
	      <tr bgcolor=F9f9f9>
		<td width=40 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Codigo</td>
		<td width=500 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Nome</td>
		<td colspan=3 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td>";

   $sql=pg_query("select pro_codigo, pro_nome from produto order by pro_nome ");
     while($row=pg_fetch_array($sql)) {
       echo "<tr>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[pro_codigo]</td>
	       <td width=300 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[pro_nome]</td>
	       <td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'editar','materiais.php?acao=form_edit&pro_codigo='.$row[pro_codigo])."</td>
	       <td width=66 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'apagar','materiais.php?acao=del&pro_codigo='.$row[pro_codigo])."</td>
	       <td width=66 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'><img  src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/print.gif OnClick='Imprime(\"$row[0]\")' border=0></a></td>
	     </tr>";
     }
	echo "</tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table>";
        */
}


//------------------------------------------------------------------>
//-> Mostrando o resultado da busca
//------------------------------------------------------------------>

 if($acao=="busca") {
 reglog($id_login,"Buscando em Materiais $palavra_chave");
//
//-> Verificando Busca

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
	<legend>Buscar Produtos em estoque</legend>
	  <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
         <tr>
         </tr >
	      <tr>
	       <form method=post action=$PHP_SELF>
		<input type=hidden name=acao value=busca>
		<input type=hidden name=id_login value=$id_login>
	       <td width=132 align=right>Buscar:</td>
	       <td colspan=2 width=100><input type=text name=palavra_chave class=box onBlur=\"javascript:this.value=this.value.toUpperCase();\"></td>
	       <td><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/procurar_on.jpg></td></form>
	      </tr>
	     </table>
	   </fieldset>";

$sql=pg_query("select pro_codigo, pro_nome from produto where (pro_nome like upper('$palavra_chave%')) order by pro_nome");
$num=pg_num_rows($sql);
  if($num=="0") { $resp = "Nenhum Registro encontrado com \"$palavra_chave\""; }
  if($num=="1") { $resp = "Encontrado <b>$num</b> Registro com \"$palavra_chave\""; }
  if($num>"1") { $resp = "Encontrados <b>$num</b> Registros com \"$palavra_chave\""; }

  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>$resp</legend>
	     <table width=100% align=center cellspacing=2 cellpadding=4 border=0>
	      <tr bgcolor=F9f9f9>
		<td width=40 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Codigo</td>
		<td width=270 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Nome</td>
		<td style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Quantidade em Estoque</td>";
     while($row=pg_fetch_array($sql)) {
$estoque = pg_fetch_array(pg_query("select sum(sal_qtde) from saldo where pro_codigo = '$row[pro_codigo]' and set_codigo = '99405' group by pro_codigo"));

if($estoque[0]<=0) { $est = "<font color=red><b>$estoque[0]</b></font>"; } else { $est = "<font color=blue><b>$estoque[0]</b></font>"; }
       echo "<tr>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[pro_codigo]</td>
	       <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[pro_nome]</td>
	       <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;' width=160>$est</td>
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
//-> Formulario consulta almoxarifado
//------------------------------------------------------------------>
if($acao=="form_consulta_almoxarifado")
{
	echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Opções de Cadastro</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
	       <td width=79><a href=materiais.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
	       <td>&nbsp;</td>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br>";
$data = date("d/m/Y");
$dt_inicial = $data;
$dt_final = $data;
$select = "select pro_nome from produto where pro_codigo = $_GET[pro_codigo]";
$exec_select = pg_query($select);
$linha = pg_fetch_array($exec_select);
echo "<table><tr>";
	echo "<th width=100 align=left>";
		echo "PRODUTO";
	echo "</td>";
	echo "<th align=left>";
		echo "<b>".$linha[pro_nome]."</b>";
	echo "</td>";
echo "</tr></table>";
echo " <form name='frm_consulta' onsubmit='return CheckCall()' method='post' action=''>";
echo "<input type=hidden name=acao value=consultar>";
echo "<input type=hidden name=pro_codigo value=$_GET[pro_codigo]>";
echo "<input type=hidden name=id_login value=$id_login>";
echo "  <fieldset>";
echo "   <legend>Consulta</legend>";
echo "    <table whidht=90% border=0 cellspacing=2 cellpadding=1>";
echo "     <tr>\n";
echo "      <td valign='bottom' style='width:15%'>Data Inicial</td>";
echo "      <td><input class='box' type='text'   name='dt_inicial' size='12' value='$dt_inicial'/ maxlength='10' onKeypress=\"return Ajusta_Data(this, event);\">";
echo "       </td>";
echo "      </tr>";
echo "      <tr>";
echo "       <td>";
echo "	Data Final";
echo "       </td>";
echo "       <td>";
echo "          <input class='box' type='text' name='dt_final' size='12' value='$dt_final' maxlength='10' onKeypress=\"return Ajusta_Data(this, event);\"/></td>";
echo "     </tr>";
echo "     <tr>";
echo "       <td>";
echo "	Centro estocador";
echo "       </td>";
echo "      <td>";
			$select = "select a.set_codigo, a.set_nome from setor a, produto_setor b where a.set_codigo = b.set_codigo and b.pro_codigo = $_GET[pro_codigo]";
			//echo $select;
			$exec_select = pg_query($select);
			echo "<select name='centro_estocador' class='box'>";
			while($linha = pg_fetch_array($exec_select))
			{
				echo "<option value='$linha[set_codigo]'>$linha[set_nome]</option>";
			}

			echo "</select>";
echo "      </td>";
echo "</tr>";
echo "<tr>
	       <td>&nbsp;</td>
	       <td><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg></td>
	      </tr>";
echo "</table></fieldset><br>";
}

//------------------------------------------------------------------>
//-> Formulario consultar
//------------------------------------------------------------------>
if($acao=="consultar")
{
	echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Opções de Cadastro</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
	       <td width=79><a href=materiais.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
	       <td>&nbsp;</td>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br>";
		$select = "select pro_nome from produto where pro_codigo = $_POST[pro_codigo]";
$exec_select = pg_query($select);
$linha = pg_fetch_array($exec_select);
echo "<table><tr>";
	echo "<th width=100 align=left>";
		echo "PRODUTO";
	echo "</td>";
	echo "<th align=left>";
		echo "<b>".$linha[pro_nome]."</b>";
	echo "</td>";
echo "</tr></table>";
echo " <form name='frm_consulta' onsubmit='return CheckCall()' method='post' action='$PHP_SELF'>\n";
echo "<input type=hidden name=acao value=consultar>";
echo "<input type=hidden name=pro_codigo value=$_POST[pro_codigo]>";
echo "<input type=hidden name=id_login value=$id_login>";
//print_r($_POST);
//print_r($_GET);
echo "  <fieldset>";
echo "   <legend>Consulta</legend> \n";
echo "    <table width=90% border=0 cellspacing=2 cellpadding=1>\n";
echo "     <tr>\n";
echo "      <td valign='bottom' style='width:15%'>Data Inicial</td>\n";
echo "      <td><input class='box' type='text'   name='dt_inicial' size='12' value='$_POST[dt_inicial]' maxlength='10' onKeypress=\"return Ajusta_Data(this, event);\">\n";
echo "       </td>";
echo "      </tr>";
echo "      <tr>";
echo "       <td>";
echo "	Data Final";
echo "       </td>";
echo "       <td>";
echo "          <input class='box' type='text' name='dt_final' size='12' value='$_POST[dt_final]' maxlength='10' onKeypress=\"return Ajusta_Data(this, event);\"/></td>\n";
echo "     </tr>\n";
echo "     <tr>\n";
echo "       <td>";
echo "	Centro estocador";
echo "       </td>";
echo "      <td>";
			$select = "select a.set_codigo, a.set_nome from setor a, produto_setor b where a.set_codigo = b.set_codigo and b.pro_codigo = $_POST[pro_codigo]";
			//echo $select;
			$exec_select = pg_query($select);
			echo "<select name='centro_estocador' class='box'>";
			while($linha = pg_fetch_array($exec_select))
			{
				if($_POST[centro_estocador] == $linha[set_codigo])
				{
					$l = "selected";
				} else {
					$l = "";
				}
				echo "<option value='$linha[set_codigo]' $l>$linha[set_nome]</option>";
			}

			echo "</select>";
echo "      </td>\n";
echo "</tr>";
echo "<tr>
	       <td>&nbsp;</td>
	       <td><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg></td>
	      </tr>";
echo "      <td>&nbsp;&nbsp;&nbsp;</td>\n";
echo "     </tr>";
echo "</table></fieldset><br>";

$dt_ini = $_POST[dt_inicial];
$dt = explode("/",$dt_ini);
/*
echo $_POST[dt_inicial];
echo "<pre>";
	print_r($dt);
echo "</pre><br>";
echo "<br>->";
*/
if($dt[0] > 1)
{
	$num = cal_days_in_month(CAL_GREGORIAN, $dt[1], $dt[2]);
	if($dt[0] <= 10)
	{
		$dt[0] = $dt[0] - 1;
		$dt[0] = "0".$dt[0];
	} else {
		$dt[0] = $dt[0] - 1;
	}
	$mes = $dt[1];
	$ano = $dt[2];
} else {
	if($dt[1] == 1)
	{
		$dt[1] = 13;
		$dt[2] = $dt[2]-1;
	}
	$num = cal_days_in_month(CAL_GREGORIAN, ($dt[1]-1), $dt[2]);
	if($dt[1] <= 10)
	{
		$mes = $dt[1]-1;
		$mes = "0".$mes;
	} else {
		$mes = $dt[1]-1;
	}
	$dt[0] = $num;
	$ano = $dt[2];
}
$data_inicial = $dt[0]."/".$mes."/".$ano;
//echo $data_inicial;

$dt_ini = $data_inicial;


	$select_saldo_anterior = "select calcula_estoque($_POST[pro_codigo], $_POST[centro_estocador], '$dt_ini')";
	$saldo = pg_query($select_saldo_anterior);
	$saldo_anterior = pg_fetch_array($saldo);
	/*echo $select_saldo_anterior;
	echo pg_last_error($db);*/
	$select_preco = "select verifica_preco($_POST[pro_codigo], $_POST[centro_estocador], '$dt_ini')";
	$pre = pg_query($select_preco);
	$preco = pg_fetch_array($pre);
	/*echo "<br>";
	echo $select_preco;
	echo "<br>".pg_last_error($db);*/
echo "  <fieldset>";
echo "   <legend>Lista</legend> \n";
	echo "<table width=90% border=1 cellspacing=1 cellpadding=1>";
		echo "<tr bgcolor=F9f9f9>";
			echo "<th style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;text-align:left' width=100px>";
				echo "Saldo Anterior:";
			echo "</th>";
			echo "<td style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;' width=150px>";
				echo "&nbsp;".$saldo_anterior[0];
			echo "</td>";
			echo "<th style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;text-align:left;' width=100px>";
				echo "Pre&ccedil;o M&eacute;dio:";
			echo "</th>";
			echo "<td style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;' width=150px>";
				echo "&nbsp;".$preco[0];
			echo "</td>";
			echo "<th style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;text-align:left;' width=100px>";
				echo "Vlr Financeiro:";
			echo "</th>";
			echo "<td style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;' width=150>";
				echo "&nbsp;".$saldo_anterior[0]*$preco[0];
			echo "</td>";
		echo "</tr>";
	echo "</table>";
	echo "<table width=90% border=1 cellspacing=1 cellpadding=1>";

		$select_total_entrada = "select pro_codigo, pro_nome, to_char(mov_data, 'dd/mm/yyyy') as mov_data, sum(ite_quantidade) as qtde, codsetor,
												case when ite_vlrunit is not null then sum(ite_quantidade * ite_vlrunit) else sum ( coalesce(verifica_preco($_POST[pro_codigo], $_POST[centro_estocador], mov_data), 0) * ite_quantidade) end as vlr
												from v_movimentacao
												where sinal = '+'
												and mov_data >= '$dt_ini' and mov_data <= '$_POST[dt_final]'
												and pro_codigo = $_POST[pro_codigo]
												and codsetor = $_POST[centro_estocador]
												group by mov_data, pro_codigo, pro_nome, codsetor, ite_vlrunit";
		$exec_select = pg_query($select_total_entrada);
		/*echo $select_total_entrada;
		echo pg_last_error($db);*/
		/*echo "<pre>";
			print_r($linha);
		echo "</pre><br>";*/
		echo "<tr>";
			echo "<td valign=top style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;' width=345px>";
				echo "<table>";
					echo "<tr bgcolor=F9f9f9>";
							echo "<th style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;text-align:left'>";
								echo "Data";
							echo "</th>";
							echo "<th style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;text-align:left'>";
								echo "Entradas";
							echo "</th>";
							echo "<th style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;text-align:left'>";
								echo "Pre&ccedil;o";
							echo "</th>";
						echo "</tr>";
					while($linha = pg_fetch_array($exec_select))
					{
						echo "<tr>";
							echo "<td style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;text-align:left'>";
								echo "&nbsp;".$linha[mov_data];
							echo "</td>";
							echo "<td style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;text-align:left'>";
								echo "&nbsp;".$linha[qtde];
							echo "</td><td style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;text-align:left'>";
								echo $linha[vlr];
							echo "</td>";
						echo "</tr>";
					}
				echo "</table>";
			echo "</td>";

		$select_total_saida = "select pro_codigo, pro_nome, to_char(mov_data, 'dd/mm/yyyy') as mov_data, sum(ite_quantidade) as qtde, codsetor,
											sum( coalesce(verifica_preco(pro_codigo, codsetor, mov_data), 0) * ite_quantidade) as vlr
											from v_movimentacao
											where sinal = '-'
											and mov_data >= '$dt_ini' and mov_data <= '$_POST[dt_final]'
											and pro_codigo = $_POST[pro_codigo]
											and codsetor = $_POST[centro_estocador]
											group by mov_data, pro_codigo, pro_nome, codsetor, ite_vlrunit";
		$exec_total = pg_query($select_total_saida);
		/*echo "<pre>";
			print_r($linha_total);
		echo "</pre>";*/
			echo "<td valign=top style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>";
				echo "<table>";
				echo "<tr bgcolor=F9f9f9>";
							echo "<th style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;text-align:left'>";
								echo "Data";
							echo "</th>";
							echo "<th style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;text-align:left'>";
								echo "Sa&iacute;da";
							echo "</th>";
							echo "<th style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;text-align:left'>";
								echo "Pre&ccedil;o";
							echo "</th>";
						echo "</tr>";
					while($linha_total = pg_fetch_array($exec_total))
					{
						echo "<tr>";
							echo "<td style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;text-align:left'>";
								echo "&nbsp;".$linha_total[mov_data];
							echo "</td>";
							echo "<td style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;text-align:left'>";
								echo "&nbsp;".$linha_total[qtde];
							echo "</td><td style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;text-align:left'>";
								echo $linha_total[vlr];
							echo "</td>";
						echo "</tr>";
					}
				echo "</table>";
			echo "</td>";
		echo "</tr>";
	echo "</table>";
	echo "<table width=90% border=1 cellspacing=2 cellpadding=1>";

		$select_saldo_atual = "select calcula_estoque($_POST[pro_codigo], $_POST[centro_estocador], '$_POST[dt_final]')";
		$saldo = pg_query($select_saldo_atual);
		$saldo_atual = pg_fetch_array($saldo);
		/*echo $select_saldo_anterior;
		echo pg_last_error($db);*/
		$select_preco = "select verifica_preco($_POST[pro_codigo], $_POST[centro_estocador], '$_POST[dt_final]')";
		$pre = pg_query($select_preco);
		$preco = pg_fetch_array($pre);
		/*echo "<br>";
		echo $select_preco;
		echo "<br>".pg_last_error($db);*/

		echo "<tr bgcolor=F9f9f9>";
			echo "<th style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;text-align:left' width=100px>";
				echo "Saldo Atual:";
			echo "</th>";
			echo "<td style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;' width=150px>";
				echo "&nbsp;".$saldo_atual[0];
			echo "</td>";
			echo "<th style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;text-align:left;' width=100px>";
				echo "Pre&ccedil;o M&eacute;dio:";
			echo "</th>";
			echo "<td style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;' width=150px>";
				echo "&nbsp;".$preco[0];
			echo "</td>";
			echo "<th style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;text-align:left;' width=100px>";
				echo "Vlr Financeiro:";
			echo "</th>";
			echo "<td style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;' width=150>";
				echo "&nbsp;".$saldo_atual[0]*$preco[0];
			echo "</td>";
		echo "</tr>";

	echo "</table>";
	echo "</fieldset>";
}

//------------------------------------------------------------------>
//-> Formulario de Adicao de Conteudo
//------------------------------------------------------------------>

 if($acao=="form_add") {
 reglog($id_login,"Formulario de Adicao de Materiais");
//
//-> Abaixo sao os botoes de voltar / cadastro simples / cadastro completo

  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Opções de Cadastro</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
	       <td width=79><a href=materiais.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
	       <td>&nbsp;</td>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br>";
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
	    <legend>Cadastro de Produto</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
		<td width=70>Nome:</td>
		<td><input type=text name=pro_nome class=box size=60></td>
	      </tr>
	      <tr>
		<td width=70>Grupo:</td>
		<td>
		 <select name=gru_codigo class=box>";
	    //
	    //-> SQL do Grupo de Produto
	    $query = pg_query("select gru_codigo, gru_nome from grupo order by gru_nome");
	      while($grupo=pg_fetch_array($query)) {
	       echo "<option value='$grupo[gru_codigo]'>$grupo[gru_nome]</option>";
	      }
	   echo "</select>
	        </td>
	      </tr>
	      <tr>
		<td width=110>Tipo do Produto:</td>
		<td>
		 <select name=pro_tipo class=box>
		  <option value='D'>Diversos</option>
		  <option value='M'>Medicamentos</option>
		  <option value='T'>Materiais Hospitalares</option>
          <option value='O'>Odontol&oacute;gico</option>
		 </select>
	      </tr>
	      <tr>
		<td width=70>Unidade de Medida:</td>
		<td>
		 <select name=umed_codigo class=box>";
	    //
	    //-> SQL da Unidade de Medida
	    $query = pg_query("select umed_codigo, umed_nome from unidmedida order by umed_nome");
	      while($umed=pg_fetch_array($query)) {
	       echo "<option value='$umed[umed_codigo]'>$umed[umed_nome]</option>";
	      }
	   echo "</select>
	        </td>
	      </tr>
	      <tr>
		<td width=70>Portaria Psicotropico:</td>
		<td>
		 <select name=psico_codigo class=box>";
	    //
	    //-> SQL Psicotropico
	    $query = pg_query("select psico_codigo, psico_nome from psicotropicos order by psico_nome");
	       echo "<option value=''>---</option>";
	      while($psico=pg_fetch_array($query)) {
	       echo "<option value='$psico[psico_codigo]'>$psico[psico_nome]</option>";
	      }
	   echo "</select>
	        </td>
	      </tr>
	      <tr>
		<td width=70>Codigo Barras: </td>
		<td><input type=text name=pro_barcode class=box size=15></td>
	      </tr>
	      <tr>
		<td width=70>Custo de Referencia: </td>
		<td><input type=text name=pro_custo class=box size=20></td>
	      </tr>
	      <tr>
		<td width=70>Embalagem:</td>
		<td><input type=text name=pro_embalagem class=box size=60></td>
	      </tr>
	      <tr>
		<td width=70>Descricao Tecnica:</td>
		<td><input type=textarea name=pro_descricao_tecnica class=box size=40></td>
	      </tr>
	      <tr>
		<td width=110>Mov. Entrada?:</td>
		<td>
		 <select name=pro_entrada class=box>
		  <option value=S>Sim</option>
		  <option value=N>Nao</option>
		 </select>
	      </tr>
	      <tr>
		<td width=110>Mov. Saida?:</td>
		<td>
		 <select name=pro_saida class=box>
		  <option value=S>Sim</option>
		  <option value=N>Nao</option>
		 </select>
	      </tr>
	      <tr>
		<td width=110>Mov. Dispensacao?:</td>
		<td>
		 <select name=pro_dispensacao class=box>
		  <option value=S>Sim</option>
		  <option value=N>Nao</option>
		 </select>
	      </tr>
	      <tr>
		<td width=110>Mov. Transferencia?:</td>
		<td>
		 <select name=pro_transferencia class=box>
		  <option value=S>Sim</option>
		  <option value=N>Nao</option>
		 </select>
	      </tr>
	      <tr>
		<td width=70>Estoque Minimo</td>
		<td><input type=textarea name=pro_minimo class=box size=15></td>
	      </tr>
	      <tr>
		<td width=70>Estoque Maximo:</td>
		<td><input type=textarea name=pro_maximo class=box size=15></td>
	      </tr>
	      <tr>
		<td width=70>Tempo de Reposicao:</td>
		<td><input type=textarea name=pro_tempo_reposicao class=box size=15></td>
	      </tr>
	      <tr>
		<td width=70>Tempo de Seguranca:</td>
		<td><input type=textarea name=pro_tempo_reposicao class=box size=15></td>
	      </tr>
	      <tr>
		<td width=70>Observacao:</td>
		<td><input type=textarea name=pro_observacao class=box size=40></td>
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
//-> Formulario de InclusÃ£o do produto_setor
//------------------------------------------------------------------>

if($acao == "form_incluir")
{
	reglog($id_login,"Formulario de Edicao de Materiais");
//
//-> Formulario de edicao do cadastro SIMPLES
	$select = "select pro_nome from produto where pro_codigo = $_GET[pro_codigo]";
	$exec_select = pg_query($select);
	$linha = pg_fetch_array($exec_select);
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
		echo "<table><tr>";
				echo "<th width=100 align=left>";
					echo "PRODUTO";
				echo "</td>";
				echo "<th align=left>";
					echo "<b>".$linha[pro_nome]."</b>";
				echo "</td>";
			echo "</tr></table>";

	echo "<form method=post action=inserirProdutoSetor.php>";
	echo "<input type=hidden name=id_login value=$id_login>";
	echo "<input type=hidden name=pro_codigo value=$_GET[pro_codigo]>";
	echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>";
	echo "<tr>";
	echo "<td>";
	echo "<fieldset>";
	echo "<legend>Cadastro de Produto</legend>";
	echo "<table width=100% align=center cellspacing=2 cellpadding=4 border=0>";
		echo "<tr>\n";
			echo "<td valign='middle'>Setor</td>\n";
			echo "<td><select name='set_codigo' class=box>\n";
				$UniQuery=pg_query("SELECT set_codigo, set_nome FROM Setor ORDER BY set_nome");
				while($SetArray=pg_fetch_array($UniQuery)) {
					echo ($set_codigo==$SetArray[set_codigo])?"<option value='$SetArray[set_codigo]' selected> $SetArray[set_nome]</option>":"<option value='$SetArray[set_codigo]' > $SetArray[set_nome]</option>\n";
				}
				echo "</select>\n";
			echo "</td>\n";
		echo "</tr>\n";
		echo "<tr>";
			echo "<td width=115>Estoque Minimo</td>";
			echo "<td><input type=textarea name=pro_minimo class=box size=15 value='$row[pro_minimo]'></td>";
		echo "</tr>";
		echo "<tr>";
			echo "<td width=115>Estoque Maximo:</td>";
			echo "<td><input type=textarea name=pro_maximo class=box size=15 value='$row[pro_maximo]'></td>";
		echo "</tr>";
		echo "<tr>";
			echo "<td width=115>Tempo de Reposicao:</td>";
			echo "<td><input type=textarea name=pro_tempo_reposicao class=box size=15 value='$row[pro_tempo_reposicao]'></td>";
		echo "</tr>";
		echo "<tr>";
			echo "<td width=115>Tempo de Seguranca:</td>";
			echo "<td><input type=textarea name=pro_seguranca class=box size=15 value='$row[pro_seguranca]'></td>";
		echo "</tr>";
		echo "<tr>";
			echo "<td>";
				echo "<input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg>";
			echo "</td>";
		echo "</tr>";
	echo "</table>";
	echo "</fieldset>";
	echo "</table>";
	echo "</form>";
	echo "<fieldset>";
	echo "<legend>Lista</legend>";
		echo "<table width=98% align=center cellspacing=2 cellpadding=4 border=0>";
			echo "<tr bgcolor=F9f9f9>";
				echo "<td width=300 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>";
					echo "SETOR";
				echo "</td>";
				echo "<td width=100 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>";
					echo "EST. MIN&Iacute;MO";
				echo "</td>";
				echo "<td width=100 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>";
					echo "EST. M&Aacute;XIMO";
				echo "</td>";
				echo "<td width=150 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>";
					echo "TEMPO REPOSI&Ccedil;&Atilde;O";
				echo "</td>";
				echo "<td width=150 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>";
					echo "EST. SEGURAN&Ccedil;A";
				echo "</td>";
			echo "</tr>";
			$select = "select a.set_nome, c.prset_minimo, c.prset_maximo, c.prset_tempo_reposicao, c.prset_seguranca, c.prset_codigo, c.set_codigo
							from setor a, produto b, produto_setor c
							where a.set_codigo = c.set_codigo
							and b.pro_codigo = c.pro_codigo
							and c.pro_codigo = $_GET[pro_codigo]";

			//echo $select;
			$exec_select = pg_query($select);
			while($linha = pg_fetch_array($exec_select))
			{
				echo "<tr>";
					echo "<td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>";
						echo $linha[0];
					echo "</td>";
					echo "<td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>";
						echo $linha[1];
					echo "</td>";
					echo "<td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>";
						echo $linha[2];
					echo "</td>";
					echo "<td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>";
						echo $linha[3];
					echo "</td>";
					echo "<td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>";
						echo $linha[4];
					echo "</td>";
					echo "<td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>";
						echo "<a href=materiais.php?acao=form_alterar&prset_codigo=$linha[5]&id_login=$id_login><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg></a>";
					echo "</td>";
					echo "<td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>";
						echo "<a href=\"apagarProdutoSetor.php?pro_codigo=$_GET[pro_codigo]&prset_codigo=$linha[5]&set_codigo=$linha[6]&id_login=$id_login\"><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.jpg></a>";
					echo "</td>";
				echo "</tr>";
			}
		echo "</table>";
	echo "</fieldset><br>";
}

//------------------------------------------------------------------>
//-> Formulario de ediÃ§Ã£o do produto_setor
//------------------------------------------------------------------>

if($acao == "form_alterar")
{
	reglog($id_login,"Formulario de Edicao de Materiais");
//
//-> Formulario de edicao do cadastro SIMPLES
	$select = "select a.set_codigo, c.prset_minimo, c.prset_maximo, c.prset_tempo_reposicao, c.prset_seguranca, c.pro_codigo
					from setor a, produto b, produto_setor c
					where a.set_codigo = c.set_codigo
					and b.pro_codigo = c.pro_codigo
					and c.prset_codigo = $_GET[prset_codigo]";
	$exec_select = pg_query($select);
	$linha = pg_fetch_array($exec_select);
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
	echo "<form method=post action=alteraProdutoSetor.php>";
	echo "<input type=hidden name=id_login value=$id_login>";
	echo "<input type=hidden name=prset_codigo value=$_GET[prset_codigo]>";
	echo "<input type=hidden name=pro_codigo value=$linha[pro_codigo]>";
	echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>";
	echo "<tr>";
	echo "<td>";
	echo "<fieldset>";
	echo "<legend>Altera&ccedil;&atilde;o de Produto</legend>";
	echo "<table width=100% align=center cellspacing=3 cellpadding=0 border=0>";
		echo "<tr>\n";
			echo "<td valign='middle'>Setor</td>\n";
			echo "<td><select name='set_codigo' class=box>\n";
				$UniQuery=pg_query("SELECT set_codigo, set_nome FROM Setor where set_estoque = 'S' ORDER BY set_nome");
				while($SetArray=pg_fetch_array($UniQuery)) {
					echo ($linha[set_codigo]==$SetArray[set_codigo])?"<option value='$SetArray[set_codigo]' selected> $SetArray[set_nome]</option>":"<option value='$SetArray[set_codigo]' > $SetArray[set_nome]</option>\n";
				}
				echo "</select>\n";
			echo "</td>\n";
		echo "</tr>\n";
		echo "<tr>";
			echo "<td width=115>Estoque Minimo</td>";
			echo "<td><input type=textarea name=pro_minimo class=box size=15 value='$linha[prset_minimo]'></td>";
		echo "</tr>";
		echo "<tr>";
			echo "<td width=115>Estoque Maximo:</td>";
			echo "<td><input type=textarea name=pro_maximo class=box size=15 value='$linha[prset_maximo]'></td>";
		echo "</tr>";
		echo "<tr>";
			echo "<td width=115>Tempo de Reposicao:</td>";
			echo "<td><input type=textarea name=pro_tempo_reposicao class=box size=15 value='$linha[prset_tempo_reposicao]'></td>";
		echo "</tr>";
		echo "<tr>";
			echo "<td width=115>Tempo de Seguranca:</td>";
			echo "<td><input type=textarea name=pro_seguranca class=box size=15 value='$linha[prset_seguranca]'></td>";
		echo "</tr>";
		echo "<tr>";
			echo "<td>";
				echo "<input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg>";
			echo "</td>";
		echo "</tr>";
	echo "<table>";
	echo "</fieldset>";
	echo "</table>";
	echo "</form>";
}



//------------------------------------------------------------------>
//-> Formulario de Edicao de Conteudo
//------------------------------------------------------------------>

 if($acao=="form_edit") {
 reglog($id_login,"Formulario de Edicao de Materiais");
//
//-> Formulario de edicao do cadastro SIMPLES

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
 $sqlproduto = "select * from produto where pro_codigo ='$pro_codigo'";
 $row=pg_fetch_array(pg_query($sqlproduto));
 if ($row[pro_entrada] == 'S' ) {
    $vlent1 = 'selected '; $vlent2 = '';
    }
  else {
    $vlent1 = ''; $vlent2='selected';
    }
 if ($row[pro_saida] == 'S' ) {
    $vlsai1 = 'selected '; $vlsai2 = '';
    }
  else {
    $vlsai1 = ''; $vlsai2='selected';
    }
 if ($row[pro_dispensacao] == 'S' ) {
    $vldis1 = 'selected '; $vldis2 = '';
    }
  else {
    $vldis1 = ''; $vldis2='selected';
    }
 if ($row[pro_transferencia] == 'S' ) {
    $vltra1 = 'selected '; $vltra2 = '';
    }
  else {
    $vltra1 = ''; $vltra2='selected';
    }
    
if ($row['pro_tipo'] == 'D' ) {
    $vltip1 = 'selected '; $vltip2 = ''; $vltip3 = ''; $vltip4 = '';
    }
 if ($row['pro_tipo'] == 'M' ) {
    $vltip1 = ''; $vltip2 = 'selected'; $vltip3 = ''; $vltip4 = '';
    }
 if ($row['pro_tipo'] == 'T' ) {
    $vltip1 = ''; $vltip2 = ''; $vltip3 = 'selected';  $vltip4 = '';
    }
 if ($row['pro_tipo'] == 'O' ) {
    $vltip1 = ''; $vltip2 = ''; $vltip3 = '';  $vltip4 = 'selected';
    }

  echo "<br><br><form method=post action=$PHP_SELF>
	<input type=hidden name=acao value=edit>
	<input type=hidden name=id_login value=$id_login>
	<input type=hidden name=pro_codigo value=$pro_codigo>
	<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Cadastro de Produto</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>

	      <tr>
		<td width=70>Nome:</td>
		<td><input type=text name=pro_nome class=box size=60 value='$row[pro_nome]'></td>
	      </tr>
	      <tr>
		<td width=70>Grupo:</td>
		<td>
		 <select name=gru_codigo class=box>";
	    //
	    //-> SQL do Estado
	    $query = pg_query("select gru_codigo, gru_nome from grupo order by gru_nome");
	      while($grupo=pg_fetch_array($query)) {
	       echo ($grupo[gru_codigo]==$row[gru_codigo])?"<option value='$grupo[gru_codigo]' selected>$grupo[gru_nome]</option>":"<option value='$grupo[gru_codigo]'>$grupo[gru_nome]</option>";
	      }
	   echo "</select>
	        </td>
	      </tr>
	      <tr>
		<td width=110>Tipo do Produto:</td>
		<td>
		 <select name=pro_tipo class=box>
		  <option value='D' $vltip1>Diversos</option>
		  <option value='M' $vltip2>Medicamentos</option>
		  <option value='T' $vltip3>Materiais Hospitalares</option>
          <option value='O' $vltip4>Odontol&oacute;gico</option>
		 </select>
	      </tr>
	      <tr>
		<td width=70>Unidade de Medida:</td>
		<td>
		 <select name=umed_codigo class=box>";
	    //
	    //-> SQL da Unidade de Medida
	    $query = pg_query("select umed_codigo, umed_nome from unidmedida order by umed_nome");
	      while($umed=pg_fetch_array($query)) {
	       echo ($umed[umed_codigo]==$row[umed_codigo])?"<option value='$umed[umed_codigo]' selected>$umed[umed_nome]</option>":"<option value='$umed[umed_codigo]'>$umed[umed_nome]</option>";
	      }
	   echo "</select>
	        </td>
	      </tr>
	      <tr>
		<td width=70>Portaria Psicotropico:</td>
		<td>
		 <select name=psico_codigo class=box>";
	    //
	    //-> SQL Psicotropico
	    $query = pg_query("select psico_codigo, psico_nome from psicotropicos order by psico_nome");
	       echo "<option value=''>---</option>";
	      while($psico=pg_fetch_array($query)) {
	       echo ($psico[psico_codigo]==$row[psico_codigo])?"<option value='$psico[psico_codigo]' selected>$psico[psico_nome]</option>":"<option value='$psico[psico_codigo]'>$psico[psico_nome]</option>";
	      }
	   echo "</select>
	        </td>
	      <tr>
		<td width=70>Codigo Barras: td>
		<td><input type=text name=pro_barcode class=box size=15 value='$row[pro_barcode]'></td>
	      </tr>
	      <tr>
		<td width=70>Custo de Referencia: </td>
		<td><input type=text name=pro_custo class=box size=20 value='$row[pro_custo]'></td>
	      </tr>
	      <tr>
		<td width=70>Embalagem:</td>
		<td><input type=text name=pro_embalagem class=box size=60 value='$row[pro_embalagem]'></td>
	      </tr>
	      <tr>
		<td width=70>Descricao Tecnica:</td>
		<td><input type=textarea name=pro_descricao_tecnica class=box size=40 value='$row[pro_descricao_tecnica]'></td>
	      </tr>
	      <tr>
		<td width=110>Mov. Entrada?:</td>
		<td>
		 <select name=pro_entrada class=box>
		  <option value=S $vlent1>Sim</option>
		  <option value=N $vlent2>Nao</option>
		 </select>
	      </tr>
	      <tr>
		<td width=110>Mov. Saida?:</td>
		<td>
		 <select name=pro_saida class=box>
		  <option value=S $vlsai1>Sim</option>
		  <option value=N $vlsai2>Nao</option>
		 </select>
	      </tr>
	      <tr>
		<td width=110>Mov. Dispensacao?:</td>
		<td>
		 <select name=pro_dispensacao class=box>
		  <option value=S $vldis1>Sim</option>
		  <option value=N $vldis2>Nao</option>
		 </select>
	      </tr>
	      <tr>
		<td width=110>Mov. Transferencia?:</td>
		<td>
		 <select name=pro_transferencia class=box>
		  <option value=S $vltra1>Sim</option>
		  <option value=N $vltra2>Nao</option>
		 </select>
	      </tr>
	      <tr>
		<td width=70>Estoque Minimo</td>
		<td><input type=textarea name=pro_minimo class=box size=15 value='$row[pro_minimo]'></td>
	      </tr>
	      <tr>
		<td width=70>Estoque Maximo:</td>
		<td><input type=textarea name=pro_maximo class=box size=15 value='$row[pro_maximo]'></td>
	      </tr>
	      <tr>
		<td width=70>Tempo de Reposicao:</td>
		<td><input type=textarea name=pro_tempo_reposicao class=box size=15 value='$row[pro_tempo_reposicao]'></td>
	      </tr>
	      <tr>
		<td width=70>Tempo de Seguranca:</td>
		<td><input type=textarea name=pro_seguranca class=box size=15 value='$row[pro_seguranca]'></td>
	      </tr>

	      <tr>
		<td width=70>Observacao:</td>
		<td><input type=textarea name=pro_observacao class=box size=40 value='$row[pro_observacao]'></td>
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
    $sql = "insert into produto ( " .
           "pro_nome, " .
           "gru_codigo, " .
           "pro_barcode, " .
           "pro_custo, " .
           "pro_embalagem, " .
           "pro_descricao_tecnica, " .
           "pro_observacao, " .
           "pro_saida, " .
           "pro_entrada, " .
           "pro_dispensacao, " .
           "pro_transferencia, " .
           "umed_codigo, " .
           "psico_codigo, " .
           "pro_tipo " .
            ") values ( " .
                       "upper('$pro_nome'), " .
                       ($gru_codigo            ? "'$gru_codigo'"            : "null") . "," .
                       ($pro_barcode           ? "'$pro_barcode'"           : "null") . "," .
                       ($pro_custo             ? "'$pro_custo'"             : "null") . "," .
                       ($pro_embalagem         ? "'$pro_embalagem'"         : "null") . "," .
                       ($pro_descricao_tecnica ? "'$pro_descricao_tecnica'" : "null") . "," .
                       ($pro_observacao        ? "'$pro_observacao'"        : "null") . "," .
                       ($pro_entrada        ? "'$pro_entrada'"        : "null") . "," .
                       ($pro_saida        ? "'$pro_saida'"        : "null") . "," .
                       ($pro_dispensacao        ? "'$pro_dispensacao'"        : "null") . "," .
                       ($pro_transferencia        ? "'$pro_transferencia'"        : "null") . "," .
                       ($umed_codigo        ? "'$umed_codigo'"        : "null") . "," .
                       ($psico_codigo        ? "'$psico_codigo'"        : "null") . "," .
                       ($pro_minimo        ? "'$pro_minimo'"        : "null") . "," .
                       ($pro_maximo        ? "'$pro_maximo'"        : "null") . "," .
                       ($pro_tempo_reposicao        ? "'$pro_tempo_reposicao'"        : "null") . "," .
                       ($pro_seguranca        ? "'$pro_seguranca'"        : "null") . "," .
                       ($pro_tipo        ? "'$pro_tipo'"        : "null") . " " .

                     " )";

//pro_saida char(1) NOT NULL,
//pro_entrada char(1) NOT NULL,
//pro_emprestimo char(1) NOT NULL,
//pro_dispensacao char(1) NOT NULL,
//pro_transferencia char(1) NOT NULL,
//pro_tipo char(1) NOT NULL, -- M - MEDICAMENTOS

echo "=>".$sql."<br>";


$query=pg_query($sql);
 reglog($id_login,"Adicionando Material $pro_nome Uni.: $pro_unidade Entr.: $pro_entrada Said. $pro_saida");
msg($id_login,$acao,$query);

}

//
//-> EDIT <--------------------------------------------------------->

 if($acao=="edit") {
  $sql = pg_query("update produto set " .
            "pro_nome=upper('$pro_nome'), " .
            "gru_codigo='$gru_codigo', " .
            ($pro_barcode ? "pro_barcode='$pro_barcode'" : "pro_barcode=null") . ", " .
            ($pro_custo ? "pro_custo='$pro_custo'" : "pro_custo=null") . ", " .
            ($pro_embalagem ? "pro_embalagem='$pro_embalagem'" : "pro_embalagem=null") . ", " .
            ($pro_descricao_tecnica ? "pro_descricao_tecnica='$pro_descricao_tecnica'" : "pro_descricao_tecnica=null") . ", " .
            ($pro_observacao ? "pro_observacao='$pro_observacao'" : "pro_observacao=null") . "," .
            ($pro_entrada ? "pro_entrada='$pro_entrada'" : "pro_entrada=null") . ", " .
            ($pro_saida ? "pro_saida='$pro_saida'" : "pro_saida=null") . ", " .
            ($pro_dispensacao ? "pro_dispensacao='$pro_dispensacao'" : "pro_dispensacao=null") . ", " .
            ($pro_transferencia ? "pro_transferencia='$pro_transferencia'" : "pro_transferencia=null") . ", " .
            ($umed_codigo ? "umed_codigo='$umed_codigo'" : "umed_codigo=null") . ", " .
            ($psico_codigo ? "psico_codigo='$psico_codigo'" : "psico_codigo=null") . ", " .
            ($pro_minimo ? "pro_minimo='$pro_minimo'" : "pro_minimo=null") . ", " .
            ($pro_maximo ? "pro_maximo='$pro_maximo'" : "pro_maximo=null") . ", " .
            ($pro_tempo_reposicao ? "pro_tempo_reposicao='$pro_tempo_reposicao'" : "pro_tempo_reposicao=null") . ", " .
            ($pro_seguranca ? "pro_seguranca='$pro_seguranca'" : "pro_seguranca=null") . ", " .
            ($pro_tipo ? "pro_tipo='$pro_tipo'" : "pro_tipo=null") . "  " .
            "where pro_codigo='$pro_codigo'");
 reglog($id_login,"Editando Material $pro_nome Uni.: $pro_unidade Entr.: $pro_entrada Said. $pro_saida");
msg($id_login,$acao,$sql);
}

//
//-> DEL <---------------------------------------------------------->

 if($acao=="del") {
  $sql = pg_query("delete from produto where pro_codigo='$pro_codigo'");
 reglog($id_login,"Excluindo Material Cod.: $pro_codigo");
msg($id_login,$acao,$sql);
}

?>

