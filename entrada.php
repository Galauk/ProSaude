<script language="JavaScript" type="text/javascript" src="funcoes.js"></script>
<script>
	function abre_movim(id_login, mov_nr_nota)
	{
		// O Parametro que esta sendo passado para este valor e o mov_codigo e nao foi alterado para continuar funcionando ok
		    // Marco Aurelio - 20/12/2006
		 window.open('./relatorio/MovExibirRel.php?acao=form_edit&id_login='+id_login+'&mov_nr_nota='+mov_nr_nota,null,"height=400,width=750,status=yes,toolbar=no,menubar=no,resizable=yes, location=no,scrollbars=yes");
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


//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
	 reglog($id_login,"Entrando em ENTRADA");
//------------------------------------------------------------------>
echo "<fieldset><legend>MOVIMENTAÇÃO/ENTRADA</legend>";


$stmt = "SELECT uni_codigo FROM logon WHERE id_login = $id_login";
$stmt = db_query($stmt);
$dados = pg_fetch_array($stmt);


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
	       <td width=95>".ChmodBtn($id_login,'adicionar','entrada.php?acao=form_add')."</td>
           <td width=74>".ChmodBtn($id_login,'devolucao','entradevolucao.php?acao=form_entrada')."</td>";
                if (chmodbtn($id_login,"procurar_if","entrada.php"))
                {
                    echo "<form method=post action=$PHP_SELF>";
                }
                echo "
                        <input type=hidden name=acao value=busca>
                        <input type=hidden name=id_login value=$id_login>
                        <td width=180 align=right>Buscar:</td>
                        <td width=90><input type=text name=palavra_chave class=box onBlur=\"javascript:this.value=this.value.toUpperCase();\"></td>
                        <td>".ChmodBtn($id_login,'procurar','entrada.php')."</td></form>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br>";

//
//-> Listando

    if (chmodbtn($id_login,"listar_if","entrada.php"))
    {
            echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
                   <tr>
                    <td>
                     <fieldset>
                      <legend>Listando �ltimas <b>15</b> Entradas Cadastradas</legend>
                       <table width=100% align=center cellspacing=2 cellpadding=4 border=0>
                        <tr bgcolor=F9f9f9>
                          <td width=40 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Data</td>
                          <td width=200 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Fornecedor</td>
                          <td width=200 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Setor</td>
                          <td width=200 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Nr. Nota</td>
                          <td width=200 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Tipo Entrada</td>
                          <td colspan=3 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td>";
          	$sqlmov = "SELECT mov_codigo,
		 					  for_nome,
							  set_nome,
							  to_char(mov_data,'DD/MM/YYYY') as mov_data ,
							  mov_data as dataMov,
							  mov_nr_nota,
                              CASE  WHEN mov_entrada = 'E-SI' 		THEN 'ENTRADA POR SALDO DE IMPLANTA��O'
									WHEN mov_entrada = 'E-C' 		THEN 'ENTRADA POR CONCORR�NCIA'
									WHEN mov_entrada = 'E-DL' 		THEN 'ENTRADA POR DISPENSA DE LICITA��O'
									WHEN mov_entrada = 'E-CONV' 	THEN 'ENTRADA POR CONVITE'
									WHEN mov_entrada = 'D' 			THEN 'ENTRADA POR DOA��O'
									WHEN mov_entrada = 'E-D' 		THEN 'ENTRADA POR DOA��O'
									WHEN mov_entrada = 'E-P' 		THEN 'ENTRADA POR PREG�O'
									WHEN mov_entrada = 'E-DL' 		THEN 'ENTRADA POR DISPENSA DE LICITA��O'
									WHEN mov_entrada = 'A' 			THEN 'ENTRADA POR AJUSTE DE ESTOQUE'
									WHEN mov_entrada = 'E-AE' 		THEN 'ENTRADA POR AJUSTE DE ESTOQUE'
									WHEN mov_entrada = 'E-EVENTUAL' THEN 'ENTRADA POR ENTRADA EVENTUAL'
									WHEN mov_entrada = 'E-O' 		THEN 'ENTRADA POR ENTRADA ORDIN�RIA'
									WHEN mov_entrada = 'E-TP' 		THEN 'ENTRADA POR TOMADA DE PRE�OS'
									WHEN mov_entrada = 'E-INEX' 	THEN 'ENTRADA POR INEXIGIBILIDADE'
									WHEN mov_entrada = 'P' 			THEN 'ENTRADA POR PERMUTA'
									WHEN mov_entrada = 'E-PER' 		THEN 'ENTRADA POR PERMUTA'
									WHEN mov_entrada = 'E' 			THEN 'ENTRADA DE NOTA FISCAL'
									WHEN mov_entrada = 'M' 			THEN 'ENTRADA DE EMPRESTIMO'
									WHEN mov_entrada = 'I' 			THEN 'ENTRADA DE INVENTARIO'
									WHEN mov_entrada = 'O' 			THEN 'OUTRAS ENTRADAS'
									WHEN mov_entrada = 'T' 			THEN 'ENTRADA POR TRANSFERENCIA'
									WHEN mov_entrada = 'V' 			THEN 'DEVOLUCAO DE SETORES'
                               END as tipoentrada
                              FROM movimento,
							  	   fornecedor,
								   setor a
                             WHERE movimento.for_codigo = fornecedor.for_codigo
                               AND a.set_codigo = movimento.set_entrada
                               AND mov_tipo = 'E' "
    .($dados[0] == "" ? "" : " AND a.uni_codigo = ".$dados[0]).
                           " ORDER BY dataMov DESC limit 15";
           $sql = db_query($sqlmov);
                    $controle = 0;
               while($row = pg_fetch_array($sql)) {
                          $c1 = "";
                          $c2 = "#F2F2F2";

                          if ($controle == 0) {
                            $cor = $c1;
                            $controle++;
                          } else {
                            $cor = $c2;
                            $controle = 0;
                          }
                 echo "<tr bgcolor='$cor'>
                         <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[mov_data] </td>
                         <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[for_nome] </td>
                         <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[set_nome] </td>
                         <td align=center width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[mov_nr_nota]</td>
                         <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[tipoentrada]</td>
                         <td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'editar','entrada.php?acao=form_edit&mov_codigo='.$row[mov_codigo])."</td>
                         <td width=66 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'apagar','entrada.php?acao=del&mov_codigo='.$row[mov_codigo])."</td>
						 <td width=66 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'><a href='javascript:abre_movim($id_login, $row[mov_codigo])'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/imprimir.jpg border=0></a></td>
                       </tr>";
                           $c++;
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
 reglog($id_login,"Buscando em ENTRADA: $palavra_chave ");

if(strlen($palavra_chave)<"1") {
        echo "<br><br><br><br><br><br><br><br><br><br><br><br><br>
                <table height=100 width=50% align=center cellspacing=0 cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>
                 <tr bgcolor=f9f9f9>
                   <td align=center><font color=red size=2><b>ERRO</b></font><br>Busca com menos de <b>3</b> caracteres n�o permitida</td>
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
	    <legend>Op��es</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
	       <td width=79><a href=movimentacao.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
	       <td width=95>".ChmodBtn($id_login,'adicionar','entrada.php?acao=form_add')."</td>";
               if (chmodbtn($id_login,"procurar_if","entrada.php"))
               {
                    echo "<form method=post action=$PHP_SELF>";
               }
               echo "
                        <input type=hidden name=acao value=busca>
                        <input type=hidden name=id_login value=$id_login>
                        <td width=180 align=right>Buscar:</td>
                        <td width=90><input type=text name=palavra_chave class=box onBlur=\"javascript:this.value=this.value.toUpperCase();\"></td>
                        <td>".ChmodBtn($id_login,'procurar','entrada.php')."</td></form>
                        <td width=107><a href='logoff.php?id_login=$id_login' target='_parent'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/sair.gif border=0></a></td>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br>";

   $sqlv="select mov_codigo, for_nome, to_char(mov_data, 'dd/mm/yyyy') as mov_data,
                     mov_data as mov_data2 , mov_nr_nota,
                  case when mov_entrada = 'E' then 'Nota Fiscal'
                       when mov_entrada = 'A' then 'Ajuste'
                       when mov_entrada = 'M' then 'Emprestimo'
                       when mov_entrada = 'I' then 'Inventario'
                       when mov_entrada = 'D' then 'Doacao'
                       when mov_entrada = 'P' then 'Permuta'
                       when mov_entrada = 'O' then 'Outras Entradas'
                       when mov_entrada = 'V' then 'Devol. Setor'
                  end as tipoentrada
                  from movimento, fornecedor, setor a
                  where movimento.for_codigo = fornecedor.for_codigo
                  and a.set_codigo = movimento.set_entrada
                  and   mov_tipo = 'E'"
                    .($dados[0]=="" ? "" : " AND a.uni_codigo = ".$dados[0]).
                  "and   (for_nome ilike upper('%$palavra_chave%')
                  or    upper(case when mov_entrada = 'E' then 'Nota Fiscal'
                             when mov_entrada = 'A' then 'Ajuste'
                             when mov_entrada = 'M' then 'Emprestimo'
                             when mov_entrada = 'I' then 'Inventario'
                             when mov_entrada = 'D' then 'Doacao'
                             when mov_entrada = 'P' then 'Permuta'
                             when mov_entrada = 'O' then 'Outras Entradas'
                       when mov_entrada = 'V' then 'Devol. Setor'
                  end) like upper('$palavra_chave%')
                     or  mov_nr_nota = '$palavra_chave' ";
    if (strpos($palavra_chave, "/") != 0)
       $sqlv .= "or mov_data = '$palavra_chave'";

    $sqlv .= ")
              order by for_nome, mov_data2 ";
    $sql=db_query($sqlv);
//     vSQL($sqlv, '1');

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
		<td width=200 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Fornecedor</td>
		<td width=40 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Num. Nota</td>
		<td width=40 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Tipo Entrada</td>
		<td colspan=2 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td>";

     $controle = 0;
     while($row=pg_fetch_array($sql)) {
		$c1 = "";
		$c2 = "#F2F2F2";

		if ($controle == 0) {
		  $cor = $c1;
		  $controle++;
		} else {
		  $cor = $c2;
		  $controle = 0;
		}
       echo "<tr bgcolor='$cor'>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[mov_data]</td>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[for_nome]</td>
	       <td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'><a href='javascript:abre_movim($id_login, $row[mov_codigo])'>$row[mov_nr_nota]</a></td>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[tipoentrada]</td>
	       <td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'editar','entrada.php?acao=form_edit&mov_codigo='.$row[mov_codigo])."</td>
	       <td width=66 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'apagar','entrada.php?acao=del&mov_codigo='.$row[mov_codigo])."</td>
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
	 reglog($id_login,"Formulario de ADICAO ENTRADA");

//
//-> Abaixo sao os botoes de voltar / cadastro simples / cadastro completo

  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Op��es de Cadastro</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
	       <td width=79><a href=entrada.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
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
     $sqlnota=db_query("select nextval('seq_mov_codigo'::text) as novo_codigo");
     $rownota = pg_fetch_array($sqlnota);
     echo "<input type=hidden name=mov_codigo value=$rownota[novo_codigo]>
	      <tr>
		<td width=150>Tipo da Entrada:</td>
		<td>
		 <select name=mov_entrada id=mov_entrada class=box>
			<optgroup label='HORUS'>
				<option value=E-SI>Entrada por Saldo de Implanta��o</option>
				<option value=E-C>Entrada por Concorr�ncia</option>
				<option value=E-DL>Entrada por Dispensa de Licita��o</option>
				<option value=E-CONV>Entrada por Convite</option>
				<option value=E-D>Entrada por Doa��o</option>
				<option value=E-P>Entrada por Preg�o</option>
				<option value=E-AE>Entrada por Ajuste de Estoque</option>
				<option value=E-EVENTUAL>Entrada por Entrada Eventual</option>
				<option value=E-O>Entrada por Entrada Ordin�ria</option>
				<option value=E-TP>Entrada por Tomada de Pre�os</option>
				<option value=E-INEX>Entrada por Inexigibilidade</option>
				<option value=E-PER>Entrada por Permuta</option>
			</optgroup>
			<optgroup label='PADR�ES'>
				<option value=E>Entrada por Nota Fiscal de Compra</option>
				<option value=M>Entrada por Emprestimo</option>
				<option value=I>Entrada por Inventario</option>
				<option value=O>Entrada por Outras Entradas</option>
				<option value=V>Entrada por Devol. Setor</option>
			</optgroup>
		 </select>
	      </tr>";
     echo "
	      <tr>
		<td width=70>Fornecedor:</td>
		<td>
		 <select name=for_codigo class=box>";
	    //
	    //-> SQL da Unidade
	    $query = db_query("select for_codigo, for_nome
                           from fornecedor order by for_nome");
	      while($fornecedor=pg_fetch_array($query)) {
	       echo "<option value='$fornecedor[for_codigo]'>$fornecedor[for_nome]</option>";
	      }
	   echo "</select>
	        </td>
	      <tr>
		<td width=70>Centro Estocador:</td>

		<td>
		 <select name=set_entrada class=box>";
	    //
	    //-> SQL da Unidade
	    /*$query = db_query("select * from setor
                           where set_estoque = 'S' order by set_nome");*/

        $query = db_query("select * from setor
                           where set_estoque = 'S'"
                           .($dados[0]=="" ? "" : " AND setor.uni_codigo = ".$dados[0]).
                           "order by set_nome");

	      while($setor=pg_fetch_array($query)) {
	       echo "<option value='$setor[set_codigo]'>$setor[set_nome]</option>";
	      }
	   echo "</select>
	        </td>
	      </tr>";
     $sqldata_hora = db_query("select to_char(current_date, 'dd/mm/yyyy') as data, extract(hour from current_time) || ':' || extract(minute from current_time) as hora");
     $rowdata_hora = pg_fetch_array($sqldata_hora);
     $mov_data = $rowdata_hora['data'];
     $mov_dt_nota = $rowdata_hora['data'];
          echo "
	     <tr>
     		<td width=40>Data de Entrada:</td>
    		<td><input type=text name=mov_data maxlength=10 class=box size=20 value=$mov_data  onKeypress=\"return Ajusta_Data(this, event);\"></td>
         </tr>
	     <tr>
     		<td width=40>Data da Emissao da Nota:</td>
    		<td><input type=text name=mov_dt_nota class=box maxlength=10 size=20 value=$mov_dt_nota  onKeypress=\"return Ajusta_Data(this, event);\"></td>
         </tr>
	     <tr>
    		<td width=40>Desconto:</td>
    		<td>
                <input type=text name=mov_desconto class=box size=20 onkeydown=\"Bloqueia_Caracteres(event); formata_moeda(this,20,event,2)\">
                &nbsp;&nbsp;* <font color=red>Informar casas decimais (digitar apenas n&uacute;meros)</font>
            </td>
         </tr>
	     <tr>
    		<td width=40>Numero da Nota:</td>
    		<td><input type=text name=mov_nr_nota class=box size=20 value=$rownota[novo_codigo]></td>
        </tr>
	     <tr>
    		<td width=40>Acrescimos:</td>
    		<td>
                <input type=text name=mov_acrescimo class=box size=20 onkeydown=\"formata_moeda(this,20,event,2)\">
                &nbsp;&nbsp;* <font color=red>Informar casas decimais (digitar apenas n&uacute;meros)</font>
            </td>
         </tr>
	     <tr>
    		<td width=40>Motivo do Acrescimo:</td>
    		<td><input type=text name=mov_tipo_acrescimo maxlength=60 class=box size=20></td>
        </tr>
	      <tr>
		<td width=70>Total da Nota:</td>
		<td>
            <input type=text name=mov_total_nota class=box size=20 onkeydown=\"formata_moeda(this,20,event,2)\">
            &nbsp;&nbsp;* <font color=red>Informar casas decimais (digitar apenas n&uacute;meros)</font>
        </td>
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
 }//fechamento do if - acao = simples

//------------------------------------------------------------------>
//-> Formulario de Edicao de Conteudo
//------------------------------------------------------------------>

 if($acao=="form_edit") {
	 reglog($id_login,"Formulario de EDICAO ENTRADA");

//
//-> Formulario de edicao do cadastro SIMPLES
  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
         <tr>
          <td>
           <fieldset>
            <legend>Op��es de Cadastro</legend>
             <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
              <tr>
               <td width=79><a href=entrada.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
               <td>&nbsp;</td>
              </tr>
             </table>
           </fieldset>
          </td>
         </tr>
        </table><br>";

//
//-> Pegando as informcoes do banco pra mostrar no formulario
 $sqlmovimento =  "select mov_codigo,
 						  to_char(mov_data, 'dd/mm/yyyy') as mov_data,
 						  mov_tipo,
 						  usu.usr_nome,
 						  for_codigo,
                          mov_desconto,
                          mov_observacao,
                          mov_entrada,
                          set_entrada,
                          mov_nr_nota,
                          to_char(mov_dt_nota, 'dd/mm/yyyy') as mov_dt_nota,
                          retorna_usuario(m.usr_codigo) as login_usuario,
                          to_char(mov_data_inclusao, 'dd/mm/yyyy') as mov_data_inclusao,
                          mov_total_nota,
                          mov_acrescimo,
                          mov_tipo_acrescimo
                     from movimento as m
                     JOIN usuarios as usu
                       ON usu.usr_codigo  = m.usr_codigo
                    where mov_codigo = '$mov_codigo'";
 $row=pg_fetch_array(db_query($sqlmovimento));

  echo "<br><br><form method=post action=$PHP_SELF>
	<input type=hidden name=acao value=edit>
	<input type=hidden name=id_login value=$id_login>
	<input type=hidden name=mov_codigo value=$mov_codigo>
	<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Entrada de Materiais</legend>";
                 //echo "Ultima Alteracao: $row[login_usuario] - $row[mov_data_inclusao]";
        echo "
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>";
         echo "<tr>
         		<td>Ultima Alteracao:</td>
         	    <td>$row[usr_nome] - $row[mov_data_inclusao]</td>
         	  </tr>";
	      echo "<tr>
		<td width=170>Fornecedor:</td>
		<td>
		 <select name=for_codigo class=box>";
	    //
	    //-> SQL do Fornecedor
	    $query = db_query("select for_codigo, for_nome
                           from fornecedor order by for_nome");
	      while($fornecedor=pg_fetch_array($query)) {
	       echo ($fornecedor[for_codigo]==$row[for_codigo])?"<option value='$fornecedor[for_codigo]' selected>$fornecedor[for_nome]</option>":"<option value='$fornecedor[for_codigo]'>$fornecedor[for_nome]</option>";
	      }
	   echo "</select>
	        </td>
	      </tr>
	      <tr>
		<td width=70>Centro Estocador:</td>
		<td>
		 <select name=set_entrada class=box>";
	    //
	    //-> SQL da Unidade
	    /*$query = db_query("select set_codigo, set_nome
                          from setor where set_estoque = 'S' order by set_nome");*/

        $query = db_query("select * from setor
                           where set_estoque = 'S'"
                           .($dados[0]=="" ? "" : " AND setor.uni_codigo = ".$dados[0]).
                           "order by set_nome");

	      while($setor=pg_fetch_array($query)) {
	       echo ($setor[set_codigo]==$row[set_entrada])?"<option value='$setor[set_codigo]' selected>$setor[set_nome]</option>":"<option value='$setor[set_codigo]'>$setor[set_nome]</option>";
	      }
	   echo "</select>
	        </td>
	      </tr>
	      <tr>
		<td width=70>Data de Entrada:</td>
		<td><input type=text name=mov_data class=box size=20 maxlength=10 value='$row[mov_data]'  onKeypress=\"return Ajusta_Data(this, event);\"></td>
	      </tr>
	      <tr>
		<td width=70>Data Emissao da Nota:</td>
		<td><input type=text name=mov_dt_nota class=box size=20 maxlength=10 value='$row[mov_dt_nota]'  onKeypress=\"return Ajusta_Data(this, event);\"></td>
	      </tr>
	      <tr>
		<td width=70>Desconto:</td>
		<td>
            <input type=text name=mov_desconto class=box size=20 value='$row[mov_desconto]' onkeydown=\"Bloqueia_Caracteres(event); formata_moeda(this,20,event,2)\">
            &nbsp;&nbsp;* <font color=red>Informar casas decimais (digitar apenas n&uacute;meros)</font>
        </td>
	      </tr>
	      <tr>
		<td width=110>Tipo da Entrada:</td>
		<td>
		 <select name=mov_entrada id=mov_entrada class=box>
			<optgroup label='HORUS'>
				<option value='E-SI' "; if (trim($row['mov_entrada']) == 'E-SI') { echo "selected='selected'"; } echo ">Entrada por Saldo de Implanta��o</option>
				<option value='E-C' "; if (trim($row['mov_entrada']) == 'E-C') { echo "selected='selected'"; } echo ">Entrada por Concorr�ncia</option>
				<option value='E-DL' "; if (trim($row['mov_entrada']) == 'E-DL') { echo "selected='selected'"; } echo ">Entrada por Dispensa de Licita��o</option>
				<option value='E-CONV' "; if (trim($row['mov_entrada']) == 'E-CONV') { echo "selected='selected'"; } echo ">Entrada por Convite</option>
				<option value='E-D' "; if (trim($row['mov_entrada']) == 'E-D') { echo "selected='selected'"; } echo ">Entrada por Doa��o</option>
				<option value='E-P' "; if (trim($row['mov_entrada']) == 'E-P') { echo "selected='selected'"; } echo ">Entrada por Preg�o</option>
				<option value='E-AE'"; if (trim($row['mov_entrada']) == 'E-AE') { echo "selected='selected'"; } echo " >Entrada por Ajuste de Estoque</option>
				<option value='E-EVENTUAL' "; if (trim($row['mov_entrada']) == 'E-EVENTUAL') { echo "selected='selected'"; } echo ">Entrada por Entrada Eventual</option>
				<option value='E-O' "; if (trim($row['mov_entrada']) == 'E-O') { echo "selected='selected'"; } echo ">Entrada por Entrada Ordin�ria</option>
				<option value='E-TP' "; if (trim($row['mov_entrada']) == 'E-TP') { echo "selected='selected'"; } echo ">Entrada por Tomada de Pre�os</option>
				<option value='E-INEX' "; if (trim($row['mov_entrada']) == 'E-INEX') { echo "selected='selected'"; } echo ">Entrada por Inexigibilidade</option>
				<option value='E-PER' "; if (trim($row['mov_entrada']) == 'E-PER') { echo "selected='selected'"; } echo ">Entrada por Permuta</option>
			</optgroup>
			<optgroup label='PADR�ES'>
				<option value='E' "; if (trim($row['mov_entrada']) == 'E') { echo "selected='selected'"; } echo ">Entrada por Nota Fiscal de Compra</option>
				<option value='M' "; if (trim($row['mov_entrada']) == 'M') { echo "selected='selected'"; } echo ">Entrada por Emprestimo</option>
				<option value='I' "; if (trim($row['mov_entrada']) == 'I') { echo "selected='selected'"; } echo ">Entrada por Inventario</option>
				<option value='O' "; if (trim($row['mov_entrada']) == 'O') { echo "selected='selected'"; } echo ">Entrada por Outras Entradas</option>
				<option value='V' "; if (trim($row['mov_entrada']) == 'V') { echo "selected='selected'"; } echo ">Entrada por Devol. Setor</option>
			</optgroup>
		 </select>
	      </tr>
	      <tr>
		<td width=70>Numero da Nota:</td>
		<td><input type=text name=mov_nr_nota class=box size=20 value='$row[mov_nr_nota]'></td>
	      </tr>
	     <tr>
    		<td width=40>Acrescimos:</td>
    		<td>
                <input type=text name=mov_acrescimo class=box size=20 value='$row[mov_acrescimo]' onkeydown=\"formata_moeda(this,20,event,2)\">
                &nbsp;&nbsp;* <font color=red>Informar casas decimais (digitar apenas n&uacute;meros)</font>
            </td>
         </tr>
	     <tr>
    		<td width=40>Motivo do Acrescimo:</td>
    		<td><input type=text name=mov_tipo_acrescimo maxlength=60 class=box size=20 value='$row[mov_tipo_acrescimo]'></td>
        </tr>
	      <tr>
		<td width=70>Total da Nota:</td>
		<td>
            <input type=text name=mov_total_nota class=box size=20 value='$row[mov_total_nota]' onkeydown=\"formata_moeda(this,20,event,2)\">
            &nbsp;&nbsp;* <font color=red>Informar casas decimais (digitar apenas n&uacute;meros)</font>
        </td>
	      </tr>
	     <tr>
		<td width=40>Observacao:</td>
            <td ><textarea name=mov_observacao class=box cols=100 rows=2>$row[mov_observacao]</textarea></td>
	      </tr>
	      <tr>
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
	 reglog($id_login,"Adicionando Registro em ENTRADA");



    if( $mov_total_nota != "" || $mov_entrada != 'E')
    {
		//$mov_desconto = str_replace(".", "", $mov_desconto);
		//$mov_desconto = str_replace(",", ".", $mov_desconto);
		$mov_desconto = str_to_float($mov_desconto);

		//$mov_acrescimo = str_replace(".", "", $mov_acrescimo);
		//$mov_acrescimo = str_replace(",", ".", $mov_acrescimo);
		$mov_acrescimo = str_to_float($mov_acrescimo);

		//$mov_total_nota = str_replace(".","", $mov_total_nota);
		//$mov_total_nota = str_replace(",",".", $mov_total_nota);
		$mov_total_nota = str_to_float($mov_total_nota);

		$tipo_movimento = 'E';
		$select = "SELECT max(setp_codigo), set_codigo, setp_data_inicial, setp_data_final
				   FROM setor_periodo
				   WHERE set_codigo = $set_entrada
				   AND '$mov_data' BETWEEN setp_data_inicial AND setp_data_final
				   GROUP BY set_codigo, setp_data_inicial, setp_data_final";

		$exec_select = db_query($select);
		$quantidade = pg_fetch_array($exec_select);
		if(pg_num_rows($exec_select) > 0 || $set_saida == 99404 ){
			$sql = "insert into movimento ( " .
					   "mov_codigo, " .
					   "mov_data, " .
					   "mov_tipo, " .
					   "mov_entrada, " .
					   "for_codigo, " .
					   "mov_desconto, " .
					   "mov_observacao, " .
					   "set_entrada, " .
					   "mov_nr_nota, " .
					   "mov_dt_nota, " .
					   "usr_codigo, " .
					   "mov_data_inclusao, " .
					   "mov_ip, " .
					   "mov_acrescimo, " .
					   "mov_tipo_acrescimo, " .
					   "mov_total_nota  " .
                   ") values ( " .
					   "$mov_codigo" . ", " .
					   //grava o codigo do movimento para que possa passar posteriormente para o outro formulário
					   ($mov_data ? "'$mov_data'" : "null") . ", " .
					   "'{$tipo_movimento}'" . ", " .  //tipo da movimentação = E - Entrada
					   "'{$mov_entrada}'" . ", " .  //tipo da movimentação = E - Entrada
					   ($for_codigo ? "'$for_codigo'" : "null") . ", " .
					   ($mov_desconto ? "'$mov_desconto'" : "null") . ", " .
					   ($mov_observacao ? "'$mov_observacao'" : "null") . ", " .
					   ($set_entrada ? "'$set_entrada'" : "null") . ", " .
					   ($mov_nr_nota ? "'$mov_nr_nota'" : "null") . ", " .
					   ($mov_dt_nota ? "'$mov_dt_nota'" : "null") . ", " .
					   ($id_login ? "'$id_login'" : "null") . ", " . //Mover o login do usuario que esta usando
					   "date(now())" . ", " .
					   ($mov_ip ? "'$mov_ip'" : "null") . ", " . //Mover o ip do micro do que o usuario esta usando
					   ($mov_acrescimo ? "'$mov_acrescimo'" : "null") . ", " .
					   ($mov_tipo_acrescimo ? "'$mov_tipo_acrescimo'" : "null") . ", " .
					   ($mov_total_nota ? "'$mov_total_nota'" : "null") . "  " . //Fazer update na gravacao da nota
                   ")";
			$sql = db_query($sql);
			//msg($acao,$sql);
            echo "<SCRIPT LANGUAGE=\"JavaScript\">
					setTimeout(\"location='itens_entrada.php?id_login=$id_login&mov_codigo=$mov_codigo&mov_entrada=$mov_entrada'\", 0);
				  </SCRIPT>";
		} else {
			echo "<script>";
				echo "aviso = 'Data fora do intervalo cadastrado. \\n\\n';";
				echo "aviso+='Entre em contado com o Centro Estocador ';";
				//echo "aviso+='e devera reiniciar o processo de movimentacao de entrada.\\n\\n';";
				echo "var teste=alert(aviso);";
				echo "history.back(1);";
			echo "</script>";
		}
    }else{
		//valor 0 e n�o � doa��o
		// devera pedir valor apenas para tipo de entrada igual a 'E' (entrada por nota fiscal)
		echo "<SCRIPT LANGUAGE=\"JavaScript\">
				 alert('Digite um valor para a nota');
				 history.go(-1);
			 </SCRIPT>";
    }
}

//
//-> EDIT <--------------------------------------------------------->

 if($acao=="edit") {
	 reglog($id_login,"Editando ENTRADA $mov_codigo");

    //$mov_desconto = str_replace(".", "", $mov_desconto);
    //$mov_desconto = str_replace(",", ".", $mov_desconto);
    $mov_desconto = str_to_float($mov_desconto);

    //$mov_acrescimo = str_replace(".", "", $mov_acrescimo);
    //$mov_acrescimo = str_replace(",", ".", $mov_acrescimo);
    $mov_acrescimo = str_to_float($mov_acrescimo);

    //$mov_total_nota = str_replace(".","", $mov_total_nota);
    //$mov_total_nota = str_replace(",",".", $mov_total_nota);
    $mov_total_nota = str_to_float($mov_total_nota);

  $sql = "update movimento set " .
            ($mov_data ? "mov_data='$mov_data'" : "mov_data=null") . ", " .
            ($mov_entrada ? "mov_entrada='$mov_entrada'" : "mov_entrada=null") . ", " .
            ($for_codigo ? "for_codigo='$for_codigo'" : "for_codigo=null") . ", " .
            ($usu_codigo ? "usu_codigo='$usu_codigo'" : "usu_codigo=null") . ", " .
            ($mov_desconto ? "mov_desconto='$mov_desconto'" : "mov_desconto=null") . ", " .
            ($mov_observacao ? "mov_observacao='$mov_observacao'" : "mov_observacao=null") . ", " .
            ($set_entrada ? "set_entrada='$set_entrada'" : "set_entrada=null") . ", " .
            ($mov_nr_nota ? "mov_nr_nota='$mov_nr_nota'" : "mov_nr_nota=null") . ", " .
            ($mov_dt_nota ? "mov_dt_nota='$mov_dt_nota'" : "mov_dt_nota=null") . ", " .
            ($id_login ? "usr_codigo='$id_login'" : "usr_codigo=null") . ", " .
            "mov_data_inclusao = date(now()),  " .
            ($mov_ip ? "mov_ip='$mov_ip'" : "mov_ip=null") . ", " .
            ($mov_acrescimo ? "mov_acrescimo='$mov_acrescimo'" : "mov_acrescimo=null") . ", " .
            ($mov_tipo_acrescimo ? "mov_tipo_acrescimo='$mov_tipo_acrescimo'" : "mov_tipo_acrescimo=null") . ", " .
            ($mov_total_nota ? "mov_total_nota='$mov_total_nota'" : "mov_total_nota=null") . "  " .
            "where mov_codigo='$mov_codigo'";
    $sql = db_query($sql);
//msg($acao,$sql);
        echo "<SCRIPT LANGUAGE=\"JavaScript\">
                  setTimeout(\"location='itens_entrada.php?id_login=$id_login&mov_codigo=$mov_codigo'\", 0);
              </SCRIPT>";
}

//
//-> DEL <---------------------------------------------------------->
if($acao=="del")
{
	if(confirm){
	reglog($id_login,"Exluindo Registro de ENTRADA $mov_codigo");
	$sql = db_query("delete from itens_movimento where mov_codigo='$mov_codigo'");
	$quer = db_query("delete from movimento where mov_codigo='$mov_codigo'");
	msg($id_login,$acao,$quer);
	}
}

?>
