<?php
/**
 * iframe do Agendamento de Exames (agendar_exame.php)
*/

//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>
session_start();
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
//verauth($id_login);
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
cabecario();
include_once $_SESSION[root].$_SESSION[modulo]."config.inc.php";
include_once $_SESSION[root].$_SESSION[modulo]."funcoes.agendar_exame.php";
reglog($id_login,"Acessando Agendamento de Exames");
//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
//------------------------------------------------------------------>
?>

<script language="JavaScript" type="text/javascript" src="funcoes.js"></script>
<script>
function changeLocation(menuObj)
{
   var i = menuObj.selectedIndex;

   if(i > 0)
   {
      window.location = menuObj.options[i].value;
   }
}
// -----------------------------------------------------------------------------
function envia_form(menuObj,form_id)
{
	var i = menuObj.selectedIndex;
	if(i > 0)
	{
		document.getElementById( form_id ).submit();
	}
}
// -----------------------------------------------------------------------------
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
// -----------------------------------------------------------------------------
function altera_gexlista(id_login, agexl_codigo, dt, op)
{ 
        
        var endereco='ajax/update/agendamento/agenda_exame_ajax.php?id_login='+id_login+'&agexl_codigo='+agexl_codigo +'&data='+dt+'&op='+op;
//         alert(endereco)
//         return;
        
        ajax = ajaxInit();
        
        if(ajax) {
            ajax.open("GET", endereco , true);

            ajax.onreadystatechange = function() {
            if(ajax.readyState == 4) {
                if(ajax.status == 200) {
                   
                    //document.getElementById('hf').innerHTML = ajax.responseText;
                    //document.getElementById('usr').innerHTML = ajax.responseText;
					if( ajax.responseText == 'reload' )
					{	
						aux = document.location.href;
						document.location.href = aux;
					}
					else if( ajax.responseText == 'Erro:1' )
					{
						msg = "O Procedimento já foi cadastrado para este paciente nesta data !";
						alert( msg );
						aux = document.location.href;
						document.location.href = aux;
					}
					else if( ajax.responseText == 'Erro:2' )
					{
						msg = "Não há mais vagas para esta data !";
						alert( msg );
						aux = document.location.href;
						document.location.href = aux;
					}
					else if( ajax.responseText == 'Erro:3' )
					{
						msg = "Data inválida !";
						alert( msg );
						aux = document.location.href;
						document.location.href = aux;
					}
					else if( ajax.responseText == 'Erro:4' )
					{
						msg = "A data escolhida não respeita o intervalo para agendamento !";
						alert( msg );
						aux = document.location.href;
						document.location.href = aux;
					}
					//else
						document.getElementById('usr').innerHTML = ajax.responseText;
						
                } else {
                    alert('Erro:' + ajax.statusText);
                    document.location.href = endereco;
                }           
            }
        }    
   ajax.send(null);
    
    }
 }
</script>

<?php

#  BLOQUEIO DE AGENDAMENTOS POR DIA AGENDAMENTOS ???
#   $sql = pg_query("select *from grade_exame where med_codigo = '$uni_codigo' and 
#


//var_dump($_GET); //die();
#echo "usu_codigo - $usu_codigo";
#echo "r_esp_codigo - $esp_codigo";
#echo "r_med_codigo - $med_codigo";
#echo "agt_codigo = $agt_codigo";
$med_codigo = $uni_codigo;

if( ! $usu_codigo || ! $esp_codigo || ! $med_codigo || ! $agt_codigo )
{
	print "<h3>Escolha um paciente  e o M&eacute;dico respons&aacute;vel.</h3>";
	die();
}

//------------------------------------------------------------------>
//form procedimento
//------------------------------------------------------------------>
echo "
	<tr>
		<td align='right'>Procedimento</td>
		<td>
		<select name='med_codigo' class='box' onChange=\"javascript:changeLocation(this)\" style='width:400px'>
			<option>-- Escolha um Procedimento--</option>";

        $stmt = "select distinct(grm.proc_codigo),TRANSLATE(proc_nome, 'ZZZ-', '') as newprocnome,grm.proc_codigo from grade_exame as grm left join procedimento as proc on grm.proc_codigo = proc.proc_codigo where grm.med_codigo=$uni_codigo and grm.proc_codigo!=0 order by TRANSLATE(proc_nome, 'ZZZ-', '')";
	$sql = db_query($stmt);
	
	while($proc=pg_fetch_array($sql))
	{
		$location = "$_SERVER[PHP_SELF]?id_login=$id_login&med_codigo=$med_codigo&proc_codigo=$proc[proc_codigo]".
			"&usu_codigo=$usu_codigo&agex_codigo=$agex_codigo&data_cad=$data_cad".
			"&esp_codigo=$esp_codigo&med_codigo=$med_codigo";

        $location .= "&uni_codigo={$uni_codigo}&agt_codigo={$agt_codigo}";		
		
	echo "\n<option value='$location'>$proc[newprocnome]</option>\n";
	}

	echo "
		</select>
		</td>
	</tr>
