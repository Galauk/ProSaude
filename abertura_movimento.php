<script language="JavaScript" type="text/javascript" src="funcoes.js"></script>
<script>
	function edita(set_codigo,setp_codigo,setp_data_inicial,setp_data_final){
		document.aberturaMovimento.set_codigo.value = set_codigo;
		document.aberturaMovimento.setp_data_inicial.value = setp_data_inicial;
		document.aberturaMovimento.setp_data_final.value = setp_data_final;
		document.aberturaMovimento.setp_codigo.value = setp_codigo;
		document.aberturaMovimento.set_codigo.disabled = true;
	}
	function cancel()
	{
	       document.aberturaMovimento.set_codigo.disabled = false;
	       document.aberturaMovimento.setp_codigo.value = '';
	       document.aberturaMovimento.setp_data_inicial.value = '';
	       document.aberturaMovimento.setp_data_final.value = '';
	}

	function verifica()
	{
	       if(document.paciente.usu_nome.value == '')
	       {
	           alert("Por favor Preencha o Nome");
	           return false;
	       }
	       
	       if(document.paciente.usu_mae.value == '')
	       {
	           alert("Por favor Preencha o Nome da Mae");
	           return false;
	       }
	       
	       if(document.paciente.usu_datanasc.value == '')
	       {
	           alert("Por favor Preencha a Data de Nascimento");
	           return false;
	       }
	       
	       if(document.paciente.usu_end_rua.value == '')
	       {
	           alert("Por favor Preencha a Rua");
	           return false;
	       }
	       
	       if(document.paciente.usu_end_nr.value == '')
	       {
	           alert("Por favor Preencha o Numero");
	           return false;
	       }
	       
	       if(document.paciente.usu_end_bairro.value == '')
	       {
	           alert("Por favor Preencha o Bairro");
	           return false;
	       }
	       
	       if(document.paciente.usu_end_cidade.value == '')
	       {
	           alert("Por favor Preencha a Cidade");
	           return false;
	       }
	       return true;
	}

	function validaData(idData){
		elmt = document.getElementById(idData);
		strdata = elmt.value;
		//Verifica a quantidade de digitos informada esta correta.
		if (strdata.length != 10){
			elmt.value = '';
			elmt.focus();
			alert("Formato da data nao e valido. Formato correto: - dd/mm/aaaa.");
			return false;
		}
		//Verifica mascara da data
		if ("/" != strdata.substr(2,1) || "/" != strdata.substr(5,1)){
			elmt.value = '';
			elmt.focus();
			alert("Formato da data nao e valido. Formato correto: - dd/mm/aaaa.");
			return false;
		}
		dia = strdata.substr(0,2)
		mes = strdata.substr(3,2);
		ano = strdata.substr(6,4);
		//Verifica o dia
		if (isNaN(dia) || dia > 31 || dia < 1){
			elmt.value = '';
			elmt.focus();
			alert("Formato do dia nao e valido.");
			return false;
		}
		if (mes == 4 || mes == 6 || mes == 9 || mes == 11){
			if (dia == "31"){
				elmt.value = '';
				elmt.focus();
				alert("O mes informado nao possui 31 dias.");
				return false;
			}
		}
		if (mes == "02"){
			bissexto = ano % 4;
			if (bissexto == 0){
				if (dia > 29){
					elmt.value = '';
					elmt.focus();
					alert("O mes informado possui somente 29 dias.");
					return false;
				}
			}else{
				if (dia > 28){
					elmt.value = '';
					elmt.focus();
					alert("O mes informado possui somente 28 dias.");
					return false;
				}
			}
		}
	//Verifica o mes
		if (isNaN(mes) || mes > 12 || mes < 1){
			elmt.value = '';
			elmt.focus();
			alert("Formato do mes nao e valido.");
			return false;
		}
		//Verifica o ano
		if (isNaN(ano)){
			elmt.value = '';
			elmt.focus();
			alert("Formato do ano nao e valido.");
			return false;
		}
		
	 	return true;
			
	}
	
	function comparaEValidaDatas(data_inicial, data_final){
		//Verifica se a data inicial � maior que a data final
		var data_inicial = document.getElementById(data_inicial);
		var data_final   = document.getElementById(data_final);
		var set_codigo   = document.getElementById('set_codigo').value;
	
		if(set_codigo == 0){
			alert("O setor deve ser informado.");
			return false;
		}
		if (data_inicial.value.length == 0){
			data_inicial.focus();
			alert("A data inicial deve ser informada.");
			return false;
		}
		if (data_final.value.length == 0){
			data_final.focus();
			alert("A data final deve ser informada.");
			return false;
		}
		str_data_inicial = data_inicial.value;
		str_data_final   = data_final.value;
		dia_inicial      = data_inicial.value.substr(0,2);
		dia_final        = data_final.value.substr(0,2);
		mes_inicial      = data_inicial.value.substr(3,2);
		mes_final        = data_final.value.substr(3,2);
		ano_inicial      = data_inicial.value.substr(6,4);
		ano_final        = data_final.value.substr(6,4);
		if(ano_inicial > ano_final){
			data_final.focus();
			alert("A data inicial deve ser menor que a data final."); 
			return false;
		}else{
			if(ano_inicial == ano_final){
				if(mes_inicial > mes_final){
					data_final.focus();
					alert("A data inicial deve ser menor que a data final.");
					return false;
				}else{
					if(mes_inicial == mes_final){
						if(dia_inicial > dia_final){
							data_final.focus();
							alert("A data inicial deve ser menor que a data final.");
							return false;
						}
					}
				}
			}
		}
		aberturaMovimento.submit();
	}
