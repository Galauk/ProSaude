<script language="JavaScript" type="text/javascript" src="funcoes.js"></script>
<script src=script.js></script>

<script>

var gdtInicial
var gdtFinal
var gPaciente
var gMedico
var gProced
var gTipoCusto
var gHoje

function pacientes(nome,codigo,nascimento,mae,cidade) {
   document.frm_TemAtendPAM.pac_codigo.value = nome
   document.frm_TemAtendPAM.pac_nome.value = codigo
}

function CompData(data1 , data2) {       //   Se ( data1 )  MAIOR QUE   ( data2 )    FALSE    //
   var d1 = data1;
   var d2 = data2;
   for (var i = 0; i < d1.length; i++) {
        if (d1.charAt(i) == "-") {
           if ( parseInt( d1.split( "-" )[2].toString() + d1.split( "-" )[1].toString() + d1.split( "-" )[0].toString() ) 
                > 
                parseInt( d2.split( "-" )[2].toString() + d2.split( "-" )[1].toString() + d2.split( "-" )[0].toString() ) )

              { return false }   else    { return true  }
        } else 
        if (d1.charAt(i) == "/") {
           if ( parseInt( d1.split( "/" )[2].toString() + d1.split( "/" )[1].toString() + d1.split( "/" )[0].toString() ) 
                > 
                parseInt( d2.split( "/" )[2].toString() + d2.split( "/" )[1].toString() + d2.split( "/" )[0].toString() ) )
              { return false }   else   { return true  }
        }
   }
}

function VerData() {

   gdtInicial =document.frm_TemAtendPAM.dt_inicial.value;
   gdtFinal   =document.frm_TemAtendPAM.dt_final.value;
   gPaciente  =document.frm_TemAtendPAM.pac_codigo.value;
   gMedico    =document.frm_TemAtendPAM.med_codigo.value;
   gProced    =document.frm_TemAtendPAM.proc_codigo.value;
   gTipoCusto =document.frm_TemAtendPAM.TPCusto.value;

   if (gdtInicial == '') {
       alert("Periodo INVALIDO");
       document.frm_TemAtendPAM.dt_inicial.focus();
       return false;
   }
   if (gdtFinal == '') {
       gdtFinal = gdtInicial;
   }
   if (!CompData(gdtInicial,gdtFinal)) {
       alert("Data Inicial(" + gdtInicial + ") maior que Final(" + gdtFinal + ")");
       document.frm_TemAtendPAM.dt_inicial.focus();
       return false;
   }
   window.open('TempoAtendPAM.php?dt_inicial='+gdtInicial  +
                                   '&dt_final='+gdtFinal   +
					             '&pac_codigo='+gPaciente  +
					             '&med_codigo='+gMedico    +
					            '&proc_codigo='+gProced    +
					               '&tp_custo='+gTipoCusto
					              ,null
		                          ,"height=400,width=750,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");
   return true
}

function  limpa(txt) { 
   document.frm_TemAtendPAM.pac_nome.value = ''
   document.frm_TemAtendPAM.pac_codigo.value = ''
}

function AtualizProduto(p){
   url='ComboAtualizaProduto.php?valor='+p;
   IdentBrowser(url,2);
}

</script>

<?php

//$number = 1234.56;
//setlocale(LC_ALL, 'pt_BR');
//echo money_format("%=*(#10.2n", $number); 
//exit();

//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();

echo "<link href=\"../estilo.css\" rel=\"stylesheet\" type=\"text/css\">\n";
echo " <form name='frm_TemAtendPAM' method='post' action='$PHP_SELF'>\n";
echo "  <fieldset>\n";
echo "   <legend>Rela&ccedil;&atilde;o de  Tempos de Atendimento</legend> \n";
echo "    <table whidht=100% border=0 cellspacing=2 cellpadding=1>\n";
echo "     <tr>\n";
echo "      <td valign='bottom'>Data Inicial</td>\n";
echo "      <td><input class='box' type='text' name='dt_inicial' size='12' value='$dt_inicial'/ maxlength='10' onKeypress=\"return Ajusta_Data(this, event);\"></td>\n";
echo "     </tr>\n";

