<?
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	cabecario();
//------------------------------------------------------------------>

?>
<script type="text/javascript" src="funcoes.js"></script>
<script type="text/javascript" src="ajax_motor.js"></script>
<script>
	function somar()
	{
		gra_qtde_total = parent.document.getElementById('gra_qtde_total');
		inputs = document.getElementsByTagName('input');

		soma = new Number(0);
		for(i=0;i<inputs.length;i++)
		{
			if(inputs[i].name == "qtde[]")
			{
				if(isNaN(inputs[i].value))
				{
					c = false;
				} else {
					soma = soma + new Number(inputs[i].value);
				}
			}
		}
		gra_qtde_total.value = soma;
	}
	
	function buscar_usuario(gra_codigo)
	{
		if(gra_codigo != undefined)
		{
			url = "buscar_generico.php?tipo=usuario_manutencao&gra_codigo="+gra_codigo;
			ajax_tudo(url, retorno);
		} else {
		}
	}
	
	function retorno(txt)
	{
		txt = eval(txt);
		document.getElementById("msg_usuario_cad"+txt[0].codigo).innerHTML = txt[0].usr_cad;
		document.getElementById("msg_usuario_alt"+txt[0].codigo).innerHTML = txt[0].usr_alt;
	}
	
</script>
<?

	if($_POST[acao] == "salvar")
	{

		for($i = 0; $i < count($_POST[qtde]); $i++)
		{
			if($_POST[action][$i] == "inserir")
			{
				if($_POST[qtde][$i] != "" && $_POST[qtde][$i] > 0 && $_POST[qtde][$i] != "Feriado")
				{

					$insert = "INSERT INTO grade_medico
													   (med_codigo, 
													    gra_data, 
													    uni_codigo, 
													    gra_tipo, 
													    gra_status, 
													    gra_qtde, 
													    esp_codigo, 
													    gra_hora_ini, 
													    age_item, 
													    age_tipo, 
													    gra_bloqueado, 
													    usr_codigo_cad)
												 VALUES ($_POST[med_codigo], 
												  		 '{$_POST[data][$i]}', 
														 $_POST[uni_codigo], 
														 'PC', 
														 'S', 
														 {$_POST[qtde][$i]}, 
														 $_POST[esp_codigo], 
														 '$_POST[gra_hora_ini]', 
														 '$_POST[age_item]', 
														 '$_POST[age_tipo]', 
														 '$_POST[gra_bloqueado]', 
														 $id_login)";
					$exec_insert = pg_query($insert);
				}
			} else {
				$sel = "select to_char(gra_data, 'DD/MM/YYYY') as dia, 
							   gra_qtde as qtde,
							   coalesce(gra_qtde, '0') - coalesce(age_qtde, '0') as disponivel
						  from (select gra_data, 
						               sum(gra_qtde) as gra_qtde, 
						               med_codigo, 
						               esp_codigo, 
						               uni_codigo
								  from grade_medico 
								 where med_codigo='$_POST[med_codigo]' 
								   and esp_codigo='$_POST[esp_codigo]' 
								   and uni_codigo='$_POST[uni_codigo]'
								   and gra_data = '{$_POST[data][$i]}' 
								 group by gra_data, 
								          med_codigo,
										  esp_codigo, 
										  uni_codigo ) t1 
						  left join (select age_data, 
						  					esp_codigo,
						  					med_codigo, 
						  					uni_codigo,
						  					count(age_codigo) as age_qtde 
						  			   from agendamento 
						  			  where med_codigo='$_POST[med_codigo]' 
						  			  	and esp_codigo='$_POST[esp_codigo]'
										and uni_codigo='$_POST[uni_codigo]' 
										and age_data = '{$_POST[data][$i]}'
										and age_atendido in ('N', 'R', 'S') 
										and (age_status <> 'C' or age_status is null)
									  group by age_data, 
									        esp_codigo, 
									        med_codigo, 
									        uni_codigo)t2 
									    on (t1.med_codigo = t2.med_codigo
											and t1.esp_codigo = t2.esp_codigo 
											and t1.uni_codigo = t2.uni_codigo
											and t1.gra_data = t2.age_data) 
									  order by t1.gra_data";
				$exec_sel = pg_query($sel);
				$disponivel = pg_fetch_array($exec_sel);

				if($_POST[qtde][$i]  >= $_POST[agendado][$i])
				{
					$update = "update grade_medico set gra_qtde = {$_POST[qtde][$i]}, gra_hora_ini = '$_POST[gra_hora_ini]', usr_codigo_alt = $_POST[id_login] where gra_codigo = {$_POST[gra_codigo][$i]}";
					$exec_update = pg_query($update);
				}
			}
			
		}
		echo "<script>parent.document.getElementById('frameprincipal').src = \"manutencaomedico_iframe.php?med_codigo=$_POST[med_codigo]&uni_codigo=$_POST[uni_codigo]&esp_codigo=$_POST[esp_codigo]&gra_hora_ini=$_POST[gra_hora_ini]&id_dia_ini=$_POST[id_dia_ini]&id_dia_fim=$_POST[id_dia_fim]&age_tipo=$_POST[age_tipo]&age_item=$_POST[age_item]&id_login=$_POST[id_login]&gra_bloqueado=$_POST[gra_bloqueado]\"</script>";
	}


