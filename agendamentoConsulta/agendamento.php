<?php
	session_start(); 
?>
<link href="estiloAgendamento.css" rel="stylesheet" type="text/css">
<link href="../estilo.css" rel="stylesheet" type="text/css">
<script language="JavaScript" src="../atalhos.js"></script>
<!--<script language="JavaScript" type="text/javascript" src="../funcoes.js"></script>-->
<script language="JavaScript" type="text/javascript" src="../g_ajax.js"></script>
<script language="JavaScript" type="text/javascript" src="../ajax_motor.js"></script>
<script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.7.2.custom.min.js"></script>
<script type="text/javascript" src="js/calendario.js"></script>
<link rel="stylesheet" type=text/css href="js/jquery-ui-1.7.2.custom.css" />




<!--SCRIPT DO JQUERY  -->

<script>

function lucho( )
{
	var A  = new Array;
	for( var i = 0; i < arguments.length; i++ )
	{
		var obj = document.getElementById( arguments[i] );
		if( ! obj )
		{
			alert("O elemento '" + arguments[i] + "' nao foi encontrado !");
			return null;
		}
		A.push( obj );
	}
	return ( A.length == 1 ? A[0] : A ); 
}

function buscar_medicos(valor, acao)
        {				
                url = "../buscar_medicos.php?palavra="+valor+"&acao="+acao;
                ajax_tudo(url, popular_nome);
                lucho('lista_nomes').style.display = '';
                lucho('table_nomes').innerHTML = '';
                lucho("lista_carregando").style.display = '';
        }

        
        function popular_nome(txt)
        {
                try {
                        t = lucho('table_nomes');
                        lucho("lista_carregando").style.display = 'none';
                        t.innerHTML = txt;
                } catch(e) {
                        alert(e);
                }
        }
