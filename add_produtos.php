<?
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>

	include_once "authlib.inc.php";
	verauth($id_login);
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();
//------------------------------------------------------------------>
// -> Inclusao funcao lucio
//------------------------------------------------------------------>

reglog($id_login,"Adicionando Procedimento de Exames");
if($act=="addproc") {
    $tipo_movimento = 'S';
    $mov_saida = 'D';
    $mov_data = date('d/m/Y');
	$sqlsaida=pg_query("select nextval('seq_mov_codigo'::text) as novo_codigo");
    $rowsaida = pg_fetch_array($sqlsaida);
    $set_saida = 100138;
    $set_entrada = 100138;
    $sql = "insert into movimento ( " .
	    "mov_codigo, " .
            "ate_codigo, " .
            "mov_data, " .
            "mov_tipo, " .
            "mov_saida, " .
            "usu_codigo, " .
            "mov_observacao, " .
            "set_saida, " .
            "set_entrada, " .
            "mov_nr_nota, " .
            "mov_dt_nota, " .
            "usr_codigo, " .
            "mov_data_inclusao, " .
            "mov_ip, " .
            "mov_total_nota  " .
            ") values ( " .
	    "$rowsaida[novo_codigo]" . ", " .
            "$ate_codigo" . ", " .
            ($mov_data ? "'$mov_data'" : "null") . ", " .
            "'{$tipo_movimento}'" . ", " .  //tipo da movimentação S - Saida
            "'{$mov_saida}'" . ", " .  //tipo da movimentação D - Dispensacao
            ($usu_codigo ? "'$usu_codigo'" : "null") . ", " .
            ($mov_observacao ? "'$mov_observacao'" : "null") . ", " .
            ($set_saida ? "'$set_saida'" : "null") . ", " .
            ($set_entrada ? "'$set_entrada'" : "null") . ", " .
            ($mov_nr_nota ? "'$mov_nr_nota'" : "null") . ", " .
            ($mov_dt_nota ? "'$mov_dt_nota'" : "null") . ", " .
            ($id_login ? "'$id_login'" : "null") . ", " . //Mover o login do usuario que esta usando
            "date(now())" . ", " .
            ($mov_ip ? "'$mov_ip'" : "null") . ", " . //Mover o ip do micro do que o usuario esta usando
            ($mov_total_nota ? "'$mov_total_nota'" : "null") . "  " . //Fazer update na gravacao da nota
            ")";
    $consolidado = 'S';
    $ite_quantidade = "1";
    $selprod = pg_fetch_array(pg_query("select *from produto where pro_codigo = '$produto'"));
    $sqq = "insert into itens_movimento ( " .
            "usr_codigo, " .
            "pro_codigo, " .
            "ite_quantidade, " .
            "ite_vlrunit, " .
            "mov_codigo, " .
            "ite_consolidado, " .
            "ite_lote, " .
            "ite_validade  " .
            ") values ( " .
            ($usu_codigo ? "'$usu_codigo'" : "null") . ", " .
            ($produto ? "'$produto'" : "null") . ", " .
            ($ite_quantidade ? "'$ite_quantidade'" : "null") . ", " .
            ($selprod[pro_custo] ? "'$selprod'" : "null") . ", " .
            ($rowsaida[novo_codigo] ? "'$rowsaida[novo_codigo]'" : "null") . ", " .
            ($consolidado ? "'$consolidado'" : "null") . ", " .
            ($ite_lote ? "'$ite_lote'" : "null") . ", " .
            ($ite_validade ? "'$ite_validade'" : "null") . "  " .
            ")";
$q1 = pg_query($sql);
$q2 = pg_query($sqq);
        echo "<SCRIPT LANGUAGE=\"JavaScript\">
                  setTimeout(\"location='add_produtos.php?mov_codigo=$rowsaida[novo_codigo]&id_login=$id_login&ate_codigo=$ate_codigo&usu_codigo=$usu_codigo'\", 0);
              </SCRIPT>";
}
if($act=="") {
  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
         <tr>
          <td>
             <table width=100% align=center cellspacing=2 cellpadding=4 border=0>
              <tr bgcolor=F9f9f9>
                <td width=400 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Produto</td>
                <td width=40 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Quantidade</td>";

   $sql=pg_query("select *from itens_movimento where usr_codigo = '$usu_codigo'");

     while($row=pg_fetch_array($sql)) {
    $selprod = pg_fetch_array(pg_query("select *from produto where pro_codigo = '$row[pro_codigo]'"));
       $intquantidade = formata_valor0($row['ite_quantidade']);
       echo "<tr>
               <td width=400 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$selprod[pro_nome]</td>
               <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$intquantidade</td>
             </tr>";
     }
        echo "</tr>

             </table>
          </td>
         </tr>
        </table>";
}
