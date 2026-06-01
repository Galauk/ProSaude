<?php
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();
//------------------------------------------------------------------>
?>
<script language="JavaScript" type="text/javascript" src="funcoes.js"></script>
<script language="JavaScript" type="text/javascript" src="ajax_motor.js"></script>
<script>
	function abrePopUp(){
		url = "http://www.elotech.com.br/saude/Agendamento_Consulta/Manutencao_de_Agenda/Manutencao_de_Agenda.html";
		window.open(url,null,'height=400,width=650,status=yes,toolbar=no,menubar=no,location=no');
	}
	function changeLocation(menuObj)
	{
	   var i = menuObj.selectedIndex;

	   if(i > 0)
	   {
		  window.location = menuObj.options[i].value;
	   }
	}

	 function buscaferiado() {
	   data = document.manutencao.id_dia.value;
	   var url = 'age_ajax/busca_feriado.php?&data='+data;
	 ajax(url);
	}
	function mostra(nome)
	{
	   var obj = document.getElementById(nome);
	   obj.className = ( ' '+obj.className ).replace(' hide','');
	}

	function esconde(nome)
	{
	   var obj = document.getElementById(nome);
	   obj.className = ( ' '+obj.className ).replace(' hide','') + ' hide';
	}
	
	function buscarDadosComplementares(esp_codigo)
	{
		document.getElementById('gra_qtde_total').value = "";
		document.getElementById('id_dia_fim').value = "";
		d = document.getElementById('id_dia_ini');
		d.innerHTML = "";
		d.options[0]=new Option("...","");
		age_item = document.getElementById('age_item').value;
		url = "buscarDados.php?med_codigo="+med_codigo+"&esp_codigo="+esp_codigo+"&age_item="+age_item;
		ajax_tudo(url, popularDados );
	}
	
	function popularDados(texto)
	{
		d = document.getElementById('id_dia_ini');
		d.options[0]=new Option("...","");
		res = texto.split(";");
		for(k = 0; k < res.length; k++)
		{
			aux = res[k].split("-");
			if(aux[0] != undefined && aux[1] != undefined)
			{
				d.options[d.options.length]=new Option(aux[0],aux[0]+"-"+aux[1]);
			}
		}
	}
	
	function popularDiaFim(valor)
	{
		//alert(valor);
		v = valor.split("-");
		document.getElementById('id_dia_fim').value = v[1];
	}

	function buscarEspecialidade()
	{
		document.getElementById('gra_qtde_total').value = "";
		document.getElementById('id_dia_fim').value = "";
		e = document.getElementById('id_dia_ini');
		d = document.getElementById('esp_codigo');
		d.innerHTML = "";
		e.innerHTML = "";
		d.options[0]=new Option("...","");
		e.options[0]=new Option("...","");
		uni_codigo = document.getElementById('uni_codigo').value;
		med_codigo =  document.getElementById('med_codigo').value;
		url = "buscarEspecialidade.php?uni_codigo="+uni_codigo+"&med_codigo="+med_codigo;
		ajax_tudo(url, popularEspecialidade);
	}
	
	function popularEspecialidade(txt)
	{
		d = document.getElementById('esp_codigo');
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
	
	function montar(id_login)
	{
		uni_codigo = document.getElementById('uni_codigo').value;
		med_codigo =  document.getElementById('med_codigo').value;
		esp_codigo = document.getElementById('esp_codigo').value;
		gra_hora_ini = document.getElementById('gra_hora_ini').value;
		gra_bloqueado = document.getElementById('gra_bloqueado').value;
		id_dia_ini = document.getElementById('id_dia_ini').value;
		id_dia_ini = id_dia_ini.split("-");
		id_dia_ini = id_dia_ini[0];
		id_dia_fim = document.getElementById('id_dia_fim').value;
		gra_qtde_total = document.getElementById('gra_qtde_total').value;
		age_tipo = document.getElementById('age_tipo').value;
		age_item = document.getElementById('age_item').value;
		//alert(gra_bloqueado);
		if (uni_codigo == ''){
			alert("Escolha uma unidade");
			document.getElementById('uni_codigo').focus();
		}else if(med_codigo == ''){
			alert("Escolha um Profissional");
			document.getElementById('med_codigo').focus();
		}else if (esp_codigo == ''){
			alert("Escolha uma Atividade Profissional");
			document.getElementById('esp_codigo').focus();
		}else if(document.getElementById('gra_hora_ini').value == ''){
			alert("A hora inicial não pode ser vazia");
			document.getElementById('gra_hora_ini').focus();
		}else if(id_dia_ini == ''){
			alert("Escolha um período");
			document.getElementById('id_dia_ini').focus();
		}else{
			document.getElementById('frameprincipal').src = "manutencaomedico_iframe.php?med_codigo="+med_codigo+"&uni_codigo="+uni_codigo+"&esp_codigo="+esp_codigo+"&gra_hora_ini="+gra_hora_ini+"&id_dia_ini="+id_dia_ini+"&id_dia_fim="+id_dia_fim+"&gra_qtde_total="+gra_qtde_total+"&age_tipo="+age_tipo+"&age_item="+age_item+"&id_login="+id_login+"&gra_bloqueado="+gra_bloqueado;
		}
	}
	
</script>
<style type="text/css">
.caixa { 
   padding: 10px; 
   width: 175px;
   height: 160px;
   background: #000000; 
   border: 1px solid #333; 
   margin: 20px; 
} 
.hide {
   visibility: hidden;
}
</style>
<?
reglog($id_login,"Acessando Manutencao dos Medicos");
//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
//------------------------------------------------------------------>

echo "<fieldset><legend>MANUTENÇÃO DE AGENDAS</legend>";

echo "<table width=100% align=center cellspacing=2 cellpadding=2 border=0 style='border-top:1px solid;border-left:1px solid;border-right:1px solid;border-bottom:1px solid;border-color:909090'>";
	echo "<tr>";
		/*echo "<td align=center>";
			echo "<table width=100% align=center cellspacing=3 cellpadding=0 border=0 style='width:500px;'>";
				echo "<tr>";*/
					$sqlUni = "SELECT uni_codigo from usuarios where usr_codigo = $id_login";
					$row = pg_fetch_array(pg_query($sqlUni));
					echo "<td align=right width=320>Unidade de atendimento</td>";
					echo "<td colspan=3>";
					echo "<select name=uni_codigo id=uni_codigo class=boxr onChange=\"buscarEspecialidade()\">";
						echo "<option value=''>...</option>";
							if ($row[0])
							{
								$sql = pg_query("select * from unidade where uni_codigo = $row[0] order by uni_desc");
							}
								else {
								$sql = pg_query("select * from unidade order by uni_desc");
							}
							while($uni=pg_fetch_array($sql))
							{
								echo "<option value='$uni[uni_codigo]'>$uni[uni_desc]</option>";
							}
						echo "</select>";
					echo "</td>";
					
				echo "</tr>";
				echo "<tr>";

					echo "<td align=right>Profissional</td>";
					echo "<td colspan=3>";
						echo "<select name=mAtividadeed_codigo id=med_codigo class=boxr onChange=\"buscarEspecialidade()\">";
							echo "<option value=''>...</option>";
							$sql = pg_query("SELECT * 
											   FROM usuarios
											  WHERE usr_tipo_medico in ('A','E','M','D')
											  ORDER BY usr_nome");
							while($med=pg_fetch_array($sql))
							{
							   echo "<option value=$med[usr_codigo]>$med[usr_nome]</option>";
							}
						echo "</select>";
					echo "</td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td align=right>Atividade Profissional</td>";
					echo "<td colspan=3>";
						echo "<select name=esp_codigo id=esp_codigo class=boxr onChange=\"buscarDadosComplementares(this.value);\">";
							echo "<option>...</option>";
						echo "</select>";
					echo "</td>";
				/*echo "</tr>";
			echo "</table>";
		echo "</td>";*/
	echo "</tr>";
	echo "<tr>";
		echo "<td align=right>";
			//echo "<form method=post action= name=manutencao>";
			/*echo "<table align=center cellspacing=3 cellpadding=0 border=1 align=center style='width:600px;'>";
				echo "<tr>";
					echo "<td>";*/
						echo "Hora Ini.";
					echo "</td>";
					echo "<td width=100px>";
						echo "<input type=text name=gra_hora_ini id=gra_hora_ini class=box size=5 value='08:00'>";
					echo "</td>";
					echo "<td width=\"143\">";
						echo "<select name=age_item id=age_item class=box  onChange=\"buscarDadosComplementares(document.getElementById('esp_codigo').value);\">";
							echo "<option value='CB'>CLINICA BÁSICA</option>";
							echo "<option value='ES'>ESPECIALIDADE</option>";
						echo "</select>";
					echo "</td>";
					echo "<td>";
							echo "<select name=age_tipo id=age_tipo class=box>";
							echo "<option value='PC'>PC</option>";
							echo "<option value='GE'>GE</option>";
							echo "<option value='RT'>RT</option>";
							echo "<option value='AL'>AL</option>";
							echo "<option value='CA'>CA</option>";
							echo "<option value='CT'>CT</option>";
							echo "<option value='DI'>DI</option>";
						echo "</select>";
					echo "</td>";
				echo "</tr>";
				echo "<tr>";/*
					echo "<td align=right>";
						echo "Bloqueado at&eacute;";
					echo "</td>";
					echo "<td>";
						/*$sql = "select gra_codigo, gra_data, gra_tipo, gra_status, gra_qtde,
									   gra_hora_ini, age_item, age_tipo, gra_obs, to_char(gra_bloqueado,'DD/MM/YYYY') as gra_bloqueado
										from grade_medico
										where med_codigo = '$med_codigo'
										and   uni_codigo = '$uni_codigo'
										and   esp_codigo = '$esp_codigo'
										and   gra_data = '$Data[2]-$Data[1]-$Data[0]' order by gra_hora_ini";*//*
						echo "<input type=text class=box name='gra_bloqueado' id=gra_bloqueado size=12 value='12/12/20010' readonly>";
					echo "</td>";*/
				echo "<input type=\"hidden\" class=box name='gra_bloqueado' id=gra_bloqueado size=12 value='12/12/20010' readonly>";
					echo "<td align=\"right\">";
						echo "Quantidade Total de Vagas:";
					echo "</td>";
					echo "<td colspan=\"3\">";
						echo "<input type=text name=gra_qtde_total id=gra_qtde_total class=box size=3 value='' readonly>";
					echo "</td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td align=right>";
						echo "Periodo";
					echo "</td>";
					echo "<td colspan=3>";
						echo "<table border=0>";
							echo "<tr>";
								echo "<td width=80px>";
									echo "<select name=id_dia_ini id=id_dia_ini class=boxn onChange=\"popularDiaFim(this.value)\" style='width:80px'>";
										echo "<option value=''>...</option>";
									echo "</select>";
									//echo "<input type=text name=id_dia_ini id=id_dia_ini class=boxn size=12 id='data' maxlength='10' onKeypress=\"return Ajusta_Data(this, event);\">";
								echo "</td>";
								echo "<td align=center>";
									echo "&Agrave;";
								echo "</td>";
								echo "<td>";
									/*echo "<select name=id_dia_fim id=id_dia_fim>";
										echo "<option value=''>...</option>";
									echo "</select>";*/
									echo "<input type=text name=id_dia_fim id=id_dia_fim class=boxn size=12 id='data' maxlength='10' onKeypress=\"return Ajusta_Data(this, event);\" readonly>";
								echo "</td>";
							echo "</tr>";
						echo "</table>";
					echo "</td>";
				echo "</tr>";
				echo "<tr>";
					echo "<td colspan=3 align=right>";
						echo "<input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/fazer_agenda_on.jpg onclick='montar($id_login);'>";
					echo "</td>";
					
					
				
				/*echo "</tr>";
			echo "</table>";
			//echo "</form>";*/
		echo "</td>";
	echo "</tr>";
	echo "<tr>";
		echo "<td colspan=4>";
			echo "<table class='table'>";
				echo "<tr>";
					echo "<td>";
						echo "<iframe id=frameprincipal name=frameprincipal src=# frameborder=no marginheight=0 marginwidth=0 scrolling=yes width='100%' height=290></iframe>";
					echo "</td>";
				echo "</tr>";
			echo "</table>";
		echo "</td>";
	echo "</tr>";
	/*echo "<tr>";
		echo "<td colspan=5>";
			include_once "calendarMedico.php";
		echo "</td>";
	echo "</tr>";*/
echo "</table>";
		
?>
</fieldset>