</table>";

//------------------------------------------------------------------>
/*
*/ 
//------------------------------------------------------------------>
if( $med_codigo && $proc_codigo && $agt_codigo )
{
	$proc_codigo    = intval($proc_codigo);
	$med_codigo     = intval($med_codigo);
	$agt_codigo     = intval($agt_codigo);
	
	//------------------------------------------------------------------>
	// inserindo exame (ie) e recarregando
	//------------------------------------------------------------------>
	if( empty($agex_codigo) )
	{
		// se houver um agendamento não finalizado para hj, RECUPERAR !
		$stmt = sprintf("SELECT agex_codigo FROM %s.agendamento_exame 
			WHERE usu_codigo = %d AND agex_data_cad = CURRENT_DATE AND agex_status = 'A' AND agt_codigo = %d",
			ESQ_SAUDE, $usu_codigo, $agt_codigo);
		
		$agex_codigo = db_get( $stmt );
		
		if( empty($agex_codigo) )
		{
			db_query('begin');
			
			$stmt = "INSERT INTO agendamento_exame 
				(usu_codigo, agex_data_cad, med_codigo_responsavel, esp_codigo_responsavel, agt_codigo) 
				VALUES ('$usu_codigo', CURRENT_DATE, '$med_codigo','$esp_codigo', '$agt_codigo') ";
			db_query($stmt);
			
			$agex_codigo = db_get('SELECT MAX(agex_codigo) FROM agendamento_exame');
			
			db_query('commit');
 		}
 		
 		$location = "$_SERVER[PHP_SELF]?id_login=$id_login&med_codigo=$med_codigo&proc_codigo=$proc_codigo".
			"&usu_codigo=$usu_codigo&agex_codigo=$agex_codigo&data_cad=$data_cad".
			"&esp_codigo=$esp_codigo&med_codigo=$med_codigo";

        $location .= "&uni_codigo={$uni_codigo}&agt_codigo={$agt_codigo}";
 		
 		echo "
 		<script language=\"JavaScript\">
			setTimeout(\"location='$location'\", 0);
		</script>";
	}

	// hj faz parte de qual período ?
	$stmt ='SELECT gex_periodo
		FROM grade_exame_mensal 
		WHERE gex_periodo <= CURRENT_DATE AND med_codigo = '.$med_codigo.' 
		ORDER BY gex_periodo DESC
		LIMIT 1';
	
    $row            = db_getRow($stmt);
	$gex_periodo 	= $row[0];
	
	//------------------------------------------------------------------>
	// tabela/form da data
	//------------------------------------------------------------------>
   //var_dump($_POST);
    
	$data = $_POST['data'];
	if( ! empty($data) )
	{
	        $stmt_lab = "SELECT med_tipoagendamento as proc_tipo_manut FROM medico 
	             WHERE med_codigo= $med_codigo";
	        $manut_row = db_getRow($stmt_lab);
		$proc_tipo = $manut_row[0];
		$gex_tipo = $proc_tipo;
		$erro = valida_agenda( $usu_codigo, $data, $proc_codigo, $med_codigo, $gex_tipo );
        //$erro = valida_agenda_agt( $usu_codigo, $data, $proc_codigo, $med_codigo, $proc_tipo );
		if( $erro == 1 )
		{
			print '
			<script type="text/javascript">
				alert("O Procedimento já foi cadastrado para este paciente nesta data !");
			</script>
			';
		}
		else if( $erro == 2 )
		{
			print '
			<script type="text/javascript">
				alert("Não há mais vagas para esta data !");
			</script>
			';
		}
		else if( $erro == 3 )
		{
			print '
			<script type="text/javascript">
				alert("A Data escolhida não pode ser reservada !");
			</script>
			';
		}
		else if( $erro == 4 )
		{
			print '
			<script type="text/javascript">
				alert("A Data escolhida não pode ser reservada !");
			</script>
			';
		}
		else if( $erro == 0 )
		{

			// caso o a agendamento viole o intervalo minimo do agendamento (quando houver)
			$stmt_teste_int = "
				SELECT COUNT(agexl_codigo), intervalo 
				FROM agendamento_exame_lista,
					( SELECT COALESCE(proc_intervalo_min,0) AS intervalo FROM procedimento 
					  WHERE proc_codigo = {$proc_codigo} ) AS teste
				WHERE agexl_data between '{$data}'::date - intervalo AND '{$data}'::date + intervalo AND
				proc_codigo = {$proc_codigo} AND usu_codigo = {$usu_codigo}
				GROUP BY intervalo
			";

			$row_teste = db_getRow( $stmt_teste_int );
			if( $row_teste[1] > 0 && $row_teste[0] > 0 )
			{
				print '
				<script type="text/javascript">
					alert("O paciente ja possui um agendamento num intervalo minimo de '.$row_teste[1].' dias!");
				</script>';
			}

			$stmt = "INSERT INTO agendamento_exame_lista
			(agex_codigo, usu_codigo, med_codigo, proc_codigo, agexl_data, agexl_status,
				usr_codigo_cad, agexl_dt_cadastro ) 
			VALUES
			($agex_codigo, $usu_codigo, $med_codigo, $proc_codigo, '$data', 'A', $id_login, CURRENT_DATE)";
				
			db_query($stmt);

			// 'salva' a última data cadastrada !
			// recarrega com a data_cad no parametro _GET
			if( empty($data_cad) )
			{
				print '
				<script type="text/javascript">
					aux = document.location.href + "&data_cad='.$data.'";
					document.location.href = aux ;
				</script>
				';
			}
			$data_cad = ( empty($data_cad) ? $data : $data_cad );
		}
	}
	
	// verificar dias disponíveis
	// deixar fazer agendamento retrotativo de ateh ... dias
	//estaa em 30 eu alterei para 0 - marco 06/04
    $retro = 0;
   //Select que vai identificar as datas possiveis de agendamento, levando em conta o tipo de agendamaento 
   //do Laboratorio - que esta na variavel $proc_tipo

     $stmt_lab = "SELECT med_tipoagendamento as proc_tipo_manut FROM medico 
                  WHERE med_codigo= $med_codigo";

     $manut_row = db_getRow($stmt_lab);
     $proc_tipo = $manut_row[0];
    if ($uni_codigo == '2165')
    {
        $sel = "select to_char(graex_data, 'DD/MM/YYYY') as dia,
            graex_qtde as qtde, 
	    coalesce(graex_qtde, 0) - coalesce((select count(*) from agendamento_exame_lista 
		                                where agexl_data = a.graex_data
					        and   med_codigo = a.med_codigo
					        and   proc_codigo = a.proc_codigo), 0) as disponivel
	    from grade_exame as a
            where med_codigo = $uni_codigo 
	    and   proc_codigo = $proc_codigo 
	    and   graex_data >= CURRENT_DATE 
	    and   graex_qtde - (coalesce((select count(*) from agendamento_exame_lista
	                                    where agexl_data = a.graex_data
                                            and   med_codigo = a.med_codigo
                                            and   proc_codigo = a.proc_codigo), 0)) > 0
	    order by graex_data";
	 $tit = "Vaga";
    }	
    if ($uni_codigo != '2165')
    {
        $sel = "select DISTINCT(proc_codigo),to_char(graex_data, 'DD/MM/YYYY') as dia,
            graex_qtde as qtde, 
	    coalesce(graex_qtde, 0) - coalesce((select count(*) from agendamento_exame_lista 
		                                where agexl_data = a.graex_data
					        and   med_codigo = a.med_codigo
					        and   proc_codigo = a.proc_codigo), 0) as disponivel
	    from grade_exame as a
            where med_codigo = $uni_codigo 
	    and   proc_codigo = $proc_codigo 
	    and   graex_data >= CURRENT_DATE 
	    and   graex_qtde - (coalesce((select count(*) from agendamento_exame_lista
	                                    where agexl_data = a.graex_data
                                            and   med_codigo = a.med_codigo
                                            and   proc_codigo = a.proc_codigo), 0)) > 0
	    order by to_char(graex_data, 'DD/MM/YYYY')";

	$tit = "Paciente";   
      }	
/*    if (($proc_tipo == 'V') || ($proc_tipo == 'D'))
    {
        $sel = "select to_char(graex_data, 'DD/MM/YYYY') as dia,
            graex_qtde as qtde, 
	    coalesce(graex_qtde, 0) - coalesce((select count(distinct usu_codigo) from agendamento_exame_lista 
		                                where agexl_data = a.graex_data
					        and   med_codigo = a.med_codigo), 0) as disponivel
	    from grade_exame as a
            where med_codigo = $uni_codigo 
	    and   graex_data >= CURRENT_DATE 
	    order by graex_data";
	$tit = "";   
      }*/	

//echo $sel;


	$qry = db_query($sel);

	echo "
		<form action='$_SERVER[PHP_SELF]?$_SERVER[QUERY_STRING]' id='form_data' method='post'>
			<input type='hidden' value='form_data' value='OK' />  &nbsp; 
			<input type='hidden' name='med_codigo' value=$med_codigo />  &nbsp; 
	<tr><td>
	<table width=100% cellspacing=0 cellpadding=0 border=0>
	<tr>
			
		<td width='130' align=right valign='absmiddle'><strong><font color=red>Dias dispon&iacute;veis: </font>&nbsp;<strong></td>
		<td width='210'>
			<select name='data' onchange=\"envia_form(this,'form_data')\" class='box'><option> Selecione uma Data</option>";
	
	if( $DB_NUM_ROWS <= 0 )
	   print '<option value="01/01/0001">[ NENHUMA DATA DISPON&Iacute;VEL ]</option>';
	while($row = pg_fetch_array($qry))
	{
	//	$selected = $row['graex_data'] == $data_cad ? 'selected' : '';
	//	$selected = $row['dia'] == $data_cad ? 'selected' : '';
		if ($uni_codigo == '2165')  $S = $row['disponivel'] > 1 ? 's' : '';
		if ($uni_codigo == '2165') $vagas = /*$selected ? '' : */"($row[disponivel] $tit$S)";
	        if ($uni_codigo != '2165')   
		{
		   $diadisponivel = $row['dia'];
        	   $verificaexames = "select gex_codigo, gex_periodo, med_codigo, vlr_mensal as gem_valor,gex_periodo+29 as fimperiodo from grade_exame_mensal where med_codigo = '$uni_codigo' and '$diadisponivel' between gex_periodo and gex_periodo+29";
        	   $verexames = db_getRow($verificaexames);
        	   $diaperiodoini = $verexames[1];
        	   $diaperiodofim = $verexames[4];
        	   $totalexames = "select coalesce(sum(preco_procedimento(proc_codigo)),0) as valor 
        	                          from agendamento_exame_lista
        	                          where med_codigo = $med_codigo
        				  and   agexl_data  >= '$diaperiodoini'
        				  and   agexl_data <= '$diaperiodofim'"; 


        	   $totexames = db_getRow($totalexames);
        	   $diferenca = $verexames[3] - $totexames[0];
       	           $vagas = "Vlr Periodo R$ $verexames[3] Gasto R$ $totexames[0] Saldo R$ $diferenca ";
        	}   

		print "\n<option value='$row[dia]' $selected>$row[dia] $vagas</option>";
	}

	
	print "
		</select>
		</td> 
		<td> &nbsp; </td>";
	print "
		<td><!--<input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/enviar_on.jpg>--></td>
		</form>
	</tr>
	</table>
	</td></tr>
	";

	//------------------------------------------------------------------>
	// exames já agendados para esse 'agex_codigo' !
	//------------------------------------------------------------------>
	// 
	
	// verificar a quantia total dos exames agendados
	$stmt = "SELECT SUM(p.proc_valor) FROM agendamento_exame_lista AS a
		INNER JOIN procedimento AS p ON p.proc_codigo = a.proc_codigo
		WHERE a.agex_codigo = $agex_codigo AND agexl_status = 'A'
		and   a.med_codigo = $med_codigo";//AND p.gex_tipo = 'V'";
	
	$quantia = number_format( db_get($stmt),2 );
	
	echo "
	<fieldset>
	<legend>Exames j&aacute; Agendados</legend>
	
	<div style='font-weight:bold;' id='upd'>
		Atualiza&ccedil;&atilde;o:<label style='font-weight:bold;color:#10d' id='usr'></label>
	</div>";
if($uni_codigo!=2165) {
	echo "<p>Quantia total (R\$) <strong>$quantia</strong></p>";
}
//	 	Arrumar Datas</a></p>

        echo "
	<p><a href=\"javascript:popup('agendar_exame_arruma.php?id_login=$id_login&agex_codigo=$agex_codigo&med_codigo=$med_codigo')\">
		<img  src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/unificar_datas.jpg> </a></p>";
	echo "	
	<table class='lista'>
	<tr bgcolor='#ffffff'>
		<th>Procedimento</th>
		<th>Laborat&oacute;rio</th>
		<th width='80'>Agendado em</th>
		<th width='80'>Data</th>
		<th>&nbsp;</th>
	</tr>\n";


	     $stmt = "SELECT agexl_codigo, TO_CHAR(agexl_dt_cadastro,'dd/mm/yyyy') as data_cad,
				TO_CHAR(agexl_data,'dd/mm/YYYY') as data, proc_nome, p.proc_codigo, l.med_codigo,
				agexl_data, p.proc_codigo, usu_codigo, m.med_nome
			FROM agendamento_exame_lista AS l
			LEFT JOIN procedimento AS p ON p.proc_codigo = l.proc_codigo
			LEFT JOIN medico AS m ON l.med_codigo = m.med_codigo
			WHERE l.usu_codigo = $usu_codigo
			AND   m.med_codigo = $med_codigo
            AND agexl_status = 'A'
            GROUP BY agexl_codigo, data_cad, data, proc_nome, p.proc_codigo, l.med_codigo, agexl_data, p.proc_codigo, usu_codigo, med_nome 
			ORDER BY to_char(agexl_data,'YYYY/mm/dd')";

	$qry = db_query($stmt);

	
	$exames = 0;
	while( $row = pg_fetch_array($qry))
	  {
		$exames++;

		print "<tr>
			<td>$row[proc_nome]</td>
			<td>$row[m1] $row[m2] $row[med_nome]</td>
			<td align='center'>$row[data_cad]</td>
			<td><select id='txt_data_$row[agexl_codigo]' name='agexl_data' class='box'
					onchange=\"altera_gexlista($id_login,$row[agexl_codigo],this.value, 'upd')\">";
			
	     $medico = $row['med_codigo'];
             $laboratorio = "select med_tipoagendamento
	                from   medico
	                where med_codigo = '$row[med_codigo]'";
             $laborat1 =  db_query($laboratorio);
             $laborat = pg_fetch_array($laborat1); 
	     if ( $uni_codigo == '2165' )
	     {
		// verificar dias disponíveis (para alteração)
		  if ($uni_codigo == '2165')
		       $stmt = "SELECT graex_data, TO_CHAR(graex_data,'DD/MM/YYYY') as dataf, 
				coalesce(graex_qtde, 0) - coalesce((select count(*) from agendamento_exame_lista 
		                                where agexl_data = ge.graex_data
					        and   med_codigo = ge.med_codigo
					        and   proc_codigo = ge.proc_codigo), 0) as vagas
	                FROM grade_exame AS ge
			LEFT JOIN procedimento AS p ON p.proc_codigo = ge.proc_codigo
		        WHERE graex_data >= CURRENT_DATE - $retro ".
                       "AND p.proc_codigo = '$row[proc_codigo]' 
			AND ge.med_codigo = '$row[med_codigo]' ".
		       "ORDER BY to_char(graex_data,'YYYY/mm/dd')";
		  if ($uni_codigo != '2165')
		       $stmt = "SELECT graex_data, TO_CHAR(graex_data,'DD/MM/YYYY') as dataf 
                     ,  coalesce(graex_qtde, 0) - coalesce((select count(distinct usu_codigo) from agendamento_exame_lista 
		                                where agexl_data = ge.graex_data
					        and   med_codigo = ge.med_codigo
					        ), 0) as vagas
	                FROM grade_exame AS ge
		        WHERE graex_data >= CURRENT_DATE - $retro  
                        AND ge.med_codigo = '$row[med_codigo]' ".
		       "ORDER BY to_char(graex_data,'YYYY/mm/dd')";

		 $qry1 = db_query($stmt);
                 print "\n\n";
		 while( $row1 = pg_fetch_array($qry1) )
		 {
			$selected = $row1['graex_data'] == $row['agexl_data'] ? 'selected' : '';
			$S = $row1['vagas'] > 1 ? 's' : '';
			$vagas = /*$selected ? '' : */"($row1[vagas] vaga$S)";
		        #if (($uni_codigo == '2165') ) $vagas = /*$selected ? '' : */"($row1[vagas] vaga$S)";
		        if (($uni_codigo == '2165') ) $vagas = /*$selected ? '' : */"($row1[vagas] paciente$S)";
			print "\n<option value='$row1[graex_data]' $selected>$row1[dataf] $vagas</option>";
		 }
               print "\n\n";
		print "</select>
			</td>
			<td align='center'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagarlinha.jpg' border='0' 
				onclick=\"if(confirm('Apagar o Agendamento para o Procedimento $row[proc_nome] ?')){ 
					altera_gexlista($id_login,$row[agexl_codigo],this.value, 'del');
					;}\"
				style='cursor:pointer;'></td>
		</tr>";
	     } //fim do if $laborat[0] = 'P' or 'Q'	

	     if ( $uni_codigo!= "2165" )  {
	        $medico = $row['med_codigo'];
		$stmt = "SELECT graex_data, TO_CHAR(graex_data,'DD/MM/YYYY') as dataf 
	                FROM grade_exame AS ge
		        WHERE graex_data >= CURRENT_DATE - $retro ".
		       "AND ge.med_codigo = '$row[med_codigo]' ".
		       "ORDER BY to_char(graex_data,'YYYY/mm/dd')";
		$qry1 = db_query($stmt);
		   $diferenca = 0;
		   $diadisponivel = $row['agexl_data'];


        	   $verificaexames = "select gex_codigo, gex_periodo, med_codigo, vlr_mensal as gem_valor,gex_periodo+29 as fimperiodo from grade_exame_mensal where med_codigo = '$uni_codigo' and '$diadisponivel' between gex_periodo and gex_periodo+29";
        	   $verexames = db_getRow($verificaexames);
        	   $diaperiodoini = $verexames[1];
        	   $diaperiodofim = $verexames[4];
        	   $totalexames = "select coalesce(sum(preco_procedimento(proc_codigo)),0) as valor 
        	                          from agendamento_exame_lista
        	                          where med_codigo = $med_codigo
        				  and   agexl_data  >= '$diaperiodoini'
        				  and   agexl_data <= '$diaperiodofim'"; 


        	   $totexames = db_getRow($totalexames);
        	   $diferenca = $verexames[3] - $totexames[0];

		   if ($uni_codigo != '2165') {
        	      $vagas = "Saldo R$ $diferenca ";
		   }   
		   else {
		      $vagas = " ";
		   }   

        print "\n\n";
		while( $row1 = pg_fetch_array($qry1) )
		{
			$selected = $row1['graex_data'] == $row['agexl_data'] ? 'selected' : '';
//		        if (($laborat[0] == 'V') ) $vagas = /*$selected ? '' : */"($row1[vagas] $vagas)";
//		        if (($laborat[0] == 'D') ) $vagas = /*$selected ? '' : */"($row1[vagas])";
			print "\n<option value='$row1[graex_data]' $selected>$row1[dataf] $vagas</option>";
$data = $row1[dataf];
		}
               print "\n\n";
		print "</select>
			</td>
			<td align='center'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagarlinha.jpg' border='0' 
				onclick=\"if(confirm('Apagar o Agendamento para o Procedimento $row[proc_nome] ?')){ 
					altera_gexlista($id_login,$row[agexl_codigo],this.value, 'del');
					;}\"
				style='cursor:pointer;'></td>
		</tr>";
	     } //fim do if $laborat[0] = 'V' or 'D'	
	 }    

	print "
	</tr>
	</table>
	</fieldset>
	";
	
	if( $exames )
	{
		print "<p><a href='#' onclick=\"window.open('agendar_exame_print.php?acao=form_imprime&imprimir=a&agex_codigo=$agex_codigo&usu_codigo=$usu_codigo&lab=$med_codigo','nv','width=750,height=400')\">
			<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/print.gif' alt='Imprimir' border='0' />
		</a></p>";
	}

}

print "
</body>
</html>";