function passar_medico(codigo, nome)
        {
        		
                lucho("pac_codigo").value = codigo;
                lucho("pac_nome").value = nome;

                
                if(document.getElementById("pac_prontuario") != null)
                {
                        lucho("pac_prontuario").value = prontuario;
						/*if( at_iframe_esq != null )
							at_iframe_esq();*/
                }
                
                lucho('lista_nomes').style.display = 'none';
                lucho('pac_nome').focus();
				ajaxMedico();
				
		}
		function chamaPaciente(id){
			
			url = "listPacientesAg.php?id="+id;
			window.open(url, null, "height=600,width=800,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");	
			
		}

		
		function addPacientesAg(codigo, nome,id)
		{
		
			$('pac_codigo').value 		= codigo;
			$('pac_nome').value 		= nome;
		    url = "agendaPessoa.php?codigo="+codigo+"&nome="+nome+"&id="+id;
			ajax_tudo(url,inserirNaColuna);
		}
		
		function inserirNaColuna(txt)
		{
			var escreve = txt.split('/');
			
			document.getElementById('nomePessoa'+escreve[1]).innerHTML = escreve[0];
			var col1 = document.getElementById('col1'+escreve[1]);
			var col2 = document.getElementById('col2'+escreve[1]);
			var col3 = document.getElementById('col3'+escreve[1]);
			var col4 = document.getElementById('col4'+escreve[1]);
			var col5 = document.getElementById('col5'+escreve[1]);
			var col6 = document.getElementById('col6'+escreve[1]);
		
			col1.setAttribute("class","celulaAgendada");
			col2.setAttribute("class","celulaAgendada");
			col3.setAttribute("class","celulaAgendada");
			col4.setAttribute("class","celulaAgendada");
			col5.setAttribute("class","celulaAgendada");
			col6.setAttribute("class","celulaAgendada");
			
			
		}
		function mudaCor(muda)
		{
			var col1 = document.getElementById('col1'+muda);
			var col2 = document.getElementById('col2'+muda);
			var col3 = document.getElementById('col3'+muda);
			var col4 = document.getElementById('col4'+muda);
			var col5 = document.getElementById('col5'+muda);
			var col6 = document.getElementById('col6'+muda);
		
			col1.setAttribute("class","celulaReservada");
			col2.setAttribute("class","celulaReservada");
			col3.setAttribute("class","celulaReservada");
			col4.setAttribute("class","celulaReservada");
			col5.setAttribute("class","celulaReservada");
			col6.setAttribute("class","celulaReservada");	
		}
	function mudaCorTransf(mudaT)
		{
			var col1 = document.getElementById('col1'+mudaT);
			var col2 = document.getElementById('col2'+mudaT);
			var col3 = document.getElementById('col3'+mudaT);
			var col4 = document.getElementById('col4'+mudaT);
			var col5 = document.getElementById('col5'+mudaT);
			var col6 = document.getElementById('col6'+mudaT);
		
			col1.setAttribute("class","celulaTransferida");
			col2.setAttribute("class","celulaTransferida");
			col3.setAttribute("class","celulaTransferida");
			col4.setAttribute("class","celulaTransferida");
			col5.setAttribute("class","celulaTransferida");
			col6.setAttribute("class","celulaTransferida");	
		}
		function mudaCorFalta(mudaF)
		{
			var col1 = document.getElementById('col1'+mudaF);
			var col2 = document.getElementById('col2'+mudaF);
			var col3 = document.getElementById('col3'+mudaF);
			var col4 = document.getElementById('col4'+mudaF);
			var col5 = document.getElementById('col5'+mudaF);
			var col6 = document.getElementById('col6'+mudaF);
		
			col1.setAttribute("class","celulafalta");
			col2.setAttribute("class","celulafalta");
			col3.setAttribute("class","celulafalta");
			col4.setAttribute("class","celulafalta");
			col5.setAttribute("class","celulafalta");
			col6.setAttribute("class","celulafalta");	
		}
		function ajaxMedico(){
			var pac_codigo = document.getElementById('pac_codigo').value;
			var pac_nome = document.getElementById('pac_nome').value;
			url = "ajaxMedico.php?pac_nome="+pac_nome+"&pac_codigo="+pac_codigo;
			ajax_tudo(url,preencheMedico)
		}
		function preencheMedico(txt){
			document.getElementById('minhaDiv').innerHTML = txt;
		}

function pegaData()
{
	var data = document.getElementById('calendario').value;
	document.getElementById('umaData').innerHTML = data;	
}
 function chamaAgenda() {
	 document.getElementById('externa').style.display = '';
    $("p").show("slow");
	document.getElementById('imagem').style.display = "none";
	mostra();
    }
function mostra() {
  $("div:eq(0)").show("fast", function () {
    /* use callee so don't have to name the function */
    $(this).next("div").show("fast", arguments.callee);
  });
}

//
      function trocar_cor(id, id2)
        {
                campo = lucho(id);
                campo.style.background = "#ABCDEF";
                if(id2 != null)
                {
                        lucho(id2).style.display = '';
                }
        }
        
        function retirar_cor(id, id2)
        {
                campo = lucho(id);
                campo.style.background = "#FFFFFF";
                if(id2 != null)
                {
                        lucho(id2).style.display = 'none';
                }
        }
shortcut.add("Right",function() 
{
     add_dest();
});

shortcut.add("Left",function() 
{
     rem_dest();
});

shortcut.add("F2",function() {
 buscar_medicos(lucho('pac_nome'), 'buscar_medicos');return false;link_f7();
});
shortcut.add("F12", function(){
 var pac_codigo = document.form_msg.pac_codigo.value;
  window.open("exa_historico.php?id_login=<?=$id_login?>&usu_codigo="+pac_codigo,null,"height=460,width=800,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");
});

shortcut.add("F9",function() 
{
  window.open("../paciente_ficha.php?acao=form_add&type=c&id_login=$id_login&controle=1",null,"height=460,width=800,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");
});
</script>
<!-- <style>
      p { background:yellow; }
   </style>-->
