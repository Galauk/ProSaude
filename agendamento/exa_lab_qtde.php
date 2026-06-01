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
			  if(document.form_msg.vlr_mensal.value=='') {
			     alert('Preencha o Valor Mensal');
			    return false;
			  } 
			  if(document.form_msg.quota_diaria.value=='0') {
			     alert('Seleciona Quota Diaria Maior Que 0');
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

			url = 'exa_lab_procedimento.php?acao=continua';
			break;
		case 'continuar_editar':
			  if(document.form_msg.vlr_mensal.value=='') {
			     alert('Preencha o Valor Mensal');
			    return false;
			  } 
			  if(document.form_msg.quota_diaria.value=='0') {
			     alert('Seleciona Quota Diaria Maior Que 0');
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

			url = 'exa_lab_procedimento.php?acao=continuar_editar';
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
			url = 'exa_lab_procedimento.php?acao=addperiodo';
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
			url = 'exa_lab_procedimento.php?acao=delperiodo';
			break;
	}


	document.form_msg.action = url;
	document.form_msg.submit();
}

</script>
<script LANGUAGE="JavaScript">
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
	 <td><font color='#FFFFFF'>Qtd. Proc.</font></td>
	 <td><font color='#FFFFFF'>Saldo</font></td>
	 <td width=420 colspan=3>&nbsp;</td>
	</tr>";
$sql = pg_query("select to_char(grm.gex_periodo,'DD/MM/YYYY') as gex_periodo2,* from grade_exame_mensal as grm left join medico as m on m.med_codigo = grm.med_codigo order by to_date(gex_periodo,'YYYY') desc,to_date(gex_periodo,'MM') desc,to_date(gex_periodo,'DD') desc") or die(pg_last_error());
  while($rr = pg_fetch_array($sql)) {
   $query = pg_query("select *from grade_exame where med_codigo = $rr[med_codigo] and graex_data = '$rr[gex_periodo]'");
   $num = pg_num_rows($query);
   $row = pg_fetch_array($query);
if($num!=0) {
 echo "<tr bgcolor='#f1f1f1'>
	 <td>$rr[med_nome]</td>
	 <td align=center>$rr[gex_periodo2]</td>
	 <td align=center><font size=2><b>$num</b></td>
	 <td align=center><font size=2><b>$row[graex_saldo_atual]</b></td>
	 <td width=180><a href=$PHP_SELF?acao=copiar&gex_codigo=$rr[gex_codigo]><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/copiar.png border=0></a></td>
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
	</tr>
	<tr>
	 <td class='bordaN' width=15%>Valor Mensal(<b><font color=red>R$</font></b>):</td>
	 <td colspan=5><input type=text name=vlr_mensal class=box size=12 value='$vlr_mensal' onKeyPress=\"return(FormataReais(this,'.',',',event))\"><a href='#' OnClick='clr();'>&nbsp;Limpar</a></td>
	</tr>
	<tr>
	 <td class='bordaN' width=15%>Qtd. Permitida por dia:</td>
	 <td colspan=5><select name=quota_diaria class=box>";
for($i=0;$i<=200;$i++) {
   echo ($i==$quota_diaria)?"<option value=$i selected>$i</option>":"<option value=$i>$i</option>";
}
 echo "</select>&nbsp;</td>
	</tr>
	<tr>
	 <td class='bordaN' width=15%>Periodo:</td>
	 <td width=220><select name=gex_periodo class=box onChange=\"javascript:changeLocation(this)\">
	  <option>::.. Periodo ..::</option>";
$query = pg_query("select to_char(gex_periodo, 'DD/MM/YYYY') as gex_periodo,gex_codigo from grade_exame_mensal where med_codigo = $h_med_codigo order by to_date(gex_periodo,'YYYY') desc,to_date(gex_periodo,'MM') desc,to_date(gex_periodo,'DD') desc") or die(pg_last_error());
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
$query = pg_query("select TRANSLATE(proc_nome, 'ZZZ-', '') as newprocnome,*from procedimento where proc_exame = 'S' order by TRANSLATE(proc_nome, 'ZZZ-', '')");
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
	</tr>
	<tr>
	 <td class='bordaN' width=15%>Qtd. Permitida por dia:</td>
	 <td colspan=5><select name=quota_diaria class=box>";
for($i=0;$i<=200;$i++) {
   echo ($i==$graex[graex_qtde_maxdiario])?"<option value=$i selected>$i</option>":"<option value=$i>$i</option>";
}
 echo "</select>&nbsp;</td>
	</tr>
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
$q = pg_query("select *from grade_exame where gex_codigo = '$rr[gex_codigo]'");
  while($rrw=pg_fetch_array($q)) {
     $proc = pg_fetch_array(pg_query("select TRANSLATE(proc_nome, 'ZZZ-', '') as newprocnome,* from procedimento where proc_codigo = $rrw[proc_codigo]order by TRANSLATE(proc_nome, 'ZZZ-', '')"));
               echo "<option value='$proc[proc_codigo]' disabled>$proc[newprocnome]</option>";
}
         echo "</select>
        </td>
    </tr>
</table>
";
 echo "</fieldset><br><br>
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
   $sql = pg_query($stmt) or die (pg_last_error());

               echo "<SCRIPT LANGUAGE=\"JavaScript\">";//"
 	       echo "alert('Excluido com SUCESSO!');";
               echo "setTimeout(\"location='$PHP_SELF?id_login=$id_login&acao=form_add'\", 1);";//"
               echo "</SCRIPT>";

}

if($acao=="continua") {
   foreach ($dest_list  as $i => $value) {

$stmt = "INSERT INTO grade_exame ( 
	med_codigo, 
	gex_codigo,
	proc_codigo, 
	graex_data, 
	graex_qtde_maxdiario, 
	graex_valor, 
	usr_codigo_cad, 
	usr_codigo_alt,
	graex_saldo_atual
	 ) VALUES ( 
	".intval($h_med_codigo).", 
	".intval($gex_codigo).", 
	".intval($dest_list[$i]).", 
	'$h_gex_periodo', 
	".intval($quota_diaria).", 
	".floatval($vlr_mensal).", 
	".intval($usr_codigo_cad).", 
	".intval($usr_codigo_alt).",
	".floatval($vlr_mensal)." )";

    $sql = pg_query($stmt) or die(pg_last_error());
   }
               echo "<SCRIPT LANGUAGE=\"JavaScript\">";//"
 	       echo "alert('Cadastrado com SUCESSO!');";
               echo "setTimeout(\"location='$PHP_SELF?id_login=$id_login&acao=cad_preco&med_codigo=$h_med_codigo&gex_periodo=$h_gex_periodo'\", 1);";//"
               echo "</SCRIPT>";
}

