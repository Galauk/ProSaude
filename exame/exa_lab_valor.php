<?
	session_start();
	include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
	#verauth($id_login);

	include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();

?>
<script type="text/Javascript">

function send(action)
{
	switch(action) {
		case 'continuar':
var vlr_mensal = document.getElementById('vlr_mensal').value;
   if( vlr_mensal == "" || vlr_mensal == null )
    {
        alert( 'Preencha o Campo valor' );
        vlr_mensal.focus();
        return false;
    }
var DestL = $('dest_list');
   if( DestL.length == 0 || DestL.options[0].value == '0' )
    {
        alert( 'Escolha pelo menos 1 Procedimento !' );
        DestL.focus();
        return false;
    }


    DestL.multiple = true;
    for( var i = 0; i < DestL.length; i++ )
        DestL.options[ i ].selected = true;

			url = 'exa_lab_valor.php?acao=continua';
			break;
		case 'continuar_editar':
			  if(document.form_msg.vlr_mensal.value=='') {
			     alert('Preencha o Valor Mensal');
			    return false;
			  } 


var DestL = $('dest_list');
   if( DestL.length == 0 || DestL.options[0].value == '0' )
    {
        alert( 'Escolha pelo menos 1 Procedimento !' );
        DestL.focus();
        return false;
    }


    DestL.multiple = true;
    for( var i = 0; i < DestL.length; i++ )
        DestL.options[ i ].selected = true;

			url = 'exa_lab_valor.php?acao=continuar_editar';
			break;
		case 'addperiodo':
			  if(document.form_msg.h_med_codigo.value=='') {
			     alert('Selecione o Laboratorio');
			    return false;
			  } 
			  if(document.form_msg.new_gex_periodo.value=='') {
			     alert('Preencha o Novo Periodo');
			    return false;
			  } 
			url = 'exa_lab_valor.php?acao=addperiodo';
			break;
		case 'addperiodo2':
		var med_codigo = document.getElementById('med_codigo').value;
		
			if(document.form_msg.h_med_codigo.value=='') {
				alert('Selecione o Laboratorio');
				return false;
			} 
			if(document.form_msg.new_gex_periodo.value=='') {
				alert('Preencha o Novo Periodo');
				return false;
			} 
			url = 'exa_lab_valor.php?acao=cpperiodo&acao2=addperiodo&med_codigo=med_codigo';
			break;
		case 'delperiodo':
			  if(document.form_msg.h_med_codigo.value=='') {
			     alert('Selecione o Laboratorio');
			    return false;
			  } 
			  if(document.form_msg.h_gex_periodo.value=='') {
			     alert('Selecione o Periodo');
			    return false;
			  } 
			url = 'exa_lab_valor.php?acao=delperiodo';
			break;
	}


	document.form_msg.action = url;
	document.form_msg.submit();
}

</script>
<script LANGUAGE="JavaScript">
function selecionaPeriodo(){
	var med_codigo = document.getElementById('med_codigo').value;
	url = "selecionaPeriodo.php?med_codigo="+med_codigo;
	ajax_tudo(url,sucesso);	
}
function selecionaPeriodoDois(){
	var med_codigo = document.getElementById('med_codigo_dois').value;
	url = "selecionaPeriodo.php?med_codigo="+med_codigo;
	ajax_tudo(url,Teste);	
}
function Teste(txt){
	document.getElementById('teste').innerHTML = txt;
	url = 'exa_lab_valor.php?acao=cpperiodo&acao3=pegaDados&gex='+gex
	document.form_msg.action = url;
	document.form_msg.submit();
}

function sucesso(txt){
	document.getElementById('oculta').innerHTML = txt;
	url = 'exa_lab_valor.php?acao=cpperiodo&acao3=pegaDados&gex='+gex
	document.form_msg.action = url;
	document.form_msg.submit();
}
function passaPeriodo(){
	var gex = document.getElementById('oculta').value;
	url = 'exa_lab_valor.php?acao=cpperiodo&acao3=pegaDados&gex='+gex
	document.form_msg.action = url;
	document.form_msg.submit();
}

function amf2005_BecameCurrency(cur,len)
{
   n='__0123456789';
   d=cur.value;
   l=d.length;
   r='';
   if (l > 0)
   {
        z=d.substr(0,l-1);
        s='';
        a=2;
        for (i=0; i < l; i++)
        {
                c=d.charAt(i);
                if (n.indexOf(c) > a)
                {
                        a=1;
                        s+=c;
                };
        };
        l=s.length;
        t=len-1;
        if (l > t)
        {
                l=t;
                s=s.substr(0,t);
        };
        if (l > 2)
        {
                r=s.substr(0,l-2)+'.'+s.substr(l-2,2);
        }
        else
        {
                if (l == 2)
                {
                        r='0.'+s;
                }
                else
                {
                        if (l == 1)
                        {
                                r='0.0'+s;
                        };
                };
        };
        if (r == '')
        {
                r='0.00';
        }
        else
        {
                l=r.length;
                if (l > 6)
                {
                        j=l%3;
                        w=r.substr(0,j);
                        wa=r.substr(j,l-j-6);
                        wb=r.substr(l-6,6);
                        if (j > 0)
                        {
                                w+='.';
                        };
                        k=(l-j)/3-2;
                        for (i=0; i < k; i++)
                        {
                                w+=wa.substr(i*3,3)+'.';
                        };
                        r=w+wb;
                };
        };
   };
   if (r.length <= len)
   {
        cur.value=r;
   }
   else
   {
        cur.value=z;
   };
   return 'ok';
};


