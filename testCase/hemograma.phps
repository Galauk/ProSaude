<?php

		error_reporting(E_ALL & ~E_NOTICE );
		ini_set("display_errors",1);
		ini_set("ignore_repeated_errors",0);

require_once 'PHPUnit.php';
require_once 'C:\desenvolvimento\elotech\WebSocialComum\class\calculosHemogramaClass.php';

class hemogramaTest extends PHPUnit_TestCase{

	// Carlos Alberto Amorim
	public function testVCM(){
		$hemo = new Hemograma();
		$this->assertEquals(88.89, $hemo->calculoVcm(40, 4.5));
	}
	
	public function testHCM(){
		$hemo = new Hemograma();
		$this->assertEquals(29.33, $hemo->calculoHcm(13.2, 4.5));
	}
	
	public function testCHCM(){
		$hemo = new Hemograma();
		$this->assertEquals(33, $hemo->calculoChcm(13.2, 40));
	}

	// Helen Ribeiro da Silva
	public function testVCM2(){
		$hemo = new Hemograma();
		$this->assertEquals(87.96, $hemo->calculoVcm(38, 4.32));
	}
	
	public function testHCM2(){
		$hemo = new Hemograma();
		$this->assertEquals(28.7, $hemo->calculoHcm(12.4, 4.32));
	}
	
	public function testCHCM2(){
		$hemo = new Hemograma();
		$this->assertEquals(32.63, $hemo->calculoChcm(12.4,38));
	}
}
