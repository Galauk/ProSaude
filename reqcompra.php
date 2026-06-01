<?php

//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>

include_once "authlib.inc.php";
verauth($id_login);
session_start();
require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";

cabecario();

$common = new commonClass();

$stmt = "SELECT uni_codigo FROM usuarios WHERE usr_codigo = $id_login";
$stmt = db_query($stmt);
$dados = pg_fetch_array($stmt);

//------------------------------------------------------------------>


//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
	 reglog($id_login,"Entrando em ENTRADA");
//------------------------------------------------------------------>

echo '<script language="JavaScript" type="text/javascript" src="funcoes.js"></script>';

if (empty($acao) OR ($acao == 'form_entrada')) {

//
//-> Botoes
  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Opções</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
	       <td width=79><a href=materiais.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
	       <td width=95>".ChmodBtn($id_login,'adicionar','reqcompra.php?acao=form_add')."</td>";
	       if (chmodbtn($id_login,"procurar_if","reqcompra.php"))
	       {
		  echo "<form method=post action=$PHP_SELF>";
	       }
	       echo "
		      <input type=hidden name=acao value=busca>
		      <input type=hidden name=id_login value=$id_login>
		      <td width=180 align=right>Buscar:</td>
		      <td width=90><input type=text name=palavra_chave class=box onBlur=\"javascript:this.value=this.value.toUpperCase();\"></td>
		      <td>".ChmodBtn($id_login,'procurar','reqcompra.php')."</td></form>
		      <td width=107><a href='logoff.php?id_login=$id_login' target='_parent'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/sair.gif border=0></a></td>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br>";

//
//-> Listando

  if (chmodbtn($id_login,"listar_if","reqcompra.php"))
  {
	echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	       <tr>
		<td>
		 <fieldset>
		  <legend>Listando Últimas <b>15</b> Requisicao de Compras Cadastradas</legend>
		   <table class='lista' align=center cellspacing=2 cellpadding=4 border=0>
		    <tr bgcolor=F9f9f9>
		      <th width=40>Data</td>
		      <th width=200>Setor</td>
		      <th width=200>Situa&ccedil;&atilde;o</td>
		      <th colspan=2>&nbsp;</td>";
      
	 $sql=pg_query("select to_char(rcom_data, 'dd/mm/yyyy') as rcom_data, set_nome, rcom_codigo,
			case when rcom_status = 'A' then 'ABERTA' when rcom_status = 'F' then 'FECHADA' else 'CANCELADA' end as situacao
			from requisicao_compra, setor
			where   requisicao_compra.set_codigo = setor.set_codigo "
			.($dados[0]=="" ? "" : " AND uni_codigo = ".$dados[0]).
			" order by rcom_codigo desc limit 15");
	   while($row=pg_fetch_array($sql)) {
	     echo "<tr>
		     <td align=center>$row[rcom_data] </td>
		     <td>$row[set_nome] </td>
		     <td align=center>$row[situacao]</td>
		 <!--<td width=66> <a href=reqandamento.php?acao=form_entrada&rcom_codigo=$row[rcom_codigo]&id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/andamento_requisicao_on.jpg border=0> </a> </td>-->
		     <td width=60>".ChmodBtn($id_login,'editar','reqcompra.php?acao=form_edit&rcom_codigo='.$row[rcom_codigo])."</td>
		     <td width=66>".ChmodBtn($id_login,'apagar','reqcompra.php?acao=del&rcom_codigo='.$row[rcom_codigo])."</td>
		   </tr>";
	   }
  }
	echo "</tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table>";
}


