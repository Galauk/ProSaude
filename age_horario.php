<?php 
/*error_reporting(E_ALL & ~E_NOTICE ); // & ~E_NOTICE 
ini_set("display_errors",1);
ini_set("ignore_repeated_errors",0);
*/
include("global.php");

   		$form = new classForm();
		$common = new commonClass();
		$table = new tableClass();
		echo $common->incJquery('../');
		
		function arrtime($min) {
			$ex = explode(":",$min);
			$last = substr($min,4,1);
			$min = substr($min,3,1);
			if($last>=5) {
				$minuto = $min +1;
				$mm = 0;
			}
			if($last<=5) {
				$minuto = $min;
				$mm = 0;
			}
			if($minuto==6) { $minuto = 5; }
			$hora = $ex[0].":".$minuto.$mm;
		return $hora;
		}
		
		
 	
function difDeHoras($hIni, $hFinal)
{        
    // Separa á hora dos minutos
    $hIni = explode(':', $hIni);
    $hFinal = explode(':', $hFinal);
    
    // Converte a hora e minuto para segundos
    $hIni = (60 * 60 * $hIni[0]) + (60 * $hIni[1]);
    $hFinal = (60 * 60 * $hFinal[0]) + (60 * $hFinal[1]);
    
    // Verifica se a hora final é maior que a inicial
    if(!($hIni < $hFinal)) {
        return false;
    }
    
    // Calcula diferença de horas
    $difDeHora = $hFinal - $hIni;
    
    //Converte os segundos para Hora e Minuto
    $tempo = $difDeHora / (60 * 60);
    $tempo = explode('.', $tempo); // Aqui divide o restante da hora, pois se não for inteiro, retornará um decimal, o minuto, será o valor depois do ponto.
    $hora = $tempo[0];
    @$minutos = (float) (0) . '.' . $tempo[1]; // Aqui forçamos a conversão para float, para não ter erro.
    $minutos = $minutos * 60; // Aqui multiplicamos o valor que sobra que é menor que 1, por 60, assim ele retornará o minuto corretamente, entre 0 á 59 minutos.
    $minutos = explode('.', $minutos); // Aqui damos explode para retornar somente o valor inteiro do minuto. O que sobra será os segundos
    $minutos = $minutos[0];
	//Aqui faz uma verificação, para retornar corretamente as horas, mas se não quiser, só mandar retornar a variavel hora e minutos
    if (!(isset($tempo[1]))) {
        if($hora == 1){
                return $hora;
        } else {
            return $hora;
        }
    } else {
        if($hora == 1){
            if($minutos == 1){
                return $hora.":".$minutos;
            } else {
                return $hora.":".$minutos;
            }
        } else {
            if($minutos == 1){
                return $hora.":".$minutos;
            } else {
                return $hora.":".$minutos;
            }
        }
    }
}
    

?>
		<script type="text/javascript" src="/WebSocialComum/library/js/jquery-1.6.2.min.js"></script>
		<script src='/WebSocialComum/library/js/jquery.maskedinput-1.3.min.js'></script>
		<script type='text/javascript' src='/WebSocialComum/library/js/tiny_mce/tiny_mce.js'></script>
		<script type="text/javascript" src="/WebSocialSaude/lib/ui/jquery-ui-1.8.16.custom.min.js"></script>
		<script type="text/javascript" src="/WebSocialComum/library/js/jquery.buscar.js"></script>
		<script type="text/javascript" src="/WebSocialSaude/zf/public/js/jquery.validate.min.js"></script>
		<link rel="stylesheet" href="/WebSocialSaude/lib/themes/base/jquery.ui.all.css">
		<link rel="stylesheet" href="/WebSocialSaude/lib/themes/ui-lightness/jquery-ui-1.8.10.custom.css">
		<link rel="stylesheet" href="/WebSocialSaude/lib/demos.css">
		<script src="ajax_motor.js"></script>
		<script>
			$(function(){
				$.datepicker.regional['pt-BR'] = {
			        closeText: 'Fechar',
			        prevText: '&#x3c;Anterior',
			        nextText: 'Pr&oacute;ximo&#x3e;',
			        currentText: 'Hoje',
			        monthNames: ['Janeiro','Fevereiro','Mar&ccedil;o','Abril','Maio','Junho',
			        'Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
			        monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun',
			        'Jul','Ago','Set','Out','Nov','Dez'],
			        dayNames: ['Domingo','Segunda-feira','Ter&ccedil;a-feira','Quarta-feira','Quinta-feira','Sexta-feira','Sabado'],
			        dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sab'],
			        dayNamesMin: ['Dom','Seg','Ter','Qua','Qui','Sex','Sab'],
			        weekHeader: 'Sm',
			        dateFormat: 'dd/mm/yy',
			        firstDay: 0,
			        isRTL: false,
			        showMonthAfterYear: false,
			        yearSuffix: ''
			};
								
			$.datepicker.setDefaults($.datepicker.regional['pt-BR']);
						$("input.data").datepicker();
						$("input.data-mes-ano").datepicker( {
						        changeMonth: true,
						        changeYear: true,
						        showButtonPanel: true,
						        dateFormat: 'mm/yy',
						onClose: function(dateText, inst) { 
						    var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
						    var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
						    $(this).datepicker('setDate', new Date(year, month, 1));
					}
				});
});
</script>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
</HEAD>

 <body bgcolor="#E8F4F">
 <?php 

