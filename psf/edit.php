<script type="text/javascript" src="../ajax_motor.js"></script>
<script language="javascript" src="jquery-1.3.2.js"></script>
<script type="text/javascript" src="../fazer_agendamento.js.php"></script>
<script type="text/javascript" src="funcaobuscaEndereco.js"></script>
<script type="text/javascript" src="../funcoes_busca.js"></script>
<link href="teste.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="../style/Style.css" type="text/css" />
<link href="../estiloNovo.css" type="text/css" rel="stylesheet">
<script>
$(document).ready(function() {
$("#content > div").hide();
$("#content > div:eq(0)").show();
$("#tabs > a:eq(0)").css("background", "url(tab-selected.jpg) top left no-repeat");
});

function opentab(num) {
	$("#content > div").hide();
	$("#content > div:eq(" + (num-1) + ")").fadeIn();
	$("#tabs > a").css("background", "url(tab.jpg) top left no-repeat");
	$("#tabs > a:eq(" + (num-1) + ")").css("background", "url(tab-selected.jpg) top left no-repeat");	
}

//////////////////////////////////////////funcao de abas///////////////////////////////////////////////
function adicionaPac(){
	url = "listPacientesPsf.php";
window.open(url, null, "height=600,width=800,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes"); 
}
function addPacientes( codigo, nome, nascimento, mae, cidade )
{
	$('pac_codigo').value 		= codigo;
	$('pac_nome').value 		= nome;
	$('pac_nascimento').value 	= nascimento;
	$('pac_mae').value 			= mae;
	$('pac_cidade').value 		= cidade;
	$('pac_prontuario').value 	= '';
	var numero_fam = document.getElementById('numero_fam').value;
	var codigo_ficha_familia = document.getElementById('codigo_ficha_familia').value;


	url = "inserir_pac.php?codigo="+codigo+"&nome="+nome+"&nascimento="+nascimento+"&mae="+mae+"&cidade="+cidade+
	"&numero_fam="+numero_fam+"&codigo_ficha_familia="+codigo_ficha_familia;
	ajax_tudo(url,listPacientes);
	//alert(numero_fam);
}
function deletaPac(usu_codigo,codigo_fam)
{
	url = "deletaPac.php?usu_codigo="+usu_codigo+"&codigo_fam="+codigo_fam;
	ajax_tudo(url,listPacientesDel)
}
function listPacientesDel()
{
	var numero_fam = document.getElementById('numero_fam').value;
	var codigo_ficha_familia = document.getElementById('codigo_ficha_familia').value;
	url = "listar.php?codigo_ficha_familia="+codigo_ficha_familia+"&numero_fam="+numero_fam;
	ajax_tudo(url,deletaPacientes)
}
function deletaPacientes(txt)
{
//alert(txt);
	//alert(txt);
	document.getElementById('minhaDiv').innerHTML = txt;

}
function listPacientes()
{	
	//alert(txt);
	var numero_fam = document.getElementById('numero_fam').value;
	var codigo_ficha_familia = document.getElementById('codigo_ficha_familia').value;
	url = "listar.php?codigo_ficha_familia="+codigo_ficha_familia+"&numero_fam="+numero_fam;
	document.getElementById('botaoenviar').style.display = "none";
	ajax_tudo(url,AparecerDiv)
}

function incluiPacientes(txt)
{
	document.getElementById('minhaDiv').innerHTML = txt;

}

function editaPrincipal(event)
{
	var codigo_ficha_familia = document.getElementById('codigo_ficha_familia').value;
	var tipo_logradouro_fam = document.getElementById('tipo_logradouro_fam').value;
	var ativo_fam = document.getElementById('ativo_fam').value;
	var endereco_fam = document.getElementById('endereco_fam').value;
	var numero_fam = document.getElementById('numero_fam').value;
	var bairro_fam = document.getElementById('bairro_fam').value;
	var cep_fam = document.getElementById('cep_fam').value;
	var municipio_fam = document.getElementById('municipio_fam').value;
	var segmento = document.getElementById('segmento').value;
	var area_fam = document.getElementById('area_fam').value;
	var micro_area_fam = document.getElementById('micro_area_fam').value;
	var tipo_logradouro_fam = document.getElementById('tipo_logradouro_fam').value;
	var psf_dia = document.getElementById('psf_dia').value;
	var psf_mes = document.getElementById('psf_mes').value;
	var psf_ano = document.getElementById('psf_ano').value;
	document.getElementById('botaoenviar').style.display = "none";
	

	if(codigo_ficha_familia == "")
	{
	alert('Preencha o campo Nr.Ficha');
	} if(tipo_logradouro_fam == ""){
	alert('Preencha o campo tipo Logradouro');
	} if(ativo_fam == ""){
	alert('Preencha o campo ativo');
	} if(endereco_fam == ""){
	alert('Preencha o campo endereco');
	} if(numero_fam == ""){
	alert('Preencha o campo numero');
	} if(bairro_fam == ""){
	alert('Preencha o campo Bairro');
	} if(cep_fam == ""){
	alert('Preencha o campo cep');
	} if(municipio_fam == ""){
	alert('Preencha o campo municipio');
	} if(segmento == ""){
	alert('Preencha o campo segmento');
	} if(area_fam == ""){
	alert('Preencha o campo area');
	} if(micro_area_fam == ""){
	alert('Preencha o campo area');
	}else{
	
	url = "editaBd.php?codigo_ficha_familia="+codigo_ficha_familia+"&tipo_logradouro_fam="+tipo_logradouro_fam+"&ativo_fam="+ativo_fam+
	"&endereco_fam="+endereco_fam+"&numero_fam="+numero_fam+"&bairro_fam="+bairro_fam+"&cep_fam="+cep_fam+
	"&municipio_fam="+municipio_fam+"&segmento="+segmento+"&area_fam="+area_fam+"&micro_area_fam="+micro_area_fam+"&tipo_logradouro_fam="+tipo_logradouro_fam+
	"&psf_dia="+psf_dia+"&psf_mes="+psf_mes+"&psf_ano="+psf_ano;
	ajax_tudo(url, listPacientes);
	}

}