//------------------------------------------------------------------>
//-> Mostrando o resultado da busca
//------------------------------------------------------------------>

 if($acao=="busca") {
//
//-> Verificando Busca
 reglog($id_login,"Buscando em REQUISICAO COMPRA: $palavra_chave ");

/*if(strlen($palavra_chave)<"1") {
        echo "<br><br><br><br><br><br><br><br><br><br><br><br><br>
                <table height=100 width=50% align=center cellspacing=0 cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>
                 <tr bgcolor=f9f9f9>
                   <td align=center><font color=red size=2><b>ERRO</b></font><br>Busca com menos de <b>3</b> caracteres não permitida</td>
                 </tr>
                </table><br>";
        echo "<SCRIPT LANGUAGE=\"JavaScript\">
                  setTimeout(\"location='$PHP_SELF?id_login=$id_login'\", 2000);
              </SCRIPT>";
 exit;
}*/

//
//-> Subistituindo o + por porcentagem na busca
   $str = str_replace("+","%",$palavra_chave);
   $pos = strpos($palavra_chave,"+");
  if($pos=="0") {
     $v1=1;
  } else {
     $v1=2;
  }
//echo $v1;
//
//-> Botoes
  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Opções</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
	       <td width=79><a href=materiais.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
	       <td width=95>".ChmodBtn($id_login,'adicionar','reqcompra.php?acao=form_add')."</td>";
	       if (chmodbtn($id_login,"procurar_if","reqcompra.php"))
	       {
		  echo "<form method=post action=$PHP_SELF>";
	       }
	       echo "
		       <input type=hidden name=acao value=busca>
		       <input type=hidden name=id_login value=$id_login>
		       <td width=180 align=right>Buscar:</td>
		       <td width=90><input type=text name=palavra_chave class=box onBlur=\"javascript:this.value=this.value.toUpperCase();\"></td>
		       <td>".ChmodBtn($id_login,'procurar','reqcompra.php')."</td></form>
		       <td width=107><a href='logoff.php?id_login=$id_login' target='_parent'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/sair.gif border=0></a></td>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br>";

   $sqlv = "select rcom_codigo, set_nome, to_char(rcom_data, 'dd/mm/yyyy') as rcom_data,
		  rcom_previsao_entrega, rcom_data as rcom_data2,
          case
			when rcom_status = 'A' then 'ABERTA'
			when rcom_status = 'F' then 'FECHADA'
			else 'CANCELADA'
		  end as situacao
          from requisicao_compra, setor
          where requisicao_compra.set_codigo = setor.set_codigo "
		  .($dados[0]=="" ? "" : " AND setor.uni_codigo = ".$dados[0]).
		  " and (set_nome ilike upper('%$palavra_chave%') or
          case
			when rcom_status = 'A' then 'ABERTA'
			when rcom_status = 'F' then 'FECHADA'
			else 'CANCELADA'
		  end = UPPER('$palavra_chave') ";
						 

    if (strpos($palavra_chave, "/") != 0)
       $sqlv .= "or rcom_data = '$palavra_chave' ";

    $sqlv .= ")
              order by rcom_data2 desc ";
     //vSQL($sqlv, '1');
    $sql=pg_query($sqlv);
	
	//echo "<pre>$sqlv</pre>";


$num=pg_num_rows($sql);
  if($num=="0") { $resp = "Nenhum Registro encontrado com \"$palavra_chave\""; }
  if($num=="1") { $resp = "Encontrado <b>$num</b> Registro com \"$palavra_chave\""; }
  if($num>"1") { $resp = "Encontrados <b>$num</b> Registros com \"$palavra_chave\""; }

  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>$resp</legend>
	     <table width=100% align=center cellspacing=2 cellpadding=4 border=0>
		<td width=40>Data</td>
		<td width=200>Setor</td>
		<td width=200>Status</td>
		<td colspan=2>&nbsp;</td>";

     while($row=pg_fetch_array($sql)) {
       echo "<tr>
	       <td align=center>$row[rcom_data]</td>
	       <td align=center>$row[set_nome]</td>
	       <td align=center>$row[situacao]</td>
	       <!--<td width=60>".ChmodBtn($id_login,'andamento_requisicao_on','reqandamento.php?acao=form_edit&rcom_codigo='.$row[rcom_codigo])."</td>-->
	       <td width=60>".ChmodBtn($id_login,'editar','reqcompra.php?acao=form_edit&rcom_codigo='.$row[rcom_codigo])."</td>
	       <td width=66>".ChmodBtn($id_login,'apagar','reqcompra.php?acao=del&rcom_codigo='.$row[rcom_codigo])."</td>
	     </tr>";
     }
	echo "</tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table>";
}

