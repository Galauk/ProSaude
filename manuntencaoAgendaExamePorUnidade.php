<script src="script.js" language="javascript" type="text/javascript"></script>
<script src="ajax_motor.js" language="javascript" type="text/javascript"></script>
<script type="text/javascript" src="funcoes.js"></script>

<script>

function somar(e,data,proc_codigo,med_codigo)
	{
		validaUnidade(e,data,proc_codigo,med_codigo);
		
		gra_qtde_total = parent.document.getElementById('gra_qtde_total');
		inputs = document.getElementsByTagName('input');
		//alert(inputs);
		//alert(inputs.length);
		soma = new Number(0);
		for(i=0;i<inputs.length;i++)
		{
			if(inputs[i].name == "qtde[]")
			{
				if(isNaN(inputs[i].value))
				{
					c = false;
				} else {
					//alert(inputs[i].value);
					soma = soma + new Number(inputs[i].value);
				}
			}
		}
		gra_qtde_total.value = soma;
	}
	
	function buscar_usuario(graex_codigo)
	{
		if(graex_codigo != undefined)
		{
			url = "buscar_usuario_exame.php?tipo=usuario_manutencao&graex_codigo="+graex_codigo;
			ajax_tudo(url, retorno);
		} else {
			//alert("Esta data nao possui registro!");
		}
	}
	function validaUnidade(quantidade,data,proc_codigo,med_codigo){ 
	 	var endereco='validacaounidade.php?quantidade='+quantidade.value+"&data="+data+"&proc_codigo="+proc_codigo+"&med_codigo="+med_codigo; 
	    
	   
	   ajax = ajaxInit();
	   
	   if(ajax) {
	       ajax.open("GET", endereco , true);

	       ajax.onreadystatechange = function() {
		       if(ajax.readyState == 4) {
		           if(ajax.status == 200) {		        	   
		               resp = ajax.responseText;

		               if(resp == 0){
			               alert("QUANTIDADE INVALIDA");
			               quantidade.value = "";
			               quantidade.focus();
		               }
		              
		              
		               //document.getElementById('usr').innerHTML = ajax.responseText;
		                                 
		           } else {
		               alert(ajax.statusText);
		           }           
		       }
	       }
	     
		ajax.send(null);
		}
	}
	
	function retorno(txt)
	{
		txt = eval(txt);
		//document.getElementById("msg_usuario").style.display = '';
		document.getElementById("msg_usuario_cad"+txt[0].codigo).innerHTML = txt[0].usr_cad;
		document.getElementById("msg_usuario_alt"+txt[0].codigo).innerHTML = txt[0].usr_alt;
	}
	
	function montar(id_login)
	{
		gex_codigo = document.getElementById('id_dia_ini').value;
		med_codigo =  document.getElementById('med_codigo').value;
		proc_codigo =  document.getElementById('proc_codigo').value;
		proc_codigo = proc_codigo.split("-");
	//	alert(proc_codigo);
		id_dia_ini = document.getElementById('id_dia_inicial').value;
		id_dia_ini = id_dia_ini.split("-");	
		id_dia_ini = id_dia_ini[0];

		id_dia_fim = document.getElementById('id_dia_fim').value;
		document.getElementById('frameprincipal').src = "manutencaoagendaexame_iframe.php?med_codigo="+med_codigo+"&proc_codigo="+proc_codigo+"&id_dia_ini="+id_dia_ini+"&id_dia_fim="+id_dia_fim+"&id_login="+id_login+"&gex_codigo="+gex_codigo;
	}
	function montarUnidade(id_login)
	{
		gex_codigo = document.getElementById('id_dia_ini').value;
		med_codigo =  document.getElementById('med_codigo').value;
		proc_codigo =  document.getElementById('proc_codigo').value;
		proc_codigo = proc_codigo.split("-");
	//	alert(proc_codigo);
		id_dia_ini = document.getElementById('id_dia_inicial').value;
		id_dia_ini = id_dia_ini.split("-");	
		id_dia_ini = id_dia_ini[0];

		id_dia_fim = document.getElementById('id_dia_fim').value;
		document.getElementById('frameprincipal').src = "manutencaoagendaexame_iframe.php?med_codigo="+med_codigo+"&proc_codigo="+proc_codigo+"&id_dia_ini="+id_dia_ini+"&id_dia_fim="+id_dia_fim+"&id_login="+id_login+"&gex_codigo="+gex_codigo;
	}
