<style>
    p {
    margin: 0 0 0px 0px !important;
    font-size: 14px !important;
    /*  text-align: justify; */
}

thead {
    display: table-header-group;
}

tfoot {
    display: table-footer-group;
    overflow: visible;
}

/* DILEE COMENTOU PARA ARRUMAR A RECEITA COMUM 22/10/2018
table thead tr td{
    border:#FFFFFF 0px;
}

table tfoot tr td{
    border:#FFFFFF 0px;
}

table{
  border-collapse: collapse;
  width: 100%
}

table tr:hover{
  background-color: #EEE;
}

table tr th{
  border: 1px solid;
  padding: 4px 2px;
}

table tr td{
  border: 1px solid;
  padding: 2px 5px;
  font-size: 0.75em;
  height: 50px
}
*/

.div_cabecalho {
    border: solid #000 !important;
    border-width: 2px 2px 2px 2px !important;
    width: 570px;
    font-family: sans-serif;
    float: left;
    margin-left: 20px;
    margin-top: 50px;
}

.identificacao_estabelecimento {
    margin: 0px 15px 0px 15px;
    height: 100px;
    font-family: sans-serif;
}

.brasao {
    
    width: 15%;
    float: left;
    margin: 15px 0px 0px 0px;
    height: 80px;
}

.dados_sec {
    width: 75%;
    float: left;
    margin: 15px auto 0px auto;
    height: 80px;
    text-align: center;
    font-size: 12px;

}

.cod_bar {
    width: 95px;
    float: left;
    margin: 15px 0px 0px 0px;
    height: 80px;
    text-align: center;
    font-size: 12px;
}

#img_bar {
    margin: 15px 15px 15px 0px;
}

.dados_titulo {
    margin: 0px 15px 0px 15px;
    border: solid #000 !important;
    border-width: 2px 2px 0px 2px !important;
    height: 13px;
    font-family: sans-serif;
    font-size: 11px;
    text-align: center;
}

.outros_dados_sec {
    width: 40%;
    float: left;
    border: 1px solid;
}

.identificacao_formulario {
    margin: 0px 15px 5px 15px;
    border: solid #000 !important;
    border-width: 2px 2px 2px 2px !important;
    height: 65px;
    font-family: sans-serif;
}

.dados_form_dir_padrao {
    font-family: sans-serif;
    font-size: 10px;
    float: left;
    margin: 1px 0px 0px 10px;
    text-align: right;
}

.dados_form_dir {
    font-family: sans-serif;
    font-size: 11px;
    float: left;
    margin: 1px 0px 0px 10px;
    text-align: right;
}

.dados_form_esq_padrao {
    font-size: 10px;
    float: left;
    margin: 1px 0px 0px 10px;
    width: 320px;
}

.dados_form_esq {
    font-family: sans-serif;
    font-size: 11px;
    float: left;
    margin: 1px 0px 0px 10px;
    width: 350px;
}

.dados_form_esq_anvisa {
    font-size: 10px;
    float: left;
    margin: 2px 0px 0px 10px;
    width: 300px;
    line-height: 12px;
}

.dados_form_dir2 {
    font-size: 10px;
    float: right;
    margin: 10px 10px 0px 0px;
    text-align: center;
    /*    height: 100px;*/
}

.identificacao_responsavel {
    margin: 0px 15px 5px 15px;
    border: solid #000 !important;
    border-width: 2px 2px 2px 2px !important;
    height: 83px;
    font-family: sans-serif;

}

.table-dados {
    width: 100%;
    font-family: sans-serif;
    font-size: 11px;
    border-bottom: 0px solid;
}

.identificacao_responsavel_anvisa {
    margin: 0px 15px 5px 15px;
    border: solid #000 !important;
    border-width: 2px 2px 2px 2px !important;
    height: 30px;
    font-family: sans-serif;

}

.identificacao_responsavel_receb {
    margin: 0px 15px 5px 15px;
    border: solid #000 !important;
    border-width: 2px 2px 2px 2px !important;
    height: 300px;
    font-family: sans-serif;
    font-size: 10px;

}

.identificacao_responsavel_receb_smpl {
    margin: 0px 15px 5px 15px;
    border: solid #000 !important;
    border-width: 2px 2px 2px 2px !important;
    height: 405px;
    font-family: sans-serif;
    font-size: 10px;

}

.field_estab fieldset {
    margin: 15px 15px 15px 15px;
}

.dados_resp_receb {
    text-align: center;
}

.medicamentos_prescricao {
    font-size: 12px !important;
    height: 380px;

}