//------------------------------------------------------------------>
//-> Formulario de Adicao de Conteudo
//------------------------------------------------------------------>

 if($acao=="form_add") {
	 reglog($id_login,"Formulario de ADICAO REQUISICAO COMPRA");

//
//-> Abaixo sao os botoes de voltar / cadastro simples / cadastro completo

  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Opções de Cadastro</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
	       <td width=79><a href=reqcompra.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
            <form name=formbusca method=post action=$PHP_SELF>
             <input type=hidden name=acao value=form_add>
             <input type=hidden name=action value=buscar>
             <input type=hidden name=id_login value=$id_login>
	   </fieldset></form>
	  </td>
	 </tr>
        </table></table><br>";

//
//-> Este if esta apontando quando a acao for vazia ele vai mostrar o cadastro simples
//   no cadastro completo vc vai ter que passar a variavel acao para completo

  echo "<form method=post action=$PHP_SELF>
	<input type=hidden name=acao value=add>
	<input type=hidden name=id_login value=$id_login>
	<input type=hidden name=type value=simples>
	<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Cabecalho da Nota</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>";
     $sqlnota=pg_query("select nextval('seq_rcom_codigo'::text) as novo_codigo");
     $rownota = pg_fetch_array($sqlnota);
     echo "<input type=hidden name=rcom_codigo value=$rownota[novo_codigo]>
	      <tr>
		<td width=170>Centro Estocador Solicitante:</td>
		<td>
		 <select name=set_codigo class=box>";
	    //
	    //-> SQL da Unidade
	    $query = pg_query("select * from setor
                           where set_estoque = 'S' "
                           .($dados[0]=="" ? "" : " AND uni_codigo = ".$dados[0]).
                           " order by set_nome");
	      while($setor=pg_fetch_array($query)) {
	       echo "<option value='$setor[set_codigo]'>$setor[set_nome]</option>";
	      }
	   echo "</select>
	        </td>
	      </tr>";
     $sqldata_hora = pg_query("select to_char(current_date, 'dd/mm/yyyy') as data, extract(hour from current_time) || ':' || extract(minute from current_time) as hora");
     $rowdata_hora = pg_fetch_array($sqldata_hora);
     $rcom_data = $rowdata_hora['data'];
          echo "
	     <tr>
     		<td>Data da Requisicao :</td>
    		<td><input type=text name=rcom_data class=box size=20 value=$rcom_data maxlength=10 onKeypress=\"return Ajusta_Data(this, event);\"></td>
         </tr>
	     <tr>
     		<td>Previs&atilde;o de Entrega :</td>
    		<td><input type=text name=rcom_previsao_entrega class=box size=20 value='' maxlength=10 onKeypress=\"return Ajusta_Data(this, event);\"></td>       </tr>
	    <tr>
		    <td>Situa&ccedil;&atilde;o da Requisi&ccedil;&atilde;o:</td>
		    <td>
		        <select name=rcom_status class=box>
          	         <option value=A>Aberta</option>
    		         <option value=F disabled=disabled>Fechada</option>
    		         <option value=C disabled=disabled>Cancelada</option>
		        </select>
            </td>
	    </tr>
	    <tr>
            <td>Observacao:</td>
            <td ><textarea name=rcom_observacao class=box cols=100 rows=2></textarea></td>
	      </tr>
	      <tr>
	       <td>&nbsp;</td>
	       <td><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg></td>
	      </tr>
      </td>
	 </tr>
        </table><br></form>";
 }//fechamento do if - acao = simples

