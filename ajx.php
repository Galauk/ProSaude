<?
  $gmtDate = gmdate("D, d M Y H:i:s");
 
  header("Expires: {$gmtDate} GMT");
  header("Last-Modified: {$gmtDate} GMT");
  header("Cache-Control: no-cache, must-revalidate");
  header("Pragma: no-cache");
 
  //os readers acima serao explicados apos o script
 
  $n = $_GET["n"]; //pegar a variavei enviada
 
  //vamos multiplicar essa variavel por 50
  $n *= 50;
 
  echo $n; //agora vamos "retornar" o valor, para isso escrevemos ele
  echo $_GET["teste"]; 
  ?>