.medicamentos_prescricao_smpl {
    font-size: 12px !important;
    height: 346px;
}

.assinatura_resp {
    height: 40px;
    text-align: center;
}

.identificacoes {
    margin: 0px 15px 0px 15px;
    /*border:solid #000 !important;
    border-width:2px 2px 2px 2px !important;*/
    height: 115px;
    font-family: sans-serif;
    font-size: 12px;
    margin-bottom: 1px;
}

.identificacao_comprador {
    width: 50%;
    height: 115px;
    float: left;
}

.identificacao_fornecedor {
    width: 50%;
    height: 119px;
    float: left;
}

.titulo_identificacao_comprador {
    height: 20px;
    border: solid #000 !important;
    border-width: 2px 0px 0px 2px !important;
    width: 100%;
    font-size: 10px;
    text-align: center;
    line-height: 20px;
}

.titulo_identificacao_fornecedor {
    height: 20px;
    border: solid #000 !important;
    border-width: 2px 2px 0px 2px !important;
    /* width: 231px; */
    font-size: 10px;
    text-align: center;
    line-height: 20px;
}

.identificacao_comprador_dados {
    text-align: left;
    /* width: 203px; */
    height: 80px;
    border: 2px solid;
    border-width: 2px 0 2px 2px;
    font-size: 12px;
}

.identificacao_fornecedor_dados {
    /* width: 231px; */
    height: 80px;
    border: 2px solid;
    text-align: center;
}

#endereco {
    border: 2px solid;
    border-width: 2px 0px 0px 0px !important;
    font-size: 10px;
    font-family: sans-serif;
    width: 100%;
    text-align: center;
    height: 20px;
    line-height: 20px;
}

#titulo_impressao {
    /* width: 100%; */
    height: 20px;
    margin: 0 15px 4px 15px;
    text-align: center;
    font-family: sans-serif;
    font-size: 14px;
    border-width: 2px 0px 0px 0px !important;
    border: 2px solid #000 !important;
    line-height: 20px;
}

@CHARSET "ISO-8859-1";

body{
  margin: 0;
  padding: 0;
  font-family: Verdana;
  background-color: #FFF;
}

#page{
  width: 90% !important; 
  margin: 0 auto;
  padding: 10px;
}

#header{
  height: 65px;
}

#header_logo{
  float: left;
  width: 60px;
  height: 75px;
  overflow: hidden;
}

#header_logo img{
  width: 60px;
}

#header_dados{
  width: 90%;
  margin: 10px 0 0 0;
}

#sec_nome{
  text-align: center;
  font-size: 18px;
  font-weight: bold;
}

#pref_nome{
  text-align: center;
  font-size: 14px;
}

#header_barcode{
  float: left;
  margin: 10px 0 0 0;
}

#dados_pac{
  background-color: #EFEFEF;
  border-top: 1px solid #000;
  border-bottom: 1px solid #000;
  /*margin: 0px 0px 0px 0;
  padding: 15px 10px;*/
  font-size: 10px;
  height: auto;
}

#pac_nome{
  font-size:15px;
  font-weight: bold; 
}

.dados_gerais{
  font-size:12px;
}

#receita{
	min-height: 250px;	
}

#receita p{
  margin: 0 0 30px 10px;
  font-size: 14px;
/*  text-align: justify; */
}

#receita label{
  font-size: 12px;
  width: 120px;
  display: inline-block;
  text-align: right;
  margin: 2px 5px 2px 0;
}

#receita span{
  font-size: 10px;
  font-weight: bold;

}

#rec_titulo{
  background-color: #EFEFEF;
  padding: 2px 10px;
  margin: 0 0 10px 0;
  border-bottom: 1px solid #999;
}

h2.titulo{
  padding: 3px 5px;
  margin: 0 0 5px 0;
  font-size: 12px;
  background-color: #EFEFEF;
  border-bottom: 1px solid #999;
  font-weight: none; 
}

.medTitulo{
  float: left;
  font-size: 11px;
  font-weight: bold;
  padding: 0 0 0 5px;
}

.medQtd{
  float: right;
  font-size: 12px;
  font-weight: bold;
  padding: 0 15px 0 0;
} 

.sublinhas{
    width: 100%;
    font-size: 10px;
    margin-left: 7px;
}

.medUso{
  font-size: 8px;
  margin: 0 0 0px 0;
  padding: 0 0 0 15px;
} 

#medico{
  border-top: 1px solid #000;
  width: 250px;
  margin: 50px auto 0 auto;
  text-align: center;
}

