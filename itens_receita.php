<?php
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>
	
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
	require_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
	verauth($id_login);
	
	cabecario();
	$common = new commonClass();
	echo $common->incJquery();
//------------------------------------------------------------------>
?>
<script src="../ajax_motor.js"></script>
<script>
function chama_ver(login)
{
    med = document.getElementById("pro_codigo").value;
    url = "ver_estoque.php?id_login="+login+"&med="+med;
    window.open(url,null,"height=400,width=650,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");
}
function verificaEstoque(pro_codigo, id_login){
	url = "../verificarEstoque.php?id_login="+id_login+"&pro_codigo="+pro_codigo;
	ajax_tudo(url, responde);
}
function responde(txt){
	document.getElementById('estoque').innerHTML = "<b>Estoque: </b>"+txt;
}
</script>
<?
$select = "SELECT ate_codigo
			 FROM atendimento 
			WHERE usu_codigo = $usu_codigo 
			  AND age_codigo = $age_codigo";
$query = pg_query($select);
$resultado= pg_fetch_array($query);
$ate_codigo = $resultado['ate_codigo'];

$sql = pg_query("select to_char(rec_data, 'dd/mm/yyyy'), rec_tipo from receita where ate_codigo = $ate_codigo");

$ReceitasAnt = pg_num_rows($sql);
if ($ReceitasAnt == 0) {
	$row = pg_fetch_array($sql);
}
echo $common->menuTab(array('Medicamentos'));
echo $common->bodyTab('1');
if ($acao=="") {
//
//-> Botoes

     echo "<a href='prontuario.php?acao=choice_receita&pagina=7&id_login=$id_login&ate_codigo=$ate_codigo&age_codigo=$age_codigo&med_codigo=$med_codigo&usu_codigo=$usu_codigo'>
     			<img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg border=0>
     	   </a><br>";

//
//-> Listando
     $table = new tableClass();
     
     echo $table->openTable("lista", 700);
     	echo $table->criaLinha(array("Data", "Tipo de Receita", "Situa&ccedil;&atilde;o", "2&ordf; Via"),null, null, "S");
		$sqlReceitas = " SELECT to_char(rec_data, 'dd/mm/yyyy') AS rec_data, 
								CASE rec_tipo 
								  WHEN 'posto' THEN '<font color=orange><b>Medicamento de Posto</b></font>' 
								  WHEN 'controlados' THEN '<font color=red><b>Medicamentos Controlados</b></font>' 
								  WHEN 'externo' THEN '<font color=blue><b>Medicamentos Externos</b></font>' 
								END as tipo, 
								CASE rec_finalizada 
								  WHEN 'S' THEN '<font color=green><b>Finalizada</b></font>' 
								  WHEN 'N' THEN '<font color=red><b>Não Finalizada</b></font>' 
								END as finalizada, 
								CASE rec_finalizada 
								  WHEN 'S' THEN '<a href=# OnClick=\"window.open(\'../print_receita_2via.php?age_codigo=$age_codigo&id_login=$id_login&ate_codigo=$ate_codigo&tp_action=$tp_action&receita='||rec_codigo||'\',null,\'height=600,width=550,status=yes,toolbar=no,menubar=no,location=no\');\"><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/print_on.jpg alt=\"imprimir\" border=0></a>' 
								  WHEN 'N' THEN '&nbsp;' 
								END as imprimir
						   FROM receita
		                  WHERE ate_codigo = $ate_codigo";
     	$executa = pg_query($sqlReceitas);
     	while ($dados = pg_fetch_row($executa)){
     		echo $table->criaLinha($dados, null, null, "N");
     	}
     echo $table->closeTable();
}

