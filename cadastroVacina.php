<link href="estiloNovo.css" type="text/css" rel="stylesheet">
<script language="JavaScript" type="text/javascript" src="ajax_motor.js"></script>
<script language="JavaScript" type="text/javascript" src="funcoes.js"></script>
<?php
include_once 'global.php';
include_once $_SESSION['root'].$_SESSION['modulo']."authlib.inc.php";
verauth($id_login);


include_once $_SESSION['root'].$_SESSION['modulo']."funcao.calendario.php";

include_once $_SESSION['root'].$_SESSION['comum']."library/php/funcoes.inc.php";

$table = new tableClass();
$common = new commonClass();
$form = new classForm();

echo $common->incJquery();
echo $common->menuTab(array("Cadastro De Vacinas"));
if($acao == ""){
	echo $table->openTable("table",null,1);
    echo "<a href='$PHP_SELF?acao=form_edit&add=add'><img src=$_SESSION[linkroot]$_SESSION[comum]imgs/adicionar_on.jpg></a>";
	echo $table->closeTable();
	echo $form->openForm("$PHP_SELF","POST");
	echo $table->openTable("lista");
	echo $form->hiddenForm("acao","form_add");
	
	echo $table->criaLinha(array("Nome"),null,array("3"),"S");
		$sqlListaVacina = "SELECT *
		               FROM produto as pro		               
		               JOIN grupo as g
		                 ON pro.gru_codigo = g.gru_codigo
		               JOIN carteirinha as c
		                 ON c.pro_codigo = pro.pro_codigo
		              WHERE gru_nome ilike '%vacina%'";		
		
		$queryListaVacina = pg_query($sqlListaVacina);
		while($r = pg_fetch_array($queryListaVacina)){		
			echo $table->criaLinha(
                array(
                    "$r[pro_nome]",
                    "<a href=\"cadastroVacina.php?acao=form_edit&pro_codigo=$r[pro_codigo]\"><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg'></a>",
                    "<a href=\"cadastroVacina.php?acao=deletar&pro_codigo=$r[pro_codigo]\"><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.jpg'></a>"
                )
            );
		}
	echo $table->closeTable();
	echo $form->closeForm();
}
if($acao == "form_edit"){
    echo $form->openForm("$PHP_SELF","POST");

    if($add == "add"){
        echo $form->hiddenForm("acao","salvar");
        $and = "AND pro_codigo not in (SELECT pro_codigo FROM carteirinha)";
    }else{
        echo $form->hiddenForm("acao","editar");
        echo $form->hiddenForm("pro_codigo","$pro_codigo");
        $and = "";
    }

    echo "
        <table border='0'>
            <tr>
                <td width='150px'>";
                $sqlCarrega = "select pro.pro_nome as pro,* 
                                    from produto as pro										
                                    left join carteirinha as car
                                    on pro.pro_codigo = car.pro_codigo
                                where pro.pro_codigo = $pro_codigo";
                $qryCarrega = pg_query($sqlCarrega);
                $trasDados = pg_fetch_array($qryCarrega);
                //echo $form->inputText("nome_vac",$trasDados['pro'],"Nome");
                $sqlVacina = "SELECT pro_codigo,
                                pro_nome
                                FROM produto AS p
                                JOIN grupo AS g
                                    ON g.gru_codigo = p.gru_codigo
                                WHERE g.gru_nome ilike '%VACINA%'
                                $and";

                $sqlEstrategia = "SELECT * FROM vacina_estrategia;";
                                
                $queryVacina = pg_query($sqlVacina);
                
                error_reporting(E_ALL);

                $queryEstrategia = pg_query($sqlEstrategia) or die(pg_lasty_error());

                $estrategias = Array();

                while($row = pg_fetch_assoc($queryEstrategia)){
                    // array_reduce($estrategias, $row);
                    $estrategias[$row['vac_est_codigo']] = $row['descricao'];
                    // $estrategias['est_codigo'] = $row['vac_est_codigo'];
                }
                
                // echo "<pre>";
                // print_r($estrategias);

                echo $form->hiddenForm("pro_codigo_antigo","$trasDados[pro_codigo]");
                echo $form->inputSelect("pro_codigo",null,"Produto",$sqlVacina,null,null,$pro_codigo);
                echo "
                </td>
                <td width=5px></td>
            </tr>
            <tr>
                <td></td>
            </tr>
            <tr>
                <td width='150px'></td>
            </tr>
            <tr>
                <td></td>
            </tr>
            <tr>
                <td width='100px'>";
                $arraySimNao = array("S"=>"Sim","N"=>"Nao");
                echo $form->inputSelect("dose",$arraySimNao,"Dose",null,null,null,$trasDados['dose']);
            echo "
                </td>
            </tr>
            <tr>
                <td width='100px'>";
                    $arraySimNao = array("S"=>"Sim","N"=>"Nao");
                    echo $form->inputSelect("dose_unica", $arraySimNao,"Dose &Uacute;nica",null,null,null,$trasDados['dose_unica']);
            echo "
                </td>
            </tr>
            <tr>
                <td width='100px'>";
                    $arraySimNao = array("S"=>"Sim","N"=>"Nao");
                    echo $form->inputSelect("dose_um", $arraySimNao,"Primeira Dose",null,null,null,$trasDados['dose_um']);
                    echo"
                </td>
            </tr>
            <tr>
                <td>";
                    echo $form->inputSelect("dose_dois",$arraySimNao,"Segunda Dose",null,null,null,$trasDados['dose_dois']);	
                    echo" 
                </td>							
            </tr>
            <tr>
                <td width='100px'>";
                    echo $form->inputSelect("dose_tres",$arraySimNao,"Terceira Dose",null,null,null,$trasDados['dose_tres']); 
            echo "
                </td>
            </tr>
            <tr>
                <td>";
                    echo $form->inputSelect("dose_quatro",$arraySimNao,"Quarta Dose",null,null,null,$trasDados['dose_quatro']);
                echo"
                </td>							
            </tr>
            <tr>
                <td width='100px'>";
                    echo $form->inputSelect("dose_cinco",$arraySimNao,"Quinta Dose",null,null,null,$trasDados['dose_cinco']); 
                    echo"
                </td>
            </tr>
            <tr>
                <td>";
                    echo $form->inputSelect("reforco",$arraySimNao,"Refor&ccedil;o 1",null,null,null,$trasDados['reforco']);
                    echo"	 
                </td>							
            </tr>
            <tr>
                <td>";
                    echo $form->inputSelect("reforco_2",$arraySimNao,"Refor&ccedil;o 2",null,null,null,$trasDados['reforco_2']);
                    echo"	 
                </td>							
            </tr>
            <tr>
                <td>";
                    $arrayDoses = array("0", "1", "2", "3", "4");
                    echo $form->inputSelect("revacinacao", $arrayDoses,"Revacina&ccedil;&atilde;o",null,null,null,$trasDados['revacinacao']);
                    
                    echo"	 
                </td>							
            </tr>
            <tr>
                <td>";
                    $arrayT = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20");

                    echo $form->inputSelect("tratamento",$arrayT,"Tratamento",null,null,null,$trasDados['tratamento']);
                    echo"	 
                </td>							
            </tr>
            <tr>
                <td>";
                    echo $form->inputText("codigo_exportacao",$trasDados['codigo_exportacao'],"C&oacute;digo exporta&ccedil;&atilde;o");
                    echo"	 
                </td>							
            </tr>
            <tr>
                <td>";
                    echo $form->inputSelect("estrategia", $estrategias, "Estrat&eacute;gia");
                    echo"	 
                </td>							
            </tr>
            <tr>
                <td>
                    <a href=\"cadastroVacina.php\"><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.jpg'></a>
                    <input type='image' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg'>
                </td>	
            </tr>
            </table>
		</td>
	    </tr>
    </table>";
    echo $form->closeForm();
 }

