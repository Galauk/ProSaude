<script language="JavaScript" type="text/javascript" src="funcoes.js"></script>
<script>

  
    function ajaxInit() {
        var req;

        try {
            req = new ActiveXObject("Microsoft.XMLHTTP");
        } catch(e) {
        try {
 
        req = new ActiveXObject("Msxml2.XMLHTTP");
 
        } catch(ex) {
 
        try {
 
        req = new XMLHttpRequest();
 
        } catch(exc) {
 
 alert("Esse browser nao tem recursos para uso do Ajax");
 
    req = null;
 
        }
 
    }
 
 }

    return req;

}


function changeLocation(menuObj)
{
   var i = menuObj.selectedIndex;

   if(i > 0)
   {
      window.location = menuObj.options[i].value;
   }
}

function valida_form()
{
	if(document.nv_periodo.med_codigo.value == '')
	{
		alert("Por favor escolha o medico");
		return false;
	} else if(document.nv_periodo.esp_codigo.value == '') {
		alert("Por favor escolha a especialidade");
		return false;
	} else if(document.nv_periodo.agt_item.value == '') {
		alert("Por favor escolha o item");
		return false;
	} else if(document.getElementById('nvdata').value == '') {
		alert("Por favor digite a nova data para o periodo!");
		document.getElementById('nvdata').focus();
		return false;
	} else {
		return true;
	}
}

</script>
<?
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>
   	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	require_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
	cabecario();
//------------------------------------------------------------------>




//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
//------------------------------------------------------------------>