echo $common->menuTab(array('Manutencao por Horario de Consulta'));

echo $common->bodyTab('1');


 if(empty($acao)) {
		  $sql = pg_query("select esp_nome,usr_nome,med_codigo, esp.esp_codigo,to_char(grm_periodo,'dd/mm/yyyy') as grm_periodo from grade_mensal as grm join usuarios as usu on usu.usr_codigo = grm.med_codigo join especialidade as esp on esp.esp_codigo = grm.esp_codigo group by esp_nome,med_codigo, esp.esp_codigo,grm_periodo,usr_codigo order by grm_periodo limit 15");
				echo $common->commonButton("Adicionar","age_horario.php?acao=form_add","adicionar.png");
				
 				echo "<form><table class=lista>
					<tr>
						<th>Medico</th>
						<th>Especialidade</th>
						<th>Periodo</th>
						<th colspan=3>Opções</th>
					</tr>";
			$num = pg_num_rows($sql);
			if($num >0){
				while($rr=pg_fetch_array($sql)) {
				echo"
					<tr>
						<td>$rr[usr_nome]</td>
						<td>$rr[esp_nome]</td>
						<td>$rr[grm_periodo]</td>
						<td width=30>"; echo $common->commonButton("Apagar","age_horario.php?acao=del&med_codigo=$rr[med_codigo]&esp_codigo=$rr[esp_codigo]&grm_periodo=$rr[grm_periodo]","apagar.png",null); echo"</td>
					</tr>";
			}}
			else{
				echo"
					<tr>
						<td colspan='3'>Nenhum registro encontrado</td>						
					</tr>";
			}
			echo "</table>";
 }
 	
