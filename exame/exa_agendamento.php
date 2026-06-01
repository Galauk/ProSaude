<?
	session_start();
	include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
	#verauth($id_login);
	
	include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();
?>
<script language="javascript">
function showdiv(ativar,divA) { 
  var A=document.getElementById(divA);
 if(ativar=='sim') {  
   A.style.display = 'block';
 } else {
   A.style.display= 'none';
 }
}
</script>
<script type="text/Javascript">
function send(action)
{
	switch(action) {
		case 'continuar':
			if(document.form_msg.vlr_mensal.value=='') {
				alert('Preencha o Valor Mensal');
				return false;
			}
			if(document.form_msg.quota_diaria.value=='0') {
				alert('Seleciona Quota Diaria Maior Que 0');
				return false;
			}

			var DestL = $('dest_list');
			if( DestL.length == 0 || DestL.options[0].value == '0' ){
				alert( 'Escolha pelo menos 1 Procedimento !' );
				DestL.focus();
				return false;
			}


    DestL.multiple = true;
    for( var i = 0; i < DestL.length; i++ )
        DestL.options[ i ].selected = true;

                        url = 'exa_lab_valor.php?acao=continua';
                        break;
                case 'continuar_editar':
                          if(document.form_msg.vlr_mensal.value=='') {
                             alert('Preencha o Valor Mensal');
                            return false;
                          }
                          if(document.form_msg.quota_diaria.value=='0') {
                             alert('Seleciona Quota Diaria Maior Que 0');
                            return false;
                          }

var DestL = $('dest_list');
   if( DestL.length == 0 || DestL.options[0].value == '0' )
    {
        alert( 'Escolha pelo menos 1 Procedimento !' );
        DestL.focus();
        return false;
    }


    DestL.multiple = true;
    for( var i = 0; i < DestL.length; i++ )
        DestL.options[ i ].selected = true;

                        url = 'exa_lab_valor.php?acao=continuar_editar';
                        break;
                case 'addperiodo':
                          if(document.form_msg.h_med_codigo.value=='') {
                             alert('Selecione o Laboratorio');
                            return false;
                          }
                          if(document.form_msg.new_gex_periodo.value=='') {
                             alert('Preencha o Novo Periodo');
                            return false;
                          }
                        url = 'exa_lab_valor.php?acao=addperiodo';
                        break;
                case 'delperiodo':
                          if(document.form_msg.h_med_codigo.value=='') {
                             alert('Selecione o Laboratorio');
                            return false;
                          }
                          if(document.form_msg.h_gex_periodo.value=='') {
                             alert('Selecione o Periodo');
                            return false;
                          }
                        url = '../liberacao/exa_lab_valor.php?acao=delperiodo';
                        break;
        }


        document.form_msg.action = url;
        document.form_msg.submit();
}
function loadMedCod(event){
	//document.location.href = 'exa_agendamento.php?cod=' + document.form_msg.lab_codigo.value+'&uni_codigo=' + document.form_msg.uni_codigo.value+'lab_codigo=' + document.form_msg.lab_codigo.value;
	var medcod = document.form_msg.lab_codigo.value;
	//MEXER AQUI!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
	//chamar um arq externo passando o medcod e retornando valor de quota_qtde.
	url = "buscaCampo.php?medcod="+medcod;
	ajax_tudo(url, preencheQuotaQtde);
//	document.form_msg.dest_list.clear;
/*	if(document.form_msg.dest_list[0].value != "0") {
		for(var i = 0; i < document.form_msg.dest_list.options.length; i++) {
			document.form_msg.dest_list.options[i] = null;
		}
	}
*/}
function preencheQuotaQtde(txt){
	document.getElementById('quota_qtde').value = txt;
}
</script>

        <style>
                .borda {
                        border-bottom: 1px solid;
                        border-top: 1px solid;
                        border-left: 1px solid;
                        border-right: 1px solid;
                        border-color: #cccccc;
                }
                .borda2 {
                        border-bottom: 1px solid;
                        border-top: 1px solid;
                        border-left: 1px solid;
                        border-right: 1px solid;
                        border-color: #909090;
                }
                .bordaN {
                        border-bottom: 1px solid;
                        border-right: 1px solid;
                        border-color: #909090;
                        background: #f9f9f9;
                        text-align: right;
                }
        </style>
