<?php
	@ session_start();
	@ $id_login = base64_decode($_SESSION["b80bb7740288fda1f201890375a60c8f"]);
	header("Content-type: text/html; charset=ISO-8859-1");
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
	
	
	//
	// -> Arquivos de Funções
	//
	
	// inclui algumas funcoes especificas para o banco
	include_once $_SESSION[root].$_SESSION[modulo]."funcoes.db.php";
	
	// ------------------------------------------------------------------>
	/**
	* @brief Cabecario das telas
	*/
	function cabecario( $hotkey = false )
	{
		include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
		echo 
		'<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">'."\n".
		"<html>\n".
		"<head>\n".
		"	<link href='estilo.css' rel='stylesheet' type='text/css'>\n".
		"	<link href='estilo_janela.css' rel='stylesheet' type='text/css'>\n".	
		"	<link href='../estilo.css' rel='stylesheet' type='text/css'>\n".
		"	<link href='../estilo_janela.css' rel='stylesheet' type='text/css'>\n".	
		"	<title>GPS - Software de Gestão Pública</title>\n".
		"</head>\n".
		"<body bgcolor='#FFFFFF' ".($hotkey ? 'onkeydown="return hotkey(event);"' : '' ).">\n";
	
		//Criar o objeto XMLHttpRequest - para possibilitar o uso do AJAX
		?>
		
		<script type="text/javascript">
		<!--
		function createRequestObject()
		{
		   var ro;
		   var browser = navigator.appName;
		   if (browser == "Microsoft Internet Explorer"){
		      ro = new ActiveXObject("Microsoft.XMLHTTP");
		   } else {
		        ro = new XMLHttpRequest();
		   }
		   return ro;
		}
		
		var http = createRequestObject();
		
		/**
		 * @var id_campo : id do campo a ser validado
		 * @var txt_campo : nome (texto) do campo
		 * @return bool
		*/
		function valida( id_campo, txt_campo )
		{
			c = document.getElementById( id_campo );
			
			if( ! c )
			{
				alert('Elemento "'+ id_campo +'" não existe !');
				return false;
			}
			
			if( ! c.value )
			{
				alert('O Campo "'+ txt_campo +'" é obrigatório !');
				c.focus();
				return false;
			}
			
			return true;
		}
		
		/** Insere um elemento de uma janela 'popup' a um select de um 'opener'
		 * @var id_opener
		 * @var opt_value
		 * @var opt_text
		*/
		function add_opener_option( id_opener, opt_value, opt_text )
		{
			var wd  = window.opener.document;
			var opt = wd.getElementById( id_opener );
			
			// procura por uma ocorrência
			for( i = 0; i < opt.length; i++ )
			{
				if( opt.options[i].value == opt_value )
				{
					opt.options[i].selected = true;
					self.close();
					return;
				}
			}
			
			try {
				l = opt.length + 1;
				opt.length = l;
				opt.options[ l-1 ] = new Option( opt_text, opt_value, selected=true );
			}
			catch( ex )
			{
				//l = opt.length + 1;
				//opt.length = l;
			}
		
		 	self.close();
		}
		
		/** Insere um elemento de uma janela 'popup' a um input 'text' e um 'hidden' de um 'opener'
		 * @var id_opener
		 * @var opt_value
		 * @var opt_text
		*/
		function add_opener_hidden_text( id_opener_txt, id_opener_hidden, code, txt )
		{
			var wd  = window.opener.document;
			var o_txt = wd.getElementById( id_opener_txt );
			var o_hid = wd.getElementById( id_opener_hidden );
		
			o_txt.value = txt;
			o_hid.value = code;
			
			self.close();
		
		}
		/** Insere um elemento de uma janela 'popup (ajax)' a um input 'text' e um 'hidden'
		 * @var id_opener
		 * @var opt_value
		 * @var opt_text
		*/
		function add_popup_hidden_text( id_opener_txt, id_opener_hidden, code, txt )
		{
		
		 	var o_txt = document.getElementById( id_opener_txt );
		 	var o_hid = document.getElementById( id_opener_hidden );
			
			o_txt.value = txt;
			o_hid.value = code;
		}
		/** funcao padrao para abrir as janelas
		 * @url endereco
		 * @tgt caso QUEIRA abrir um target especifico !
		*/
		function popup( url, tgt )
		{
			if( ! tgt ) tgt = 'YabaDabaDuh';
			win = window.open( url, tgt, 'width=500,height=450,scrollbars=yes');
			setTimeout( "win.focus();" , 1 );
		}
		
		//-->
		</script>
		
		<?
	} //-> FECHAMENTO DA FUNCAO CABECARIO
	
	//------------------------------------------------------------------>
	//-> Mensagens dos registros
	//------------------------------------------------------------------>
	
	function msg($id_login,$acao,$sql,$text_ok,$text_error)
	{
		//$GetNameFile=str_replace("/","",$_SERVER["SCRIPT_NAME"]);
		$GetNameFile = $_SERVER["SCRIPT_NAME"];
		switch ($acao)
		{
			case "add":
				$resp_ok = "<font size=2 color=green><b>INCLUSO com Sucesso</b></font>";
				$resp_erno = "<font size=2 color=red><b>ERRO ao INCLUIR</b></font>";
				break;
			case "edit":
				$resp_ok = "<font size=2 color=green><b>EDITADO com Sucesso</b></font>";
				$resp_erno = "<font size=2 color=red><b>ERRO ao EDITAR</b></font>";
				break;
			case "del":
				$resp_ok = "<font size=2 color=green><b>APAGADO com Sucesso</b></font>";
				$resp_erno = "<font size=2 color=red><b>ERRO ao APAGAR</b></font>";
				break;
			case "txt":
				$resp_ok = "<font size=2 color=green><b>".$text_ok."</b></font>";
				$resp_erno = "<font size=2 color=red><b>".$text_error."</b></font>";
				break;
		}
		echo "<br><br><br><br><br><br><br><br><br><br><br><br><br>
				<table height=100 width=100% align=center cellspacing=0 cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>
						<tr bgcolor=f9f9f9>
							<td align=center>";
								if ($sql) { 
									echo $resp_ok;
								}else{ 
									echo $resp_erno;
								} 
					  echo "</td>
						</tr>
				</table><br>";
		echo "<SCRIPT LANGUAGE=\"JavaScript\">
				setTimeout(\"location='$GetNameFile?id_login=$id_login'\", 2000);
		</SCRIPT>";
	}
	
	//------------------------------------------------------------------>
	//-> Cabecario de Relatorios
	//------------------------------------------------------------------>
	 function cabecario_rel($titulo,$dt_i,$dt_f,$unidade) {	 		
	 	 echo "
	 	 <table>
	 	 	<tr>
	 	 		<td>
	 	 			Relat&oacute;rio de $titulo
	 	 		</td>
	 	 	</tr
	 	 </table>";
	 }
	
	//------------------------------------------------------------------>
	//-> Formatacao de Valor
	//------------------------------------------------------------------>
	 //rotina que formata o valor para sair com duas casas decimais 
	 function formata_valor($valor) {
	   $sep_valor=explode(".",$valor);
	   if($sep_valor[1]=="") { $zero_2="00"; }
	   if(strlen($sep_valor[1])=="1") { $zero_2="$sep_valor[1]0"; }
	   if(strlen($sep_valor[1])>="2") { $zero_2=substr($sep_valor[1],0,2); }
	   return "$sep_valor[0].$zero_2";
	 }
	 //rotina que formata o valor para sair com quatro casas decimais 
	 function formata_valor4($valor) {
	   $sep_valor=explode(".",$valor);
	   if($sep_valor[1]=="") { $zero_2="0000"; }
	//   if(strlen($sep_valor[1])=="1") { $zero_2="$sep_valor[1]000"; }
	//   if(strlen($sep_valor[1])>="2") { $zero_2=substr($sep_valor[1],0,2); }
	   $zero_2=$sep_valor[1];
	   return "$sep_valor[0].$zero_2";
	 }
	 //rotina que formata o valor para sair inteiro, sem casas decimais
	 function formata_valor0($valor) {
	   $sep_valor=explode(".",$valor);
	   if($sep_valor[1]=="") { $zero_2="0000"; }
	   if(strlen($sep_valor[1])=="1") { $zero_2="$sep_valor[1]0"; }
	   if(strlen($sep_valor[1])>="2") { $zero_2=substr($sep_valor[1],0,2); }
	   return "$sep_valor[0]";
	 }
	
	//------------------------------------------------------------------>
	//-> Valor por extenso
	//------------------------------------------------------------------>
	function extenso($valor=0, $maiusculas=false){
	global $rt;
	    // verifica se tem virgula decimal
	    if (strpos($valor,",") > 0)
	    {
	      // retira o ponto de milhar, se tiver
	      $valor = str_replace(".","",$valor);
	
	      // troca a virgula decimal por ponto decimal
	      $valor = str_replace(",",".",$valor);
	    }
	    $singular = array("Centavo", "Real", "Mil", "Milhão", "Bilhão", "Trilhão", "Quatrilhão");
	    $plural = array("Centavos", "Reais", "Mil", "Milhões", "Bilhões", "Trilhões","Quatrilhões");
	    $c = array("", "Cem", "Duzentos", "Trezentos", "Quatrocentos","Quinhentos", "Seiscentos", "Setecentos", "Oitocentos", "Novecentos");
	    $d = array("", "Dez", "Vinte", "Trinta", "Quarenta", "Cinquenta","Sessenta", "Setenta", "Oitenta", "Noventa");
	    $d10 = array("Dez", "Onze", "Doze", "Treze", "Quatorze", "Quinze","Dezesseis", "Dezesete", "Dezoito", "Dezenove");
	    $u = array("", "Um", "Dois", "Três", "Quatro", "Cinco", "Seis","Sete", "Oito", "Nove");
	    $z=0;
	    $valor = number_format($valor, 2, ".", ".");
	    $inteiro = explode(".", $valor);
	    for($i=0;$i<count($inteiro);$i++)
	        for($ii=strlen($inteiro[$i]);$ii<3;$ii++)
	            $inteiro[$i] = "0".$inteiro[$i];
	    $fim = count($inteiro) - ($inteiro[count($inteiro)-1] > 0 ? 1 : 2);
	    for ($i=0;$i<count($inteiro);$i++) {
	        $valor = $inteiro[$i];
	        $rc = (($valor > 100) && ($valor < 200)) ? "cento" : $c[$valor[0]];
	        $rd = ($valor[1] < 2) ? "" : $d[$valor[1]];
	        $ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : "";
	        $r = $rc.(($rc && ($rd || $ru)) ? " e " : "").$rd.(($rd && $ru) ? " e " : "").$ru;
	        $t = count($inteiro)-1-$i;
	        $r .= $r ? " ".($valor > 1 ? $plural[$t] : $singular[$t]) : "";
	        if ($valor == "000")$z++; elseif ($z > 0) $z--;
	        if (($t==1) && ($z>0) && ($inteiro[0] > 0)) $r .= (($z>1) ? " de " : "").$plural[$t];
	        if ($r) $rt = $rt . ((($i > 0) && ($i <= $fim) &&
	           ($inteiro[0] > 0) && ($z < 1)) ? ( ($i < $fim) ? ", " : " e ") : " ") . $r;
	    }
	         if(!$maiusculas){
	          return (strtolower($rt) ? strtolower($rt) : "zero");
	         } else {
	          return($rt ? $rt : "Zero");
	         }
	
	}
	
	//
	// FUNCAO PARA GRAVACAO DE LOG
	//
	function reglog($id_login,$click) {
	  $id_login = base64_decode($_SESSION["b80bb7740288fda1f201890375a60c8f"]);
	  $row=pg_fetch_array(pg_query("select *from usuarios where usr_codigo='$id_login'"));
	  $data=date("d_m_Y");
	  $fp=fopen("log/SAU_".$data.".log","ab");
	  $dt=date("d/m/Y H:i");
	  fputs($fp,"<font color=blue>$dt</font> - <font color=red>$row[usr_nome]</font> :: $click ({$_SERVER['REMOTE_ADDR']})<br>\n");
	  fclose($fp);
	}
	
	
	function vSQL($sql,$print) {
	 if($print>="1") {
	   echo "------------------------------------------------------------------------------------<br><br>";
	   echo "DUMP DA SQL<br><br>";
	   echo $sql;
	   echo "<br><br>------------------------------------------------------------------------------------";
	   }
	  if(!($db = pg_connect("host=localhost dbname=ibisaude user=postgres password=gvw60!@.5A")))
	   die("pg_connect");
	
	  if(!pg_send_query($db, $sql))
	   die("pg_send_query");
	
	  if(!($result = pg_get_result($db)))
	   die("pg_get_result");
	
	  echo "<br>".(pg_result_error($result) . "<br />\n");
	
	
	}
	
	/**
	 * @brief Retorna o dia da semana por extenso
	 * @param $ds (int) Caso assuma o valor padrão, ele pega a data atual
	 * @note domingo = 0 ... sábado = 6
	 * @return string
	*/
	function dia_da_semana( $ds = -1 )
	{
		if( $ds == -1 ) $ds = date('w');
		switch($ds)
		{
			case 1:
			$dia_da_semana = "Segunda Feira";
			break;
			
			case 2:
			$dia_da_semana = "Terça Feira";
			break;
			
			case 3:
			$dia_da_semana = "Quarta Feira";
			break;
			
			case 4:
			$dia_da_semana = "Quinta Feira";
			break;
			
			case 5:
			$dia_da_semana = "Sexta Feira";
			break;
			
			case 6:
			$dia_da_semana = "Sábado";
			break;
			
			case 0:
			$dia_da_semana = "Domingo";
			break;
		}
		return $dia_da_semana;
	}
	
	/**
	 * @brief Monta os divs para formar uma janela 'popup ajax'
	 * @var $id
	 * @var $titulo
	 * @var $class
	 * @var $style
	 * @return String
	*/
	function monta_janela( $id, $titulo = '', $class='janela', $style='' )
	{
		$style = ( empty($style) ? '' : " style=\"$style\"" );
		return "
		<div class=\"{$class}\" id=\"{$id}\"{$style}>
			<div class=\"titulo\" id=\"{$id}_titulo\">
				<span id=\"{$id}_titulo_txt\">{$titulo}</span>
				<img src=\"".$_SESSION[linkroot].$_SESSION[comum]."imgs/jan_fechar.jpg\" onclick=\"esconde_janela('{$id}')\" alt=\"Fechar\" />
				<img src=\"".$_SESSION[linkroot].$_SESSION[comum]."imgs/jan_min.jpg\" id=\"{$id}_mm\" class=\"mm\" onclick=\"mm_janela('{$id}')\" alt=\"Fechar\" />
			</div>
			<div class=\"conteudo\" id=\"{$id}_conteudo\">
				Carregando <img src=\"".$_SESSION[linkroot].$_SESSION[comum]."imgs/loading.gif\" alt=\"Carregando\" align=\"absmiddle\" />
			</div>
		</div>
		";
	}
	
	/**
	 * @return String
	*/
	function monta_janela_usu( $id, $titulo = '', $class='janela', $style="width:300px;height:100px" )
	{
		return "
		<div class=\"{$class}\" id=\"{$id}\" style=\"$style\">
			<div class=\"titulo\" id=\"{$id}_titulo\">
				<span id=\"{$id}_titulo_txt\">{$titulo}</span>
				<img src=\"".$_SESSION[linkroot].$_SESSION[comum]."imgs/jan_fechar.jpg\" onclick=\"esconde_janela('{$id}')\" alt=\"Fechar\" />
			</div>
			<div class=\"conteudo\" id=\"{$id}_conteudo\">
				Carregando <img src=\"".$_SESSION[linkroot].$_SESSION[comum]."imgs/loading.gif\" alt=\"Carregando\" align=\"absmiddle\" />
			</div>
		</div>
		";
	}
	
	/**
	 *  Devolve o nome do mes
	*/
	function mes( $mes = 1 )
	{
		switch( intval($mes) )
		{
			case 1: return 'Janeiro';
			case 2: return 'Fevereiro';
			case 3: return 'Março';
			case 4: return 'Abril';
			case 5: return 'Maio';
			case 6: return 'Junho';
			case 7: return 'Julho';
			case 8: return 'Agosto';
			case 9: return 'Setembro';
			case 10: return 'Outubro';
			case 11: return 'Novembro';
			case 12: return 'Dezembro';
		}
	}
	
	/** 
	 * Devolve os options para um select
	*/
	function meses_select( $selected = 0 )
	{
		$html = '';
		for( $i=1; $i <=12; $i++ )
		{
			$S = ( $i == $selected ? ' selected="selected"' : '' );
			$html .= "\n\t\t<option value=\"$i\"{$S}>".mes($i)."</option>";
		}
		return $html;
	}
	
	//MONTAR A JANELA DE ESCOLHA DE DIA
	function monta_calendario()
	{
		echo "<div style=\"height:178;width:450px;position:absolute;top:25%;left:25%;border:1px solid black;border-collapse:collapse;display:none\" id=\"janela\">
			<div class=\"titulo\" id=\"titulo\" style=\"background:url(".$_SESSION[linkroot].$_SESSION[comum]."imgs/jan_fundo_titulo.jpg);height:18px;\">
				<span id=\"janela_titulo_txt\" style=\"position:absolute;top:2px\">CALEND&Aacute;RIO</span>
				<div style=\"float:right;position:absolute;top:0px;left:94.5%;cursor:pointer;\"><img src=\"".$_SESSION[linkroot].$_SESSION[comum]."imgs/jan_fechar.jpg\" onclick=\"fecharCal('janela')\" alt=\"Fechar\"/></div>
			</div>
			<div id=\"cal\"></div>
		</div>";
	}
	//
	
	/**
	 * @brief Essa função cria uma zebragem na tabela conforme as cores informadas
	 * @using zebragem('Cor Inicial', 'Cor Final', 'Numero de Controle do Loop')
	 * @return String -> Propriedade bgcolor com a cor para zebragem
	 */
	
	function zebragem($cor_ini, $cor_fim, $controle) {
	    // Verifica se é par ou impar
	    $controle = $controle % 2;
	    
	    if ($controle == 0) {
	        $controle++;
	        return "bgcolor='$cor_ini'";
	    } else {
	        $controle = 0;
	        return "bgcolor='$cor_fim'";
	    }
	}
	
	function verIdade($idade = "")
	{
		if($idade != "")
		{
			$sql = "select 
						case when (anos.ano > 0) then anos.ano || ' ano(s)' else ' ' end as a,
						case when (meses.mes > 0) then meses.mes || ' mes(es)' else ' ' end as m,
						case when (dias.dia > 0) then dias.dia || ' dia(s)' else ' ' end as d
					
					from (select extract(years from age(current_date, '$idade')) as ano) as anos,
						(select extract(month from age(current_date, '$idade')) as mes) as meses,
						(select extract(days from age(current_date, '$idade')) as dia) as dias";
			$exec_sql = pg_query($sql);
			$idade_texto = pg_fetch_array($exec_sql);
			//return $idade_texto[0]." ".$idade_texto[1]." ".$idade_texto[2];
			return $idade_texto[0];
		} else {
			return "-";
		}
	}
	
	function verIdadeII($idade = "")
	{
	 	if($idade != "")
		{
			$sql = "select case when (anos.ano > 0) then anos.ano || ' ' else ' ' end as anos
					from (select extract(years from age(current_date, '$idade')) as ano) as anos";
			$exec_sql = pg_query($sql);
			$idade_texto = pg_fetch_array($exec_sql);
			//return $idade_texto[0]." ".$idade_texto[1]." ".$idade_texto[2];
			return $idade_texto[0];
		} else {
			return "-";
		}
		
	}
	
	/**
	 * Transforma um valor entrado em string para float (para calculo do BD ou PHP)
	 * Troca a ',' por '.'
	 *
	 * @param $str
	*/
	function str_to_float( $str )
	{
		
		// se achar '.' e ',' tratar como 'money' : R$ 2.500,25
		if( preg_match( "/^(\d+\.)+\d+\,\d+$/", $str ) )
		{
			$str = preg_replace( "/\./", "", $str );
			$str = preg_replace( "/\,/", ".", $str );
			return (float) $str;
		}
		
		// se achar ',', trocar por '.', transformando em 'float'
		if( preg_match( "/^\d+\,\d+$/", $str ) )
		{
			$str = preg_replace( "/\,/", ".", $str );
			return (float) $str;
		}
		
		return (float) $str;
	}
	
	
	//FUNÇÃO PARA BOTÃO QUE EXECUTA UMA OU MAIS FUNÇÕES EM JAVASCRIPT
	function ChmodBtnJS($id_login, $acao, $href, $funcao)
	{
	
		/**  APENAS PARA PASTA LEF -> tirar o primeiro str_replace depois de colocado na raiz */
		//$GetNameFile=str_replace("lef","",str_replace("/","",$_SERVER["SCRIPT_NAME"]));
		$GetNameFile=str_replace("/","",$_SERVER["SCRIPT_NAME"]);
		
		$SepNameFile = explode(".",$GetNameFile);
		$SepHref = explode(".",$href);
		$arq_name = explode(".",$href);
		$arquivo = $arq_name[0].".php";
		
		//***
		if ($arquivo=="apresenta_produto.php") {
			$GetNameFile="apresenta_produto.php";
			$SepNameFile[0]="apresenta_produto";
		}
		//***/
		
		$perm = pg_fetch_array ( pg_query ("select p.perm_descricao,p.perm_programa,up.nivel_i,up.nivel_a,up.nivel_d,up.nivel_l,up.nivel_b,up.perm_set from usuarios_permissoes as up left join permissoes as p on up.perm_codigo=p.perm_codigo where up.usr_codigo = '$id_login' and p.perm_programa = '$arquivo'"));
	
		if(($acao=="adicionar"/* && $SepHref[0]==$SepNameFile[0]*/))
		{
			if($perm[nivel_i]=="S")
			{
				$Btn = "<a href=\"javascript:;\" onclick=\"$funcao\"><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/".$acao."_on.jpg border=0></a>";
			} else {
				$Btn = "<img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/".$acao."_off.jpg border=0>";
			}
		}
	
		if(($acao=="editar"/* && $SepHref[0]==$SepNameFile[0]*/))
		{
			if($perm[nivel_a]=="S")
			{
				$Btn = "<a href=\"javascript:;\" onclick=\"$funcao\"><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/".$acao."_on.jpg border=0></a>";
			} else {
				$Btn = "<img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/".$acao."_off.jpg border=0>";
			}
		}
		
		if(($acao=="apagar"/* && $SepHref[0]==$SepNameFile[0]*/))
		{
			if($perm[nivel_d]=="S")
			{
			   $Btn = "<a href=\"javascript:;\" onClick=\"if (!confirm('Realmente deseja apagar este registro?')){ return false; } else { $funcao }\"><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/".$acao."_on.jpg border=0></a>";
			} else {
			   $Btn = "<img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/".$acao."_off.jpg border=0>";
			}
		}
	
		//***
		if(($acao=="delpront" && $SepHref[0]==$SepNameFile[0]))
		{
			if($perm[nivel_d]=="S")
			{
				$Btn = "<a href=\"$href&id_login=$id_login\" onClick=\"if (!confirm('Realmente deseja apagar esta consulta?'))  return false\"><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/".$acao."_on.jpg border=0></a>";
			} else {
				$Btn = "<img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/".$acao."_off.jpg border=0>";
			}
		}
	
		if(($acao=="procurar"/* && $SepHref[0]==$SepNameFile[0]*/))
		{
			if($perm[nivel_b]=="S")
			{
				$Btn = "<input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/procurar_on.jpg>";
			} else {
				$Btn = "<img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/".$acao."_off.jpg border=0>";
			}
		}
	
		if(($acao!="procurar" && $acao!="adicionar" && $acao!="editar" && $acao!="apagar" && $acao!="procurar_if" && $acao!="adicionar_if" && $acao!="editar_if" && $acao!="apagar_if" && $acao!="lista_if"/* && $SepHref[0]!=$SepNameFile[0]*/))
		{
			if($perm[perm_set]=="S")
			{
				$Btn = "<a href=$href&id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/".$acao."_on.jpg border=0></a>";
			} else {
			   $Btn = "<img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/".$acao."_off.jpg border=0>";
			}
		}
		
		/**
		 * traz apenas true ou false
		*/
		if($acao=="adicionar_if")
		{
			if($perm[nivel_i]=="S")
			{
			   $Btn = true;
			} else {
			   $Btn = false;
			}
		}
		if($acao=="editar_if")
		{
			if($perm[nivel_a]=="S")
			{
				$Btn = true;
			} else {
				$Btn = false;
			}
		}
		if($acao=="apagar_if")
		{
			if($perm[nivel_d]=="S")
			{
			   $Btn = true;
			} else {
			   $Btn = false;
			}
		}
		if($acao=="procurar_if")
		{
			if($perm[nivel_b]=="S")
			{
			   $Btn = true;
			} else {
			   $Btn = false;
			}
		}
		if($acao=="listar_if")
		{
			if($perm[nivel_l]=="S")
			{
			   $Btn = true;
			} else {
			   $Btn = false;
			}
		}
		/**
		 * -------
		*/
		
		
		return $Btn;
	}
	
	/**
	 * Verifica se o email é valido !
	 * @author http://www.phpit.net/code/valid-email/
	*/
	//function valid_email($email)
	function email_valido( $email )
	{
		// First, we check that there's one @ symbol, and that the lengths are right
		if (!ereg("^[^@]{1,64}@[^@]{1,255}$", $email)) {
			// Email invalid because wrong number of characters in one section, or wrong number of @ symbols.
			return false;
		}
	
		// Split it into sections to make life easier
		$email_array = explode("@", $email);
		$local_array = explode(".", $email_array[0]);
		for ($i = 0; $i < sizeof($local_array); $i++) {
			if (!ereg("^(([A-Za-z0-9!#$%&#038;'*+/=?^_`{|}~-][A-Za-z0-9!#$%&#038;'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$", $local_array[$i])) {
				return false;
			}
		}  
		if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1])) { // Check if domain is IP. If not, it should be valid domain name
			$domain_array = explode(".", $email_array[1]);
			if (sizeof($domain_array) < 2) {
				return false; // Not enough parts to domain
			}
			for ($i = 0; $i < sizeof($domain_array); $i++) {
				if (!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$", $domain_array[$i])) {
					return false;
				}
			}
		}
		return true;
	} 
	//Busca produto
	
	/*function divBuscaProduto()
	        {	
				
	                $div = "<div id='lista_produto' style='text-align:right;display:none;position:absolute;left:2%;border:1px solid black;background:#FFFFFF;max-height:325px;width:770px;'>
	                                        <img src=\"".$_SESSION[linkroot].$_SESSION[comum]."imgs/jan_fechar.jpg\" style=\"cursor:pointer\" onclick=\"$('lista_produto').style.display = 'none';\"/>
	                                        <div style=\"width:100px;display:none;background:red;font-weight:bold;border:1px solid black;\" id=\"lista_prod_carregando\">Carregando...</div>
	                                        <div style='text-align:left;overflow:auto;max-height:300px;width:770px;'>
	                                                <table id='table_produtos' cellspacing='0' cellpading='0' width='80%' style='white-space:nowrap;'></table>
	                                        </div>
	                                </div>";
	                return $div;
	        }*/
			
	function divBuscaProduto(){	
		$div = "<div id='lista_produto' style='text-align:right;display:none;position:absolute;left:2%;border:1px solid black;background:#FFFFFF;max-height:325px;width:770px;'>
					<img src=\"".$_SESSION[linkroot].$_SESSION[comum]."imgs/jan_fechar.jpg\" style=\"cursor:pointer\" onclick=\"$('lista_produto').style.display = 'none';\"/>                                      
					<div style='text-align:left;overflow:auto;max-height:300px;width:770px;'>
                    	<table id='table_produtos' cellspacing='0' cellpading='0' width='80%' style='white-space:nowrap;'></table>
					</div>
				</div>";
		return $div;
	}
	
	/**
	 * Fun<E7><E3>o que cria uma div de busca de pacientes
     */
	function divBuscaPaciente($caminho = ''){
		$div = "<div id='lista_nomes' style='text-align:right;display:none;position:absolute;left:2%;border:1px solid black;background:#FFFFFF;max-height:145px;width:770px;'>
					<img src=\"".$_SESSION[linkroot].$_SESSION[comum]."imgs/jan_fechar.jpg\" style=\"cursor:pointer\" onclick=\"$('lista_nomes').style.display = 'none';\"/>
					<div style=\"width:100px;display:none;background:red;font-weight:bold;border:1px solid black;\" id=\"lista_carregando\">Carregando...</div>
					<div style='text-align:left;overflow:auto;max-height:125px;width:770px;'>
						<table id='table_nomes' cellspacing='0' cellpading='0' width='80%' style='white-space:nowrap;'></table>
					</div>
				</div>";
		return $div;
	}