function AparecerDiv(txt)
{   
	//alert(txt);
	document.getElementById('minhaDiv').innerHTML = txt;
	if(txt == 1)
	{
	alert("Ja existe um domicilio com esse numero de ficha!");	
	}else if(txt == 2){
	alert("Ja existe um domicilio com esse numero de residencia!");
	}else{
	document.getElementById('manipulada').style.display = "block";
	}
}

function alteraDados()
{
	var codigo_ficha_familia = document.getElementById('codigo_ficha_familia').value;
	var tipo_logradouro_fam = document.getElementById('tipo_logradouro_fam').value;
	var ativo_fam = document.getElementById('ativo_fam').value;
	var endereco_fam = document.getElementById('endereco_fam').value;
	var numero_fam = document.getElementById('numero_fam').value;
	var bairro_fam = document.getElementById('bairro_fam').value;
	var cep_fam = document.getElementById('cep_fam').value;
	var municipio_fam = document.getElementById('municipio_fam').value;
	var segmento = document.getElementById('segmento').value;
	var area_fam = document.getElementById('area_fam').value;
	var micro_area_fam = document.getElementById('micro_area_fam').value;
	var tipo_logradouro_fam = document.getElementById('tipo_logradouro_fam').value;
	var psf_dia = document.getElementById('psf_dia').value;
	var psf_mes = document.getElementById('psf_mes').value;
	var psf_ano = document.getElementById('psf_ano').value;
	
	var tipo_casa_fam = document.getElementById('tipo_casa_fam').value;
	var destino_lixo_fam = document.getElementById('destino_lixo_fam').value;
	var tratamento_agua_fam = document.getElementById('tratamento_agua_fam').value;
	var destino_fezes_fam = document.getElementById('destino_fezes_fam').value;
	var abastecimento_agua_fam = document.getElementById('abastecimento_agua_fam').value;
	var plano_fam = document.getElementById('plano_fam').value;
	var plano_nome_fam = document.getElementById('plano_nome_fam').value;
	var procura_unidade_fam = document.getElementById('procura_unidade_fam').value;
	var comunicacao_meios_fam = document.getElementById('comunicacao_meios_fam').value;
	var grupo_fam = document.getElementById('grupo_fam').value;
	var transporte_meios_fam = document.getElementById('transporte_meios_fam').value;
	var tipo_forro_fam = document.getElementById('tipo_forro_fam').value;
	var tipo_cobertura_fam = document.getElementById('tipo_cobertura_fam').value;
	var tipo_piso_fam = document.getElementById('tipo_piso_fam').value;
	var renda_fam = document.getElementById('renda_fam').value;	
	var animais_fam = document.getElementById('animais_fam').value;
	var qnt_animais_fam = document.getElementById('qnt_animais_fam').value;
	var cond_criacao_fam = document.getElementById('cond_criacao_fam').value;
	var qnt_comodos_fam = document.getElementById('qnt_comodos_fam').value;
	var conservacao_dom_fam = document.getElementById('conservacao_dom_fam').value;
	var energia_fam = document.getElementById('energia_fam').value;	
	var bolsa_fam = document.getElementById('bolsa_fam').value;
	var codigo_ficha_familia = document.getElementById('codigo_ficha_familia').value;
	var numero_fam = document.getElementById('numero_fam').value;
	
	url = "salvaTudo.php?tipo_casa_fam="+tipo_casa_fam+"&destino_lixo_fam="+destino_lixo_fam+"&tratamento_agua_fam="+tratamento_agua_fam+"&destino_fezes_fam="+destino_fezes_fam+"&abastecimento_agua_fam="+abastecimento_agua_fam+"&plano_fam="+plano_fam+"&plano_nome_fam="+plano_nome_fam+"&procura_unidade_fam="+procura_unidade_fam+"&comunicacao_meios_fam="+comunicacao_meios_fam+"&grupo_fam="+grupo_fam+"&transporte_meios_fam="+transporte_meios_fam+"&tipo_forro_fam="+tipo_forro_fam+"&tipo_cobertura_fam="+tipo_cobertura_fam+"&tipo_piso_fam="+tipo_piso_fam+"&renda_fam="+renda_fam+"&animais_fam="+animais_fam+"&qnt_animais_fam="+qnt_animais_fam+"&cond_criacao_fam="+cond_criacao_fam+"&qnt_comodos_fam="+qnt_comodos_fam+"&conservacao_dom_fam="+conservacao_dom_fam+"&energia_fam="+energia_fam+"&bolsa_fam="+bolsa_fam+"&numero_fam="+numero_fam+"&codigo_ficha_familia="+codigo_ficha_familia+"&codigo_ficha_familia="+codigo_ficha_familia+"&tipo_logradouro_fam="+tipo_logradouro_fam+"&ativo_fam="+ativo_fam+
	"&endereco_fam="+endereco_fam+"&numero_fam="+numero_fam+"&bairro_fam="+bairro_fam+"&cep_fam="+cep_fam+
	"&municipio_fam="+municipio_fam+"&segmento="+segmento+"&area_fam="+area_fam+"&micro_area_fam="+micro_area_fam+"&tipo_logradouro_fam="+tipo_logradouro_fam+
	"&psf_dia="+psf_dia+"&psf_mes="+psf_mes+"&psf_ano="+psf_ano;
	
	ajax_tudo(url,sucesso);
}