function FormataReais(fld, milSep, decSep, e) {
var sep = 0;
var key = '';
var i = j = 0;
var len = len2 = 0;
var strCheck = '0123456789';
var aux = aux2 = '';
var whichCode = (window.Event) ? e.which : e.keyCode;
if (whichCode == 13) return true;
key = String.fromCharCode(whichCode);  // Valor para o código da Chave
if (strCheck.indexOf(key) == -1) return false;  // Chave inválida
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

function clr() {
 document.form_msg.vlr_mensal.value='';
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
<?
if(empty($acao)) {

echo "<h3>$acao</h3>";

//
//-> Botoes
  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
         <tr>
          <td>
           <fieldset>
            <legend>Opções</legend>
             <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
              <tr>
               <td width=95><a href=$PHP_SELF?acao=form_add&id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg border=0></a></td>
               <td>&nbsp;</td>
              </tr>
             </table>
           </fieldset>
          </td>
         </tr>
        </table><br>";


 echo "<fieldset><legend>Laboratorios/Procedimentos de Valor Financeiro </legend>";
 echo "<table width=100% cellspacing=1 cellpadding=5 border=0>
	<tr bgcolor='#000000'>
	 <td><font color='#FFFFFF'>Laboratorios</font></td>
	 <td><font color='#FFFFFF'>Periodo</font></td>
	 <td><font color='#FFFFFF'>Max/Mes</font></td>
	 <td><font color='#FFFFFF'>Saldo</font></td>
	 <td width=420 colspan=3>&nbsp;</td>
	</tr>";
	$select = "SELECT to_char(grm.gex_periodo,'DD/MM/YYYY') as gex_periodo2,
					  * 
				 FROM grade_exame_mensal as grm 
				 LEFT JOIN medico as m 
				   ON m.med_codigo = grm.med_codigo 
				WHERE m.quota_qtde = 'N'
				ORDER BY cast(gex_periodo as text) DESC";
	$sql = pg_query($select) or die(pg_last_error());

	while($rr = pg_fetch_array($sql)) {
		$query = pg_query("select *from grade_exame where med_codigo = $rr[med_codigo] and graex_data between to_date('$rr[gex_periodo]', 'yyyy-mm-dd') and to_date('$rr[gex_periodo]', 'yyyy-mm-dd') + 30 order by graex_data");
	
		$num = pg_num_rows($query);
		$row = pg_fetch_array($query);
		if($num!=0) {
			echo "
			<tr bgcolor='#f1f1f1'>
				<td>$rr[med_nome]</td>
				<td align=center>$rr[gex_periodo2]</td>
				<td align=center><font size=2><b>$row[graex_qtd_maxmes]</b></td>
				<td align=center><font size=2><b>$row[graex_valor]</b></td>
				<td width=180><a href=$PHP_SELF?acao=cpperiodo&gex_codigo=$rr[gex_codigo]><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/copiar.png border=0></a></td>
				<td width=120><a href=$PHP_SELF?acao=form_edit&gex_codigo=$rr[gex_codigo]><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.png border=0></a></td>
				<td width=120><a href=$PHP_SELF?acao=del&gex_codigo=$rr[gex_codigo]><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.png border=0></a></td>
			</tr>";
		}
	}
	echo "</table>";
	echo "</fieldset>";
}

if ($acao=="form_add") {

 echo "<fieldset><legend>Formulario - Laboratorios/Procedimentos</legend>";
 echo "<form method='post' action='$PHP_SELF' id='form_msg' name='form_msg'>";
 echo "<input type=hidden name=h_med_codigo value=$h_med_codigo>
	  <input type=hidden name=h_gex_periodo value=$h_gex_periodo>
	  <input type=hidden name=gex_codigo value=$gex_codigo>
	<table width=90% cellspacing=1 cellpadding=5 border=0>
	<tr>
	 <td class='bordaN' width=15%>Prestador de Servico:</td>
	 <td colspan=5><select name=med_codigo class=box onChange=\"javascript:changeLocation(this)\">
	  <option>::.. Selecione um Laboratorio ..::</option>";
	
$sql = pg_query("select *from medico where prestador_servico ='S' and med_codigo != 2165 order by med_nome");
 while($row=pg_fetch_array($sql)) {
 echo ($row[med_codigo]==$h_med_codigo)?"<option value='$PHP_SELF?quota_diaria=$quota_diaria&vlr_mensal=$vlr_mensal&gex_codigo=$gex_codigo&id_login=$id_login&acao=form_add&h_med_codigo=$row[med_codigo]&vlr_mensal=$vlr_mensal&quota_diaria=$quota_diaria&h_gex_periodo=$h_gex_periodo' selected>$row[med_nome]</option>":"<option value='$PHP_SELF?id_login=$id_login&acao=form_add&h_med_codigo=$row[med_codigo]&gex_codigo=$gex_codigo&quota_diaria=$quota_diaria&vlr_mensal=$vlr_mensal'>$row[med_nome]</option>";
}
 echo "</select></td>
	</tr>";
	echo "<tr>
	 <td class='bordaN' width=15%>Periodo:</td>";
	 echo "
	 <td width=220>
	 <select name=gex_periodo class=box onChange=\"javascript:changeLocation(this)\">
	  <option>::.. Periodo ..::</option>";
	 $seleciona = "SELECT to_char(gex_periodo, 'DD/MM/YYYY') as gex_periodo,
	 					  gex_codigo 
	 				 FROM grade_exame_mensal 
	 				WHERE med_codigo = $h_med_codigo 
	 				  AND vlr_mensal IS NULL
	 				ORDER BY gex_periodo DESC";
$query = pg_query($seleciona) or die(pg_last_error());
 while($rr=pg_fetch_array($query)) {
 echo ($rr[gex_periodo]==$h_gex_periodo)?"<option selected value='$PHP_SELF?quota_diaria=$quota_diaria&vlr_mensal=$vlr_mensal&gex_codigo=$rr[gex_codigo]&id_login=$id_login&acao=form_add&h_med_codigo=$h_med_codigo&vlr_mensal=$vlr_mensal&quota_diaria=$quota_diaria&h_gex_periodo=$rr[gex_periodo]'>$rr[gex_periodo]</option>":"<option value='$PHP_SELF?id_login=$id_login&acao=form_add&h_med_codigo=$h_med_codigo&vlr_mensal=$vlr_mensal&quota_diaria=$quota_diaria&h_gex_periodo=$rr[gex_periodo]&gex_codigo=$rr[gex_codigo]&quota_diaria=$quota_diaria&vlr_mensal=$vlr_mensal'>$rr[gex_periodo]</option>";
}
 echo "</select>&nbsp;
	  <input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/btndel.png border=0 align='absmiddle' onclick=\"return send('delperiodo');\">
       </td>
  <td width=10>&nbsp;</td>
                <td width=150 align=right class='bordaN'>Novo Periodo: </td>\n
                <td width=70><input type=text name=new_gex_periodo size='12' class='boxl' id='data' maxlength='10' onKeypress=\"return Ajusta_Data(this, event);\"></td>\n
                <td><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/add_on.gif border=0  onclick=\"return send('addperiodo');\"></td>\n
              </tr>
			  <tr>
	 <td class='bordaN' width=15%>Valor Mensal(<b><font color=red>R$</font></b>):</td>
	 <td colspan=5><input type=text name=vlr_mensal id='vlr_mensal' class=box size=12 value='$vlr_mensal' onKeyPress=\"return(FormataReais(this,'.',',',event))\"><a href='#' OnClick='clr();'>&nbsp;Limpar</a></td>
	</tr>
	</table>";
 echo "</fieldset>";
if(($h_med_codigo!='' AND $h_gex_periodo!='')) {
  echo "<script language=\"JavaScript\" type=\"text/javascript\" src=\"procedimento.js\"></script>";
   echo "<fieldset><legend>Selecione o Procedimentos que este laboratorio fara no periodo</legend>";
   echo "<table width=100% cellspacing=0 cellpadding=5 border=0>
    <tr>
        <td style='vertical-align:top;'>
            <label for='dest'>Lista de Procedimentos</label>
            <br />
            <img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/selecionar_on.png' id='add_dest' title='Selecionar' style='cursor:pointer; vertical-align: middle;'/>
            <img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/selecionar_todos_on.png' id='add_dest_all' title='Selecionar Todos'
                style='cursor:pointer; vertical-align: middle;'/>

            <!--<input type='button' id='add_dest' value='ADD' class='btn' />
            <input type='button' id='add_dest_all' value='ADD ALL' class='btn' />-->
        </td>
        <td>
            <select id='dest' name='dest' class='box' size='4' style='width:800px;height:200px;'>";
$query = pg_query("select TRANSLATE(proc_nome, 'ZZZ-', '') as newprocnome,*from procedimento order by TRANSLATE(proc_nome, 'ZZZ-', '')");
  while($row=pg_fetch_array($query)) {
               echo "<option value='$row[proc_codigo]'>$row[newprocnome]</option>";
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
</table>
";
 echo "</fieldset><br><br>
	<div align=center><a href='#' onclick=\"return send('continuar');\"><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/continuar_cadastro.png border=0 ></a></div></form>";
 }
}

if ($acao=="form_edit") {

 echo "<fieldset><legend>Formulario - Laboratorios/Procedimentos</legend>";
 echo "<form method='post' action='$PHP_SELF' id='form_msg' name='form_msg'>";
    $rr = pg_fetch_array(pg_query("select to_char(gex_periodo, 'DD/MM/YYYY') as gex_periodonew,*from grade_exame_mensal where gex_codigo = $gex_codigo"));
    $sql = pg_query("select *from grade_exame where gex_codigo = $rr[gex_codigo]");
    $graex = pg_fetch_array($sql);
 echo "<input type=hidden name=h_med_codigo value=$rr[med_codigo]>
	  <input type=hidden name=h_gex_periodo value=$rr[gex_periodo]>
	  <input type=hidden name=gex_codigo value=$rr[gex_codigo]>
	<table width=90% cellspacing=1 cellpadding=5 border=0>
	<tr>
	 <td class='bordaN' width=15%>Prestador de Servico:</td>
	 <td colspan=5>";
$med = pg_fetch_array(pg_query("select *from medico where med_codigo = '$graex[med_codigo]' order by med_nome"));
 echo $med[med_nome];
 echo "</td>
	</tr>
	<tr>
	 <td class='bordaN' width=15%>Valor Mensal(<b><font color=red>R$</font></b>):</td>
	 <td colspan=5><input type=text name=vlr_mensal class=box size=12 value='$graex[graex_valor]' onKeyPress=\"return(FormataReais(this,'.',',',event))\"><a href='#' OnClick='clr();'>&nbsp;Limpar</a></td>
	</tr>";
echo"
	<tr>
	 <td class='bordaN' width=15%>Periodo:</td>
	 <td width=220>$rr[gex_periodonew]
       </td>
	</table>";
 echo "</fieldset>";
  echo "<script language=\"JavaScript\" type=\"text/javascript\" src=\"procedimento.js\"></script>";
   echo "<fieldset><legend>Selecione o Procedimentos que este laboratorio fara no periodo</legend>";
   echo "<table width=100% cellspacing=0 cellpadding=5 border=0>
    <tr>
        <td style='vertical-align:top;'>
            <label for='dest'>Lista de Procedimentos</label>
            <br />
            <img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/selecionar_on.png' id='add_dest' title='Selecionar' style='cursor:pointer; vertical-align: middle;'/>
            <img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/selecionar_todos_on.png' id='add_dest_all' title='Selecionar Todos'
                style='cursor:pointer; vertical-align: middle;'/>

            <!--<input type='button' id='add_dest' value='ADD' class='btn' />
            <input type='button' id='add_dest_all' value='ADD ALL' class='btn' />-->
        </td>
        <td>
            <select id='dest' name='dest' class='box' size='4' style='width:800px;height:200px;'>";
$query = pg_query("select TRANSLATE(proc_nome, 'ZZZ-', '') as newprocnome,*from procedimento where proc_exame = 'S' order by TRANSLATE(proc_nome, 'ZZZ-', '')");
  while($row=pg_fetch_array($query)) {
$qq = pg_query("select *from grade_exame where gex_codigo = $rr[gex_codigo] and proc_codigo = $row[proc_codigo]");
$rwa=pg_fetch_array($qq);
            if($row[proc_codigo]!=$rwa[proc_codigo]) {
               echo "<option value='$row[proc_codigo]'>$row[newprocnome]</option>";
            }
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
            <select id='dest_list' name='dest_list[]' class='box' size='4' style='width:800px;height:200px;'>";
$q = pg_query("select distinct(proc_codigo),proc_codigo from grade_exame where gex_codigo = '$rr[gex_codigo]'");
  while($rrw=pg_fetch_array($q)) {
     $proc = pg_fetch_array(pg_query("select TRANSLATE(proc_nome, 'ZZZ-', '') as newprocnome,* from procedimento where proc_codigo = $rrw[proc_codigo]order by TRANSLATE(proc_nome, 'ZZZ-', '')"));
               echo "<option value='$proc[proc_codigo]' disabled>$proc[newprocnome]</option>";
}
         echo "</select>
        </td>
    </tr>
</table>
";
 echo "</fieldset><br><br>";

 echo"
	<div align=center><a href='#' onclick=\"return send('continuar_editar');\"><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/continuar_cadastro.png border=0 ></a></div></form>";
}


if($acao=="addperiodo") {
$stmt = "INSERT INTO grade_exame_mensal ( 
					gex_periodo, 
					med_codigo, 
					usr_codigo_cad, 
					usr_codigo_alt
					 ) VALUES ( 
					'$new_gex_periodo', 
					".intval($h_med_codigo).", 
					".intval($usr_codigo_cad).", 
					".intval($usr_codigo_alt)." )";

   $sql = pg_query($stmt) or die (pg_last_error());
   
               echo "<SCRIPT LANGUAGE=\"JavaScript\">";//"
               echo "setTimeout(\"location='$PHP_SELF?id_login=$id_login&acao=form_add'\", 1);";//"
 	       echo "alert('Cadastro de Periodo Realizado com SUCESSO!');";
               echo "</SCRIPT>";
  
}

if($acao=="delperiodo") {
   $stmt = "DELETE FROM grade_exame_mensal WHERE gex_periodo = '$h_gex_periodo' and med_codigo = '$h_med_codigo'";
/*   echo $stmt;
   exit;*/
   $sql = pg_query($stmt) or die (pg_last_error());

               echo "<SCRIPT LANGUAGE=\"JavaScript\">";//"
 	       echo "alert('Excluido com SUCESSO!');";
               echo "setTimeout(\"location='$PHP_SELF?id_login=$id_login'\", 1);";//"
               echo "</SCRIPT>";

}

if($acao=="continua") {
$vlr_mensal = str_replace(".","",$vlr_mensal);
$vlr_mensal = str_replace(",",".",$vlr_mensal);
$upvlr = pg_query("update grade_exame_mensal set vlr_mensal = '$vlr_mensal' where med_codigo = '$h_med_codigo' and gex_periodo = '$h_gex_periodo'");
   foreach ($dest_list  as $i => $value) {

if($vlr_mensal=="") { 
   $vlr_mensal = "0.00"; 
}

$q = "BEGIN; ";
/*$stmt = "SELECT
         to_char(MAX(gex_periodo), 'dd/mm/yyyy') as max,
         ('$h_gex_periodo' > MAX(gex_periodo)+29) as ok_max ,
         TO_CHAR(MAX(gex_periodo)+30,'dd/mm/YYYY') as prox_max
         FROM grade_exame_mensal AS m
         WHERE med_codigo = {$h_med_codigo} ";
        $per_row = db_getRow($stmt);*/

     $conta=1;
/*               $dataini = $per_row[0];
               $datainc = $per_row[0];
               $datafin = $per_row[2];*/
			   $dataini = $h_gex_periodo;
			   $datainc = $h_gex_periodo;
			   $qtde = contaDiasMes($dataini);
			   
              while ($conta <= $qtde)
              {
               //verificar feriado
               $vediafer = "SELECT count(*) from feriado WHERE fer_data = '$datainc'";
               $vefer = db_getRow($vediafer);

               if ($vefer[0] == 0) {
                   //verificar dia da semana
                   $vediasem = "SELECT EXTRACT(dow from TO_DATE('$datainc', 'dd/mm/yyyy'))";
                   $vedia = db_getRow($vediasem);
                   if (($vedia[0] <> 0) && ($vedia[0]<>6)) {
                      //gravar dados
                     if ($manut_row['proc_tipo_manut'] == 'P') $proc_codigo = 0;

					 $q .= "INSERT INTO grade_exame
                       (med_codigo, proc_codigo, graex_data, usr_codigo_cad,gex_codigo,graex_valor,graex_saldo_atual)
                       VALUES
                       ('$h_med_codigo', '$dest_list[$i]','$datainc','$id_login','$gex_codigo','$vlr_mensal','$vlr_mensal');";
                     /*$q .= "INSERT INTO grade_exame
                       (graex_qtd_maxmes,med_codigo, proc_codigo, graex_qtde, graex_data, usr_codigo_cad,gex_codigo,graex_valor,graex_saldo_atual)
                       VALUES
                       ('$quota_mes','$h_med_codigo', '$dest_list[$i]', '$quota_diaria', '$datainc', '$id_login','$gex_codigo','$vlr_mensal','$vlr_mensal');";*/

                     $sqldata = "select to_char(to_date('$datainc', 'dd/mm/yyyy')+1, 'dd/mm/yyyy')";
                     $rowdata = db_getRow($sqldata);
                     $conta = $conta + 1;
                     $datainc = $rowdata[0];
                   } //fim if diasemana
                   else
             	   {
                     $sqldata = "select to_char(to_date('$datainc', 'dd/mm/yyyy')+1, 'dd/mm/yyyy')";
                     $rowdata = db_getRow($sqldata);
                     $conta = $conta + 1;
                     $datainc = $rowdata[0];
                   } //fim do else - final de semana
                } // fim if feriado
                else
                {
                $sqldata = "select to_char(to_date('$datainc', 'dd/mm/yyyy')+1, 'dd/mm/yyyy')";
                $rowdata = db_getRow($sqldata);
                $conta = $conta + 1;
                $datainc = $rowdata[0];
                } //fim do else - feriado
              } //fim while
          $q .= " COMMIT;";
 $rq = db_query($q);
}


               echo "<SCRIPT LANGUAGE=\"JavaScript\">";//"
 	       echo "alert('Cadastrado com SUCESSO!');";
               echo "setTimeout(\"location='$PHP_SELF?quota_diaria=$quota_diaria&id_login=$id_login&acao=cad_preco&med_codigo=$h_med_codigo&gex_periodo=$h_gex_periodo'\", 1);";//"
               echo "</SCRIPT>";
}

if($acao=="continuar_editar") {
$vlr_mensal = str_replace(",",".",$vlr_mensal);
/*echo "update grade_exame set graex_valor = '$vlr_mensal' where  to_char(graex_data,'dd/mm/yyyy')= '$h_gex_periodo' and med_codigo = '$h_med_codigo";
exit;*/

$upvlr = pg_query("update grade_exame set graex_valor = '$vlr_mensal' where  to_char(graex_data,'YYYY-MM-DD')= '$h_gex_periodo' and med_codigo = '$h_med_codigo'") or die(pg_last_error());
  if(empty($dest_list)) {
               echo "<SCRIPT LANGUAGE=\"JavaScript\">";//"
               echo "alert('***** ATENCAO *****     Ao Atualiza estes procedimentos podera ter influencia nas vagas diarias');";
               echo "setTimeout(\"location='$PHP_SELF?quota_diaria=$quota_diaria&id_login=$id_login&acao=upd_preco&gex_codigo=$gex_codigo&med_codigo=$h_med_codigo&gex_periodo=$h_gex_periodo'\", 1);";//"
               echo "</SCRIPT>";
exit();
  }

   foreach ($dest_list  as $i => $value) {
$q = "BEGIN; ";
$stmt = "SELECT
         to_char(MAX(gex_periodo), 'dd/mm/yyyy') as max,
         ('$h_gex_periodo' > MAX(gex_periodo)+29) as ok_max ,
         TO_CHAR(MAX(gex_periodo)+30,'dd/mm/YYYY') as prox_max
         FROM grade_exame_mensal AS m
         WHERE med_codigo = {$h_med_codigo} ";
        $per_row = db_getRow($stmt);

     $conta=1;
               $dataini = $per_row[0];
               $datainc = $per_row[0];
               $datafin = $per_row[2];
               while ($conta <= 30)
               {
               //verificar feriado
               $vediafer = "SELECT count(*) from feriado WHERE fer_data = '$datainc'";
               $vefer = db_getRow($vediafer);

               if ($vefer[0] == 0) {
                   //verificar dia da semana
                   $vediasem = "SELECT EXTRACT(dow from TO_DATE('$datainc', 'dd/mm/yyyy'))";
                   $vedia = db_getRow($vediasem);
                   if (($vedia[0] <> 0) && ($vedia[0]<>6)) {
                      //gravar dados
                     if ($manut_row['proc_tipo_manut'] == 'P') $proc_codigo = 0;
                     $q .= "INSERT INTO grade_exame
                       (med_codigo, proc_codigo, graex_qtde, graex_data, usr_codigo_cad,gex_codigo,graex_status,graex_valor,graex_saldo_atual)
                       VALUES
                       ($h_med_codigo, $dest_list[$i], '$quota_diaria', '$datainc', $id_login,$gex_codigo,'S','$vlr_mensal','$vlr_mensal');";
                     $sqldata = "select to_char(to_date('$datainc', 'dd/mm/yyyy')+1, 'dd/mm/yyyy')";
                     $rowdata = db_getRow($sqldata);
                     $conta = $conta + 1;
                     $datainc = $rowdata[0];
                   } //fim if diasemana
                   else
             {
                     $sqldata = "select to_char(to_date('$datainc', 'dd/mm/yyyy')+1, 'dd/mm/yyyy')";
                     $rowdata = db_getRow($sqldata);
                     $conta = $conta + 1;
                     $datainc = $rowdata[0];
                   } //fim do else - final de semana
                } // fim if feriado
                else
                {
                $sqldata = "select to_char(to_date('$datainc', 'dd/mm/yyyy')+1, 'dd/mm/yyyy')";
                $rowdata = db_getRow($sqldata);
                $conta = $conta + 1;
                $datainc = $rowdata[0];
                } //fim do else - feriado
               } //fim while
          $q .= " COMMIT;";
}
          $rq = db_query($q);
               echo "<SCRIPT LANGUAGE=\"JavaScript\">";//"
 	       echo "alert('Cadastrado com SUCESSO!');";
               echo "setTimeout(\"location='$PHP_SELF?id_login=$id_login&quota_diaria=$quota_diaria&acao=upd_preco&gex_codigo=$gex_codigo&med_codigo=$h_med_codigo&gex_periodo=$h_gex_periodo'\", 1);";//"
               echo "</SCRIPT>";
}

if($acao=="cad_preco") {
 echo "<fieldset><legend>Digite o Valor de Cada Procedimentos</legend>";
 echo "<form method=post action='$PHP_SELF' onsubmit='return validaform()' name='finalizar'>
	<input type=hidden name=acao value=finalizar>
	<input type=hidden name=quota_diaria value=$quota_diaria>
	<input type=hidden name=med_codigo value=$med_codigo>
	<input type=hidden name=gex_periodo value=$gex_periodo>
	<table width=100% cellspacing=1 cellpadding=5 border=0>
	<tr bgcolor='#000000'>
	 <td><font color='#FFFFFF'>Procedimento</font></td>
	 <td><font color='#FFFFFF'>Preco do Procedimento</font></td>
	</tr>";
	$dataArray = explode("/",$gex_periodo);
	$dataFix = $dataArray[0]."/".$dataArray[1]."/".$dataArray[2];
	$dataFunc = menorData($gex_periodo);
	$sql ="select TRANSLATE(proc_nome, 'ZZZ-', '') as newprocnome,* from grade_exame as grm left join procedimento as proc on proc.proc_codigo = grm.proc_codigo where grm.med_codigo = '$med_codigo' and grm.graex_data = '$dataFunc' order by TRANSLATE(proc_nome, 'ZZZ-', '')";
	$qry =  pg_query($sql);

  while($row = pg_fetch_array($qry)) {
  echo "
	<input type=hidden name=proc_codigo[] value=$row[proc_codigo]>
	<tr bgcolor='#f1f1f1'>
	 <td>$row[newprocnome]</td>";
if(($row[graex_valor]=="0.00" and $row[graex_qtd_maxmes]!="")) {
  echo "<td><b><font color=red>$row[graex_qtd_maxmes]</font> / Mes</b></td>";
} else {
  echo "<td><input type=text name=proc_valor[] class=box size=8 onkeyup='amf2005_BecameCurrency(this,15)' onblur='amf2005_BecameCurrency(this,15)' maxsize=15 value='$row[proc_valor]'></td>";
}
  echo "</tr>";
}
	
 echo "</table>";
 echo "</fieldset>";
 echo "<table width=100% cellspacing=1 cellpadding=5 border=0>";
 echo "<tr>
	<td align=center><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.png></td>
	</tr>";
 echo "</table>";
}

if($acao=="newstatus") {
   $sql = pg_query("update grade_exame set graex_status='$status' where graex_codigo = '$graex_codigo'");
               echo "<SCRIPT LANGUAGE=\"JavaScript\">";//"
               echo "setTimeout(\"location='$PHP_SELF?id_login=$id_login&acao=upd_preco&gex_codigo=$gex_codigo&med_codigo=$med_codigo&gex_periodo=$gex_periodo'\", 1);";//"
               echo "</SCRIPT>";
}

if($acao=="upd_preco") {
 echo "<fieldset><legend>Digite o Valor de Cada Procedimentos</legend>";
 echo "<form method=post action='$PHP_SELF' onsubmit='return validaform()' name='finalizar'>
	<input type=hidden name=acao value=finalizar>
	<input type=hidden name=med_codigo value=$med_codigo>
	<input type=hidden name=quota_diaria value=$quota_diaria>
	<input type=hidden name=gex_periodo value=$gex_periodo>
	<input type=hidden name=gex_codigo value=$gex_codigo>
	<table width=100% cellspacing=1 cellpadding=5 border=0>
	<tr bgcolor='#000000'>
	 <td><font color='#FFFFFF'>Status</font></td>
	 <td><font color='#FFFFFF'>Procedimento</font></td>
	 <td><font color='#FFFFFF'>Preco do Procedimento</font></td>
	</tr>";
$sql = pg_query("select TRANSLATE(proc_nome, 'ZZZ-', '') as newprocnome,grm.proc_valor as valor,* from grade_exame as grm left join procedimento as proc on proc.proc_codigo = grm.proc_codigo where grm.gex_codigo = '$gex_codigo' and grm.med_codigo = '$med_codigo' and grm.graex_data = '$gex_periodo' order by TRANSLATE(proc_nome, 'ZZZ-', '')");
  while($row = pg_fetch_array($sql)) {
  if($row[graex_status]=="S") {
     $img = "<a href=$PHP_SELF?status=N&graex_codigo=$row[graex_codigo]&acao=newstatus&med_codigo=$med_codigo&gex_periodo=$gex_periodo&gex_codigo=$gex_codigo><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/on.png border=0></a>";
} else {
     $img = "<a href=$PHP_SELF?status=S&graex_codigo=$row[graex_codigo]&acao=newstatus&med_codigo=$med_codigo&gex_periodo=$gex_periodo&gex_codigo=$gex_codigo><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/off.png border=0></a>";
}
  echo "
	<input type=hidden name=proc_codigo[] value=$row[proc_codigo]>
	<tr bgcolor='#f1f1f1'>
	 <td width=22>$img</td>
	 <td>$row[newprocnome]</td>
	 <td><input type=text name=proc_valor[] value='$row[valor]' class=box size=8 onkeyup='amf2005_BecameCurrency(this,15)' onblur='amf2005_BecameCurrency(this,15)' maxsize=15></td>
	</tr>";
}
 echo "</table>";
 echo "</fieldset>";
 echo "<table width=100% cellspacing=1 cellpadding=5 border=0>";
 echo "<tr>
	<td align=center><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.png></td>
	</tr>";
 echo "</table>";
}

if($acao=="finalizar") {
  foreach ($proc_codigo  as $i => $value) {

if($proc_valor[$i]=="") { 
   $vlr_mensal = "0.00"; 
} else {
   $vlr_mensal = $proc_valor[$i];
}
$q = "BEGIN; ";
$stmt = "SELECT
         to_char(MAX(gex_periodo), 'dd/mm/yyyy') as max,
         ('$gex_periodo' > MAX(gex_periodo)+29) as ok_max ,
         TO_CHAR(MAX(gex_periodo)+30,'dd/mm/YYYY') as prox_max
         FROM grade_exame_mensal AS m
         WHERE med_codigo = {$med_codigo} ";
        $per_row = db_getRow($stmt);

     $conta=1;
               $dataini = $per_row[0];
               $datainc = $per_row[0];
               $datafin = $per_row[2];
               while ($conta <= 30)
               {
               //verificar feriado
               $vediafer = "SELECT count(*) from feriado WHERE fer_data = '$datainc'";
               $vefer = db_getRow($vediafer);

               if ($vefer[0] == 0) {
                   //verificar dia da semanaPeriodo:
                   $vediasem = "SELECT EXTRACT(dow from TO_DATE('$datainc', 'dd/mm/yyyy'))";
                   $vedia = db_getRow($vediasem);
                   if (($vedia[0] <> 0) && ($vedia[0]<>6)) {
                      //gravar dados
                     if ($manut_row['proc_tipo_manut'] == 'P') $proc_codigo = 0;

   $q .= "UPDATE grade_exame SET proc_valor = '$vlr_mensal'
          WHERE med_codigo = '$med_codigo' and proc_codigo = '$proc_codigo[$i]';";

                     $sqldata = "select to_char(to_date('$datainc', 'dd/mm/yyyy')+1, 'dd/mm/yyyy')";
                     $rowdata = db_getRow($sqldata);
                     $conta = $conta + 1;
                     $datainc = $rowdata[0];
                   } //fim if diasemana
                   else
             {
                     $sqldata = "select to_char(to_date('$datainc', 'dd/mm/yyyy')+1, 'dd/mm/yyyy')";
                     $rowdata = db_getRow($sqldata);
                     $conta = $conta + 1;
                     $datainc = $rowdata[0];
                   } //fim do else - final de semana
                } // fim if feriado
                else
                {
                $sqldata = "select to_char(to_date('$datainc', 'dd/mm/yyyy')+1, 'dd/mm/yyyy')";
                $rowdata = db_getRow($sqldata);
                $conta = $conta + 1;
                $datainc = $rowdata[0];
                } //fim do else - feriado
               } //fim while
          $q .= " COMMIT;";
 $rq = db_query($q);
}










               echo "<SCRIPT LANGUAGE=\"JavaScript\">";//"
 	       echo "alert('Finalizado com SUCESSO!');";
               echo "setTimeout(\"location='$PHP_SELF?id_login=$id_login'\", 1);";//"
               echo "</SCRIPT>";

}

if($acao=="finalizar_upd") {
  foreach ($graex_codigo  as $i => $value) {
   $stmt = "UPDATE grade_exame SET proc_valor = '$proc_valor[$i]' WHERE gex_codigo = '$gex_codigo' and med_codigo = '$med_codigo' and graex_data = '$gex_periodo' and graex_codigo = '$graex_codigo[$i]'";
   $sql = pg_query($stmt) or die(pg_last_error());
  }
               echo "<SCRIPT LANGUAGE=\"JavaScript\">";//"
 	       echo "alert('Finalizado com SUCESSO!');";
               echo "setTimeout(\"location='$PHP_SELF?id_login=$id_login'\", 1);";//"
               echo "</SCRIPT>";

}

if($acao=="del") {
   $stmt2 = "DELETE from grade_exame_mensal where gex_codigo = '$gex_codigo'";
   $stmt = "DELETE from grade_exame where gex_codigo = '$gex_codigo'";
   $sql2 = pg_query($stmt2) or die(pg_last_error());
   $sql = pg_query($stmt) or die(pg_last_error());
               echo "<SCRIPT LANGUAGE=\"JavaScript\">";//"
               echo "alert('Excluido com SUCESSO!');";
               echo "setTimeout(\"location='$PHP_SELF?id_login=$id_login'\", 1);";//"
               echo "</SCRIPT>";

}

if($acao=="cpperiodo") {
	if ($acao2=="addperiodo"){
			if (cadastraPeriodo($new_gex_periodo, $med_codigo, $usr_codigo_cad, $usr_codigo_alt)){
				echo "<SCRIPT LANGUAGE=\"JavaScript\">";
				echo "setTimeout(\"location='$PHP_SELF?id_login=$id_login&acao=cpperiodo&gex_codigo=$gex_codigo'\", 1);";
				echo "alert('Cadastro de Periodo Realizado com SUCESSO!');";
				echo "</SCRIPT>";
			}else{
				echo "<SCRIPT LANGUAGE=\"JavaScript\">";
				echo "setTimeout(\"location='$PHP_SELF?id_login=$id_login&acao=cpperiodo&gex_codigo=$gex_codigo'\", 1);";
				echo "alert('Periodo ja cadastrado!');";
				echo "</SCRIPT>";		
			}
	}

	echo "<fieldset><legend> Copiar Configuracoes para outro Periodo</legend>";
	echo "<form method='post' action='$PHP_SELF' id='form_msg' name='form_msg'>";
	$rr = pg_fetch_array(pg_query("select to_char(gex_periodo, 'DD/MM/YYYY') as gex_periodonew,*from grade_exame_mensal where gex_codigo = $gex_codigo"));
	$sql = pg_query("select *from grade_exame where gex_codigo = $rr[gex_codigo]");
	$graex = pg_fetch_array($sql);

	echo "<h3>Copiar De:</h3>";
	echo "<input type=hidden name=h_med_codigo value=$rr[med_codigo]>
		  <input type=hidden name=h_gex_periodo value=$rr[gex_periodo]>
		  <input type=hidden name=gex_codigo value=$rr[gex_codigo]>

		<table width=90% cellspacing=1 cellpadding=5 border=0>
			<tr>
				<td class='bordaN' width=15%>Prestador de Servico:</td>
				<td colspan=5>";
$med = pg_fetch_array(pg_query("select * from medico where med_codigo = '$graex[med_codigo]' order by med_nome"));
 echo $med[med_nome];
 echo "</td>
	</tr>
	<input type=hidden name=quota_diaria value=1>
	<tr>
	 <td class='bordaN' width=15%>Periodo:</td>
	 <td width=220>$rr[gex_periodonew]</td>
       </tr>
	</table>";
$gex_periodo_copy = $rr[gex_periodonew];
echo "<h3>Copiar Para:</h3>";
echo"
<table width=90% cellspacing=1 cellpadding=5 border=0>
	<tr>
		<td colspan=5>
			";
			if($acao3 == "pegaDados" ){ // Verifica se foi selecionado o periodo e entao da o select no selecionado assim q a pagina recarrega
				$sqlCarrega = "	select to_char(ge.gex_periodo,'dd/mm/yyyy') as 
									   gex_periodo,me.med_nome,
									   me.med_codigo 
								  from grade_exame_mensal as ge
								  join medico as me
									on me.med_codigo = ge.med_codigo
									where ge.gex_codigo = $gex";
				$qryCarrega = pg_query($sqlCarrega);
				$umaLinha = pg_fetch_array($qryCarrega);
				echo"<select name=med_codigo_dois id='med_codigo_dois' class=box onChange=\"selecionaPeriodoDois()\">
						<option value='{$umaLinha['med_codigo']}' selected='selected'>$umaLinha[med_nome]</option>";
				$sql = pg_query("select *from medico where prestador_servico ='S' order by med_nome");
		
				while($row=pg_fetch_array($sql)) {
 					echo"<option value='{$row['med_codigo']}'>$row[med_nome]</option>";
					}	
				echo"</select>";
				
				// por padrão ele cai sempre fora desse if que é chamado após a mudança do periodo 
				//ele da um refresh na pagina por isso ele utiliza os sql's para buscar pelo gex_codigo o prestador daquele periodo.
				echo" 
				</td>
			<tr>";
echo"
			</tr>
				<input type=hidden name=quota_diaria value=1>
			<tr>
				<td class='bordaR' width=15%>Periodo:</td>
				<td width=220>
					<select id='teste' name='teste' class=box>";
echo"
						<option>::.. Periodo ..::</option>
						<option value='{$gex}' selected='selected'>{$umaLinha['gex_periodo']}</option>
					</select>&nbsp;";
//////////////////////////////////////////////////
echo"	<input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/btndel.png border=0 align='absmiddle' onclick=\"return send('delperiodo');\">
		</td>
		<td width=10>&nbsp;</td>
		<td width=150 align=right class='bordaR'>Novo Periodo: </td>\n
		<td width=70>
			<input type=text name=new_gex_periodo size='12' class='boxl' id='data' maxlength='10' onKeypress=\"return Ajusta_Data(this, event);\"></td>\n
		<td>
			<input type='hidden' name='gex_codigo' value='$gex_codigo'>
			<input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/add_on.gif border=0 onclick=\"return send('addperiodo2');\"></td>\n
	</tr>
";
echo"</table>";
 echo "</fieldset>";
  echo "<br><br>
	<div align=center><a href='$PHP_SELF?gex_codigo_new=$gex&gex_periodo_copy=$gex_periodo_copy&gex_codigo=$gex_codigo&gex_periodo={$umaLinha['gex_periodo']}&acao=cpall&med_codigo_rec={$umaLinha['med_codigo']}&med_codigo=$graex[med_codigo]'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/copiar_dados_2_on.jpg border=0></a></div></form>";
	

 ////////////////////////////////////////////////////////////////////
				//até aqui vem o combo do periodo
			}else{// se nao foi selecionado o periodo nem dado reload ele faz esse select e permanece os prestadores.
				echo"<select name=med_codigo id='med_codigo' class=box onChange=\"selecionaPeriodo()\">";
					 echo"	<option>::.. Selecione um Laboratorio ..::</option>";
					 $sql = pg_query("select *from medico where prestador_servico ='S' order by med_nome");
 
				
				 	while($row=pg_fetch_array($sql)) {
 						echo"<option value='{$row['med_codigo']}'>$row[med_nome]</option>";
					}
			
			///////////////////////////////////////////////////////
 echo "</select>

 		</td>
	<tr>";
echo"
	</tr>
		<input type=hidden name=quota_diaria value=1>
	<tr>
		<td class='bordaR' width=15%>Periodo:</td>
		<td width=220>
			<select id='oculta' name='oculta' class=box onchange='passaPeriodo()'>
				<option>::.. Periodo ..::</option>";
				
				//chamaNovoPeriodo
				
echo "
			</select>&nbsp;";
			
			/////////////////////////////////////////////////////////////
echo"	<input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/btndel.png border=0 align='absmiddle' onclick=\"return send('delperiodo');\">
		</td>
		<td width=10>&nbsp;</td>
		<td width=150 align=right class='bordaR'>Novo Periodo: </td>\n
		<td width=70>
			<input type=text name=new_gex_periodo size='12' class='boxl' id='data' maxlength='10' onKeypress=\"return Ajusta_Data(this, event);\"></td>\n
		<td>
			<input type='hidden' name='gex_codigo' value='$gex_codigo'>
			<input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/add_on.gif border=0 onclick=\"return send('addperiodo2');\"></td>\n
	</tr>
";
echo"</table>";
 echo "</fieldset>";
$pegaPeriodo = "select gex_codigo,to_char(gex_periodo,'DD/MM/YYYY') as data from grade_exame_mensal where gex_codigo = $gex";

$qryPeriodo = pg_query($pegaPeriodo);
$linha = pg_fetch_array($qryPeriodo);
$gex_periodox = $linha['data'];



 echo "<br><br>
	<div align=center><a href='$PHP_SELF?gex_codigo_new=$gex_codigo_new&gex_periodo_copy=$gex_periodo_copy&gex_codigo=$gex_codigo&gex_periodox=$gex_periodox&acao=cpall&med_codigo=$graex[med_codigo]'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/copiar_dados_2_on.jpg border=0></a></div></form>";
			}
}

if($acao=="cpall") {
$h_med_codigo = $med_codigo;
$qy = pg_query("select proc_codigo,graex_qtde,graex_valor from grade_exame 
		where med_codigo = $med_codigo
		and gex_codigo = $gex_codigo
		and graex_valor is not  NULL
		group by proc_codigo,graex_qtde,graex_valor");
		
$sqlGex = "select to_char(gex_periodo, 'DD/MM/YYYY') as gex_period,gex_codigo from grade_exame_mensal where med_codigo = $h_med_codigo and gex_codigo not in (select distinct(gex_codigo) from grade_exame where med_codigo = $h_med_codigo) order by gex_periodo desc";

while($rr = pg_fetch_array($qy)) {

$q = "BEGIN; ";

     $conta=1;

			   $dataini = $gex_periodo_copy;
			   $datainc = $gex_periodo;
				$qtde = contaDiasMes($dataini);

               while ($conta <= $qtde)
               {
				   
               //verificar feriado
               $vediafer = "SELECT count(*) from feriado WHERE fer_data = '$datainc'";
               $vefer = db_getRow($vediafer);

               if ($vefer[0] == 0) {
                   //verificar dia da semana
                   $vediasem = "SELECT EXTRACT(dow from TO_DATE('$datainc', 'dd/mm/yyyy'))";
				   
                   $vedia = db_getRow($vediasem);
                   if (($vedia[0] <> 0) && ($vedia[0]<>6)) {
                      //gravar dados
                     if ($manut_row['proc_tipo_manut'] == 'P') $proc_codigo = 0;
					 
                     $teste = "INSERT INTO grade_exame
                       (med_codigo, proc_codigo, graex_valor, graex_data, usr_codigo_cad,gex_codigo)
                       VALUES
                       ($med_codigo_rec, $rr[proc_codigo], '$rr[graex_valor]', '$datainc', $id_login,$gex_codigo_new)";
					 $rq = db_query($teste);
                     $sqldata = "select to_char(to_date('$datainc', 'dd/mm/yyyy')+1, 'dd/mm/yyyy')";
                     $rowdata = db_getRow($sqldata);
                     $conta = $conta + 1;
                     $datainc = $rowdata[0];
                   } //fim if diasemana
                   else
             {
                     $sqldata = "select to_char(to_date('$datainc', 'dd/mm/yyyy')+1, 'dd/mm/yyyy')";
                     $rowdata = db_getRow($sqldata);
                     $conta = $conta + 1;
                     $datainc = $rowdata[0];
                   } //fim do else - final de semana
                } // fim if feriado
                else
                {
                $sqldata = "select to_char(to_date('$datainc', 'dd/mm/yyyy')+1, 'dd/mm/yyyy')";
                $rowdata = db_getRow($sqldata);
                $conta++;
                $datainc = $rowdata[0];
                } //fim do else - feriado
               } //fim while

		  



}

              echo "<SCRIPT LANGUAGE=\"JavaScript\">";//"
 	       echo "alert('Copiado com SUCESSO!');";
               echo "setTimeout(\"location='$PHP_SELF?id_login=$id_login'\", 1);";//"
               echo "</SCRIPT>";
}



?>