<script language="JavaScript" src="<?=$_SESSION[linkroot].$_SESSION[comum]?>library/js/atalhos.js"></script>
<script language="JavaScript" type="text/javascript" src="<?=$_SESSION[linkroot].$_SESSION[comum]?>library/js/funcoes.js"></script>
<script language="JavaScript" type="text/javascript" src="../g_ajax.js"></script>
<script language="JavaScript" type="text/javascript" src="<?=$_SESSION[linkroot].$_SESSION[comum]?>library/js/ajax_motor.js"></script>
<script type="text/javascript" src="exa_agendamento.js.php?id_login=<?=$id_login?>"></script>
<script language="JavaScript" type="text/javascript" src="procedimento.js"></script>


<script>
shortcut.add("Right",function() 
{
     add_dest();
});

shortcut.add("Left",function() 
{
     rem_dest();
});

shortcut.add("F2",function() 
{
	if ($F('pac_nome')!= ''){
		buscar_nome($F('pac_nome'), 'buscar_nome');return false;link_f7();
	}else{
		buscar_nome($F('pac_nascimento'), 'buscar_data');return false;link_f7();
	}
});

shortcut.add("F4",function() 
{
 buscar_nome($F('pac_nascimento'), 'buscar_data');
});

shortcut.add("F8",function() 
{
 var pac_codigo = document.form_msg.pac_codigo.value;
  window.open("exa_historico.php?id_login=<?=$id_login?>&usu_codigo="+pac_codigo,null,"height=460,width=800,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");
});
shortcut.add("F9",function() 
{
  window.open("../paciente_ficha.php?acao=form_add&type=c&id_login=$id_login&controle=1",null,"height=460,width=800,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");
});
shortcut.add("F12",function() 
{
   document.form_msg.lab_codigo.focus();
});
shortcut.add("Ctrl+F12",function() 
{
	var unidade = document.getElementById("uni_codigo").value;
	function popularProcedimento(txt){
		d = document.getElementById('dest');
		d.innerHTML = "";
		r =txt;
		res = r.split(";");
		for(x = 0; x < res.length; x++){

			aux = res[x].split("-");
			if(aux[1] != undefined)
			{
				d.options[d.options.length]=new Option(aux[1],aux[0]);
			}
		}
	}
	
 if((document.form_msg.lab_codigo.value == "0" && document.form_msg.pac_codigo.value == "")) {
    document.getElementById("dados").style.display="none";
 } else {
    document.getElementById("dados").style.display="block";
    document.form_msg.dest.focus();
 }
	if(document.form_msg.dest_list[0].value != "0") {
		var uni_codigo = document.form_msg.uni_codigo.value;
		var usu_codigo = document.form_msg.pac_codigo.value;
		var med_codigo = document.form_msg.med_codigo.value;
		var esp_codigo = document.form_msg.esp_codigo.value;
		var lab_codigo = document.form_msg.lab_codigo.value;
		var id_login = document.form_msg.id_login.value;
		
		var DestL = $('dest_list');
			DestL.multiple = true;
		var proc = "";
		for( var i = 0; i < DestL.length; i++ ) {
			proc += DestL.options[i].value + ",";
		}
		quotaQtde = document.getElementById('quota_qtde').value;
		if(quotaQtde == 'S'){
			url = "exa_listadtagendamento.php?id_login=" + id_login + "&usu_codigo=" + usu_codigo + "&med_codigo=" + med_codigo + "&esp_codigo=" + esp_codigo + "&lab_codigo=" + lab_codigo + "&proc_codigo=" + proc + "&uni_codigo=" + uni_codigo;
//			window.open("exa_listadtagendamento.php?id_login=" + id_login + "&usu_codigo=" + usu_codigo + "&uni_codigo=" + uni_codigo + "&med_codigo=" + med_codigo + "&esp_codigo=" + esp_codigo + "&lab_codigo=" + lab_codigo + "&proc_codigo=" + proc,null,"height=460,width=300,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");
		}else {
			//AQUI ABERTURA DE TELA IMPRESSAO
			url = "../liberacao/valida_valor.php?id_login=" + id_login + "&usu_codigo=" + usu_codigo + "&lab_codigo=" + lab_codigo + "&proc_codigo=" + proc;
//			window.open("valida_valor.php?id_login=" + id_login + "&usu_codigo=" + usu_codigo + "&uni_codigo=" + uni_codigo +  "&lab_codigo=" + lab_codigo + "&proc_codigo=" + proc ,null,"height=460,width=300,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");
		}
		window.open(url,null,"height=460,width=300,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");
		DestL.innerHTML = "";
	}
	if (document.getElementById("dados").style.display == 'block'){
		var teste = document.form_msg.lab_codigo.value;
		e = document.getElementById('dest');
		e.innerHTML = "";
		e.options[0]=new Option("...","");
		url = "exa_busca.php?cod="+teste+"&uni_codigo="+unidade;
		ajax_tudo(url, popularProcedimento);
	}
	
});

