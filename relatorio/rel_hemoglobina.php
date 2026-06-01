<?php
    session_start();
    require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
    require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";

    ini_set('memory_limit', '2048M');

    $total = 0;
    $recebeCodigoUnidade = '';
    $recebeDatas = '';

    // var_dump($unidadeId);

    if ($unidadeId) {
        $recebeCodigoUnidade = 'and ate.uni_codigo = '.$unidadeId;
    }

    // var_dump($dataFinal);die();
    if ($dataInicial != '' && $dataFinal != '') {
        $recebeDatas = " and dt_requisicao between '".$dataInicial."' and '".$dataFinal."'";
    }

    // die($recebeDatas);

    $query = "SELECT  usr.usr_codigo, usr.usr_nome, proc.proc_nome, to_char(dt_requisicao,'DD/MM/YYYY'), uni.uni_desc
    from requisicao_exames as req
        inner join atendimento as ate 
            on ate.ate_codigo = req.ate_codigo

        inner join agendamento as age
            on ate.ate_codigo = age.age_codigo

        inner join usuarios as usr
            on age.med_codigo = usr.usr_codigo

        inner join unidade as uni
            on ate.uni_codigo = uni.uni_codigo
        
        inner join procedimento as proc
            on req.proc_codigo = proc.proc_codigo

        where proc.proc_codigo = 4626 $recebeCodigoUnidade $recebeDatas
    ";


    // die($query);

    $sql = pg_query($query) or die (pg_last_error());
    
    while ($a = pg_fetch_array($sql)) {
        $a->usr_nome = trim($a->usr_nome);
        $a->proc_nome = trim($a->proc_nome);

        $recebeQuery[] = $a;
    }

?>

<style>
    #header {
        height: 85px;
    }

    #header_dados {
        float: left;
        width: 390px;
        margin: 10px 0 0 0;
    }

    #sec_nome {
        text-align: center;
        font-size: 18px;
        font-weight: bold;
    }

    #pref_nome {
        text-align: center;
        font-size: 14px;
    }

    #dados_pac {
        background-color: #EFEFEF;
        border-top: 1px solid #000;
        border-bottom: 1px solid #000;
        margin: 0px 0 10px 0;
        padding: 15px 10px;
        font-size: 10px;
    }

    #header_logo {
        float: left;
        width: 60px;
        height: 75px;
        overflow: hidden;
    }

    #pac_nome {
        font-size: 15px;
        font-weight: bold;
    }

    #header_logo {
        float: left;
        width: 60px;
        height: 75px;
        overflow: hidden;
    }

    #header_logo img {
        width: 60px;
    }

    td {
        text-align: center;
    }
</style>

<table style="width:100%">
    <div id="header">
        <div id="header_logo">
            <img src="/WebSocialComum/imgs/brasao.jpg" title="Logo Prefeitura">
        </div>
        <div id="header_dados">
            <div id="sec_nome">SMS DE PALOTINA</div>
            <div id="pref_nome">R. GETULIO VARGAS, 739</div>
        </div>
        <div id="header_barcode"></div>
        <div class="clear"></div>
    </div>

    <div id="dados_pac">
        <div id="pac_nome">SOLICITA&Ccedil;&Otilde;ES - ELETROFORESE DE HEMOGLOBINA	 <font color="blue"></font>  <font color="red"></font> <div id="periodo" style="float:right; font-size: 14px;">PER&Iacute;ODO: <?=date("d/m/Y", strtotime($dataInicial)).' - '.date("d/m/Y", strtotime($dataFinal))?></div></div>
        <div id="pac_end"></div>
    </div>

    <tr>
        <th>Solicitante</th>
        <th>Unidade</th>
        <th>Exame</th>
        <th>Data Requisicao</th>
    </tr>
    
    <?
        for ($contador=0; $contador < count($recebeQuery); $contador++) {
            $total++;
            echo "<tr>
                    <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>
                        {$recebeQuery[$contador][usr_nome]}
                    </td>
                    
                    <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>
                        {$recebeQuery[$contador][uni_desc]}
                    </td>

                    <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>
                        {$recebeQuery[$contador][proc_nome]}
                    </td>
                    
                    <td align=left style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>
                        {$recebeQuery[$contador][to_char]}
                    </td>

                </tr>";   
        }
    ?>

    <td></td>

    <td></td>

    <td style="font-weight: bold;padding: 15px;">
        Total de Solicita&ccedil;&otilde;es
    </td>

    <td style="font-weight: bold;padding: 15px;">
        <?php echo $total; ?>
    </td>
</table>