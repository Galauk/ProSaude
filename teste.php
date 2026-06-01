<link href='<?=$this->baseUrl('/public/css/prontuario/receita-medica/imprimir-anvisa.css')?>' rel='stylesheet' type='text/css'> </link>
<link href='<?=$this->baseUrl('/public/css/print.css')?>' rel='stylesheet' type='text/css'> </link>
<head>
    <script>
        window.print();
    </script>
</head>
<body>
<div style="clear:both;"></div>
<?php
    $qnt_vias = 2;
?>
<?for($i=1;$i<=$qnt_vias;$i++):?>
    <div class="div_cabecalho">
        <div class="identificacao_estabelecimento">
            <div class="brasao">
                <img src="../WebSocialComum/public/images/brasao.gif");" width="80" height="70"/>
            </div>
            <div class="dados_sec">
                <br/>
                <b>Nome secretaria</b><br/>
                C.N.P.J.:CNPJ<br/>
                <b>Descricao Unidade</b>
            </div>
            <div class="cod_bar">
                &nbsp;
            </div>
        </div>
        <div id="titulo_impressao"><b>tipo impressao</b></div>
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
                <b><?=date("d/m/Y");?></b>
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
             
            Dados da Unidade
        </div>
    </div>
<?  endfor;?>
</body>

