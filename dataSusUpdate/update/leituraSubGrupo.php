<link href='../../css/estiloForm.css' rel='stylesheet' type='text/css' />
<link href='../../css/estiloCommon.css' rel='stylesheet' type='text/css' />
<?php
session_start();
include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
require_once $_SESSION[root].$_SESSION[comum]."class/formClass.php";
require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
require_once $_SESSION[root].$_SESSION[comum]."class/tableClass.php";

$ponteiro = fopen ("tb_sub_grupo.txt", "r");

	while (!feof ($ponteiro)) {
		$linha = fgets($ponteiro, 4096);
		
		$co_grupo = substr($linha,0,2);
		$co_sub_grupo = substr($linha,2,2);
		$no_sub_grupo = substr($linha,4,100);
		$dt_competencia = substr($linha,104,6);
		
		$stmt = "UPDATE tb_sub_grupo SET 
						co_grupo = '$co_grupo', 
						co_sub_grupo = '$co_sub_grupo',
						no_sub_grupo = '$no_sub_grupo',
						dt_competencia = '$dt_competencia'
						WHERE no_sub_grupo = '$no_sub_grupo'" ;

	    $qry = pg_query($stmt) or die (pg_last_error());
		$affected = pg_affected_rows($qry);
		if($affected == 0){
			$inserir = "INSERT INTO tb_sub_grupo ( 
									co_grupo,
									co_sub_grupo, 
									no_sub_grupo,
									dt_competencia
     					 ) VALUES ( 
									'$co_grupo', 
									'$co_sub_grupo',
									'$no_sub_grupo',
									'$dt_competencia' ) ";
			//echo $inserir."<br/><br/>";

			$qryInseri = pg_query($inserir) or die (pg_last_error());
		}

	}
echo "<script>
			alert(Salvo com Sucesso!);
	  </script>";
/*$common = new commonClass();
echo $common->incJquery();
echo $common->modalMsg('OK','Importado com Sucesso!');*/

fclose ($ponteiro);



?>