</script>
<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	cabecario();

$gex_codigo = $_GET['gex_codigo'];
$cod_unidade = $_GET['uni_codigo'];
//echo $cod_unidade;
$gex_codigo = $_GET['gex_codigo'];

	$sql = "select TO_CHAR(gex_periodo,'dd/mm/YYYY') as max,	
				   TO_CHAR(gex_periodo+29,'dd/mm/YYYY') as prox_max 
			  from grade_exame_mensal 
			 where gex_codigo = $gex_codigo";
	$exe_sql = pg_query($sql);
	$res_exe= pg_fetch_array($exe_sql);
	
	$dia_fim = $res_exe['prox_max'];
	$dia_ini = $res_exe['max'];
	
if($_POST[acao] == "salvar"){
	
/*echo "<pre>";
			print_r($_POST);
		echo "</pre>";*/
		for($i = 0; $i < count($_POST['qtde']); $i++)
		{
			$totalDeResultadoUnidade = $_POST['unidade'][$i];
			$totalDeResultado = $_POST['laboratorio'][$i];	
			$data = $_POST['data'][$i];	
			$periodo = $_GET['periodo'];
			$qtde = $_POST['qtde'][$i] == '' ? 0 : $_POST['qtde'][$i]; 
			$proc_codigo = $_POST['proc_codigo'];
			$id_login = $_POST['id_login'];
			$graexuni_codigo = $_POST['graexuni_codigo'][$i];			
			$texto = $idade > 18 ? "maior idade" : "menor idade";
			
			/*if($_POST['unidade'][$i] > $_POST['laboratorio'][$i])
			{
				echo"FERRO DE VEZ!!!";
				exit();
			}else{
				echo $_POST['unidade'][$i] .">". $_POST['laboratorio'][$i];
				exit();
			}*/
			
			

			
			//echo $i . "=" . $_POST[qtde][$i]."<br>";
			/*$select = "select gra_codigo from grade_medico where med_codigo = $_POST[med_codigo] and gra_data = '{$_POST[data][$i]}' and uni_codigo = $_POST[uni_codigo] and esp_codigo = $_POST[esp_codigo] and age_item = '$_POST[age_item]' and age_tipo = '$_POST[age_tipo]' and gra_hora_ini = '$_POST[gra_hora_ini]'"; // and gra_tipo = $POST[gra_tipo] -> precisa???
			$exec_select = pg_query($select);
			if(pg_num_rows($exec_select) == 0)*/
			//echo $_POST[action][$i]."<br>";
			if($_POST[action][$i] == "inserir")
			{
				//echo"IF<br>";
				if($_POST[qtde][$i] != "" && $_POST[qtde][$i] > 0 && $_POST[qtde][$i] != "Feriado")
				{
					
					//echo "insert - $i<br>";
					$insert = "insert into grade_exame_unidade
					(med_codigo, graexuni_data, graexuni_qtde, proc_codigo, usr_codigo_cad)
					values
					('$med_codigo', '$data', '$qtde', '$proc_codigo', '$id_login')";
					
					$exec_insert = pg_query($insert);
					
					//echo $insert."<br>";
					//echo"DATA".$periodo;
					//exit;
					//echo pg_last_error($db);
				}
			} else {
				//echo "ELSE"."<br>";
				$sel = "select to_char(graexuni_data, 'DD/MM/YYYY') as dia,
						graexuni_qtde as qtde, 
							   coalesce(graexuni_qtde) - 
							coalesce((select count(*) from agendamento_exame_lista 
										where agexl_data = a.graexuni_data
										and   med_codigo = a.med_codigo
										and   proc_codigo = a.proc_codigo
										and uni_codigo = a.uni_codigo)) as disponivel,
										graexuni_codigo
						 from grade_exame_unidade as a
						 WHERE med_codigo = $med_codigo
						   and proc_codigo = $proc_codigo
						   and uni_codigo = $_GET[uni_codigo] 
						   and graexuni_data = '$data[0]'
						order by graexuni_data";
				//echo $sel;
				$exec_sel = pg_query($sel);
				//echo $exec_sel;
				$disponivel = pg_fetch_array($exec_sel);
				$graex_qtde = $disponivel['graexuni_qtde'];
				$paraDistribuir = $disponivel['disponivel'];
				
				if($_POST[qtde][$i]  >= $_POST[agendado][$i])
				{
				
					$update = "update grade_exame_unidade set graexuni_qtde = '$qtde', usr_codigo_alt = '$id_login' where graexuni_codigo = '$graexuni_codigo'";
					//echo $update."<br>";
					$exec_update = pg_query($update) or die(pg_last_error());
//echo $update."<br>";
				}
			}
			
		}
		echo"med_codigo=$_POST[med_codigo]&proc_codigo=$_POST[proc_codigo]&id_dia_ini=$_POST[id_dia_ini]&id_dia_fim=$_POST[id_dia_fim]&id_login=$_POST[id_login]&uni_codigo=$_POST[uni_codigo]&gex_codigo=$_POST[gex_codigo]";
		echo "<script>parent.document.getElementById('framesecundario').src = \"manuntencaoAgendaExamePorUnidade.php?med_codigo=$_POST[med_codigo]&proc_codigo=$_POST[proc_codigo]&id_dia_ini=$_POST[id_dia_ini]&id_dia_fim=$_POST[id_dia_fim]&id_login=$_POST[id_login]&uni_codigo=$_POST[uni_codigo]&gex_codigo=$_POST[gex_codigo]\"</script>";
		/*echo "<script>parent.document.getElementById('framesecundario').src = \"manuntencaoAgendaExamePorUnidade.php\"</script>";*/
	//include "manuntencaoAgendaExamePorUnidade.php";
		
	
	}