//------------------------------------------------------------------>
//-> Formulario de Edicao de Conteudo
//------------------------------------------------------------------>

 if($acao=="form_edit") {
	 reglog($id_login,"Formulario de EDICAO REQUISICAO DE COMPRA");

//
//-> Formulario de edicao do cadastro SIMPLES
  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
         <tr>
          <td>
           <fieldset>
            <legend>Opções de Cadastro</legend>
             <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
              <tr>
               <td width=79><a href=reqcompra.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
               <td>&nbsp;</td>
              </tr>
             </table>
           </fieldset>
          </td>
         </tr>
        </table><br>";

//
//-> Pegando as informcoes do banco pra mostrar no formulario
 $sqlmovimento =  "select to_char(rcom_previsao_entrega, 'dd/mm/yyyy') as rcom_previsao_entrega,rcom_codigo, to_char(rcom_data, 'dd/mm/yyyy') as rcom_data, rcom_status,
                          set_codigo, rcom_observacao
                          from requisicao_compra
                   where  rcom_codigo = '$rcom_codigo'";
$row=pg_fetch_array(pg_query($sqlmovimento));
 if ($row[rcom_status] == 'A' ) {
    $vl1 = 'selected ';
    $vl2 = '';
    $vl3 = '';
    }
 if ($row[rcom_status] == 'F' ) {
    $vl1 = '';
    $vl2 = 'selected ';
    $vl3 = '';
    }
 if ($row[rcom_status] == 'C' ) {
    $vl1 = '';
    $vl2 = '';
    $vl3 = 'selected ';
    }

  echo "<br><br><form method=post action=$PHP_SELF name='form'>
	<input type=hidden name=acao value=edit>
	<input type=hidden name=id_login value=$id_login>
	<input type=hidden name=rcom_codigo value=$rcom_codigo>
	<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Requisicao de Compra</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>";
        echo "
	      <tr>
		<td width=200>Centro Estocador:</td>
		<td>
		 <select name=set_codigo class=box>";
	    //
	    //-> SQL da Unidade
	    $query = pg_query("select set_codigo, set_nome
                          from setor where set_estoque = 'S' order by set_nome");
	      while($setor=pg_fetch_array($query)) {
	       echo ($setor[set_codigo]==$row[set_codigo])?"<option value='$setor[set_codigo]' selected>$setor[set_nome]</option>":"<option value='$setor[set_codigo]'>$setor[set_nome]</option>";
	      }
	   echo "</select>
	        </td>
	      </tr>
	      <tr>
		<td width=70>Data de Requisi&ccedil;&atilde;o:</td>
		<td><input type=text name=rcom_data class=box size=20 value='$row[rcom_data]' maxlength=10 onKeypress=\"return Ajusta_Data(this, event);\"></td>
	      </tr>
	      <tr>
		<td width=70>Previs&atilde;o de Entrega:</td>
		<td><input type=text name=rcom_previsao_entrega class=box size=20 value='$row[rcom_previsao_entrega]' maxlength=10 onKeypress=\"return Ajusta_Data(this, event);\"></td>
	      </tr>
	    <tr>
		    <td width=110>Situa&ccedil;&atilde;o da Requisi&ccedil;&atilde;o de Compra</td>
		    <td>
		        <select name=rcom_status class=box>
          	         <option value=A $vl1>Aberta</option>
    		         <option value=F $vl2>Fechada</option>
    		         <option value=C $vl3>Cancelada</option>
		        </select>
            </td>
	    </tr>

	     <tr>
		<td width=40>Observacao:</td>
            <td ><textarea name=rcom_observacao class=box cols=100 rows=2>$row[rcom_observacao]</textarea></td>
	      </tr>
	      <tr>
	      	<td>&nbsp;</td>
	       <td>".
	   		$common->commonButton("Editar", null, "editar_on.png", "onClick=\"document.form.submit();\"")
	   		  ."
	   	   </td>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br></form>";
}

//------------------------------------------------------------------>
//-> SQL's
//------------------------------------------------------------------>
//
//-> ADD <---------------------------------------------------------->

 if($acao=="add") {
	 reglog($id_login,"Adicionando Registro em REQUISICAO COMPRA");

    $sql = pg_query("insert into requisicao_compra ( " .
            "rcom_codigo, " .
            "rcom_data, " .
	"rcom_previsao_entrega, " .   
            "set_codigo, " .
            "rcom_status, " .
            "usr_codigo," .
            "rcom_observacao " .
            ") values ( " .
            "$rcom_codigo" . ", " .
            ($rcom_data ? "'$rcom_data'" : "null") . ", " .
	($rcom_previsao_entrega ? "'$rcom_previsao_entrega'" : "null") . ", " .   
            ($set_codigo ? "'$set_codigo'" : "null") . ", " .
            ($rcom_status ? "'$rcom_status'" : "null") . ", " .
            ($id_login ? "'$id_login'" : "null") . ", " . //Mover o login do usuario que esta usando
            ($rcom_observacao ? "'$rcom_observacao'" : "null") . "  " .
            ")");
//msg($acao,$sql);
        echo "<SCRIPT LANGUAGE=\"JavaScript\">
                  setTimeout(\"location='itens_reqcompra.php?id_login=$id_login&rcom_codigo=$rcom_codigo'\", 0);
              </SCRIPT>";
}

//
//-> EDIT <--------------------------------------------------------->

 if($acao=="edit") {
	 reglog($id_login,"Editando REQUISICAO COMPRA $rcom_codigo");

  $sql = pg_query("update requisicao_compra set " .
         ($rcom_previsao_entrega ? "rcom_previsao_entrega='$rcom_previsao_entrega'" : "rcom_data=null") . ", " .   
	   ($rcom_data ? "rcom_data='$rcom_data'" : "rcom_data=null") . ", " .
            ($set_codigo ? "set_codigo='$set_codigo'" : "set_codigo=null") . ", " .
            ($rcom_status ? "rcom_status='$rcom_status'" : "rcom_status=null") . ", " .
            ($id_login ? "usr_codigo='$id_login'" : "usr_codigo=null") . ", " .
            ($rcom_observacao ? "rcom_observacao='$rcom_observacao'" : "rcom_observacao=null") . "  " .
            "where rcom_codigo='$rcom_codigo'");

//msg($acao,$sql);
        echo "<SCRIPT LANGUAGE=\"JavaScript\">
                  setTimeout(\"location='itens_reqcompra.php?id_login=$id_login&rcom_codigo=$rcom_codigo'\", 0);
              </SCRIPT>";
}

//
//-> DEL <---------------------------------------------------------->

 if($acao=="del") {
	 reglog($id_login,"Exluindo Registro de REQUISICAO DE COMPRA $rcom_codigo");
  $quer = pg_query("delete from requisicao_compra where rcom_codigo='$rcom_codigo'");
msg($id_login,$acao,$quer);
}

?>
