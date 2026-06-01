<?php
@ session_start();
@ $id_login = base64_decode($_SESSION["b80bb7740288fda1f201890375a60c8f"]);

header("Content-type: text/html; charset=ISO-8859-1");
//acrescentado Claudia 19/03/07 - 20h02header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past

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


//
// -> Arquivos de Funções
//

// inclui algumas funcoes especificas para o banco
	include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";

// ------------------------------------------------------------------>
/**
* @brief Cabecario das telas
*/
function cabecario( $hotkey = false )
{
	include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	echo 
	'<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" '.
		'"http://www.w3.org/TR/html4/loose.dtd">'."\n".
	"<html>\n".
	"<head>\n".
	"<link href='../estilo.css' rel='stylesheet' type='text/css'>\n".
	"<link href='estilo_janela.css' rel='stylesheet' type='text/css'>\n".
	"<link href='estilo.css' rel='stylesheet' type='text/css'>\n".
	"<link href='../estilo_janela.css' rel='stylesheet' type='text/css'>\n".
	"<script language='JavaScript' type='text/javascript' src='funcoes.js'></script>".
	"<script language='JavaScript' type='text/javascript' src='ajax_motor.js'></script>".
	"<title>SSP - Sistema de Saude Publica</title>\n".
	"</head>\n".
	"<body bgcolor='#F4F2E7' ".($hotkey ? 'onkeydown="return hotkey(event);"' : '' )." >\n";
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
   return ro
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

-->
</script>

<?php
} //-> FECHAMENTO DA FUNCAO CABECARIO

//------------------------------------------------------------------>
//-> Mensagens dos registros
//------------------------------------------------------------------>

