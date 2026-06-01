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
function buscarUnidade(uni_codigo,proc_codigo,med_codigo,id_dia_ini,id_dia_fim,gex_codigo,id_login){		
	var quantidade='manuntencaoAgendaExamePorUnidade.php?uni_codigo='+uni_codigo+'&proc_codigo='+proc_codigo+'&med_codigo='+med_codigo+'&gex_codigo='+gex_codigo+'&id_login='+id_login;
	document.getElementById('framesecundario').src = quantidade;
/*		ajax = ajaxInit();
		
		if(ajax) {
			ajax.open("GET", quantidade, true);
			
			ajax.onreadystatechange = function() {
				if(ajax.readyState == 4) {
					if(ajax.status == 200) {
						//alert(ajax.responseText);
						document.getElementById('agendaUnidade').innerHTML = ajax.responseText;
						}
				}
			}    
		ajax.send(null);
		
		}*/
		//	alert();
	}
	function somar()
	{
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
<?
//echo"<pre>".print_r($_GET)."</pre>";
	if($_POST[acao] == "salvar")
	{
		/*echo "<pre>";
			print_r($_POST);
		echo "</pre>";*/
		for($i = 0; $i < count($_POST['qtde']); $i++)
		{
			$med_codigo = $_POST[med_codigo];	
			$data = $_POST[data][$i];
			$qtde = $_POST[qtde][$i] == '' ? 0 : $_POST[qtde][$i]; 
			$proc_codigo = $_POST[proc_codigo];
			$id_login = $_POST[id_login];
			$graex_codigo = $_POST[graex_codigo][$i];
			$texto = $idade > 18 ? "maior idade" : "menor idade";

			
			//echo $i . "=" . $_POST[qtde][$i]."<br>";
			/*$select = "select gra_codigo from grade_medico where med_codigo = $_POST[med_codigo] and gra_data = '{$_POST[data][$i]}' and uni_codigo = $_POST[uni_codigo] and esp_codigo = $_POST[esp_codigo] and age_item = '$_POST[age_item]' and age_tipo = '$_POST[age_tipo]' and gra_hora_ini = '$_POST[gra_hora_ini]'"; // and gra_tipo = $POST[gra_tipo] -> precisa???
			$exec_select = pg_query($select);
			if(pg_num_rows($exec_select) == 0)*/
			//echo $_POST[action][$i]."<br>";
			if($_POST[action][$i] == "inserir")
			{
				if($_POST[qtde][$i] != "" && $_POST[qtde][$i] > 0 && $_POST[qtde][$i] != "Feriado")
				{
					//echo "insert - $i<br>";
					$insert = "insert into grade_exame
					(med_codigo, graex_data, graex_qtde, proc_codigo, usr_codigo_cad)
					values
					('$med_codigo', '$data', '$qtde', '$proc_codigo', '$id_login')";
					echo $insert;
//					$exec_insert = pg_query($insert);
					echo $insert."<br>";
					//exit;
					//echo pg_last_error($db);
				}
			} else {
			$sel = "select to_char(graex_data, 'DD/MM/YYYY') as dia,
			               graex_qtde as qtde, 
			               coalesce(graex_qtde, 0) - 
				           coalesce((select count(*) from agendamento_exame_lista 
				                            where agexl_data = a.graex_data
							    and   med_codigo = a.med_codigo
							    and   proc_codigo = a.proc_codigo), 0) as disponivel
			        from grade_exame as a
			        where med_codigo = $_POST[med_codigo]
			        and   proc_codigo = $_POST[proc_codigo] 
			        and   graex_data = '$row[graex_data]' 
			        order by graex_data";
				echo $exec_sel;
				$exec_sel = pg_query($sel);
				$disponivel = pg_fetch_array($exec_sel);
				if($_POST[qtde][$i]  >= $_POST[agendado][$i])
				{
					$update = "update grade_exame set graex_qtde = '$qtde', usr_codigo_alt = '$id_login' where graex_codigo = '$graex_codigo'";
					$exec_update = pg_query($update) or die(pg_last_error());
//echo $update."<br>";
				}
			}
			
		}
	echo "<script>parent.document.getElementById('frameprincipal').src = \"manutencaoagendaexame_iframe.php?med_codigo=$_POST[med_codigo]&proc_codigo=$_POST[proc_codigo]&id_dia_ini=$_POST[id_dia_ini]&id_dia_fim=$_POST[id_dia_fim]&id_login=$_POST[id_login]\"</script>";
	}


//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
//------------------------------------------------------------------>
/*echo "<pre>";
	print_r($_REQUEST);
echo "</pre>";*/
	$med_codigo = $_GET['med_codigo'];
	$data_ini = $_GET['id_dia_ini'];
	$data_fim = $_GET['id_dia_fim'];
	$gex_codigo = $_GET['gex_codigo'];
	
	$stmt_lab = "SELECT med_tipoagendamento as proc_tipo_manut FROM medico 
	             WHERE med_codigo= '$med_codigo'";
	$manut_row = db_getRow($stmt_lab);
	if ($manut_row['proc_tipo_manut'] == 'P')
	   echo "<center><h4> Os dados deste Laboratorio sao independentes do Procedimento. 
	          Equivalem ao total de pacientes atendidos por dia por Laboratorio. </h4></center>";
	if ($manut_row['proc_tipo_manut'] == 'V')
	{
	       $verificaexames = "select gem_codigo, gem_periodo, med_codigo, gem_valor 
	                          from grade_exame_mensal_manut
	                          where med_codigo = $med_codigo
				  and   gem_periodo  = '$data_ini'";
	       $verexames = db_getRow($verificaexames);
	       $totalexames = "select coalesce(sum(preco_procedimento(proc_codigo)),0) as valor 
	                          from agendamento_exame_lista
	                          where med_codigo = $med_codigo
				  and   agexl_data  >= '$data_ini'
				  and   agexl_data <= '$data_fim'";
	       $totexames = db_getRow($totalexames);

	       $diferenca = $verexames[3] - $totexames[0];
	   echo "<center><h4> Os dados deste Laboratorio sao expressos em valores durante o periodo. <br>
	          Valor maximo cadastrado: R$ $verexames[3] =>  Valor ja agendado no periodo: R$ $totexames[0] 
		  ==> <b>Saldo: R$ $diferenca</b><h4></center>";
        }

	
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
	echo "<input type=hidden name=proc_codigo value=$_GET[proc_codigo]>";
	echo "<input type=hidden name=id_login value=$_GET[id_login]>";
	echo "<input type=hidden name=id_dia_ini value=$_GET[id_dia_ini]>";
	echo "<input type=hidden name=id_dia_fim value=$_GET[id_dia_fim]>";
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
			$busca_feriado = "select to_char(fer_data, 'dd/mm/yyyy') as data from feriado where fer_data = (date '$data_ini' + $i)";
			$exec_busca = pg_query($busca_feriado);
			//echo pg_last_error($db);
			$linha = pg_fetch_array($exec_busca);
			/*if($linha != "")
			{
				echo $linha[0]."-<br>";
			}*/
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
			/*if($dia_semana != 0 && $dia_semana != 6)
			{*/
			$med_codigo = $_GET['med_codigo'];
			$stmt_lab = "SELECT med_tipoagendamento as proc_tipo_manut 
						   FROM medico 
						  WHERE med_codigo='$med_codigo'";
			
	                 $manut_row = db_getRow($stmt_lab);

				$busca = "select * from grade_exame where med_codigo = $_GET[med_codigo] and graex_data = '$data[0]' and proc_codigo = $proc_codigo";

				//echo $busca."<br>";
				$exec_busca = pg_query($busca);
				$row = pg_fetch_array($exec_busca);
				$gra_qtde_soma += $row[graex_qtde];
				
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
								echo "<input type=hidden name=graex_codigo[] value=$row[graex_codigo]>";
								echo "<input type=hidden name=data[] value=$data[0]>";
							} else {
								echo "<input type=hidden name=graex_codigo[] value='$row[graex_codigo]' readonly style='background:gray'>";
								echo "<input type=hidden name=data[] value='$data[0]' readonly style='background:gray'>";
							}
							if($dia_semana[0] != 0)
							{
								echo "<a href=\"F\" class=\"info\" onmouseover=\"buscar_usuario($row[graex_codigo]);\">";
							} else {
								echo "<a href=\"#\" class=\"info_2\" onmouseover=\"buscar_usuario($row[graex_codigo]);\">";
							}
								echo "<span style=\"width:200px;\">";
									echo "<table>";
										echo "<tr>";
											echo "<td>";
												echo "<strong>Cadastrado por:</strong>";
											echo "</td>";
											echo "<td id=\"msg_usuario_cad$row[graex_codigo]\"></td>";
										echo "</tr>";
										echo "<tr>";
											echo "<td>";
												echo "<strong>Alterado por:</strong>";
											echo "</td>";
											echo "<td id=\"msg_usuario_alt$row[graex_codigo]\"></td>";
										echo "</tr>";
									echo "</table>";
								echo "</span>";
								echo $data[0][0].$data[0][1]."-".$data[0][3].$data[0][4];
							echo "</a>";
						echo "</td>";
						echo "<td>";
			$sel = "select to_char(graex_data, 'DD/MM/YYYY') as dia,
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
			        order by graex_data";

							//echo $sel;
							$exec_sel = pg_query($sel);
							$disponivel = pg_fetch_array($exec_sel);
							echo "<input type=text size=1 name='disponivel' disabled class=box style='background: #EEE8AA' value='$disponivel[2]' readonly>&nbsp;";
							$agendados = $row[graex_qtde] - $disponivel[2];
						//	echo $agendados . ' - ' . 'disponivel = ' . $disponivel[2] . ' graex_qtde ' . $row[graex_qtde];
							($agendados = ($agendados > 0) ? $agendados : null);
							echo "<input type=text size=1 name='agendado[]' readonly class=box style='background:#98FB98' value='$agendados' readonly>&nbsp;";
							//echo $disponivel[1]."&nbsp;&nbsp;";
							if($linha[0] != $data[0])
							{
								//echo $d . " - " . $hoje;
								if($d >= $hoje)
								{
									echo "<input type=text size=1 name='qtde[]' class=box onChange=somar(); value='$row[graex_qtde]'>";
								} else {
									echo "<input type=text size=1 name='qtde[]' class=box readonly style='background:#FF4500' value='$row[graex_qtde]'>";
								}
							} else {
								if($data_ini != "")
								{
									echo "<input type=text size=1 name='qtde[]' class=box readonly style='background:#000080' value=''>";
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
	
	
	####################################################################################################################################
	####################################################################################################################################
	####################################################### UNIDADES  ##################################################################
    ####################################################################################################################################
	echo "<table width=100% align=center cellspacing=2 cellpadding=2 border=0>";
	echo "<tr>";
		echo "<td align=right>Unidade</td>";
//		echo "<td colspan=2>";
		echo "<td >";
			echo "<select name=unidade id=unidade class=boxr onchange='buscarUnidade(this.value,$proc_codigo,$med_codigo,$data_ini,$data_fim,$gex_codigo,$_GET[id_login])'>";
			//echo "<select name=unidade id=unidade class=boxr onchange='montarUnidade($id_login)'>";
			echo "<option value='0'>...</option>";
			$sql = pg_query("select * from unidade
					 order by uni_desc");
			while($med=pg_fetch_array($sql))
			{
			   echo "<option value='$med[uni_codigo]'>$med[uni_desc]</option>";
			}
			echo "</select>";
			echo "</td>";
		echo "</tr>";
	echo "</table>";
	echo "<iframe id=framesecundario name=framesecundario src=# frameborder=no marginheight=0 marginwidth=0 scrolling=yes width='100%' height=290></iframe>";
//	echo"<div id='agendaUnidade'></div>";
?>

