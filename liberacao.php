<link href="estilo.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="view.css" media="all">
<script type="text/javascript" src="view.js"></script>
<script type="text/javascript" src="calendar.js"></script>
<?
session_start();
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."config.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."json.inc.php"
?>
<script>
function pacientes(codigo,nome,nascimento,mae,cidade)
{
	document.getElementById('botaoPrograma').style.visibility = 'visible';
	document.getElementById('botao').style.display = '';
	document.getElementById('gridProdutos').style.display = 'none';
	document.getElementById('resposta').style.display = 'none';
	
	document.getElementById('pro_codigo').value = '';
	document.getElementById('pro_nome').value = '';
	document.getElementById('quantidade').value = '';
	document.getElementById('ite_consolidado').value = '';
	document.getElementById('posologia').value = '';
	document.getElementById('detalhes').value = '';
	document.getElementById('observacoes').value = '';
	
	document.getElementById('gridProdutos').innerHTML = '';
	
	document.getElementById('pac_nome').value=nome;
	document.getElementById('pac_codigo').value=codigo;
	document.getElementById('pac_nascimento').value=nascimento;
	document.getElementById('pac_mae').value=mae;
	document.getElementById('pac_cidade').value=cidade;
	
	document.getElementById('pac_nascimento').focus();
	
	//verificar_medicamento('pacientes');
	//setTimeout( 'verificar_medicamento();', 500 );
}
function buscar_dados_paciente(valor)
{
	url = "buscar_generico.php?tipo=dados_paciente&usu_prontuario="+valor;
	ajax_tudo(url, preencher_campo);
}

function preencher_campo(txt)
{
	if(txt != "vazio" && txt != undefined && txt != "")
	{
		txt = txt.split(";");
		document.getElementById('pac_codigo').value = txt[0];
		document.getElementById('pac_nome').value = txt[1];
		document.getElementById('pac_nascimento').value = txt[2];
		document.getElementById('pac_mae').value = txt[3];
		document.getElementById('pac_cidade').value = txt[4];
		//alert( 'prrenchar_campo' );
		verificar_medicamento( 'prrenchar_campo' );
	} else {
		document.getElementById('pac_codigo').value = "";
		document.getElementById('pac_nome').value = "NADA ENCONTRADO";
		document.getElementById('pac_nascimento').value = "";
		document.getElementById('pac_mae').value = "";
		document.getElementById('pac_cidade').value = "";
	}
	
}

function buscar_prontuario()
{
  pac_codigo = document.getElementById('pac_codigo').value;
  url = "buscar_generico.php?tipo=prontuario_paciente&usu_codigo="+pac_codigo;
  ajax_tudo(url, preencher_campo_prontuario);
}

function preencher_campo_prontuario(txt)
{
	if(txt != "vazio" && txt != undefined && txt != "")
	{
		document.getElementById('pac_prontuario').value = txt;
		//alert('preencher_campo_prontuario');
	}
}
</script>


<!--//////////////////////////-->
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
 buscar_nome($F('pac_nome'), 'buscar_nome');return false;link_f7();
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
</script>

<table width="100%">
	<tr>
					<td>
						Codigo Liberac&atilde;o&nbsp;<input type="text" name="liberacao" value="" class="box">
					</td>
					<td>
					Data Liberac&atilde;o&nbsp;
					
			<input id="element_1_1" name="element_1_1" class="boxData" size="2" maxlength="2" value="" type="text"> /
			<label for="element_1_1"></label>
			<input id="element_1_2" name="element_1_2" class="boxData" size="2" maxlength="2" value="" type="text"> /
			<label for="element_1_2"></label>
	 		<input id="element_1_3" name="element_1_3" class="boxData" size="4" maxlength="4" value="" type="text">
			<label for="element_1_3"></label>
			<img id="cal_img_1" class="datepicker" src="images/calendar.gif" alt="Pick a date.">
		<script type="text/javascript">
			Calendar.setup({
			inputField	 : "element_1_3",
			baseField    : "element_1",
			displayArea  : "calendar_1",
			button		 : "cal_img_1",
			ifFormat	 : "%B %e, %Y",
			onSelect	 : selectEuropeDate
			});
		</script>
		
					</td>
				</tr>
				<?
					echo "<tr>";
                                                echo "<td>Prontuario&nbsp;";
                                                echo "<input type=text name='pac_codigo' id='pac_codigo' class=boxNumero readonly value='$pac[usu_codigo]'>";
												echo "</td>";
                                                       


                                                echo "<td>Paciente&nbsp;";
                                                
                                                        echo "<input type=text name=pac_nome id=pac_nome value='$pac[usu_nome]' class=boxTexto onkeyup=\"buscar_nome(this.value);\" style=\"text-transform:uppercase;\" onkeypress=\"if(event.keyCode == 13)buscar_nome(\$F('pac_nome'), 'buscar_nome')\">";
                                                echo "<a href='#' onclick=\"buscar_nome(\$F('pac_nome'), 'buscar_nome');return false;link_f7()\"><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/localizar.jpg id=localizar align=absmiddle border=0></a>";

												echo "</td>";

                                              //  echo divBuscaPaciente();
											  echo "</tr>
											  		<tr>";
                                               
                                                echo "<td>Nascimento&nbsp;"; 
                                                        echo "<input type=text name=pac_nascimento value='$pac[usu_datanasc]' id=pac_nascimento class=boxNumero onkeypress=\"if(event.keyCode == 13)buscar_nome(\$F('pac_nascimento'), 'buscar_data');return Ajusta_Data(this, event);\" maxlength=\"10\">
													
													";
                                                echo "</td>";
                                      
                                                echo "<td>Mae
													  <input type=text name=pac_mae value='$pac[usu_mae]' id=pac_mae class=boxTexto  readonly>
													  </td>
							 		</tr>
						<tr>
							 <td>Cidade &nbsp;
							 <input type=text name=pac_cidade id=pac_cidade value='$pac[usu_end_cidade]' class=boxTexto readonly></td>
											  
					";
						echo 	"<td>
									<a href='#'> <img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/listar_procedimento_on.jpg'> </a>
								 </td>
							  </tr>";
				?>
</table>