function sucesso(txt)
{
	 window.location = "psf.php";
	
}
function doencaEdit(usu_codigo){
	url = "doencaEdit.php?usu_codigo="+usu_codigo;
	window.open(url, null, "height=600,width=800,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes"); 
}
function doenca(usu_codigo){
	url = "doenca.php?usu_codigo="+usu_codigo;
	window.open(url, null, "height=600,width=800,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes"); 
}
function salvaDoenca(usu_codigo,selecionadas,gestante){
	//alert(selecionadas);
	var codigo_ficha_familia = document.getElementById('codigo_ficha_familia').value;
	var numero_fam = document.getElementById('numero_fam').value;
	url = "alteraDoenca.php?numero_fam="+numero_fam+"&codigo_ficha_familia="+codigo_ficha_familia+"&usu_codigo="+usu_codigo+"&selecionadas="+selecionadas+"&gestante="+gestante;
	//ajax_tudo(url,concluiDoenca);
	ajax_tudo(url,listPacientes);
}
function editaDoenca (usu_codigo,selecionadas,gestante){
	var codigo_ficha_familia = document.getElementById('codigo_ficha_familia').value;
	var numero_fam = document.getElementById('numero_fam').value;
	url = "editaCheck.php?numero_fam="+numero_fam+"&codigo_ficha_familia="+codigo_ficha_familia+"&usu_codigo="+usu_codigo+"&selecionadas="+selecionadas+"&gestante="+gestante;
	//ajax_tudo(url,concluiDoenca);
	ajax_tudo(url,listPacientes);
}
function concluiDoenca(txt){
	//alert(txt);
	
}
function buscarMicroArea(){
	area_fam = document.getElementById('area_fam').value;
	url = "buscarMicroarea.php?area_fam="+area_fam;
	ajax_tudo(url,popularMicro);
}
function popularMicro(txt){
	d = document.getElementById('micro_area_fam');
	d.innerHTML = "";
	d.options[0]=new Option("...","");
	r =txt;
	res = r.split(";");
	for(x = 0; x < res.length; x++)
	{
		aux = res[x].split("-");
		if(aux[1] != undefined)
		{
			d.options[d.options.length]=new Option(aux[1],aux[0]);
		}
	}	
}
function buscarMicroArea(){
	area_fam = document.getElementById('area_fam').value;
	url = "buscarMicroarea.php?area_fam="+area_fam;
	ajax_tudo(url,popularMicro);
}
function popularMicro(txt){
	d = document.getElementById('micro_area_fam');
	d.innerHTML = "";
	d.options[0]=new Option("...","");
	r =txt;
	res = r.split(";");
	for(x = 0; x < res.length; x++)
	{
		aux = res[x].split("-");
		if(aux[1] != undefined)
		{
			d.options[d.options.length]=new Option(aux[1],aux[0]);
		}
	}	
}
</script>
<?
session_start();
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
verauth($id_login);
include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
cabecario();
include_once $_SESSION[root].$_SESSION[modulo]."funcao.calendario.php";