if($acao=="form_add") {	 	
	
		echo $form->openForm($PHP_SELF,'post','hora',"OnSubmit='return verifica();'");		
		echo $form->hiddenForm('acao', 'add');
		  echo "<table width='100%' align='center' cellspacing='3' cellpadding='0' border='0'>
		            <tr>
		                <td width='180'>";
		
		  	$sqlHora = "SELECT to_char(teste,'HH24:MI') as cod,to_char(teste,'HH24:MI') as hora FROM generate_series('2008-03-01 00:00'::timestamp,
		                              '2008-03-01 23:00', '1 hours') as teste;";
		    echo $form->inputSelect("hr_ini", '', "Hora Inicial", $sqlHora, 10, null, "text", "N", "Horario Inicial");
		
		   	$sqlHora2 = "SELECT to_char(teste,'HH24:MI') as cod,to_char(teste,'HH24:MI') as hora FROM generate_series('2008-03-01 00:00'::timestamp,
		                              '2008-03-01 23:00', '1 hours') as teste;";
       echo $form->inputSelect("hr_fim", '', "Hora Final", $sqlHora2, 10, null, "text", "N", "Horario Final");
		    
			$sqlMedico = "SELECT DISTINCT(u.usr_codigo),u.usr_nome
							FROM usuarios AS u
							JOIN medico_especialidade AS me
						  	  ON me.med_codigo=u.usr_codigo
						   WHERE u.usr_tipo_medico IN ('M','E','D','A','P')
						   ORDER BY u.usr_nome;";
			echo $form->inputSelect("med_codigo", '', "Medico", $sqlMedico, 10, null, "text", "N", "SELECIONE UM MEDICO");
			$sqlUnidade = "SELECT uni_codigo,uni_desc FROM unidade";
			echo $form->inputSelect("uni_codigo", '', "Unidade", $sqlUnidade, 10, null, "text", "N", "SELECIONE UMA UNIDADE");
			echo $form->inputText('comp_data',$dt,'Data','12','10',null,null,null,null,null,null,'inputForm data');
					echo "</td>
		            </tr>
		            <tr>
		            <td>";
				echo "
					<div id='btnsave' style='clear:both; width:400px; border:solid 0px;'>";
						echo"<div style='float:right; width:205px;'>";		
							echo $common->commonButton("Gerar Horarios", null, "salvar.gif", "onclick=\"document.hora.submit();\"");
						echo"</div>";
						echo $common->commonButton("voltar","age_horario.php","voltar.png");
		        echo "</td>
		            </tr>
		        </table><br><br><br>";		        
		echo $form->closeForm();	
		
}

 if($acao=="add") {
 	if($hr_ini==0) {
				echo $common->modalMsg("ERRO","Selecione uma HORA INICIAL","age_horario.php?acao=form_add",$sql);
 	exit;
 	}
 	if($hr_fim==0) {
				echo $common->modalMsg("ERRO","Selecione uma HORA FINAL","age_horario.php?acao=form_add",$sql);
 	exit;
 	}
 	if($med_codigo==0) {
				echo $common->modalMsg("ERRO","Selecione um MEDICO","age_horario.php?acao=form_add",$sql);
 	exit;
 	}
 	if($uni_codigo==0) {
				echo $common->modalMsg("ERRO","Selecione uma UNIDADE","age_horario.php?acao=form_add",$sql);
 	exit;
 	}
 	if(empty($comp_data)) {
				echo $common->modalMsg("ERRO","Campo DATA Nao pode ser Vazio","age_horario.php?acao=form_add",$sql);
 	exit;
 	}
 	$dif = difDeHoras($hr_ini, $hr_fim);
  	$hrdig = "15";
 	$ex = explode(".",$hrdig);
 	if($ex[0]=="") { $hr = $hrdig; } else { $hr = $ex[0]; }
 	
 	$usr = pg_fetch_array(pg_query("select *from usuarios as usr join medico_especialidade as me on me.med_codigo = usr_codigo where usr_codigo = $med_codigo"));
	$esp_codigo = $usr[esp_codigo];
 	
 	$agt = pg_query("select *from agente");
 	
 	while($rag = pg_fetch_array($agt)) {
 		$ins = "INSERT INTO grade_mensal (med_codigo,grm_qtde,esp_codigo,agt_codigo,grm_periodo,age_item)
 				VALUES('$med_codigo','10000','$esp_codigo','$rag[agt_codigo]','$comp_data','CB')";

 		$qq = pg_query($ins);
 	}
 	

 	$sqlLast = pg_fetch_array(pg_query("select ((to_char(('$comp_data'::date + interval '1 month'),'yyyymm')||'01')::date - interval '1 day')::date as data"));
	$ex = explode("-",$sqlLast[data]);
 	if(($ex[2]=="31")) { $fimMes = "30"; } else { $fimMes = "29"; }
 	
 	$sqlDia = pg_query("select a.data, EXTRACT(dow from a.data) from ( select (generate_series(0,$fimMes) + date '$comp_data') as data) a ");

 	while($dia=pg_fetch_array($sqlDia)) {
   		$qq = pg_query("SELECT to_char(teste,'HH24:MI') as hora FROM generate_series('2008-03-01 $hr_ini'::timestamp,
                              '2008-03-01 $hr_fim', '$hr minutes') as teste;");  	
	while($res = pg_fetch_array($qq)) {
	 if($res[hora]!=$hr_fim) {	
	 	if(($dia[date_part]==0 OR $dia[date_part]==6)) { $qtd = 0; } else { $qtd = 1; }
	 	$hora = $res[hora];
	 	$ins = "INSERT INTO grade_medico (med_codigo,gra_data,uni_codigo,gra_tipo,gra_status,gra_qtde,esp_codigo,gra_hora_ini,age_item,age_tipo,gra_bloqueado)
	 					VALUES('$med_codigo','$dia[data]','$uni_codigo','PC','S','$qtd','$esp_codigo','$hora','CB','PC','20010-12-12')";
	 		$sql = pg_query($ins) or die(pg_last_error());
	 }
	}  
   }	
		   if($sql){
				echo $common->modalMsg("OK","Cadastrado com Sucesso","age_horario.php");
			} else {
				echo $common->modalMsg("ERRO","Erro ao cadastrar","age_horario.php",$sql);
			}
}
 			if($acao=="del") {
				echo $common->modalConfirm("Deseja realmente apagar todos os dados desde item?", "age_horario.php?med_codigo=$med_codigo&esp_codigo=$esp_codigo&grm_periodo=$grm_periodo&comp_codigo=$comp_codigo&acao=delete","age_horario.php");
			}
			if($acao=="delete") {
				$ver = pg_query("select *from agendamento where med_codigo = '$med_codigo' and esp_codigo = '$esp_codigo' and age_data between '$grm_periodo' and (age_data + interval '30')");
				if(pg_num_rows($ver)!=0) {
					echo $common->modalMsg("ERRO","Existem Agendamentos neste periodo Impossivel Apagar!","age_horario.php",$ver);					
				exit;		
				}
	   		   $sql = pg_query("delete from grade_mensal where med_codigo = '$med_codigo' and esp_codigo = '$esp_codigo' and grm_periodo = '$grm_periodo'");
			   $qq = pg_query("delete from grade_medico where gra_data >= '$grm_periodo' and gra_data <= ((to_char(('$grm_periodo'::date + interval '1 month'),'yyyymm')||'01')::date - interval '1 day')::date and med_codigo = '$med_codigo' and esp_codigo = '$esp_codigo'");
				
			if($sql){
				echo $common->modalMsg("OK","Excluido com Sucesso","age_horario.php");
			} else {
				echo $common->modalMsg("ERRO","Erro ao excluir","age_horario.php",$del_1);
			}
							
		}			

		echo $common->closeTab();

?>

</BODY>
</HTML>
