<link href="estilo.css" rel="stylesheet" type="text/css">
<STYLE type="text/css">
<!--
BODY {
scrollbar-face-color: #e1e1e1;
scrollbar-highlight-color: #cccccc;
scrollbar-3dlight-color: #909090;
scrollbar-darkshadow-color: #909090;
scrollbar-shadow-color: #eeeeee;
scrollbar-arrow-color: #909090;
scrollbar-track-color: #909090;
}
-->
</STYLE>
<meta http-equiv="refresh" content="5">
<?
	session_start();
	include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
	verauth($id_login);
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario($id_login);

	$data=date("d_m_Y");
	//pega o endereço do diretório
$diretorio = "log/";;
// abre o diretório
$ponteiro  = opendir($diretorio);
// monta os vetores com os itens encontrados na pasta
while ($nome_itens = readdir($ponteiro)) {
   
}
$ar = "log/SAU_".$data.".log";
$file = fopen($ar, 'r');
				
	while (!feof($file)){
		
		$buffer = fgets($file);
		echo $buffer;
	}
	for($i=0;$i<strlen($buffer);$i++){
		//echo $buffer[$i];
	}
$file = fclose($ar);

      //$tt=shell_exec("tail -n 25 log/SAU_".$data.".log");
      //$tt=fopen("log/SAU_".$data.".log","a+");
 //secho $tt;

?>