if($acao == "salvar"){
	$validade = $_POST['validade'];
	$dose = $_POST['dose'];
	$dose_unica = $_POST['dose_unica'];
	$dose_um = $_POST['dose_um'];
	$dose_dois = $_POST['dose_dois'];
	$dose_tres = $_POST['dose_tres'];
	$dose_quatro = $_POST['dose_quatro'];
	$dose_cinco = $_POST['dose_cinco'];
	$reforco = $_POST['reforco'];
	$reforco_2 = $_POST['reforco_2'];
	$revacinacao = $_POST['revacinacao'];
	$tempo = $_POST['tempo'];
	$nome_vac = $_POST['nome_vac'];
	$num_doses = $_POST['num_doses'];
	$marca = $_POST['marca'];
	$pro_codigo = $_POST['pro_codigo'];
    $codigo_exportacao = $_POST['codigo_exportacao'];
    
    $sqlCarteirinha = "insert into 
                        carteirinha
                        (pro_codigo,
                        dose,
                        dose_unica,
                        dose_um,
                        dose_dois,
                        dose_tres,
                        dose_quatro,
                        dose_cinco,
                        reforco,
                        reforco_2,
                        revacinacao,
                        codigo_exportacao)
                    values(
                        $pro_codigo,
                        $dose,
                        '$dose_unica',
                        '$dose_um',
                        '$dose_dois',
                        '$dose_tres',
                        '$dose_quatro',
                        '$dose_cinco',
                        '$reforco',
                        '$reforco_2',
                        $revacinacao,
                        $codigo_exportacao)";

                        
    $queryCarteirinha = pg_query($sqlCarteirinha);
    
    echo $common->modalMsg("OK","Salvo com Sucesso!","cadastroVacina.php");
}