</script>
<?
echo "<form method='post' action='$PHP_SELF' id='form_msg' name='form_msg'>";
	echo "<input type=hidden name=id_login value=$id_login>";
	echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
		<tr>
			<td>
				<fieldset>
					<legend>Medico Solicitante</legend>
					<table width=100% align=center cellspacing=3 cellpadding=0 border=0>
						<tr>
							<td width=75>Especialidade:</td>
							<td width=30>
								<select id='esp_codigo' class='boxa' onchange='at_medico()'>
								<option value='0'>....</option>";
								$stmt = "SELECT esp_codigo, esp_nome FROM especialidade ORDER BY esp_nome";
								$qry = db_query( $stmt );
								while( $esp = pg_fetch_array($qry) )
								{
								echo "\n\t\t\t<option value='{$esp[0]}'>{$esp[1]}</option>";
								}
								echo "</select>
							</td>
							<td width=3%>&nbsp;</td>
							<td width=45>Medico:</td>
							<td>
							<select id='med_codigo' class='boxa' disabled=\"disabled\" onchange=\"preferencia_di()\">
							<option value='0'>....</option>
							</select>
							</td>
						</tr>
					</table>
				</fieldset>
			</td>
		</tr>
	</table>";
	$sqlUni = "SELECT uni_codigo 
				 FROM usuarios 
				WHERE usr_codigo = $id_login";
	$row = pg_fetch_array(pg_query($sqlUni));
	if(!empty($row[0])) { 
		$wr = "where uni_codigo = $row[0]"; 
	}
	echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
		<tr>
			<td>
				<fieldset>
					<legend>Unidade de Origem do AGENDAMENTO</legend>
					<table width=100% align=center cellspacing=3 cellpadding=0 border=0>
						<tr>
							<td width=4%>Unidade:</td>
							<td>
								<select id='uni_codigo' class='boxa'>
									<option value='0'>....</option>";
									$stmt = "SELECT uni_codigo,
													uni_desc 
											   FROM unidade $wr 
											  ORDER BY uni_desc desc";
									$qry = db_query( $stmt );
									while( $agt = pg_fetch_array($qry) ){
										echo "\n\t\t\t<option value='{$agt[0]}'>{$agt[1]}</option>";
									}
								echo "</select>
							</td>
						</tr>
					</table>
	           </fieldset>
			</td>
		</tr>
	</table>";
	echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
		<tr>
			<td>
				<fieldset>
					<legend>Laboratorio de Atendimento</legend>
					<table width=100% align=center cellspacing=3 cellpadding=0 border=0>
						<tr>
							<td width=4%>Prestador:</td>
							<td>
								<select id='lab_codigo' class='boxa' onchange='loadMedCod(this)'>
									<option value='0'>....</option>";
									$stmt = "select med_codigo,med_nome from medico where prestador_servico = 'L' order by med_nome";
									$qry = db_query( $stmt );
									while( $agt = pg_fetch_array($qry) )
									{
										echo "\n\t\t\t<option value='{$agt[0]}'>{$agt[1]}</option>";
									}
								echo "</select>
								<input type='hidden' name='quota_qtde' id='quota_qtde'>
							</td>
						</tr>
					</table>
				</fieldset>
			</td>
		</tr>
	</table>";
	$pac = pg_fetch_array(pg_query("SELECT * FROM usuario WHERE usu_codigo = '$sel[usu_codigo]'"));
	echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
		<tr>
			<td>
				<fieldset>";
					echo "<legend>Dados do Paciente</legend>";
					echo "<table width=100% cellspacing=0 cellpadding=1 border=0>";
						echo "<tr>";
							echo "<td align=right width=30>Prontuario:</td>";
							echo "<td width=130>";
								echo "<input type=text name='pac_codigo' id='pac_codigo' class=boxl size=10 readonly value='$pac[usu_codigo]'>";
							echo "</td>";
							echo "<td width=40>Paciente</td>";
							echo "<td>";
								echo "<input type=text name=pac_nome id=pac_nome value='$pac[usu_nome]' class=boxl size=60 onkeyasdfup=\"buscar_nome(this.value);\" style=\"text-transform:uppercase;\" onkeypress=\"if(event.keyCode == 13)buscar_nome(\$F('pac_nome'), 'buscar_nome')\">";
								echo "<a href='#' onclick=\"buscar_nome(\$F('pac_nome'), 'buscar_nome');return false;link_f7()\"><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/localizar.jpg id=localizar align=absmiddle border=0></a>";
								
								echo divBuscaPaciente();
							echo "</td>";
							echo "<td width=10>Nascimento:</td>";
							echo "<td>";
								echo "<input type=text name=pac_nascimento value='$pac[usu_datanasc]' id=pac_nascimento class=boxl size=15 onkeypress=\"if(event.keyCode == 13)buscar_nome(\$F('pac_nascimento'), 'buscar_data');return Ajusta_Data(this, event);\" maxlength=\"10\">";
								echo "<a href='#' onclick=\"buscar_nome(\$F('pac_nascimento'), 'buscar_data')\"><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/localizar.jpg id=localizar align=absmiddle border=0></a>";
							echo "</td>";
						echo "</tr>";
						echo "<tr>";
							echo "<td align=right>Mae</td>";
							echo "<td width=100 colspan=3>";
								echo "<table width=100% cellspacing=0 cellpadding=0 border=0>
									<tr>
										<td><input type=text name=pac_mae value='$pac[usu_mae]' id=pac_mae class=boxl size=50 readonly></td>
										<td>&nbsp;</td>
										<td>Cidade:</td>
										<td><input type=text name=pac_cidade id=pac_cidade value='$pac[usu_end_cidade]' class=boxl size=23 readonly></td>
									</tr>
								</table>";
							echo "</td>";
							echo "<td colspan=2>";
								echo "<a href='#' OnClick='window.open(\"../paciente_ficha.php?acao=form_add&type=c&id_login=$id_login&controle=1\",null,\" height=460,width=800,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes\");'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/ficha_on.jpg align=absmiddle id=ficha border=0></a>";
							echo "</td>";
						echo "</tr>";
					echo "</table>";
				echo "</fieldset>
			</td>
		</tr>
	</table>";
	echo "<div id='dados' style='display:none'>";
		echo "<script language=\"JavaScript\" type=\"text/javascript\" src=\"procedimento.js\"></script>";
		echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
			<tr>
				<td>
					<fieldset>
						<legend>Selecione os Procedimentos</legend>";
						echo "<table width=100% cellspacing=0 cellpadding=5 border=0>
							<tr>
								<div id='add_dest'></div>
								<div id='add_dest_all'></div>
								<div id='rem_dest'></div>
								<div id='rem_dest_all'></div>
								<td width=500>";
									echo "<select id='dest' name='dest' class='box' size='4' style='width:500px;height:200px;'>";
									echo "</select>";
								echo "</td>
								<td>
									<select id='dest_list' name='dest_list[]' class='box' size='4' style='width:400px;height:200px;'>
										<option value='0'>... Procedimentos Selecionados...</option>
									</select>
								</td>
							</tr>
						</table>";
				echo "</fieldset>
				</td>
			</tr>
		</table>";
	echo "</div>";
?>