#medico h2{
  font-size: 12px;
  margin: 0;
  padding: 0;
}

#medico h3{
  font-size: 10px;
  margin: 0;
  padding: 0;
  font-weight: none; 
}

#cid{
  float: left;
  padding: 5px;
  background-color: #EFEFEF;
  border: 1px solid;
  font-size: 10px;
  width: 162px;
}

#cid p{ text-align: center; }
#cid div{
  font-weight: bold;
  font-size: 9px; 
}




#footer{
  background-color: #EFEFEF;
  border-top: 1px solid #000;
  text-align: center; 
  font-size: 10px;
  margin: 20px 0 0 0;
  padding: 7px 0;
}

.left{ float: left; }
.right{ float: right; }
.clear{ clear: both }

#pac_end{
    height: auto;
}
</style>
<?php

    

?>
<head>
    <script>
        window.print();
    </script>
</head>
<body>
<div style="clear:both;"></div>
<?for($i=1;$i<=2;$i++):?>
    <div class="div_cabecalho">
        <div class="identificacao_estabelecimento">
            <div class="brasao">
                <img src="zf/public/images/brasao.jpg" alt="">
            </div>
            <div class="dados_sec">
                <br/>
                <b></b><br/>
                C.N.P.J.:<br/>
                <b></b>
            </div>
            <div class="cod_bar">
                <div id="img_bar">
                
                </div>
            </div>
        </div>
        <div id="titulo_impressao"><b>RECEITUÁRIO CONTROLE ESPECIAL</b></div>
        <div class="dados_titulo">
            IDENTIFICAÇÃO DO EMITENTE
        </div>
        <div class="identificacao_formulario">
            <div class="dados_form_dir">
                Nome Completo:<br/> 
                CRM:<br/>
                End.Completo:<br/>
                Telefone:<BR/>
                Cidade:
            </div>
            <div class="dados_form_esq_anvisa">
                <b> <br/></b> 
                <b> </b><br/>
                <b> </b><br/>
                <b> </b><BR/>
                <b> </b>
            </div>
            <div class="dados_form_dir2">
                Dt.Emissão: <b></b>
                <br/>
                <?if($i == 1){
                   echo $i."ª Via Farmácia";
                }else{
                   echo $i."ª Via Paciente";
                }
?>
            </div>
        </div>

        <div class="identificacao_responsavel_anvisa">
           <div class="dados_form_dir">
                Paciente:<br/> 
                Endereco:<br/>
            </div>
            <div class="dados_form_esq">
                <table>
                    <b><br/></b> 
                    <b></b><br/>
                </table>
                
            </div>
        </div>

        <div class="identificacao_responsavel_receb">
            <div class="dados_resp_receb">
                <b>Prescrição</b>
            </div>
            <div class="medicamentos_prescricao">
                
			<div class="medItem">
                                
                                <!-- #105247 NOVOS ITENS PRODUTO E DESCRIÇÃO -->
                            
                                <div class="medTitulo">
                                   
				</div>
                            
                                <!-- #105247 FIM NOVOS ITENS -->
                            
				<div class="medTitulo">
			
				</div>
				<div class="medQtd"><?= $item->irec_quantidade; ?></div>	
				<div class="clear"></div>
				<div class="medUso"><?= $item->irec_recomendacao; ?></div>
			</div>
		
            </div>
            <div class="assinatura_resp">
               --------------------------------------------------------------<br/>
                   
            </div>
        </div>
        
        <div class="identificacoes">
           <div class="identificacao_comprador">
               <div class="titulo_identificacao_comprador">  
                   IDENTIFICAÇÃO DO COMPRADOR
               </div>
               <div class="identificacao_comprador_dados">  
                   Nome:&nbsp; _____________________<br/>
                   Ident:__________Org.Emissor: __<br/>
                   End:  _______________________<br/>
                   Cidade: ___________Estado: ___<br/>
                   Fone: ______________________<br/>
               </div>
            </div>
            <div class="identificacao_fornecedor">
                <div class="titulo_identificacao_fornecedor">  
                   IDENTIFICAÇÃO DO FORNECEDOR  
               </div>
                <div class="identificacao_fornecedor_dados">
                    <br/>
                   ______________________________<br/>
                   Assinatura do Farmacêutico<br/>
                   <br/>
                   Data:___/___/___
               </div>
            </div>
        </div>
        <div id="endereco">
           
            
        </div>
        
    </div>
<?  endfor;?>
</body>

