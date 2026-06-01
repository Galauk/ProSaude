<?php
/**
 * Arquivo iframe da "Manutencao de Grupos de Agenda"
*/

//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>
session_start();
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
cabecario();
//------------------------------------------------------------------>

reglog($id_login,"Abrindo Lista de Agentes");

?>
<script src="script.js" language="javascript" type="text/javascript"></script>
<script>
    function ajaxInit() {
        var req;
        try {
            req = new ActiveXObject("Microsoft.XMLHTTP");
        } 
        catch(e) {
                try {
                    req = new ActiveXObject("Msxml2.XMLHTTP");
                } 
            catch(ex) {
            try {
                    req = new XMLHttpRequest();
            } 
            catch(exc) {
             alert("Esse browser não tem recursos para uso do Ajax");
              req = null;
            }
 
        }  
 
        }
    return req;
}
    
    function alteragrm(grm_codigo, old_qtde, grm_qtde,id_medico,id_esp,id_agto,grm_periodo,periodo,agt_codigo,id_login)

     { 
       
        if (Number(grm_qtde) <= -1) { 
            
            alert("Quantidade deve ser maior que zero!"); 
            document.getElementById('frm_grm_qtde' + grm_codigo).value = old_qtde; 
            document.getElementById('frm_grm_qtde' + grm_codigo).focus(); 
            return false; 
           
        } 
        var endereco='ajax/update/agendamento/agdto_ajax.php?grm_codigo='+grm_codigo+
        '&old_qtde='+old_qtde+'&grm_qtde='+grm_qtde+'&periodo='+periodo+'&cod_medico='+id_medico+'&cod_esp='+id_esp+'&cod_agto='+id_agto+'&grm_periodo='+grm_periodo+'&id_login='+id_login; 
         //  alert(endereco) 
        ajax = ajaxInit();
        
        if(ajax) {
            ajax.open("GET", endereco , true);

            ajax.onreadystatechange = function() {
            if(ajax.readyState == 4) {
                if(ajax.status == 200) {
                   
                    //document.getElementById('hf').innerHTML = ajax.responseText;
                    document.getElementById('usr').innerHTML = ajax.responseText;
                                      
                } else {
                    alert(ajax.statusText);
                }           
            }
        }    
   ajax.send(null);
    
    }
 }

</script>
<?php

//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
//------------------------------------------------------------------>
echo "<div style='font-weight:bold;' id='upd'>Atualiza&ccedil;&atilde;o:<label style='font-weight:bold;color:#10d' id='usr'></label></div>"; 
 echo "<table class='lista' higth=80%  cellspacing=1 cellpadding=4 border=0>\n
	<tr>\n
	    <th>\n
        Grupos</th>\n
		<th>\n
        Unidade</th>\n
	    <th width=80>\n
        Qtd./Age</th>\n
	    <th>\n
        Cadastrada Por</th>\n
	    <th>\n
        Alterado Por</th>\n
	</tr>\n";
$i=0;
  $sql = pg_query ("select * from grade_mensal where esp_codigo = '$esp_codigo' 
                    and med_codigo = '$med_codigo' 
                    and age_item = '$agt_item' and grm_periodo='$grm_periodo'");


echo "<form name='ff' method='get' action='$PHP_SELF'>\n
      <input type=hidden name='grm_codigo' id='grm_codigo' value='' \>\n
      <input type=hidden name='id_login' id='$id_login' value='' \>\n";

   while($rr=pg_fetch_array($sql)) {
   $nagt = pg_fetch_array(pg_query("select 
   agt_codigo, agt_numero, uni_codigo,agt_responsavel as agtresp,agt_descricao as agtdesc,
    case when (agt_responsavel is null) then agt_numero||'-'|| agt_descricao  else agt_numero||'-'||agt_responsavel end as agt_responsavel  , agt_descricao
   from agente where agt_codigo='$rr[agt_codigo]' order by agt_numero"));
	
	$sql2 = "select uni_desc from unidade where uni_codigo = $nagt[uni_codigo]";
	//echo "<br><br>";
	$exec_sql = pg_query($sql2);
	$linha = pg_fetch_array($exec_sql);

      $ag = pg_query("select *from agendamento where agt_codigo = '$nagt[agt_codigo]' and esp_codigo = '$esp_codigo' and med_codigo='$med_codigo' and age_data >= to_date('$grm_periodo','YYYY-MM-DD') and age_data <= to_date('$grm_periodo','YYYY-MM-DD') + interval '30 day'") or die(pg_last_error());

#echo "select *from agendamento where agt_codigo = '$nagt[agt_codigo]' and esp_codigo = '$esp_codigo' and med_codigo='$med_codigo' and
# age_tipo='$agt_item' and uni_codigo='$nagt[uni_codigo]' and age_data >= to_date('$grm_periodo','YYYY-MM-DD') and age_data <= to_date('$grm_periodo','YYYY-MM-DD') + interval '30 day'";
	$teste = pg_num_rows($ag);
       if(($teste==$rr[grm_qtde] and $teste!=0)) { 
	  $cor = "red"; 
       } else { 
         if($teste>0) { 
            $cor = "green"; 
         } else { 
	    $cor = "blue";
         }
       }

echo "<tr>\n
    	 <td>\n
             $nagt[agtdesc]</td>\n
		<td>&nbsp;";
             echo ($linha[0] == "" ? "&nbsp;" : $linha[0]);
echo "</td>\n
	     <td width=20 align=center>\n
        <input type=text id='cx'  name=grm_qtde value='$rr[grm_qtde]' class=boxagente onChange=\"alteragrm($rr[grm_codigo],$i,this.value,$med_codigo,$esp_codigo,'$agt_item','$grm_periodo','$periodo','$rr[agt_codigo]','$id_login')\" ><b>/</b><font color=$cor size=2><b>$teste</b></td>\n 
	    <td width=150 align=center>\n
     $rr[usr_login_cad] &nbsp;</td>\n
	    <td  width=200 id='s$i' >\n
          {$rr['usr_login_alt']} &nbsp; </td>\n 
	</tr>"; //$rr[usr_login_alt]&nbsp;
}
echo "</form>\n
      </table>\n";

