<html>
<head>
<?
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
	require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
	require_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
?>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>GPS - Software de Gestão P&uacute;blica</title>
<link href="estilo.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="ajax_motor.js"></script>
<script type="text/javascript">
	function dispensarMedicamentosReceita(rec_codigo, id_login){
		var myArray = new Array;
		$(".irec_qtde").each(function(){
			var dados = $(this).val().split("|");
			var irec_codigo = dados[0];
			var qtd = $(".irec_quantidade_"+dados[0]).val();
			myArray.push(irec_codigo+"|"+qtd);
		});
		
		$.ajax({
            url: "dispensacaoCodBarrasAtualizaReceita.php",
            type: "POST",
            data: {itens:myArray},
            success: function(txt){
                
            }
        });
		
		url = "dispensarMedicamentosReceita.php?id_login="+id_login+"&rec_codigo="+rec_codigo+"&irec_dados="+myArray;
		//exit;
		ajax_tudo(url,respostaDispensacao);
	}
	function respostaDispensacao(txt){
		resposta = txt.split('|');
		var rec_codigo = resposta[0];
		var alerta = 0;
		
		if (txt.slice(0, 4) != "erro"){
			if(txt.slice(0, 4) == "aler"){
				alerta = 1; // define se vai apresentar o alerta de que há reserva de envio
				rec_codigo = resposta[0].slice(4);
			}
			location.href="dispensacaoCodBarras.php?rec_codigo="+rec_codigo+"&acao=pronto&id_login="+resposta[1]+"&alerta="+alerta;
		}else{
			location.href="dispensacaoCodBarras.php?rec_codigo="+resposta[0].slice(4)+"&acao=erro&id_login="+resposta[1];
		}
	}

</script>
</head>