$codigo_ficha_familia_edit = $_GET['codigo_ficha_familia'];	
//echo $codigo_ficha_familia_edit;
$tudo = "select * from psf where codigo_ficha_familia = '$codigo_ficha_familia'";
$qryTudo = pg_query($tudo);
$linhaTudo = pg_fetch_array($qryTudo);	   
echo"

<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	<tr>
		<td>
		<fieldset>
		<legend>Cadastro</legend>
			<table width=100% align=center cellspacing=3 cellpadding=0 border=0>
				<tr>
					<td align='center' class='corColuna'>
						<b>FICHA A</b>
					</td>
				<td align='center' class='corColuna'>
					<h3> SECRETARIA MUNICIPAL DE SA&Uacute;DE </h3>
				</td>
				<td align='center' class='corColuna'>
					<b>PR</b>
				</td>
			</tr>
			</table>
			</fieldset>
		</td>
	</tr>
";
///////////////////////////////////////////////Parte de baixo////////////////////////////////////////////////
echo "
	<tr>
		<td>
		<fieldset>
			<table width='100%' align='center' border='0'>
				<tr>
					<td>
						<b>Nr.Ficha:<br/>
						<input type='text' name='codigo_ficha_familia' id='codigo_ficha_familia' class='boxNumero' value='{$linhaTudo['codigo_ficha_familia']}'>
					</td>
					<td>
						<b>Tipo de Lodradouro:<br;>
						<select name='tipo_logradouro_fam' id='tipo_logradouro_fam' class='boxTexto'>
							<option> </option>
							<option value='rua'".($linhaTudo['tipo_logradouro_fam'] == 'rua' ? "selected='selected'": '').">Rua</option>
							<option value='alameda'".($linhaTudo['tipo_logradouro_fam'] == 'alameda' ? "selected='selected'": '').">Alameda</option>
							<option value='travessia'".($linhaTudo['tipo_logradouro_fam'] == 'travessia' ? "selected='selected'": '').">Travessia</option>
							<option value='avenida'".($linhaTudo['tipo_logradouro_fam'] == 'avenida' ? "selected='selected'": '').">Avenida</option>
							<option value='estrada'".($linhaTudo['tipo_logradouro_fam'] == 'estrada' ? "selected='selected'": '').">Estrada</option>
						</select>
					</td>
					<td colspan='2'>
						<b>	Ativo:<br/>
						<input type='radio' name='ativo_fam' id='ativo_fam' value='sim'".($linhaTudo['ativo_fam'] == 'sim' ? "checked": '').">Sim
						<input type='radio' name='ativo_fam' id='ativo_fam' value='nao'".($linhaTudo['ativo_fam'] == 'nao' ? "checked": '').">N&atilde;o
					</td>
				</tr>
				<tr>";
					echo"
				<td width='350'><b>Endereco:<br/>
					<input type=hidden name='end_codigo' id='end_codigo' size=10 >
					<input name='endereco_fam' id='endereco_fam'  type='text' size='50' class='boxTexto' value='{$linhaTudo['endereco_fam']}'>
					<span onclick=\"buscar_endereco(\$F('endereco_fam'), 'buscar_endereco');return false;\"><img border='0' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/buscar4_on.jpg' style='vertical-align:bottom; cursor:pointer'></span>";
					echo divBuscaEndereco();	
				echo"
		  		</td>";
					echo"
					<td width='4px'>
						<b>N&uacute;mero:<br/>
						<input type='text' class='boxNumero' name=numero_fam id='numero_fam' value='{$linhaTudo['numero_fam']}'>
					</td>
					<td width='4px'>
						<b>Bairro:<br/>
						<input type='text' class='boxTexto' name=bairro_fam id='bairro_fam' value='{$linhaTudo['bairro_fam']}'>
					</td>
					<td>
						<b>CEP:<br/>
						<input type='text' class='boxTexto' name=cep_fam id='cep_fam' value='{$linhaTudo['cep_fam']}'>
					</td>
				</tr>
				<tr>
					<td>
						<b>Munic&iacute;pio:<br/>
						<input type='text' class='boxTexto' name=municipio_fam id='municipio_fam' value='{$linhaTudo['municipio_fam']}'>
					</td>
					<td>
						<b>Segmento:
						<input type='text' class='boxData' name=segmento id='segmento' value='{$linhaTudo['segmento']}'>
					</td>
					";
					echo"
					<td>
						<b>&Aacute;rea:<br/>
						<select name='area_fam' id='area_fam' class='boxNumero' onchange=\" buscarMicroArea()\">
							<option>SELECIONE</option>'";
								$sqlArea = "select * from area";
								$qryArea = pg_query($sqlArea);
								while($linhas = pg_fetch_array($qryArea)){
									echo "<option value='$linhas[area_codigo]'>$linhas[area_desc]</option>";
								}
							echo"</select>
					</td>";
					
					echo"
					<td>
						<b>Micro-&Aacute;rea:<br/>
						<select name='micro_area_fam' id='micro_area_fam' class='boxNumero'>
							<option>...</option>";
					echo"
						</select>
					</td>
					</tr>
						<tr>
					<td>
						<b>Tel:<br/>
						<input type='text' class='boxData' name='ddd' id='ddd' value='$ddd'> -
						<input type='text' class='boxNumero' name='tel' id='tel' value='$tel'>
					</td>
					<td colspan='3'>
						<b>Data Cadastro:<br/>";
						campoData('psf_dia','psf_mes','psf_ano');	
						echo"  </td>
				</tr>
			</table>
			</fieldset>
			</td>
		</tr>";


