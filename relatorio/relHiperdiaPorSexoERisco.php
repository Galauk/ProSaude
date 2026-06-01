<link href="styleRelatorio.css" rel="stylesheet" type="text/css">
<?
	session_start();
 	require_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
	require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
	require_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
 	include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";

$common = new commonClass();
$form = new classForm();
$table = new tableClass();
$data= date("d/m/Y");
echo $common->incJquery();

$data_inicial = $_GET["di"];
$data_final = $_GET["df"];
$gel_codigo = $_GET["gel_codigo"];
$cid_codigo = $_GET["cid_codigo"];

$sqlCidade = "select * from cidade where cid_codigo = $cid_codigo";
$queryCidade = pg_query($sqlCidade);
$linhaCidade = pg_fetch_array($queryCidade);


echo "<link href=\"../estilo.css\" rel=\"stylesheet\" type=\"text/css\">";
	
	echo "<center>".$common->commonButton("Imprimir", null, "print.png", "onclick=\"javascript:window.print();this.style.display='none';\"")."</center>";

	echo "<table class=table style='font-size:14px;font-family:verdana' border=0>
			<tr>
				<td width=130><b>GEST&Atilde;O P&Uacute;BLICA DE SA&Uacute;DE</b></td>
				<td width=10 align=right>".date("d/m/Y h:i:s")."</td>
			</tr>
			<tr>
				<td colspan=2>".strtoupper(html_entity_decode($Tit))."</td>
			</tr>
			<tr>
				<td><b> Cidade: </b> $linhaCidade[cid_nome]</td>
				<td><b> cod.IBGE: </b>: $linhaCidade[cid_codigo_ibge]</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
		</table>";
		
	echo "<table width='100%' align='center'>
			<tr>
				<th colspan=7 style='font-size:16px;text-align:center'>
					Hipertensos e Diabeticos  por Sexo e risco
				</th>
			</tr>
			<tr>
				<td>
				 <table style='border: 1px solid black;' border='1' cellpadding='2' cellspacing='0' width='100%'>
				  <tbody><tr>
			
					
					<td rowspan='3' class='TabTitulo' align='center' width='7%'>
					  <span class='TitRel'>CIDADE</span>
					</td>
					<td colspan='5' class='TabTitulo' align='center' width='30%'>
					  <span class='TitRel'>No de Diabeticos</span>
					</td>
					<td colspan='3' rowspan='2' class='TabTitulo' align='center' width='18%'>
					  <span class='TitRel'>No de Hipertensos</span>
			
					</td>
					<td colspan='3' rowspan='2' class='TabTitulo' align='center' width='20%'>
					  <span class='TitRel'>No de Diabeticos com Hipertensao</span>
					</td>
					<!--
					<td colspan='6' class='TabTitulo' align=center width='25%'>
					  <span class='TitRel'>No de Hipertensos<br> por risco</span>
					</td>
					-->
				  </tr>
				  <tr>
					<td rowspan='2' class='TabTitulo' align='center'>
			
					  <span class='TitRelTerra'>Total</span>
					</td>
					<td colspan='2' class='TabTitulo' align='center'>
					  <span class='TitRel'>Tipo 1</span>
					</td>
					<td colspan='2' class='TabTitulo' align='center'>
					  <span class='TitRel'>Tipo 2</span>
			
					</td>
					<!--
					<td rowspan='2' class='TabTitulo' align=center>
					  <span class='TitRelTerra'>Total</span>
					</td>
					<td rowspan='2' class='TabTitulo' align=center>
					  <span class='CorpoAzul'>Baixo</span>
					</td>
					<td rowspan='2' class='TabTitulo' align=center>
					  <span class='CorpoAzul'>Médio</span>
					</td>
					<td rowspan='2' class='TabTitulo' align=center>
					  <span class='CorpoAzul'>Alto</span>
					</td>
					<td rowspan='2' class='TabTitulo' align=center>
					  <span class='CorpoAzul'>Muito<br>Alto</span>
					</td>
					<td rowspan='2' class='TabTitulo' align=center>
					  <span class='CorpoAzul'>Não<br>Aplicável</span>
					</td>
					-->
				  </tr>
				  <tr>
					<td class='TabTitulo' align='center'>
					  <span class='CorpoAzul'>Masc</span>
					</td>
					<td class='TabTitulo' align='center'>
			
					  <span class='CorpoAzul'>Fem</span>
					</td>
					<td class='TabTitulo' align='center'>
					  <span class='CorpoAzul'>Masc</span>
					</td>
					<td class='TabTitulo' align='center'>
					  <span class='CorpoAzul'>Fem</span>
			
					</td>
					<td class='TabTitulo' align='center'>
					  <span class='TitRelTerra'>Total</span>
					</td>
					<td class='TabTitulo' align='center'>
					  <span class='CorpoAzul'>Masc</span>
					</td>
					<td class='TabTitulo' align='center'>
			
					  <span class='CorpoAzul'>Fem</span>
					</td>
					<td class='TabTitulo' align='center'>
					  <span class='TitRelTerra'>Total</span>
					</td>
					<td class='TabTitulo' align='center'>
					  <span class='CorpoAzul'>Masc</span>
			
					</td>
					<td class='TabTitulo' align='center'>
					  <span class='CorpoAzul'>Fem</span>
					</td>
				  </tr>
				</td>
			</tr>
		</table>";
			if($data_inicial == ""){
				$whereData = "temp_data = '$data_final'";
			}
			if($data_inicial == "" && $data_final == ""){
				$whereData = "";	
			}
			if($data_inicial == true && $data_final == true){
				$whereData = "temp_data between '$data_inicial' and '$data_final'";	
			}
			
			$sqlHiperDiabetes1Total = "select count(hiper_codigo) as total from hiperdia where hiper_diabetes_1 = 'S'";
			$queryHiperDiabetes1Total = pg_query($sqlHiperDiabetes1Total);
			$regHiperDiabetes1Total = pg_fetch_array($queryHiperDiabetes1Total);
			$totalDiabetes = $regHiperDiabetes1Total["total"];
			
			$sqlHiperMasculinoDiabetes1 = "select count(hiper_codigo) as total 
											 from hiperdia as hip
											 join usuario as usu
											   on usu.usu_codigo = hip.usu_codigo
											where hiper_diabetes_1 = 'S' and usu_sexo = 'M'";
			$queryHiperMasculinoDiabetes1 = pg_query($sqlHiperMasculinoDiabetes1);
			$regHiperMasculinoDiabetes1 = pg_fetch_array($queryHiperMasculinoDiabetes1);
			$masculinoDiabetesTipo1 = $regHiperMasculinoDiabetes1['total'];
			
			$sqlHiperFemininoDiabetes1 = "select count(hiper_codigo) as total 
											 from hiperdia as hip
											 join usuario as usu
											   on usu.usu_codigo = hip.usu_codigo
											where hiper_diabetes_1 = 'S' and usu_sexo = 'F'";
			$queryHiperFemininoDiabetes1 = pg_query($sqlHiperFemininoDiabetes1);
			$regHiperFemininoDiabetes1 = pg_fetch_array($queryHiperFemininoDiabetes1);
			$femininoDiabetesTipo1 = $regHiperFemininoDiabetes1['total'];
			
			$sqlHiperMasculinoDiabetes2 = "select count(hiper_codigo) as total 
											 from hiperdia as hip
											 join usuario as usu
											   on usu.usu_codigo = hip.usu_codigo
											where hiper_diabetes_2 = 'S' and usu_sexo = 'M'";
			$queryHiperMasculinoDiabetes2 = pg_query($sqlHiperMasculinoDiabetes2);
			$regHiperMasculinoDiabetes2 = pg_fetch_array($queryHiperMasculinoDiabetes2);
			$masculinoDiabetesTipo2 = $regHiperMasculinoDiabetes2['total'];
			
			$sqlHiperFemininoDiabetes2 = "select count(hiper_codigo) as total 
											 from hiperdia as hip
											 join usuario as usu
											   on usu.usu_codigo = hip.usu_codigo
											where hiper_diabetes_2 = 'S' and usu_sexo = 'F'";
			$queryHiperFemininoDiabetes2 = pg_query($sqlHiperFemininoDiabetes2);
			$regHiperFemininoDiabetes2 = pg_fetch_array($queryHiperFemininoDiabetes2);
			$femininoDiabetesTipo2 = $regHiperFemininoDiabetes2['total'];
			
			$sqlHiperHipertensoTotal = "select count(hiper_codigo) as total from hiperdia where hiper_hipertensao = 'S'";
			$queryHiperHipertensoTotal = pg_query($sqlHiperHipertensoTotal);
			$regHiperHipertensoTotal = pg_fetch_array($queryHiperHipertensoTotal);
			$totalHipertenso = $regHiperHipertensoTotal["total"];
			
			$sqlHiperMasculinoHipertensao = "select count(hiper_codigo) as total 
											 from hiperdia as hip
											 join usuario as usu
											   on usu.usu_codigo = hip.usu_codigo
											where hiper_diabetes_1 = 'S' and usu_sexo = 'M'";
			$queryHiperMasculinoHipertensao = pg_query($sqlHiperMasculinoHipertensao);
			$regHiperMasculinoHipertensao = pg_fetch_array($queryHiperMasculinoHipertensao);
			$masculinoHipertensao = $regHiperMasculinoHipertensao['total'];
			
			$sqlHiperFemininoHipertensao = "select count(hiper_codigo) as total 
											 from hiperdia as hip
											 join usuario as usu
											   on usu.usu_codigo = hip.usu_codigo
											where hiper_diabetes_1 = 'S' and usu_sexo = 'F'";
			$queryHiperFemininoHipertensao = pg_query($sqlHiperFemininoHipertensao);
			$regHiperFemininoHipertensao = pg_fetch_array($queryHiperFemininoHipertensao);
			$femininoHipertensao = $regHiperFemininoHipertensao['total'];
			
			$sqlHiperdia = "  SELECT count(hiper_codigo) as total 
			                    FROM hiperdia as hip
								JOIN usuario as usu
								  ON usu.usu_codigo = hip.usu_codigo
							   WHERE hiper_hipertensao = 'S' 
							     AND hiper_diabetes_1 = 'S' 
								  OR hiper_diabetes_2 = 'S' ";
			$queryHiperdia = pg_query($sqlHiperdia);
			$regHiperdia = pg_fetch_array($queryHiperdia);
			$totalHiperdia = $regHiperdia['total'];
			
			$sqlHiperdiaMasculino = "  SELECT count(hiper_codigo) as total 
			                    FROM hiperdia as hip
								JOIN usuario as usu
								  ON usu.usu_codigo = hip.usu_codigo
							   WHERE hiper_hipertensao = 'S' 
							     AND hiper_diabetes_1 = 'S' 
								  OR hiper_diabetes_2 = 'S' 
								 AND usu_sexo = 'M'";
			$queryHiperdiaMasculino = pg_query($sqlHiperdiaMasculino);
			$regHiperdiaMasculino = pg_fetch_array($queryHiperdiaMasculino);
			$totalHiperdiaMasculino = $regHiperdiaMasculino['total'];
			
			$sqlHiperdiaFeminino = "  SELECT count(hiper_codigo) as total 
			                    FROM hiperdia as hip
								JOIN usuario as usu
								  ON usu.usu_codigo = hip.usu_codigo
							   WHERE hiper_hipertensao = 'S' 
							     AND (hiper_diabetes_1 = 'S' 
								  OR hiper_diabetes_2 = 'S') 
								 AND usu_sexo = 'F'";
			$queryHiperdiaFeminino = pg_query($sqlHiperdiaFeminino);
			$regHiperdiaFeminino = pg_fetch_array($queryHiperdiaFeminino);
			$totalHiperdiaFeminino = $regHiperdiaFeminino['total'];
			echo "
			<br/>
			<table border='1' width=100%>
				<tr>
				  	<td width='9%'>
						<b>$linhaCidade[cid_nome]
					</td>
					<td align='center' width='10%' class='CorpoTerra'>
						$totalDiabetes
					</td>
					<td align='center'>
						$masculinoDiabetesTipo1
					</td>
					<td align='center' width='7%'>
						$femininoDiabetesTipo1
					</td>
					<td align='center' width='8%'>
						$masculinoDiabetesTipo2
					</td>
					<td align='center'  width='7%'>
						$femininoDiabetesTipo2
					</td>
					<td align='center' class='CorpoTerra'>
						$totalHipertenso
					</td>
					<td align='center'>
						$masculinoHipertensao
					</td>
					<td align='center'>
						$femininoHipertensao
					</td>
					<td align='center' class='CorpoTerra'>
						$totalHiperdia
					</td>
					<td align='center'>
						$totalHiperdiaMasculino
					</td>
					<td align='center'>
						$totalHiperdiaFeminino
					</td>
				  </tr>
			</table>";