<?
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>
session_start();
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
cabecario();
//------------------------------------------------------------------>


//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
//--


 $titulo="total de agendamentos por médico";
 $dt_i="01/07/2006";
 $dt_f="01/08/2006";
 $unidade="CONSÓRCIO INTERMUNICIPAL DE SAUDE";

 //
 // -> Sentando a funcao

 cabecario_rel($titulo,$dt_i,$dt_f,$unidade);

?>


