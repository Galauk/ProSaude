<?

/*
#Saude_relatorio_tipo:
html
pdf
mesclado
etiqueta
*/

//$sal_debug = 1;


if(!$saude_relatorio_tipo)
	$sal_relatorio_tipo = "html";

if($saude_relatorios_tipo == "html")
	$extensao = "html";
else
	$extensao = "pdf";

if($_GET['dir']){
	if($_GET['subdir'])
		$dir = "/home/websites/gps/public_html/".$_GET['dir']."/".$_GET['subdir'];
	else
		$dir = "/home/websites/gps/public_html/".$_GET['dir'];
}
// /home/websites/gps/public_html/relatorio
/* Nao mais usado */
//if($_SESSION['desenvolvimento']==1) echo $saude_relatorio_tipo;

# Include AgataAPI class
include_once '/home/websites/gps/public_html/agata/classes/core/AgataAPI.class';
//echo $etiqueta1;
// if ($etiqueta1 == 0) {
//    
//    $Parameters[0] = 'dias';
//    $Parameters[1] = 'datainicial';
//    $Parameters[2] = 'datafinal';
//
// }
//echo $_GET["$Parameters[0]"];
//echo (' olá ');
/*---- Define nome da saida aleatorio----*/
$random = rand();

$Output = "/home/websites/gps/public_html/relatorio/output/$projeto$random.$extensao";


# Instantiate AgataAPI
$api = new AgataAPI;
$api->setLanguage('pt'); //'en', 'pt', 'es', 'de', 'fr', 'it', 'se'
$api->setReportPath("$dir/$projeto.agt");
$api->setProject('saude');
$api->setOutputPath($Output);


/*----- Define formato de saida caso diferente de etiqueta ou mesclado ------*/
if($sal_relatorio_tipo != "mesclado" && $sal_relatorios_tipo != "etiqueta"){
   $api->setFormat($sal_relatorio_tipo); // 'pdf', 'txt', 'xml', 'html', 'csv', 'sxw'
   if ($sal_relatorio_tipo== "pdf")
      $api->setLayout('PDF');
   else
      $api->setLayout('default-HTML');
}

#How to set parameters, if they exist
    for($i = 0; $i < count($Parameters)  ; $i++){ // $Parameters[$i]
	if($_POST[$Parameters[$i]])
		$api->setParameter('$'.$Parameters[$i], $_POST[$Parameters[$i]]);
	else
		$api->setParameter('$'.$Parameters[$i], $_GET[$Parameters[$i]]);
}


/*----- GERA O RELATORIO -------*/
if($sal_relatorio_tipo == "etiqueta")
{
	echo "<center>Gera&ccedil;&atilde;o de Etiquetas<br></center>";
	$ok = $api->generateLabel();
}
else
if($sal_relatorio_tipo == "mesclado")
{
	echo "<center>Gera&ccedil;&atilde;o de Relat&oacute;rios em PDF<br></center>";
	$ok = $api->generateDocument();
}
else{
//	echo "<center>Gera&ccedil;&atilde;o de Relat&oacute;rios<br></center>";
	$ok = $api->generateReport();
}


/*------ Confirmacao do Relatorio -------*/
if (!$ok)
{
    echo $api->getError();
}
else
{
	if ($sal_relatorio_tipo == "pdf")
		echo "<center><a href='relatorios/output/$projeto$random.pdf'>Download</a></center>";
	else
	if ($sal_relatorio_tipo == "mesclado") // padrao 
		echo "<center><a href='relatorios/output/$projeto$random.pdf'>Download</a></center>";
	else
	if ($sal_relatorio_tipo == "etiqueta")
		echo "<center><a href='relatorios/output/$projeto$random.pdf'>Download</a></center>";
	else
		include ($Output);        
}
?> 

