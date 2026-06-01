<?php


	//$data_ini = "13/03/2007";
	//$data_alta = "11/04/2007";
	
    $barra = array("/");
    $data1 = str_replace($barra, "", $data_ini);
    $data2 = str_replace($barra, "", $data_alta);

	//$date_now = mktime(0, 0, 0, date("m"), date("d"), date("Y"));

	$d1 = substr($data1, 2,2); //30
	$m1 = substr($data1, 0,2); //11
	$Y1 = substr($data1, 4); //2006

	$d2 = substr($data2, 2,2); //30
	$m2 = substr($data2, 0,2); //11
	$Y2 = substr($data2, 4); //2006

    //print	"data processada 1 :".
	$data_processada1 = mktime(0,0,0,$d1,$m1,$Y1);
	//echo "<br />";
	//print	"data processada 2 :".
	$data_processada2 = mktime(0,0,0,$d2,$m2,$Y2);
	
	$data_final = $data_processada2 - $data_processada1;
	
	//echo "<br />".$data_final."<br />";
	
	if ($data_final >= 2505600)
	{	
		print "Paciente internado a mais de 1 mes !";
	}
	
	
?>