//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
//------------------------------------------------------------------>
//echo "<pre>";
//	print_r($_GET);
//echo "</pre>";
	
	
	$stmt_lab = "SELECT med_tipoagendamento as proc_tipo_manut FROM medico 
	             WHERE med_codigo=$med_codigo";
	$manut_row = db_getRow($stmt_lab);
	
	if ($manut_row['proc_tipo_manut'] == 'P')
	   echo "<center><h4>As informa&ccedil;&otilde;es a seguir s&atilde;o referentes as unidades.</h4></center>";
	if ($manut_row['proc_tipo_manut'] == 'V')
	{
	       $verificaexames = "select gem_codigo, gem_periodo, med_codigo, gem_valor 
	                          from grade_exame_mensal_manut
	                          where med_codigo = $med_codigo
				  and   gem_periodo  = '$dia_ini'";
	       $verexames = db_getRow($verificaexames);
	       $totalexames = "select coalesce(sum(preco_procedimento(proc_codigo)),0) as valor 
	                          from agendamento_exame_lista
	                          where med_codigo = $med_codigo
				  and   agexl_data  >= '$dia_ini'
				  and   agexl_data <= '$dia_fim'";
	       $totexames = db_getRow($totalexames);

	       $diferenca = $verexames[3] - $totexames[0];
	   echo "<center><h4> Os dados deste Laboratorio sao expressos em valores durante o periodo. <br>
	          Valor maximo cadastrado: R$ $verexames[3] =>  Valor ja agendado no periodo: R$ $totexames[0] 
		  ==> <b>Saldo: R$ $diferenca</b><h4></center>";
        }

	
	$select = "select (date '$dia_fim' - date '$dia_ini') as total";
	$select_dias = pg_query($select);
	$total_dias = pg_fetch_array($select_dias);
	$total_dias[0];
	$exp=explode("/",$dia_ini);
	$dia = date("D", $dia_ini);
	$hoje = date("Y-m-d");
	/*Monday - > segunda
	Tuesday - > terÃ§a
	Wednesday - > Quarta
	Thursday - > quinta
	Friday - > sexta
	Saturday - > sabado
	Sunday -> domingo*/
	echo "<form name=gra_medico action=$PHP_SELF method=POST>";
	echo "<input type=hidden name=acao value=salvar>";
	echo "<input type=hidden name=med_codigo value=$_GET[med_codigo]>";
	echo "<input type=hidden name=proc_codigo value=$_GET[proc_codigo]>";
	echo "<input type=hidden name=id_login value=$_GET[id_login]>";
	echo "<input type=hidden name=uni_codigo value=$_GET[uni_codigo]>";
	echo "<input type=hidden name=id_dia_ini value=$dia_ini>";
	echo "<input type=hidden name=id_dia_fim value=$dia_fim>";
	echo "<input type=hidden name=data value=$data[0]>";
	echo "<input type=hidden name=gex_codigo value=$gex_codigo>";

			
	echo "<table border=1 style='width:955px;' align=center cellspacing=0 cellpadding=0>";
		echo "<tr>";
			echo "<th>";
				echo "Segunda - Feira";
			echo "</th>";
			echo "<th>";
				echo "Ter&ccedil;a - Feira";
			echo "</th>";
			echo "<th>";
				echo "Quarta - Feira";
			echo "</th>";
			echo "<th>";
				echo "Quinta - Feira";
			echo "</th>";
			echo "<th>";
				echo "Sexta - Feira";
			echo "</th>";
			echo "<th>";
				echo "S&aacute;bado";
			echo "</th>";
			echo "<th>";
				echo "Domingo";
			echo "</th>";
		echo "</tr>";
		echo "<tr>";
	/*echo "</table>";
	echo "<table border=1>";*/
		$k = 0;
		/*$teste = "select * from feriado";
		$exec_teste = pg_query($teste);
		while($r = pg_fetch_array($exec_teste))
		{
			echo $r[fer_data]."<br>";
		}*/
		$buscar = pg_query("select to_char(current_date, 'w') as dia_semana");
		$dia_semana = pg_fetch_array($buscar);
		for($i = 0; $i <= $total_dias[0]; $i++)
		{
			$busca_feriado = "select to_char(fer_data, 'dd/mm/yyyy') as data from feriado where fer_data = (date '$dia_ini' + $i)";
			$exec_busca = pg_query($busca_feriado);
			//echo pg_last_error($db);
			$linha = pg_fetch_array($exec_busca);
			/*if($linha != "")
			{
				echo $linha[0]."-<br>";
			}*/
			$select = "select to_char(date '$dia_ini' + $i, 'dd/mm/yyyy') as data";
			$exec_select = pg_query($select);
			$data = pg_fetch_array($exec_select);
			$exp = explode("/",$data[0]);
			$dia_semana = date('w', mktime(0,0,0,$exp[1],$exp[0],$exp[2]));
	
	
			
			
			switch($dia_semana)
			{
				case 1:
					$dia_da_semana = "Segunda - Feira";
				break;

				case 2:
					$dia_da_semana = "Terça - Feira";
				break;

				case 3:
					$dia_da_semana = "Quarta - Feira";
				break;

				case 4:
					$dia_da_semana = "Quinta - Feira";
				break;

				case 5:
					$dia_da_semana = "Sexta - Feira";
				break;

				case 6:
					$dia_da_semana = "Sábado";
				break;

				case 0:
					$dia_da_semana = "Domingo";
				break;
			}
			if($i == 0)
			{
				if($dia_semana[0] == 2)
				{
					echo "<td>&nbsp;</td>";
					$k++;
				} else if($dia_semana[0] == 3){
					echo "<td>&nbsp;</td>";
					echo "<td>&nbsp;</td>";
					$k++;
					$k++;
				} else if($dia_semana[0] == 4){
					echo "<td>&nbsp;</td>";
					echo "<td>&nbsp;</td>";
					echo "<td>&nbsp;</td>";
					$k++;
					$k++;
					$k++;
				} else if($dia_semana[0] == 5){
					echo "<td>&nbsp;</td>";
					echo "<td>&nbsp;</td>";
					echo "<td>&nbsp;</td>";
					echo "<td>&nbsp;</td>";
					$k++;
					$k++;
					$k++;
					$k++;
				} else if($dia_semana[0] == 6){
					echo "<td>&nbsp;</td>";
					echo "<td>&nbsp;</td>";
					echo "<td>&nbsp;</td>";
					echo "<td>&nbsp;</td>";
					echo "<td>&nbsp;</td>";
					$k++;
					$k++;
					$k++;
					$k++;
					$k++;
				} else if($dia_semana[0] == 0){
					echo "<td>&nbsp;</td>";
					echo "<td>&nbsp;</td>";
					echo "<td>&nbsp;</td>";
					echo "<td>&nbsp;</td>";
					echo "<td>&nbsp;</td>";
					echo "<td>&nbsp;</td>";
					$k++;
					$k++;
					$k++;
					$k++;
					$k++;
					$k++;
				}
			}
			/*if($dia_semana != 0 && $dia_semana != 6)
			{*/
			
			$stmt_lab = "SELECT med_tipoagendamento as proc_tipo_manut 
						   FROM medico 
						  WHERE med_codigo='$med_codigo'";
			
	                 $manut_row = db_getRow($stmt_lab);

				//$busca = "select * from grade_exame_unidade where med_codigo = $_POST[med_codigo] and graexuni_data = '$data[0]' and proc_codigo = $proc_codigo";
				//echo $busca;
				//echo"AIOIASOIOASIOAS". $data[0];

				/*$busca = "select count(graexuni_qtde) as cont, 
								 graexuni_qtde,*
							from grade_exame_unidade
							where to_char(graexuni_data, 'mm/yyyy') = '".pegaMesAno($data[0], "/")."'
							 and proc_codigo = '$proc_codigo'
							 and uni_codigo = '$cod_unidade'
							group by graexuni_qtde,
							   		graexuni_codigo,
									med_codigo,
									proc_codigo,
									uni_codigo,
									graexuni_data,
									graexuni_qtde_maxdiario,
									graexuni_valor,
									graexuni_hora_ini,
									usr_codigo_cad,
									usr_codigo_alt,
									graexuni_saldo_atual,
									proc_valor,
									gex_codigo,
									graexuni_status,
									graexuni_qtd_maxmes
							order by cont desc";*/
					$busca = "select to_char(graexuni_data, 'DD/MM/YYYY') as dia,
						graexuni_qtde as qtde, 
							   coalesce(graexuni_qtde) - 
							coalesce((select count(*) from agendamento_exame_lista 
										where agexl_data = a.graexuni_data
										and   med_codigo = a.med_codigo
										and   proc_codigo = a.proc_codigo
										and uni_codigo = a.uni_codigo)) as disponivel,
										graexuni_codigo
						 from grade_exame_unidade as a
						 WHERE med_codigo = $med_codigo
						   and proc_codigo = $proc_codigo
						   and uni_codigo = $_GET[uni_codigo] 
						   and graexuni_data = '$data[0]'
						order by graexuni_data";
							//echo"data"."$data[0]<br>"."procediment"."$proc_codigo<br>"."unidade"."$med[uni_codigo]";

				//echo $busca."<br>";
				$exec_busca = pg_query($busca);
				$row = pg_fetch_array($exec_busca);
				$gra_qtde_soma += $row['graexuni_qtde'];
			//	echo $row[graexuni_codigo];
				
				$d = explode("/", $data[0]);
				$d = $d[2]."-".$d[1]."-".$d[0];
				
				echo "<td>";
				echo "<table cellpading=0 cellspacing=0 border=0 width=\"100%\">";
					echo "<tr>";
						echo "<td width=\"32\">";
							if(pg_num_rows($exec_busca) > 0)
							{
								echo "<input type=hidden name=action[] value=alterar>";
							} else {
								echo "<input type=hidden name=action[] value=inserir>";
							}
							if($d >= $hoje)
							{
								echo "<input type=hidden name=graexuni_codigo[] value=$row[graexuni_codigo]>";
								echo "<input type=hidden name=data[] value=$data[0]>";
							} else {
								echo "<input type=hidden name=graexuni_codigo[] value='$row[graexuni_codigo]' readonly style='background:gray'>";
								echo "<input type=hidden name=data[] value='$data[0]' readonly style='background:gray'>";
							}
							if($dia_semana[0] != 0)
							{
								echo "<a href=\"#\" class=\"info\" onmouseover=\"buscar_usuario($row[graexuni_codigo]);\">";
							} else {
								echo "<a href=\"#\" class=\"info_2\" onmouseover=\"buscar_usuario($row[graexuni_codigo]);\">";
							}
								echo "<span style=\"width:200px;\">";
									echo "<table>";
										echo "<tr>";
											echo "<td>";
												echo "<strong>Cadastrado por:</strong>";
											echo "</td>";
											echo "<td id=\"msg_usuario_cad$row[graexuni_codigo]\"></td>";
										echo "</tr>";
										echo "<tr>";
											echo "<td>";
												echo "<strong>Alterado por:</strong>";
											echo "</td>";
											echo "<td id=\"msg_usuario_alt$row[graexuni_codigo]\"></td>";
										echo "</tr>";
									echo "</table>";
								echo "</span>";
								echo $data[0][0].$data[0][1]."-".$data[0][3].$data[0][4];
							echo "</a>";
						echo "</td>";
						echo "<td>";
			/*$sel = "select to_char(graex_data, 'DD/MM/YYYY') as dia,
			               graex_qtde as qtde, 
			               coalesce(graex_qtde, 0) - 
				           coalesce((select count(*) from agendamento_exame_lista 
				                            where agexl_data = a.graex_data
							    and   med_codigo = a.med_codigo
							    and   proc_codigo = a.proc_codigo), 0) as disponivel
			        from grade_exame as a
			        where med_codigo = $_GET[med_codigo]
			        and   proc_codigo = $_GET[proc_codigo] 
			        and   graex_data = '$row[graex_data]' 
			        order by graex_data";*/
					//echo $data[0];
					
			$sel = "select to_char(graexuni_data, 'DD/MM/YYYY') as dia,
						graexuni_qtde as qtde, 
							   coalesce(graexuni_qtde) - 
							coalesce((select count(*) from agendamento_exame_lista 
										where agexl_data = a.graexuni_data
										and   med_codigo = a.med_codigo
										and   proc_codigo = a.proc_codigo
										and uni_codigo = a.uni_codigo)) as disponivel,
										graexuni_codigo
						 from grade_exame_unidade as a
						 WHERE med_codigo = $med_codigo
						   and proc_codigo = $proc_codigo
						   and uni_codigo = $_GET[uni_codigo] 
						   and graexuni_data = '$data[0]'
						order by graexuni_data";
						
			/*$buscaAgendados = "select count(*) as agendados from agendamento_exame_lista
									where med_codigo = 2165
									and   proc_codigo = 20216
									and uni_codigo = 560430
									and agexl_data = '$data[0]'";
			$exe_buscaAgendados = pg_query($buscaAgendados);
			$res_exe_buscaAgendados = pg_fetch_array($exe_buscaAgendados);
			$exames_agendados = $res_exe_buscaAgendados['agendados'];*/
			
		/*	$sel = "select to_char(graex_data, 'DD/MM/YYYY') as dia,
			               graex_qtde as qtde, 
			               coalesce(graex_qtde, 0) - 
				           coalesce((select count(*) from agendamento_exame_lista 
				                            where agexl_data = a.graex_data
							    and   med_codigo = a.med_codigo
							    and   proc_codigo = a.proc_codigo), 0) as disponivel
			        from grade_exame_unidade as a
			        where med_codigo = $_GET[med_codigo]
			        and   proc_codigo = $_GET[proc_codigo] 
			        and   graex_data = '$row[graex_data]' 
			        order by graex_data";
			$sel = "select * from grade_exame_unidade
					 WHERE med_codigo = $_GET[med_codigo] 
							and
							proc_codigo = $_GET[proc_codigo]
							and
							uni_codigo = $_GET[uni_codigo]
							and
						graexuni_data between '$dia_ini' and '$dia_fim'";
*/
						
							$exec_sel = pg_query($sel);
							$disponivel = pg_fetch_array($exec_sel);
							/*echo "dispo".$disponivel['qtde'];
							echo"med".$med_codigo;
							echo"proc".$proc_codigo;
							echo"uni".$_GET[uni_codigo];
							echo"datta".$data[0];
							echo $sel;*/
							
							//VALIDA��O DE CAMPOS
							$vagasPorLaboratorio = "SELECT graex_qtde FROM grade_exame
														WHERE graex_data = '$data[0]'
														 AND
														  proc_codigo = $proc_codigo
														 AND
														  med_codigo = $med_codigo";
							$exeVagasPorLaboratorio = pg_query($vagasPorLaboratorio);
							$resExeVagasPorLaboratorio = pg_fetch_array($exeVagasPorLaboratorio);
							$totalDeResultado = $resExeVagasPorLaboratorio['graex_qtde'];
			
							//SOMA DE QUANTIDADE DISTRIBUIDOS POR UNIDADE.
							$vagasDistribuidasParaUnidade = "SELECT sum(graexuni_qtde) AS soma
																FROM grade_exame_unidade
																WHERE graexuni_data = '$data[0]'
																 AND
																  proc_codigo = $proc_codigo
																 AND
																  med_codigo = $med_codigo";
							$exeVagasDistribuidasParaUnidade = pg_query($vagasDistribuidasParaUnidade);
							
							$resExeVagasDistribuidasParaUnidade = pg_fetch_array($exeVagasDistribuidasParaUnidade);
							$totalDeResultadoUnidade = $resExeVagasDistribuidasParaUnidade['soma'];
		
							//
							echo"<input type='hidden' name='unidade[]' class=box style='background: #EEE8AA' value='$totalDeResultadoUnidade'";							
							echo"<input type='hidden' name='laboratorio[]' class=box style='background: #EEE8AA' value='$totalDeResultado'";
							
							//VAGAS DISPONIVES
							//echo $sel;
							echo "<input type=text size=1 name='disponivel' disabled class=box style='background: #EEE8AA' value='$disponivel[2]' readonly>&nbsp;";
							//echo"AG".$row[graexuni_qtde],$disponivel[2] ;
							$agendados = $disponivel[1] - $disponivel[2];
						//echo $agendados . ' - ' . 'disponivel = ' . $disponivel[2] . ' graex_qtde ' . $row[graex_qtde];
							($agendados = ($agendados > 0) ? $agendados : null);
							//	Quantidade agendada para o dia
							echo "<input type=text size=1 name='agendado[]' readonly class=box style='background:#98FB98' value='$agendados' readonly>&nbsp;";
							
							//echo $disponivel[1]."&nbsp;&nbsp;";
							if($linha[0] != $data[0])
							{
								//echo $d . " - " . $hoje;
								if($d >= $hoje)
								{
									echo "<input type=text id=qtde size=1 name='qtde[]' class=box onChange=somar(this,'$data[0]',$proc_codigo,$med_codigo); value='$disponivel[1]'>";
								} else {
									echo "<input type=text size=1 name='qtde[]' class=box readonly style='background:#FF4500' value=''>";
								}
							} else {
								if($dia_ini != "")
								{
									echo "<input type=text size=1 name='qtde[]' class=box readonly style='background:#000080' value='7'>";
								}
							}
						echo "</td>";
						//echo "<td>";
						//echo "</td>";
					echo "</tr>";
				echo "</table>";
				echo "</td>";
				/*echo "<td>";
					echo "<b>".$dia_da_semana."</b>";
				echo "</td>";*/
				//echo $dia_semana;
				$k++;
			//}
			if($k % 7 == 0)
			{
				echo "</tr><tr>";
			}
		}
		echo "</tr>";
		echo "<tr>";
			echo "<td colspan=7 align=center>";
				echo "<input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg>";
			echo "</td>";
		echo "</tr>";
	echo "</table>";
	echo "<table border=0><tr><td class=box style='background:#FF4500' width=15px>&nbsp;<td width=180px>Data indisponivel para alteracao</td><td class=box style='background:#000080' width=15px>&nbsp;</td><td width=50px>Feriado</td><td class=box style='background:#EEE8AA' width=15px>&nbsp;</td><td width=100px>Vagas Disponiveis</td><td class=box style='background:#98FB98' width=15px>&nbsp;</td><td width=180px>Quantidade agendada para o dia</td><td class=box width=15px>&nbsp;</td><td width=180px>Campo para preenchimento</td><td>&nbsp;</td></tr></table>";
	echo "</form>";
	/*	echo "<script>parent.document.getElementById('gra_qtde_total').value = $gra_qtde_soma</script>";*/

	/*echo "<div style=\"height:178;width:450px;position:absolute;top:15%;left:25%;border:1px solid black;border-collapse:collapse;display:none;z-index:5\" id=\"msg_usuario\">";
		echo "<div class=\"titulo\" id=\"titulo\" style=\"background:url(".$_SESSION[linkroot].$_SESSION[comum]."imgs/jan_fundo_titulo.jpg);height:18px;\">
			<span id=\"janela_titulo_txt\" style=\"position:absolute;top:2px\">Altera&ccedil;&otilde;es</span>
			<div style=\"float:right;position:absolute;top:0px;left:94.5%;cursor:pointer;\"><img src=\"".$_SESSION[linkroot].$_SESSION[comum]."imgs/jan_fechar.jpg\" onclick=\"fecharCal('msg_usuario')\" alt=\"Fechar\"/></div>
		</div>
		<div style=\"background:#FDF5E6\">
			<table>
				<tr>
					<th width=\"100px\" style=\"text-align:left;\">Cadastrado por:</th>
					<td><div id=\"msg_usuario_cad\"></div></td>
				</tr>
				<tr>
					<th style=\"text-align:left;\">Alterado por:</th>
					<td><div id=\"msg_usuario_alt\"></div></td>
				</tr>
			</table>
		</div>
	</div>";*/
	
?>