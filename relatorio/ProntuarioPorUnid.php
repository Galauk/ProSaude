<?
/* 
* @brief       Gera o relatorio de prontuario por unidade
* mais informacoes...
* mais informacoes ainda...
*/
?>
<style type="text/css">
.quebra_pagina
{
	page-break-before: always;
}
</style>

<script language=javascript>

function imprimir() {
       window.print();
}
</script>
<?php
    //------------------------------------------------------------------>
    // -> Inclusao principal para montagem do sistema
    //------------------------------------------------------------------>
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
    
    $titulo="Prontuario por Unidade";
    $stmt = pg_query("SELECT uni_desc FROM unidade WHERE uni_codigo = $_GET[uni]");
    $unid = pg_fetch_array($stmt);
    
    include "cabecalho.php";
    echo $_GET['uni']!=-1 ? "UNIDADE: ".$unid[0] : "";
    $sql = "select a.usu_prontuario,a.usu_nome,to_char(a.usu_datanasc,'dd/mm/YYYY') as usu_datanasc,
                a.usu_mae,b.uni_desc,b.uni_localizacao
                from usuario a, unidade b 
                where a.uni_unidade = b.uni_codigo"
                .($_GET['uni']!=-1 ? " and a.uni_unidade = $_GET[uni]" : "").
                ($_GET['usu']!='' ? " and a.usu_codigo = $_GET[usu]" : "").
                " order by a.usu_nome";
    $sql = pg_query($sql);
    $count = 0;
echo "<table>
            <tr align='center'>"
                .($_GET['uni']==-1 ? "<td width='5%'>UNIDADE</td>" : "").
               "<td width='13%'>PRONTUARIO</td>
                <td width='13%'>DATA NASC.</td>
                <td width='35%'>PACIENTE</td>
                <td width='35%'>M&Atilde;E</td>
            </tr>";
            while($dados = pg_fetch_array($sql))
            {
                $dados['uni_localizacao']=='CADASTRAR' ? $uni = "N TEM" : $uni = $dados['uni_desc'];
                $count++;
                echo "<tr>"
                        .($_GET['uni']==-1 ? "<td>$uni</td>" : "").
                       "<td>".$dados['usu_prontuario']."</td>
                        <td align='center'>".$dados['usu_datanasc']."</td>
                        <td>".$dados['usu_nome']."</td>                        
                        <td>".$dados['usu_mae']."</td>
                    </tr>";
            }
echo "      <tr><td>TOTAL: $count</td><td>&nbsp;</td></tr>";
echo "</table>";