</script>
<?php

//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>

include_once "authlib.inc.php";
verauth($id_login);

session_start();
require_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
require_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
cabecario();
$common = new commonClass();
echo $common->incJquery();
$form = new classForm();
$table = new tableClass();

$stmt = "SELECT uni_codigo 
		   FROM logon
		  WHERE id_login = $id_login";
$stmt = db_query($stmt);
$dados = pg_fetch_array($stmt);


//------------------------------------------------------------------>

//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
       reglog($id_login,"Entrando em PACIENTE");
//------------------------------------------------------------------>

echo $common->menuTab(array("Setor por Per&iacute;odo"));
echo $common->bodyTab('1');
if(empty($acao)) {
	$acao = 'add';
}
if ($acao == "add")
{
	if ($_POST['envia']){
		if($_POST['setp_codigo']==0){
			reglog($id_login,"Adicionando Registro em SETOR");
			$sql = "INSERT INTO setor_periodo( 
                            set_codigo,
                            setp_data_inicial,
                            setp_data_final,
                            usr_incl_codigo,
                            setp_inclusao_data) 
                     VALUES (
                            ".$_POST['set_codigo'].",
                            '".$_POST['setp_data_inicial']."',
                            '".$_POST['setp_data_final']."',
                            ".$_GET['id_login'].",
                            current_timestamp)";
			$sql = pg_query($sql) or die($sql.pg_last_error());;
			reglog($id_login,"Adicionando Setor $set_nome ");
		}else if ($_POST['setp_codigo']!=0){
			$sql = "UPDATE setor_periodo 
					   SET setp_data_inicial = '".$_POST['setp_data_inicial']."',
                           setp_data_final = '".$_POST['setp_data_final']."',
                           usr_alt_codigo = ".$_GET['id_login'].",
                           setp_alteracao_data = '".date('d/m/Y H:i:s')."'
                     WHERE setp_codigo = ".$setp_codigo;
			$sql = pg_query($sql);
			reglog($id_login,"Alterando Setor $set_nome ");
		}
	}
//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
reglog($id_login,"Entrando em SETOR PERIODO");
//------------------------------------------------------------------>
//------------------------------------------------------------------>
//-> Formulario de Edicao de Conteudo
//------------------------------------------------------------------>


reglog($id_login,"Formulario de EDICAO SETOR PERIODO");
//
//-> Formulario de edicao do cadastro SIMPLES

//
//-> Abaixo sao os botoes de voltar / cadastro simples / cadastro completo
//echo $common->commonButton("Voltar", "cadastros_materiais.php?id_login=$id_login", "voltar.png");
//echo "<a href=cadastros_materiais.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a><br>";
//
//-> Pegando as informcoes do banco pra mostrar no formulario
 
