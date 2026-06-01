<script language="JavaScript" type="text/javascript" src="../funcoes.js"></script>
<script language='JavaScript' type='text/javascript' src='../ajax_motor.js'></script>
<SCRIPT Language="Javascript"><!--

function CheckCall() {   
   gUnidad = document.frm_pacPorUnidade.uni_codigo.value;
   var endereco = '../exportacao/exportaPacientesPorUnidade.php?unidadeOrigem='+gUnidad; 
   ajax_tudo(endereco,criarLink);    
   return false;
}

function criarLink(txt)
{	if(txt == 1)
	{
		window.location ="../lib/baixarArquivo.php?arquivo=../exportacao/arquivos/PacientesPorUnidade.txt";
	}else{
		alert("Houve um erro na geração do arquivo, tente novamente.");
	}  
}

function valor(indice)
{
   document.frm_AgPorMedico.SintAn.value = document.frm_AgPorMedico.val[indice].value;
}

--></script>


<?
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();

echo "<script>\n";
echo "	 function getIDmedico() {\n";
echo "	       document.frm_AgPorMedico.codMed.value = document.frm_AgPorMedico.med_codigo.value;\n";
echo "		}\n";
echo "</script>\n";

//echo "<script language="javascript" src="teotokos.js" type="text/javascript"></script>\n";
//--------------------------------------------------------------------------------------------------------------------------

echo "<link href=\"../estilo.css\" rel=\"stylesheet\" type=\"text/css\">\n";

echo " <form name=\"frm_pacPorUnidade\" method=\"post\" action=\"$PHP_SELF\">\n";
echo "  <fieldset>";
echo "   <legend>Exporta&ccedil;&atilde;o Pacientes por Unidade</legend> \n";
echo "    <table width=100% border='0'  cellspacing='2' cellpadding='1'>\n";
echo "     <tr>\n";
$sql = "SELECT uni_codigo from usuarios
        where usr_codigo = $id_login";
$row = pg_fetch_array(pg_query($sql));
echo "      <td valign='bottom'>Unidade</td>\n";
echo "      <td><select name='uni_codigo' class=box>\n";
					$sql="SELECT uni_codigo, uni_desc FROM unidade  ";
			                if ($row[0])  $sql .= "WHERE uni_codigo = $row[0] "; 
			                $sql .= " ORDER BY uni_desc  ";
		                        $query=pg_query($sql);
					if ( ! $row[0]) { 
echo "           <option value=''> --- Todas unidades ---</option>\n";
}
					while($unidade=pg_fetch_array($query)) {
						  echo ($uni_codigo==$unidade[uni_codigo])?"<option value='$unidade[uni_codigo]' selected> $unidade[uni_desc]</option>":"<option value='$unidade[uni_codigo]' > $unidade[uni_desc]</option>\n";
					}
echo "          </select>\n";
echo "      </td>\n";
echo "     </tr>\n";
echo"		<tr><td>&nbsp;</td></tr>";
echo "     <tr>\n";
echo "      <td>&nbsp;</td> ";

echo "      <td> <img  src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/gerar_relatorio_on.jpg OnClick='CheckCall()'  name='enviar' value='ENVIAR'> </td>\n";
echo "     </tr>\n";
echo "    </table>\n";
echo "  </fieldset>\n";
echo " </form>\n";
