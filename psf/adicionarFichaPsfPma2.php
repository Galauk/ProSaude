<link href='../css/estiloform.css' rel='stylesheet' type='text/css' />
<link href='../css/estilocss' rel='stylesheet' type='text/css' />
<script>
	function carregaPma2(pma2_codigo, id_login){
		location.href = "fichaPma2.php?id_login="+id_login+"&pma2_codigo="+pma2_codigo;
		
	}
</script>
<?php
session_start();
require_once $_SESSION[root].$_SESSION[comum].'class/commonClass.php';
require_once $_SESSION[root].$_SESSION[comum].'class/formClass.php';
require_once $_SESSION[root].$_SESSION[comum].'class/tableClass.php';
require_once $_SESSION[root].$_SESSION[comum].'library/php/debug.inc.php';
require_once $_SESSION[root].$_SESSION[comum].'library/php/db.inc.php';

$common = new commonClass();
$form = new classForm();
$table = new tableClass();

$array = array($common->commonButton("Adicionar","pma2.php?id_login=$id_login",
			   "adicionar_on.png"),
				$form->inputText("palavra", NULL,"Buscar"),
				$form->hiddenForm("acao", "buscar"),
				$common->commonButton("buscar", null,"buscar.png", "onclick=\"document.formu.submit();\""));
$arrayTamanho = array("300"
			   );

echo $common->incJquery();
echo $common->menuTab(array("Lista Fichas PMA2"));
	echo $common->bodyTab('1');
		 $sql ="SELECT pm.pma2_codigo,
		 			   c.cid_nome ||'-'|| e.uf_sigla as cid_est,
		 			   u.usr_nome,
		 			   uni.uni_desc,
		 			   area_desc,
		 			   CASE pma2_mes
				         WHEN '01' THEN 'Janeiro'
				         WHEN '02' THEN 'Fevereiro'
				         WHEN '03' THEN 'Março'
				         WHEN '04' THEN 'Abril'
				         WHEN '05' THEN 'Maio'
				         WHEN '06' THEN 'Junho'
				         WHEN '07' THEN 'Julho'
				         WHEN '08' THEN 'Agosto'
				         WHEN '09' THEN 'Setembro'
				         WHEN '10' THEN 'Outubro'
				         WHEN '11' THEN 'Novembro'
				         WHEN '12' THEN 'Dezembro'
				         
				        
				      END as pma2_mes ,
		 			   pma2_ano
				  FROM pma2 pm
				  JOIN cidade c
				    ON pm.cid_codigo_ibge = c.cid_codigo_ibge
				  JOIN estado e
				    ON c.uf_codigo = e.uf_codigo
				  JOIN usuarios u
				    ON u.usr_codigo = pm.usr_codigo
				  JOIN unidade uni
				    ON pm.uni_cnes = uni.uni_codigo
				 ORDER BY pm.pma2_codigo desc
				 LIMIT 10
				    ";
		 $query = pg_query($sql);
		// $res = pg_fetch_array($query);
		
			
echo $form->openForm("AdicionarFichaPsfPma2.php","GET","formu");	 
		echo $table->openTable();
		
			echo $table->criaLinha($array,$arrayTamanho,NULL);
		echo $table->closeTable();
echo $form->closeForm();
if($acao == ""){	
		echo $table->openTable("lista","100%");
			echo $table->criaLinha(array("Cidade","Cadastrador","Unidade","&Aacute;rea","Mês", "Ano","Exportação"),NULL,NULL,"S");
			while($res =pg_fetch_array($query))
			{
				 
				if($res[pma2_mes] == "01")
					$res[pma2_mes] = "Janeiro";
				else if ($res[pma2_mes] == "02")
					$res[pma2_mes] = "Fevereiro";
				else if ($res[pma2_mes] == "03")
					$res[pma2_mes]= "Mar&ccedil;o";
				else if($res[pma2_mes] =="04")
					$res[pma2_mes] ="Abril";
				else if($res[pma2_mes] =="05")
					$res[pma2_mes] ="Maio";
				else if($res[pma2_mes] =="06")
					$res[pma2_mes] ="Junho";
				else if($res[pma2_mes] =="07")
					$res[pma2_mes] ="Julho";
				else if($res[pma2_mes] =="08")
					$res[pma2_mes] ="Agosto";
				else if($res[pma2_mes] =="09")
					$res[pma2_mes] ="Setembro";
				else if($res[pma2_mes] =="10")
					$res[pma2_mes] ="Outrubro";
				else if($res[pma2_mes] =="11")
					$res[pma2_mes] ="Novembro";
				else if($res[pma2_mes] =="12")
					$res[pma2_mes] ="Dezembro";
				echo $table->criaLinha(array("$res[cid_est]","$res[usr_nome]","$res[uni_desc]","$res[area_desc]","$res[pma2_mes]","$res[pma2_ano]","<a href=setor.php>".$common->commonButton("Exportar", "NULL","laudo.png")."</a>"),NULL,NULL,"N","onClick=carregaPma2($res[pma2_codigo],$id_login)");			
			}
			
		echo $table->closeTable();
	}
