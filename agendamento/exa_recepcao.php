<?
session_start();
/**
 * alteracoes: Colocado restricoes no acesso seguindo a tabela usuarios_acessos caso o usuario tenha restricao.
 * alteracoes dia 14/05/2007: colocado return false nos botoes
 * @brief Arrumado um bug nas datas que não iam pro banco formatado no formato yyyy-mm-dd e feito o total de pacientes em cima da listagem dos mesmos -> 18/05/2007
 */
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>
	include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
	verauth($id_login);

	include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();
		
	//echo monta_janela("transferir", "TRANSFER&Circ;NCIA");
	
	echo monta_calendario();
    
    /*echo "<pre>";
        print_r($_REQUEST);
    echo "</pre>";*/
    
?>
<fieldset><legend>RECEP&Ccedil;&Atilde;O</legend>
<div class="janela" id="transferir">
	<div class="titulo" id="transferir_titulo">
		<span id="tranferir_titulo_txt">TRANSFER&Ecirc;NCIA</span>
		<img src="<?=$_SESSION[linkroot].$_SESSION[comum]; ?>imgs/jan_fechar.jpg" onclick="esconde_janela('transferir')" alt="Fechar" />
		<img src="<?=$_SESSION[linkroot].$_SESSION[comum]; ?>imgs/jan_min.jpg" id="transferir_mm" class="mm" onclick="mm_janela('transferir')" alt="Fechar" />
	</div>
	<div class="conteudo" id="transferir_conteudo">
		Carregando <img src="<?=$_SESSION[linkroot].$_SESSION[comum]; ?>imgs/loading.gif" alt="Carregando"/>
	</div>
</div>
<script language="JavaScript" type="text/javascript" src="../funcoes.js"></script>
<script language="JavaScript" type="text/javascript" src="../g_ajax.js"></script>
<script language="JavaScript" type="text/javascript" src="../ajax_motor.js"></script>
<SCRIPT LANGUAGE="JavaScript">
function hotkey(eventname,age_codigo,uni_codigo,esp_codigo,med_codigo,age_data,id_login, hora) {
    var teste = +age_codigo;
  /*  if(eventname.keyCode == 118) {
     if(teste=="") {
        alert("ERRO: Voce deve selecionar o paciente antes de executar esta a��o.");
        return false;
     } else {
       self.location.href="agendamento.php?id_login="+id_login+"&acao=mostra_age&f7=ok&age_data="+age_data+"&uni_codigo="+uni_codigo+"&med_codigo="+med_codigo+"&esp_codigo="+esp_codigo+"&acao=mostra_age&age_codigo="+age_codigo+"&hora="+hora; 
     }
    }*/
    if(eventname.keyCode == 119) {
     if(teste=="") {
        alert("ERRO: Voce deve selecionar o paciente antes de executar esta a��o.");
        return false;
     } else {
       self.location.href="agendamento.php?id_login="+id_login+"&acao=mostra_age&f8=ok&age_data="+age_data+"&uni_codigo="+uni_codigo+"&med_codigo="+med_codigo+"&esp_codigo="+esp_codigo+"&acao=mostra_age&age_codigo="+age_codigo+"&hora="+hora; 
     }
    }
    if(eventname.keyCode == 120) {
     if(teste=="") {
        alert("ERRO: Voce deve selecionar o paciente antes de executar esta a��o.");
        return false;
     } else {
       self.location.href="agendamento.php?id_login="+id_login+"&acao=mostra_age&f9=ok&age_data="+age_data+"&uni_codigo="+uni_codigo+"&med_codigo="+med_codigo+"&esp_codigo="+esp_codigo+"&acao=mostra_age&age_codigo="+age_codigo+"&hora="+hora; 
     }
    }
}

function hotkey2(eventname,age_codigo,uni_codigo,esp_codigo,med_codigo,age_data,id_login, hora) {
    var teste = +age_codigo;

    //var t = '';
    //for( var i=0; i < arguments.length; i++ ) t+= arguments[i] +", ";
    //alert(t);
	
    if(eventname == 118) {
       self.location.href="agendamento.php?id_login="+id_login+"&acao=mostra_age&f7=ok&age_data="+age_data+"&uni_codigo="+uni_codigo+"&med_codigo="+med_codigo+"&esp_codigo="+esp_codigo+"&acao=mostra_age&age_codigo="+age_codigo+"&hora="+hora;
       return false;
    }
    if(eventname == 119) {
     if(teste=="") {
        alert("ERRO: Voce deve selecionar o paciente antes de executar esta a��o.");
        return false;
     } else {
       self.location.href="agendamento.php?id_login="+id_login+"&acao=mostra_age&f8=ok&age_data="+age_data+"&uni_codigo="+uni_codigo+"&med_codigo="+med_codigo+"&esp_codigo="+esp_codigo+"&acao=mostra_age&age_codigo="+age_codigo+"&hora="+hora; 
     }
    }
    if(eventname == 120) {
     if(teste=="") {
        alert("ERRO: Voce deve selecionar o paciente antes de executar esta a��o.");
        return false;
     } else {
       self.location.href="agendamento.php?id_login="+id_login+"&acao=mostra_age&f9=ok&age_data="+age_data+"&uni_codigo="+uni_codigo+"&med_codigo="+med_codigo+"&esp_codigo="+esp_codigo+"&acao=mostra_age&age_codigo="+age_codigo+"&hora="+hora; 
     }
    }
}

 function msg(id_login,age_codigo,uni_codigo,esp_codigo,med_codigo,age_data, hora) { 
     self.location.href="agendamento.php?id_login="+id_login+"&age_data="+age_data+"&uni_codigo="+uni_codigo+"&med_codigo="+med_codigo+"&esp_codigo="+esp_codigo+"&acao=mostra_age&age_codigo="+age_codigo+"&hora="+hora; 
 }
