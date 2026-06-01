<?php

if(isset($_POST['action'])){
	
	if($_POST['action'] == "add"){
		$esp_codigo = $_POST['especialidade'];
		$enc_descricao = $_POST['observacao'];
		$ate_codigo = $_POST['ate_codigo'];
		$usr_codigo = 280; 
		$med_codigo = 280; //$_POST['usr_codigo']; Atributo redundante, afinal já existe atendimento.med_codigo
		
		pg_query("INSERT INTO encaminhamento (
							  esp_codigo,
							  enc_descricao,
							  ate_codigo,
							  usr_codigo,
							  med_codigo,
							  enc_data
							  ) VALUES (
							  $esp_codigo,
							  '$enc_descricao',
							  $ate_codigo,
							  $usr_codigo,
							  $med_codigo,
							  NOW()
							  )") or die (pg_last_error());
							  
		$msg = "Adicionado com sucesso!";		
		
	} elseif($_POST['action'] == "excluir"){
		
		require_once '../global.php';
		
		$enc_codigo = $_POST['enc_codigo'];
		
		$result = pg_query("DELETE FROM encaminhamento WHERE enc_codigo=$enc_codigo") or die(pg_last_error());
		die("1");
	}
	
}

?><script type="text/javascript">

	$(function(){

		$("a.excluir").click(function(e){
			e.preventDefault();
			var cod = $(this).attr("rel");
			
			if(confirm("Deseja realmente exluir este item?")){
				$.ajax({
					url: "encaminhamento.php",
					type:"POST",
					data: {
						action: "excluir",
						enc_codigo: cod
					},
					success: function(r){
						window.location.href = window.location.href;
					}
				});
			}

			return false;
		});

	});

</script><?php 
	
// lista de encaminhamentos deste agendamento
$ate_codigo = $_GET['ate_codigo'];
if(empty($ate_codigo))
	die("Código do atendimento necessário.");
	
$query = pg_query("SELECT enc.enc_codigo, 
						  esp_nome, 
						  enc_descricao 
					 FROM encaminhamento AS enc
			 		 JOIN especialidade AS esp 
			 		   ON esp.esp_codigo=enc.esp_codigo
			 	    WHERE ate_codigo=$ate_codigo;");

$sqlSelect = "SELECT * 
                FROM especialidade
               WHERE esp_encaminhamento=true 
               ORDER BY esp_nome";

$common = new commonClass();
$form = new classForm();

echo $common->menuTab(Array("Histórico de Encaminhamentos"));
echo $common->bodyTab('1');

if( isset($msg))
	echo "<h3>$msg</h3>";

echo $form->openForm("", "POST","form");
echo $form->hiddenForm("action", "add");
echo $form->hiddenForm("ate_codigo", $_GET['ate_codigo']);
echo $form->hiddenForm("usr_codigo", $_GET['usr_codigo']);

$options = array(
	"nome" => "especialidade",
	"caption" => "Especialidade",
	"sql" => $sqlSelect,
 	"disabledFirst" => TRUE
);
echo $form->inputSelect($options);
echo $form->textArea("observacao", false, "Observações");
echo $common->commonButton("Salvar",NULL,NULL, "onclick='document.form.submit()'");
echo $form->closeForm();

$table = new tableClass();
echo $table->openTable("lista", "100%");
echo $table->criaLinha(array("Num.", "Especialidade","Observações","Opções"), array(100,"40%","60%",100), null, "S");

$layout = "<img src=\"".LINKCOMUM."/imgs/printer.png\" alt=\"Imprimir\" title=\"Imprimir\" style=\"cursor:pointer\" onclick=\"window.open('../print_encaminhamento2.php?enc_codigo=%s',null,'height=545,width=605,status=yes,toolbar=no,menubar=no,location=no');\" />&nbsp;";
$layout .= "<a href=\"#\" rel=\"%s\" class=\"excluir\"><img src=\"".LINKCOMUM."/imgs/cross.png\" alt=\"Deletar\" title=\"Deletar\" /></a>";

while($r = pg_fetch_array($query)){
	echo $table->criaLinha( array(++$_i,$r['esp_nome']."</span>",($r['enc_descricao']?$r['enc_descricao']:"<em>Sem observações</em>"), sprintf($layout,$r['enc_codigo'],$r['enc_codigo']) ) );
}
