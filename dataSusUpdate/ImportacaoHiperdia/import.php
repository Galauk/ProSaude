<?php


ini_set("display_errors",1);
ini_set("ignore_repeated_errors",0);


//$conexao = ibase_connect("sigsaude.fdb","SYSDBA","masterkey");
$conexao = ibase_connect('localhost:C:\desenvolvimento\elotech\WebSocialSaude\dataSusUpdate\ImportacaoHiperdia\Hiper.gdb','SYSDBA','masterkey') or die( ibase_errmsg() );
echo var_dump($conexao);

if ($conexao){
	echo "abre fdp";
$resultado = ibase_query($conexao,'SELECT * FROM  TB_PESSOA_HIPER');
//echo $resultado;
	while ($objresultado = ibase_fetch_assoc($resultado)){
		echo $objresultado['NO_PESSOA']. "<br>";
	}		
}

//ibase_close($conexao);