<?php
/**
@brief Gera o calendario
Manipulador de dias / calendario
@note 		Depende dos arquivos db.inc.php e funcoes.db.php
*/

/** constante com a qtde de dias */
$QTDE_DIAS	= array( 0, 31, 0, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 );

/** manipulador de Dia */
class Data
{
	/** configuracao */
	public $dia 				= 0;
	public $mes 				= 0;
	public $ano 				= 0;
	public $link				= '';
	public $link_full			= true; // formata a data completa no link, ou somente o dia 
	public $dia_semana 	= -1; // 0: Domingo, 1:Segunda, ..., 6:Sabado
	
	/** Construtor */
	function Data( $dia = null, $mes = null, $ano = null )
	{
		// validacoes
		if( ! empty($ano) && $ano < 1970 )
		{
			printf(  '<strong>ERRO: Ano "%d" inv&aacute;lido !</strong>', $ano );
			exit;
		}
		if( ! empty($mes) && ( $mes < 1 || $mes > 12) )
		{
			printf(  '<strong>ERRO: M&ecirc;s "%d" inv&aacute;lido !</strong>', $mes );
			exit;
		}
		
		$this->dia 		= empty($dia)			? date('d') 		: $dia;
		$this->mes 	= empty($mes) 		? date('m') 	: $mes;
		$this->ano 		= empty($ano) 			? date('Y') 		: $ano;
		
		$this->dia_semana = (int) date( 'w', mktime(0,0,0, $this->mes, $this->dia, $this->ano) );
		
		// fevereiro ?
		global $QTDE_DIAS;
		$QTDE_DIAS[2] = ( $this->ano % 4 == 0 ? 29 : 28 );
		
		if( ! empty($dia) && ( $dia < 1 || $dia > $QTDE_DIAS[ $mes ] ) )
		{
			printf(  '<strong>ERRO: Dia "%d" inv&aacute;lido !</strong>', $mes );
			exit;
		}

	}
	
	/** imprime a data completa/dia formatado ! */
	function toHtml( $full = false )
	{
		return 
			//( $this->link ? "<a href=\"{$this->link}\"".$this->format( $this->link_full )."'>" : '' ) .
			( $this->link ? "<a href=\"{$this->link}\"'>" : '' ) .
			$this->format() .
			( $this->link ? "</a>" : '' );
	}
	function __toString() { return $this->toHtml() ; }
	
	/** formata */
	function format( $full = false )
	{
		if( $full )	return sprintf("%02d/%02d/%04d", $this->dia, $this->mes, $this->ano);
		return sprintf("%02d", $this->dia);
	}
	
}

/** manipulador de Dia :: conectando no banco, verifica se eh feriado !*/
class DataDB extends Data
{
	/** configuracao */
	var $eh_feriado		= false;
	var $feriado			= '';

	function DataDB( $dia = null, $mes = null, $ano = null )
	{
		Data::Data( $dia, $mes, $ano );
		session_start();
		require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
		require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.db.php";
		$stmt 					= "SELECT fer_nome FROM feriado WHERE fer_data = '{$this->dia}/{$this->mes}/{$this->ano}'";
		$this->feriado 	= db_get( $stmt );
		$this->eh_feriado= ( ! empty($this->feriado) );
	}
}

/** manipulador de Calendario */
class Calendarios
{
	/** array contendo as 42 posicoes de uma tabela */
	public $Dias = array(); 
	
	/** configuracao */
	public $mes 		= 0;
	public $ano 		= 0;
	public $ptrPrimeiro	= -1; // primeiro dia do mes
	public $ptrUltimo	= -1; // ultimo dia do mes
	public $spacer		= '--';
	
	/** array contendo o texto (pode aparecer antes ou depois) de um Dia especifico */
	public $Texto 	= array();

	/** "constantes" */
	public $mesExtenso = array( '', 'Janeiro', 'Fevereiro', 'Mar&ccedil;o', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 
			'Setembro', 'Outubro', 'Novembro', 'Dezembro');
	
