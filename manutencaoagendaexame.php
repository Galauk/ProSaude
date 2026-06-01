<?php
	//------------------------------------------------------------------>
	// -> Inclusao principal para montagem do sistema
	//------------------------------------------------------------------>
session_start();
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
cabecario();
	//------------------------------------------------------------------>
//
//-> Botoes
  echo "<fieldset>
            <legend>Op&ccedil;&otilde;es</legend>
             <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
              <tr>
                <td width=120><a href=exame/exa_agendamento.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/fazeragendamento_on.jpg border=0></a></td>
                <td width=205><a href=exa_lab_valor.php?id_login=$id_login><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/labquotavlr.png' border=0></a></td>
                <td width=205><a href=exa_lab_qtde.php?id_login=$id_login><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/labquotauni.png' border=0></a></td>
                <td width=205><a href='#'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/vagasunidades.png' border=0></a></td>
                <td width=205><a href=../manutencaoagendaexame.php?id_login=$id_login><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/manutencaoagenda_on.jpg' border=0></a></td>
                <td width=205>&nbsp;</td>
               </form>
              </tr>
             </table>
       </fieldset>
      <br>";
?>

<script language="JavaScript" type="text/javascript" src="funcoes.js"></script>
<script language="JavaScript" type="text/javascript" src="ajax_motor.js"></script>
<script>
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
	
	function buscarDadosComplementares()
	{
		document.getElementById('id_dia_fim').value = "";
		d = document.getElementById('id_dia_ini');
		d.innerHTML = "";
		d.options[0]=new Option("...","");
		med_codigo =  document.getElementById('med_codigo').value;
		url = "buscarDadosExame.php?med_codigo="+med_codigo;
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
				d.options[d.options.length]=new Option(aux[0],aux[2]);
			}
		}
	}
	
	function popularDiaFim(valor)
	{
		//alert(valor);
		url = "buscarFimPeriodo.php?gex_codigo="+valor;			
		if(ajax) {			
			ajax.open("GET", url, true);		
			
			ajax.onreadystatechange = function() {
				if(ajax.readyState == 4) {
					if(ajax.status == 200) {
						//alert(ajax.responseText);
						resposta = ajax.responseText;
						aux = resposta.split("-");
						
						document.getElementById('id_dia_fim').value = aux[1];
						document.getElementById("id_dia_inicial").value = aux[0];
						}
				}
			}    
		ajax.send(null);
		
		}
	
	}

	function buscarProcedimento()
	{
		e = document.getElementById('id_dia_ini');
		e.innerHTML = "";
		e.options[0]=new Option("...","");
		med_codigo =  document.getElementById('med_codigo').value;
		url = "buscarProcedimento.php?med_codigo="+med_codigo;
		ajax_tudo(url, popularProcedimento);
	}
	
	function popularProcedimento(txt)
	{
		d = document.getElementById('proc_codigo');
		d.innerHTML = "";
		d.options[0]=new Option("...","");
		r =txt;
		res = r.split(";");
		for(x = 0; x < res.length; x++)
		{
		//devera ser alterado aqui apos a configuracao correta da nova tabela de exames
		//marco
			aux = res[x].split("-");
			if(aux[1] != undefined)
			{
				d.options[d.options.length]=new Option(aux[1],aux[0]);
			}
		}
	}
	
	function montar(id_login)
	{
		gex_codigo = document.getElementById('id_dia_ini').value;
		med_codigo =  document.getElementById('med_codigo').value;
		proc_codigo =  document.getElementById('proc_codigo').value;
		proc_codigo = proc_codigo.split("-");
		if(gex_codigo == null || gex_codigo == ""){
			
			return false;
			exit();
		}
		if(med_codigo == null || med_codigo == ""){
			
			return false;
			exit();
		}
		if(proc_codigo == null || proc_codigo == ""){
			
			return false;
			exit();
		}
		
	//	alert(proc_codigo);
		id_dia_ini = document.getElementById('id_dia_inicial').value;
		id_dia_ini = id_dia_ini.split("-");	
		id_dia_ini = id_dia_ini[0];

		id_dia_fim = document.getElementById('id_dia_fim').value;
		document.getElementById('frameprincipal').src = "manutencaoagendaexame_iframe.php?med_codigo="+med_codigo+"&proc_codigo="+proc_codigo+"&id_dia_ini="+id_dia_ini+"&id_dia_fim="+id_dia_fim+"&id_login="+id_login+"&gex_codigo="+gex_codigo;
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
reglog($id_login,"Acessando Manutencao dos Exames");
//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
//------------------------------------------------------------------>

echo "<fieldset><legend>MANUTENCÃO DE AGENDAS DE EXAMES</legend>";

echo "<table width=100% align=left cellspacing=2 cellpadding=2 border=0 style='border-top:1px solid;border-left:1px solid;border-right:1px solid;border-bottom:1px solid;border-color:909090'>";
	echo "<tr>";
		echo "<td align=right>Laboratorio</td>";
//		echo "<td colspan=2>";
		echo "<td >";
			echo "<select name=mAtividadeed_codigo id=med_codigo class=boxr onChange=\"buscarProcedimento()\">";
			echo "<option value=''>...</option>";
			$sql = pg_query("select * from medico 
			                 where prestador_servico = 'L' 
					 order by med_nome");
			while($med=pg_fetch_array($sql))
			{
			   echo "<option value=$med[med_codigo]>$med[med_nome]</option>";
			}
			echo "</select>";
		echo "</td>";
	echo "</tr>";

	echo "<tr>";
        	echo "<td align=right>Procedimento </td>";
//		echo "<td colspan=2>";
		echo "<td>";
                	echo "<select name=proc_codigo id=proc_codigo class=boxr onChange=\"buscarDadosComplementares();\">";
	        	echo "<option>...</option>";
	        	echo "</select>";
		echo "</td>";
	echo "</tr>";

        echo "<tr>";
	    echo "<td align=right>";
                    echo "Periodo";
            echo "</td>";
//            echo "<td colspan=2>";
            echo "<td>";

	    echo "<table border=0>";
        	echo "<tr>";
		echo "<td width=120px>";
	  echo "<select name=id_dia_ini id=id_dia_ini class=boxn onChange=\"popularDiaFim(this.value)\" style='width:120px'>";
        	echo "<option value=''>...</option>";
	  echo "</select>";
	  echo"<input type='hidden' name='id_dia_inicial' id='id_dia_inicial'>";
		//echo "<input type=text name=id_dia_ini id=id_dia_ini class=boxn size=12 id='data' maxlength='10' onKeypress=\"return Ajusta_Data(this, event);\">";
		echo "</td>";
	echo "<td align=center>";
	echo "&Agrave;";
	echo "</td>";
	echo "<td>";
	echo "<input type=text name=id_dia_fim id=id_dia_fim class=boxn size=12 id='data' maxlength='10' onKeypress=\"return Ajusta_Data(this, event);\" readonly>";
	echo "</td>";
	echo "</tr>";
echo "</table>";
//	echo "</td>";
//	echo "</tr>";
	echo "<tr>";
//	echo "<td colspan=3 align=left>";
	echo "<td colspan=4 align=left>";
		echo "&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; "; 
		echo "&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; "; 
		echo "&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; "; 
		echo " <input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/fazer_agenda_on.jpg onclick='montar($id_login);'>";
	echo "</td>";
	echo "</td>";
	echo "</tr>";
	echo "<tr>";
		echo "<td colspan=3>";
			echo "<table width='100%'>";
				echo "<tr>";
					echo "<td>";
	echo "<iframe id=frameprincipal name=frameprincipal src=# frameborder=no marginheight=0 marginwidth=0 scrolling=yes width=100% height=290></iframe>";
					echo "</td>";
				echo "</tr>";
			echo "</table>";
		echo "</td>";
	echo "</tr>";
echo "</table>";
		
?>
</fieldset>
