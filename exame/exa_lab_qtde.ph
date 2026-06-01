<?
	session_start();
	include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
	#verauth($id_login);

	include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();

?>
<script type="text/Javascript">

function send(action)
{
	switch(action) {
		case 'continuar':
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
			
			url = 'exa_lab_qtde.php?acao=continua';
			break;
		case 'continuar_editar':
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
		
			url = 'exa_lab_qtde.php?acao=continuar_editar';
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
			url = 'exa_lab_qtde.php?acao=addperiodo';
			break;
		case 'addperiodo2':
			if(document.form_msg.h_med_codigo.value=='') {
				alert('Selecione o Laboratorio');
				return false;
			} 
			if(document.form_msg.new_gex_periodo.value=='') {
				alert('Preencha o Novo Periodo');
				return false;
			} 
			url = 'exa_lab_qtde.php?acao=cpperiodo&acao2=addperiodo';
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
			url = 'exa_lab_qtde.php?acao=delperiodo';
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
                .bordaR {
                        border-bottom: 1px solid;
                        border-right: 1px solid;
                        border-color: #909090;
			background: #FF1200;
			text-align: right;
			color:#FFFFFF;
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


 echo "<fieldset><legend>Laboratorio CENTRAL/Procedimentos por Quota Unit&aacute;ria </legend>";
 echo "<table width=100% cellspacing=1 cellpadding=5 border=0>
	<tr bgcolor='#000000'>
	 <td><font color='#FFFFFF'>Laboratorios</font></td>
	 <td><font color='#FFFFFF'>Periodo</font></td>
	 <td><font color='#FFFFFF'>Qtd. Proc.</font></td>
	 <td><font color='#FFFFFF'>Qtd. Vagas</font></td>
	 <td width=420 colspan=5>&nbsp;</td>
	</tr>";
	
// $med_codigo = "2165";
	
	
	
/*	
echo "<script>alert($med_codigo);</script>";
*/

$sql = pg_query("select to_char(grm.gex_periodo,'DD/MM/YYYY') as gex_periodo2,* from grade_exame_mensal as grm left join medico as m on m.med_codigo = grm.med_codigo order by med_nome asc, gex_periodo desc") or die(pg_last_error());
  while($rr = pg_fetch_array($sql)) {
   $gex_periodo = menorData($rr[gex_periodo2]);//retorna a menor data que possui cadastro no mês da data passada como parâmetro
   $query = pg_query("select *from grade_exame where med_codigo = $rr[med_codigo] and graex_data = '$gex_periodo'");
   $num = pg_num_rows($query);
   $row = pg_fetch_array($query);
   $dt = explode("-",$gex_periodo);
   $select = "SELECT SUM(GRAEX_QTDE) AS TOTAL
			    FROM GRADE_EXAME 
			   WHERE MED_CODIGO = '$rr[med_codigo]' 
			     AND TO_CHAR(GRAEX_DATA, 'mm/YYYY') = '$dt[1]/$dt[0]'";
   $total = pg_fetch_array(pg_query($select));
 
//   $total = pg_fetch_array(pg_query("select sum(graex_qtde) as total from grade_exame where med_codigo = $rr[med_codigo] and graex_data = '$gex_periodo'"));
//	if($num!=0) {
	 echo "<tr bgcolor='#f1f1f1'>
		 <td>$rr[med_nome]</td>
		 <td align=center>$rr[gex_periodo2]</td>
		 <td align=center><font size=2><b>$num</b></td>
		 <td align=center><font size=2><b>$total[total]</b></td>
		 <td width=120><a href='exa_vagas_por_unidade.php?gex_periodo=$gex_periodo&id=$id_login&gex_codigo=$rr[gex_codigo]&med_codigo=$rr[med_codigo]'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/distribuir_on.png border=0></a></td>
		 <td width=180><a href='$PHP_SELF?acao=cpperiodo&gex_codigo=$rr[gex_codigo]&med_codigo=$rr[med_codigo]'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/copiar.png border=0></a></td>
		 <td width=120><a href=$PHP_SELF?acao=form_edit&gex_codigo=$rr[gex_codigo]&med_codigo=$rr[med_codigo]><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.png border=0></a></td>
		 <td width=120><a href=$PHP_SELF?acao=del&gex_codigo=$rr[gex_codigo]&med_codigo=$rr[med_codigo]><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.png border=0></a></td>
		 <td width=120><a href=$PHP_SELF?acao=delproc&gex_codigo=$rr[gex_codigo]&med_codigo=$rr[med_codigo]><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagarproc.png border=0></a></td>		 
		</tr>";
//	}
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
	
$sql = pg_query("select *from medico where prestador_servico ='S' order by med_nome");
#and med_codigo = 2165 
 while($row=pg_fetch_array($sql)) {
 echo ($row[med_codigo]==$h_med_codigo)?"<option value='$PHP_SELF?quota_diaria=$quota_diaria&vlr_mensal=$vlr_mensal&gex_codigo=$gex_codigo&id_login=$id_login&acao=form_add&h_med_codigo=$row[med_codigo]&vlr_mensal=$vlr_mensal&quota_diaria=$quota_diaria&h_gex_periodo=$h_gex_periodo' selected>$row[med_nome]</option>":"<option value='$PHP_SELF?id_login=$id_login&acao=form_add&h_med_codigo=$row[med_codigo]&gex_codigo=$gex_codigo&quota_diaria=$quota_diaria&vlr_mensal=$vlr_mensal'>$row[med_nome]</option>";
}
 echo "</select></td>
	</tr>";
	echo "
	<input type=hidden name=quota_diaria value=1>
	<tr>
	 <td class='bordaN' width=15%>Periodo:</td>
	 <td width=220><select name=gex_periodo class=box onChange=\"javascript:changeLocation(this)\">
	  <option>::.. Periodo ..::</option>";
	$sqlP = "SELECT distinct(gem.gex_codigo),
				    to_char(gex_periodo, 'DD/MM/YYYY') as gex_periodo2
			   FROM grade_exame_mensal as gem
			  WHERE gem.gex_codigo 
			    NOT in (SELECT distinct(gex_codigo) 
					      FROM grade_exame) 
		   ORDER BY gem.gex_codigo desc";
	$query = pg_query($sqlP);
 while($rr=pg_fetch_array($query)) {
 echo ($rr[gex_periodo2]==$h_gex_periodo)?"<option selected value='$PHP_SELF?quota_diaria=$quota_diaria&vlr_mensal=$vlr_mensal&gex_codigo=$rr[gex_codigo]&id_login=$id_login&acao=form_add&h_med_codigo=$h_med_codigo&vlr_mensal=$vlr_mensal&quota_diaria=$quota_diaria&h_gex_periodo=$rr[gex_periodo2]'>$rr[gex_periodo2]</option>":"<option value='$PHP_SELF?id_login=$id_login&acao=form_add&h_med_codigo=$h_med_codigo&vlr_mensal=$vlr_mensal&quota_diaria=$quota_diaria&h_gex_periodo=$rr[gex_periodo2]&gex_codigo=$rr[gex_codigo]&quota_diaria=$quota_diaria&vlr_mensal=$vlr_mensal'>$rr[gex_periodo2]</option>";
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
	<input type=hidden name=quota_diaria value=1>
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
     $proc = pg_fetch_array(pg_query("select TRANSLATE(proc_nome, 'ZZZ-', '') as newprocnome,* from procedimento where proc_codigo = $rrw[proc_codigo] order by TRANSLATE(proc_nome, 'ZZZ-', '')"));
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

	if (cadastraPeriodo($new_gex_periodo, $h_med_codigo, $usr_codigo_cad, $usr_codigo_alt)){
		echo "<SCRIPT LANGUAGE=\"JavaScript\">";
		echo "setTimeout(\"location='$PHP_SELF?id_login=$id_login&acao=form_add&gex_codigo=$gex_codigo'\", 1);";
		echo "alert('Cadastro de Periodo Realizado com SUCESSO!');";
		echo "</SCRIPT>";
	}else{
		echo "<SCRIPT LANGUAGE=\"JavaScript\">";
		echo "setTimeout(\"location='$PHP_SELF?id_login=$id_login&acao=form_add&h_med_codigo=$h_med_codigo'\", 1);";
		echo "alert('Periodo ja cadastrado!');";
		echo "</SCRIPT>";		
	}

  
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

$q = "BEGIN; ";
/*$stmt = "SELECT
         to_char(MAX(gex_periodo), 'dd/mm/yyyy') as max,
         ('$h_gex_periodo' > MAX(gex_periodo)+29) as ok_max ,
         TO_CHAR(MAX(gex_periodo)+30,'dd/mm/YYYY') as prox_max
         FROM grade_exame_mensal AS m
         WHERE med_codigo = {$h_med_codigo} ";
	$per_row = db_getRow($stmt);*/

     $conta=1;
               /*$dataini = $per_row[0];
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
                       (med_codigo, proc_codigo, graex_qtde, graex_data, usr_codigo_cad,gex_codigo)
                       VALUES
                       ($h_med_codigo, $dest_list[$i], '0', '$datainc', $id_login,$gex_codigo);";
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
               echo "setTimeout(\"location='$PHP_SELF?id_login=$id_login&acao=cad_preco&med_codigo=$h_med_codigo&gex_periodo=$h_gex_periodo&gex_codigo=$gex_codigo'\", 1);";//"
               echo "</SCRIPT>";
}

if($acao=="continuar_editar") {
  if(empty($dest_list)) { 
               echo "<SCRIPT LANGUAGE=\"JavaScript\">";//"
 	       echo "alert('***** ATENCAO *****     Atualizar estes procedimentos pode influenciar na quantidade de vagas diarias');";
               echo "setTimeout(\"location='$PHP_SELF?id_login=$id_login&acao=upd_preco&gex_codigo=$gex_codigo&med_codigo=$h_med_codigo&gex_periodo=$h_gex_periodo'\", 1);";
               echo "</SCRIPT>";
exit();
  }
   foreach ($dest_list  as $i => $value) {

$q = "BEGIN; ";
/*$stmt = "SELECT
         to_char(MAX(gex_periodo), 'dd/mm/yyyy') as max,
         ('$h_gex_periodo' > MAX(gex_periodo)+29) as ok_max ,
         TO_CHAR(MAX(gex_periodo)+30,'dd/mm/YYYY') as prox_max
         FROM grade_exame_mensal AS m
         WHERE med_codigo = {$h_med_codigo} ";
	$per_row = db_getRow($stmt);*/

     $conta=1;
 /*              $dataini = $per_row[0];
               $datainc = $per_row[0];
               $datafin = $per_row[2];*/
			   echo $gex_codigo;
			   $dta = pg_fetch_array(pg_query("SELECT GEX_PERIODO FROM GRADE_EXAME_MENSAL WHERE GEX_CODIGO = $gex_codigo"));
			   $datainc = formatarData($dta[gex_periodo]);
			   $dataini = formatarData($dta[gex_periodo]);
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
                       (med_codigo, proc_codigo, graex_qtde, graex_data, usr_codigo_cad,gex_codigo,graex_status)
                       VALUES
                       ($h_med_codigo, $dest_list[$i], '0', '$datainc', $id_login,$gex_codigo,'S');";
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
               echo "setTimeout(\"location='$PHP_SELF?id_login=$id_login&acao=upd_preco&gex_codigo=$gex_codigo&med_codigo=$h_med_codigo&gex_periodo=$h_gex_periodo'\", 1);";//"
               echo "</SCRIPT>";

}

if($acao=="cad_preco") {
 echo "<fieldset><legend>Digite a Quantidade de Vagas diarias para Cada Procedimento</legend>";
 echo "<form method=post action='$PHP_SELF' onsubmit='return validaform()' name='finalizar'>
	<input type=hidden name=acao value=finalizar>
	<input type=hidden name=med_codigo value=$med_codigo>
	<input type=hidden name=gex_periodo value=$gex_periodo>
	<table width=100% cellspacing=1 cellpadding=5 border=0>
	<tr bgcolor='#000000'>
	 <td><font color='#FFFFFF'>Procedimento</font></td>
	 <td><font color='#FFFFFF'>Quantidade Diaria</font></td>
	</tr>";
	$gex_periodo = menorData($gex_periodo);//retorna a menor data que possui cadastro no mês da data passada como parâmetro
	$sql = pg_query("select TRANSLATE(proc_nome, 'ZZZ-', '') as newprocnome,* from grade_exame as grm left join procedimento as proc on proc.proc_codigo = grm.proc_codigo where grm.med_codigo = '$med_codigo' and grm.graex_data = '$gex_periodo' order by TRANSLATE(proc_nome, 'ZZZ-', '')");
  while($row = pg_fetch_array($sql)) {
  echo "
	<input type=hidden name=proc_codigo[] value=$row[proc_codigo]>
	<tr bgcolor='#f1f1f1'>
	 <td>$row[newprocnome]</td>
	 <td><input type=text name=graex_qtde[] class=box size=4 maxsize=4></td>
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

if($acao=="newstatus") {
   $sql = pg_query("update grade_exame set graex_status='$status' where graex_codigo = '$graex_codigo'");
               echo "<SCRIPT LANGUAGE=\"JavaScript\">";//"
               echo "setTimeout(\"location='$PHP_SELF?id_login=$id_login&acao=upd_preco&gex_codigo=$gex_codigo&med_codigo=$med_codigo&gex_periodo=$gex_periodo'\", 1);";//"
               echo "</SCRIPT>";
}

if($acao=="upd_preco") {
 echo "<fieldset><legend>Digite a Cota Diaria para Cada Procedimento</legend>";
 echo "<form method=post action='$PHP_SELF' onsubmit='return validaform()' name='finalizar'>
	<input type=hidden name=acao value=finalizar>
	<input type=hidden name=med_codigo value=$med_codigo>
	<input type=hidden name=gex_periodo value=$gex_periodo>
	<input type=hidden name=gex_codigo value=$gex_codigo>
	<table width=100% cellspacing=1 cellpadding=5 border=0>
	<tr bgcolor='#000000'>
         <td><font color='#FFFFFF'>Status</font></td>
	 <td><font color='#FFFFFF'>Procedimento</font></td>
	 <td><font color='#FFFFFF'>Quantidade por Procedimento</font></td>
	</tr>";
	$dt = explode("-",$gex_periodo);
	$gex_periodo = "$dt[2]/$dt[1]/$dt[0]";
	$gex_periodo = menorData($gex_periodo);
$sql = pg_query("select TRANSLATE(proc_nome, 'ZZZ-', '') as newprocnome,grm.graex_qtde as graex_qtde,* from grade_exame as grm left join procedimento as proc on proc.proc_codigo = grm.proc_codigo where grm.gex_codigo = '$gex_codigo' and grm.med_codigo = '$med_codigo' and grm.graex_data = '$gex_periodo' order by TRANSLATE(proc_nome, 'ZZZ-', '')");

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
	 <td><input type=text name=graex_qtde[] value='$row[graex_qtde]' class=box size=4 maxsize=4></td>
	</tr>";
}
 echo "</table>";
 echo "</fieldset>";
 echo "<table width=100% cellspacing=1 cellpadding=5 border=0>";
 echo "<tr>
	<td align=center><a href=exa_lab_qtde.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a>&nbsp;<input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.png></td>
	</tr>";
 echo "</table>";
}

if($acao=="finalizar") {
	
  foreach ($proc_codigo  as $i => $value) {
     $conta=1;
	 		$caracter = "/";
	 		$verFormatoData = strpos($gex_periodo,$caracter);
			if($verFormatoData == true){
				$dataini = $gex_periodo;
			}else{
			   $dataini = $gex_periodo;
			}
			   $datainc = $gex_periodo;
			
			   $qtde = contaDiasMes($dataini);
               while ($conta <= $qtde)
               {
				  
               //verificar feriado
               $vediafer = "SELECT count(*) from feriado WHERE fer_data = '$datainc'";
               $vefer = db_getRow($vediafer);

               if ($vefer[0] == 0) {
                   //verificar dia da semana
                   $vediasem = "SELECT EXTRACT(dow from TO_DATE('$dataini', 'dd/mm/yyyy'))";
                   $vedia = db_getRow($vediasem);
                   if (($vedia[0] <> 0) && ($vedia[0]<>6)) {
					   
                      //gravar dados
                     if ($manut_row['proc_tipo_manut'] == 'P');
					   $q .= "UPDATE grade_exame SET graex_qtde = '$graex_qtde[$i]' 
						  WHERE med_codigo = '$med_codigo' and graex_data = '$datainc' and proc_codigo = '$proc_codigo[$i]';";
							
 			
                     $sqldata = "SELECT CAST('$datainc' AS DATE) + INTERVAL '1 DAYS' AS Data;";
                     $rowdata = db_getRow($sqldata);
                     $conta = $conta + 1;
                     $datainc = $rowdata[0];
                   } //fim if diasemana
                   else
             {
                     $sqldata = "SELECT CAST('$datainc' AS DATE) + INTERVAL '1 DAYS' AS Data;";
                     $rowdata = db_getRow($sqldata);
                     $conta = $conta + 1;
                     $datainc = $rowdata[0];
                   } //fim do else - final de semana
                } // fim if feriado
                else
                {
                $sqldata = "SELECT CAST('$datainc' AS DATE) + INTERVAL '1 DAYS' AS Data;";
                $rowdata = db_getRow($sqldata);
                $conta = $conta + 1;
                $datainc = $rowdata[0];
                } //fim do else - feriado
               } //fim while
		
 		$rq = db_query($q);
}//fim do foreach

               echo "<SCRIPT LANGUAGE=\"JavaScript\">";//"
 	       echo "alert('Finalizado com SUCESSO!');";
               echo "setTimeout(\"location='$PHP_SELF?id_login=$id_login'\", 1);";//"
               echo "</SCRIPT>";

}

if($acao=="finalizar_upd") {
  foreach ($graex_codigo  as $i => $value) {
   $stmt = "UPDATE grade_exame SET graex_qtde = '$graex_qtde[$i]' WHERE gex_codigo = '$gex_codigo' and med_codigo = '$med_codigo' and graex_data = '$gex_periodo' and graex_codigo = '$graex_codigo[$i]'";
   $sql = pg_query($stmt) or die(pg_last_error());
  }
               echo "<SCRIPT LANGUAGE=\"JavaScript\">";//"
 	       echo "alert('Finalizado com SUCESSO!');";
               echo "setTimeout(\"location='$PHP_SELF?id_login=$id_login'\", 1);";//"
               echo "</SCRIPT>";

}

if($acao=="del") {
   $stmt = "DELETE from grade_exame where gex_codigo = '$gex_codigo'";
   $sql = pg_query($stmt) or die(pg_last_error());
   $stmt2 = "DELETE from grade_exame_mensal where gex_codigo = '$gex_codigo'";
   $sql2 = pg_query($stmt2) or die(pg_last_error());
               echo "<SCRIPT LANGUAGE=\"JavaScript\">";//"
 	       echo "alert('Excluido com SUCESSO!');";
               echo "setTimeout(\"location='$PHP_SELF?id_login=$id_login'\", 1);";//"
               echo "</SCRIPT>";

}

if($acao=="cpperiodo") {
	if ($acao2=="addperiodo"){
			if (cadastraPeriodo($new_gex_periodo, $h_med_codigo, $usr_codigo_cad, $usr_codigo_alt)){
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
	$origem = $rr['gex_periodo'];

	echo "<h3>Copiar De:</h3>";
	echo "<input type=hidden name=h_med_codigo value=$rr[med_codigo]>
		  <input type=hidden name=h_gex_periodo value=$origem>
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
echo "<table width=90% cellspacing=1 cellpadding=5 border=0>
	<tr>
	 <td class='bordaR' width=15%>Prestador de Servico:</td>
	 <td colspan=5>";
$med = pg_fetch_array(pg_query("select *from medico where med_codigo = '$graex[med_codigo]' order by med_nome"));
 echo $med[med_nome];
 echo "</td>
	</tr>
	<input type=hidden name=quota_diaria value=1>
	<tr>
	 <td class='bordaR' width=15%>Periodo:</td>
	 <td width=220><select name=gex_periodo class=box onChange=\"javascript:changeLocation(this)\">
	  <option>::.. Periodo ..::</option>";
if($h_med_codigo=="") { $h_med_codigo = $med[med_codigo]; }

$query = pg_query("select to_char(gex_periodo, 'DD/MM/YYYY') as gex_period,gex_codigo from grade_exame_mensal where med_codigo = $h_med_codigo and gex_codigo not in (select distinct(gex_codigo) from grade_exame where med_codigo = $h_med_codigo) order by gex_periodo desc") or die(pg_last_error());

 while($rr=pg_fetch_array($query)) {
 echo ($rr[gex_period]==$gex_periodo)?"<option selected value='$PHP_SELF?gex_periodo_copy=$gex_periodo_copy&gex_codigo_new=$rr[gex_codigo]&id_login=$id_login&acao=cpperiodo&h_med_codigo=$med[med_codigo]&gex_periodo=$rr[gex_period]&gex_codigo=$gex_codigo'>$rr[gex_period]</option>":"<option value='$PHP_SELF?id_login=$id_login&acao=cpperiodo&h_med_codigo=$med[med_codigo]&gex_periodo=$rr[gex_period]&gex_codigo=$gex_codigo&gex_codigo_new=$rr[gex_codigo]&gex_periodo_copy=$gex_periodo_copy'>$rr[gex_period]</option>";
}
 echo "</select>&nbsp;
	  <input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/btndel.png border=0 align='absmiddle' onclick=\"return send('delperiodo');\">
       </td>
  <td width=10>&nbsp;</td>
                <td width=150 align=right class='bordaR'>Novo Periodo: </td>\n
                <td width=70><input type=text name=new_gex_periodo size='12' class='boxl' id='data' maxlength='10' onKeypress=\"return Ajusta_Data(this, event);\"></td>\n
                <td>
				<input type='hidden' name='gex_codigo' value='$gex_codigo'>
				<input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/add_on.gif border=0 onclick=\"return send('addperiodo2');\"></td>\n
              </tr>
	</table>";


 echo "</fieldset>";

 echo "<br><br>
	<div align=center><a href='$PHP_SELF?gex_codigo_new=$gex_codigo_new&gex_periodo_copy=$gex_periodo_copy&gex_codigo=$gex_codigo&origem=$origem&gex_periodo=$gex_periodo&acao=cpall&med_codigo=$graex[med_codigo]'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/copiar_dados_2_on.jpg border=0 ></a></div></form>";
}
if($acao=="cpall") {
$h_med_codigo = $med_codigo;
$qy = pg_query("select proc_codigo,graex_qtde from grade_exame 
		where med_codigo = $med_codigo
		and gex_codigo = $gex_codigo
		and graex_qtde != 0
		group by proc_codigo,graex_qtde");



while($rr = pg_fetch_array($qy)) {

$q = "BEGIN; ";

     $conta=1;

			   $dataini = $gex_periodo_copy;//=origem
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
                       (med_codigo, proc_codigo, graex_qtde, graex_data, usr_codigo_cad,gex_codigo)
                       VALUES
                       ($h_med_codigo, $rr[proc_codigo], '$rr[graex_qtde]', '$datainc', $id_login,$gex_codigo_new)";
					   
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
         // $q .= " COMMIT;";
		  

//echo $q."<br><br>";

}

               echo "<SCRIPT LANGUAGE=\"JavaScript\">";//"
 	       echo "alert('Copiado com SUCESSO!');";
               echo "setTimeout(\"location='$PHP_SELF?id_login=$id_login'\", 1);";//"
               echo "</SCRIPT>";

		
   /*            echo "<SCRIPT LANGUAGE=\"JavaScript\">";//"
 	       echo "alert('Data Invalida !');";
               echo "setTimeout(\"location='$PHP_SELF?id_login=$id_login'\", 1);";//"
               echo "</SCRIPT>";*/
	}
	



if($acao=="delproc") {
$qy = pg_query("select distinct(grx.proc_codigo) as proc_codigo,TRANSLATE(proc_nome, 'ZZZ-', '') as newprocnome from grade_exame as grx left join procedimento as p on p.proc_codigo = grx.proc_codigo
		where grx.med_codigo = 2165 
		and grx.gex_codigo = $gex_codigo");

echo "Total de Procedimentos <b>".pg_num_rows($qy)."</b><br><br>";

 echo "<fieldset><legend>OS PROCEDIMENTOS LISTADOS NUNCA RECEBER&Atilde;O UM AGENDAMENTO. </legend>";
 echo "<table width=100% cellspacing=1 cellpadding=5 border=0>
	<tr bgcolor='#000000'>
	 <td><font color='#FFFFFF'>Procedimento</font></td>
	 <td width=420>&nbsp;</td>
	</tr>";
while($rr = pg_fetch_array($qy)) {
$sel = pg_query("select distinct(proc_codigo) from agendamento_exame_lista where proc_codigo = $rr[proc_codigo]");
if(pg_num_rows($sel)==0) {
 echo "<tr bgcolor='#f1f1f1'>
	 <td>$rr[newprocnome]</td>
	 <td width=120><a href=$PHP_SELF?acao=delprocall&gex_codigo=$gex_codigo&proc_codigo=$rr[proc_codigo]><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.png border=0></a></td>
	</tr>";
 }
}
 echo "</table>";
 echo "</fieldset>";
 echo "<table width=100% cellspacing=1 cellpadding=5 border=0>";
 echo "<tr>
	<td align=center><a href=exa_lab_qtde.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a>&nbsp;</td>
	</tr>";
 echo "</table>";

}

if($acao=="delprocall") {
   $stmt = "DELETE FROM grade_exame WHERE gex_codigo = '$gex_codigo' and med_codigo = '2165' and proc_codigo = '$proc_codigo'";
   $sql = pg_query($stmt) or die (pg_last_error());

               echo "<SCRIPT LANGUAGE=\"JavaScript\">";//"
 	       echo "alert('Excluido com SUCESSO!');";
               echo "setTimeout(\"location='$PHP_SELF?id_login=$id_login&acao=delproc&gex_codigo=$gex_codigo'\", 1);";//"
               echo "</SCRIPT>";
	

}
?>