	/** estilos */
	public $estilos = array( "calendario" => "calend", "domingo" => "dom", "feriado" => "fer", 
			"nav" => "nav", "hoje" => "hoje", "texto" => "txt" );
	
	/** conteudo de navegacao */
	public $navHtml = '';
	
	/** quando ativado, inclui o estilo caso seja o dia de hoje */
	public $mostra_hoje = true;
	
	/** ponteiro interno para "loop" dos dias do calendario */
	public $_contador;
	
	/** Construtor */
	
   public function Calendario( $mes = null, $ano = null)
	{
		global $QTDE_DIAS;
        $this->mes = $mes;
        $this->ano = $ano;

		// validacoes
		if( ! empty($ano) && $ano < 1970 )
		{
			printf(  '<strong>ERRO: Ano "%d" inv&aacute;lido !</strong>', $ano );
			exit;
		}
		if( ! empty($this->mes) && ( $this->mes < 1 || $this->mes > 12) )
		{
			printf(  '<strong>ERRO: M&ecirc;s "%d" inv&aacute;lido !</strong>', $this->mes );
			exit;
		}
		
		$this->mes    = empty($this->mes)   	? date('m') 	: $this->mes;
		$this->ano 	  = empty($this->ano) 		? date('Y') 	: $this->ano;
		
		$this->mes = intval($this->mes);
		$this->ano 	= intval($this->ano);
		
		// gerando array 
		$this->Dias = array_fill( 0, 42, null );

		$D = new Data;

		$dia_1w 	= date( 'w', mktime(0,0,0, $this->mes, 1, $this->ano) );
		$this->ptrPrimeiro = $dia_1w;
		$this->_contador = $this->ptrPrimeiro;
		$dia_atual = 0;
		
		//for( $i = $dia_1w; $i <= $D->qtdeDias[ $this->mes ] + $dia_1w - 1; $i++ )
		for( $i = $dia_1w; $i <= $QTDE_DIAS[ $this->mes ] + $dia_1w - 1; $i++ )
		{
			$dia_atual++;
			$this->Dias[ $i ] = & new DataDB( $dia_atual, $this->mes, $this->ano );
			$this->ptrUltimo = $i;
		}
	}

	/** percorrendo o array "enquanto tem dias..." */
	function reset()
	{ 
		$this->contador = $this->ptrPrimeiro;
		return $this;
	}
	function temDias()
	{
		if( $this->_contador >= $this->ptrPrimeiro && $this->_contador  <= $this->ptrUltimo  )
		{
			//return $this->getDia( ++$this->_contador );
			return $this->Dias[ $this->_contador++ ];
		}
		return null;
	}