function changeLocation(menuObj)
{
   var i = menuObj.selectedIndex;

   if(i > 0)
   {
      window.location = menuObj.options[i].value;
   }
}
</script>
<script>
function gradata(hr) {
     document.forms.age_data.value = hr;
}
function gralimpa() {
     document.forms.age_data.value = '';
}
    var m = "";
    var a = "";
    var i = "";
    var t = "";
	function salvarFaltaMedico(med_codigo, age_codigo, id_login, tipo)
	{
        var m = med_codigo;
        var a = age_codigo;
        var i = id_login;
        var t = tipo;
		if(tipo == 'salvar')
		{
			aux = confirm('Deseja Salvar Falta para este medico?');
		} else {
			aux = confirm('Deseja Retirar Falta para este medico?');
		}
		if(aux)
		{
			try{
				 ajax = new XMLHttpRequest();
			}catch(ee){
				try{
					 ajax = new ActiveXObject("Msxml2.XMLHTTP");
				}catch(e){
					try{
						 ajax = new ActiveXObject("Microsoft.XMLHTTP");
					}catch(E){
						 ajax = false;
					}
				}
			}
			url = "salvarFaltaMedico.php?med_codigo="+med_codigo+"&age_codigo="+age_codigo+"&id_login="+id_login;
            //ajax_tudo(url, resposta_salvar);
			//alert(url);
			ajax.open("GET", url, true);
            ajax.onreadystatechange = function()
            {
                if(ajax.readyState == 4)
                {
                    txt = ajax.responseText.split("-");
                    if(txt[1] == false)
                    {
                        salvarFaltaMedico(med_codigo, age_codigo, id_login, tipo);
                    } else {
                        caminho = document.getElementById('med_null').value;
                        data = document.getElementById('data').value;
                        hora = document.getElementById('hora').value;
                        location.href = caminho+"&age_data="+data+"&hora="+hora+"&acao=mostra_age";
                    }
                }
            }
			ajax.send(null);
            //location.href = location.href;
		} else {
			return false;
		}
	}
    
    function resposta_salvar(txt)
    {
        alert(txt);
        txt = txt.split("-");
        if(txt[1] == "false")
        {
            salvarFaltaMedico(m, a, i, t);
        } else {
            location.href = location.href;
        }
    }
    
    
	function transferir(url, div)
	{
		document.getElementById('transferir').style.display='block';
		exec_ajax(url, div);
	}
	
	function buscarMed(codigo, valor)
	{
		d = document.getElementById('data');
		d.innerHTML = "";
		d.options[0]=new Option("-> Data <-","");
		campo = document.getElementById('med_codigo');
		campo.innerHTML = "";
		campo.options[0]=new Option("-> Medico <-","");
		var uni_codigo = document.getElementById('uni_codigo').value;
		var esp_codigo = document.getElementById('esp_codigo').value;
		if(valor == 0)
		{
			if(document.getElementById('uni_codigo').value == "")
			{
				return false;
			}
		} else if(valor == 1) {
			if(document.getElementById('esp_codigo').value == "")
			{
				return false;
			}
		}
		url = 'buscarMedico.php?uni_codigo='+uni_codigo+'&esp_codigo='+esp_codigo;
		ajax_tudo( url, popularMedico );
	}
	
	function popularMedico( txt )
	{
		campo = document.getElementById('med_codigo');
		campo.options[0]=new Option("-> Medico <-","");
		resp =txt;
		resposta = resp.split(";");
		for(i = 0; i < resposta.length; i++)
		{
            aux = resposta[i].split("-");
            if(aux[1] != undefined)
            {
                campo.options[campo.options.length]=new Option(aux[1],aux[0]);
            }
		}
	}
	
	function buscarVaga(med_codigo)
	{
		d = document.getElementById('data');
		d.innerHTML = "";
		d.options[0]=new Option("-> Data <-","");
		age_tipo = document.getElementById('age_tipo').value;
		uni_codigo = document.getElementById('uni_codigo').value;
		esp_codigo = document.getElementById('esp_codigo').value;
		url = 'buscarVagaMedico.php?med_codigo='+med_codigo+'&uni_codigo='+uni_codigo+'&esp_codigo='+esp_codigo+'&age_tipo='+age_tipo;
		ajax_tudo( url, populaData );
	}
	
	function populaData( txt )
	{
        //alert(txt);
        //document.getElementById('teste').innerHTML = txt;
		d = document.getElementById('data');
		d.options[0]=new Option("-> Data <-","");
		r =txt;
		res = r.split(";");
		for(x = 0; x < res.length; x++)
		{
            auxi = res[x].split("-");
            if(auxi[1] != undefined)
            {
                d.options[d.options.length]=new Option('Data '+auxi[0]+'Hora '+auxi[1]+'Vagas '+auxi[2],auxi);
            }
		}
	}
	
	function salvar()
	{
		
		age_tipo = document.getElementById('age_tipo').value;
		uni_codigo = document.getElementById('uni_codigo').value;
		esp_codigo = document.getElementById('esp_codigo').value;
		med_codigo = document.getElementById('med_codigo').value;
		age_paciente = document.getElementById('age_paciente').value;
		age_item = document.getElementById('age_item').value;
		age_valor_proc = document.getElementById('age_valor_proc').value;
		agt_codigo = document.getElementById('agt_codigo').value;
		age_codigo = document.getElementById('age_codigo').value;
		usu_codigo = document.getElementById('usu_codigo').value;
		id_login = document.getElementById('id_login').value;
		dat = document.getElementById('data').value;
		data = dat.split(",");
		age_data = data[0];
		if(uni_codigo == "")
		{
			alert("Favor escolher a unidade");
			document.getElementById('uni_codigo').value;
			return false;
		}
		if(esp_codigo == "")
		{
			alert("Favor escolher a especialidade");
			document.getElementById('esp_codigo').value;
			return false;
		}
		if(med_codigo == "")
		{
			alert("Favor escolher o medico");
			document.getElementById('med_codigo').value;
			return false;
		}
		if(data == "")
		{
			alert("Favor escolher a data");
			document.getElementById('data').value;
			return false;
		}
		url = 'salvarTransferencia.php?age_tipo='+age_tipo+'&uni_codigo='+uni_codigo+'&esp_codigo='+esp_codigo+'&med_codigo='+med_codigo+"&age_paciente="+age_paciente+"&age_item="+age_item+"&age_valor_proc="+age_valor_proc+"&agt_codigo="+agt_codigo+"&age_data="+age_data+"&age_codigo="+age_codigo+"&usu_codigo="+usu_codigo+"&id_login="+id_login;
		ajax_tudo( url, transferencia );
	}
	
	function transferencia( texto )
	{
		da = new Date();
		dia = da.getDate();
		mes = da.getMonth();
		ano = da.getFullYear();
		if(mes < 10)
		{
			m = '0'+mes;
		} else {
			m = mes;
		}
		dd = dia+"/"+m+"/"+ano;
		t = texto.split("-");
		//document.getElementById('teste').innerHTML = texto;
		if(t[0] == "true")
		{
			alert("Paciente transferido com sucesso");
			document.getElementById('transferir').style.display='none';
			print = "print_guia.php?uni_codigo="+uni_codigo+"&esp_codigo="+esp_codigo+"&agt_codigo="+agt_codigo+"&usu_codigo="+id_login+"&age_codigo="+t[1]+"&med_codigo="+med_codigo;
			window.open(print, "", "");
			self.location.reload();
		} else if(texto == "false") {
			alert("Erro ao efetuar transferencia");
			document.getElementById('transferir').style.display='none';
		}
	}
	
	function atualizar( campo )
	{
		uni = document.getElementById("uni_null").value;
		med = document.getElementById("med_null").value;
		esp = document.getElementById("esp_null").value;
		data = campo.value;
		endereco = "buscarHorario.php?esp_codigo="+esp.value+"&med_codigo="+med+"&uni_codigo="+uni+"&data="+data;
		ajax_tudo( endereco, popular_horario );
	}
	
	function popular_horario(txt)
	{
		d = document.getElementById('hora');
		d.innerHTML = "";
		aux = txt.split("-");
		for(k = 0; k < aux.length; k++)
		{
			if(aux[k] != "")
			{
				d.options[d.options.length]=new Option(aux[k],aux[k]);
			}
		}
		d.focus();
	}
	
	function verificar()
	{
		
		if(document.getElementById('hora').value == '')
		{
			alert('Preencha a Hora');
			return false;
		} else {
			return true;
		}		
		
	}
	
