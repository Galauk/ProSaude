<?php
session_start();
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
verauth($id_login);
require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
cabecario();
//var_dump($_REQUEST);
function ultimoDiaMes($data){
       $ex = explode("/",$data);
       $mes = $ex[1];
       $ano = $ex[2];
       $dia = $ex[0];
       if($dia>=30) {
		  $dia = date("t", mktime(0, 0, 0, $mes, 1, $ano)); 
       } else {
       	  $dia = $dia;
       }
    return $dia."/".$mes."/".$ano;
  }
?>
<!--<p id="t"></p>-->

<script language="JavaScript" type="text/javascript" src="ajax_motor.js"></script>
<script language="JavaScript" type="text/javascript" src="funcoes.js"></script> 
<link rel="stylesheet" href="/WebSocialSaude/lib/themes/base/jquery.ui.all.css">
<link rel="stylesheet" href="/WebSocialSaude/lib/themes/ui-lightness/jquery-ui-1.8.10.custom.css">
<script type="text/javascript" src="/WebSocialComum/library/js/jquery-1.6.2.min.js"></script>
<script type="text/javascript" src="/WebSocialSaude/lib/ui/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="/WebSocialComum/library/js/jquery.shortcuts.min.js"></script>
<script type="text/javascript" src="/WebSocialComum/library/js/jquery.buscar.js"></script>
<script type="text/javascript">
$(function(){
	$("#medicamentos input").buscar({
		tipo:'produtosPorSetor',
		callback: function(e,ui){
			set_codigo = $("#set_codigo").val();
			mov_data = $("#mov_data").val();
			//alert(mov_data);
			calcula_preco(set_codigo, mov_data)
		},
		template : function(ul, item) {
			return $("<li></li>").data("item.autocomplete", item).append(
					"<a>" + item.label + "</a>").appendTo(ul);
		}
	});
});
function ajaxInit() {
	var req;
	try
	{
		req = new ActiveXObject("Microsoft.XMLHTTP");
	}
	catch(e)
	{
		try
		{
			req = new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch(ex)
		{
			try
			{
				req = new XMLHttpRequest();
			}
			catch(exc)
			{
				alert("Esse browser não tem recursos para uso do Ajax");
				req = null;
			}
		}
	}
    return req;
}

//Função que adiciona os campos;
function CampoValidade(event) {
	
	function criarCampo(txt){		
		d = document.getElementById('validade');
		var resp = txt.split("|"); 
		
		d1 = document.getElementById('dose');
		if (resp[0] == "S"){
			d.style.display = 'block';
			// var texto = "<td width='40' align='right'>Validade:</td><td><input type='text' name='ite_validade' id='ite_validade' class='box' size='20' onKeypress=\"return Ajusta_Data(this, event);\"></td>";
			// d.innerHTML = d.innerHTML+texto;
		}
		else {
			d.style.display = 'none';
		}
		if (resp[1] == "S"){
			d.style.display = 'block';
			d1.style.display = 'block';
			// var texto = "<td width='40' align='right'>Validade:</td><td><input type='text' name='ite_validade' id='ite_validade' class='box' size='20' onKeypress=\"return Ajusta_Data(this, event);\"></td>";
			// d.innerHTML = d.innerHTML+texto;
		}
		else {
			d1.style.display = 'none';
		}
	}

	var codigoProduto = document.inclui_item.pro_codigo.value;
	url = "buscaValidade.php?codProduto="+codigoProduto;
	ajax_tudo(url, criarCampo);
}
function calcula_preco( set_entrada, mov_data )
{	
	CampoValidade(this);
	var pro_codigo 		= document.getElementById('pro_codigo').value;
	
	var ite_quantidade 	= document.getElementById('ite_quantidade').value;

	if( ! pro_codigo || ! ite_quantidade ) return;

	var endereco = 'ajax/operacao/itens_entrada_ajax.php?pro_codigo='+pro_codigo+'&set_entrada='+set_entrada+'&mov_data='+mov_data+'&qtde='+ite_quantidade;
//alert(endereco);
	ajax = ajaxInit();

	if(ajax)
	{
		ajax.open("GET", endereco , true);
		ajax.onreadystatechange = function()
		{
			if(ajax.readyState == 4)
			{
				if(ajax.status == 200)
				{
					var resp 	= new String(ajax.responseText);

					var arr		= resp.split(';');
					/*if(document.inclui_item.totalitem.value != "")
					{*/
						document.getElementById('ite_vlrunit').value = arr[0];
						document.getElementById('totalitem_r').value = arr[1];
						document.getElementById('totalitem').value 	 = arr[1];
					//}
				}
				else
				{
					alert('Erro:' + ajax.statusText);
					//document.location.href = endereco;
				}
			}
		}
		ajax.send(null);
	}
}
//validade = new String();
var validade = null;
var controle = 0;

// uso ????
/*function verificar()
{
	controle++;
 	ajax = ajaxInit();
	pro_codigo =  document.getElementById('pro_codigo').value;
	url = "buscarValidade.php?pro_codigo="+pro_codigo;
	ajax.open("GET", url, true);
	if(ajax)
	{
		ajax.onreadystatechange = function()
		{
			if(ajax.readyState == 4)
			{
				//if(ajax.status == 200)
				//{
					//alert(ajax.responseText);
					validade = ajax.responseText;
				//} else {
					//alert('Erro:' + ajax.statusText);
				//}
			}
		}
		ajax.send(null);
	}
}*/


function notnull()
{
	var pro_codigo =  document.getElementById('pro_codigo').value;
	var url = "buscarValidade.php?pro_codigo="+pro_codigo;
	ajax_tudo( url, notnull_b );
	return false;
}

function notnull_b( VAL )
{
	pro_codigo = document.getElementById("pro_codigo");
//	alert(a.value);
	 if (pro_codigo.value == 0) {
        alert ('O produto deve ser escolhido');
        document.inclui_item.medicamentos.focus();
        return false;
     }
    if (document.inclui_item.ite_quantidade.value == '') {
       alert ('A quantidade deve ser digitada');
       document.inclui_item.ite_quantidade.focus();
       return false;
    }
   
    if (document.inclui_item.ite_quantidade.value == 0) {
       alert ('A quantidade nao pode ser zero');
       document.inclui_item.ite_quantidade.focus();
       return false;   	
	 }
    if (document.inclui_item.totalitem.value == '') {
       alert ('O valor total deve ser digitado');
       document.inclui_item.ite_vlrunit.focus();
       return false;
    }
    /*if (document.inclui_item.totalitem.value == 0) {
       alert ('O valor total nao pode ser zero');
       document.inclui_item.ite_vlrunit.focus();
       return false;
    }*/
    if (document.inclui_item.ite_vlrunit.value == '') {
       alert ('O valor unitario  deve ser digitado');
       document.inclui_item.ite_vlrunit.focus();
       return false;
    }
    /*if (document.inclui_item.ite_vlrunit.value == 0) {
       alert ('O valor unitario  nao pode ser zero');
       document.inclui_item.ite_vlrunit.focus();
       return false;
    }*/

	if( VAL == 'S')
	{
		if(document.inclui_item.ite_lote.value == '')
		{
			alert('O lote deve ser digitado');
			document.inclui_item.ite_lote.focus();
			return false;
		}

//		if(document.inclui_item.ite_dose.value == '')
//		{
//			alert('A Dose deve ser digitada');
//			document.inclui_item.ite_dpse.focus();
//			return false;
//		}
		if(document.inclui_item.ite_validade.value == '')
		{
			alert('A validade deve ser digitada');
			document.inclui_item.ite_validade.focus();
			return false;
		}
		
		
        if( ! verifica_validade_lote() )
        {
            return false;
        }		//return false;
	}
	document.getElementById("inclui_item").submit();
}


</script>

<script>
function calcula()
{
    document.inclui_item.totalitem.value = document.inclui_item.ite_quantidade.value * 
                                      document.inclui_item.ite_vlrunit.value; 
}
function calcula2()
{
    //document.inclui_item.totalitem.value = document.inclui_item.totalitem.value.replace(',','.');
    valor_total = document.inclui_item.totalitem.value.replace('.','');
    valor_total = valor_total.replace(',', '.');
    n = new Number();
    n = valor_total;
    x = new Number();
    x = document.inclui_item.ite_quantidade.value;
    r = new Number(n/x);
    //r = r.toFixed(2);
    //r = r.toPrecision(2);

    document.inclui_item.ite_vlrunit.value = r;

    //alert(n+' - '+x+' - '+r);
    //alert(r.toFixed(2));
    //return false;
    /*document.inclui_item.ite_vlrunit.value = document.inclui_item.totalitem.value / \n
                                      document.inclui_item.ite_quantidade.value; \n*/

}
function calcula_altera()
{
    valor_total = document.altera_item.totalitem.value.replace('.','');
    valor_total = valor_total.replace(',', '.');
    n = new Number();
    n = valor_total;
    x = new Number();
    x = document.altera_item.ite_quantidade.value;
    r = new Number(n/x);
    //r = r.toFixed(2);
    //r = r.toPrecision(2);

    document.altera_item.ite_vlrunit.value = r;
    /*document.altera_item.ite_vlrunit.value = document.altera_item.totalitem.value / \n
                                      document.altera_item.ite_quantidade.value; \n*/
}

function verifica_total_inclusao()
{
    document.altera_item.totalitem.value = document.altera_item.ite_quantidade.value * 
                                      document.altera_item.ite_vlrunit.value;
}

function notnull2()
{
	alert("val="+validade);
    if (document.inclui_item.ite_quantidade.value == '') {
       alert ('A quantidade deve ser digitada');
       document.inclui_item.ite_quantidade.focus();
       return false;
    }
   /* if (document.inclui_item.ite_quantidade.value == 0) {
       alert ('A quantidade nao pode ser zero');
       document.inclui_item.ite_quantidade.focus();
       return false;
    }*/
    if (document.inclui_item.ite_vlrunit.value == '') {
       alert ('O valor unitario  deve ser digitado');
       document.inclui_item.ite_vlrunit.focus();
       return false;
    }
    if (document.inclui_item.ite_vlrunit.value == 0) {
       alert ('O valor unitario  nao pode ser zero');
       document.inclui_item.ite_vlrunit.focus();
       return false;
    }

	if(validade == 'S')
	{

		if(document.inclui_item.ite_lote.value == '')
		{
			alert('O lote!@ deve ser digitado');
			document.inclui_item.ite_lote.focus();
			return false;
		}

		if(document.inclui_item.ite_validade.value == '')
		{
			alert('A validade deve ser digitada');
			document.inclui_item.ite_validade.focus();
			return false;
		}

        if( ! verifica_validade_lote() )
        {
            return false;
        }


		//return false;
	}
	if(validade != '')
	{
		return true;
	} else {
		return false;
	}
}


// verifica se a data de validade eh maior que a data de hoje
function verifica_validade_lote()
{
	//alert();
    var data_arr = $("#ite_validade").val().split(/[\/\\\.\-\_\s]/);
    var data_hj = new Date( ".date('Y').", ".date('m').", ".date('d')." );

    if( data_arr.length != 3 )
    {
        alert( 'A data digitada deve ser valida !' );
        return false;
    }

    var data_re = new Date( data_arr[2], data_arr[1], data_arr[0] );

    if( ( data_hj.getTime() > data_re.getTime() ) )
    {
        alert( 'Lote nao pode ter validade menor que o dia de hoje !' );
        return false;
    }
    return true;


}

//mov_codigo adicionado por dilee para consolidar os itens caso os valores sejam iguais
function verificatotal(mov_codigo,mov_entrada)
{
	alert ('asdad')
	alert(mov_codigo);
	alert(mov_entrada);
	exit;
	if (document.getElementById('validade').innerHTML != ''){
		if(  $F('ite_validade').length > 0 && ! verifica_validade_lote() )
			return false;
	}
	
	var v1 = Number( document.dados_nota.vlrtotal.value );
	var v2 = Number( document.dados_nota.vlrtotalinfo.value );
    var v3 = document.inclui_item.mov_entrada.value;

    var dif = Math.abs( parseInt(v1) - parseInt(v2) );

    if ((document.dados_nota.vlrtotal.value != document.dados_nota.vlrtotalinfo.value) && (v3 == 'E'))
    {
       alert ('Valor total digitado diferente do valor total informado');
       return false;
    } else {
	    if(document.dados_nota.vlrtotal.value == document.dados_nota.vlrtotalinfo.value){
	        url = 'salvarItensConsolidado.php?mov_codigo='+mov_codigo;
	        alert( url );
	        ajax_tudo(url, resposta);
	        return false;
	    }else{
	   	 alert ('Valor total digitado diferente do valor total informados');
	   	 return false;
	    }
    }
}

function resposta( txt )
{
    //alert(txt);
    if(txt == 'true')
    {
        location.href = 'entrada.php?id_login=$id_login';
        return true;
    } else {
        alert('Erro ao consolidar movimentacao');
        return false;
    }
}


</script>
<?php

//Deve ser feita uma rotina para conferencia do total da nota em JavaScript, quando o usuario finalizar a nota
//termina, mas avisar o usuario e gravar no log este aviso.


$stmt = "SELECT uni_codigo FROM usuarios WHERE usr_codigo = $id_login";
$stmt = db_query($stmt);
$dados = pg_fetch_array($stmt);

//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
//------------------------------------------------------------------>
if ( empty($action) || ($acao == 'form_inclui_item') )
{
	//echo "<pre>".print_r($_POST,true);

  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	       <form name=dados_nota method=post action='' onSubmit=\"return notnull()\">
		<input type=hidden name=mov_codigo value=$mov_codigo>

	 <tr>
	  <td>
	   <fieldset>
	    <legend>Dados da Entrada</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>";
    $sql =  pg_query("select mov_codigo, 
    				   		 to_char(mov_data, 'dd/mm/yyyy') as mov_data, 
    				         mov_tipo, 
    				         for_codigo,
                       		 mov_desconto, 
                       		 mov_observacao,
                        case when mov_entrada = 'E' then 'Nota Fiscal'
                             when mov_entrada = 'A' then 'Ajuste'
                             when mov_entrada = 'M' then 'Emprestimo'
                             when mov_entrada = 'I' then 'Inventario'
                             when mov_entrada = 'D' then 'Doacao'
                             when mov_entrada = 'P' then 'Permuta'
                             when mov_entrada = 'O' then 'Outras Entradas'
                         end as tipoentrada,
                             set_entrada, 
                             mov_nr_nota, 
                             to_char(mov_dt_nota, 'dd/mm/yyyy') as mov_dt_nota,
                             mov_total_nota, 
                             mov_acrescimo, 
                             mov_tipo_acrescimo, 
                             mov_entrada
             			from movimento
             where  mov_codigo = '$mov_codigo'");
   $row=pg_fetch_array($sql);
   $sqlfornecedor=pg_query("select for_codigo, for_nome, for_nome_fantasia
                from fornecedor where for_codigo = '$row[for_codigo]'");
   $rowfornecedor = pg_fetch_array($sqlfornecedor);
   $sqlsetor = pg_query("select * from setor where set_estoque = 'S' and set_codigo = '$row[set_entrada]'");
   $rowsetor = pg_fetch_array($sqlsetor);

   /*$stmt = "select round(cast (sum(ite_vlrunit * ite_quantidade) - coalesce(mov_desconto,0) +
          coalesce(mov_acrescimo,0) as numeric), 2) as total, ite_vlrtotal
                from movimento, itens_movimento
                where movimento.mov_codigo = itens_movimento.mov_codigo
                and   movimento.mov_codigo = '$mov_codigo'
                group by mov_desconto, mov_acrescimo";*/

   $stmt = "select round(cast (sum(ite_vlrtotal) - coalesce(mov_desconto,0) +
          coalesce(mov_acrescimo,0) as numeric), 2) as total
                from movimento, itens_movimento
                where movimento.mov_codigo = itens_movimento.mov_codigo
                and   movimento.mov_codigo = '$mov_codigo'
                group by mov_desconto, mov_acrescimo";

   $sqltotal=pg_query($stmt);
   $rowtotal = pg_fetch_array($sqltotal);
   $vlrtotal = formata_valor($rowtotal['total']);
   $vlrdesconto = formata_valor4($row['mov_desconto']);
   $vlrtotaldigitado = ($row['mov_total_nota']);
 echo "
                <tr>
	          <td width=70>Dados do Fornecedor</td>
	          <td width=150 colspan=5><input type=text readonly class=box name=for_nome size=100 value='$rowfornecedor[for_nome]'></td>
               </tr>
          <tr>
	          <td width=70>Unidade</td>
	          <td width=70><input type=text readonly class=box name=uni_desc size=40 value='$rowsetor[set_nome]'></td>
	          <td width=20>Valor Total Calculado</td>
	          <td width=20><input type=text readonly class=box name=vlrtotal size=20 value='$vlrtotal'></td>
	          <td width=20>Valor Total Informado</td>
	          <td width=20><input type=text readonly class=box name=vlrtotalinfo size=20 value='$vlrtotaldigitado'></td>
	      </tr>
	      <tr>
		<td width=70>Numero NF:</td>
		<td><input type=text readonly name=mov_nr_nota class=box size=20 value='$row[mov_nr_nota]'></td>
		<td width=70>Data Emissao da Nota:</td>
		<td><input type=text readonly name=mov_dt_nota class=box size=20 value='$row[mov_dt_nota]'></td>
		<td width=70>Desconto:</td>
		<td><input type=text readonly name=mov_desconto class=box size=20 value='$vlrdesconto'></td>
	      </tr>
	      <tr>
		<td width=70>Acrescimo:</td>
		<td><input type=text readonly name=mov_acrescimo class=box size=20 value='$row[mov_acrescimo]'></td>
		<td width=70>Tipo de Acrescimo:</td>
		<td><input type=text readonly name=mov_tipo_acrescimo class=box size=20 value='$row[mov_tipo_acrescimo]'></td>
	      </tr>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
     </form>
        </table><br>";

//
//-> Botoes
  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
		 <tr>
		  <td>
		   <fieldset>
			<legend>Digita&ccedil;&atilde;o dos Itens da Entrada</legend>
			 <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
			   <form onSubmit=\"return notnull()\"  name=inclui_item id=inclui_item method=post action=''>
			<input type=hidden name=action value=form_insert>
			<input type=hidden name=acao value=>
			<input type=hidden name=mov_codigo value=$mov_codigo>
					<input type=hidden name=mov_entrada value=$mov_entrada>
				</tr>
			<table border='0'>	
			  <tr>
			<td width=20>Produtos: </td>
			<td colspan=4>	
				<input type=hidden id='hidd'value='$row[set_entrada]'>	
				<input type=hidden id=mov_data value='$row[mov_data]'>
				<div id='medicamentos'>
					<input name='medicamentos' id='medicamentos'style='border:1px solid #B0CCE5;background-color:#E8F4FE;width:400px' />
				</div>
			</td>
			<td colspan=4>			
			 <select name='pro_codigo' class='box' id='pro_codigo' style='display:none;' onchange=\"calcula_preco( '$row[set_entrada]', '$row[mov_data]')\">
			 <option value='0'>...</option>";
			//
			//-> SQL do produto
			$sql = "select distinct a.pro_codigo, a.pro_nome, a.pro_validade
					from produto a, setor b, produto_setor c
					where a.pro_codigo = c.pro_codigo
					and b.set_codigo = c.set_codigo 
					and pro_situacao = 'A'"
					.($dados[0]=="" ? "" : " and b.uni_codigo = ".$dados[0]).
					" order by pro_nome";
			$query = pg_query($sql);
			  while($produto=pg_fetch_array($query)) {
			   echo "<option value='$produto[pro_codigo]'>$produto[pro_nome]</option>";
			  }
		   echo "</select>";
		   //$query = pg_query($sql) or die(pg_last_error());
		   echo "
			  </tr>
			 <tr>
				<td width=20>Quantidade:</td>
				<td>
					<input type='text' name='ite_quantidade' id='ite_quantidade' class='box' size='20' onchange=\"calcula_preco( '$row[set_entrada]', '$row[mov_data]')\"/>
				</td>
				<td width=20 >Valor Total:</td>
				<td colspan=3>";    
				if( $row['mov_entrada'] == 'I' )
				{
					echo "<input type='text' name='totalitem_r' id='totalitem_r' readonly='readonly' class='box' size='20' onchange=\"calcula2();\" onkeydown=\"formata_moeda(this,20,event,2)\" />";
					echo "<input type='hidden' name='totalitem' id='totalitem' />";
				}
				else
				{
					echo "<input type='text' name='totalitem' id='totalitem' class='box' size='20' onchange=\"calcula2();\" onkeydown=\"formata_moeda(this,20,event,2)\" />";
					echo "<input type='hidden' name='totalitem_r' id='totalitem_r' />";
				}
	
				
	
				echo "</td>
				
			 </tr>
			 <tr>
			 	<td width=20>Valor Unitario:</td>
				<td><input type='text' name='ite_vlrunit' id='ite_vlrunit' readonly class='box' size='20'></td>
				<td width=20 colspan=3 style='padding:0px;'>
					<div id='validade' style='display:none'>
						<table border=0 cellspacing=0>
							<tr>
								<td width='120'>Lote:</td>
								<td width='220'><input type=text name=ite_lote class=box size=20></td>
								<td width='40' align='right'>Validade:</td>
								<td><input type='text' name='ite_validade' id='ite_validade' class='box' size='20' onKeypress=\"return Ajusta_Data(this, event);\"></td>
								<td>
									<div id='dose' style='display:none'>
										<table>
											<tr>
												<td width='120'>Doses:</td>
												<td width='220'><input type=text name=ite_dose class=box size=20></td>
											</tr>						
										</table>
									</div>
								<td>
								
							</tr>
						</table>
					</div>
				</td>";
			
			echo"
			   </tr>
				<tr>
				<td width=60><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/add_on.gif ' /></td>
			    <td width=60></td>
			   <td width=60 colspan='0'><a href=entrada.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>			  
			    <td colspan='2'></div></td>
			  </tr>
			  <tr>
			
			  </tr>
			  </form>
		</table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br>";
//
//-> Listando
  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Listando Itens Cadastradros para o Movimento</legend>
	     <table width=100% align=center cellspacing=2 cellpadding=4 border=0>
	      <tr bgcolor=F9f9f9>
		<td style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Produto</td>
		<td width=40 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Quantidade</td>
		<td width=40 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Vlr.Unit.</td>
		<td width=40 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Vlr.Total</td>
		<td width=100 colspan=2 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td>";

    /*$sql="select ite_codigo, itens_movimento.pro_codigo, pro_nome,  ite_quantidade, ite_vlrunit, ite_vlrdesc,
                         ite_lote, ite_validade, to_char((ite_quantidade * ite_vlrunit), '999999999.99') as valortotal
                  from itens_movimento, produto
                  where itens_movimento.pro_codigo = produto.pro_codigo
                  and   (select mov_tipo from movimento where mov_codigo = itens_movimento.mov_codigo) = 'E'
                  and   mov_codigo = $mov_codigo
                  order by mov_codigo desc ";*/
    $sql="select ite_codigo, 
    			 itens_movimento.pro_codigo, 
    			 pro_nome, 
    			 pro_validade, 
    			 ite_quantidade, 
    			 ite_vlrunit, 
    			 ite_vlrdesc,
    			 ite_lote, 
    			 to_char(ite_validade, 'dd/mm/yyyy')as ite_validade, 
    			 to_char( ite_vlrtotal, '999999999.99') as valortotal
            from itens_movimento, 
            	 produto
           where itens_movimento.pro_codigo = produto.pro_codigo
             and (select mov_tipo 
					from movimento 
				   where mov_codigo = itens_movimento.mov_codigo) = 'E'
             and mov_codigo = $mov_codigo
           order by mov_codigo desc ";
    $sql = pg_query($sql);
     while($row=pg_fetch_array($sql))
     {
        $intquantidade = formata_valor0($row['ite_quantidade']);
        $vlrunit = formata_valor4($row['ite_vlrunit']);
        $valor=explode('.', $row['ite_vlrunit']);
        $vlrtotal = formata_valor($row['valortotal']);

          echo "<tr>
              <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[pro_nome]</td>
              <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$intquantidade</td>
              <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$vlrunit</td>
              <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$vlrtotal</td>
              <td width=100 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'><a href=$PHP_SELF?id_login=$id_login&acao=form_edit&ite_codigo=$row[ite_codigo]&action=altera_item&mov_codigo=$mov_codigo><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg border=0></a></td>
              <td width=100 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'><a href=$PHP_SELF?id_login=$id_login&acao=del&ite_codigo=$row[ite_codigo]&action=form_exclui&mov_codigo=$mov_codigo><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.jpg border=0></a></td>
            </tr>";
     }
	echo "</tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table>";
}
else if ($acao == 'form_edit')
{

  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Dados da Entrada</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>";
    $sql =  pg_query("select mov_codigo, to_char(mov_data, 'dd/mm/yyyy') as mov_data, mov_tipo, for_codigo,
                          mov_desconto, mov_observacao,
                          set_entrada, mov_nr_nota, to_char(mov_dt_nota, 'dd/mm/yyyy') as mov_dt_nota
             from movimento
             where  mov_codigo = '$mov_codigo'");

   $row=pg_fetch_array($sql);

   $sqlfornecedor=pg_query("select for_codigo, for_nome, for_nome_fantasia
                from fornecedor where for_codigo = '$row[for_codigo]'");

   $rowfornecedor = pg_fetch_array($sqlfornecedor);

   $sqlsetor = pg_query("select * from setor where set_estoque = 'S' and set_codigo = '$row[set_entrada]'");

   $rowsetor = pg_fetch_array($sqlsetor);

   /*$stmt = "select round(cast (sum(ite_vlrunit * ite_quantidade) - coalesce(mov_desconto,0) +
          coalesce(mov_acrescimo,0) as numeric), 2) as total
                from movimento, itens_movimento
                where movimento.mov_codigo = itens_movimento.mov_codigo
                and   movimento.mov_codigo = '$mov_codigo'
                group by mov_desconto, mov_acrescimo";*/

   $stmt = "select round(cast (sum(ite_vlrtotal) - coalesce(mov_desconto,0) +
          coalesce(mov_acrescimo,0) as numeric), 2) as total
                from movimento, itens_movimento
                where movimento.mov_codigo = itens_movimento.mov_codigo
                and   movimento.mov_codigo = '$mov_codigo'
                group by mov_desconto, mov_acrescimo";

   $sqltotal=pg_query($stmt);

   $rowtotal = pg_fetch_array($sqltotal);

   $vlrtotal = formata_valor($rowtotal['total']);

   $vlrdesconto = formata_valor4($row['mov_desconto']);

   $vlrtotaldigitado = formata_valor($row['mov_total_nota']);

    echo "
                <tr>
	          <td width=70>Dados do Fornecedor</td>
	          <td width=150 colspan=5><input type=text readonly name=for_nome size=100 value='$rowfornecedor[for_nome]'></td>
               </tr>
          <tr>
	          <td width=70>Unidade</td>
	          <td width=70><input type=text readonly name=uni_desc size=40 value='$rowsetor[set_nome]'></td>
	          <td width=20>Valor Total Calculado</td>
	          <td width=20><input type=text readonly name=vlrtotal size=20 value='$vlrtotal'></td>
	          <td width=20>Valor Total Informado</td>
	          <td width=20><input type=text readonly name=vlrtotalinfo size=20 value='$vlrtotaldigitado'></td>
	      </tr>
	      <tr>
		<td width=70>Numero NF:</td>
		<td><input type=text readonly name=mov_nr_nota class=box size=20 value='$row[mov_nr_nota]'></td>
		<td width=70>Data Emissao da Nota:</td>
		<td><input type=text readonly name=mov_dt_nota class=box size=20 value='$row[mov_dt_nota]'></td>
		<td width=70>Desconto:</td>
		<td><input type=text readonly name=mov_desconto class=box size=20 value='$vlrdesconto'></td>
	      </tr>
	      <tr>
		<td width=70>Acrescimo:</td>
		<td><input type=text readonly name=mov_acrescimo class=box size=20 value='$row[mov_acrescimo]'></td>
		<td width=70>Tipo de Acrescimo:</td>
		<td><input type=text readonly name=mov_tipo_acrescimo class=box size=20 value='$row[mov_tipo_acrescimo]'></td>
	      </tr>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br>";
//
//-> Botoes
   /* $stmt = "select ite_codigo, itens_movimento.pro_codigo, pro_nome,  ite_quantidade, ite_vlrunit, ite_vlrdesc,
                         ite_lote, ite_validade,
                         to_char((ite_quantidade * ite_vlrunit), '999999999.99') as valortotal
                  from itens_movimento, produto
                  where itens_movimento.pro_codigo = produto.pro_codigo
                  and   (select mov_tipo from movimento where mov_codigo = itens_movimento.mov_codigo) = 'E'
                  and   mov_codigo = $mov_codigo
                  and   ite_codigo = $ite_codigo
                  order by mov_codigo desc ";*/

    $stmt = "select ite_codigo, itens_movimento.pro_codigo, pro_nome,  ite_quantidade, ite_vlrunit, ite_vlrdesc,
                         ite_lote, to_char(ite_validade, 'dd/mm/yyyy')as ite_validade,
                         ite_vlrtotal as valortotal
                  from itens_movimento, produto
                  where itens_movimento.pro_codigo = produto.pro_codigo
                  and   (select mov_tipo from movimento where mov_codigo = itens_movimento.mov_codigo) = 'E'
                  and   mov_codigo = $mov_codigo
                  and   ite_codigo = $ite_codigo
                  order by mov_codigo desc ";

   $sql=pg_query($stmt);
  $row = pg_fetch_array($sql);
  $intquantidade = formata_valor0($row['ite_quantidade']);

  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Alteracao do Item da Entrada</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	       <form name=altera_item method=post action=$PHP_SELF>
		<input type='hidden' name='action' value='edit'>
		<input type='hidden' name='acao' value=''>
		<input type='hidden' name='id_login' value=$id_login>
		<input type='hidden' name='mov_codigo' value=$mov_codigo>
		<input type='hidden' name='ite_codigo' value=$ite_codigo>
	      <tr>
		<td width=20>Produto:</td>
		<td colspan=4>
		 <select name=pro_codigo class=box>";
	    //
	    //-> SQL do produto
		$sql = "select distinct a.pro_codigo, a.pro_nome
				from produto a, setor b, produto_setor c
				where a.pro_codigo = c.pro_codigo
				and b.set_codigo = c.set_codigo "
                .($dados[0]=="" ? "" : " and b.uni_codigo = ".$dados[0]).
                " order by pro_nome";
	    $query = pg_query($sql);
	      while($produto=pg_fetch_array($query)) {
	       echo ($produto[pro_codigo]==$row[pro_codigo])?"<option value='$produto[pro_codigo]' selected>$produto[pro_nome]</option>":"<option value='$produto[pro_codigo]'>$produto[pro_nome]</option>";
	      }

       //$valortotal = $row[ite_quantidade] * $row[ite_vlrunit];

       echo "</select>
	        </td>
	      </tr>
	     <tr>
     		<td width=20>Quantidade:</td>
    		<td><input type=text name=ite_quantidade class=box size=20 value='$intquantidade' onchange='calcula_altera()'></td>
     		<td width=20>Valor Total:</td>
    		<td><input type='text' name='totalitem' class=box size=20 value='$row[valortotal]' onchange='calcula_altera()' onkeydown=\"formata_moeda(this,20,event,2)\"></td>

     		<td width=20>Valor Unitario:</td>
    		<td><input type=text name=ite_vlrunit readonly class=box size=20 value=$row[ite_vlrunit]></td>
         </tr>
	     <tr>
     		<td width=20>Lote:</td>
    		<td><input type=text name=ite_lote class=box size=20 value='$row[ite_lote]'></td>
     		<td width=20>Validade:</td>
    		<td><input type='text' name='ite_validade' id='ite_validade' class='box' size='20' value='$row[ite_validade]'></td>
	       <td width=95><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg></td>
	      </tr></form>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br>";

//
	echo "</tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table>";
}

//------------------------------------------------------------------>
//-> SQL's
//------------------------------------------------------------------>
//
//-> ADD <---------------------------------------------------------->
else if($action=="form_insert")
{    
    
	$iteDtValidade = ultimoDiaMes($ite_validade);
    $ite_consolidado = 'S'; 
    //die($iteDtValidade);
    $stmt = "insert into itens_movimento ( 
    						pro_codigo, 
    						ite_quantidade, 
    						ite_vlrunit, 
    						mov_codigo,
    						ite_consolidado,
    						ite_lote,
    						ite_validade,
    						ite_dose,
    						ite_vlrtotal
    						)
    				 values ( " .
				            ($pro_codigo ? "'$pro_codigo'" : "null") . ", " .
				            ($ite_quantidade ? "'$ite_quantidade'" : "null") . ", " .
				            ($ite_vlrunit ? "'$ite_vlrunit'" : "null") . ", " .
				            ($mov_codigo ? "'$mov_codigo'" : "null") . ", " .
				            "'{$ite_consolidado}'" . ", " .
				            ($ite_lote ? "'$ite_lote'" : "'SEM_LOTE'") . ", " .
				            ($iteDtValidade != "" && $iteDtValidade != "//"  ? "'$iteDtValidade'" : "'31/12/2900'") . ",  " .
				            ($ite_dose ? "'$ite_dose'" : "1") . ",  " .
				            ($totalitem ? str_to_float($totalitem) : "null")." 
				            )";
    //die($stmt);
	$sql = pg_query($stmt) or die(pg_last_error());
	
      $calcestoque = pg_fetch_row(pg_query("select mov_data, set_entrada
                                             from movimento where mov_codigo = $mov_codigo"));
      $atualiza_preco = pg_query("select calcula_preco($pro_codigo,  $calcestoque[1], '$calcestoque[0]')");


    //msg($id_login,$acao,$sql);

    echo "<SCRIPT LANGUAGE=\"JavaScript\">
                setTimeout(\"location='itens_entrada.php?id_login=$id_login&mov_codigo=$mov_codigo&mov_entrada=$mov_entrada&acao=form_inclui_item&action='\", 0);
        </SCRIPT>";
}
//
//-> EDIT <--------------------------------------------------------->
else if($action=="edit")
{
	
	$update = "update itens_movimento set " .
            ($pro_codigo ? "pro_codigo='$pro_codigo'" : "pro_codigo=null") . ", " .
            ($ite_quantidade ? "ite_quantidade='$ite_quantidade'" : "ite_quantidade=null") . ", " .
            ($ite_vlrunit ? "ite_vlrunit='$ite_vlrunit'" : "ite_vlrunit=null") . ", " .
            ($mov_codigo ? "mov_codigo='$mov_codigo'" : "mov_codigo=null") . ", " .
            ($ite_lote ? "ite_lote='$ite_lote'" : "ite_lote='SEM_LOTE'") . ", " .
            ($ite_validade ? "ite_validade='$ite_validade'" : "ite_validade='31/12/2900'") . ",  " .
            ($ite_dose ? "ite_dose='$ite_dose'" : "ite_dose=1") . ",  " .
            ($totalitem ? "ite_vlrtotal=".str_to_float($totalitem) : "ite_vlrtotal=0") . "  " .
            "where ite_codigo='$ite_codigo'";
	//die($update);
	$sql = pg_query($update);

//msg($id_login,$acao,$sql);
      $calcestoque = pg_fetch_row(pg_query("select mov_data, set_entrada
                                             from movimento where mov_codigo = $mov_codigo"));
      $atualiza_preco = pg_query("select calcula_preco($pro_codigo,  $calcestoque[1], '$calcestoque[0]')");


     echo "<SCRIPT LANGUAGE=\"JavaScript\">
                setTimeout(\"location='itens_entrada.php?id_login=$id_login&mov_codigo=$mov_codigo&acao=form_inclui_item&action='\", 0);
           </SCRIPT>";
}

//
//-> DEL <---------------------------------------------------------->
if($action=="form_exclui")
{
	echo"<script>
		if (confirm('Deseja Apagar esse registro?')){
			location.href ='$PHP_SELF?id_login=$id_login&acao=del&ite_codigo=$ite_codigo&action=form_exclui_item&mov_codigo=$mov_codigo';
		}else{
			location.href ='itens_entrada.php?id_login=$id_login&mov_codigo=$mov_codigo';
		}
	</script>";
}
if($action=="form_exclui_item")
{
	
	//if (confirm("Deseja Apagar esse registro?")){
	    $sql = pg_query("delete 
	    				   from itens_movimento 
	    				  where ite_codigo='$ite_codigo'");
	    $calcestoque = pg_fetch_row(pg_query("select mov_data, 
	    											 set_entrada
	                                            from movimento 
	                                           where mov_codigo = $mov_codigo"));
	    $deletepreco = pg_query("delete 
	    						   from precomedio 
	    						  where pro_codigo = $pro_codigo and
	                              mov_data = $calcestoque[0]");
	    $atualiza_preco = pg_query("select calcula_preco($pro_codigo,  $calcestoque[1], $calcestoque[0])");
	//}
//else{
//		return  false;
//	}
    //msg($id_login,$acao,$sql);
    echo "<SCRIPT LANGUAGE=\"JavaScript\">";
        echo "setTimeout(\"location='itens_entrada.php?id_login=$id_login&mov_codigo=$mov_codigo&acao=form_inclui_item&action='\", 0);";
    echo "</SCRIPT>";
}


?>
