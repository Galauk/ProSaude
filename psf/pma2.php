<script>
function validaForm(){
	
   	var indice = document.pma2.cid_codigo_ibge.selectedIndex;
   	var valorCidade = document.pma2.cid_codigo_ibge.options[indice].value; 

   	var indiceUni = document.pma2.uni_codigo.selectedIndex;
   	var valorUni = document.pma2.uni_codigo.options[indiceUni].value;

   	var segmento = document.getElementById("segmento").value;

   	var area = document.getElementById("area").value;

	var indiceMes = document.pma2.mes.selectedIndex;
   	var valorMes = document.pma2.mes.options[indiceMes].value; 

	var indiceAno = document.pma2.ano.selectedIndex;
   	var valorAno = document.pma2.ano.options[indiceAno].value; 
   	

    if(valorCidade == ""){
		alert("A cidade Deve ser preenchida!")
		document.pma2.cid_codigo_ibge.options[indice].focus();
		return false;
		
   	}
    if(segmento == ""){
		alert("O segmento Deve ser preenchido!")
		document.getElementById("segmento").focus();
		return false;
   	}
	if(valorUni == ""){
		alert("O Unidade Deve ser preenchido!")
		document.pma2.uni_codigo.options[indiceUni].focus();
		return false;
   	}
	if(area == ""){
		alert("O area Deve ser preenchido!")
		document.getElementById("area").focus();
		return false;
   	}
	if(valorMes == ""){
		alert("O area Deve ser preenchido!")
		document.getElementById("valorMes").focus();
		return false;
   	}
	if(area == ""){
		alert("O area Deve ser preenchido!")
		document.getElementById("area").focus();
		return false;
   	}
	document.pma2.submit();
}
</script>
<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
	require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
	require_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	$usr_codigo = $id_login;
	
	$common = new commonClass();
	$form = new classForm();
	$table = new tableClass();
	echo $common->incJquery();

	echo $common->menuTab(array('Inserir ficha PMA2'));
	echo $common->bodyTab('1');
		echo $form->openForm("pma2.php","POST","pma2");
			echo $form->hiddenForm("acao", "salvar");
			echo $form->hiddenForm("id_login", "$usr_codigo");
			$sqlCidade = "SELECT c.cid_codigo_ibge, 
								 c.cid_nome ||'-'|| e.uf_sigla as cid_est
							FROM cidade c
							JOIN estado e
							  ON c.uf_codigo = e.uf_codigo
						   ORDER BY c.cid_nome";
			echo $form->inputSelect("cid_codigo_ibge", null, "Cidade", $sqlCidade, null, "cidade", null, "style='width:200px'");
			echo $form->inputText("segmento", null, "Segmento", null, 2, "style='text-transform:uppercase'");
			$sqlUnidade = "SELECT uni_codigo, 
								  uni_desc
							 FROM unidade
						    ORDER BY uni_desc";
			echo $form->inputSelect("uni_codigo", null, "Unidade", $sqlUnidade, null, "unidade", null, "style='width:200px'");
			echo $form->inputText("area", null, "&Aacute;rea", null,3);
			$mes = Array("01"=>"Janeiro","02"=>"Fevereiro","03"=>"Mar&ccedil;o","04"=>"Abril","05"=>"Maio","06"=>"Junho","07"=>"Julho","08"=>"Agosto","09"=>"Setembro","10"=>"Outrubro","11"=>"Novembro","12"=>"Dezembro");
			echo $form->inputSelect("mes", $mes, "M&ecirc;s", null, null, null, date(m));
			$anoAtual = date('Y');
			$ano = array();
			for($i = 0; $i < 5; $i++){
				$ano[$anoAtual] = $anoAtual;
				$anoAtual--;
			}
			echo $form->inputSelect("ano", $ano, "Ano", null, null, null, date('Y'));
			echo $common->commonButton("Adicionar",null,"adicionar_on.png","onClick=validaForm();");
			echo $common->commonButton("Voltar","adicionarFichaPsfPma2.php",$_SESSION[linkroot].$_SESSION[comum]."imgsBotoes/voltar.png");
			//echo $form->submitButton("Adicionar", $_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg","onClick=return validaForm();");
			//echo $form->submitButton("Voltar", $_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg");
		echo $form->closeForm();
	echo $common->closeTab();
	if ($_POST['acao'] == "salvar"){
		$cid_codigo_ibge = $_POST['cid_codigo_ibge'];  
		$pma2_segmento = $_POST['segmento'];
		$uni_cnes = $_POST['uni_codigo'];
		$area_desc = $_POST['area'];
		$pma2_mes = $_POST['mes'];
		$pma2_ano = $_POST['ano'];		
		$pegaSeq = "SELECT nextval('pma2_pma2_codigo_seq'::regclass)";
		$exec = pg_query($pegaSeq);
		$linha = pg_fetch_array($exec);
		$pma2_codigo = $linha[0];
		$insert = "INSERT INTO pma2 ( pma2_codigo,
											cid_codigo_ibge, 
											pma2_segmento, 
											uni_cnes, 
											area_desc, 
											pma2_mes,
											pma2_ano, 
											usr_codigo
										  ) 
									VALUES 
										  ( '$pma2_codigo',
											'$cid_codigo_ibge', 
											'$pma2_segmento', 
											'$uni_cnes', 
											'$area_desc', 
											'$pma2_mes',
											'$pma2_ano', 
											$id_login
										  )";
		$execInsert = pg_query($insert);
		
		if ($execInsert){
			echo $common->modalMsg("OK", "PMA2 salvo com sucesso!", "fichaPMA2.php?pma2_codigo=$pma2_codigo");
		}else{
			echo $common->modalMsg("ERRO", "Houve um erro e os dados do PMA2 n&atilde;o foram salvos, tente novamente!", "fichaPMA2.php?acao=&pma2_codigo=$pma2_codigo#tabs-2");
		}
	}
?>