<script>

function validaForm(){
	
   	var indice = document.ssa2.cid_codigo_ibge.selectedIndex;
   	var valorCidade = document.ssa2.cid_codigo_ibge.options[indice].value; 

   	

   	var micro = document.getElementById("micro_area").value;

   	var area = document.getElementById("area_desc").value;

	var indiceMes = document.ssa2.mes.selectedIndex;
   	var valorMes = document.ssa2.mes.options[indiceMes].value; 

	var indiceAno = document.ssa2.ano.selectedIndex;
   	var valorAno = document.ssa2.ano.options[indiceAno].value; 
   	

    if(valorCidade == ""){
		alert("A cidade Deve ser preenchida!")
		document.ssa2.cid_codigo_ibge.options[indice].focus();
		return false;		
   	}
    if(micro == ""){
		alert("A Micro Area Deve ser preenchida!")
		document.getElementById("micro_area").focus();
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
	document.ssa2.submit();
}
</script>
<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
	require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
	require_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	
	$common = new commonClass();
	$form = new classForm();
	$table = new tableClass();
	echo $common->incJquery();
	$id_login = $_GET['id_login'];
	echo $common->menuTab(array('Inserir ficha SSA2'));
	
	echo $common->bodyTab('1');
	
	
		echo $form->openForm("fichaSsa2.php","POST","ssa2");
			echo $form->hiddenForm("acao", "add");
			echo $form->hiddenForm("id_login", "$id_login");
			
			$sqlCidade = "SELECT c.cid_codigo_ibge, 
								 c.cid_nome ||'-'|| e.uf_sigla as cid_est
							FROM cidade c
							JOIN estado e
							  ON c.uf_codigo = e.uf_codigo
						   ORDER BY c.cid_nome";
			echo $form->inputSelect("cid_codigo_ibge", null, "Cidade", $sqlCidade, null, "cid_codigo_ibge", null, "style='width:200px'");
			$mes = Array("01"=>"Janeiro","02"=>"Fevereiro","03"=>"Mar&ccedil;o","04"=>"Abril","05"=>"Maio","06"=>"Junho","07"=>"Julho","08"=>"Agosto","09"=>"Setembro","10"=>"Outrubro","11"=>"Novembro","12"=>"Dezembro");
			echo $form->inputSelect("mes", $mes, "M&ecirc;s", null, NULL, "mes", date(m));
			$anoAtual = date('Y');
			$ano = array();
			for($i = 0; $i < 5; $i++){
				$ano[$anoAtual] = $anoAtual;
				$anoAtual--;
			}
			echo $form->inputSelect("ano", $ano, "Ano", null, NULL, "ano", date('Y'));
			echo $form->inputText("area_desc", $area_desc,"&Aacute;rea");
			echo $form->inputText("micro_area", $micro_area,"Micro &Aacute;rea");
			echo $common->commonButton("Adicionar",null,"adicionar_on.png","onClick=validaForm();");
			echo $common->commonButton("Voltar","adicionarFichaPsf.php",$_SESSION[linkroot].$_SESSION[comum]."imgsBotoes/voltar.png");
		echo $form->closeForm();
	echo $common->closeTab();
		if($acao == 'add'){
			$id_login = $_POST['id_login'];
			$cidade = $_POST['cid_codigo_ibge'];
			$mes = $_POST['mes'];
			$ano = $_POST['ano'];
			$area_desc = $_POST['area_desc'];
			$micro_area  = $_POST['micro_area'];
			//$data = "01/".$mes."/".$ano;
			$pegaSeq = "SELECT nextval('ssa2_ssa2_codigo_seq'::regclass)";
			$exec = pg_query($pegaSeq);
			$linha = pg_fetch_array($exec);
			$ssa2_codigo = $linha[0];
			$inserir = "insert into ssa2 (ssa2_codigo,
												cid_codigo_ibge,
												ssa2_mes,
												ssa2_ano,
												usr_codigo,
												area_desc,
												micro_area) 
									     VALUES($ssa2_codigo,
									     		'$cidade',
									     		'$mes',
									     		'$ano',
									     		'$id_login',
									     		'$area_desc',
									     		'$micro_area')";
			
			$exeInserir = pg_query($inserir);
			
		if ($exeInserir){
			echo $common->modalMsg("OK", "Inserido com sucesso","SSA2.php?id_login=$id_login&ssa2_mes=$mes&ssa2_ano=$ano&cid_codigo_ibge=$cidade&ssa2_codigo=$ssa2_codigo");
		}else{
			echo $common->modalMsg("ERRO", "Houve um erro e os dados do SSA2 n&atilde;o foram salvos, tente novamente!", "fichaSsa2.php?id_login=$id_login&ssa2_mes=$mes&ssa2_ano=$ano&cid_codigo_ibge=$cidade&ssa2_codigo=$ssa2_codigo");
		}
			
		}
	
?>