echo $form->openForm("", "POST", "aberturaMovimento");
	echo $form->hiddenForm("id_login", $id_login);
	echo $form->hiddenForm("setp_codigo", $setp_codigo);
	$selectSetor = "SELECT set_codigo,
						   set_nome 
					  FROM setor
					 WHERE set_estoque = 'S' "
						   .($dados[0]=="" ? "" : " AND uni_codigo = ".$dados[0]).
				    "GROUP BY set_nome, 
				    	   set_codigo 
				     ORDER BY set_nome";
						  
	echo $form->inputSelect("set_codigo", null, "Setor", $selectSetor, null, null, null, "style=\"width:200px;\"");
	echo $form->inputText("setp_data_inicial", null, "Data Inicial", 10, 10, "onkeypress=\"return Ajusta_Data(this, event);\" onChange=\"return validaData('setp_data_inicial');\"");
	echo $form->inputText("setp_data_final", null, "Data Final", 10, 10, "onkeypress=\"return Ajusta_Data(this, event);\" onChange=\"return validaData('setp_data_final');\"");
	echo $form->hiddenForm("envia", "e");
	$arrayBotoes = array($common->commonButton("Voltar", "cadastros_materiais.php?id_login=$id_login", "voltar.png"), $common->commonButton("Salvar", null, "salvar.gif", "onclick=\"return comparaEValidaDatas('setp_data_inicial', 'setp_data_final');\""), $common->commonButton("Limpar", null, "limpar.png", "onclick=\"cancel();return false;\""));
	echo "<br><br>".$table->openTable(null);
		echo $table->criaLinha($arrayBotoes);
	echo $table->closeTable();
	echo $form->closeForm();

?>
<table class="lista" width='100%'>
	<tr align="center">
		<th width="50%">SETOR</th>
		<th>DATA INICIAL</th>
		<th>DATA FINAL</th>
		<th>&nbsp;</th>
	</tr>
<?
       $sql = "SELECT set_nome, 
       				  set_codigo
              	 FROM setor
                WHERE set_estoque = 'S'
              	  AND set_codigo in (SELECT set_codigo 
              	  					   FROM setor_periodo) "
             .($dados[0]=="" ? "" : " AND uni_codigo = ".$dados[0]).
              " GROUP BY set_nome, 
              		  set_codigo
              	ORDER BY set_codigo";
       $sql = pg_query($sql);
       while ($cod = pg_fetch_array($sql))
       {
              $sql2 = "SELECT a.setp_codigo,
              				  b.set_nome,
              				  to_char(max(setp_data_final),'DD/MM/YYYY') as setp_data_final, 
              				  to_char(setp_data_inicial,'DD/MM/YYYY') as setp_data_inicial
                     	 FROM setor_periodo a, 
                     	 	  setor b 
                     	WHERE a.set_codigo = $cod[1]
                     	  AND setp_data_final = (SELECT MAX(setp_data_final) 
                     	  						   FROM setor_periodo a, 
                     	  						   		setor b 
                     	  						  WHERE a.set_codigo = $cod[1])
                     	  AND b.set_codigo = a.set_codigo
                     	  AND b.set_estoque = 'S'
                     	GROUP BY a.set_codigo,a.setp_codigo,b.set_nome,a.setp_data_inicial
                     	ORDER BY a.set_codigo";
              $sql2 = pg_query($sql2);
              $setor = pg_fetch_array($sql2);
              $setor['setp_codigo']=="" ? $setp_cod = "0" : $setp_cod = $setor['setp_codigo'];
              $setor['setp_data_final']=="" ? $setp_dt_fim = "Sem data" : $setp_dt_fim = $setor['setp_data_final'];
              $setor['setp_data_inicial']=="" ? $setp_dt_ini = "Sem data" : $setp_dt_ini = $setor['setp_data_inicial'];
              echo "<tr>
                            <td>".$cod['set_nome']."</td>
                            <td align=\"center\">".$setp_dt_ini."</td>
                            <td align=\"center\">".$setp_dt_fim."</td>
                            <td>".
              					$common->commonButton("Editar", null, "editar_on.png", "onclick=\"edita($cod[set_codigo],$setp_cod,'$setp_dt_ini','$setp_dt_fim');\"")
                                  ."
                            </td>
                     </tr>";
       }
?>
</table>
<?
}
echo $common->closeTab();
?>
