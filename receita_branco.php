<link href='zf/public/css/prontuario/receita-medica/imprimir-anvisa.css' rel='stylesheet' type='text/css'> </link>
<link href='zf/public/css/print.css' rel='stylesheet' type='text/css'> </link>
<head>
    <script>
    </script>
</head>
<body>
<div style="clear:both;"></div>
<?php
include "../WebSocialComum/library/php/db.inc.php";
  $uni = pg_fetch_array(pg_query("select *from unidade where uni_codigo =".$_REQUEST['uni_codigo']));
  $sec = pg_fetch_array(pg_query("select *from secretaria limit 1"));
    $qnt_vias = $_REQUEST['qtd'];
?>
<?for($i=1;$i<=$qnt_vias;$i++):?>
    <div class="div_cabecalho">
        <div class="identificacao_estabelecimento">
            <div class="brasao">
                <img src="zf/public/images/brasao.gif" width="80" height="70"/>
            </div>
            <div class="dados_sec">
                <br/>
                <b><?=utf8_encode($sec[nome_secretaria])?></b><br/>
                C.N.P.J.:<?=$sec[cnpj_secretaria]?><br/>
                <b><?=$uni[uni_desc]?></b>
            </div>
            <div class="cod_bar">
                &nbsp;
            </div>
        </div>
        <div id="titulo_impressao"><b>RECEITUARIO MEDICO</b></div>
        <div class="dados_titulo">
            IDENTIFICAÇÃO DO PACIENTE
        </div>

        <div class="identificacao_responsavel">
           <div class="dados_form_dir_padrao">
                Pront / Paciente:<br/> 
                Idade / Sexo:<br/> 
                Dt.Nascimento:<br/>
                CNS:<br/>
                Endereço:<br/>
                Dt.Emissão:<br/>
            </div>
            <div class="dados_form_esq_padrao">
                <b>&nbsp;<br/></b>
                <b>&nbsp; / &nbsp;</b><br/>
                <b> &nbsp;&nbsp;&nbsp;<br/></b>
                <b>&nbsp;</b><br/>
                <b>&nbsp;</b><br/>
                <b>&nbsp;</b>
            </div>
        </div>

        <div class="identificacao_responsavel_receb_smpl">
            <div class="dados_resp_receb">
                <b>Prescrição</b>
            </div>
            <div class="medicamentos_prescricao_smpl">


            </div>
            <div class="assinatura_resp">
                --------------------------------------------------------------<br/>
                    Medico<br/>
                    CRM:
            </div>
        </div>
        <div id="endereco">
             
            <?=$uni[uni_endereco]?>, CNPJ:<?$uni[uni_cnpj]?>, FONE:<?=$uni[cnes_telefone]?>
        </div>
    </div>
<?  endfor;?>
</body>