	/** devolve a string resultante (tabela) */
	function __toString() { return $this->toHtml() ; }
	function toHtml( $mes = false )
	{
		$html = "\n<table". ( $this->estilos['calendario'] ? " class=\"{$this->estilos['calendario']}\"" : '' ) .">";
		
		if( $mes )
			$html .= "\n\t<caption>{$this->mesExtenso[$this->mes]}, {$this->ano}";
		else
		if( $this->navHtml && ! $mes )
			$html .= "\n\t<tr>\n\t\t<td colspan='7'>";
			
		if( $this->navHtml )
			$html .= "\n\t\t".$this->navHtml."\n\t";

		if( $this->navHtml && ! $mes )
			$html .= "</td>\n\t</tr>" ;
		else 
		if( $mes )
			$html .= "</caption> ";

		$html .=	
			"\n\t<tr>".
			"\n\t\t<th class=\"{$this->estilos['domingo']}\">Dom</th>".
			"\n\t\t<th>Seg</th>".
			"\n\t\t<th>Ter</th>".
			"\n\t\t<th>Qua</th>".
			"\n\t\t<th>Qui</th>".
			"\n\t\t<th>Sex</th>".
			"\n\t\t<th>Sab</th>".
			"\n\t</tr>";
		for( $i=0; $i < 42; $i ++ )
		{
			if( $i % 7 == 0 )	$html .= "\n\t<tr>" ;
			
			$html .= "\n\t\t<td " ;
			$class = false;
			
			if( $this->mostra_hoje && 
				@ $this->Dias[ $i ]->dia == date('d') && 
				$this->Dias[ $i ]->mes == date('m')  && 
				$this->Dias[ $i ]->ano == date('Y') &&
				$this->estilos['hoje'] ) 
			{
				$class = true;
				$html .= " class=\"{$this->estilos['hoje']}";
			}

			//if( $this->Dias[$i]->dia_semana === 0 ) 
			if( $i % 7 == 0 )
				$html .= ( ! $class ? " class=\"{$this->estilos['domingo']}\""  : $this->estilos['domingo'] );
			
			$html .= ($class ? '"' : '' ) .">" ; 

			if( ! $this->Dias[$i] )
			{
				$html .= $this->spacer ;
			}
			else
			{
				//if( $this->Dias[$i]->dia_semana === 0 ) 
				if( $i % 7 == 0 )
					$html .= "<span". ( $this->estilos['domingo'] ? " class=\"{$this->estilos['domingo']}\"" : '' ) . ">";
				
				if( $this->Dias[$i]->eh_feriado ) 
					$html .= "<span". ( $this->estilos['feriado'] ? " class=\"{$this->estilos['feriado']}\"" : '' ) .
					"title=\"{$this->Dias[$i]->feriado}\">";
				
				if( ! empty($this->Texto[ $i ] ) && $this->Texto[$i]['direcao'] == 'L' )
					$html .= "<span class=\"{$this->estilos['texto']}\">{$this->Texto[$i]['texto']}</span>";
				
				//$html .= "<span style='cursor:pointer;height:100%;width:100%;' onclick=\"passarData(this.innerHTML,'$_GET[id]')\">".$this->Dias[$i]->toHtml()."</span>";
				
				$dia_hoje = date("d");
				
				$html .= "<span style='cursor:pointer;height:100%;width:100%;'".
				(!$this->Dias[$i]->eh_feriado && !($i % 7 == 0) && ($this->Dias[$i]->dia >= $dia_hoje) ? "onclick=passarData(this.innerHTML,'$_GET[id]')" : null )
				.">".$this->Dias[$i]->toHtml()."</span>";
				
				if( ! empty($this->Texto[ $i ] ) && $this->Texto[$i]['direcao'] == 'R' )
					$html .= "<span class=\"{$this->estilos['texto']}\">{$this->Texto[$i]['texto']}</span>";
	
				if( $this->Dias[$i]->eh_feriado ) 
					$html .= "</span>";
					
				//if( $this->Dias[$i]->dia_semana === 0 ) 
				if( $i % 7 == 0 )
					$html .= "</span>";
			}
			
			$html .=	
				"</td>".
				( $i % 7 == 6 ? "\n\t</tr>" : "" );
		}
		$html .= "\n</table>";
		return $html;
	}
	
	/** pega o objeto Dia */
	function getDia( $dia )
	{
		// posicao do dia no Array!
		$pos = $dia + $this->ptrPrimeiro - 1;
		
		if( empty($this->Dias[ $pos ]  ) )
		{
			printf(  '<strong>ERRO: Dia "%d" inv&aacute;lido ! ( Per&iacute;odo v&aacute;lido dos dias  %02d a %02d )</strong>', 
				$dia, $this->getPrimeiroDia()->toHtml(), $this->getUltimoDia()->toHtml()  );
			exit;
		}
		return $this->Dias[ $pos ];
	}
	
	
	/** Seta o link */
	function setLink( $dia, $link = true , $completo = true )
	{
		$this->getDia( $dia )->link 		= $link;
		$this->getDia( $dia )->link_full 	= $completo;
	}
	
	/** pega o Primeiro / Ultimo dia do mes */
	function getPrimeiroDia() { return $this->Dias[ $this->ptrPrimeiro ]; }
	function getUltimoDia() { return $this->Dias[ $this->ptrUltimo ]; }
	