if($acao == "buscar"){	
$sql = "SELECT pm.pma2_codigo,
		 			   c.cid_nome ||'-'|| e.uf_sigla as cid_est,
		 			   u.usr_nome,
		 			   uni.uni_desc,
		 			   area_desc,
		 			   CASE pma2_mes
				         WHEN '01' THEN 'Janeiro'
				         WHEN '02' THEN 'Fevereiro'
				         WHEN '03' THEN 'Março'
				         WHEN '04' THEN 'Abril'
				         WHEN '05' THEN 'Maio'
				         WHEN '06' THEN 'Junho'
				         WHEN '07' THEN 'Julho'
				         WHEN '08' THEN 'Agosto'
				         WHEN '09' THEN 'Setembro'
				         WHEN '10' THEN 'Outubro'
				         WHEN '11' THEN 'Novembro'
				         WHEN '12' THEN 'Dezembro'
				         
				        
				      END as pma2_mes ,
		 			   pma2_ano
				  FROM pma2 pm
				  JOIN cidade c
				    ON pm.cid_codigo_ibge = c.cid_codigo_ibge
				  JOIN estado e
				    ON c.uf_codigo = e.uf_codigo
				  JOIN usuarios u
				    ON u.usr_codigo = pm.usr_codigo
				  JOIN unidade uni
				    ON pm.uni_cnes = uni.uni_codigo
			 WHERE cid_nome LIKE UPPER ('%$palavra%') 
			
				OR usr_nome LIKE '%$palavra%' 
				OR uni_desc LIKE UPPER ('%$palavra%')
				OR area_desc LIKE '%$palavra%'
				OR pma2_mes LIKE '%$palavra%'";
	$query = pg_query($sql);
	echo $table->openTable("lista","100%");
			echo $table->criaLinha(array("Cidade","Cadastrador","Unidade","&Aacute;rea","Mês", "Ano","Exportação"),NULL,NULL,"S");
			while($res =pg_fetch_array($query))
			{
				 
				if($res[pma2_mes] == "01")
					$res[pma2_mes] = "Janeiro";
				else if ($res[pma2_mes] == "02")
					$res[pma2_mes] = "Fevereiro";
				else if ($res[pma2_mes] == "03")
					$res[pma2_mes]= "Mar&ccedil;o";
				else if($res[pma2_mes] =="04")
					$res[pma2_mes] ="Abril";
				else if($res[pma2_mes] =="05")
					$res[pma2_mes] ="Maio";
				else if($res[pma2_mes] =="06")
					$res[pma2_mes] ="Junho";
				else if($res[pma2_mes] =="07")
					$res[pma2_mes] ="Julho";
				else if($res[pma2_mes] =="08")
					$res[pma2_mes] ="Agosto";
				else if($res[pma2_mes] =="09")
					$res[pma2_mes] ="Setembro";
				else if($res[pma2_mes] =="10")
					$res[pma2_mes] ="Outrubro";
				else if($res[pma2_mes] =="11")
					$res[pma2_mes] ="Novembro";
				else if($res[pma2_mes] =="12")
					$res[pma2_mes] ="Dezembro";
				echo $table->criaLinha(array("$res[cid_est]","$res[usr_nome]","$res[uni_desc]","$res[area_desc]","$res[pma2_mes]","$res[pma2_ano]","<a href=setor.php>".$common->commonButton("Exportar", "NULL","laudo.png")."</a>"),NULL,NULL,"N","onClick=carregaPma2($res[pma2_codigo],$id_login)");			
			}
			
		echo $table->closeTable();
}
	echo $comoon->closeTab()

?>