reglog($id_login,"Acessando Manutencao de Agentes");
if($act=="deldate") {
reglog($id_login,"Apagando Periodo do agente Cod: $agt_codigo");
   $ver = pg_query("select *from grade_mensal where med_codigo='$med_codigo' and esp_codigo='$esp_codigo' and age_item='$agt_item' and grm_periodo='$grm_periodo' and grm_qtde != 0");
if(pg_num_rows($ver)=="0") { 
  $q = "delete from grade_mensal where med_codigo='$med_codigo' and esp_codigo='$esp_codigo' and age_item='$agt_item' and grm_periodo='$grm_periodo'";
  $rq=pg_query($q);
     echo "<table width=100% cellspacing=0 cellpadding=0 border=1>
	   <tr>
	    <td align=center>Excluido com Sucesso</td>
	   </tr>
	   </table>";
     echo "<SCRIPT LANGUAGE=\"JavaScript\">
                  setTimeout(\"location='$PHP_SELF?id_login=$id_login&med_codigo=$med_codigo&esp_codigo=$esp_codigo&agt_item=$agt_item'\", 2000);
              </SCRIPT>";
  } else {
     echo "<table width=100% cellspacing=0 cellpadding=0 border=0>
	   <tr>
	    <td align=center>Impossivel APAGAR Pois Existem Dados na Manutencao</td>
	   </tr>
	   </table>";
     echo "<SCRIPT LANGUAGE=\"JavaScript\">
                  setTimeout(\"location='$PHP_SELF?id_login=$id_login&med_codigo=$med_codigo&esp_codigo=$esp_codigo&agt_item=$agt_item'\", 2000);
              </SCRIPT>";

  }

}

if($act=="newdate") {
  
reglog($id_login,"Adicionando nova data para o agente Cod: $agt_codigo");
   $sql=pg_query("select *from agente");
   $erro = 0;
   $msg = '';
 while($agt=pg_fetch_array($sql)) {

if ($med_codigo == ''){
	$msg .= "vc deve selecionar um m�dico antes<br>";
	$erro++;
}

if ($esp_codigo == ''){
	$msg .= "vc deve selecionar uma especialidade antes";
	$erro++;
} 
if ($erro > 0) {
echo $msg;
echo "<a href=>voltar</a>";
	exit;
}
  $query = "select grm_periodo
          from grade_mensal
          where med_codigo = $med_codigo
          and esp_codigo = $esp_codigo
          and agt_codigo = $agt[agt_codigo]
          and age_item = '$agt_item'
          and grm_periodo <= '{$nvdata}'
          order by 1 desc limit 1";
          
  $exec_sql = db_query($query);
  
  $data_periodo = pg_fetch_array($exec_sql);
  
  if($data_periodo[0] != "")
  {
  
    $query = "select (('{$data_periodo[0]}'::date + interval '1 month') - interval '1 day')::date - ('{$data_periodo[0]}')";
    
    $quantidade = db_get($query);
    
    $query = "select *
            from grade_mensal
            where med_codigo = $med_codigo
            and esp_codigo = $esp_codigo
            and agt_codigo = $agt[agt_codigo]
            and age_item = '$agt_item'
            and '$nvdata'
            between grm_periodo
            and (grm_periodo + interval '$quantidade day')";
            
    $exec_sql = db_query($query);
    
    if(pg_num_rows($exec_sql) == 0)
    {
        $query = "select grm_periodo
                from grade_mensal
                where med_codigo = $med_codigo
                and esp_codigo = $esp_codigo
                and agt_codigo = $agt[agt_codigo]
                and age_item = '$agt_item'
                and grm_periodo >= '{$nvdata}'
                order by 1 limit 1";
        $exec_sql = db_query($query);
    } else {
      echo "
          <script>
            alert('Este periodo nao pode ser cadastrado.');
            location.href = '".$PHP_SELF."?".$QUERY_STRING."';
          </script>
          ";
      exit();
    }
    
    $data_periodo = pg_fetch_array($exec_sql);
  }
  if($data_periodo[0] != "")
  {
    $query = "select (('$nvdata'::date + interval '1 month') - interval '1 day')::date";
    
    $d = db_get($query);

    $query = "select '$d'
            where '$d' between('$data_periodo[0]'::date) and (('$data_periodo[0]'::date + interval '1 month') - interval '1 day')";
            
    $exec_sql = db_query($query);
    
    if(pg_num_rows($exec_sql) > 0)
    {
      echo "
          <script>
            alert('Este periodo nao pode ser cadastrado.');
            location.href = '".$PHP_SELF."?".$QUERY_STRING."';
          </script>
          ";
      exit();
    }
    
  }
  	
  $q = "insert into grade_mensal ( " .
            "med_codigo, " .
            "grm_qtde, " .
            "esp_codigo, " .
            "agt_codigo, " .
            "grm_periodo, " .
            "age_item " .
            ") values ( " .
            "'$med_codigo', " .
            "'0', " .
            "'$esp_codigo', " .
            "'$agt[agt_codigo]', " .
            "'$nvdata', " .
            "'$agt_item' " .
            ")";
  $rq=pg_query($q);
 }
     echo "<SCRIPT LANGUAGE=\"JavaScript\">
                  setTimeout(\"location='$PHP_SELF?id_login=$id_login&med_codigo=$med_codigo&esp_codigo=$esp_codigo&agt_item=$agt_item'\", 0);
              </SCRIPT>";

}
 if(empty($acao)) {

 echo "<table width=100% height='100%' align=center cellspacing=2 cellpadding=4 border=0 style='border-top:1px solid;border-left:1px solid;border-right:1px solid;border-bottom:1px solid;border-color:909090'>\n
         <tr>\n
          <td>\n
             <table width=100% align=center cellspacing=3 cellpadding=0 border=0>\n
              <tr>\n
				<td align=right>Per&iacute;odo:</td>\n
				<td>";
				 $sql = pg_query("select * from grade_exame_mensal where gex_codigo = '$gex_codigo'");
				 $dados = pg_fetch_array($sql);
				 $per = $dados[gex_periodo];
				 $dt = formatarData($dados[gex_periodo]);
				 echo "$dt
		 		</td>\n
              </tr>\n
              <tr>\n
				<td align=right>Prestador de servi&ccedil;o:</td>\n
				<td>";
		 $sql = pg_query("select * from medico where med_codigo = '$med_codigo'");
		 $dados = pg_fetch_array($sql);
		 echo "$dados[med_nome]";
		 echo "</td>\n
              </tr>\n
              <tr>\n
				<td align=right>Procedimento:</td>\n
				<td><select name=proc_item class=boxr onChange=\"javascript:changeLocation(this)\">\n";
				echo "<option selected='selected'>...</option>\n"; 
		$select = pg_query("select distinct(agl.proc_codigo) as proc_codigo,TRANSLATE(proc.proc_nome, 'ZZZ-', '') as newprocnome from grade_exame as agl left join procedimento as proc on proc.proc_codigo = agl.proc_codigo where agl.gex_codigo = $gex_codigo ORDER BY newprocnome");
	    while($array = pg_fetch_array($select)) { 
	    	echo ($proc_codigo==$array[proc_codigo])?"<option value=exa_vagas_por_unidade.php?gex_periodo=$gex_periodo&id_login=$id_login&gex_codigo=$gex_codigo&med_codigo=$med_codigo&proc_codigo=$array[proc_codigo] selected>$array[newprocnome]</option>":"<option value=exa_vagas_por_unidade.php?gex_periodo=$gex_periodo&id_login=$id_login&gex_codigo=$gex_codigo&med_codigo=$med_codigo&proc_codigo=$array[proc_codigo]>$array[newprocnome]</option>"; 
		}
	echo "</select></td>\n
              </tr>\n
             </table>\n";
  echo "<table width=100% cellspacing=0 cellpadding=0 border=0 align=center>\n
         <tr>\n
          <td align=center>\n
           <iframe name=frameprincipal src=exa_distribuir_vagas_iframe.php?gex_periodo=$gex_periodo&id_login=$id&periodo=$per&med_codigo=$med_codigo&proc_codigo=$proc_codigo&gex_codigo=$gex_codigo frameborder=no marginheight=0 marginwidth=0 scrolling=yes width=100% height=400 >\n</iframe>\n
	  </td>\n
	 </tr>\n
	</table>\n";
}
?>