function msg($id_login,$acao,$sql) {
	//$GetNameFile=str_replace("/","",$_SERVER["SCRIPT_NAME"]);
	$GetNameFile = $_SERVER["SCRIPT_NAME"];
     switch ($acao) {
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
}

     if($sql) {
	  echo "<br><br><br><br><br><br><br><br><br><br><br><br><br>
	        <table height=100 width=50% align=center cellspacing=0 cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>
	         <tr bgcolor=f9f9f9>
	           <td align=center>$resp_ok</td>
	         </tr>
	        </table><br>";
	echo "<SCRIPT LANGUAGE=\"JavaScript\">
	          setTimeout(\"location='$GetNameFile?id_login=$id_login'\", 2000);
	      </SCRIPT>";
     } else {
	  echo "<br><br><br><br><br><br><br><br><br><br><br><br><br>
	        <table height=100 width=50% align=center cellspacing=0 cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>
	         <tr bgcolor=f9f9f9>
	           <td align=center>$resp_erno</td>
	         </tr>
	        </table><br>";
	echo "<SCRIPT LANGUAGE=\"JavaScript\">
	          setTimeout(\"location='$GetNameFile?id_login=$id_login'\", 2000);
	      </SCRIPT>";
     }
}

//------------------------------------------------------------------>
//-> Cabecario de Relatorios
//------------------------------------------------------------------>
 function cabecario_rel($titulo,$dt_i,$dt_f,$unidade) {

   include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
   echo "<body bgcolor=FFFFFF topmargin=0 leftmargin=0>
	  <script>
	   function imprimir() {
	   window.print();
	</script>";
 echo "<table width=100% cellspacing=3 cellpadding=0 border=0 bgcolor=eeeeee height=40>
        <tr>
         <td width=90%><font size=3><b>".strtoupper($titulo)."</b></font></td>
         <td><a href='#' OnClick='imprimir()'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/print_on.jpg border=0></a></td>
        </tr>
        </table><br>";
echo "<table width=100% cellspacing=0 cellpadding=2 border=0 align=center>
		<tr>
	     <td width=200><font size=2 face=courier>GESTÃO PÚBLICA DE SAÚDE</font></td>
	     <td><font size=2 face=courier>".date("d/m/Y h:i:s")."</font></td>
	    </tr>
	    <tr>
	     <td colspan=2><font size=2 face=courier>".strtoupper($titulo)."</font></td>
	    </tr>
	    <tr>
	     <td colspan=2><font size=2 face=courier>PERIODO: $dt_i A $dt_f</font></td>
	    </tr>
	    <tr>
	     <td colspan=2><font size=2 face=courier>UNIDADE: $unidade</font></td>
	    </tr>
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
  $row=pg_fetch_array(pg_query("select *from usuarios where usr_codigo='$id_login'"));
  $data=date("d_m_Y");
  $fp=fopen("log/gps_".$data.".log","ab");
  $dt=date("d/m/Y H:i");
  fputs($fp,"<font color=blue>$dt</font> - <font color=red>$row[usr_nome]</font> :: $click <br>\n");
  fclose($fp);
}


function vSQL($sql,$print) {
 if($print>="1") {
   echo "------------------------------------------------------------------------------------<br><br>";
   echo "DUMP DA SQL<br><br>";
   echo $sql;
   echo "<br><br>------------------------------------------------------------------------------------";
   }
  if(!($db = pg_connect("host=localhost dbname=castro user=postgres password=gvw60!@.5A")))
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

/** Devolve o nome do mes
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

/** Devolve os options para um select
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

//ADICIONADO POR RENATO -> APENAS PARA MONTAR A JANELA DE ESCOLHA DE DIA
function monta_calendario()
{
	echo "<div style=\"height:178;width:450px;position:absolute;top:25%;left:25%;border:1px solid black;border-collapse:collapse;display:none;z-index:5\" id=\"janela\">
		<div class=\"titulo\" id=\"titulo\" style=\"background:url(".$_SESSION[linkroot].$_SESSION[comum]."imgs/jan_fundo_titulo.jpg);height:18px;\">
			<span id=\"janela_titulo_txt\" style=\"position:absolute;top:2px\">CALEND&Aacute;RIO</span>
			<div style=\"float:right;position:absolute;top:0px;left:94.5%;cursor:pointer;\"><img src=\"".$_SESSION[linkroot].$_SESSION[comum]."imgs/jan_fechar.jpg\" onclick=\"fecharCal('janela')\" alt=\"Fechar\"/></div>
		</div>
		<div id=\"cal\"></div>
	</div>";
}
//

//ADICIONADO POR RENATO -> APENAS PARA BUSCAR A IDADE COM ANOS, MESES E DIAS
	function verIdade($idade)
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
		return $idade_texto[0]." ".$idade_texto[1]." ".$idade_texto[2];
		/*$sql = "SELECT age(current_date, '$idade') as idade";
		$exec_sql = pg_query($sql);
		$idade_texto = pg_fetch_array($exec_sql);
		
		$idade_texto =  
		
		$idade = explode(" ", $idade_texto[0]);
		$anos = $idade[0];
		$years = $idade[1];
		$meses = $idade[2];
		$mons = $idade[3];
		$dias = $idade[4];
		if($years[0] == "y")
		{
			$idade = $anos."(A) ".($meses > 0 ? $meses."(M) " : null).($dias > 0 ? $dias."(D)" : null);
		}else if($years[0] == "m") {
			$idade = $anos."(M) ".($dias > 0 ? $dias."(D)" : null);
		} else if($years[0] == "d") {
			$idade = $dias."(D) ";
		}*/
		//return $idade;
	}
	

//

    //FUNÇÃO PARA BOTÃO QUE EXECUTA UMA OU MAIS FUNÇÃO EM JAVASCRIPT
    function ChmodBtnJS($id_login, $acao, $href, $funcao) {

	    /**  APENAS PARA PASTA LEF -> tirar o primeiro str_replace depois de colocado na raiz */
	    //$GetNameFile=str_replace("lef","",str_replace("/","",$_SERVER["SCRIPT_NAME"]));
		$GetNameFile=str_replace("/","",$_SERVER["SCRIPT_NAME"]);
		
	    $SepNameFile = explode(".",$GetNameFile);
	    $SepHref = explode(".",$href);
	    $arq_name = explode(".",$href);
	    $arquivo = $arq_name[0].".php";

        //***
        //---- OLHA ISTO AQUI -  FOI EU QUEM FEZ --------------
        if ($arquivo=="apresenta_produto.php") {
            $GetNameFile="apresenta_produto.php";
            $SepNameFile[0]="apresenta_produto";
           }
        //-----------------------------------------------------
        //***/
        
        $perm = pg_fetch_array ( pg_query ("select p.perm_descricao,p.perm_programa,up.nivel_i,up.nivel_a,up.nivel_d,up.nivel_l,up.nivel_b,up.perm_set from usuarios_permissoes as up left join permissoes as p on up.perm_codigo=p.perm_codigo where up.usr_codigo = '$id_login' and p.perm_programa = '$arquivo'"));
    
        if(($acao=="adicionar" AND $SepHref[0]==$SepNameFile[0]))
        {
            if($perm[nivel_i]=="S")
            {
                $Btn = "<a href=\"javascript:;\" onclick=\"$funcao\"><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/".$acao."_on.jpg border=0></a>";
            } else {
                $Btn = "<img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/".$acao."_off.jpg border=0>";
            }
        }
   
        if(($acao=="editar" AND $SepHref[0]==$SepNameFile[0]))
        {
            if($perm[nivel_a]=="S")
            {
                $Btn = "<a href=\"javascript:;\" onclick=\"$funcao\"><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/".$acao."_on.jpg border=0></a>";
            } else {
                $Btn = "<img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/".$acao."_off.jpg border=0>";
            }
        }
        
        if(($acao=="apagar" AND $SepHref[0]==$SepNameFile[0]))
        {
            if($perm[nivel_d]=="S")
            {
               $Btn = "<a href=\"javascript:;\" onClick=\"if (!confirm('Realmente deseja apagar este registro?')){ return false; } else { $funcao }\"><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/".$acao."_on.jpg border=0></a>";
            } else {
               $Btn = "<img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/".$acao."_off.jpg border=0>";
            }
        }

        //***
        if(($acao=="delpront" AND $SepHref[0]==$SepNameFile[0]))
        {
            if($perm[nivel_d]=="S")
            {
                $Btn = "<a href=\"$href&id_login=$id_login\" onClick=\"if (!confirm('Realmente deseja apagar esta consulta?'))  return false\"><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/".$acao."_on.jpg border=0></a>";
            } else {
                $Btn = "<img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/".$acao."_off.jpg border=0>";
            }
        }

        if(($acao=="procurar" AND $SepHref[0]==$SepNameFile[0]))
        {
            if($perm[nivel_b]=="S")
            {
                $Btn = "<input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/procurar_on.jpg>";
            } else {
                $Btn = "<img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/".$acao."_off.jpg border=0>";
            }
        }

        if(($acao!="procurar" AND $acao!="adicionar" AND $acao!="editar" AND $acao!="apagar" AND $SepHref[0]!=$SepNameFile[0]))
        {
            if($perm[perm_set]=="S")
            {
                $Btn = "<a href=$href&id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/".$acao."_on.jpg border=0></a>";
            } else {
               $Btn = "<img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/".$acao."_off.jpg border=0>";
            }
        }
        //***/
        return $Btn;
    }
    
	/*
	 Função que cria uma div de busca de pacientes
	*/
	function divBuscaPaciente()
	{
		$div = "<div id='lista_nomes' style='text-align:right;display:none;position:absolute;left:2%;border:1px solid black;background:#FFFFFF;max-height:325px;width:770px;'>
					<img src=\"".$_SESSION[linkroot].$_SESSION[comum]."imgs/jan_fechar.jpg\" style=\"cursor:pointer\" onclick=\"$('lista_nomes').style.display = 'none';\"/>
					<div style=\"width:100px;display:none;background:red;font-weight:bold;border:1px solid black;\" id=\"lista_carregando\">Carregando...</div>
					<div style='text-align:left;overflow:auto;max-height:300px;width:770px;'>
						<table id='table_nomes' cellspacing='0' cellpading='0' width='80%' style='white-space:nowrap;'></table>
					</div>
				</div>";
		return $div;
	}
	
	/*
	 Função que cria uma div de busca de pacientes
	*/
	function divBuscaMedicamento()
	{
		$div = "<div id='lista_medicamentos' style='text-align:right;display:none;position:absolute;left:2%;border:1px solid black;background:#FFFFFF;max-height:250px;width:770px;'>
					<img src=\"".$_SESSION[linkroot].$_SESSION[comum]."imgs/jan_fechar.jpg\" style=\"cursor:pointer\" onclick=\"$('lista_medicamentos').style.display = 'none';\"/>
					<div style=\"width:100px;display:none;background:red;font-weight:bold;border:1px solid black;\" id=\"lista_carregando_medicamentos\">Carregando...</div>
					<div style='text-align:left;overflow:auto;max-height:225px;width:770px;'>
						<table id='table_medicamentos' cellspacing='0' cellpading='0' width='80%' style='white-space:nowrap;'></table>
					</div>
				</div>";
		return $div;
	}
	
	function mensagem($acao, $sql)
	{
		switch ($acao) {
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
		}
		
		if($sql) {
			echo "<br><br><br><br><br><br><br><br><br><br><br><br><br>
			  <table height=100 width=50% align=center cellspacing=0 cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>
			   <tr bgcolor=f9f9f9>
				 <td align=center>$resp_ok</td>
			   </tr>
			  </table><br>";
		} else {
			echo "<br><br><br><br><br><br><br><br><br><br><br><br><br>
			  <table height=100 width=50% align=center cellspacing=0 cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>
			   <tr bgcolor=f9f9f9>
				 <td align=center>$resp_erno</td>
			   </tr>
			  </table><br>";
		}
	}
	
?>