echo "
</table><br>
";
//////////////////////////////////////////////////////////////////////////////////////////////////////BOTAOOO ENVIAR
echo "
	<span onClick=\"listPacientes();\"style='cursor: pointer;' id='botaoenviar'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/enviar_dados_on.jpg' /></span>
";
echo "<div id='manipulada' style='display:none'>";




///////////////////////////////////////////////////Fim//////////////////////////////////////////////////////
$bdr = "style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'";
$bdr2 = "style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'";
echo "

	<div id='tabnav'>
		<div id='tabela'>    	
			<div id='tabs'>
				<a href='#' onclick='opentab(1);'>Fam&iacute;lia</a>
				<a href='#' onclick='opentab(2);'>Moradia</a>
				<a href='#' onclick='opentab(3);'>Gerais</a> 
				<a href='#' onclick='opentab(4);'>Complementares</a>           
			</div>
		</div>
		<div id='content'>
			<div> 
				<table>
					<tr>
						<td>
							<div id='minhaDiv'>
							
							</div>
								<span style='cursor: pointer;' id='botaoenviar' onclick='adicionaPac()'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg border=0></span>
						</td>
					</tr>
				</table>
			</div>";
////////////////////////////////////////////////fim da aba familia///////////////////////////////////	
echo"
	<div>
		<table border='0' width='98%'>
			<tr>
				<td width='80px'>
					<b>Tipo de Casa:
				</td>
				<td>
					<select name='tipo_casa_fam' id='tipo_casa_fam' class='box'>
						<option> </option>
						<option value='tijolo'".($linhaTudo['tipo_casa_fam'] == 'tijolo' ? "selected='selected'": '')."> Tijolo/Adobe </option>
						<option value='taipa'".($linhaTudo['tipo_casa_fam'] == 'taipa' ? "selected='selected'": '')."> Taipa Revestida </option>
						<option value='taipan'".($linhaTudo['tipo_casa_fam'] == 'taipan' ? "selected='selected'": '')."> Taipa n&atilde;o Revestida </option>
						<option value='materialap".($linhaTudo['tipo_casa_fam'] == 'materialap' ? "selected='selected'": '')."'> Material Aproveitado </option>
						<option value='madeira'".($linhaTudo['tipo_casa_fam'] == 'madeira' ? "selected='selected'": '')."> Madeira </option>
					</select>
				</td>
				<td width='100px'>
					<b>Destino do Lixo:
				</td>
				<td>
					<select name='destino_lixo_fam' id='destino_lixo_fam' class='box'>
						<option> </option>
						<option value='coletado'".($linhaTudo['destino_lixo_fam'] == 'coletado' ? "selected='selected'": '').">Coletado</option>
						<option value='queimado".($linhaTudo['destino_lixo_fam'] == 'queimado' ? "selected='selected'": '')."'>Queimado/Enterrado</option>
						<option value='ceu'".($linhaTudo['destino_lixo_fam'] == 'ceu' ? "selected='selected'": '').">C&eacute;u Aberto</option>
						</select>
					</td>
				<td width='150px'>
					<b>Tratamento de &Agrave;gua:
				</td>
				<td>
					<select name='tratamento_agua_fam' id='tratamento_agua_fam' class='box'>
						<option> </option>
						<option value='filtrada'".($linhaTudo['tratamento_agua_fam'] == 'filtrada' ? "selected='selected'": '').">Filtrada</option>
						<option value='fervida'".($linhaTudo['tratamento_agua_fam'] == 'fervida' ? "selected='selected'": '').">Fervida</option>
						<option value='clorada'".($linhaTudo['tratamento_agua_fam'] == 'clorada' ? "selected='selected'": '').">Clorada</option>
						<option value='semtr'".($linhaTudo['tratamento_agua_fam'] == 'semtr' ? "selected='selected'": '').">Sem Tratamento</option>
					</select>
				</td>
			</tr>
			<tr>
				<td width='150px'>
					<b>Abastecimento de &Agrave;gua:
				</td>
				<td>
					<select name='abastecimento_agua_fam' id='abastecimento_agua_fam' class='box'>
						<option> </option>
						<option value='rede'".($linhaTudo['abastecimento_agua_fam'] == 'rede' ? "selected='selected'": '').">Rede Publica</option>
						<option value='poco'".($linhaTudo['abastecimento_agua_fam'] == 'poco' ? "selected='selected'": '').">Po&ccedil;o ou nascente</option>
					</select>
				</td>
				<td width='150px'>
					<b>Destino de Fezes e Urina:
				</td>
				<td>
					<select name='destino_fezes_fam' id='destino_fezes_fam' class='box'>
						<option> </option>
						<option value='fossa'".($linhaTudo['destino_fezes_fam'] == 'fossa' ? "selected='selected'": '').">Fossa</option>
						<option value='esgoto'".($linhaTudo['destino_fezes_fam'] == 'esgoto' ? "selected='selected'": '').">Esgoto</option>
					</select>
				</td>	
				<td>
					<b>Energia Eletrica:
				</td>
				<td>
					<select name='energia_fam' id='energia_fam' class='box'>
						<option> </option>
						<option value='sim'".($linhaTudo['energia_fam'] == 'sim' ? "selected='selected'": '').">Sim</option>
						<option value='nao'".($linhaTudo['energia_fam'] == 'nao' ? "selected='selected'": '').">N&atilde;o</option>
				</td>
			</tr>
			<tr>
				<td>
					<b>Estado Conserva&ccedil;&atilde;o:
				</td>
				<td>
					<select name='conservacao_dom_fam' id='conservacao_dom_fam' class='box'>
						<option></option>
						<option value='pessima'".($linhaTudo['conservacao_dom_fam'] == 'pessima' ? "selected='selected'": '').">Pessima</option>
						<option value='ruim'".($linhaTudo['conservacao_dom_fam'] == 'ruim' ? "selected='selected'": '').">Ruim</option>
						<option value='regular'".($linhaTudo['conservacao_dom_fam'] == 'regular' ? "selected='selected'": '').">Regular</option>
						<option value='boa'".($linhaTudo['conservacao_dom_fam'] == 'boa' ? "selected='selected'": '').">Boa</option>
						<option value='otima'".($linhaTudo['conservacao_dom_fam'] == 'otima' ? "selected='selected'": '').">Otima</option>
					</select>
				</td>
				<td>
					<b>Quantidade de Comodos:
				</td>
				<td>
					<input type='text' name='qnt_comodos_fam' id='qnt_comodos_fam' class='boxData' value='{$linhaTudo['qnt_comodos_fam']}'>
				</td>
				<td>
					<b>Tipo de Cobertura:
				</td>
				<td>
					<select name='tipo_cobertura_fam' id='tipo_cobertura_fam' class='box'>
						<option></option>
						<option value='telhado'".($linhaTudo['tipo_cobertura_fam'] == 'telhado' ? "selected='selected'": '').">Telhado</option>
						<option value='madeira'".($linhaTudo['tipo_cobertura_fam'] == 'madeira' ? "selected='selected'": '').">Madeira</option>
						<option value='vegetal'".($linhaTudo['tipo_cobertura_fam'] == 'vegetal' ? "selected='selected'": '').">Vegetal</option>
						<option value='outros'".($linhaTudo['tipo_cobertura_fam'] == 'outros' ? "selected='selected'": '').">Outros</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<b>Tipo de Forro:
				</td>
				<td>
					<select name='tipo_forro_fam' id='tipo_forro_fam' class='box'>
						<option></option>
						<option value='gesso'".($linhaTudo['tipo_forro_fam'] == 'gesso' ? "selected='selected'": '').">Gesso</option>
						<option value='madeira'".($linhaTudo['tipo_forro_fam'] == 'madeira' ? "selected='selected'": '').">Madeira</option>
						<option value='lage'".($linhaTudo['tipo_forro_fam'] == 'lage' ? "selected='selected'": '').">Lage</option>
						<option value='outros'".($linhaTudo['tipo_forro_fam'] == 'outros' ? "selected='selected'": '').">Outros</option>
						<option value='semforro'".($linhaTudo['tipo_forro_fam'] == 'semforro' ? "selected='selected'": '').">Sem Forro</option>
					</select>
				</td>
				<td>
					<b>Tipo de Piso:
				</td>
				<td>
					<select name='tipo_piso_fam' id='tipo_piso_fam' class='box'>
						<option></option>
						<option value='azulejo'".($linhaTudo['tipo_piso_fam'] == 'azulejo' ? "selected='selected'": '').">Azulejo</option>
						<option value='tacos'".($linhaTudo['tipo_piso_fam'] == 'tacos' ? "selected='selected'": '').">Tacos</option>
						<option value='outros'".($linhaTudo['tipo_piso_fam'] == 'outros' ? "selected='selected'": '').">Outros</option>
						<option value='sempiso'".($linhaTudo['tipo_piso_fam'] == 'sempiso' ? "selected='selected'": '').">Sem Piso</option>
					</select>
				</td>
			</tr>
		</table>
	</div>";