</script>
<div id=teste></div>
<body onkeydown='hotkey(event,"<?=$age_codigo?>","<?=$uni_codigo?>","<?=$esp_codigo?>","<?=$med_codigo?>","<?=$age_data?>","<?=$id_login?>", "<?=$hora?>")'>
<?

    if($f7=="ok") {
		$rr=pg_fetch_array(pg_query("select *from agendamento where age_codigo='$age_codigo'"));
		if($rr[age_atendido]=="S") { 
			$tipo_age="N"; 
			reglog($id_login,"Cancelado Paciente: $rr[usu_codigo]");
		} else { 
			$tipo_age="S"; 
			reglog($id_login,"Recepcionado Paciente: $rr[usu_codigo]");
		}
        $stmt = "update agendamento set age_atendido='$tipo_age',usr_codigo_alt='$id_login',dt_atualizacao=NOW(),age_timestamp=".($tipo_age == "S" ? "CURRENT_TIMESTAMP" : 'null')." where age_codigo='$age_codigo'";
        //print "<h1>OIE</h1>";
        //exit;
		$sql = db_query($stmt);
        if(pg_affected_rows($sql) > 0)
        {
            //print "<h1>OIE</h1>";
            
            echo "<SCRIPT LANGUAGE=\"JavaScript\">
                      //setTimeout(\"location.href='$PHP_SELF?id_login=$id_login&uni_codigo=$uni_codigo&esp_codigo=$esp_codigo&med_codigo=$med_codigo&age_data=$age_data&hora=$hora&acao=mostra_age'\", 0);
                  </SCRIPT>";
        }
    }

  if($f8=="ok") {
		$rr=pg_fetch_array(pg_query("select *from agendamento where age_codigo='$age_codigo'"));
		if($rr[age_atendido]=="F") { 
			$tipo_age="N"; 
			reglog($id_login,"Desmarcado Falta Paciente: $rr[usu_codigo]");
			$vc=explode("/",$age_data);
			$ndata = $vc[1]."-".$vc[2];      
			$sel = pg_fetch_array(pg_query("select *from grade_mensal where med_codigo='$med_codigo'  and agt_codigo='384931' and esp_codigo='$esp_codigo'"));
			if($sel[grm_qtde]<=1) { $qtde = "0"; } else { $qtde = ($sel[grm_qtde]-1); }
			$qur = pg_query("update grade_mensal set grm_qtde ='$qtde' where agt_codigo='384931' and med_codigo = '$med_codigo' and esp_codigo = '$esp_codigo'");

			$med = pg_fetch_array(pg_query("select *from grade_medico where med_codigo='$med_codigo' and esp_codigo ='$esp_codigo' and uni_codigo ='$uni_codigo' and gra_data = '$age_data'"));
			if($med[gra_qtde]<=1) { $somamed = "0"; } else { $somamed = ($med[gra_qtde]-1); }
			$upmed = pg_query("update grade_medico set gra_qtde ='$somamed' where med_codigo='$med_codigo' and esp_codigo ='$esp_codigo' and uni_codigo ='$uni_codigo' and gra_data = '$age_data'"); 
		} else { 
			$tipo_age="F"; 
			reglog($id_login,"Marcado Falta Paciente: $rr[usu_codigo]");
			$vc=explode("/",$age_data);
			$ndata = $vc[1]."-".$vc[2];      
			$sel = pg_fetch_array(pg_query("select *from grade_mensal where med_codigo='$med_codigo'  and agt_codigo='384931' and esp_codigo='$esp_codigo'"));
			$qtde = ($sel[grm_qtde]+1);
			$qur = pg_query("update grade_mensal set grm_qtde ='$qtde' where agt_codigo='384931' and med_codigo = '$med_codigo' and esp_codigo = '$esp_codigo'");
			//echo "update grade_mensal set grm_qtde ='$qtde' where agt_codigo='384931' and med_codigo = '$med_codigo' and esp_codigo = '$esp_codigo' and to_char(grm_periodo,'MM-YYYY')='$ndata'";

			$med = pg_fetch_array(pg_query("select *from grade_medico where med_codigo='$med_codigo' and esp_codigo ='$esp_codigo' and uni_codigo ='$uni_codigo' and gra_data = '$age_data'"));
			$somamed=($med[gra_qtde]+1);
			$upmed = pg_query("update grade_medico set gra_qtde ='$somamed' where med_codigo='$med_codigo' and esp_codigo ='$esp_codigo' and uni_codigo ='$uni_codigo' and gra_data = '$age_data'"); 
		}
		$sql = pg_query("update agendamento set age_atendido='$tipo_age',usr_codigo_alt='$id_login',dt_atualizacao=NOW(),age_timestamp=null where age_codigo='$age_codigo'");
        if(pg_affected_rows($sql) > 0)
        {
            echo "<SCRIPT LANGUAGE=\"JavaScript\">
                      setTimeout(\"location.href='$PHP_SELF?id_login=$id_login&uni_codigo=$uni_codigo&esp_codigo=$esp_codigo&med_codigo=$med_codigo&age_data=$age_data&hora=$hora&acao=mostra_age'\", 0);
                  </SCRIPT>";
        }
  }

  if($f9=="ok") {
		$rr=pg_fetch_array(pg_query("select *from agendamento where age_codigo='$age_codigo'"));
reglog($id_login,"Cancelado ou Recepcionado Paciente: $rr[usu_codigo]");
		if($rr[age_atendido]=="T") { 
			reglog($id_login,"Desmarcado Transferencia Paciente: $rr[usu_codigo]");
			$tipo_age="N"; 
			$vc=explode("/",$age_data);
			$ndata = $vc[1]."-".$vc[2];      
			$sel = pg_fetch_array(pg_query("select *from grade_mensal where med_codigo='$med_codigo'  and agt_codigo='393519' and esp_codigo='$esp_codigo'"));
			if($sel[grm_qtde]<=1) { $qtde = "0"; } else { $qtde = ($sel[grm_qtde]-1); }
			$qur = pg_query("update grade_mensal set grm_qtde ='$qtde' where agt_codigo='393519' and med_codigo = '$med_codigo' and esp_codigo = '$esp_codigo'");

			$med = pg_fetch_array(pg_query("select *from grade_medico where med_codigo='$med_codigo' and esp_codigo ='$esp_codigo' and uni_codigo ='$uni_codigo' and gra_data = '$age_data'"));
			if($med[gra_qtde]<=1) { $somamed = "0"; } else { $somamed = ($med[gra_qtde]-1); }
			$upmed = pg_query("update grade_medico set gra_qtde ='$somamed' where med_codigo='$med_codigo' and esp_codigo ='$esp_codigo' and uni_codigo ='$uni_codigo' and gra_data = '$age_data'"); 
		} else { 
			$tipo_age="T"; 
			reglog($id_login,"Transferido Paciente: $rr[usu_codigo]");
			$vc=explode("/",$age_data);
			$ndata = $vc[1]."-".$vc[2];      
			$sel = pg_fetch_array(pg_query("select *from grade_mensal where med_codigo='$med_codigo'  and agt_codigo='393519' and esp_codigo='$esp_codigo'"));
			$qtde = ($sel[grm_qtde]+1);
			$qur = pg_query("update grade_mensal set grm_qtde ='$qtde' where agt_codigo='393519' and med_codigo = '$med_codigo' and esp_codigo = '$esp_codigo'");

			$med = pg_fetch_array(pg_query("select *from grade_medico where med_codigo='$med_codigo' and esp_codigo ='$esp_codigo' and uni_codigo ='$uni_codigo' and gra_data = '$age_data'"));
			$somamed=($med[gra_qtde]+1);
			$upmed = pg_query("update grade_medico set gra_qtde ='$somamed' where med_codigo='$med_codigo' and esp_codigo ='$esp_codigo' and uni_codigo ='$uni_codigo' and gra_data = '$age_data'"); 
		}
		$sql = pg_query("update agendamento set age_atendido='$tipo_age',usr_codigo_alt='$id_login',dt_atualizacao=NOW(),age_timestamp=null  where age_codigo='$age_codigo'");
        if(pg_affected_rows($sql) > 0)
        {
            echo "<SCRIPT LANGUAGE=\"JavaScript\">
                      setTimeout(\"location.href='$PHP_SELF?id_login=$id_login&uni_codigo=$uni_codigo&esp_codigo=$esp_codigo&med_codigo=$med_codigo&age_data=$age_data&hora=$hora&acao=mostra_age'\", 0);
                  </SCRIPT>";
        }
  }



//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
//------------------------------------------------------------------>
reglog($id_login,"Acesando RECEPCAO");

  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
         <tr>
          <td>
           <fieldset>
            <legend>Op&ccedil;&otilde;es</legend>
             <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
              <tr>
               <td width=126>".ChmodBtn($id_login,'fazeragendamento','fazer_agendamento.php?')."</td>
               <td width=146>".ChmodBtn($id_login,'manutencaoagenda','manutencaomedicos.php?')."</td>
               <td>".ChmodBtn($id_login,'manutencaogrupodeagente','manutencaoagentes.php?')."</td>
               <td><a href=exa_lab_procedimento.php?id_login=$id_login>Laboratorio por procedimento</a></td>
              </tr>
             </table>
           </fieldset>
          </td>
         </tr>
        </table><br>";

//
//-> Botoes
 echo "<form name=forms method=post action=$PHP_SELF>
	<input type=hidden name=uni_codigo value=$uni_codigo>
	<input type=hidden name=id_login value=$id_login>
	<input type=hidden name=esp_codigo value=$esp_codigo>
	<input type=hidden name=med_codigo value=$med_codigo>
	<input type=hidden name=procedimento value=$procedimento>
	<input type=hidden name=acao value=mostra_age>
	<table width=733 align=center cellspacing=2 cellpadding=4 border=0 style='border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'>
         <tr>
          <td>
             <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
              <tr>
		<td width=122 align=right>Unidade de sa&uacute;de</td>
		<td width=320><select name=uni_null id=uni_null class=boxr onChange=\"javascript:changeLocation(this)\">";
	echo "<option>...</option>";
        $exec_sel = pg_query("select * from usuarios_acessos where usr_codigo = '$id_login'");
        

        if(pg_num_rows($exec_sel) > 0)
        {
            $and_Select = "where uni_codigo in (select uni_codigo
                                                from usuarios_acessos
                                                where usr_codigo = $id_login)";
        }

        $sql = pg_query("select * from unidade $and_Select order by uni_desc");
	  while($uni=pg_fetch_array($sql)) {
	   echo ($uni[uni_codigo]==$uni_codigo)?"<option value='$PHP_SELF?id_login=$id_login&uni_codigo=$uni[uni_codigo]&med_codigo=med_codigo&esp_codigo=$esp_codigo' selected>$uni[uni_desc]</option>":"<option value='$PHP_SELF?id_login=$id_login&uni_codigo=$uni[uni_codigo]&med_codigo=med_codigo&esp_codigo=$esp_codigo'>$uni[uni_desc]</option>";
	  }
	$agesel = pg_fetch_array(pg_query("select usr_codigo_cad,to_char(dt_cadastro,'DD/MM/YYYY') as dt_cadastro from agendamento where age_codigo = '$age_codigo'"));
	$seluser = pg_fetch_array(pg_query("select *from usuarios where usr_codigo = '$agesel[usr_codigo_cad]'"));
	echo "</select></td>
		<td><b><font color=blue>Cadastrado Por:</font></b> $seluser[usr_nome] $agesel[dt_cadastro]</td>
              </tr>
              <tr>
		<td width=122 align=right>Profissional</td>
		<td width=320><select name=med_null id=med_null class=boxr  onChange=\"javascript:changeLocation(this)\">";
	echo "<option>...</option>";
    if(pg_num_rows($exec_sel) > 0)
    {
        $sql = pg_query("select *
                        from medico
                        where med_codigo in (select med_codigo
                                            from usuarios_acessos
                                            where usr_codigo = $id_login
                                            and uni_codigo = $uni_codigo)
                        order by med_nome");
    } else {
        $sql = pg_query("select * from medico order by med_nome");
    }
	  while($med=pg_fetch_array($sql)) {
	   echo ($med[med_codigo]==$med_codigo)?"<option value=$PHP_SELF?id_login=$id_login&med_codigo=$med[med_codigo]&uni_codigo=$uni_codigo&esp_codigo=$esp_codigo selected>$med[med_nome]</option>":"<option value=$PHP_SELF?id_login=$id_login&med_codigo=$med[med_codigo]&uni_codigo=$uni_codigo&esp_codigo=$esp_codigo>$med[med_nome]</option>";
	  }
	$agesel_u = pg_fetch_array(pg_query("select usr_codigo_alt,to_char(dt_atualizacao,'DD/MM/YYYY') as dt_atualizacao from agendamento where age_codigo = '$age_codigo'"));
	$seluser_u = pg_fetch_array(pg_query("select *from usuarios where usr_codigo = '$agesel_u[usr_codigo_alt]'"));
	echo "</select></td>
		<td><b><font color=orange>Alterado Por:</font></b> $seluser_u[usr_nome] $agesel_u[dt_atualizacao]</td>
              </tr>
              <tr>
		<td align=right width=122>Especialidade</td>
		<td><select name=esp_null id=esp_null class=boxr onChange=\"javascript:changeLocation(this)\">";
	echo "<option>...</option>";
    if(pg_num_rows($exec_sel) > 0)
    {
        $sql = pg_query("select medico_especialidade.esp_codigo,esp_nome from medico_especialidade, especialidade where medico_especialidade.esp_codigo=especialidade.esp_codigo and medico_especialidade.med_codigo='$med_codigo' and medico_especialidade.esp_codigo in (select esp_codigo
                                from usuarios_acessos
                                where usr_codigo = $id_login
                                and med_codigo = $med_codigo
                                and uni_codigo = $uni_codigo)");
    } else {
        $sql = pg_query("select medico_especialidade.esp_codigo,esp_nome from medico_especialidade, especialidade where medico_especialidade.esp_codigo=especialidade.esp_codigo and medico_especialidade.med_codigo='$med_codigo'");
    }
	  while($esp=pg_fetch_array($sql)) {
	   echo ($esp[esp_codigo]==$esp_codigo)?"<option value=$PHP_SELF?id_login=$id_login&med_codigo=$med_codigo&uni_codigo=$uni_codigo=esp_codigo=$esp[esp_codigo] selected>$esp[esp_nome]</option>":"<option value=$PHP_SELF?id_login=$id_login&med_codigo=$med_codigo&uni_codigo=$uni_codigo&esp_codigo=$esp[esp_codigo]>$esp[esp_nome]</option>";
	  }
	echo "</select></td>
              </tr>
	      </table>
             <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
              <tr>
		<td width=96>&nbsp;</td>
		<td width=5 align=right>Data</td>
		<td width=15><input type=text name=age_data class='boxn' size='12' id='data' maxlength='10' onKeypress=\"return Ajusta_Data(this, event);\" value=".(!$_POST[age_data] ? date("d/m/Y") :$_POST[age_data])." onfocus='atualizar(this)'>
	        <td width=25><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/calendario.png onclick=\"abrirCalendario('data');return false;\"></td>
		<td width=10 align=right>&nbsp;&nbsp;&nbsp;&nbsp;Hora</td>
		<td width=70>";
        $age_data_f = explode("/",$age_data);
        $age_data_f = $age_data_f[2].$age_data_f[1].$age_data_f[0];
        
		$sql = "SELECT DISTINCT gra_hora_ini FROM grade_medico 
							WHERE esp_codigo = $esp_codigo AND med_codigo = $med_codigo AND uni_codigo = $uni_codigo and gra_data ='". ($age_data ? $age_data_f : date("Y-m-d"))."'  ORDER BY gra_hora_ini";
        
		$exec_sql = pg_query($sql);
		echo "<select name=hora id=hora class=box>";
				
				while($linha = pg_fetch_array($exec_sql))
				{
					if($linha[0] == $hora)
					{
						echo "<option value=$linha[0] selected>$linha[0]</option>";
					} else {
						echo "<option value=$linha[0]>$linha[0]</option>";
					}
				}
				
			echo "</select>
		</td>
		<td width=108><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/listarpacientes_on.jpg onclick=\"return verificar();\"></td>
		<td>&nbsp;</td>
	      </tr>
	     </table>
             <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
              <tr>
		<th>PACIENTES ";
		if($hora)
		{
			echo "DAS ".$hora;
		}
		echo "</th>
		<td align=right><div id='total_pacientes'></div><br><font color=blue><b>Recepcionado </b></font>&nbsp;&nbsp;&nbsp;<font color=green><b>Atendido </b></font>&nbsp;&nbsp;&nbsp;<font color=orange><b>Transferido  </b></font>&nbsp;&nbsp;&nbsp;<font color=red><b>Faltoso </b></font>&nbsp;&nbsp;&nbsp;<font color=purple><b>M&eacute;dico Faltou</b></font>&nbsp;&nbsp;&nbsp;Agendado</td>
	      </tr></form>
	     </table>	   

          </td>
         </tr>
        </table>";
 echo "<table width=733 align=center cellspacing=0 cellpadding=4 border=0>
       <tr bgcolor=cccccc>
	 <td width=80 style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-color:909090'><font color=red>C&oacute;digo Pac.</font></td>
	 <td width=200 style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-color:909090'><font color=red>Paciente</font></td>
	 <td width=30 style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-color:909090'><font color=red>Idade</font></td>
	 <td width=120 style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-color:909090'><font color=red>M&atilde;e</font></td>
	 <td width=70 style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>Recep&ccedil;&atilde;o</font></td>
	<td width=70 style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>Atendido</font></td>
	<td width=70 style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>Municipio</font></td>
	<td width=70 style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>Transferir</font></td>
	<td width=70 style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>R</font></td>
	<td width=70 style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>T</font></td>
	<td width=70 style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>F</font></td>
	<td width=70 style='border-bottom:1px solid;border-top:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'><font color=red>M</font></td>
	</tr>";

if($acao=="mostra_age") {
if($age_data=="") {
 $age_data = date("d/m/Y");
}

$dtn = explode("/",$age_data);
 $grm_data = "$dtn[2]-$dtn[1]-$dtn[0]";

	if(!empty($hora))
	{
		$andHora = " and age_hora = '$hora' ";
	}

  $sql = pg_query("select *from agendamento where age_data='$grm_data' and med_codigo = '$med_codigo' and esp_codigo = '$esp_codigo' and uni_codigo = '$uni_codigo' $andHora order by age_timestamp, age_codigo");
  
  // Gambiarra para escrever o total dos pacientes, sem ter que botar a query aqui em cima =D
  echo "<script>document.getElementById('total_pacientes').innerHTML = '<b>TOTAL DE PACIENTES: ".pg_num_rows($sql)."</b>';</script>";
  
if(pg_num_rows($sql)=="0") {
   echo "<tr>
	   <td colspan=11 bgcolor=f9f9f9 style='border-bottom:1px solid' align=center>Nenhum paciente a recepcionar nesta data.</td>
	 </tr>";
}

//"
    while($row=pg_fetch_array($sql))
	{
		$pac=pg_fetch_array(pg_query("select * from usuario where usu_codigo='$row[usu_codigo]'"));
		if($row[age_atendido] == "S" && $row[age_falta_medico] != "M")
		{
			$array_s[] = array ($row[age_codigo], "S", $row[usu_codigo]);
		} else if($row[age_atendido] == "N" && $row[age_falta_medico] != "M") {
			$array_n[] = array ($row[age_codigo], "N", $row[usu_codigo]);
		} else if($row[age_atendido] == "F" && $row[age_falta_medico] != "M") {
			$array_f[] = array ($row[age_codigo], "F", $row[usu_codigo]);
		} else if($row[age_atendido] == "T" && $row[age_falta_medico] != "M") {
			$array_t[] = array ($row[age_codigo], "T", $row[usu_codigo]);
		} else if($row[age_atendido] == "A" && $row[age_falta_medico] != "M") {
			$array_a[] = array ($row[age_codigo], "A", $row[usu_codigo]);
		} else if($row[age_falta_medico] == "M") {
			$array_m[] = array ($row[age_codigo], "M", $row[usu_codigo]);
		}
	}
	
	
	$dia_hoje = date("Y-m-d");
	
	for($i = 0; $i < count($array_n); $i++)
	{
		$select = "select * from agendamento where age_data='$grm_data' and med_codigo = '$med_codigo' and esp_codigo = '$esp_codigo' and uni_codigo = '$uni_codigo' and age_codigo = {$array_n[$i][0]} order by age_timestamp, age_codigo";
		$sql = pg_query($select);
		$row = pg_fetch_array($sql);
		/*for($j = 0; $j < count($array_n[$i]); $j++)
		{*/
			$busca = "select * from usuario where usu_codigo='{$array_n[$i][2]}'";
			$pac=pg_fetch_array(pg_query($busca));
			$calcdt=date("Y");
			$strip=explode("-",$pac[usu_datanasc]);
			$result_idade=($calcdt-$strip[0]);
			$bold_font_open=""; $bold_font_close="";
		
			echo "<tr style='cursor: pointer !important;text-transform:upperCase;' bgcolor=ffffff onmouseover=\"javascript:style.backgroundColor='#EDF0F8';this.style.cursor='pointer';\" onmouseout=\"javascript:style.backgroundColor='#ffffff'\">
			<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data','$hora');\" width=10%>$bold_font_open $row[usu_codigo] $bold_font_close</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data','$hora');\" width=30%>$bold_font_open $pac[usu_nome] $bold_font_close</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data','$hora');\" width=18%>$bold_font_open ";echo verIdade("$pac[usu_datanasc]");echo " $bold_font_close</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data','$hora');\" width=30%>$bold_font_open $pac[usu_mae] $bold_font_close</td>";
			echo "<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data','$hora');\">";
				echo $bold_font_open ."&nbsp;". $bold_font_close;
			echo "</td>";
			echo "<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data','$hora');\">";
				echo $bold_font_open ."&nbsp;". $bold_font_close;
			echo "</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data','$hora');\">$bold_font_open". strtoupper($pac[usu_end_cidade]) ." $bold_font_close</td>";
			if($grm_data >= $dia_hoje)
			{
			echo "<td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'>
				<input type=image src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/transferir_on.jpg' onclick=\"transferir('transferirPaciente.php?id_login=$id_login&usu_codigo=$row[usu_codigo]&age_tipo=$row[age_tipo]&age_paciente=$row[age_paciente]&age_item=$row[age_item]&esp_codigo=$row[esp_codigo]&age_valor_proc=$row[age_valor_proc]&agt_codigo=$row[agt_codigo]&uni_codigo=$row[uni_codigo]&med_codigo=$row[med_codigo]&age_codigo=$row[age_codigo]&usu_codigo=$row[usu_codigo]', 'transferir_conteudo');return false;\">
			</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'>
				<input type=image src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/r_on.jpg' onclick=\"hotkey2(118,$row[age_codigo],$uni_codigo,$esp_codigo,$med_codigo,'$age_data',$id_login,'$hora');return false;\">
			</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'>
				<input type=image src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/t_on.jpg' onclick=\"hotkey2(120,$row[age_codigo],$uni_codigo,$esp_codigo,$med_codigo,'$age_data',$id_login,'$hora');return false;\">
			</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'>
				<input type=image src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/f_on.jpg' onclick=\"hotkey2(119,$row[age_codigo],$uni_codigo,$esp_codigo,$med_codigo,'$age_data',$id_login,'$hora');return false;\">
			</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090' width=80px>";
			if($row[age_falta_medico] == "M")
			{
				echo "<input id=link type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/m_on.jpg onclick=\"salvarFaltaMedico($med_codigo, $row[age_codigo], $id_login, 'retirar');return false;\"></td>";
			} else {
				echo "<input id=link type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/m_on.jpg onclick=\"salvarFaltaMedico($med_codigo, $row[age_codigo], $id_login, 'salvar');return false;\"></td>";
			}
			} else {
				echo "<td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090' width=80px>&nbsp;</td><td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090' width=80px>&nbsp;</td><td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090' width=80px>&nbsp;</td><td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090' width=80px>&nbsp;</td><td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090' width=80px>&nbsp;</td>";
			}
			echo "</tr>";
		//}
	}
	if(!empty($array_n))
	{
		echo "<tr><td colspan=13 style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090;font-weight:bold'>";
			echo count($array_n)." paciente(s) n&atilde;o recepcionado(s).";
		echo "</td></tr>";
	}
	for($i = 0; $i < count($array_s); $i++)
	{
		$select = "select * from agendamento where age_data='$grm_data' and med_codigo = '$med_codigo' and esp_codigo = '$esp_codigo' and uni_codigo = '$uni_codigo' and age_codigo = {$array_s[$i][0]} order by age_timestamp, age_codigo";
		$sql = pg_query($select);
		$row = pg_fetch_array($sql);
		
		$data = explode(" ", $row[age_timestamp]);
		$dat = explode("-", $data[0]);
		$da = $dat[2]."/".$dat[1]."/".$dat[0];
		
		$data_atend = explode(" ", $row[age_data_atend]);
		$dat_atend = explode("-", $data_atend[0]);
		$da_atend = $dat_atend[2]."/".$dat_atend[1]."/".$dat_atend[0];

		/*for($j = 0; $j < count($array_n[$i]); $j++)
		{*/
			$busca = "select * from usuario where usu_codigo='{$array_s[$i][2]}'";
			$pac=pg_fetch_array(pg_query($busca));
			$calcdt=date("Y");
			$strip=explode("-",$pac[usu_datanasc]);
			$result_idade=($calcdt-$strip[0]);
			$bold_font_open="<font color=blue><b>"; $bold_font_close="</font></b>";
			echo "<tr style='cursor: pointer;text-transform:upperCase;' bgcolor=ffffff onmouseover=\"javascript:style.backgroundColor='#EDF0F8'\" onmouseout=\"javascript:style.backgroundColor='#ffffff'\">
			<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data','$hora');\" width=10%>$bold_font_open $row[usu_codigo] $bold_font_close</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data','$hora');\" width=30%>$bold_font_open $pac[usu_nome] $bold_font_close</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data','$hora');\" width=18%>$bold_font_open ";echo verIdade("$pac[usu_datanasc]");echo " $bold_font_close</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data','$hora');\" width=30%>$bold_font_open $pac[usu_mae] $bold_font_close</td>";
			echo "<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data','$hora');\">";
				echo $bold_font_open .($row[age_timestamp] != '' ? substr($data[1], 0, 8) : null). $bold_font_close;
			echo "</td>";
			echo "<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data','$hora');\">";
				echo $bold_font_open .($row[age_data_atend] != '' ? substr($data_atend[1], 0, 8) : null). $bold_font_close;
			echo "</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data','$hora');\">$bold_font_open". strtoupper($pac[usu_end_cidade]) ." $bold_font_close</td>";
			if($grm_data >= $dia_hoje)
			{
			echo "<td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'>
				<input type=image src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/transferir_on.jpg' onclick=\"transferir('transferirPaciente.php?id_login=$id_login&usu_codigo=$row[usu_codigo]&age_tipo=$row[age_tipo]&age_paciente=$row[age_paciente]&age_item=$row[age_item]&esp_codigo=$row[esp_codigo]&age_valor_proc=$row[age_valor_proc]&agt_codigo=$row[agt_codigo]&uni_codigo=$row[uni_codigo]&med_codigo=$row[med_codigo]&age_codigo=$row[age_codigo]&usu_codigo=$row[usu_codigo]', 'transferir_conteudo');return false;\">
			</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'>
				<input type=image src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/r_on.jpg' onclick=\"hotkey2(118,$row[age_codigo],$uni_codigo,$esp_codigo,$med_codigo,'$age_data',$id_login,'$hora');return false;\">
			</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'>
				<input type=image src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/t_on.jpg' onclick=\"hotkey2(120,$row[age_codigo],$uni_codigo,$esp_codigo,$med_codigo,'$age_data',$id_login,'$hora');return false;\">
			</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'>
				<input type=image src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/f_on.jpg' onclick=\"hotkey2(119,$row[age_codigo],$uni_codigo,$esp_codigo,$med_codigo,'$age_data',$id_login,'$hora');return false;\">
			</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090' width=80px>";
			if($row[age_falta_medico] == "M")
			{
				echo "<input id=link type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/m_on.jpg onclick=\"salvarFaltaMedico($med_codigo, $row[age_codigo], $id_login, 'retirar');return false;\"></td>";
			} else {
				echo "<input id=link type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/m_on.jpg onclick=\"salvarFaltaMedico($med_codigo, $row[age_codigo], $id_login, 'salvar');return false;\"></td>";
			}
			} else {
				echo "<td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090' width=80px>&nbsp;</td><td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090' width=80px>&nbsp;</td><td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090' width=80px>&nbsp;</td><td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090' width=80px>&nbsp;</td><td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090' width=80px>&nbsp;</td>";
			}
			echo "</tr>";
		//}
	}
	if(!empty($array_s))
	{
		echo "<tr><td colspan=13 style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090;font-weight:bold'>";
			echo count($array_s)." paciente(s) recepcionado(s).";
		echo "</td></tr>";
	}
	for($i = 0; $i < count($array_f); $i++)
	{
		$select = "select * from agendamento where age_data='$grm_data' and med_codigo = '$med_codigo' and esp_codigo = '$esp_codigo' and uni_codigo = '$uni_codigo' and age_codigo = {$array_f[$i][0]} order by age_timestamp, age_codigo";
		$sql = pg_query($select);
		$row = pg_fetch_array($sql);
		
		$data = explode(" ", $row[age_timestamp]);
		$dat = explode("-", $data[0]);
		$da = $dat[2]."/".$dat[1]."/".$dat[0];
		
		$data_atend = explode(" ", $row[age_data_atend]);
		$dat_atend = explode("-", $data_atend[0]);
		$da_atend = $dat_atend[2]."/".$dat_atend[1]."/".$dat_atend[0];
		/*for($j = 0; $j < count($array_n[$i]); $j++)
		{*/
			$busca = "select * from usuario where usu_codigo='{$array_f[$i][2]}'";
			$pac=pg_fetch_array(pg_query($busca));
			$calcdt=date("Y");
			$strip=explode("-",$pac[usu_datanasc]);
			$result_idade=($calcdt-$strip[0]);
			$bold_font_open="<font color=red><b>"; $bold_font_close="</font></b>"; 
			echo "<tr style='cursor: hand;text-transform:upperCase;' bgcolor=ffffff onmouseover=\"javascript:style.backgroundColor='#EDF0F8'\" onmouseout=\"javascript:style.backgroundColor='#ffffff'\">
			<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data','$hora');\" width=10%>$bold_font_open $row[usu_codigo] $bold_font_close</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data','$hora');\" width=30%>$bold_font_open $pac[usu_nome] $bold_font_close</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data','$hora');\" width=18%>$bold_font_open ";echo verIdade("$pac[usu_datanasc]");echo " $bold_font_close</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data','$hora');\" width=30%>$bold_font_open $pac[usu_mae] $bold_font_close</td>";
			echo "<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data','$hora');\">";
				echo $bold_font_open .($row[age_timestamp] != '' ? substr($data[1], 0, 8) : null). $bold_font_close;
			echo "</td>";
			echo "<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data','$hora');\">";
				echo $bold_font_open .($row[age_data_atend] != '' ? substr($data_atend[1], 0, 8) : null). $bold_font_close;
			echo "</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data');\">$bold_font_open". strtoupper($pac[usu_end_cidade]) ." $bold_font_close</td>";
			if($grm_data >= $data_hoje)
			{
			echo "<td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'>
				<input type=image src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/transferir_on.jpg' onclick=\"transferir('transferirPaciente.php?id_login=$id_login&usu_codigo=$row[usu_codigo]&age_tipo=$row[age_tipo]&age_paciente=$row[age_paciente]&age_item=$row[age_item]&esp_codigo=$row[esp_codigo]&age_valor_proc=$row[age_valor_proc]&agt_codigo=$row[agt_codigo]&uni_codigo=$row[uni_codigo]&med_codigo=$row[med_codigo]&age_codigo=$row[age_codigo]&usu_codigo=$row[usu_codigo]', 'transferir_conteudo');return false;\">
			</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'>
				<input type=image src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/r_on.jpg' onclick=\"hotkey2(118,$row[age_codigo],$uni_codigo,$esp_codigo,$med_codigo,'$age_data',$id_login,'$hora');return false;\">
			</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'>
				<input type=image src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/t_on.jpg' onclick=\"hotkey2(120,$row[age_codigo],$uni_codigo,$esp_codigo,$med_codigo,'$age_data',$id_login,'$hora');return false;\">
			</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'>
				<input type=image src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/f_on.jpg' onclick=\"hotkey2(119,$row[age_codigo],$uni_codigo,$esp_codigo,$med_codigo,'$age_data',$id_login,'$hora');return false;\">
			</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090' width=80px>";
			if($row[age_falta_medico] == "M")
			{
				echo "<input id=link type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/m_on.jpg onclick=\"salvarFaltaMedico($med_codigo, $row[age_codigo], $id_login, 'retirar');return false;\"></td>";
			} else {
				echo "<input id=link type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/m_on.jpg onclick=\"salvarFaltaMedico($med_codigo, $row[age_codigo], $id_login, 'salvar');return false;\"></td>";
			}
			} else {
				echo "<td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090' width=80px>&nbsp;</td><td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090' width=80px>&nbsp;</td><td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090' width=80px>&nbsp;</td><td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090' width=80px>&nbsp;</td><td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090' width=80px>&nbsp;</td>";
			}
			echo "</tr>";
		//}
	}
	if(!empty($array_f))
	{
		echo "<tr><td colspan=13 style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090;font-weight:bold'>";
			echo count($array_f)." paciente(s) faltoso(s).";
		echo "</td></tr>";
	}
	for($i = 0; $i < count($array_t); $i++)
	{
		$select = "select * from agendamento where age_data='$grm_data' and med_codigo = '$med_codigo' and esp_codigo = '$esp_codigo' and uni_codigo = '$uni_codigo' and age_codigo = {$array_t[$i][0]} order by age_timestamp, age_codigo";
		$sql = pg_query($select);
		$row = pg_fetch_array($sql);
		
		$data = explode(" ", $row[age_timestamp]);
		$dat = explode("-", $data[0]);
		$da = $dat[2]."/".$dat[1]."/".$dat[0];
		
		$data_atend = explode(" ", $row[age_data_atend]);
		$dat_atend = explode("-", $data_atend[0]);
		$da_atend = $dat_atend[2]."/".$dat_atend[1]."/".$dat_atend[0];
		/*for($j = 0; $j < count($array_n[$i]); $j++)
		{*/
			$busca = "select * from usuario where usu_codigo='{$array_t[$i][2]}'";
			$pac=pg_fetch_array(pg_query($busca));
			$calcdt=date("Y");
			$strip=explode("-",$pac[usu_datanasc]);
			$result_idade=($calcdt-$strip[0]);
			$bold_font_open="<font color=orange><b>"; $bold_font_close="</font></b>";
			echo "<tr style='cursor: hand;text-transform:upperCase;' bgcolor=ffffff onmouseover=\"javascript:style.backgroundColor='#EDF0F8'\" onmouseout=\"javascript:style.backgroundColor='#ffffff'\">
			<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data','$hora');\" width=10%>$bold_font_open $row[usu_codigo] $bold_font_close</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data','$hora');\" width=30%>$bold_font_open $pac[usu_nome] $bold_font_close</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data','$hora');\" width=18%>$bold_font_open ";echo verIdade("$pac[usu_datanasc]");echo " $bold_font_close</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data','$hora');\" width=30%>$bold_font_open $pac[usu_mae] $bold_font_close</td>";
			echo "<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data','$hora');\">";
				echo $bold_font_open .($row[age_timestamp] != '' ? substr($data[1], 0, 8) : null). $bold_font_close;
			echo "</td>";
			echo "<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data','$hora');\">";
				echo $bold_font_open .($row[age_data_atend] != '' ? substr($data_atend[1], 0, 8) : null). $bold_font_close;
			echo "</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data','$hora');\">$bold_font_open". strtoupper($pac[usu_end_cidade]) ." $bold_font_close</td>";
			if($grm_data >= $dia_hoje)
			{
			echo "<td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'>
				<input type=image src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/transferir_on.jpg' onclick=\"transferir('transferirPaciente.php?id_login=$id_login&usu_codigo=$row[usu_codigo]&age_tipo=$row[age_tipo]&age_paciente=$row[age_paciente]&age_item=$row[age_item]&esp_codigo=$row[esp_codigo]&age_valor_proc=$row[age_valor_proc]&agt_codigo=$row[agt_codigo]&uni_codigo=$row[uni_codigo]&med_codigo=$row[med_codigo]&age_codigo=$row[age_codigo]&usu_codigo=$row[usu_codigo]', 'transferir_conteudo');return false;\">
			</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'>
				<input type=image src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/r_on.jpg' onclick=\"hotkey2(118,$row[age_codigo],$uni_codigo,$esp_codigo,$med_codigo,'$age_data',$id_login,'$hora');return false;\">
			</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'>
				<input type=image src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/t_on.jpg' onclick=\"hotkey2(120,$row[age_codigo],$uni_codigo,$esp_codigo,$med_codigo,'$age_data',$id_login,'$hora');return false;\">
			</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'>
				<input type=image src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/f_on.jpg' onclick=\"hotkey2(119,$row[age_codigo],$uni_codigo,$esp_codigo,$med_codigo,'$age_data',$id_login,'$hora');return false;\">
			</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090' width=80px>";
			if($row[age_falta_medico] == "M")
			{
				echo "<input id=link type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/m_on.jpg onclick=\"salvarFaltaMedico($med_codigo, $row[age_codigo], $id_login, 'retirar');return false;\"></td>";
			} else {
				echo "<input id=linreturn false;k type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/m_on.jpg onclick=\"salvarFaltaMedico($med_codigo, $row[age_codigo], $id_login, 'salvar');\"></td>";
			}
			} else {
				echo "<td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090' width=80px>&nbsp;</td><td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090' width=80px>&nbsp;</td><td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090' width=80px>&nbsp;</td><td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090' width=80px>&nbsp;</td><td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090' width=80px>&nbsp;</td>";
			}
			echo "</tr>";
		//}
	}
	if(!empty($array_t))
	{
		echo "<tr><td colspan=13 style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090;font-weight:bold'>";
			echo count($array_t)." paciente(s) transferido(s).";
		echo "</td></tr>";
	}
	for($i = 0; $i < count($array_m); $i++)
	{
		$select = "select * from agendamento where age_data='$grm_data' and med_codigo = '$med_codigo' and esp_codigo = '$esp_codigo' and uni_codigo = '$uni_codigo' and age_codigo = {$array_m[$i][0]} order by age_timestamp, age_codigo";
		$sql = pg_query($select);
		$row = pg_fetch_array($sql);
		
		$data = explode(" ", $row[age_timestamp]);
		$dat = explode("-", $data[0]);
		$da = $dat[2]."/".$dat[1]."/".$dat[0];
		
		$data_atend = explode(" ", $row[age_data_atend]);
		$dat_atend = explode("-", $data_atend[0]);
		$da_atend = $dat_atend[2]."/".$dat_atend[1]."/".$dat_atend[0];
		/*for($j = 0; $j < count($array_n[$i]); $j++)
		{*/
			$busca = "select * from usuario where usu_codigo='{$array_m[$i][2]}'";
			$pac=pg_fetch_array(pg_query($busca));
			$calcdt=date("Y");
			$strip=explode("-",$pac[usu_datanasc]);
			$result_idade=($calcdt-$strip[0]);
			$bold_font_open="<font color=purple><b>"; $bold_font_close="</font></b>";
			echo "<tr style='cursor: hand;text-transform:upperCase;' bgcolor=ffffff onmouseover=\"javascript:style.backgroundColor='#EDF0F8'\" onmouseout=\"javascript:style.backgroundColor='#ffffff'\">
			<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data','$hora');\" width=10%>$bold_font_open $row[usu_codigo] $bold_font_close</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data','$hora');\" width=30%>$bold_font_open $pac[usu_nome] $bold_font_close</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data','$hora');\" width=18%>$bold_font_open ";echo verIdade("$pac[usu_datanasc]");echo " $bold_font_close</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data','$hora');\" width=30%>$bold_font_open $pac[usu_mae] $bold_font_close</td>";
			echo "<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data','$hora');\">";
				echo $bold_font_open .($row[age_timestamp] != '' ? substr($data[1], 0, 8) : null). $bold_font_close;
			echo "</td>";
			echo "<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data','$hora');\">";
				echo $bold_font_open .($row[age_data_atend] != '' ? substr($data_atend[1], 0, 8) : null). $bold_font_close;
			echo "</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data','$hora');\">$bold_font_open". strtoupper($pac[usu_end_cidade]) ." $bold_font_close</td>";
			if($grm_data >= $data_hoje)
			{
			echo "<td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'>
				<input type=image src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/transferir_on.jpg' onclick=\"transferir('transferirPaciente.php?id_login=$id_login&usu_codigo=$row[usu_codigo]&age_tipo=$row[age_tipo]&age_paciente=$row[age_paciente]&age_item=$row[age_item]&esp_codigo=$row[esp_codigo]&age_valor_proc=$row[age_valor_proc]&agt_codigo=$row[agt_codigo]&uni_codigo=$row[uni_codigo]&med_codigo=$row[med_codigo]&age_codigo=$row[age_codigo]&usu_codigo=$row[usu_codigo]', 'transferir_conteudo');return false;\">
			</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'>
				<input type=image src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/r_on.jpg' onclick=\"hotkey2(118,$row[age_codigo],$uni_codigo,$esp_codigo,$med_codigo,'$age_data',$id_login,'$hora');return false;\">
			</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'>
				<input type=image src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/t_on.jpg' onclick=\"hotkey2(120,$row[age_codigo],$uni_codigo,$esp_codigo,$med_codigo,'$age_data',$id_login,'$hora');return false;\">
			</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'>
				<input type=image src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/f_on.jpg' onclick=\"hotkey2(119,$row[age_codigo],$uni_codigo,$esp_codigo,$med_codigo,'$age_data',$id_login,'$hora');return false;\">
			</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090' width=80px>";
			if($row[age_falta_medico] == "M")
			{
				echo "<input id=link type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/m_on.jpg onclick=\"salvarFaltaMedico($med_codigo, $row[age_codigo], $id_login, 'retirar');return false;\"></td>";
			} else {
				echo "<input id=link type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/m_on.jpg onclick=\"salvarFaltaMedico($med_codigo, $row[age_codigo], $id_login, 'salvar');return false;\"></td>";
			}
			} else {
				echo "<td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090' width=80px>&nbsp;</td><td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090' width=80px>&nbsp;</td><td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090' width=80px>&nbsp;</td><td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090' width=80px>&nbsp;</td><td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090' width=80px>&nbsp;</td>";
			}
			echo "</tr>";
		//}
	}
	if(!empty($array_m))
	{
		echo "<tr><td colspan=13 style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090;font-weight:bold'>";
			echo count($array_m)." consulta(s) com falta do m&eacute;dico.";
		echo "</td></tr>";
	}
	for($i = 0; $i < count($array_a); $i++)
	{
		$select = "select * from agendamento where age_data='$grm_data' and med_codigo = '$med_codigo' and esp_codigo = '$esp_codigo' and uni_codigo = '$uni_codigo' and age_codigo = {$array_a[$i][0]} order by age_timestamp, age_codigo";
		$sql = pg_query($select);
		$row = pg_fetch_array($sql);
		
		$data = explode(" ", $row[age_timestamp]);
		$dat = explode("-", $data[0]);
		$da = $dat[2]."/".$dat[1]."/".$dat[0];
		
		$data_atend = explode(" ", $row[age_data_atend]);
		$dat_atend = explode("-", $data_atend[0]);
		$da_atend = $dat_atend[2]."/".$dat_atend[1]."/".$dat_atend[0];
		/*for($j = 0; $j < count($array_n[$i]); $j++)
		{*/
			$busca = "select * from usuario where usu_codigo='{$array_a[$i][2]}'";
			$pac=pg_fetch_array(pg_query($busca));
			$calcdt=date("Y");
			$strip=explode("-",$pac[usu_datanasc]);
			$result_idade=($calcdt-$strip[0]);
			$bold_font_open="<font color=green><b>"; $bold_font_close="</font></b>";
			echo "<tr style='cursor: hand;text-transform:upperCase;' bgcolor=ffffff onmouseover=\"javascript:style.backgroundColor='#EDF0F8'\" onmouseout=\"javascript:style.backgroundColor='#ffffff'\">
			<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data','$hora');\" width=10%>$bold_font_open $row[usu_codigo] $bold_font_close</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data','$hora');\" width=30%>$bold_font_open $pac[usu_nome] $bold_font_close</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data','$hora');\" width=18%>$bold_font_open ";echo verIdade("$pac[usu_datanasc]");echo " $bold_font_close</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data','$hora');\" width=30%>$bold_font_open $pac[usu_mae] $bold_font_close</td>";
			echo "<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data','$hora');\">";
				echo $bold_font_open .($row[age_timestamp] != '' ? substr($data[1], 0, 8) : null). $bold_font_close;
			echo "</td>";
			echo "<td style='border-bottom:1px solid;border-left:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data','$hora');\">";
				echo $bold_font_open .($row[age_data_atend] != '' ? substr($data_atend[1], 0, 8) : null). $bold_font_close;
			echo "</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090' onclick=\"msg($id_login,'$row[age_codigo]','$uni_codigo','$esp_codigo','$med_codigo','$age_data','$hora');\">$bold_font_open". strtoupper($pac[usu_end_cidade]) ." $bold_font_close</td>";
			/*if($grm_data >= $dia_hoje)
			{
			echo "<td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'>
				<input type=image src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/transferir_on.jpg' onclick=\"transferir('transferirPaciente.php?id_login=$id_login&usu_codigo=$row[usu_codigo]&age_tipo=$row[age_tipo]&age_paciente=$row[age_paciente]&age_item=$row[age_item]&esp_codigo=$row[esp_codigo]&age_valor_proc=$row[age_valor_proc]&agt_codigo=$row[agt_codigo]&uni_codigo=$row[uni_codigo]&med_codigo=$row[med_codigo]&age_codigo=$row[age_codigo]&usu_codigo=$row[usu_codigo]', 'transferir_conteudo')\">
			</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'>
				<input type=image src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/r_on.jpg' onclick=\"hotkey2(118,$row[age_codigo],$uni_codigo,$esp_codigo,$med_codigo,'$age_data',$id_login)\">
			</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'>
				<input type=image src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/t_on.jpg' onclick=\"hotkey2(120,$row[age_codigo],$uni_codigo,$esp_codigo,$med_codigo,'$age_data',$id_login)\">
			</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090'>
				<input type=image src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/f_on.jpg' onclick=\"hotkey2(119,$row[age_codigo],$uni_codigo,$esp_codigo,$med_codigo,'$age_data',$id_login)\">
			</td>
			<td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090' width=80px>";
			/*if($row[age_falta_medico] == "M")
			{
				echo "<input id=link type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/m_on.jpg onclick=\"salvarFaltaMedico($med_codigo, $row[age_codigo], $id_login, 'retirar');\"></td>";
			} else {
				echo "<input id=link type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/m_on.jpg onclick=\"salvarFaltaMedico($med_codigo, $row[age_codigo], $id_login, 'salvar');\"></td>";
			}*/
			//} else {
				echo "<td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090' width=80px>&nbsp;</td><td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090' width=80px>&nbsp;</td><td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090' width=80px>&nbsp;</td><td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090' width=80px>&nbsp;</td><td style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090' width=80px>&nbsp;</td>";
			//}
			echo "</tr>";
		//}
	}
	if(!empty($array_a))
	{
		echo "<tr><td colspan=13 style='border-bottom:1px solid;border-left:1px solid;border-right:1px solid;border-color:909090;font-weight:bold'>";
			echo count($array_a)." paciente(s) atendido(s).";
		echo "</td></tr>";
	}
	echo "</table><br>";
// }
}
	print "</body></html>";
?>
</fieldset>
