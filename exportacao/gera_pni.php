<?php
	require_once '../global.php';
	setError(1);
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=ISO-8859-1" />
	<link href="/WebSocialSaude/lib/themes/ui-lightness/jquery-ui-1.8.10.custom.css" rel="stylesheet" type="text/css" />
	<link href="/WebSocialComum/library/css/ui.jqgrid.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="/WebSocialComum/library/js/jquery-1.6.2.min.js"></script>
	<script type="text/javascript" src="/WebSocialSaude/lib/ui/jquery-ui-1.8.10.custom.js"></script>
	<script type="text/javascript" src="/WebSocialComum/library/js/i18n/grid.locale-pt-br.js"></script>
	<script type="text/javascript" src="/WebSocialComum/library/js/jquery.jqGrid.min.js"></script>
	<script language="JavaScript" type="text/javascript" src="../funcoes.js"></script>
	<?php $form = new classForm(); ?>
	<?php $table = new tableClass(); ?>
	<?php $common = new commonClass(); ?>

</head>
<script>
	jQuery(function($){
		$("#tabs").tabs();
		$("#list12").jqGrid('navGrid','#pager12',{add:false,edit:false,del:false,search:false});
	  })
</script>
<body>
<?php

	echo $common->menuTab(array("SI-PNI"));
	echo $common->bodyTab();

	$cnes = pg_fetch_array(pg_query("select *from unidade where uni_codigo = ".$_REQUEST['uni_codigo'].""));
	$pasta = $_SESSION[root].$_SESSION[modulo]."exportacao/sipni/";
	$vs = pg_fetch_array(pg_query("select * from config where conf_chave = 'VERSAO_SAUDE'"));
	$ss = pg_fetch_array(pg_query("select * from config where conf_chave = 'SIGLA_SISTEMA'"));
	$sa = pg_fetch_array(pg_query("select * from config where conf_chave = 'NOME_DO_SISTEMA'"));
	$cs = pg_fetch_array(pg_query("select * from config where conf_chave = 'CODIGO_DO_SISTEMA'"));
	$ns = pg_fetch_array(pg_query("select * from config where conf_chave = 'SENHA_DO_ARQUIVO'"));

	$sigla_sistema = $ss[conf_valor_string];
	$senha_arquivo = $ns[conf_valor_string];
	$nome_sistema = $ns[conf_valor_string];
	$versao = $vs[conf_valor_string];
        
	
#
# NOMENCLATURA DOS ARQUIVOS
#		
	$file_dadosImportacao = $pasta."H".$cnes[uni_cnes].".DAT";
	$file_dadosVacinados = $pasta."P".$cnes[uni_cnes].".DAT";
	$file_registroIndividual = $pasta."L".$cnes[uni_cnes].".DAT";
	$file_boletimImuno = $pasta."B".$cnes[uni_cnes].".DAT";
	$file_itenBoletim = $pasta."I".$cnes[uni_cnes].".DAT";
        //var_dump($file_dadosImportacao,$file_dadosVacinados,$file_registroIndividual,$file_boletimImuno,$file_itenBoletim);die('asda');
	
#
# GERA INFORMACAO
#	
	$dadosImportacao = "\"$sigla_sistema\",\"$versao\",\"$versao\"";	
	
	
	$fp01 = fopen($file_dadosImportacao,"w");
	fputs($fp01,$dadosImportacao);
	
#
# DADOS DO VACINADO
#	
$fp02 = fopen($file_dadosVacinados,"w");
$sql = pg_query("select cnes_cod_cns,usu_cartao_sus,usu_nome,usu_mae,usu_sexo,to_char(usu_datanasc,'dd/mm/yyyy') as usu_datanasc,rac.rac_codigo,p.pais_codigo,cid_codigo_ibge,rua_bairro,rua_nome,dom_numero,dom_complemento,rua_cep,dom_telefone,dom.bai_codigo 
					from vacina_usuario  as vac 
					join usuario as usu on usu.usu_codigo = vac.usu_codigo 
					left join raca as rac on rac.rac_codigo = usu.rac_codigo 
					join domicilio as dom on dom.dom_codigo = usu.dom_codigo
					join rua as r on r.rua_codigo = dom.dom_codigo 
					join tb_ms_tipo_domicilio as tpdom on tpdom.co_tipo_domicilio = dom.co_tipo_domicilio 
					join cidade as c on c.cid_codigo=r.cid_codigo 
					join estado as e on c.uf_codigo = e.uf_codigo 
					join pais as p on p.pais_codigo = e.pais_codigo
					join usuarios as usr on usr.usr_codigo = vac.usr_codigo
					group by cnes_cod_cns,usu_cartao_sus,usu_nome,usu_mae,usu_sexo,usu_datanasc,rac.rac_codigo,p.pais_codigo,cid_codigo_ibge,rua_bairro,rua_nome,dom_numero,dom_complemento,rua_cep,dom_telefone,dom.bai_codigo ");

while($rr = pg_fetch_array($sql)) {	
	if($rr[usu_sexo]!=" ") {
		//$dadosVacinado .= "$cs[conf_valor_string],\"$senha_arquivo\",\"$cnes[uni_cnes]\",\"$rr[cnes_cod_cns]\",\"$rr[usu_nome]\",\"$rr[usu_mae]\",\"$rr[usu_sexo]\",\"$rr[usu_datanasc] 00:00:00\",\"$rr[rac_codigo]\",\"U\",\"$rr[pais_codigo]\",\"".substr($rr[cid_codigo_ibge],0,6)."\",\"$rr[rua_bairro]\",\"$rr[rua_nome]\",\"$rr[dom_numero]\",\"$rr[dom_complemento]\",\"".substr(str_replace("-","",$rr[rua_cep]),0,8)."\",\"$rr[dom_telefone]\",\"\",\"\",\"\",\"\",\"\",\"\"\r\n";
		$dados = utf8_encode($dadosVacinado);
		$dadosVacinado .= "$cs[conf_valor_string],\"$senha_arquivo\",\"$cnes[uni_cnes]\",\"\",\"$rr[usu_nome]\",\"$rr[usu_mae]\",\"$rr[usu_sexo]\",\"$rr[usu_datanasc] 00:00:00\",\"\",\"\",\"$rr[pais_codigo]\",\"".substr($rr[cid_codigo_ibge],0,6)."\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\",\"\"\r\n";
	}
}
	fputs($fp02,$dados);
#
# PUT INFORMACAO INTO FILE
#	
	

	
		
	
	$fp03 = fopen($file_registroIndividual,"w");
	$fp04 = fopen($file_boletimImuno,"w");
	$fp05 = fopen($file_itenBoletim,"w");
	
	
	
	
	



	
	echo $common->closeTab();
?>
</body>
</html>