<?
session_start();
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
//verauth($id_login);

include_once $_SESSION[root].$_SESSION[comum]."funcoes.inc.php";
cabecario();

echo "<div id='topo' class='topo'>";
///////////////////////////////////////////////////////////////


///////////////////////////////////////////////////////////////
//echo "<div id='manipulada' style='display:none'>";
echo"
<table border='0'>
	<tr>
		<td class='primeiroNome'>
			Agenda Dia : 
		</td>
		<td	class='segundoNome' width='110px'>
			<div id='umaData'>
			
			</div>
		</td>
		<td>
			<input type='image' name='calendario' id='calendario' value='' onchange='pegaData()' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/calendario.png' />
		</td>
	</tr>

	<tr>
		<td class='primeiroNome' width='110px'>
			<b>Cod.Medico:<b>
		</td>
		<td>
			<input type=text name='pac_codigo' id='pac_codigo' class=boxNumero readonly value='$pac[usu_codigo]'>
		</td>
		<td class='primeiroNome' width='90px'>
			<b>Medico:</b>
		</td>
		<td>
			<input type=text name=pac_nome id=pac_nome value='$pac[usu_nome]' class=boxTexto onkeyup=\"buscar_medicos(this.value);\" style=\"text-transform:uppercase;\" onkeypress=\"if(event.keyCode == 13)buscar_medicos(document.getElementById('pac_nome').value, 'buscar_medicos')\">
			<a href='#' onclick=\"buscar_medicos(lucho('pac_nome'), 'buscar_medicos');return false;link_f7()\"><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/localizar.jpg id=localizar align=absmiddle border=0></a>";
		echo divBuscaPaciente();
		echo"</td>               
	</tr>
	";
echo "
</table>";
echo"<div id='minhaDiv'>";

echo"</div>";

echo"
</div>	
";
echo "
<div id='externa' style='display: none'>
	<table border='0'>
		<tr>
			<td align='center'>	
				<div id='agenda' class='alpha'>
					<p style='display: none'>
					<table border='0'>
						<tr>
							<td class='celulaAgendada' align='center'>1</td>
							<td class='celulaAgendada'>VICTOR HUGO MARQUES CALDEIRA</td>
							<td class='celulaAgendada'>08:00</td>
							<td class='celulaDosBotoes'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/r_on.jpg'></td>
							<td class='celulaDosBotoes'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/t_on.jpg'></td>
							<td class='celulaDosBotoes'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/f_on.jpg'></td>
							
						</tr>";
						$cont = 1;
						$i = 0;
						while($limite < 5 )
						{
							
							$i = $i + 1;
						
							$cont = $cont + 1;
							echo"
								<tr>
									<td class='celulaParaAgendar' align='center' id='col1".$i."'>$cont</span></td>
									<td class='celulaParaAgendar' onclick=\"chamaPaciente('".$i."')\" id='col2".$i."'><div id='nomePessoa".$i."'> </div></td>
									<td class='celulaParaAgendar' id='col3".$i."'></td>
									<td class='celulaDosBotoes' id='col4".$i."' style='cursor:pointer;' onclick=\"mudaCor('".$i."')\">
										<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/r_off.jpg' id='R'>
									</td>
									<td class='celulaDosBotoes' id='col5".$i."' style='cursor:pointer' onclick=\"mudaCorTransf('".$i."')\">
										<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/t_off.jpg' id='T'>
									</td>
									<td class='celulaDosBotoes' id='col6".$i."' style='cursor:pointer' onclick=\"mudaCorFalta('".$i."')\">
										<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/f_off.jpg' id='F'>
									</td>
								</tr>";
							$limite++;
						}
						echo"</table>
						</div>
					<td>
				<tr>
			</table>
		</div>
		
		</p>
		<div id='imagem'>  
		</div>
		";
?>