if($acao=="continuar_editar") {
   foreach ($dest_list  as $i => $value) {
$stmt = "INSERT INTO grade_exame ( 
	med_codigo, 
	gex_codigo,
	proc_codigo, 
	graex_data, 
	graex_qtde_maxdiario, 
	graex_valor, 
	usr_codigo_cad, 
	usr_codigo_alt,
	graex_saldo_atual
	 ) VALUES ( 
	".intval($h_med_codigo).", 
	".intval($gex_codigo).", 
	".intval($dest_list[$i]).", 
	'$h_gex_periodo', 
	".intval($quota_diaria).", 
	".floatval($vlr_mensal).", 
	".intval($usr_codigo_cad).", 
	".intval($usr_codigo_alt).",
	".floatval($vlr_mensal)." )";
    $sql = pg_query($stmt) or die(pg_last_error());

} 
               echo "<SCRIPT LANGUAGE=\"JavaScript\">";//"
 	       echo "alert('Cadastrado com SUCESSO!');";
               echo "setTimeout(\"location='$PHP_SELF?id_login=$id_login&acao=upd_preco&gex_codigo=$gex_codigo&med_codigo=$h_med_codigo&gex_periodo=$h_gex_periodo'\", 1);";//"
               echo "</SCRIPT>";
}

if($acao=="cad_preco") {
 echo "<fieldset><legend>Digite o Valor de Cada Procedimentos</legend>";
 echo "<form method=post action='$PHP_SELF' onsubmit='return validaform()' name='finalizar'>
	<input type=hidden name=acao value=finalizar>
	<input type=hidden name=med_codigo value=$med_codigo>
	<input type=hidden name=gex_periodo value=$gex_periodo>
	<table width=100% cellspacing=1 cellpadding=5 border=0>
	<tr bgcolor='#000000'>
	 <td><font color='#FFFFFF'>Procedimento</font></td>
	 <td><font color='#FFFFFF'>Preco do Procedimento</font></td>
	</tr>";
$sql = pg_query("select TRANSLATE(proc_nome, 'ZZZ-', '') as newprocnome,* from grade_exame as grm left join procedimento as proc on proc.proc_codigo = grm.proc_codigo where grm.med_codigo = '$med_codigo' and grm.graex_data = '$gex_periodo' order by TRANSLATE(proc_nome, 'ZZZ-', '')");
  while($row = pg_fetch_array($sql)) {
  echo "
	<input type=hidden name=graex_codigo[] value=$row[graex_codigo]>
	<tr bgcolor='#f1f1f1'>
	 <td>$row[newprocnome]</td>
	 <td><input type=text name=proc_valor[] class=box size=8 onkeyup='amf2005_BecameCurrency(this,15)' onblur='amf2005_BecameCurrency(this,15)' maxsize=15></td>
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


if($acao=="upd_preco") {
 echo "<fieldset><legend>Digite o Valor de Cada Procedimentos</legend>";
 echo "<form method=post action='$PHP_SELF' onsubmit='return validaform()' name='finalizar'>
	<input type=hidden name=acao value=finalizar_upd>
	<input type=hidden name=med_codigo value=$med_codigo>
	<input type=hidden name=gex_periodo value=$gex_periodo>
	<input type=hidden name=gex_codigo value=$gex_codigo>
	<table width=100% cellspacing=1 cellpadding=5 border=0>
	<tr bgcolor='#000000'>
	 <td><font color='#FFFFFF'>Procedimento</font></td>
	 <td><font color='#FFFFFF'>Preco do Procedimento</font></td>
	</tr>";
$sql = pg_query("select TRANSLATE(proc_nome, 'ZZZ-', '') as newprocnome,grm.proc_valor as valor,* from grade_exame as grm left join procedimento as proc on proc.proc_codigo = grm.proc_codigo where grm.gex_codigo = '$gex_codigo' and grm.med_codigo = '$med_codigo' and grm.graex_data = '$gex_periodo' order by TRANSLATE(proc_nome, 'ZZZ-', '')");
  while($row = pg_fetch_array($sql)) {
  echo "
	<input type=hidden name=graex_codigo[] value=$row[graex_codigo]>
	<tr bgcolor='#f1f1f1'>
	 <td>$row[newprocnome] $row[proc_valor]</td>
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
  foreach ($graex_codigo  as $i => $value) {
   $stmt = "UPDATE grade_exame SET proc_valor = '$proc_valor[$i]' WHERE med_codigo = '$med_codigo' and graex_data = '$gex_periodo' and graex_codigo = '$graex_codigo[$i]'";
   $sql = pg_query($stmt) or die(pg_last_error());
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

?>