if($acao == "editar"){
	$validade = $_POST['validade'];
	$dose = $_POST['dose'];
	$dose_unica = $_POST['dose_unica'];
	$dose_um = $_POST['dose_um'];
	$dose_dois = $_POST['dose_dois'];
	$dose_tres = $_POST['dose_tres'];
	$dose_quatro = $_POST['dose_quatro'];
	$dose_cinco = $_POST['dose_cinco'];
	$reforco = $_POST['reforco'];
	$reforco_2 = $_POST['reforco_2'];
	$revacinacao = $_POST['revacinacao'];
	$tratamento = $_POST['tratamento'];
	$tempo = $_POST['tempo'];
	$nome_vac = $_POST['nome_vac'];
	$num_doses = $_POST['num_doses'];
	$marca = $_POST['marca'];
	$pro_codigo = $_POST['pro_codigo'];
    $codigo_exportacao = $_POST['codigo_exportacao'] ? $_POST['codigo_exportacao'] : 0;

  
	$alteraCarteirinha = "UPDATE carteirinha SET dose = '$dose',
                                                 dose_unica = '$dose_unica',
												 dose_um = '$dose_um',
											     dose_dois = '$dose_dois',
											     dose_tres = '$dose_tres',
											     dose_quatro = '$dose_quatro',
											     dose_cinco = '$dose_cinco',
											     reforco = '$reforco',
											     reforco_2 = '$reforco_2',
											     revacinacao = '$revacinacao',
											     tratamento = '$tratamento',
											     pro_codigo = $pro_codigo,
												 codigo_exportacao = $codigo_exportacao
                                           WHERE pro_codigo = $pro_codigo_antigo";
   
	if(pg_query($alteraCarteirinha)){
		echo $common->modalMsg("OK","Editado com Sucesso!","cadastroVacina.php");
	} else {
		echo $common->modalMsg("ERRO","Erro ao editar!","cadastroVacina.php","$alteraCarteirinha");
	}
		
}
if($acao == "deletar"){
	$pegaNome = "select pro_nome from produto where pro_codigo = $pro_codigo";
	$qryNome = pg_query($pegaNome);
	$name = pg_fetch_array($qryNome);
	echo $common->modalConfirm("Deseja deletar o Produto $name[pro_nome]?", "cadastroVacina.php?acao=del&pro_codigo=$pro_codigo","cadastroVacina.php");
}
if($acao == "del"){
		$validaProduto = "select * from vacina_usuario where pro_codigo = $pro_codigo";
		$queryValidaProduto = pg_query($validaProduto);
		$numRegistros = pg_num_rows($queryCarteirinha);
		
		if($numRegistros == 0){
			$delCarteirinha = "DELETE from carteirinha where pro_codigo = $pro_codigo";
			$qryDelCarteirinha = pg_query($delCarteirinha);
			
			if(pg_query($delCarteirinha)){;
				echo $common->modalMsg("OK", "Excluido com Sucesso","cadastroVacina.php");
			}else{
				echo $common->modalMsg("ERRO", "Erro ao excluir!","cadastroVacina.php",$delPart);
			}
		}else{
		echo $common->modalMsg("ERRO", "Esse produto n&atilde;o pode ser apagado pois possui registros!","cadastroVacina.php");
		}
	}
?>