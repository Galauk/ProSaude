<?
	session_start();
	include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
	#verauth($id_login);

	include $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();
?>
<script LANGUAGE="JavaScript">
<!-- Begin
nextfield = "cmp_0"; // nome do primeiro campo do site
netscape = "";
ver = navigator.appVersion; len = ver.length;
for(iln = 0; iln < len; iln++) if (ver.charAt(iln) == "(") break;
netscape = (ver.charAt(iln+1).toUpperCase() != "C");

function keyDown(DnEvents) {
// ve quando e o netscape ou IE
k = (netscape) ? DnEvents.which : window.event.keyCode;
if (k == 13) { // preciona tecla enter
if (nextfield == 'done') {
    alert("viu como funciona?");
return false;
//return true; // envia quando termina os campos
} else {
// se existem mais campos vai para o proximo
eval('document.getElementById(' + nextfield + ').focus()');
return false;
  }
 }
}

document.onkeydown = keyDown; // work together to analyze keystrokes
if (netscape) document.captureEvents(Event.KEYDOWN|Event.KEYUP);
// End -->
</script>

<script LANGUAGE="JavaScript">
function FormataReais(fld, milSep, decSep, e) {
var sep = 0;
var key = '';
var i = j = 0;
var len = len2 = 0;
var strCheck = '0123456789';
var aux = aux2 = '';
var whichCode = (window.Event) ? e.which : e.keyCode;
if (whichCode == 13) return true;
key = String.fromCharCode(whichCode);  // Valor para o c�digo da Chave
if (strCheck.indexOf(key) == -1) return false;  // Chave inv�lida
len = fld.value.length;
for(i = 0; i < len; i++)
if ((fld.value.charAt(i) != '0') && (fld.value.charAt(i) != decSep)) break;
aux = '';
for(; i < len; i++)
if (strCheck.indexOf(fld.value.charAt(i))!=-1) aux += fld.value.charAt(i);
aux += key;
len = aux.length;
if (len == 0) fld.value = '';
if (len == 1) fld.value = '0'+ decSep + '0' + aux;
if (len == 2) fld.value = '0'+ decSep + aux;
if (len > 2) {
aux2 = '';
for (j = 0, i = len - 3; i >= 0; i--) {
if (j == 3) {
aux2 += milSep;
j = 0;
}
aux2 += aux.charAt(i);
j++;
}
fld.value = '';
len2 = aux2.length;
for (i = len2 - 1; i >= 0; i--)
fld.value += aux2.charAt(i);
fld.value += decSep + aux.substr(len - 2, len);
}
return false;
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
<script language="JavaScript" type="text/javascript" src="../funcoes.js"></script>
<script language="JavaScript" type="text/javascript" src="../g_ajax.js"></script>
<script language="JavaScript" type="text/javascript" src="../ajax_motor.js"></script>
<script language="JavaScript" type="text/javascript" src="procedimento.js"></script>
<?
/*
 echo "<form name=formulario method=post action=''>
        <table width=90% cellspacing=1 cellpadding=5 border=0>";
 echo "<tr bgcolor='#000000'>
         <td width=15%><font color='#FFFFFF'>Procedimento</font></td>
         <td colspan=2 width=15%><font color='#FFFFFF'>Preco</font></td>
        </tr>";
$query = pg_query("select *from procedimento where proc_exame = 'S'");
$i=0;
 if($act=="checkall") {
    $Btnck = "checked";
    $btnck="<a href=$PHP_SELF?act=>Deselecionar Todos</a>";
 } else {
    $Btnck = "";
    $btnck="<a href=$PHP_SELF?act=checkall>Selecionar Todos</a>";
 }
  while($row=pg_fetch_array($query)) {
    echo "<script>
	    function clr_$i() {
              document.getElementById('cmp_$i').value='';
            }
         </script>";
 echo "<tr>
         <td class='bordaN' width=15%>$row[proc_nome] <input type=checkbox name=proc_sel class=box $Btnck></td>
         <td width=15%><input type=text name=vlr_mensal[$i] id='cmp_$i' class=box size=12 onFocus=\"nextfield = 'cmp_$i';\" onKeyPress=\"return(FormataReais(this,'.',',',event))\"><a href='#' OnClick='clr_$i();'>&nbsp;Limpar</a></td>
         <td width=15%>&nbsp;</td>
        </tr>";
$i++;
}
 echo "</table>
	<table width=100% cellspacing=0 cellpadding=5 border=0>
	<tr>
	 <td width=50% align=center class='bordaN'>$btnck</td>
	 <td align=center class='bordaN'>Cadastrar</td>
	</tr>
	</table>
       </form>";
*/

echo "<form action='?id_login={$id_login}&amp;acao=nova' method='post' id='form_msg'>
	<table width=100% cellspacing=0 cellpadding=5 border=0>
    <tr>
        <td style='vertical-align:top;'>
            <label for='dest'>Lista de Procedimentos</label>
            <br />
            <img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/selecionar_on.png' id='add_dest' title='Selecionar'
                style='cursor:pointer; vertical-align: middle;'/>
            <img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/selecionar_todos_on.png' id='add_dest_all' title='Selecionar Todos'
                style='cursor:pointer; vertical-align: middle;'/>

            <!--<input type='button' id='add_dest' value='ADD' class='btn' />
            <input type='button' id='add_dest_all' value='ADD ALL' class='btn' />-->
        </td>
        <td>
            <select id='dest' name='dest' class='box' size='4' style='width:800px;height:200px;'>";
$query = pg_query("select *from procedimento where proc_exame = 'S'");
  while($row=pg_fetch_array($query)) {
               echo "<option value='$row[proc_codigo]'>$row[proc_nome]</option>";
}

          echo "</select>
        </td>
</tr>
<tr>
        <td style='vertical-align:top;'>
            <label for='dest'>Procedimentos</label>
            <br />
            <img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/remover_on.png' id='rem_dest' title='Remover'
                style='cursor:pointer; vertical-align: middle;'/>
            <img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/remover_todos_on.png' id='rem_dest_all' title='Remover Todos'
                style='cursor:pointer; vertical-align: middle;'/>

            <!--<input type='button' id='rem_dest' value='REM' class='btn' />
            <input type='button' id='rem_dest_all' value='REM ALL' class='btn' />-->
        </td>
        <td>
            <select id='dest_list' name='dest_list[]' class='box' size='4' style='width:800px;height:200px;'>
                <option value='0'>... Escolha um Procedimento...</option>
            </select>
        </td>
    </tr>
</table></form>";
?>