//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
//------------------------------------------------------------------>

	$data_ini = $_GET[id_dia_ini];
	$data_fim = $_GET[id_dia_fim];
	
	$select = "select (date '$data_fim' - date '$data_ini') as total";
	$select_dias = pg_query($select);
	$total_dias = pg_fetch_array($select_dias);
	$total_dias[0];
	$exp=explode("/",$data_ini);
	$dia = date("D", $data_ini);
	
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
	echo "<input type=hidden name=uni_codigo value=$_GET[uni_codigo]>";
	echo "<input type=hidden name=esp_codigo value=$_GET[esp_codigo]>";
	echo "<input type=hidden name=gra_hora_ini value=$_GET[gra_hora_ini]>";
	echo "<input type=hidden name=age_item value=$_GET[age_item]>";
	echo "<input type=hidden name=age_tipo value=$_GET[age_tipo]>";
	echo "<input type=hidden name=id_login value=$_GET[id_login]>";
	echo "<input type=hidden name=id_dia_ini value=$_GET[id_dia_ini]>";
	echo "<input type=hidden name=id_dia_fim value=$_GET[id_dia_fim]>";
	echo "<input type=hidden name=gra_bloqueado value=$_GET[gra_bloqueado]>";
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
		$k = 0;
		$buscar = pg_query("select to_char(current_date, 'w') as dia_semana");
		$dia_semana = pg_fetch_array($buscar);
		for($i = 0; $i <= $total_dias[0]; $i++)
		{
			$busca_feriado = "select to_char(fer_data, 'dd/mm/yyyy') as data from feriado where fer_data = (date '$data_ini' + $i)";
			$exec_busca = pg_query($busca_feriado);
			//echo pg_last_error($db);
			$linha = pg_fetch_array($exec_busca);
			$select = "select to_char(date '$data_ini' + $i, 'dd/mm/yyyy') as data";
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
				$busca = "select * from grade_medico where med_codigo = $_GET[med_codigo] and gra_data = '$data[0]' and uni_codigo = $_GET[uni_codigo] and esp_codigo = $_GET[esp_codigo] and age_item = '$_GET[age_item]' and age_tipo = '$_GET[age_tipo]' and gra_hora_ini = '$_GET[gra_hora_ini]'";
				
				$exec_busca = pg_query($busca);
				$row = pg_fetch_array($exec_busca);
				$gra_qtde_soma += $row[gra_qtde];
				
				$d = explode("/", $data[0]);
				$d = $d[2]."-".$d[1]."-".$d[0];
				
				echo "<td>";
				echo "<table cellpading=0 cellspacing=0 border=0 class='table'>";
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
								echo "<input type=hidden name=gra_codigo[] value=$row[gra_codigo]>";
								echo "<input type=hidden name=data[] value=$data[0]>";
							} else {
								echo "<input type=hidden name=gra_codigo[] value='$row[gra_codigo]' readonly style='background:gray'>";
								echo "<input type=hidden name=data[] value='$data[0]' readonly style='background:gray'>";
							}
							if($dia_semana[0] != 0)
							{
								echo "<a href=\"#\" class=\"info\" onmouseover=\"buscar_usuario($row[gra_codigo]);\">";
							} else {
								echo "<a href=\"#\" class=\"info_2\" onmouseover=\"buscar_usuario($row[gra_codigo]);\">";
							}
								echo "<span style=\"width:200px;\">";
									echo "<table class='table'>";
										echo "<tr>";
											echo "<td>";
												echo "<strong>Cadastrado por:</strong>";
											echo "</td>";
											echo "<td id=\"msg_usuario_cad$row[gra_codigo]\"></td>";
										echo "</tr>";
										echo "<tr>";
											echo "<td>";
												echo "<strong>Alterado por:</strong>";
											echo "</td>";
											echo "<td id=\"msg_usuario_alt$row[gra_codigo]\"></td>";
										echo "</tr>";
									echo "</table>";
								echo "</span>";
								echo $data[0][0].$data[0][1]."-".$data[0][3].$data[0][4];
							echo "</a>";
						echo "</td>";
						echo "<td>";
							$sel = "select to_char(gra_data, 'DD/MM/YYYY') as dia, 
										   gra_qtde as qtde,
										   coalesce(gra_qtde, '0') - coalesce(age_qtde, '0') as disponivel
									  from (select gra_data, sum(gra_qtde) as gra_qtde, 
									  			   med_codigo, esp_codigo, 
									  			   uni_codigo
											  from grade_medico 
											 where med_codigo='$_GET[med_codigo]' 
											   and esp_codigo='$_GET[esp_codigo]' 
											   and uni_codigo='$_GET[uni_codigo]'
											   and gra_data = '$data[0]' 
											   and gra_hora_ini = '$_GET[gra_hora_ini]' 
											   and age_tipo = '$_GET[age_tipo]' 
											   and age_item = '$_GET[age_item]' 
											 group by gra_data, 
											 	   med_codigo, 
											 	   esp_codigo, 
											 	   uni_codigo) t1 
									  left join (select age_data, 
									  					esp_codigo, 
									  					med_codigo, 
									  					uni_codigo,
														count(age_codigo) as age_qtde 
												   from agendamento 
												  where med_codigo='$_GET[med_codigo]' 
												    and esp_codigo='$_GET[esp_codigo]'
													and uni_codigo='$_GET[uni_codigo]' 
													and age_data = '$data[0]'
													and age_atendido in ('N', 'R', 'S') 
													and (age_status <> 'C' or age_status is null)  
													and age_item = '$_GET[age_tipo]' 
													and age_tipo = '$_GET[age_item]' 
													and age_hora = '$_GET[gra_hora_ini]'
												  group by age_data, 
												  		esp_codigo, 
												  		med_codigo, 
												  		uni_codigo) t2 
											 on (t1.med_codigo = t2.med_codigo
											 	and t1.esp_codigo = t2.esp_codigo 
											 	and t1.uni_codigo = t2.uni_codigo
												and t1.gra_data = t2.age_data) 
										  order by t1.gra_data";
							$exec_sel = pg_query($sel);
							$disponivel = pg_fetch_array($exec_sel);
							echo "<input type=text size=1 name='disponivel' disabled class=box style='background: #EEE8AA' value='$disponivel[2]' readonly>&nbsp;";
							$agendados = $row[gra_qtde] - $disponivel[2];
							($agendados = ($agendados > 0) ? $agendados : null);
							echo "<input type=text size=1 name='agendado[]' readonly class=box style='background:#98FB98' value='$agendados' readonly>&nbsp;";
							if($linha[0] != $data[0])
							{
								if($d >= $hoje)
								{
									echo "<input type=text size=1 name='qtde[]' class=box onChange=somar(); value='$row[gra_qtde]'>";
								} else {
									echo "<input type=text size=1 name='qtde[]' class=box readonly style='background:#FF4500' value='$row[gra_qtde]'>";
								}
							} else {
								if($data_ini != "")
								{
									echo "<input type=text size=1 name='qtde[]' class=box readonly style='background:#000080' value=''>";
								}
							}
						echo "</td>";

					echo "</tr>";
				echo "</table>";
				echo "</td>";

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
	echo "<table class='table' border=0><tr><td class=box style='background:#FF4500' width=15px>&nbsp;<td width=180px>Data indisponivel para alteracao</td><td class=box style='background:#000080' width=15px>&nbsp;</td><td width=50px>Feriado</td><td class=box style='background:#EEE8AA' width=15px>&nbsp;</td><td width=100px>Vagas Disponiveis</td><td class=box style='background:#98FB98' width=15px>&nbsp;</td><td width=180px>Quantidade agendada para o dia</td><td class=box width=15px>&nbsp;</td><td width=180px>Campo para preenchimento</td><td>&nbsp;</td></tr></table>";
	echo "</form>";
	echo "<script>parent.document.getElementById('gra_qtde_total').value = $gra_qtde_soma</script>";

?>