function divBuscaMedico($caminho = ''){
	$div = "
<div id='lista_medico' style='text-align:right;display:none;position:absolute;left:2%;border:1px solid black;background:#FFFFFF;max-height:325px;width:770px;'>
	<img src=\"".$_SESSION[linkroot].$_SESSION[comum]."imgs/jan_fechar.jpg\" style=\"cursor:pointer\" onclick=\"$('lista_medico').style.display = 'none';\"/>
		<div style=\"width:100px;display:none;background:red;font-weight:bold;border:1px solid black;\" id=\"lista_medico_carregando\">Carregando...
		</div>
		<div style='text-align:left;overflow:auto;max-height:300px;width:770px;'>
			<table id='table_medico' cellspacing='0' cellpading='0' width='80%' style='white-space:nowrap;'></table>
		</div>
</div>";
	return $div;
}
	function divBuscaEnfermeiro($caminho = ''){
		$div = "<div id='lista_enfermeiro' style='text-align:right;display:none;position:absolute;left:2%;border:1px solid black;background:#FFFFFF;max-height:325px;width:770px;'>
					<img src=\"".$_SESSION[linkroot].$_SESSION[comum]."imgs/jan_fechar.jpg\" style=\"cursor:pointer\" onclick=\"$('lista_enfermeiro').style.display = 'none';\"/>
					<div style=\"width:100px;display:none;background:red;font-weight:bold;border:1px solid black;\" id=\"lista_enfermeiro_carregando\">Carregando...</div>
					<div style='text-align:left;overflow:auto;max-height:300px;width:770px;'>
						<table id='table_enfermeiro' cellspacing='0' cellpading='0' width='80%' style='white-space:nowrap;'></table>
					</div>
				</div>";
		return $div;
	}
	function divBuscaEndereco($caminho = ''){
		$div = "<div id='lista_endereco' style='text-align:right;display:none;position:absolute;left:2%;border:1px solid black;background:#FFFFFF;max-height:325px;width:770px;'>
					<img src=\"".$_SESSION[linkroot].$_SESSION[comum]."imgs/jan_fechar.jpg\" style=\"cursor:pointer\" onclick=\"$('lista_endereco').style.display = 'none';\"/>
					<div style=\"width:100px;display:none;background:red;font-weight:bold;border:1px solid black;\" id=\"lista_endereco_carregando\">Carregando...</div>
					<div style='text-align:left;overflow:auto;max-height:300px;width:770px;'>
						<table id='table_endereco' cellspacing='0' cellpading='0' width='80%' style='white-space:nowrap;'></table>
					</div>
				</div>";
		return $div;
	}
	
	function divBuscaMunicipio($caminho = '',$id){
		$div = "<div id='lista_municipios' style='text-align:right;display:none;position:absolute;left:2%;border:1px solid black;background:#FFFFFF;max-height:325px;width:770px;'>
					<img src=\"".$_SESSION[linkroot].$_SESSION[comum]."imgs/jan_fechar.jpg\" style=\"cursor:pointer\" onclick=\"$('$id').style.display = 'none';\"/>
					<div style=\"width:100px;display:none;background:red;font-weight:bold;border:1px solid black;\" id=\"lista_municipios_carregando\">Carregando...</div>
					<div style='text-align:left;overflow:auto;max-height:300px;width:770px;'>
						<table id='table_municipios' cellspacing='0' cellpading='0' width='80%' style='white-space:nowrap;'></table>
					</div>
				</div>";
		return $div;
	}
	
	function divBuscaUnidade($caminho = '',$id){
		$div = "<div id='lista_unidade' style='text-align:right;display:none;position:absolute;left:2%;border:1px solid black;background:#FFFFFF;max-height:325px;width:770px;'>
					<img src=\"".$_SESSION[linkroot].$_SESSION[comum]."imgs/jan_fechar.jpg\" style=\"cursor:pointer\" onclick=\"$('$id').style.display = 'none';\"/>
					<div style=\"width:100px;display:none;background:red;font-weight:bold;border:1px solid black;\" id=\"lista_unidade_carregando\">Carregando...</div>
					<div style='text-align:left;overflow:auto;max-height:300px;width:770px;'>
						<table id='table_unidade' cellspacing='0' cellpading='0' width='80%' style='white-space:nowrap;'></table>
					</div>
				</div>";
		return $div;
	}
			
	function formatarData($dt){
		if (!empty($dt)){
			$data = explode("-", $dt);
			return $data[2]."/".$data[1]."/".$data[0];
		}
	}
	
	function mesAno($dt){
		if (!empty($dt)){
			$data = explode("-", $dt);
			return $data[1]."/".$data[0];
		}
	}
	
	function pegaMesAno($dt, $char){
		if (!empty($dt)){
			$data = explode($char, $dt);
			return $data[1]."/".$data[2];
		}
	}
	
	function menorData($gex_periodo){
		$data = explode("/", $gex_periodo);
		$mes = $data[1];
		$ano = $data[2];
		$mesano = $mes."/".$ano;
		$sql = "select min(graex_data) as mesdata from grade_exame where to_char(graex_data, 'mm/yyyy') = '$mesano'";
		$consulta = pg_query($sql);
		$pegadata = pg_fetch_array($consulta);
		$retorno = !empty($pegadata[mesdata]) ? $pegadata[mesdata] : $gex_periodo;
		return $retorno;
	}
	
	function contaDiasMes($InputDate){
		$dt = explode("/", $InputDate);
		$DayNum = $dt[0];
		$MonthNum = $dt[1];
		$YearNum = $dt[2];
		
		switch ($MonthNum){
			case 4:
			case 6:
			case 9:
			case 11:       //months with 30 Days
				$MonthDays = 30;
				break;
			case 2:
			/**
			****Cálculo de ano Bissexto para Fevereiro: 
			****a regra é que todo ano que é OU divisível por 400 
			****OU divisível por 4 E não divisível por 100 é ano bissexto 
			***/
				$leap1 = $YearNum % 400;
				if ($leap1 == 0) {
					$MonthDays = 29;
				}else{
					$leap2 = $YearNum % 4;
					$leap3 = $YearNum % 100;
					if (($leap2 == 0) & ($leap3 > 0)) {
						$MonthDays = 29;
					}else{
						$MonthDays = 28;
					}
				}
				break;
			default:  //todos os outros meses
			$MonthDays = 31;
			break;
		}
		return $MonthDays;
	}
	
	function cadastraPeriodo($new_gex_periodo, $h_med_codigo, $usr_codigo_cad, $usr_codigo_alt){
		$select = "select * 
					 from grade_exame_mensal 
					where gex_periodo = '$new_gex_periodo' 
					  and med_codigo = '$h_med_codigo'";
		echo "<script>alert('$select')</script>";
		$num = pg_num_rows(pg_query($select));
		if ($num > 0){
			return false;
		}else{	
			$stmt = "INSERT INTO grade_exame_mensal ( 
								 gex_periodo, 
								 med_codigo, 
								 usr_codigo_cad, 
								 usr_codigo_alt
								  ) VALUES ( 
								 '$new_gex_periodo', 
								 ".intval($h_med_codigo).", 
								 ".intval($usr_codigo_cad).", 
								 ".intval($usr_codigo_alt)." )";
		
		   $sql = pg_query($stmt) or die (pg_last_error());
		   return true;
		}
	}
	
	function criaMovimento($tipo, $inv_codigo, $id_login = null, $mov_observacao=null, $mov_num_receita=null){
		if($inv_codigo != null){
			$seleciona = "SELECT inv_data,
								 set_codigo
							FROM inventario
						   WHERE inv_codigo = $inv_codigo";
			$executa = pg_query($seleciona);
			$linha = pg_fetch_array($executa);
			$mov_data = $linha['inv_data'];
			$set_codigo = $linha['set_codigo'];
			$movEntradaSaida = "'I'";
			$for_codigo = '5003';
			$mov_observacao = "MOVIMENTACAO DE INVENTARIO";
			$mov_num_receita = 'null';
		}else{
			$mov_data = date("Y-m-d");
			$selectSetor = "select *from logon  WHERE id_login = $id_login";
			$execSelectSetor = pg_query($selectSetor);
			$row = pg_fetch_array($execSelectSetor); 
			
			$set_codigo = $row['cod_setor'];
			$inv_codigo = 'null';
			$for_codigo = 'null';
			$movEntradaSaida = 'null';
		}
		$select = "SELECT nextval('seq_mov_codigo') as proximaentrada";
		$exec_select = pg_query($select);
		$linha = pg_fetch_array($exec_select);
		$mov_codigo = $linha['proximaentrada'];
		
		$sql = "INSERT INTO movimento
					 (mov_codigo, 
					  mov_data, 
					  mov_tipo, 
					  mov_observacao, 
					  set_entrada, 
					  set_saida, 
					  mov_nr_nota," 
					  .($tipo == 'E' ? "mov_entrada," : "mov_saida,"). 
					  "mov_data_inclusao, 
					  inv_codigo,
					  mov_num_receita, 
					  for_codigo)
		       VALUES 
					 ('$mov_codigo', 
					  '$mov_data', 
					  '$tipo', 
					  '$mov_observacao', 
					  '$set_codigo', 
					  '$set_codigo', 
					  '$mov_codigo', 
					  $movEntradaSaida, 
					  CURRENT_DATE, 
					  $inv_codigo,
					  $mov_num_receita, 
					  $for_codigo);";
		$executa_sql = pg_query($sql);
		if($executa_sql){
			return $mov_codigo;
		}else{
			return "$sql";	
		}
	}
	
	
	
	function insereMovimento($tipo,$movEntradaSaida=null, $id_login = null, $for_codigo = null,$mov_observacao=null, $mov_num_receita=null){
		
		$mov_data = date('d-m-Y');	
		$selectSetor = "SELECT cod_setor
					  FROM logon
					 WHERE id_login = $id_login";
		$execSelectSetor = pg_query($selectSetor);
		$row = pg_fetch_array($execSelectSetor); 
		
		$set_codigo = $row['cod_setor'];
		
		$select = "SELECT nextval('seq_mov_codigo') as proximaentrada";
		$exec_select = pg_query($select);
		$linha = pg_fetch_array($exec_select);
		$mov_codigo = $linha['proximaentrada'];
		
		$sql = "INSERT INTO movimento
					 (mov_codigo, 
					  mov_data, 
					  mov_tipo, 
					  mov_observacao, 
					  set_entrada, 
					  set_saida, 
					  mov_nr_nota," 
					  .($tipo == 'E' ? "mov_entrada," : "mov_saida,"). 
					  "mov_data_inclusao, 
					  inv_codigo,
					  mov_num_receita, 
					  for_codigo)
		       VALUES 
					 ('$mov_codigo', 
					  '$mov_data', 
					  '$tipo', 
					  '$mov_observacao'," 
					  .($tipo == 'E' ? "$set_codigo" : 'null').", " 
					  .($tipo == 'S' ? "$set_codigo" : 'null').", 
					  '$mov_codigo', 
					  '$movEntradaSaida', 
					  CURRENT_DATE, "
					  .($inv_codigo == '' ? 'null' : '$inv_codigo').",
					  $mov_codigo, 
					  $for_codigo);";
		
	  $executa_sql = pg_query($sql);
		if($executa_sql){
			return $mov_codigo;
		}else{
			return "$sql";	
		}
	}
	
	function insereItensMovimento($pro_codigo, $ite_quantidade, $ite_lote, $ite_validade, $mov_codigo,  $id_login=null, $ite_dose=1){
			$select = "SELECT nextval('seq_ite_codigo') as proximaentrada";
			$exec_select = pg_query($select);
			$linha = pg_fetch_array($exec_select);
			$ite_codigo = $linha['proximaentrada'];
			$mov_data = date("Y-m-d");
			$selectSetor = "SELECT cod_setor
					  FROM logon
					 WHERE id_login = $id_login";
		$execSelectSetor = pg_query($selectSetor);
		$row = pg_fetch_array($execSelectSetor); 
			
			$set_codigo = $row['cod_setor'];
		
		$sql = "INSERT INTO itens_movimento
							(mov_codigo,
							 ite_codigo, 
						  	 pro_codigo, 
						  	 ite_quantidade, 
						  	 ite_lote,
						  	 ite_validade,
						  	 ite_consolidado, 
						  	 ite_status,
						  	 ite_dose, 
						  	 ite_vlrunit)
					 VALUES 
							('$mov_codigo',
							 '$ite_codigo', 
						  	 '$pro_codigo', 
						  	 '$ite_quantidade', 
						  	 '$ite_lote',
						  	 '$ite_validade',
						  	 'S', 
						  	 'A',
						  	 $ite_dose,
						  	 verifica_preco('$pro_codigo', '$set_codigo', '$mov_data'));";
		$exec_sql = pg_query($sql);
	if($exec_sql){
			return $ite_codigo;
		}else{
			return "$sql";	
		}
		//msg($id_login,'add',$exec_sql);
	}
	
	
	
	
	function itens_movimento($pro_codigo, $invplq_quantidade, $invplq_lote, $invplq_validade, $codMovimento, $inv_codigo, $id_login=null){
		if($inv_codigo != null){
			$seleciona = "SELECT inv_data,
								 set_codigo
							FROM inventario
						   WHERE inv_codigo = $inv_codigo";
			$executa = pg_query($seleciona);
			$linha = pg_fetch_array($executa);
			$mov_data = $linha['inv_data'];
			$set_codigo = $linha['set_codigo'];
		}else{
			$mov_data = date("Y-m-d");
			$selectSetor = "SELECT set_codigo
						  FROM usuarios
						 WHERE usr_codigo = $id_login";
			$execSelectSetor = pg_query($selectSetor);
			$row = pg_fetch_array($execSelectSetor); 
			
			$set_codigo = $row['set_codigo'];
		}
		
		$sql = "INSERT INTO itens_movimento
							(mov_codigo, 
						  	 pro_codigo, 
						  	 ite_quantidade, 
						  	 ite_lote,
						  	 ite_validade,
						  	 ite_consolidado, 
						  	 ite_status, 
						  	 ite_vlrunit)
					 VALUES 
							('$codMovimento', 
						  	 '$pro_codigo', 
						  	 '$invplq_quantidade', 
						  	 '$invplq_lote',
						  	 '$invplq_validade',
						  	 'S', 
						  	 'A', 
						  	 verifica_preco('$pro_codigo', '$set_codigo', '$mov_data'));";
		$exec_sql = pg_query($sql) or die (pg_last_error());
		return $exec_sql;
		//msg($id_login,'add',$exec_sql);
	}
	
	function attInventario($inv_codigo){
		$movEntrada = criaMovimento('E', $inv_codigo);
		$movSaida = criaMovimento('S', $inv_codigo);
		
		$select = "SELECT * 
					 FROM inventario_produto_lote_quantidade iplq
					 JOIN inventario_produto ip
					   ON ip.invp_codigo = iplq.invp_codigo
					 JOIN inventario i
					   ON i.inv_codigo = ip.inv_codigo
					WHERE i.inv_codigo = $inv_codigo";

		$exec_select = pg_query($select);
		$total = pg_num_rows($exec_select);
		
		if ($total == 0){
			$update = "UPDATE inventario
						  SET inv_acuracia = 100
						WHERE inv_codigo = $inv_codigo";
			$exec_update = pg_query($update);
		}
	
		$contaEntradas = 0;
		$contaSaidas = 0;
	
		while($linha = pg_fetch_array($exec_select)){
			$invp_codigo = $linha['invp_codigo'];
			$invplq_lote = $linha['invplq_lote'];
			$invplq_validade = $linha['invplq_validade'];
			$invplq_quantidade = $linha['invplq_quantidade'];
			$pro_codigo = $linha['pro_codigo'];
			$set_codigo = $linha['set_codigo'];
			
			$sqlEstoque = "SELECT *
							 FROM saldo
							WHERE sal_lote = '$invplq_lote'
							  AND sal_validade = '$invplq_validade'
							  AND pro_codigo = $pro_codigo
							  AND set_codigo = $set_codigo";
			$exec_sql = pg_query($sqlEstoque);
			$numLinhas = pg_num_rows($exec_sql);
			
	
			if ($numLinhas == 0){
				if (itens_movimento($pro_codigo, $invplq_quantidade, $invplq_lote, $invplq_validade, $movEntrada, $inv_codigo)){
					$contaEntradas++;
				}
			}else{
				while ($linhas = pg_fetch_array($exec_sql)){
					$sal_qtde = $linhas['sal_qtde'];
					$set_codigo = $linhas['set_codigo'];
					$sal_data = $linhas['sal_data'];
					$sal_custo = $linhas['sal_custo'];
					if ($invplq_quantidade > $sal_qtde){
						$newQtde = $invplq_quantidade - $sal_qtde;
						if (itens_movimento($pro_codigo, $newQtde, $invplq_lote, $invplq_validade, $movEntrada, $inv_codigo)){
							$contaEntradas++;
						}
					}else if ($invplq_quantidade < $sal_qtde){
						$newQtde = $sal_qtde - $invplq_quantidade;
						if (itens_movimento($pro_codigo, $newQtde, $invplq_lote, $invplq_validade, $movSaida, $inv_codigo)){
							$contaSaidas++;
						}
					}
				}
			}		
		}
		if (($contaEntradas == 0)&&($contaSaidas == 0)){
			$update = "UPDATE inventario
						  SET inv_acuracia = 100
						WHERE inv_codigo = $inv_codigo";
			$exec_update = pg_query($update);
			return "Nenhum movimento foi necessario.";
		}else{
			$acuracia = (($total - ($contaEntradas + $contaSaidas)) / $total) * 100;
			$update = "UPDATE inventario
						  SET inv_acuracia = $acuracia
						WHERE inv_codigo = $inv_codigo";
			$exec_update = pg_query($update);
			return "Foram efetuados $contaEntradas movimento".($contaEntradas > 1 ? "s" : "")." de entrada e $contaSaidas movimento".($contaSaidas > 1 ? "s" : "")." de saida.";
		}
		
		if ($contaEntradas == 0){
			$delete = "DELETE 
						 FROM movimento 
						WHERE mov_codigo = $movEntrada";
			$executa_delete = pg_query($delete);
		}
		if ($contaSaidas == 0){
			$delete = "DELETE 
						 FROM movimento 
						WHERE mov_codigo = $movSaida";
			$executa_delete = pg_query($delete);
		}
	}
	
	function moeda($valor){
		$valor = str_replace(",", "", $valor);
		return number_format($valor, 2, ',', '.');
	}
	
	//funcao moeda2() serve para os casos que no lugar da vírgula, tem que ser ponto.
	function moeda2($valor){
		$valor = str_replace(",", "", $valor);
		return number_format($valor, 2, '.', '');
	}
	
	function numero($valor){
	//	$valor = str_replace(",", "", $valor);
		return number_format($valor, 4, '.', ',');
	}
	
	function contaAgendados($med_codigo, $datainc, $proc_codigo){
		$select = "select count(*) as agendados
					 from agendamento_exame_lista
					where med_codigo = $med_codigo
					  and proc_codigo = $proc_codigo
					  and agexl_data  = '$datainc'";
		$exec = pg_query($select);
		$dados = pg_fetch_array($exec);
		$agendados = $dados['agendados'];
		return $agendados;
	}
	
	function contaVagas($proc_codigo, $med_codigo, $data, $uni_codigo = 0){
		if($uni_codigo != 0){
			$select = "select graexuni_qtde
						 from grade_exame_unidade
						where med_codigo = $med_codigo 
						  and proc_codigo = $proc_codigo
						  and graexuni_data = '$data'
						  and uni_codigo = $uni_codigo";
			$exec = pg_query($select);
			$dados = pg_fetch_array($exec);
			$qtde = $dados['graexuni_qtde'];
		}else{
			$select = "SELECT graex_qtde
						 FROM grade_exame
						WHERE med_codigo = $med_codigo 
						  AND proc_codigo = $proc_codigo
						  AND graex_data = '$data'";
			$exec = pg_query($select);
			$dados = pg_fetch_array($exec);
			$qtde = $dados['graex_qtde'];
		}
		$disponiveis = $qtde - contaAgendados($med_codigo, $data, $proc_codigo);
		return $disponiveis;
	}
	//funcao que cria um arquivo
	function criaArquivo($nome, $msg, $path = "./", $ext = ".xml", $modo = "w"){
		if (!is_dir($path)){
			return "DIR '$path' não existe";
			mkdir($path, 0777);
		}
		$completePath = $path.$nome.$ext; 
	
		$open = fopen($completePath, $modo);//pode ver os parametros do fopen no php.net
		if ($open){
			chmod($completePath, 0777);
		}
		$quebra = chr(13).chr(10);//essa eh a quebra de linha
		fwrite($open, $msg);
		return fclose($open);
	}
	
	//essa função procura array de palavras dentro de uma determinada string.
	function procpalavras ($frase, $palavras) {
		foreach ( $palavras as $key => $value ) {
			$pos = strpos($frase, $value);
			if ($pos !== false) { 
				return true;
			}			
		}
		return false;
	}
	function isDate($date){
		$char = strpos($date, "/")!= false ? "/" : "-";
		$date_array = explode($char,$date);
		if(count($date_array)!=3) return false;
			return checkdate($date_array[1],$date_array[0],$date_array[2])?($date_array[2] . "-" . $date_array[1] . "-" . $date_array[0]):false;
	}
	
	function getPath(){
		if (is_file("auth.php")){
	    	$path = "./"; 
	 	}else if (is_file("../auth.php")){
	    	$path = "../";
	 	}else{
	    	$path = "../../";
	 	}
		return $path;
	}
	
	
	function abrevianome($nomecompleto, $maxtamanho){
	if(strlen($nomecompleto)>$maxtamanho){
		$nome = explode(" ", $nomecompleto);
		$nomecartao = $nome[0]." ";
		for	($i=1; $i<(count($nome))-1; $i++){
			$nomedomeio = $nome[$i];
			if (($nomedomeio == "de") || ($nomedomeio == "da") || ($nomedomeio == "e") || ($nomedomeio == "dos") || ($nomedomeio == "das") || ($nomedomeio == "di")){
				$nomecartao .= $nomedomeio." ";
			} else {
				$reducao = substr($nomedomeio, 0, 1);
				$nomecartao .= $reducao.". ";
			}
		}//fim do for
		$nomecartao .= $nome[$i];
		if (strlen($nomecartao)>$maxtamanho){
		$nomecartao = "Nome completo reduzido ultrapassa o tamanho máximo definido";
		}
		return $nomecartao;
	} else {
		return $nomecompleto;
	}
}
	
function imagem($fileName=NULL){
	$caminho = str_replace('\\', '/',SAUDE);
 	$abs =  $caminho."raiox/server/php/files/";
	$sql = "select lo_export(upl_arquivo,'$abs'||upl_codigo||'-'||'$fileName') from upload_arquivo WHERE upl_arquivo_nome = '$fileName'"; 
	$query = pg_query($sql)or die($sql.pg_last_error());
	//header("Content-type: image/jpg");
	//header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	//header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	//header("Content-Description: PHP Generated Data");
}


function apaga_files($name){
	$caminho = str_replace('\\', '/',SAUDE);
 	$dir =  $caminho."raiox/server/php/files/";
	$handle = opendir($dir);
	while(($file = readdir($handle)) !== false)	{
		if(!in_array($file, $name)){
			unlink($dir.$file);
		}
	}
	
	$dirThumbnail =  $caminho."raiox/server/php/files/thumbnail/";
	$handleThumbnail = opendir($dirThumbnail);
	while(($fileThumbnail = readdir($handleThumbnail)) !== false)	{
		if(!in_array($fileThumbnail, $name)){
			unlink($dirThumbnail.$fileThumbnail);
		}
	}
	
}





?>