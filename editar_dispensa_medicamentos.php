<?
/**
 * @version Renato 9/5/2007 - 16:50
 * @author Anderson
 * @brief Programa para editar os itens das dispensações
 */
?>
<script>
    function exec_atualizar()
    {
        window.opener.atualizarGrid2(1);
    }
</script>
<?php

/*echo "<pre>";
    print_r($_REQUEST);
echo "<pre>";*/
session_start();
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
cabecario();

if (isset($op) && $op == "envia") {
    // Atualiza a tabela de itens_movimento
    $sql_mov = "UPDATE itens_movimento SET ite_quantidade = '$_POST[quantidade]', ite_qtde_dia = '$_POST[quantidade_dia]',
                ite_qtde_solicitada = '$_POST[total_dias]', ite_posologia = '$_POST[posologia]', ite_detalhes_tratamento = '$_POST[detalhes]',
                ite_observacoes = '$_POST[observacoes]' WHERE mov_codigo = $_POST[mov_cod] AND ite_codigo = $_POST[ite_cod]";
    $query_mov = pg_query($sql_mov) or die(pg_last_error());
    
    // Atualiza a tabela de produtos
    $sql_pro = "UPDATE produto SET pro_nome = '$_POST[produto]' WHERE pro_codigo = $_POST[pro_cod]";
    $query_pro = pg_query($sql_pro) or die(pg_last_error());
    
    echo "<script>exec_atualizar();self.close();</script>";
    
    exit();
}

// Pega as variaveis
$mov_cod = $_GET[mov_cod];
$pro_cod = $_GET[pro_cod];
$ite_cod = $_GET[ite_cod];

// Pega os dados
$sql = "SELECT a.mov_codigo AS mov_cod, a.pro_codigo AS pro_cod, a.ite_quantidade AS qtd,
        a.ite_qtde_dia AS qtd_dia, a.ite_qtde_solicitada AS total_qtd,
        a.ite_posologia AS posologia, a.ite_detalhes_tratamento AS detalhes, 
        a.ite_observacoes AS obs, b.pro_nome AS produto
        FROM itens_movimento a, produto b 
        WHERE a.pro_codigo = b.pro_codigo
        AND mov_codigo = $mov_cod
        AND ite_codigo = $ite_cod";
$query = pg_query($sql) or die(pg_last_error());
$row = pg_fetch_array($query);
?>
<script>
function dividir() {
    quantidade = document.getElementById('quantidade').value;
    quantidade_dia = document.getElementById('quantidade_dia').value;
    
    if(quantidade == "") {
            document.getElementById('quantidade').value = "";
            document.getElementById('quantidade_dia').value = "";
            document.getElementById("total_dias").value = "";
            alert("A quantidade deve ser digitada");
            document.getElementById("quantidade").focus();
            return false;
    }
    
    if(quantidade_dia == "") {
            document.getElementById('quantidade').value = "";
            document.getElementById('quantidade_dia').value = "";
            document.getElementById("total_dias").value = "";
            alert("A quantidade por dia deve ser digitada");
            document.getElementById("quantidade_dia").focus();
            return false;
    }
    
    divisao = quantidade / quantidade_dia;
    document.getElementById('total_dias').value = divisao;
}
</script>
<form name="form1" action="<?=$PHP_SELF?>?op=envia" method="post">
<fieldset><legend>EDITAR ITENS DE DISPENSA&Ccedil;&Atilde;O</legend>
<input type="hidden" name="mov_cod" value="<?=$mov_cod?>">
<input type="hidden" name="pro_cod" value="<?=$pro_cod?>">
<input type="hidden" name="ite_cod" value="<?=$ite_cod?>">
<table>
    <tr>
        <td>Produto</td>
        <td><input type="text" name="produto" value="<?=$row[produto]?>" size="50" class="box"></td>
    </tr>
    <tr>
        <td>Quantidade</td>
        <td><input type="text" name="quantidade" id="quantidade" value="<?=round($row[qtd])?>" size="10" class="box"></td>
    </tr>
    <tr>
        <td>Quantidade Dia</td>
        <td><input type="text" name="quantidade_dia" id="quantidade_dia" value="<?=$row[qtd_dia]?>" size="10" class="box" onBlur="dividir()"></td>
    </tr>
    <tr>
        <td>Total Dias</td>
        <td><input type="text" name="total_dias" id="total_dias" value="<?=round($row[total_qtd])?>" size="10" class="box"></td>
    </tr>
    <tr>
        <td>Posologia</td>
        <td><textarea name="posologia" rows="5" cols="49" class="box"><?=$row[posologia]?></textarea></td>
    </tr>
    <tr>
        <td>Detalhes do Tratamento</td>
        <td><textarea name="detalhes" rows="5" cols="49" class="box"><?=$row[detalhes]?></textarea></td>
    </tr>
    <tr>
        <td>Observa&ccedil;&otilde;es</td>
        <td><textarea name="observacoes" rows="5" cols="49" class="box"><?=$row[obs]?></textarea></td>
    </tr>
    <tr>
        <td colspan="2"><input type="image" src="<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/editar_on.jpg"></td>
    </tr>
</table>
</fieldset>
</form>