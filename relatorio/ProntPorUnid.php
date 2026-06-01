<script type="text/javascript" src="../funcoes.js"></script> 
<SCRIPT Language="Javascript">
var gdtInicial
var gdtFinal
var gMedico
var gUnidad
var gSintAna
var maxDay = new Array(31,29,31,30,31,30,31,31,30,31,30,31);


function CheckDate(d,t) {
   date_array = new Array(3);
   date_array[0]=(String(d).substr(6,2))    // dia
   date_array[1]=(String(d).substr(4,2))    // mes
   date_array[2]=(String(d).substr(0,4))    // ano

   if (date_array[0] > maxDay[date_array[1]-1]) {
       alert ("Dia invalido da data " + t)
       return 1;
   }
   if (date_array[1] > 12) {
       alert ("Mes invalido da data " + t)
       return 1;
   }
   if (date_array[2] < 1998) {
       alert ("Ano invalido da data " + t)
       return 1;
   }
}

function CheckCall() {
    uni_codigo = document.getElementById("uni_codigo").value;
    usu_nome = document.getElementById("usu_nome").value;
    usu_cod   = document.getElementById("usu_cod").value;
    if (uni_codigo == -1 && usu_nome == '')
    {
        alert('Pelo menos um campo deve ser preenchido.');
        return false;
    }
    if (usu_nome = '')
    {
        usu_cod = '';
    }
    url = 'ProntuarioPorUnid.php?uni='+uni_codigo+'&usu='+usu_cod;
  window.open(url,null,"height=400,width=750,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");
  return true;
}
function pacientes(codigo,nome,nascimento,mae,cidade) {

	document.getElementById('usu_cod').value 	= codigo;
	document.getElementById('usu_nome').value = nome;
}
</script>


<?
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();


//echo "<script language="javascript" src="teotokos.js" type="text/javascript"></script>\n";
//--------------------------------------------------------------------------------------------------------------------------


echo "<link href=\"../estilo.css\" rel=\"stylesheet\" type=\"text/css\">\n";

echo " <form name=\"frm_AgPorMedico\" method=\"post\" action=\"$PHP_SELF\">\n";
echo "  <fieldset>";
echo "   <legend>Prontu&aacute;rio por Unidade</legend> \n";
echo "    <table width=100% border='0'  cellspacing='2' cellpadding='1'>\n";
echo "     <tr>\n";
echo "          <td width='10%'>Unidade</td>
                <td>
                    <select class=\"box\" id='uni_codigo' name='uni_codigo'>
                        <option value='-1'>Todos</option>";
                       $sql = "SELECT uni_codigo,uni_desc FROM unidade order by uni_desc";
                       $sql = pg_query($sql);
                       while ($dados = pg_fetch_array($sql))
                       {
                            echo "<option value=\"".$dados['uni_codigo']."\">".$dados['uni_desc']."</option>";
                       }
echo                "</select>
                </td>";
echo "     </tr>\n";
echo "     <tr>\n";
echo "          <td>Paciente</td>
                <td>
                    <input type='hidden' name='usu_cod' id='usu_cod' value='' />
                    <input type='text' name='usu_nome' id='usu_nome' class='boxl' size='60' value='' />
                        <a href='#' OnClick='window.open(\"../list_pacientes.php?id_login=$id_login&controle=1\",null,\"height=460,width=800,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes\");'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/localizar.jpg align=absmiddle border=0></a>
                </td>";
echo "     </tr>\n";
echo "     <tr>\n";
echo "      <td><a href='../rel_index.php?id_login=$id_login#tabs-1'><img  src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.jpg  name='voltar' border=0></a></td>";
echo "      <td> <img  src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/gerar_relatorio_on.jpg OnClick='return CheckCall()'  name='enviar' value='ENVIAR'> </td>\n";
echo "     </tr>\n";
echo "    </table>\n";
echo "  </fieldset>\n";
echo " </form>\n";