<body topmargin="0" leftmargin="0" <? echo ($acao=="" ? "onload='document.form.cod_receita.focus();'" : '')?>>
<div id="div">
<?
	$form = new classForm();
	$common = new commonClass();
	$tabela = new tableClass();
	echo $common->incJquery();
	if($acao=="") {
	$selectSetor = "SELECT l.cod_setor, s.*
						  FROM logon l
						  JOIN setor s
						    ON l.cod_setor = s.set_codigo
						   AND s.set_estoque = 'S'
						   AND s.set_farmacia = 'S'
						 WHERE l.id_login  =$id_login";
		$execSelectSetor = pg_query($selectSetor);
		$row = pg_fetch_array($execSelectSetor); 
		
		$set_codigo = $row['set_codigo'];
		if(pg_num_rows($execSelectSetor) == 0)
		{
			echo$common->modalMsg("ERRO", "Você não está localizado ou ligado a nenhum setor que permita dispensação. Escoha um setor que permita dispensa no login ou entre em contado com o administrador.","dispensa_medicamentos.php?acao=listar&id_login=$id_login");
		}
		$id_login = $_GET['id_login'];
		echo $common->menuTab(array("Leitor de C&oacute;digo de Barras"));
		
		echo $common->bodyTab('1');
			echo $form->openForm($PHP_SELF, "POST", "form");
				echo $form->hiddenForm("acao", "ok");
				echo $form->hiddenForm("id_login", $id_login);
				echo $form->inputText("cod_receita", null, "C&oacute;digo da receita", 20, null);
			echo $form->closeForm();
		echo $common->closeTab();

	} else if($acao=="ok") {
		$cod_receita = substr($cod_receita,0,11);
		
		$id_login = $_POST['id_login'];
		/*
		 * SELECIONANDO O SETOR EM QUE SERÁ REALIZADA A DISPENSAÇÃO
		 */
		$selectSetor = "SELECT l.cod_setor, s.*
						  FROM logon l
						  JOIN setor s
						    ON l.cod_setor = s.set_codigo
						   AND s.set_estoque = 'S'
						   AND s.set_farmacia = 'S'
						 WHERE l.id_login  =$id_login";
		$execSelectSetor = pg_query($selectSetor);
		$row = pg_fetch_array($execSelectSetor); 
		
		$set_codigo = $row['set_codigo'];
		if(pg_num_rows($execSelectSetor) == 0)
		{
			echo$common->modalMsg("ERRO", "Você não está localizado ou ligado a nenhum setor que permita dispensação. Escoha um setor que permita dispensa no login ou entre em contado com o administrador.","dispensa_medicamentos.php?acao=listar&id_login=$id_login");
		}
		/*
		 * 
		 */
		echo $common->menuTab(array("Listando itens da receita"));
		
		$cod_receita = substr($_POST['cod_receita'],0,11);
		
		$rec_codigo = preg_replace("/^0+/","",$cod_receita);
		
		$seleciona = "SELECT p.pro_nome,
							 trunc(ir.irec_qtde_pendente),
							 ir.irec_codigo,
							 coalesce((SELECT sum(sal_qtde)
										 FROM saldo
									    WHERE pro_codigo = p.pro_codigo
										  AND set_codigo = $set_codigo
										GROUP BY pro_codigo), 0) as qtde
						FROM receita r 
						JOIN itemreceita ir 
						  ON r.rec_codigo = ir.rec_codigo 
						JOIN produto p
						  ON p.pro_codigo = ir.pro_codigo
					   WHERE r.rec_codigo = $rec_codigo
					     AND ir.irec_qtde_pendente > 0";
		$exec = pg_query($seleciona);
		
		echo $common->bodyTab('1');
		echo $tabela->openTable('lista',"100%",null, 0);
		echo $tabela->criaLinha(array('Produto','Quantidade Solicitada','Quantidade á Dispensar','Estoque'),null,null,'S');
		while ($linha = pg_fetch_row($exec)){
			//echo $tabela->criaLinha($linha);
			//$form->inputText('irec_quantidade',$linha[1])
			echo $tabela->criaLinha(Array($linha[0],$linha[1],"<input type='text' id='irec_quantidade' name='irec_quantidade' value='$linha[1]' class='inputForm irec_quantidade_$linha[2]' >",$linha[3],"<input type=hidden id=\"irec_qtde[]\" class=\"irec_qtde\" value=\"$linha[2]|$linha[1]\">"));
		}
		echo $tabela->closeTable();
		echo $form->submitButton("Enviar", $_SESSION[linkroot].$_SESSION[comum]."imgs/enviar_on.jpg", "onClick=\"dispensarMedicamentosReceita($rec_codigo, $id_login);\"");
		echo $common->closeTab();
		
	} else if($acao == "pronto"){
		$rec_codigo = $_GET['rec_codigo'];
		$alerta = $_GET['alerta'];
		$sql = "SELECT p.pro_nome,
				       trunc(im.ite_quantidade),
				       im.ite_lote,
				       to_char(im.ite_validade, 'dd/mm/yyyy')
				  FROM movimento m
				  JOIN itens_movimento im
				    ON m.mov_codigo = im.mov_codigo
				  JOIN produto p
				    ON im.pro_codigo = p.pro_codigo
				 WHERE m.mov_num_receita = $rec_codigo";
		
		$executa = pg_query($sql);
		
			
		$table = $tabela->openTable('lista',"100%",null, 0);
		$table .= $tabela->criaLinha(array('Produto','Quantidade Dispensada', 'Lote', 'Validade'),null,null,'S');
		while ($linha = pg_fetch_row($executa)){
			$table .= $tabela->criaLinha($linha);
		}
		$table .= $tabela->closeTable();
		
		echo $common->openModal("Medicamentos Dispensados com Sucesso", 700, "OK", "$PHP_SELF?id_login=$id_login");
		echo $table;
		echo $common->closeModal();
		if($alerta == 1)
			echo $common->modalMsg("ALERTA","Existem pendências de envio de materiais!");
			
	}else if($acao == "erro"){
		$id_login = $_GET['id_login'];
		$rec_codigo = $_GET['rec_codigo'];
		/*
		 * SELECIONANDO O SETOR EM QUE SERÁ REALIZADA A DISPENSAÇÃO
		 */
		$selectSetor = "SELECT cod_setor
						  FROM usuarios
						 WHERE usr_codigo = $id_login";
		$execSelectSetor = pg_query($selectSetor);
		$row = pg_fetch_array($execSelectSetor); 
		
		$set_codigo = $row['set_codigo'];
		/*
		 * 
		 */
		$sql = "SELECT *
				  FROM (SELECT p.pro_nome,
							   (trunc(ir.irec_quantidade)) AS quantidadeNaoDispensada,
							   ir.irec_qtde_pendente,
							   coalesce((SELECT sum(sal_qtde)
										   FROM saldo
										  WHERE pro_codigo = p.pro_codigo
											AND set_codigo = $set_codigo
										  GROUP BY pro_codigo), 0) as qtde
						  FROM receita r 
						  JOIN itemreceita ir 
							ON r.rec_codigo = ir.rec_codigo 
						  JOIN produto p
							ON p.pro_codigo = ir.pro_codigo
						 WHERE r.rec_codigo = $rec_codigo) AS x
				 WHERE x.irec_qtde_pendente > 0";

		
		$executa = pg_query($sql);
		
		$table = $tabela->openTable('lista',"100%",null, 0);
		$table .= $tabela->criaLinha(array('Produto','Quantidade Solicitada', 'Quantidade Pendente', 'Estoque'),null,null,'S');
		while ($linha = pg_fetch_row($executa)){
			$table .= $tabela->criaLinha($linha);
		}
		$table .= $tabela->closeTable();
		
		echo $common->openModal("Medicamentos NÃO Dispensados", 700, "OK", "$PHP_SELF?id_login=$id_login");
			echo $table;
		echo $common->closeModal();
		
		$sql = "SELECT p.pro_nome,
				       trunc(im.ite_quantidade),
				       im.ite_lote,
				       to_char(im.ite_validade, 'dd/mm/yyyy')
				  FROM movimento m
				  JOIN itens_movimento im
				    ON m.mov_codigo = im.mov_codigo
				  JOIN produto p
				    ON im.pro_codigo = p.pro_codigo
				 WHERE m.mov_num_receita = $rec_codigo";
		$executa = pg_query($sql);
		
		$table = $tabela->openTable('lista',"100%",null, 0);
		$table .= $tabela->criaLinha(array('Produto','Quantidade Dispensada', 'Lote', 'Validade'),null,null,'S');
		while ($linha = pg_fetch_row($executa)){
			//$table .= $tabela->criaLinha($linha);
			//$table .= $tabela->criaLinha(Array($linha[0],$form->inputText('ite_quantidade',$linha[1]),$linha[2],$linha[3]));
		}
		$table .= $tabela->closeTable();
		
		echo $common->openModal("Medicamentos Dispensados com Sucesso", 700, "OK");
			echo $table;
		echo $common->closeModal();
		
		echo $common->modalMsg("ERRO", "A quantidade dispensada de um ou mais medicamentos é inferior à quantidade solicitada por não possuir quantidade suficiente em estoque.");
	}

?>
</div>
</body>
</html>