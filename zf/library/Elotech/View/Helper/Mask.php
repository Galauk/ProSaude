<?php

class Elotech_View_Helper_Mask extends Zend_View_Helper_Abstract {

	public function mask($str, $formato="fone", $opcional=NULL) {
		$metodo = "mask" . ucfirst(strtolower($formato));
		return $this->$metodo($str, $opcional);
	}

	public function __call($name, $arguments) {
		$name = strtolower(substr($name, 4)); // maskFone = fone
		//trigger_error("Formato \"$name\" não é uma máscara válida.", E_USER_NOTICE);
		return $arguments[0];
	}

	private function maskFone($str) {
		$str = preg_replace("/[^0-9]+/", "", $str);
		if ($str == "")
			return "";

		$ddd = substr($str, 0, 2);
		$pre = substr($str, 2, 4);
		$num = substr($str, 6, 4);
		return "($ddd) $pre-$num";
	}

	private function maskCep($str) {
		$a = substr($str, 0, 5);
		$b = substr($str, 5, 3);
		return "$a-$b";
	}

	private function maskCpf($str) {
		$a = substr($str, 0, 3);
		$b = substr($str, 3, 3);
		$c = substr($str, 6, 3);
		$d = substr($str, 9, 2);
		return "$a.$b.$c-$d";
	}

	private function maskCnpj($str) {
		$a = substr($str, 0, 2);
		$b = substr($str, 2, 3);
		$c = substr($str, 5, 3);
		$d = substr($str, 8, 4);
		$e = substr($str, 12, 2);
		return "$a.$b.$c/$d-$e";
	}

	private function maskIe($str) {
		return number_format($str, 0, "", ".");
	}

	private function maskCreci($str) {
		$a = substr($str, 0, -1);
		$b = substr($str, -1);
		return "$a-$b";
	}

	private function maskEmail($str) {
		$str = strip_tags($str);
		return sprintf("<a href=\"mailto:%s\">%s</a>", $str, $str);
	}

	private function maskUrl($str) {
		$str = strip_tags($str);
		return sprintf("<a href=\"http://%s\" target=\"_blank\">%s</a>", $str, $str);
	}

	private function maskDh($str) {
		$dh = new Zend_Date($str);
		return $dh->get(Zend_Date::DATETIME_MEDIUM);
	}

	private function maskSexo($str) {
		switch ($str) {
			case "F":
				$str = "Feminino";
				break;
			case "M":
				$str = "Masculino";
				break;
			case "I":
			case "":
				$str = "Indiferente";
				break;
		}
		return $str;
	}

	private function maskBool($str) {
		return $str ? "Sim" : "Não";
	}

	private function maskData($str) {
		$in = strpos($str, "-") ? "-" : "/";
		$out = strpos($str, "-") ? "/" : "-";

		list($a, $m, $d) = explode($in, $str);
		return $d . $out . $m . $out . $a;
	}

	private function maskEmpty($str, $opcional=NULL) {
		return empty($str) ? $opcional : $str;
	}

	private function maskAtende($str) {
		switch ($str) {
			case "1":
				$str = "<font color=green>Atende</font>";
				break;
			case "":
				$str = "<font color=red>Não atende</font>";
				break;
		}
		return $str;
	}

	private function maskSus($str){
		$str = preg_replace("/[^0-9]+/", "", $str);
		if ($str == "")
			return "";
		
		$grupo = substr($str, 0, 2);
		$sub = substr($str, 2, 2);
		$forma = substr($str, 4, 2);
		$cod = substr($str, 6, 3);
		$dv = substr($str, 9, 1);
		
		return "$grupo.$sub.$forma.$cod-$dv";		
	}
}