echo "     <tr>\n";
echo "      <td valign='bottom'>Data Final</td>\n";
echo "      <td><input class='box' type='text' name='dt_final'   size='12' value='$dt_final' / maxlength='10' onKeypress=\"return Ajusta_Data(this, event);\"></td>\n";
echo "     </tr>\n";

echo "     <tr>\n";
echo "      <td>Paciente</td>\n";
echo "      <td><input type=text name=pac_nome class=box  ondblclick='limpa()' size=60 value='$pac_nome' readonly><a href='#' OnClick='window.open(\"../list_pacientes.php?id_login=$id_login\",null,\"height=460,width=800,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes\");'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/localizar.jpg align=absmiddle border=0></a></td>\n";
echo " <th id='tip'> </th>\n";
echo "      <td><input type=hidden name=pac_codigo class=box size=60 value='$pac_codigo'></td>\n";
echo "     </tr>\n";

echo "     <tr>\n";
echo "      <td valign='bottom'>Médico</td>\n";
echo "      <td><select name='med_codigo' class=box>\n";
echo "           <option value=''> --- Todos Médicos --- </option>\n";
					$query=pg_query("SELECT med_codigo, med_nome FROM MEDICO ORDER BY med_nome");
					while($Medico=pg_fetch_array($query)) {
						  echo ($med_codigo==$Medico[med_codigo])?
                                             "<option value='$Medico[med_codigo]' selected> $Medico[med_nome]</option>  " :
                                             "<option value='$Medico[med_codigo]'         > $Medico[med_nome]</option>\n";
					}
echo "          </select>\n";
echo "      </td>\n";
echo "     </tr>\n";

echo "     <tr>\n";
echo "      <td valign='bottom'>Procedimento</td>\n";
echo "      <td><select name='proc_codigo' value='$proc_codigo' class=box>\n";
echo "           <option value=''> --- Todos Procedimentos --- </option>\n";
	              $query=pg_query("SELECT proc_codigo, proc_nome FROM Procedimento ORDER BY proc_nome");
		          while($Procedimento=pg_fetch_array($query)) {
	                    echo ($proc_codigo==$Procedimento[proc_codigo])?
                              "<option value='$Procedimento[proc_codigo]' selected> $Procedimento[proc_nome]</option>" :
                              "<option value='$Procedimento[proc_codigo]'         > $Procedimento[proc_nome]</option>\n";
		            }
echo "          </select>\n";
echo "      </td>\n";
echo "      <td>&nbsp;</td>\n";
echo "      <td>&nbsp;</td>\n";
echo "      <td>&nbsp;</td>\n";
echo "     </tr>\n";

echo "	  <tr>\n";
echo "	   <td>Tipo de Custo</td>\n";
echo "     <td>\n";
echo "      <select name='TPCusto' value ='$TPCusto'>\n";
echo "		  <option value='1'> Custo Médio </option>\n";
echo "        <option value='2'> Custo Referência </option>\n";
echo "      </select>\n";
echo "     </td>\n";
echo "	   <td>&nbsp;</td>\n";
echo "     <td>&nbsp;</td>\n";
echo "	  </tr>\n";

echo "     <tr>\n";
echo "      <td><a href='../rel_index.php?id_login=$id_login#tabs-2'><img  src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.jpg  name='voltar' border=0></a></td>";
echo "      <td><img  src=\"".$_SESSION[linkroot].$_SESSION[comum]."imgs/gerar_relatorio_on.jpg\" OnClick=\"VerData()\"  name='enviar' value='ENVIAR'> </td>\n";
echo "     </tr>\n";

echo "   </table>\n";
echo "  </fieldset>\n";
echo " </form>\n";