if ($acao=="insert_receita"){
	$sqlQuery = "SELECT a.age_codigo,
		     			a.ate_codigo, 
		     			u.usu_nome, 
		     			to_char(a.ate_data, 'dd/mm/yyyy') as ate_data, 
		     			m.med_nome
				   FROM atendimento a, 
				   		usuario u, 
				   		medico m
				  WHERE a.usu_codigo = u.usu_codigo
				    AND a.med_codigo = m.med_codigo
				    AND a.ate_codigo = '$ate_codigo'";
    $sql =  pg_query($sqlQuery);
	if (pg_num_rows($sql) == 0)  {
		echo "<SCRIPT LANGUAGE=\"JavaScript\">
				alert (\"Paciente Sem Atendimentos\")
			  </SCRIPT>";
		echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=prontuario.php?pagina=7&id_login=$id_login&ate_codigo=$ate_codigo2&age_codigo=$age_codigo&med_codigo=$med_codigo\">";
		exit;
	}
     
     $row = pg_fetch_array($sql);
     $age_codigo = $row['age_codigo'];
     echo $form->inputText("usu_nome", $row['usu_nome'], "Paciente", 70, null, null, "text", "S");

     echo $form->openForm("prontuario.php", "GET");
     	echo $form->hiddenForm("acao", "form_insert");
     	echo $form->hiddenForm("pagina", 7);
     	echo $form->hiddenForm("ate_codigo", $ate_codigo);
     	echo $form->hiddenForm("age_codigo", $age_codigo);
     	echo $form->hiddenForm("id_login", $id_login);
     	//echo $form->hiddenForm("tp_action", "posto");
     	//echo $form->hiddenForm("rec_codigo", $receita);
     	echo $form->hiddenForm("ate_codigo", $ate_codigo);
     	echo $form->hiddenForm("usu_codigo", $usu_codigo);
		$arrayValores = array("posto" => "Medicamentos de Posto", "controlados" => "Medicamentos Controlados", "externo" => "Medicamentos Externos");
		echo $form->inputSelect("tp_action", $arrayValores, "Tipo de Receita", null, null, null, "posto", "style='width:160px;'");
		$selectProduto = "SELECT pro_codigo,
     							 pro_nome 
     						FROM produto 
     					   WHERE psico_codigo is null 
     					   ORDER BY pro_nome";
     	echo $form->inputSelect("pro_codigo", null, "Produto", $selectProduto, "onChange=\"verificaEstoque(this.value, $id_login);\"", null, null, "style=\"width:365px;\"");
     	echo "<div id='estoque' style='clear:right;margin-left:570px;padding-top:3px;'></div>";
     	echo $form->textArea("irec_recomendacao",null,"Recomenda&ccedil;&atilde;o M&eacute;dica", null, "style=\"width:365px;\"");
     	echo $form->inputText("irec_quantidade", null, "Quantidade", 5, null, "onchange='calcula();'");
     	echo $form->inputText("rec_validade", null, "Validade", 10, 10);
     
//
//-> Botoes
	echo "<table cellpadding=5 cellspacing=5 border=0>
  			<tr>
  				<td align=right width=190>
	                <input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/add_on.gif align=absmiddle style=\"border:0px;\">
	            </td>
	            <td>
	                <a href='#' OnClick='window.open(\"../print_receita.php?usu_codigo=$usu_codigo&age_codigo=$age_codigo&id_login=$id_login&ate_codigo=$ate_codigo&tp_action=$tp_action&receita=$receita\",null,\"height=600,width=550,status=yes,toolbar=no,menubar=no,location=no\");location.replace(\"prontuario.php?usu_codigo=$usu_codigo&pagina=7&id_login=$id_login&age_codigo=$age_codigo&ate_codigo=$ate_codigo\");'>
	                    <img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/finalizar_on.jpg border=0 align=absmiddle>
	                </a>
	             </td>
	     	</tr>
		</table>";
	echo $form->closeForm();

	$selectReceita = "SELECT count(*) as conta, 
							 rec_codigo 
						FROM receita
					   WHERE ate_codigo = $ate_codigo 
					     and rec_tipo = '$tp_action' 
						 and rec_finalizada = 'N' 
					   group by rec_codigo 
					   order by rec_codigo limit 1";
	$sqlconta = pg_query($selectReceita) ;
	$rowconta=pg_fetch_array($sqlconta);
	if ($rowconta['conta'] == 0 ) {
		$selecionaReceita = "SELECT nextval('seq_rec_codigo'::text) AS novo_codigo";
		$sqlreceita = pg_query($selecionaReceita) or die($selecionaReceita);       
		$rowreceita = pg_fetch_array($sqlreceita);
		$receita = $rowreceita['novo_codigo'];
	}    
	else {
		$receita = $rowconta['rec_codigo'];
	}
//
//-> Listando
	$tabela = new tableClass();
	echo $tabela->openTable("lista", "98%", null);
		echo $tabela->criaLinha(array("Produto", "Quantidade", "Recomenda&ccedil;&atilde;o", "&nbsp;"),null, null, "S");

		$selecionaItemReceita = "SELECT p.pro_nome,
										ir.irec_quantidade,  
										ir.irec_recomendacao, 
										'<a href=prontuario.php?usu_codigo=$usu_codigo&age_codigo=$age_codigo&pagina=7&ate_codigo=$ate_codigo&id_login=$id_login&acao=del&irec_codigo='||irec_codigo||'&tp_action=$tp_action&rec_codigo='||rec_codigo||'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.jpg border=0></a>' as link
                  				   FROM itemreceita ir, 
                  				   		produto p, 
                  				   		receita r
                  				  WHERE ir.pro_codigo = p.pro_codigo
		  							AND ir.rec_codigo = r.rec_codigo
		  							AND r.ate_codigo = $ate_codigo
		  							AND r.rec_codigo = $receita
	          						AND r.rec_finalizada = 'N'
                  				  ORDER BY irec_codigo DESC";
		$sql = pg_query($selecionaItemReceita);

		while($row=pg_fetch_row($sql)) {
			$intquantidade = formata_valor0($row['irec_quantidade']);
			echo $tabela->criaLinha($row);
    	}
     echo $tabela->closeTable();
	
	$form = new classForm();
	echo $form->openForm("prontuario.php", "GET");
		//?pagina=7&id_login=$id_login&ate_codigo=$ate_codigo&age_codigo=$age_codigo&med_codigo=$med_codigo&usu_codigo=$usu_codigo
		echo $form->hiddenForm("pagina", 7);
		echo $form->hiddenForm("age_codigo", $age_codigo);
		echo $form->hiddenForm("med_codigo", $med_codigo);
		echo $form->hiddenForm("usu_codigo", $usu_codigo);
		echo $form->hiddenForm("ate_codigo", $ate_codigo);
		echo $form->hiddenForm("id_login", $id_login);
		$arrayValores = array("posto"=>"Medicamentos de Posto", "controlados"=>"Medicamentos Controlados", "externo"=>"Medicamentos Externos");
		echo $form->inputSelect("acao", $arrayValores, "Tipo de Receita", null, null, null, "posto", "style='width:160px;'");
		echo "<input type=submit value='Continuar >>' class=box>";
	echo $form->closeForm();
	
}

if($acao=="choice_receita") {
	$form = new classForm();
	echo $form->openForm("prontuario.php", "GET");
	//?pagina=7&id_login=$id_login&ate_codigo=$ate_codigo&age_codigo=$age_codigo&med_codigo=$med_codigo&usu_codigo=$usu_codigo
	echo $form->hiddenForm("pagina", 7);
	echo $form->hiddenForm("age_codigo", $age_codigo);
	echo $form->hiddenForm("med_codigo", $med_codigo);
	echo $form->hiddenForm("usu_codigo", $usu_codigo);
	echo $form->hiddenForm("ate_codigo", $ate_codigo);
	echo $form->hiddenForm("id_login", $id_login);
	$arrayValores = array("posto"=>"Medicamentos de Posto", "controlados"=>"Medicamentos Controlados", "externo"=>"Medicamentos Externos");
    echo $form->inputSelect("acao", $arrayValores, "Tipo de Receita", null, null, null, "posto", "style='width:160px;'");
//    echo $common->commonButton("Continuar", "prontuario.php?pagina=7&age_codigo=$age_codigo&med_codigo=$med_codigo&usu_codigo=$usu_codigo&ate_codigo=$ate_codigo&id_login=$id_login");
	echo "<input type=submit value='Continuar >>' class=box>";
	echo $form->closeForm();

}



if($acao=="externo") {
     $sql =  pg_query("SELECT age_codigo,
     						  ate_codigo, 
     						  usu_nome, 
     						  to_char(ate_data, 'dd/mm/yyyy') as ate_data, 
     						  med_nome
                         FROM atendimento, 
                         	  usuario, 
                         	  medico
                        WHERE atendimento.usu_codigo = usuario.usu_codigo
                          AND atendimento.med_codigo = medico.med_codigo
                          AND ate_codigo='$ate_codigo'");
				
     if (pg_num_rows($sql) == 0)  {
         echo "<SCRIPT LANGUAGE=\"JavaScript\">
                    alert (\"Paciente Sem Atendimento\")
					
               </SCRIPT>";
         echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=prontuario.php?pagina=7&id_login=$id_login&age_codigo=$age_codigo\">";    exit();
     }
     $row=pg_fetch_array($sql);
     $age_codigo=$row[age_codigo];
  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	     <tr>
	      <td>
	       <fieldset>
	        <legend>Atendimento</legend>
	         <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
              <tr> 
		       <td width=50>Paciente</td>
		       <td><input type=text readonly name=usu_nome class=box01 size=70 value='$row[usu_nome]' class=box></td>
<!--              </tr>
                  <tr>
		       <td width=70>&nbsp;</td>
		       <td width=20> <input type=text readonly name=med_nome size=70 value='$row[med_nome]' class=box></td>
		       <td colspan=2><input type=text readonly name=ate_data size=11 value='$row[ate_data]' class=box></td>
              </tr>   -->
	         </table>
	       </fieldset>
	      </td>
	     </tr>
        </table><br>";

//
//-> Botoes
  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
         <tr>
	      <td>
	       <fieldset>
	        <legend>Digitacao dos Itens da Receita</legend>
	         <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	          <form name=inclui_item method=post action=$PHP_SELF>
		       <input type=hidden name=acao value=form_insert>
		       <input type=hidden name=ate_codigo value=$ate_codigo>
		       <input type=hidden name=tp_action value=externo>
		       <input type=hidden name=id_login value=$id_login>";
  $sqlconta = pg_query("select count(*) as conta, rec_codigo from receita
                          where ate_codigo = $ate_codigo and rec_tipo = 'externo' and rec_finalizada = 'N' group by rec_codigo order by rec_codigo limit 1") ;
  $rowconta=pg_fetch_array($sqlconta)                       ;
    if ($rowconta['conta'] == 0 ) {
        $sqlreceita = pg_query("select nextval('seq_rec_codigo'::text) as novo_codigo");       
        $rowreceita = pg_fetch_array($sqlreceita);
        $receita = $rowreceita['novo_codigo'];
    }    
    else {
        $receita = $rowconta['rec_codigo'];
    }
    echo"      <input type=hidden name=rec_codigo value=$receita>
               <input type=hidden name=ate_codigo value=$ate_codigo>
	     <tr>
		  <td width=20 align=right>Produto:</td>
		  <td colspan=4><input type=text name=desc_produto class=box> </td>";
	       echo "<tr>
		    <td width=20 valign=top>&nbsp;</td>
		    <td colspan=4>Recomenda&ccedil;&atilde;o M&eacute;dica:<br>
		     <textarea name=irec_recomendacao cols=40 rows=4 class=box></textarea>

	   </td>
	        </td>
	      <tr>
     		<td width=20 align=right>Quantidade:</td>
    		<td><input type=text name=irec_quantidade class=box size=5 onchange='calcula()'></td>
            <td width=20 align=right>V&aacute;lido at&eacute;:</td>
    		<td><input type=text name=rec_validade class=box size=10></td>
            </tr>
            <tr>
	       <td width=95>&nbsp;</td>
	       <td width=95 colspan='3'>
                <input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/add_on.gif>&nbsp;
                    <a href='#' OnClick='window.open(\"print_receita.php?age_codigo=$age_codigo&id_login=$id_login&ate_codigo=$ate_codigo&tp_action=externo&receita=$receita\",null,\"height=600,width=550,status=yes,toolbar=no,menubar=no,location=no\");location.replace(\"prontuario.php?pagina=7&?id_login=$id_login&age_codigo=$age_codigo&ate_codigo=$ate_codigo\");'>
                        <img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/finalizar_on.jpg border=0>
                    </a>
             </td>
	      </tr></form>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br>";
if ($rowconta != 0)
    echo "<script>
            document.inclui_item.rec_validade.disabled = true;
            document.inclui_item.rec_validade.style.background='silver';
        </script>";
//-> Listando

  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Listando Itens Cadastrados para a Receita</legend>
	     <table width=100% align=center class='lista' border='1'>
	      <tr>
			<th width=180>Produto</th>
			<th width=40>Quantidade</th>
			<th>Recomendação</th>
			<th>&nbsp;</th>
	     </tr>";

   $sql = pg_query("select desc_produto,irec_recomendacao,irec_quantidade,irec_codigo
	   from itemreceita
           ,receita
           where itemreceita.rec_codigo = receita.rec_codigo 
                 and receita.ate_codigo = $ate_codigo
 	         and  receita.rec_codigo = $receita
	         and  receita.rec_finalizada = 'N'");

     while($row=pg_fetch_array($sql)) {
       $intquantidade = formata_valor0($row['irec_quantidade']);
       echo "<tr>
	       <td width=180>$row[desc_produto]</td>
	       <td align=center>$intquantidade</td>
	       <td>$row[irec_recomendacao]</td>
	       <td width=60><a href=$PHP_SELF?ate_codigo=$ate_codigo&id_login=$id_login&acao=del&irec_codigo=$row[irec_codigo]&tp_action=$acao&rec_codigo=$rec_codigo><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.jpg border=0></a></td>
	     </tr>";
     }
	echo "
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table>";

}

if ($acao=='posto') {
	$select1 = "SELECT ate_codigo 
				  FROM atendimento 
				 WHERE usu_codigo = $usu_codigo 
				   AND age_codigo = $age_codigo";
	$query1 = pg_query($select1);
	$resultado1= pg_fetch_array($query1);
	$ate_codigo2 = $resultado1['ate_codigo'];
	
	$sqlQuery = "SELECT a.age_codigo,
		     			a.ate_codigo, 
		     			u.usu_nome, 
		     			to_char(a.ate_data, 'dd/mm/yyyy') as ate_data, 
		     			m.med_nome
				   FROM atendimento a, 
				   		usuario u, 
				   		medico m
				  WHERE a.usu_codigo = u.usu_codigo
				    AND a.med_codigo = m.med_codigo
				    AND a.ate_codigo = '$ate_codigo2'";
    $sql =  pg_query($sqlQuery);

	$selectReceita = "SELECT count(*) as conta, 
							 rec_codigo 
						FROM receita
					   WHERE ate_codigo = $ate_codigo 
					     and rec_tipo = 'posto' 
						 and rec_finalizada = 'N' 
					   group by rec_codigo 
					   order by rec_codigo limit 1";
	$sqlconta = pg_query($selectReceita) ;
	$rowconta=pg_fetch_array($sqlconta);
	if ($rowconta['conta'] == 0 ) {
		$selecionaReceita = "SELECT nextval('seq_rec_codigo'::text) AS novo_codigo";
		$sqlreceita = pg_query($selecionaReceita) or die($selecionaReceita);       
		$rowreceita = pg_fetch_array($sqlreceita);
		$receita = $rowreceita['novo_codigo'];
	}    
	else {
		$receita = $rowconta['rec_codigo'];
	}
     
     if (pg_num_rows($sql) == 0)  {
         echo "<SCRIPT LANGUAGE=\"JavaScript\">
                    alert (\"Paciente Sem Atendimentos\")
               </SCRIPT>";
         echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=prontuario.php?pagina=7&id_login=$id_login&ate_codigo=$ate_codigo2&age_codigo=$age_codigo&med_codigo=$med_codigo\">";    exit();
     }
     $row = pg_fetch_array($sql);
     $age_codigo=$row[age_codigo];
     echo $form->inputText("usu_nome", $row['usu_nome'], "Paciente",70,null, null, "text", "S");

     echo $form->openForm("prontuario.php", "GET");
     	echo $form->hiddenForm("acao", "form_insert");
     	echo $form->hiddenForm("pagina", 7);
     	echo $form->hiddenForm("ate_codigo", $ate_codigo);
     	echo $form->hiddenForm("age_codigo", $age_codigo);
     	echo $form->hiddenForm("id_login", $id_login);
     	echo $form->hiddenForm("tp_action", "posto");
     	echo $form->hiddenForm("rec_codigo", $receita);
     	echo $form->hiddenForm("ate_codigo", $ate_codigo);
     	echo $form->hiddenForm("usu_codigo", $usu_codigo);
     	$selectProduto = "SELECT pro_codigo,
     							 pro_nome 
     						FROM produto 
     					   WHERE psico_codigo is null 
     					   ORDER BY pro_nome";
     	echo $form->inputSelect("pro_codigo", null, "Produto", $selectProduto, "onChange=\"verificaEstoque(this.value, $id_login);\"", null, null, "style=\"width:365px;\"");
     	echo "<div id='estoque' style='clear:right;margin-left:570px;padding-top:3px;'></div>";
     	echo $form->textArea("irec_recomendacao",null,"Recomenda&ccedil;&atilde;o M&eacute;dica", null, "style=\"width:365px;\"");
     	echo $form->inputText("irec_quantidade", null, "Quantidade", 5, null, "onchange='calcula();'");
     	echo $form->inputText("rec_validade", null, "Validade", 10, 10);
     

//
//-> Botoes
  echo "<table cellpadding=5 cellspacing=5 border=0>
  			<tr>
  				<td align=right width=190>
	                <input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/add_on.gif align=absmiddle style=\"border:0px;\">
	            </td>
	            <td>
	                <a href='#' OnClick='window.open(\"../print_receita.php?usu_codigo=$usu_codigo&age_codigo=$age_codigo&id_login=$id_login&ate_codigo=$ate_codigo&tp_action=posto&receita=$receita\",null,\"height=600,width=550,status=yes,toolbar=no,menubar=no,location=no\");location.replace(\"prontuario.php?usu_codigo=$usu_codigo&pagina=7&id_login=$id_login&age_codigo=$age_codigo&ate_codigo=$ate_codigo\");'>
	                    <img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/finalizar_on.jpg border=0 align=absmiddle>
	                </a>
	             </td>
	     	</tr>
		</table>";
  echo $form->closeForm();
if ($rowconta != 0)
    echo "<script>
            document.inclui_item.rec_validade.disabled = true;
            document.inclui_item.rec_validade.style.background='silver';
        </script>";

//
//-> Listando
	$tabela = new tableClass();
	echo $tabela->openTable("lista", "98%", null);
		echo $tabela->criaLinha(array("Produto", "Quantidade", "Recomenda&ccedil;&atilde;o", "&nbsp;"),null, null, "S");

		$selecionaItemReceita = "SELECT p.pro_nome,
										ir.irec_quantidade,  
										ir.irec_recomendacao, 
										'<a href=prontuario.php?usu_codigo=$usu_codigo&age_codigo=$age_codigo&pagina=7&ate_codigo=$ate_codigo&id_login=$id_login&acao=del&irec_codigo='||irec_codigo||'&tp_action=$acao&rec_codigo=$rec_codigo><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.jpg border=0></a>' as link
                  				   FROM itemreceita ir, 
                  				   		produto p, 
                  				   		receita r
                  				  WHERE ir.pro_codigo = p.pro_codigo
		  							AND ir.rec_codigo = r.rec_codigo
		  							AND r.ate_codigo = $ate_codigo
		  							AND r.rec_codigo = $receita
	          						AND r.rec_finalizada = 'N'
                  				  ORDER BY irec_codigo DESC";
		$sql = pg_query($selecionaItemReceita);

		while($row=pg_fetch_row($sql)) {
			$intquantidade = formata_valor0($row['irec_quantidade']);
			echo $tabela->criaLinha($row);
    	}
     echo $tabela->closeTable();
}


 if ($acao=='controlados') {
     $sql =  pg_query("select age_codigo,ate_codigo, usu_nome, to_char(ate_data, 'dd/mm/yyyy') as ate_data, med_nome
                         from atendimento, usuario, medico
                        where atendimento.usu_codigo = usuario.usu_codigo
                          and atendimento.med_codigo = medico.med_codigo
                          and ate_codigo='$ate_codigo'");
     if (pg_num_rows($sql) == 0)  {
         echo "<SCRIPT LANGUAGE=\"JavaScript\">
                    alert (\"Paciente Sem Atendimento\")
               </SCRIPT>";
         echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"0;URL=recepcionado_medico.php?id_login=$id_login&age_codigo=$age_codigo\">";    exit();
     }
     $row=pg_fetch_array($sql);
     $age_codigo=$row[age_codigo];
  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	     <tr>
	      <td>
	       <fieldset>
	        <legend>Atendimento</legend>
	         <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
              <tr> 
		       <td width=50>Paciente</td>
		       <td><input type=text readonly name=usu_nome class=box01 size=70 value='$row[usu_nome]' class=box></td>
<!--              </tr>
                  <tr>
		       <td width=70>&nbsp;</td>
		       <td width=20> <input type=text readonly name=med_nome size=70 value='$row[med_nome]' class=box></td>
		       <td colspan=2><input type=text readonly name=ate_data size=11 value='$row[ate_data]' class=box></td>
              </tr>   -->
	         </table>
	       </fieldset>
	      </td>
	     </tr>
        </table><br>";

//
//-> Botoes
  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
         <tr>
	      <td>
	       <fieldset>
	        <legend>Digitacao dos Itens da Receita</legend>
	         <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	          <form name=inclui_item method=post action=$PHP_SELF>
		       <input type=hidden name=acao value=form_insert>
		       <input type=hidden name=ate_codigo value=$ate_codigo>
		       <input type=hidden name=tp_action value=controlados>
		       <input type=hidden name=id_login value=$id_login>";
  $sqlconta = pg_query("select count(*) as conta, rec_codigo from receita
                          where ate_codigo = $ate_codigo and rec_tipo = 'controlados' and rec_finalizada = 'N' group by rec_codigo order by rec_codigo limit 1") ;
  $rowconta=pg_fetch_array($sqlconta);
    if ($rowconta['conta'] == 0 ) {
        $sqlreceita = pg_query("select nextval('seq_rec_codigo'::text) as novo_codigo");       
        $rowreceita = pg_fetch_array($sqlreceita);
        $receita = $rowreceita[novo_codigo];
    }    
    else {
        $receita = $rowconta[rec_codigo];
    }
        
    echo"      <input type=hidden name=rec_codigo value=$receita>
               <input type=hidden name=ate_codigo value=$ate_codigo>
	     <tr>
		  <td width=20 align=right>Produto:</td>
		  <td colspan=4>
		   <select name=pro_codigo class=box>";
	    //
	    //-> SQL do produto
	    $query = pg_query("select * from produto where psico_codigo is not null order by pro_nome");
	       echo "<option value=''>---</option>";
	      while($produto=pg_fetch_array($query)) {
	       echo "<option value='$produto[pro_codigo]'>$produto[pro_nome]</option>";
	      }
	   echo "</select>
	       </td>";
	       echo "<tr>
		    <td width=20 valign=top>&nbsp;</td>
		    <td colspan=4>Recomenda&ccedil;&atilde;o M&eacute;dica:<br>
		     <textarea name=irec_recomendacao cols=40 rows=4 class=box></textarea>

	   </td>
	        </td>
	      <tr>
     		<td width=20 align=right>Quantidade:</td>
    		<td width=10><input type=text name=irec_quantidade class=box size=5 onchange='calcula()'></td>
            <td width=20 align=right>V&aacute;lido at&eacute;:</td>
    		<td width=257><input type=text name=rec_validade class=box size=10></td>
            </tr>
            <tr>
	       <td width=95>&nbsp;</td>";
    if ($rowconta != 0)
    echo "<script>
            document.inclui_item.rec_validade.disabled = true;
            document.inclui_item.rec_validade.style.background='silver';
        </script>";
           echo "<td colspan='3' width=95>
                    <input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/add_on.gif>&nbsp;
                        <a href='#' OnClick='location.replace(\"prontuario.php?pagina=7&?age_codigo=$age_codigo&ate_codigo=$ate_codigo\");window.open(\"print_receita.php?age_codigo=$age_codigo&id_login=$id_login&ate_codigo=$ate_codigo&receita=$receita&tp_action=controlados\",null,\"height=600,width=550,status=yes,toolbar=no,menubar=no,location=no\");location.replace(\"prontuario.php?pagina=7&?id_login=$id_login&age_codigo=$age_codigo&ate_codigo=$ate_codigo\");'>
                            <img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/finalizar_on.jpg border=0>
                        </a>
                </td>";

	     echo "</tr></form>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br>";
//
//-> Listando

  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Listando Itens Cadastrados para a Receita</legend>
	     <table width=100% align=center cellspacing=2 cellpadding=4 border=0>
	      <tr bgcolor=F9f9f9>
		<td width=180 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Produto</td>
		<td width=40 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Quantidade</td>
		<td style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Recomendação</td>
		<td style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td>
	     </tr>";

   $sql = pg_query("select irec_codigo, itemreceita.pro_codigo, pro_nome,  irec_recomendacao, irec_quantidade
                  from itemreceita, produto, receita
                  where itemreceita.pro_codigo = produto.pro_codigo
		  and  itemreceita.rec_codigo = receita.rec_codigo
		  and  ate_codigo = $ate_codigo
		  and  receita.rec_codigo = $receita
		  and  receita.rec_finalizada = 'N'
                  order by irec_codigo desc");

     while($row=pg_fetch_array($sql)) {
       $intquantidade = formata_valor0($row['irec_quantidade']);
       echo "<tr>
	       <td width=180 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[pro_nome]</td>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$intquantidade</td>
	       <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[irec_recomendacao]</td>
	       <td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'><a href=$PHP_SELF?ate_codigo=$ate_codigo&id_login=$id_login&acao=del&irec_codigo=$row[irec_codigo]&tp_action=$acao&rec_codigo=$rec_codigo><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.jpg border=0></a></td>
	     </tr>";
     }
	echo "
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table>";
}


 if ($acao == 'form_edit') {
  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Dados da Receita</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>";
    $sql =  pg_query("select ate_codigo, usu_nome, to_char(ate_data, 'dd/mm/yyyy') as ate_data, med_nome, usu_same
             from atendimento, usuario, medico
             where atendimento.usu_codigo = usuario.usu_codigo
             and   atendimento.med_codigo = medico.med_codigo
             and   ate_codigo = '$ate_codigo'");
   $row=pg_fetch_array($sql);
   echo "      <tr> 
		          <td width=70>Dados do Paciente</td>
		          <td width=10><input type=text readonly name=usu_same size=10 value='$row[usu_same]' class=box></td> 
		          <td colspan=2><input type=text readonly name=usu_nome   size=70 value='$row[usu_nome]' class=box></td>
               </tr>
               <tr>
		          <td width=70>&nbsp;</td>
		          <td width=20><input type=text readonly name=usu_datanasc size=20 value='$row[usu_datanasc]' class=box></td>
		          <td colspan=2><input type=text readonly name=usu_mae size=70 value='$row[usu_mae]' class=box></td>
               </tr>   
		<td><input type=text name=ate_data class=box size=20 value='$row[ate_data]'></td>
		<td><input type=text name=med_nome class=box size=20 value='$row[med_nome]'></td>
	      </tr>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br>";
//
//-> Botoes
   $sql=pg_query("select irec_codigo, itemreceita.pro_codigo, pro_nome, irec_quantidade, apre_codigo, irec_recomendacao
                    from itemreceita, produto
                   where itemreceita.pro_codigo = produto.pro_codigo
                     and rec_codigo = $rec_codigo
                     and irec_codigo = $irec_codigo
                  order by rec_codigo desc limit 15");
  $row = pg_fetch_array($sql);                
   $intquantidade = formata_valor0($row['ite_quantidade']);
  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Alteracao do Item da Receita</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	       <form name=altera_item method=post action=$PHP_SELF>
		<input type=hidden name=action value=edit>
		<input type=hidden name=acao value=>
		<input type=hidden name=id_login value=$id_login>
		<input type=hidden name=rec_codigo value=$rec_codigo>
		<input type=hidden name=irec_codigo value=$irec_codigo>
	      <tr>
		<td width=20>Produto:</td>
		<td colspan=4>
		 <select name=pro_codigo class=box>";
	    //
	    //-> SQL do produto
	    $query = pg_query("select * from produto where pro_tipo = 'M' order by pro_nome");
	       echo "<option value=''>---</option>";
	      while($produto=pg_fetch_array($query)) {
	       echo ($produto[pro_codigo]==$row[pro_codigo])?"<option value='$produto[pro_codigo]' selected>$produto[pro_nome]</option>":"<option value='$produto[pro_codigo]'>$produto[pro_nome]</option>";
	      }
	   echo "</select>
	        </td>
	      <tr>
		<td width=20>Apresentacao do Medicamento</td>
		<td colspan=4>
		 <select name=apre_codigo class=box>";
	    //
	    //-> SQL do produto
	    $query = pg_query("select * from apresentacao_produto order by apre_desc");
	       echo "<option value=''>---</option>";
	      while($apresentacao=pg_fetch_array($query)) {
	       echo ($apresentacao[apre_codigo]==$row[apre_codigo])?"<option value='$apresentacao[apre_codigo]' selected>$apresentacao[apre_desc]</option>":"<option value='$apresentacao[apre_codigo]'>$apresentacao[apre_desc]</option>";
	      }
	   echo "</select>
	        </td>
	        </td>
	      <tr>
     		<td width=20>Recomendacao:</td>
    		<td><input type=text name=irec_recomendacao class=box size=200 value='$row[irec_recomendacao]'></td>
            </tr>
	      <tr>
                <td width=20 align=right>Quantidade:</td>
                <td width=10><input type=text name=irec_quantidade class=box size=5 onchange='calcula()'></td>
                <td width=20 align=right>V&aacute;lido at&eacute;:</td>
                <td width=257><input type=text name=rec_validade class=box size=10></td>
            </tr>
            <tr>
	     <tr>
	       <td width=95><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg></td>
	       <td width=79 colspan='3'><a href=prontuario.php?pagina=7&?id_login=$id_login&ate_codigo=$ate_codigo&rec_codigo=$rec_codigo><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
	      </tr></form>
	     </table>
	   </fideldset>
	  </td>
	 </tr>
        </table><br>";

//
	echo "</tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table>";
}

//------------------------------------------------------------------>
//-> SQL's
//------------------------------------------------------------------>
//
//-> ADD <---------------------------------------------------------->

if($acao=="form_insert") {
	$ate_codigo = $_GET['ate_codigo'];
	$usu_codigo = $_GET['usu_codigo'];
	$contaReceita = "SELECT count(*) AS conta 
					   FROM receita
                      WHERE rec_codigo = $rec_codigo";
	$sqlconta = pg_query($contaReceita) ;
	$pegaMaximoReceita = "SELECT max(rec_codigo)+1 
    						FROM receita";
	$rec = pg_fetch_array(pg_query($pegaMaximoReceita));
	$cod_receita = $rec[0];
	if(empty($rec_codigo)) {
		if(empty($cod_receita)) { 
			$cod_receita='1';
		} else { 
			$cod_receita = $rec[0]; 
		}
	} else {
		$cod_receita = $rec_codigo;
	}
	#echo $sqlconta;
	$finaliza = 'N';
	$rowconta=pg_fetch_array($sqlconta);
	if ($rowconta['conta'] == 0 ) {
		$sqlreceita = "INSERT INTO receita (rec_data, 
												  rec_codigo,
												  rec_tipo,
												  rec_finalizada,
												  ate_codigo,
												  rec_validade) 
										 VALUES ( date(now()), "  . 
												( $cod_receita != "" ? "'$cod_receita'" : "null") . ", " .
												( $tp_action != "" ? "'$tp_action'" : "null") . ", " .
												( $finaliza != "" ? "'$finaliza'" : "null") . ", " .
												( $ate_codigo != "" ? "'$ate_codigo'" : "null") . ",  " .
												( $rec_validade != "" ? "'$rec_validade'" : "null") . "   " .
												")";
		$sql = pg_query($sqlreceita) or die("erro ".$sqlreceita);
		$exec = pg_fetch_array($sql);
		#Vsql($sqlreceita,'1');
		#exit;
	}
	if($desc_produto == "") {
		$insertItemReceita = "INSERT INTO itemreceita ( pro_codigo, 
														 irec_quantidade, 
														 rec_codigo, 
														 irec_recomendacao,
														 irec_qtde_pendente) 
												VALUES ( " .
													   ( $pro_codigo != "" ? "'$pro_codigo'" : "null") . ", " .
													   ( $irec_quantidade != "" ? "'$irec_quantidade'" : "null") . ", " . 
													   ( $cod_receita != "" ? "'$cod_receita'" : "null") . ", " .
													   ( $irec_recomendacao != "" ? "'$irec_recomendacao'" : "null") . ",  " .
													   ( $irec_quantidade != "" ? "'$irec_quantidade'" : "null") . 
													   ")";
		$sql = pg_query($insertItemReceita) or die("erro ".$insertItemReceita);
	} else {
		$insertItemReceita = "INSERT INTO itemreceita ( desc_produto, 
															 irec_quantidade,
															 rec_codigo,
															 irec_recomendacao) 
													VALUES ( " .
														   ( $desc_produto != "" ? "'$desc_produto'" : "null") . ", " .
														   ( $irec_quantidade != "" ? "'$irec_quantidade'" : "null") . ", " . 
														   ( $cod_receita != "" ? "'$cod_receita'" : "null") . ", " .
														   ( $irec_recomendacao != "" ? "'$irec_recomendacao'" : "null") . "  " .
														   ( $irec_quantidade != "" ? "'$irec_quantidade'" : "null") . ", " .
														   ")";
		$sql = pg_query($insertItemReceita) or die("erro ".$insertItemReceita);
	}
	echo "<SCRIPT LANGUAGE=\"JavaScript\">
				setTimeout(\"location='prontuario.php?pagina=7&usu_codigo=$usu_codigo&id_login=$id_login&rec_codigo=$rec_codigo&ate_codigo=$ate_codigo&age_codigo=$age_codigo&acao=$tp_action&action='\", 0);
		  </SCRIPT>";
	#Vsql($sql,"1");
	#Vsql($sqlreceita,"1");
}

//
//-> EDIT <--------------------------------------------------------->

 if($action=="edit") {
  $sql = pg_query("update itemreceita set " .
            ($pro_codigo ? "pro_codigo='$pro_codigo'" : "pro_codigo=null") . ", " .
            ($irec_quantidade ? "irec_quantidade='$irec_quantidade'" : "irec_quantidade=null") . ", " .
            ($apre_codigo ? "apre_codigo='$apre_codigo'" : "apre_codigo=null") . ", " .
            ($rec_codigo ? "rec_codigo='$rec_codigo'" : "rec_codigo=null") . ", " .
            ($irec_recomendacao ? "irec_recomendacao='$irec_recomendacao'" : "irec_recomendacao=null") . ", " .
            "where irec_codigo='$irec_codigo'");

msg($id_login,$acao,$sql);
       echo "<SCRIPT LANGUAGE=\"JavaScript\">
                setTimeout(\"location='prontuario.php?pagina=7&?id_login=$id_login&ate_codigo=$ate_codigo&rec_codigo=$rec_codigo&acao=$tp_action&action='\", 0);
           </SCRIPT>";
}

//
//-> DEL <---------------------------------------------------------->

if($acao=="del") {
	$ate_codigo = $_GET['ate_codigo'];
	$rec_codigo = $_GET['rec_codigo'];
	$tp_action = $_GET['tp_action'];
	$id_login = $_GET['id_login'];
	$acao = $_GET['acao'];
	$tp_action = $_GET['tp_action'];
	$usu_codigo = $_GET['usu_codigo'];
	$age_codigo = $_GET['age_codigo'];
	$irec_codigo = $_GET['irec_codigo'];
	$sql = pg_query("DELETE FROM itemreceita WHERE irec_codigo='$irec_codigo'");
	msg($id_login,$acao,$sql);
       echo "<SCRIPT LANGUAGE=\"JavaScript\">
				setTimeout(\"location='prontuario.php?pagina=7&usu_codigo=$usu_codigo&age_codigo=$age_codigo&id_login=$id_login&ate_codigo=$ate_codigo&rec_codigo=$rec_codigo&acao=$tp_action'\", 0);
			 </SCRIPT>";
}

?>

