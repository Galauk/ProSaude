<script language="JavaScript" type="text/javascript" src="funcoes.js"></script>
<script>
function abre_movim(id_login, mov_nr_nota)
{
// O Parametro que esta sendo passado para este valor e o mov_codigo e nao foi alterado para continuar funcionando ok
// Marco Aurelio - 20/12/2006
 window.open('./relatorio/MovExibirRel.php?acao=form_edit&id_login='+id_login+'&mov_nr_nota='+mov_nr_nota,null,"height=400,width=750,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes");

}
</script>
<?
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>

	session_start();
	include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
	verauth($id_login);
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();
//------------------------------------------------------------------>

echo "<fieldset><legend>MOVIMENTAÇÃO/DEVOLUÇÃO</legend>";

//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
	 reglog($id_login,"Entrando em ENTRADA DEVOLUCAO");
//------------------------------------------------------------------>



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
	       <td width=79><a href=movimentacao.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
	       <td width=95>".ChmodBtn($id_login,'adicionar','entradevolucao.php?acao=form_add')."</td>
	       <form method=post action=$PHP_SELF>
		<input type=hidden name=acao value=busca>
	<input type=hidden name=id_login value=$id_login>
	       <td width=180 align=right>Buscar</td>
	       <td width=90><input type=text name=palavra_chave class=box onBlur=\"javascript:this.value=this.value.toUpperCase();\"></td>
	       <td>".ChmodBtn($id_login,'procurar','entradevolucao.php')."</td></form>
	      </tr>
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
	    <legend>Listando &Uacute;ltimas <b>15</b> Devolu&ccedil;&otilde;es Cadastradas</legend>
	     <table width=100% align=center cellspacing=2 cellpadding=4 border=0 class=lista>
	      <tr bgcolor=F9f9f9>
		<th width=40 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Data</th>
		<th width=200 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Centro Estoc.</th>
		<th width=200 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Setor</th>
		<th width=200 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Num.Movimento</th>
		<th width=200 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Tipo Mov.</th>
		<th colspan=3 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</th>";

   $sql=pg_query("select mov_codigo, a.set_nome as desc_saida, b.set_nome as desc_consumo, mov_nr_nota,
                  to_char(mov_data,'DD/MM/YYYY') as mov_data , mov_codigo,
                  'Devolucao de Setor' as tiposaida
                  from movimento, setor as a, setor as b
                  where movimento.set_saida = a.set_codigo
                  and   movimento.set_entrada = b.set_codigo
                  and   mov_tipo = 'E'
                  and   mov_entrada = 'V'
                  order by mov_codigo desc limit 15");

     while($row=pg_fetch_array($sql)) {
       echo "<tr>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[mov_data]</td>
	       <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[desc_saida]</td>
	       <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[desc_consumo]</td>
	       <td align=center width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[mov_nr_nota]</td>
	       <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[tiposaida]</td>
	       <td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'editar','entradevolucao.php?acao=form_edit&mov_codigo='.$row[mov_codigo])."</td>
	       <td width=66 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'apagar','entradevolucao.php?acao=del&mov_codigo='.$row[mov_codigo])."</td>
		   <td width=66 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'><a href='javascript:abre_movim($id_login, $row[mov_codigo])'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/imprimir.jpg border=0></a></td>
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
//-> Mostrando o resultado da busca
//------------------------------------------------------------------>

 if($acao=="busca") {
//
//-> Verificando Busca
 reglog($id_login,"Buscando em ENTRADA DEVOLUCAO: $palavra_chave ");
 

if(strlen($palavra_chave)<"1") {
        echo "<br><br><br><br><br><br><br><br><br><br><br><br><br>
                <table height=100 width=50% align=center cellspacing=0 cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>
                 <tr bgcolor=f9f9f9>
                   <td align=center><font color=red size=2><b>ERRO</b></font><br>Busca com menos de <b>1</b> caracter não permitida</td>
                 </tr>
                </table><br>";
        echo "<SCRIPT LANGUAGE=\"JavaScript\">
                  setTimeout(\"location='$PHP_SELF?id_login=$id_login'\", 2000);
              </SCRIPT>";
 exit;
}

//
//-> Subistituindo o + por porcentagem na busca
   $str = str_replace("+","%",$palavra_chave);
   $pos = strpos($palavra_chave,"+");
  if($pos=="0") {
     $v1=1;
  } else {
     $v1=2;
  }

//
//-> Botoes
  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Opções</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
	       <td width=79><a href=movimentacao.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
	       <td width=95>".ChmodBtn($id_login,'adicionar','entradevolucao.php?acao=form_add')."</td>
	       <form method=post action=$PHP_SELF>
		<input type=hidden name=acao value=busca>
	<input type=hidden name=id_login value=$id_login>
	       <td width=180 align=right>Buscar:</td>
	       <td width=90><input type=text name=palavra_chave class=box onBlur=\"javascript:this.value=this.value.toUpperCase();\"></td>
	       <td>".ChmodBtn($id_login,'procurar','entradevolucao.php')."</td></form>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br>";

  $sqlv="select mov_codigo, 
		       a.set_nome as desc_saida, 
		       b.set_nome as desc_consumo, 
		       to_char(mov_data, 'dd/mm/yyyy') as mov_data, 
		       mov_nr_nota, 
		       mov_data as mov_data2 , 
		       mov_codigo, 
		       'Devolucao de Setor' as tiposaida 
		from movimento, 
			setor as a, 
			setor as b 
		where movimento.set_entrada = b.set_codigo 
			  and movimento.set_saida = a.set_codigo 
			  and mov_tipo = 'E' 
			  and mov_entrada = 'V' 
			  and (a.set_nome like upper('%$palavra_chave%') or b.set_nome like upper('%$palavra_chave%') or mov_nr_nota like upper('%$palavra_chave%')" ;
  
 /* $sqlv="select mov_codigo, a.set_nome as desc_saida, b.set_nome as desc_consumo,
                  to_char(mov_data, 'dd/mm/yyyy') as mov_data, mov_nr_nota,
                  mov_data as mov_data2 , mov_codigo,
                  'Devolucao de Setor' as tiposaida
                  from movimento, setor as a, setor as b
                  where movimento.set_entrada = b.set_codigo
                  and   movimento.set_saida   = a.set_codigo
                  and   mov_tipo = 'E'
                  and   mov_entrada = 'V'
                  and   (a.set_nome like upper('%$palavra_chave%')
                  or     b.set_nome like upper('%$palavra_chave%'))
                  
                     or  mov_nr_nota = '$palavra_chave' ";*/
    if (strpos($palavra_chave, "/") != 0)
       $sqlv .= "or mov_data = '$palavra_chave'";

    $sqlv .= ")
              order by a.set_nome, mov_data2 ";
   
    $sql=pg_query($sqlv);
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
		<td width=40 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Data</td>
		<td width=200 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Centro Estoc.</td>
		<td width=200 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Setor</td>
		<td width=200 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Num.Movimento</td>
		<td width=200 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Tipo Mov.</td>
		<td colspan=2 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td>";

     while($row=pg_fetch_array($sql)) {
       echo "<tr>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[mov_data]</td>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[desc_saida]</td>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[desc_consumo]</td>
	       <td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'><a href='javascript:abre_movim($id_login, $row[mov_codigo])'>$row[mov_nr_nota]</a></td>
<!--	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[mov_nr_nota]</td> -->
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[tiposaida]</td>
	       <td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'editar','entradevolucao.php?acao=form_edit&mov_codigo='.$row[mov_codigo])."</td>
	       <td width=66 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'apagar','entradevolucao.php?acao=del&mov_codigo='.$row[mov_codigo])."</td>
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
	 reglog($id_login,"Formulario de ADICAO ENTRADA DEVOLUCAO");


//
//-> Abaixo sao os botoes de voltar / cadastro simples / cadastro completo

  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Opções de Cadastro</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
	       <td width=79><a href=entradevolucao.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
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

// if(($type=="" OR $acao=="simples")) {
  echo "<form method=post action=$PHP_SELF>
	<input type=hidden name=acao value=add>
	<input type=hidden name=id_login value=$id_login>
	<input type=hidden name=type value=simples>
	<input type=hidden name=mov_entrada value='V'>
	<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Cabecalho da Saida</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>";
     $sqlsaida=pg_query("select nextval('seq_mov_codigo'::text) as novo_codigo");
     $rowsaida = pg_fetch_array($sqlsaida);
     $sqldata_hora = pg_query("select to_char(current_date, 'dd/mm/yyyy') as data, extract(hour from current_time) || ':' || extract(minute from current_time) as hora");
     $rowdata_hora = pg_fetch_array($sqldata_hora);
     $mov_data = $rowdata_hora['data'];
     $mov_dt_nota = $rowdata_hora['data'];
     echo "<input type=hidden name=mov_codigo value=$rowsaida[novo_codigo]>
	      <tr>
        		<td width=110>Tipo da Entrada: </td>
        		<td width=110>Devolucao por Setor </td>
	      </tr>
	      <tr>
     		<td width=70>Centro Estocador:</td>
	     	<td>
     		 <select name=set_entrada class=box>";
	         //
     	    //-> SQL do Centro Estocador
	         $query = pg_query("select * from setor
                                where set_estoque = 'S' order by set_nome");
	           while($setor=pg_fetch_array($query)) {
	                 echo "<option value='$setor[set_codigo]'>$setor[set_nome]</option>";
	           }
	   echo "</select>
	        </td>
         </tr>
	      <tr>
     		<td width=70>Sesstor:</td>
	     	<td>
     		 <select name=set_saida class=box>";
	         //
     	    //-> SQL do Setor onde ocorrera a entrada do produto (saida para consumo) ou
            // nos outros tipos onde deverÃ¡ ser debitda a sua movimentacao
	         $query = pg_query("select * from setor
                               
                                order by set_nome");
	           while($setor=pg_fetch_array($query)) {
	                 echo "<option value='$setor[set_codigo]'>$setor[set_nome]</option>";
	           }
	   echo "</select>
	        </td>
         </tr>
	     <tr>
     		<td width=40>Data da Entrada:</td>
    		<td><input type=text name=mov_data class=box size=20 value=$mov_data  onKeypress=\"return Ajusta_Data(this, event);\"></td>
         </tr>
	     <tr>
    		<td width=40>Numero da Entrada:</td>
    		<td><input type=text name=mov_nr_nota class=box size=20 value=$rowsaida[novo_codigo]></td>
        </tr>
	     <tr>
		<td width=40>Observacao:</td>
            <td ><textarea name=mov_observacao class=box cols=100 rows=2></textarea></td>
	      </tr>
	      <tr>
	       <td>&nbsp;</td>
	       <td><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg></td>
	      </tr>

	  </td>
	 </tr>
        </table><br></form>";
 //}//fechamento do if - acao = simples
}
//}

//------------------------------------------------------------------>
//-> Formulario de Edicao de Conteudo
//------------------------------------------------------------------>

 if($acao=="form_edit") {
	 reglog($id_login,"Formulario de EDICAO ENTRADA DEVOLUCAO");

//
//-> Formulario de edicao do cadastro SIMPLES
  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
         <tr>
          <td>
           <fieldset>
            <legend>Opções de Cadastro</legend>
             <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
              <tr>
               <td width=79><a href=entradevolucao.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
               <td>&nbsp;</td>
              </tr>
             </table>
           </fieldset>
          </td>
         </tr>
        </table><br>";

//
//-> Pegando as informcoes do banco pra mostrar no formulario
 $sqlmovimento =  pg_query("select mov_codigo, to_char(mov_data, 'dd/mm/yyyy') as mov_data, mov_tipo,
                          mov_desconto, mov_observacao,
                          set_saida, set_entrada, mov_nr_nota, to_char(mov_dt_nota, 'dd/mm/yyyy') as mov_dt_nota, mov_entrada,
                          retorna_usuario(usr_codigo) as login_usuario, to_char(mov_data_inclusao, 'dd/mm/yyyy') as mov_data_inclusao,
                         'Entrada por Devolucao de Setor' as tiposaida
                         from movimento
                   where  mov_codigo = '$mov_codigo'");
  $row = pg_fetch_array($sqlmovimento);

  echo "<br><br><form method=post action=$PHP_SELF>
	<input type=hidden name=acao value=edit>
	<input type=hidden name=id_login value=$id_login>
	<input type=hidden name=mov_codigo value=$mov_codigo>
	<input type=hidden name=set_entrada value='$row[set_entrada]'>
	<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Devolucao de Setor</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>";
         echo "Ultima Alteracao: $row[login_usuario] - $row[mov_data_inclusao]";
        $sqlcentro=pg_query("select set_codigo, set_nome
                from setor where set_codigo = '$row[set_entrada]'");
        $rowcentro = pg_fetch_array($sqlcentro);

        $sqlsetor=pg_query("select set_codigo, set_nome
                from setor where set_codigo = '$row[set_saida]'");
        $rowsetor = pg_fetch_array($sqlsetor);

         echo "
                <tr>
		          <td width=70>Centro Estocador</td>
		          <td width=70><input type=text readonly name=centro_nome size=70 value='$rowcentro[set_nome]'></td>
               </tr>
	      <tr>
     		<td width=70>Setor:</td>
	     	<td>
     		 <select name=set_saida class=box>";
	         //
     	    //-> SQL do Setor onde ocorrera a entrada do produto (saida para consumo) ou
            // nos outros tipos onde deverÃ¡ ser debitda a sua movimentacao
	         $query = pg_query("select * from setor
                                where set_estoque = 'N'
                                order by set_nome");
	           while($setor=pg_fetch_array($query)) {
	                 echo ($setor[set_codigo]==$row[set_entrada])?"<option value='$setor[set_codigo]' selected>$setor[set_nome]</option>":"<option value='$setor[set_codigo]'>$setor[set_nome]</option>";
	           }
	   echo "</select>
	      <tr>
		<td width=110>Tipo da Entrada</td>
		<td width=110>Devolucao de Setor</td>
	      </tr>

	      <tr>
		<td width=70>Data da Entrada:</td>
		<td colspan=2><input type=text name=mov_data class=box size=20 value='$row[mov_data]'  onKeypress=\"return Ajusta_Data(this, event);\"></td>
	      </tr>
             <tr>
		<td width=70>Numero da Entrada:</td>
		<td colspan=2><input type=text name=mov_nr_nota class=box size=20 value='$row[mov_nr_nota]'></td>
	      </tr>

	     <tr>
		<td width=40>Observacao:</td>
            <td colspan=2><textarea name=mov_observacao class=box cols=100 rows=2>$row[mov_observacao]</textarea></td>
	      </tr>
	      <tr>
	       <td>&nbsp;</td>
	       <td><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg></td>
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
	 reglog($id_login,"Adicionando Registro em ENTRADA DEVOLUCAO");

    $tipo_movimento = 'E';
    $sql = pg_query("insert into movimento ( " .
            "mov_codigo, " .
            "mov_data, " .
            "mov_tipo, " .
            "mov_entrada, " .
            "usu_codigo, " .
            "mov_desconto, " .
            "mov_observacao, " .
            "set_entrada, " .
            "set_saida, " .
            "mov_nr_nota, " .
            "mov_dt_nota, " .
            "usr_codigo, " .
            "mov_data_inclusao, " .
            "mov_ip, " .
            "mov_total_nota  " .
            ") values ( " .
            "$mov_codigo" . ", " .  //grava o codigo do movimento para que possa passar posteriormente para o outro
            ($mov_data ? "'$mov_data'" : "null") . ", " .
            "'{$tipo_movimento}'" . ", " .  //tipo da movimentaÃ§Ã£o = E - Entrada
            "'{$mov_entrada}'" . ", " .
            ($usu_codigo ? "'$usu_codigo'" : "null") . ", " .
            ($mov_desconto ? "'$mov_desconto'" : "null") . ", " .
            ($mov_observacao ? "'$mov_observacao'" : "null") . ", " .
            ($set_entrada ? "'$set_entrada'" : "null") . ", " .
            ($set_saida ? "'$set_saida'" : "null") . ", " .
            ($mov_nr_nota ? "'$mov_nr_nota'" : "null") . ", " .
            ($mov_dt_nota ? "'$mov_dt_nota'" : "null") . ", " .
            ($id_login ? "'$id_login'" : "null") . ", " . //Mover o login do usuario que esta usando
            "date(now())" . ", " .
            ($mov_ip ? "'$mov_ip'" : "null") . ", " . //Mover o ip do micro do que o usuario esta usando
            ($mov_total_nota ? "'$mov_total_nota'" : "null") . "  " . //Fazer update na gravacao da nota
            ")");

        echo "<SCRIPT LANGUAGE=\"JavaScript\">
                  setTimeout(\"location='itens_entradevolucao.php?id_login=$id_login&mov_codigo=$mov_codigo&action=form_inclui_item'\", 0);
              </SCRIPT>";
}

//
//-> EDIT <--------------------------------------------------------->

 if($acao=="edit") {
	 reglog($id_login,"Editando ENTRADA DEVOLUCAO $mov_codigo");

  $sql = pg_query("update movimento set " .
            ($mov_data ? "mov_data='$mov_data'" : "mov_data=null") . ", " .
            ($usu_codigo ? "usu_codigo='$usu_codigo'" : "usu_codigo=null") . ", " .
            ($mov_desconto ? "mov_desconto='$mov_desconto'" : "mov_desconto=null") . ", " .
            "set_saida = '$set_saida'" . ", " .  //tipo da movimentaÃ§Ã£o = E - Entrada
            ($mov_observacao ? "mov_observacao='$mov_observacao'" : "mov_observacao=null") . ", " .
            ($mov_nr_nota ? "mov_nr_nota='$mov_nr_nota'" : "mov_nr_nota=null") . ", " .
            ($mov_dt_nota ? "mov_dt_nota='$mov_dt_nota'" : "mov_dt_nota=null") . ", " .
            ($id_login ? "usr_codigo='$id_login'" : "usr_codigo=null") . ", " .
            "mov_data_inclusao = date(now()),  " .
            ($mov_ip ? "mov_ip='$mov_ip'" : "mov_ip=null") . ", " .
            ($mov_total_nota ? "mov_total_nota='$mov_total_nota'" : "mov_total_nota=null") . "  " .
            "where mov_codigo='$mov_codigo'");

        echo "<SCRIPT LANGUAGE=\"JavaScript\">
                  setTimeout(\"location='itens_entradevolucao.php?id_login=$id_login&mov_codigo=$mov_codigo&action=form_inclui_item'\", 0);
              </SCRIPT>";
}

//
//-> DEL <---------------------------------------------------------->

 if($acao=="del") {
	 reglog($id_login,"Exluindo Registro de ENTRADA DEVOLUCAO $mov_codigo");

  $sql = pg_query("delete from movimento where mov_codigo='$mov_codigo'");
msg($id_login,$acao,$sql);
}

?>