	/** pega a data (mm/yyyy) para o proximo mes e do mes anterior */
	function getProxCal() { return date('m/Y', mktime( 0, 0, 0, $this->mes + 1, 1, $this->ano) ); }
	function getAntCal() { return date('m/Y', mktime( 0, 0, 0, $this->mes -1, 1, $this->ano) ); }
	
	/** mostra os links */
	function showNav( $link = null )
	{
		if( ! $link ) $link = $_SERVER['PHP_SELF'] . '?';
		
		$this->navHtml = "<span". ( $this->estilos['nav'] ? " class=\"{$this->estilos['nav']}\"" : '' ) .">".
		"<a href=\"{$link}".$this->getAntCal()."\"> &lt; </a>".
		"&nbsp;  ".
		"<a href=\"{$link}".$this->getProxCal()."\"> &gt; </a>".
		"</span>";
	}
	
	/** pega a posicao no array do calendario */
	function getPos( $dia )
	{
		return ( $dia + $this->ptrPrimeiro - 1 );
	}
	
	/** seta algum texto para mostrar depois / antes do dia 
	 * @var $dia
	 * @var $texto
	 * @var $dir 		DIRECAO : L Left, R right
	*/

    function setTexto( $dia, $texto, $dir = 'R' )
	{
		// posicao do dia no Array!
		$pos = $this->getPos( $dia );
		
		if( empty($this->Dias[ $pos ]  ) )
		{
			printf(  '<strong>ERRO: Dia "%d" inv&aacute;lido ! ( Per&iacute;odo v&aacute;lido dos dias  %02d a %02d )</strong>', 
				$dia, $this->getPrimeiroDia()->toHtml(), $this->getUltimoDia()->toHtml()  );
			exit;
		}
		
		$this->Texto[ $pos ] = array( 'texto' => $texto, 'direcao' => $dir );
	}
	
//}

/** TESTE DE USO **/
/** exemplo **/
//error_reporting( E_ALL | E_STRICT );

public function cal_month_year($mes=null,$ano=null)
    {
print '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
 <!--<title>TESTE CAL</title> -->
<style type="text/css">
    table.calend { font-size: 0.85em;  background: #fff;  color: #000; font-family: Georgia, Verdana, serif; padding: 0; 
	border-collapse: collapse; border-spacing: 0px; width: 450px;}
    table.calend tr { padding: 0; }
    table.calend th { background: #e1e1e1; color: #000;  font-variant: small-caps; }
    table.calend td, table.calend 
    th { width: 15%; padding: 3px;  border-bottom: 1px solid #ccc; border-right: 1px solid #ccc;}
    table.calend td { text-align: center;  }
    table.calend span { padding: 1px; }
    table.calend span.fer { font-style: italic; text-decoration: line-through; font-weight: bold; cursor: help;}
    table.calend span.dom { color: #f00; background: #fff; font-weight: bold; }
    table.calend td.dom, table.calend th.dom { border-left: 1px solid #ccc; }
    table.calend td.hoje { background-color: #eaeaea; }
    table.calend caption { font-weight: bold; text-align: center; padding: 3px; }
    table.calend a  { color: #00a; background: transparent;  text-decoration: underline; }
    table.calend a:hover  { color: #f00; background: transparent;  text-decoration: none; }
    table.calend span.nav { margin-left: 20px; }
    table.calend span.nav a { font-weight: bold; }
    table.calend span.txt { font-size: 0.95em; margin: 3px; font-weight: bold;}
</style>
</head>
<body>
<!-- <h1>Teste calendario</h1> -->
';
/*

*/ 
   $this->Calendario($mes,$ano);       
   $this->Calendario($mes,$ano);       
   print "\n\n </body></html>";
   return $this ;
 
 }

}
/* */

 class  text_calendario extends Calendarios
 {
     
     function box_calendario()
     {
        //echo $this->mes;
        //ho $this->ano;
        $mes = $_GET[mes];
        $ano = $_GET[ano];
        $this->cal_month_year($mes,$ano);
        return $this;

     }   

 } 
    
      $calendario = new text_calendario();
      echo $calendario->box_calendario(); 

?>
