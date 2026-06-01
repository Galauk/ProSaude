<link href="estiloProntuario.css" rel="stylesheet" type="text/css">
<link href="../estilo.css" rel="stylesheet" type="text/css">

<link rel="stylesheet" type=text/css href="js/jquery-ui-1.7.2.custom.css" />
<script>
	function buscar_medicos(valor, acao)
        {				
                url = "../buscar_medicos.php?palavra="+valor+"&acao="+acao;
                ajax_tudo(url, popular_nomeM);
                $('lista_nomesM').style.display = '';
                $('table_nomesM').innerHTML = '';
                $("lista_carregandoM").style.display = '';
        }

        
        function popular_nomeM(txt)
        {
                try {
                        t = $('table_nomesM');
                        $("lista_carregandoM").style.display = 'none';
                        t.innerHTML = txt;
                } catch(e) {
                        alert(e);
                }
        }
function passar_medico(codigo, nome)
        {
        		
                $("med_codigo").value = codigo;
                $("med_nome").value = nome;

                
                if(document.getElementById("med_prontuario") != null)
                {
                        $("med_prontuario").value = prontuario;
						/*if( at_iframe_esq != null )
							at_iframe_esq();*/
                }
                
                $('lista_nomesM').style.display = 'none';
                $('med_nome').focus();
				ajaxMedico();
				
		}
</script>
<?php
session_start();
echo"
<div id='topo' class='topo'>
	
		<table border='0'>
			<tr>
				<td class='primeiroNome' width='110px'>
					<b>Cod.Medico:<b>
				</td>
				<td>
					<input type=text name='med_codigo' id='med_codigo' class=boxNumero readonly value='$med[usu_codigo]'>
				</td>
				<td class='primeiroNome' width='90px'>
					<b>Medico:</b>
				</td>
				<td>
					<input type=text name=med_nome id=med_nome value='$med[usu_nome]' class=boxTexto onkeyup=\"buscar_medicos(this.value);\" style=\"text-transform:uppercase;\" onkeypress=\"if(event.keyCode == 13)buscar_medicos(document.getElementById('med_nome').value, 'buscar_medicos')\">
					<a href='#' onclick=\"buscar_medicos(document.getElementById('med_nome').value, 'buscar_medicos');return false;link_f7()\"><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/localizar.jpg id=localizar align=absmiddle border=0></a>";
				echo divBuscaMedico("../");
				echo"</td>               
			</tr>
			";
		echo "
		</table>
	
</div>";
?>