////////////////////////////////////////////////fim da aba Moradia/////////////////////////////////////////////

echo "
	<div>
		<table border='0'>
			<tr>
				<td width='155px'>
					<b>Possui plano de Sa&uacute;de:
				</td>
				<td>
					<input type='radio' name='plano_fam' id='plano_fam' value='SIM'".($linhaTudo['plano_fam'] == 'SIM' ? "checked": '').">Sim
					<input type='radio' name='plano_fam' id='plano_fam' value='SIM'".($linhaTudo['plano_fam'] == 'SIM' ? "checked": '').">N&atilde;o
				</td>
				<td width='155px'>
					<b>Nome do Plano de Sa&uacute;de:
				</td>
				<td>
					<input type='text' class='boxTexto' name='plano_nome_fam' id='plano_nome_fam' value='{$linhaTudo['plano_nome_fam']}'>
				</td>
			</tr>
			<tr>
				<td width='175px'>
					<b>Em Caso de Doen&ccedil;a Procura:
				</td>
				<td>
					<select name='procura_unidade_fam' id='procura_unidade_fam' class='box'>
						<option> </option>
						<option value='hospital'".($linhaTudo['procura_unidade_fam'] == 'hospital' ? "selected='selected'": '').">Hospital</option>
						<option value='benzedeira'".($linhaTudo['procura_unidade_fam'] == 'benzedeira' ? "selected='selected'": '').">Benzedeira</option>
						<option value='farmacia'".($linhaTudo['procura_unidade_fam'] == 'farmacia' ? "selected='selected'": '').">Farm&aacute;cia</option>
						<option value='unidade'".($linhaTudo['procura_unidade_fam'] == 'unidade' ? "selected='selected'": '').">Unidade De Sa&uacute;de</option>
					</select>
				</td>
				<td width='175px'>
					<b>Meios de Comunica&ccedil;o:
				</td>
				<td>
					<select name='comunicacao_meios_fam' id='comunicacao_meios_fam' class='box'>
						<option> </option>
						<option value='radio'".($linhaTudo['comunicacao_meios_fam'] == 'radio' ? "selected='selected'": '').">R&aacute;dio</option>
						<option value='televisao'".($linhaTudo['comunicacao_meios_fam'] == 'televisao' ? "selected='selected'": '').">Televis&atilde;o</option>
						<option value='computador'".($linhaTudo['comunicacao_meios_fam'] == 'computador' ? "selected='selected'": '').">Computador</option>
						<option value='outroscom'".($linhaTudo['comunicacao_meios_fam'] == 'outroscom' ? "selected='selected'": '').">Outros</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<b>Grupos Comunit&aacute;rios:
				</td>
				<td>
					<select name='grupo_fam' id='grupo_fam' class='box'>
						<option> </option>
						<option value='cooperativa'".($linhaTudo['grupo_fam'] == 'cooperativa' ? "selected='selected'": '').">Cooperativa</option>
						<option value='grupo_religioso'".($linhaTudo['grupo_fam'] == 'grupo_religioso' ? "selected='selected'": '').">Grupo Religioso</option>
						<option value='associacao'".($linhaTudo['grupo_fam'] == 'associacao' ? "selected='selected'": '').">Associa&ccedil;&atilde;o</option>
						<option value='outros'".($linhaTudo['grupo_fam'] == 'outros' ? "selected='selected'": '').">Outros</option>
					</select>
				</td>
				<td>
					<b>Transporte:
				</td>
				<td>
					<select name='transporte_meios_fam' id='transporte_meios_fam' class='box'>
						<option> </option>
						<option value='onibus'".($linhaTudo['transporte_meios_fam'] == 'onibus' ? "selected='selected'": '').">&Ocirc;nibus</option>
						<option value='caminhao'".($linhaTudo['transporte_meios_fam'] == 'caminhao' ? "selected='selected'": '').">Caminh&atilde;o</option>
						<option value='carro'".($linhaTudo['transporte_meios_fam'] == 'carro' ? "selected='selected'": '').">Carro</option>
						<option value='carroca'".($linhaTudo['transporte_meios_fam'] == 'carroca' ? "selected='selected'": '').">Carro&ccedil;a</option>
						<option value='outros'".($linhaTudo['transporte_meios_fam'] == 'outros' ? "selected='selected'": '').">Outros</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<b>Renda Familiar
				</td>
				<td>
					<select name='renda_fam' id='renda_fam' class='box'>
						<option></option>
						<option value='nenhuma'".($linhaTudo['renda_fam'] == 'nenhuma' ? "selected='selected'": '').">Nenhuma Renda</option>
						<option value='100'".($linhaTudo['renda_fam'] == '100' ? "selected='selected'": '').">100 &agrave; 500</option>
						<option value='500'".($linhaTudo['renda_fam'] == '500' ? "selected='selected'": '').">500 &agrave; 800</option>
						<option value='800".($linhaTudo['renda_fam'] == '800' ? "selected='selected'": '').">800 &agrave; 1200</option>
						<option value='1200'".($linhaTudo['renda_fam'] == '1200' ? "selected='selected'": '').">1200 &agrave; 2000</option>
						<option value='2000'".($linhaTudo['renda_fam'] == '2000' ? "selected='selected'": '').">2000 &agrave; 2500</option>
						<option value='2500'".($linhaTudo['renda_fam'] == '2500' ? "selected='selected'": '').">2500 &agrave; 3500</option>
						<option value='mais'".($linhaTudo['renda_fam'] == 'mais' ? "selected='selected'": '').">Mais</option>
					</select>
				</td>
			</tr>	
			<tr>
				<td>
					<b>Bolsa:
				</td>
				<td>
					<select name='bolsa_fam' id='bolsa_fam' class='box'>
						<option></option>
						<option value='nenhuma_bolsa'".($linhaTudo['bolsa_fam'] == 'nenhuma_bolsa' ? "selected='selected'": '').">Nenhuma Bolsa</option>
						<option value='bolsa_familia'".($linhaTudo['bolsa_fam'] == 'bolsa_familia' ? "selected='selected'": '').">Bolsa Fam&iacute;lia</option>
						<option value='bolsa_alimentacao".($linhaTudo['bolsa_fam'] == 'bolsa_alimentacao' ? "selected='selected'": '')."'>Bolsa Alimenta&ccedil;&atilde;o</option>
						<option value='outras'".($linhaTudo['bolsa_fam'] == 'outras' ? "selected='selected'": '').">Outras</option>
					</select>
				</td>
			</tr>
		</table> 
	</div>";
///////////////////////////////////////////////fim da aba Gerais///////////////////////////////////////		
echo"
	<div>  
		<table>
			<tr>
				<td>
					<b>Cria Animais:
				</td>
				<td>
					<select name='animais_fam' id='animais_fam' class='box'>
						<option></option>
						<option value='sim'".($linhaTudo['animais_fam'] == 'sim' ? "selected='selected'": '').">Sim</option>
						<option value='nao'".($linhaTudo['animais_fam'] == 'nao' ? "selected='selected'": '').">N&atilde;o</option>
					</select>
				</td>
				<td>
					<b>Quantidade Animais:
				</td>
				<td>
					<select name='qnt_animais_fam' id='qnt_animais_fam' class='box'>
						<option></option>
						<option value='nenhum'".($linhaTudo['qnt_animais_fam'] == 'nenhum' ? "selected='selected'": '').">Nenhum</option>
						<option value='1".($linhaTudo['qnt_animais_fam'] == '1' ? "selected='selected'": '')."'>1 &agrave; 3</option>
						<option value='3'".($linhaTudo['qnt_animais_fam'] == '3' ? "selected='selected'": '').">3 &agrave; 5</option>
						<option value='mais'".($linhaTudo['qnt_animais_fam'] == 'mais' ? "selected='selected'": '').">Mais</option>
					</select>
				</td>
				<td>
					<b>Condi&ccedil;ao de Cria&ccedil;&atilde;o:
				</td>
				<td>
					<select name='cond_criacao_fam' id='cond_criacao_fam' class='box'>
						<option></option>
						<option value='pessima'".($linhaTudo['cond_criacao_fam'] == 'pessima' ? "selected='selected'": '').">Pessima</option>
						<option value='ruim'".($linhaTudo['cond_criacao_fam'] == 'ruim' ? "selected='selected'": '').">Ruim</option>
						<option value='regular'".($linhaTudo['cond_criacao_fam'] == 'regular' ? "selected='selected'": '').">Regular</option>
						<option value='boa'".($linhaTudo['cond_criacao_fam'] == 'boa' ? "selected='selected'": '').">Boa</option>
						<option value='otima'".($linhaTudo['cond_criacao_fam'] == 'otima' ? "selected='selected'": '').">Otima</option>
					</select>
				</td>
			</tr>
				
		</table>
	</div>";
echo "</div>"; // fim da content

///////////////////////////////////////////////fim da aba complementares///////////////////////////////	  
echo "
</div>"; // tab nav OK



echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";


echo "<span onClick=\"alteraDados();\"style='cursor: pointer;' id='botaoenviar'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/finalizar_on.jpg' /></span>";
echo "</div>";

?>
