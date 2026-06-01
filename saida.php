<script language="JavaScript" type="text/javascript" src="funcoes.js"></script>
<script>
var maxDay = new Array(31,29,31,30,31,30,31,31,30,31,30,31);


function CheckDate(d,t) {
   date_array = new Array(3);
   date_array[0]=(String(d).substr(6,2))    // dia
   date_array[1]=(String(d).substr(4,2))    // mes
   date_array[2]=(String(d).substr(0,4))    // ano

   if (date_array[0] > maxDay[date_array[1]-1]) {
       alert ("Dia invalido da data " + t)
       return 1;
   }
   if (date_array[1] > 12) {
       alert ("Mes invalido da data " + t)
       return 1;
   }
   if (date_array[2] < 2006) {
       alert ("Ano invalido da data " + t)
       return 1;
   }
}

function abre_movim(id_login, mov_nr_nota)
{
    // O Parametro que esta sendo passado para este valor e o mov_codigo e nao foi alterado para continuar funcionando ok
    // Marco Aurelio - 20/12/2006
    window.open('./relatorio/MovExibirRel.php?acao=form_edit&id_login='+id_login+'&mov_nr_nota='+mov_nr_nota,null,"height=400,width=750,status=yes,toolbar=no,menubar=no,resizable=yes, location=no,scrollbars=yes");
}

function movimentacao_saida( obj )
{
    if( obj.value == 'M' ) {
        document.getElementById('tb_forncedor').style.display='table-row';
        //document.location.href = 'saida.php?id_login='+id_login+'&acao=form_add&tipo=M';
    } else if (obj.value == 'D') {
        document.getElementById('tb_forncedor').style.display='table-row';
    } else {
        document.getElementById('tb_forncedor').style.display='none';
    }
    
    if( obj.value != 'A' && obj.value != 'D' && obj.value != 'P' && obj.value != 'S' )
    {
        document.getElementById('tb_setor').style.display='table-row';
    }
    else
    {
        document.getElementById('tb_setor').style.display='none';
    }
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

echo monta_calendario();

//------------------------------------------------------------------>

$stmt = "SELECT uni_codigo FROM logon WHERE id_login = $id_login";
$stmt = db_query($stmt);
$dados = pg_fetch_array($stmt);
//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
reglog($id_login,"Entrando em SAIDA");
//------------------------------------------------------------------>

echo "<fieldset><legend>MOVIMENTAÇÃO/SAÍDA</legend>";

$vepermissao = pg_fetch_array(db_query("SELECT usr_requisicao FROM usuarios WHERE usr_codigo = $id_login"));
if ($vepermissao['usr_requisicao'] == 'N')
{
    echo ("<script>
    alert('Você não tem permissão para fazer requisição de Movimentação de Estoque. Procure o Administrador do Estoque ou do Sistema!!!!');
    window.history.back();
    </script>");
    //die;
}

if (empty($acao) || ($acao == 'form_saida'))
{
    
    //
    //-> Botoes
    echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
            <tr>
                <td>
                    <fieldset>
                        <legend>Opções</legend>
                        <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
                            <tr>".($farmacia =='farmacia' ? "<td width=79><a href=farmacia.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>":"<td width=79><a href=movimentacao.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>")."
                                <td width=95>".ChmodBtn($id_login,'adicionar','saida.php?acao=form_add')."</td>";
                                if (chmodbtn($id_login,"procurar_if","saida.php"))
                                {                                
                                    echo "<form method=post action=$PHP_SELF>";
                                }
                                echo "
                                        <input type=hidden name=acao value=busca>
                                        <input type=hidden name=id_login value=$id_login>
                                        <td width=180 align=right>Buscar</td>
                                        <td width=90><input type=text name=palavra_chave class=box onBlur=\"javascript:this.value=this.value.toUpperCase();\"></td>
                                        <td>".ChmodBtn($id_login,'procurar','saida.php')."</td>
                                </form>
                            </tr>
                        </table>
                    </fieldset>
                </td>
            </tr>
    </table><br>";
    
    //
    //-> Listando

    if (chmodbtn($id_login,"listar_if","saida.php"))
    {
            echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
                    <tr>
                        <td>
                            <fieldset>
                                <legend>Listando &Uacute;timas <b>15</b> Saidas Cadastradas</legend>
                                <table class=lista align=center cellspacing=2 cellpadding=4 border=0>
                                    <tr bgcolor=F9f9f9>
                                        <th width=40>Data</th>
                                        <th width=200>Centro Estoc.</th>
                                        <th width=200>Setor</th>
                                        <th width=200>Num.Movimento</th>
                                        <th width=200>Tipo Mov.</th>
                                        <th colspan=3>&nbsp;</th>
									</tr>";
            $selectMovimentos = "SELECT mov_codigo, 
										a.set_nome AS desc_saida, 
										b.set_nome AS desc_consumo, 
										mov_nr_nota,
										to_char(mov_data,'DD/MM/YYYY') AS mov_data , 
										mov_codigo,
									    CASE WHEN mov_saida = 'S' THEN 'Saida de Consumo'
											 WHEN mov_saida = 'R' THEN 'SAIDA POR PERDAS'
											 WHEN mov_saida = 'S-PE' THEN 'SAIDA POR PERDAS'
											 WHEN mov_saida = 'S-VV' THEN 'SAIDA POR VALIDADE VENCIDA'
											 WHEN mov_saida = 'S-AEA' THEN 'SAIDA POR AMOSTRA, EXPOSIÇÃO E ANÁLISE'
											 WHEN mov_saida = 'S-DEP' THEN 'DEVOLUÇÃO DE ENTRADA DE PRODUTO'
											 WHEN mov_saida = 'S-TR' THEN 'SAÍDA POR TRANSFERÊNCIA E REMANEJAMENTO'
											 WHEN mov_saida = 'S-D' THEN 'SAÍDA POR DOAÇÃO'
											 WHEN mov_saida = 'S-AS' THEN 'SAÍDA POR APREENSÃO SANITÁRIA'
											 WHEN mov_saida = 'S-E' THEN 'SAÍDA PARA EMPRÉSTIMO'
											 WHEN mov_saida = 'S-P' THEN 'SAÍDA PARA PACIENTE NÃO IDENTIFICADO'
											 WHEN mov_saida = 'S-AE' THEN 'SAÍDA POR AJUSTE DE ESTOQUE'
											 WHEN mov_saida = 'M' THEN 'Emprestimo'
											 WHEN mov_saida = 'I' THEN 'Inventario'
											 WHEN mov_saida = 'D' THEN 'Doacao'
											 WHEN mov_saida = 'P' THEN 'Permuta'
											 WHEN mov_saida = 'O' THEN 'Outras Saidas'
											 WHEN mov_saida = 'A' THEN 'Ajuste'
										END AS tiposaida, 
										req_codigo
								   FROM movimento, 
								   		setor AS a, 
										setor AS b
								  WHERE movimento.set_saida = a.set_codigo
									AND movimento.set_entrada = b.set_codigo
									AND mov_tipo = 'S'"
		 .($dados[0] == "" ? "" : " AND a.uni_codigo = ".$dados[0]).
								 "ORDER BY mov_codigo DESC LIMIT 15";
            $sql = db_query($selectMovimentos);
            
            $controle = 0;
            while($row = pg_fetch_array($sql))
            {
                echo "<tr>
                        <td align=center>$row[mov_data]</td>
                        <td>$row[desc_saida]</td>
                        <td>$row[desc_consumo]</td>
                	    <td align=center>$row[mov_nr_nota]</td> 
                        <td width=60>$row[tiposaida]</td>";
			if ( empty($row['req_codigo'])) {

                           echo "<td width=60>".ChmodBtn($id_login,'editar','saida.php?acao=form_edit&mov_codigo='.$row['mov_codigo'])."</td>
                        <td width=66>".ChmodBtn($id_login,'apagar','saida.php?acao=del&mov_codigo='.$row['mov_codigo'])."</td>";
			}
			else {
			   echo "<td> &nbsp; </td> <td> &nbsp; </td>";
			}
			   
			echo "
                        <td width=66><a href='javascript:abre_movim($id_login, $row[mov_codigo])'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/imprimir.jpg border=0></a></td>
                    </tr>";
            }
    }
    echo "              </tr>
                    </table>
                </fieldset>
            </td>
        </tr>
    </table>";
}
//------------------------------------------------------------------>
//-> Mostrando o resultado da busca
//------------------------------------------------------------------>
if( $acao=="busca" ) {
    //
    //-> Verificando Busca
    reglog($id_login,"Buscando em SAIDA: $palavra_chave ");
   
   if(strlen($palavra_chave)<"1")
   {
       echo "<br><br><br><br><br><br><br><br><br><br><br><br><br>
           <table height=100 width=50% align=center cellspacing=0 cellpadding=0 border=0 style='border-top:1px solid;border-bottom:1px solid;border-color:909090;'>
               <tr bgcolor=f9f9f9>
                   <td align=center><font color=red size=2><b>ERRO</b></font><br>Busca com menos de <b>1</b> caracter nçõ permitida</td>
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
   if($pos=="0")
   {
       $v1=1;
   }
   else
   {
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
                            <td width=95>".ChmodBtn($id_login,'adicionar','saida.php?acao=form_add')."</td>";
                            if (chmodbtn($id_login,"procurar_if","saida.php"))
                            {
                                echo "<form method=post action=$PHP_SELF>";
                            }
                            echo "
                                    <input type=hidden name=acao value=busca>
                                    <input type=hidden name=id_login value=$id_login>
                                    <td width=180 align=right>Buscar:</td>
                                    <td width=90><input type=text name=palavra_chave class=box onBlur=\"javascript:this.value=this.value.toUpperCase();\"></td>
                                    <td>".ChmodBtn($id_login,'procurar','saida.php')."</td>
                            </form>
                        </tr>
                    </table>
                </fieldset>
            </td>
        </tr>
    </table><br>";

                            
   $sqlv = "SELECT mov_codigo,
	 			   a.set_nome AS desc_saida, 
	 			   b.set_nome AS desc_consumo,
         		   to_char(mov_data, 'dd/mm/yyyy') AS mov_data, 
         		   mov_nr_nota,
         		   mov_data as mov_data2 , 
         		   mov_codigo,
				   CASE WHEN mov_saida = 'S' THEN 'Saida de Consumo'
				       WHEN mov_saida = 'R' THEN 'Perdas'
				       WHEN mov_saida = 'M' THEN 'Emprestimo'
				       WHEN mov_saida = 'I' THEN 'Inventario'
				       WHEN mov_saida = 'D' THEN 'Doacao'
				       WHEN mov_saida = 'P' THEN 'Permuta'
				       WHEN mov_saida = 'O' THEN 'Outras Saidas'
				       WHEN mov_saida = 'A' THEN 'Ajuste'
				   END AS tiposaida, 
	 			   req_codigo
    		  FROM movimento
    	 LEFT JOIN setor a
      			ON movimento.set_saida   = a.set_codigo  
   		 LEFT JOIN setor b
      			ON movimento.set_entrada = b.set_codigo
			 WHERE movimento.set_saida   = a.set_codigo
			   AND mov_tipo = 'S'"
			       .($dados[0]=="" ? "" : " AND a.uni_codigo = ".$dados[0]).
                   "AND   (a.set_nome like upper('%$palavra_chave%')
                   OR   b.set_nome like upper('%$palavra_chave%')
                   OR    upper(case when mov_saida = 'S' then 'Saida de Consumo'
                   	   WHEN mov_saida = 'R' THEN 'Perdas'
                       WHEN mov_saida = 'M' THEN 'Emprestimo'
                       WHEN mov_saida = 'I' THEN 'Inventario'
                       WHEN mov_saida = 'D' THEN 'Doacao'
                       WHEN mov_saida = 'P' THEN 'Permuta'
                       WHEN mov_saida = 'O' THEN 'Outras Saidas'
                       WHEN mov_saida = 'A' THEN 'Ajuste'
                  end) like upper('%$palavra_chave%')
                     or  mov_nr_nota = '$palavra_chave' ";
    if (strpos($palavra_chave, "/") != 0)
       $sqlv .= "or mov_data = '$palavra_chave'";

    $sqlv .= ")
              order by mov_data2 desc ";
//   $sqlv="SELECT mov_codigo, a.set_nome AS desc_saida, b.set_nome AS desc_consumo,
//                  to_char(mov_data, 'dd/mm/yyyy') AS mov_data, mov_nr_nota,
//                  mov_data as mov_data2 , mov_codigo,
//                  CASE WHEN mov_saida = 'S' THEN 'Saida de Consumo'
//                       WHEN mov_saida = 'R' THEN 'Perdas'
//                       WHEN mov_saida = 'M' THEN 'Emprestimo'
//                       WHEN mov_saida = 'I' THEN 'Inventario'
//                       WHEN mov_saida = 'D' THEN 'Doacao'
//                       WHEN mov_saida = 'P' THEN 'Permuta'
//                       WHEN mov_saida = 'O' THEN 'Outras Saidas'
//                       WHEN mov_saida = 'A' THEN 'Ajuste'
//                  END AS tiposaida, req_codigo
//                  FROM movimento, setor AS a, setor AS b
//                  WHERE movimento.set_entrada = b.set_codigo
//                  AND movimento.set_saida   = a.set_codigo
//                  AND mov_tipo = 'S'"
//                    .($dados[0]=="" ? "" : " AND a.uni_codigo = ".$dados[0]).
//                  "AND   (a.set_nome like upper('%$palavra_chave%')
//                  OR     b.set_nome like upper('%$palavra_chave%')
//                  OR    upper(case when mov_saida = 'S' then 'Saida de Consumo'
//                       WHEN mov_saida = 'R' THEN 'Perdas'
//                       WHEN mov_saida = 'M' THEN 'Emprestimo'
//                       WHEN mov_saida = 'I' THEN 'Inventario'
//                       WHEN mov_saida = 'D' THEN 'Doacao'
//                       WHEN mov_saida = 'P' THEN 'Permuta'
//                       WHEN mov_saida = 'O' THEN 'Outras Saidas'
//                       WHEN mov_saida = 'A' THEN 'Ajuste'
//                  end) like upper('%$palavra_chave%')
//                     or  mov_nr_nota = '$palavra_chave' ";
//    if (strpos($palavra_chave, "/") != 0)
//       $sqlv .= "or mov_data = '$palavra_chave'";
//
//    $sqlv .= ")
//              order by mov_data2 desc ";
              
    //echo "<pre>$sqlv</pre>";
              
    $sql=db_query($sqlv);
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
		<td colspan='3' style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td>";

     $controle = 0;
    while($row=pg_fetch_array($sql))
    {
        $c1 = "";
        $c2 = "#F2F2F2";
        
        if ($controle == 0)
        {
            $cor = $c1;
            $controle++;
        }
        else
        {
            $cor = $c2;
            $controle = 0;
        }
        echo "<tr bgcolor='$cor'>
                <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[mov_data]</td>
                <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[desc_saida]</td>
                <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[desc_consumo]</td>
                <td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[mov_nr_nota]</td>
                <!--<td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[mov_nr_nota]</td> -->
                <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[tiposaida]</td> ";
           if ( empty($row['req_codigo'])) { 		
                echo "<td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'editar','saida.php?acao=form_edit&mov_codigo='.$row[mov_codigo])."</td>
                <td width=66 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'apagar','saida.php?acao=del&mov_codigo='.$row[mov_codigo])."</td> ";
            }
	    else {
	      echo "<td> &nbsp; </td> <td> &nbsp; </td> ";
	    }
	    echo "
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
//-> Formulario de Adicao de Conteudo
//------------------------------------------------------------------>

if($acao=="form_add")
{
    reglog($id_login,"Formulario de ADICAO SAIDA");


//
//-> Abaixo sao os botoes de voltar / cadastro simples / cadastro completo

  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Opções de Cadastro</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
	       <td width=79><a href=saida.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
            <form name=formbusca method=post action='$PHP_SELF?$QUERY_STRING'>
             <input type='hidden' name='acao' value='form_add'>
             <input type='hidden' name='action' value='buscar'>
             <input type='hidden' name='id_login' value='$id_login'>
	   </fieldset></form>
	  </td>
	 </tr>
        </table></table><br>";

//
//-> Este if esta apontando quando a acao for vazia ele vai mostrar o cadastro simples
//   no cadastro completo vc vai ter que passar a variavel acao para completo

// if(($type=="" OR $acao=="simples")) {
  echo "<form method='post' action='$PHP_SELF?tipo=$tipo'>
	<input type='hidden' name='acao' value='add'>
	<input type='hidden' name='id_login' value='$id_login'>
	<input type='hidden' name='type' value='simple's>
	<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Cabecalho da Saida</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>";
     $sqlsaida=db_query("select nextval('seq_mov_codigo'::text) as novo_codigo");
     $rowsaida = pg_fetch_array($sqlsaida);
     $sqldata_hora = db_query("select to_char(current_date, 'dd/mm/yyyy') as data, extract(hour from current_time) || ':' || extract(minute from current_time) as hora");
     $rowdata_hora = pg_fetch_array($sqldata_hora);
     $mov_data = $rowdata_hora['data'];
     $mov_dt_nota = $rowdata_hora['data'];
     echo "<input type=hidden name=mov_codigo value=$rowsaida[novo_codigo]>
	      <tr>
		<td width=110>Tipo da Saida:</td>
		<td>
		 <select name='mov_saida' class='box' onchange='movimentacao_saida(this)'>
			<optgroup label='HORUS'>
			  <option value='S-VV'>Saida por Validade Vencida</option>
			  <option value='S-PE'>Saida por Perda</option>
			  <option value='S-AEA'>Saida por Amostra, Exposição e Análise</option>
			  <option value='S-DEP'>Saida por Devolução de Produto</option>
			  <option value='S-D'>Saida por Doação</option>
			  <option value='S-TR'>Saida por Transferência e Remanejamento</option>
			  <option value='S-AS'>Saída por Apreensão Sanitária</option>
			  <option value='S-E'>Saída para Empréstimo</option>
			  <option value='S-P'>Saída para Paciente não identificado</option>
			  <option value='S-AE'>Saída por Ajuste de estoque</option>
			</optgroup>
			<optgroup label='PADRÕES'>
			  <option value='S'>Saida de Consumo</option>
			  <option value='A'>Saida de Ajuste</option>
			  <option value='M'>Saida de Emprestimo</option>
			  <option value='I'>Saida de Inventario</option>
			  <option value='D'>Saida por Doação</option>
			  <option value='P'>Saida de Permuta</option>
			  <option value='R'>Saida de Perda</option>
			  <option value='O'>Outras Saidas</option>
			</optgroup>
		 </select>
	      </tr>
	     ";

	     //if( $tipo == 'M' )
	    // {
	     	echo "
	     	<tr id='tb_forncedor' style='display:none'>
				<td>Fornecedor</td>
				<td><select name='for_codigo' class='box'>";

			$stmt_f 	= "SELECT for_codigo, for_nome FROM fornecedor ORDER BY for_nome";
			$qry_f 		= db_query($stmt_f) or die( pg_last_error() );
			print "\n\t\t\t<option value=''>---</option>";
			while( $row_f = pg_fetch_array($qry_f))
			{
				print "\n\t\t\t<option value='$row_f[0]'>$row_f[1]</option>";
			}

			echo "</td>
			</tr>";

	    // } // if tipo

	     echo "
	      <tr>
     		<td width=70>Centro Estocador:</td>
	     	<td>
     		 <select name=set_saida class=box>";
	         //
     	    //-> SQL do Centro Estocador
	         $query = db_query("select * from setor
                                where set_estoque = 'S'"
                                .($dados[0]=="" ? "" : " AND setor.uni_codigo = ".$dados[0]).
                                "order by set_nome");
	           while($setor=pg_fetch_array($query)) {
	                 echo "<option value='$setor[set_codigo]'>$setor[set_nome]</option>";
	           }
	   echo "</select>
	        </td>
         </tr>
	      <tr id='tb_setor' style='display:none'>
     		<td width=70>Setor:</td>
	     	<td>
     		 <select name=set_entrada class=box>
     		 ";
	         //
     	    //-> SQL do Setor onde ocorrera a entrada do produto (saida para consumo) ou
            // nos outros tipos onde deverÃƒÂ¡ ser debitda a sua movimentacao
	         $query = db_query("select * from setor
                                order by set_nome");
	           while($setor=pg_fetch_array($query)) {
	                 echo "<option value='$setor[set_codigo]'>$setor[set_nome]</option>";
	           }
	   echo "</select>
	        </td>
         </tr>
	     <tr>
     		<td width=40>Data de Saida:</td>
    		<td>
            <table cellspacing=0 cellpadding=0 border=0>
                <tr>
                    <td width=10><input type=text name=mov_data id=mov_data maxlength=10 class=box size=20 value=$mov_data  onKeypress=\"return Ajusta_Data(this, event);\"></td>
                </tr>
            </table>
         </tr>
	     <tr>
    		<td width=40>Numero da Saida:</td>
    		<td><input type=text name=mov_nr_nota  class=box size=20 value=$rowsaida[novo_codigo]></td>
        </tr>
        <tr>
    		<td width=40>Requisitante:</td>
    		<td><input type=text name=mov_requisitante class=box size=20></td>
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
	 reglog($id_login,"Formulario de EDICAO SAIDA");

//
//-> Formulario de edicao do cadastro SIMPLES
  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
         <tr>
          <td>
           <fieldset>
            <legend>Opções de Cadastro</legend>
             <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
              <tr>
               <td width=79><a href=saida.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
               <td>&nbsp;</td>
              </tr>
             </table>
           </fieldset>
          </td>
         </tr>
        </table><br>";

//
//-> Pegando as informcoes do banco pra mostrar no formulario
$sqlmovimento =  db_query("SELECT mov_codigo, to_char(mov_data, 'dd/mm/yyyy') AS mov_data, mov_tipo,
                         mov_desconto, mov_observacao, set_saida, set_entrada, mov_nr_nota,
                         to_char(mov_dt_nota, 'dd/mm/yyyy') AS mov_dt_nota, mov_saida, for_codigo,
                         retorna_usuario(usr_codigo) AS login_usuario,
                         to_char(mov_data_inclusao, 'dd/mm/yyyy') as mov_data_inclusao,
                        CASE WHEN mov_saida = 'S' THEN 'Saida de Consumo'
                           WHEN mov_saida = 'R' THEN 'Perdas'
						   WHEN mov_saida = 'S-PE' THEN 'SAIDA POR PERDAS'
						   WHEN mov_saida = 'S-VV' THEN 'SAIDA POR VALIDADE VENCIDA'
						   WHEN mov_saida = 'S-AEA' THEN 'SAIDA POR AMOSTRA, EXPOSIÇÃO E ANÁLISE'
						   WHEN mov_saida = 'S-DEP' THEN 'DEVOLUÇÃO DE ENTRADA DE PRODUTO'
						   WHEN mov_saida = 'S-TR' THEN 'SAÍDA POR TRANSFERÊNCIA E REMANEJAMENTO'
						   WHEN mov_saida = 'S-D' THEN 'SAÍDA POR DOAÇÃO'
						   WHEN mov_saida = 'S-AS' THEN 'SAÍDA POR APREENSÃO SANITÁRIA'
						   WHEN mov_saida = 'S-E' THEN 'SAÍDA PARA EMPRÉSTIMO'
						   WHEN mov_saida = 'S-P' THEN 'SAÍDA PARA PACIENTE NÃO IDENTIFICADO'
						   WHEN mov_saida = 'S-AE' THEN 'SAÍDA POR AJUSTE DE ESTOQUE'
                           WHEN mov_saida = 'M' THEN 'Emprestimo'
                           WHEN mov_saida = 'I' THEN 'Inventario'
                           WHEN mov_saida = 'D' THEN 'Doacao'
                           WHEN mov_saida = 'P' THEN 'Permuta'
                           WHEN mov_saida = 'O' THEN 'Outras Saidas'
                           WHEN mov_saida = 'A' THEN 'Ajuste'
                        END AS tiposaida,
                        mov_requisitante
                        FROM movimento
                WHERE  mov_codigo = '$mov_codigo'");
 $row = pg_fetch_array($sqlmovimento);
 
  echo "<br><br><form method=post action=$PHP_SELF>
	<input type=hidden name=acao value=edit>
	<input type=hidden name=id_login value=$id_login>
	<input type=hidden name=mov_codigo value=$mov_codigo>
	<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Saida de Materiais</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>";
         echo "Ultima Alteracao: $row[login_usuario] - $row[mov_data_inclusao]";
        $sqlcentro=db_query("SELECT set_codigo, set_nome FROM setor WHERE set_codigo = '$row[set_saida]'");
        $rowcentro = pg_fetch_array($sqlcentro);
        $sqlsetor=db_query("SELECT  set_codigo, set_nome FROM setor WHERE set_codigo = '$row[set_entrada]'");
        $rowsetor = pg_fetch_array($sqlsetor);

         echo "
                <tr>
		          <td width=70>Centro Estocador</td>
		          <td width=70><input type=text readonly name=centro_nome size=70 value='$rowcentro[set_nome]'></td>
               </tr>
	      <tr>
		<td width=110>Tipo da Saida:</td>
		<td>
		<!-- <input type='hidden' name='mov_saida' value='$row[mov_saida]' /> -->
		 <select name='mov_saida' class='box'  onchange='movimentacao_saida(this)'>
		  <optgroup label='HORUS'>
				<option value='S-VV' ".($row['mov_saida']=='S-VV' ? 'selected' : '').">Saida por Validade Vencida</option>
				<option value='S-PE' ".($row['mov_saida']=='S-PE' ? 'selected' : '').">Saida por Perda</option>
				<option value='S-AEA' ".($row['mov_saida']=='S-AEA' ? 'selected' : '').">Saida por amostra, exposição e análise</option>
				<option value='S-DEP' ".($row['mov_saida']=='S-DEP' ? 'selected' : '').">Saida por devolução de entrada de produto</option>
				<option value='S-TR' ".($row['mov_saida']=='S-TR' ? 'selected' : '').">Saida por transferÊncia e remanejamento</option>
				<option value='S-D' ".($row['mov_saida']=='S-D' ? 'selected' : '').">Saida por doação</option>
				<option value='S-AS' ".($row['mov_saida']=='S-AS' ? 'selected' : '').">Saida por apreensão sanitária</option>
				<option value='S-E' ".($row['mov_saida']=='S-E' ? 'selected' : '').">Saida por empréstimo</option>
				<option value='S-P' ".($row['mov_saida']=='S-P' ? 'selected' : '').">Saida para paciente não identificado</option>
				<option value='S-AE' ".($row['mov_saida']=='S-AE' ? 'selected' : '').">Saida por ajuste de estoque</option><option value='S' ".($row['mov_saida']=='S' ? 'selected' : '').">Saida de Consumo</option>
			</optgroup>
			<optgroup label='PADRÕES'>
				<option value='A' ".($row['mov_saida']=='A' ? 'selected' : '').">Saida de Ajuste</option>
				<option value='M' ".($row['mov_saida']=='M' ? 'selected' : '').">Saida de Emprestimo</option>
				<option value='I' ".($row['mov_saida']=='I' ? 'selected' : '').">Saida de Inventario</option>
				<option value='P' ".($row['mov_saida']=='P' ? 'selected' : '').">Saida de Permuta</option>
				<option value='D' ".($row['mov_saida']=='D' ? 'selected' : '').">Saida por Doação</option>
				<option value='R' ".($row['mov_saida']=='R' ? 'selected' : '').">Saida de Perda</option>
				<option value='O' ".($row['mov_saida']=='O' ? 'selected' : '').">Outras Saidas</option>
			</optgroup>
		 </select>
	      </tr>";

 		//if( $row['mov_saida'] == 'M' )
	     //{
	     	$style = ( $row['mov_saida'] == 'M' ? 'table-row' : 'none' ) ;
	     	echo "
	     	<tr id='tb_forncedor' style='display:$style;'>
                        <td>Fornecedor</td>
                        <td><select name='for_codigo' class='box'>";

                    $stmt_f = "SELECT for_codigo, for_nome FROM fornecedor ORDER BY for_nome";
                    $qry_f 	= db_query($stmt_f) or die( pg_last_error() );
                    while( $row_f = pg_fetch_array($qry_f))
                    {
                        $sel = $row['for_codigo'] == $row_f[0] ?  'selected' : '';
                        print "\n\t\t\t<option value='$row_f[0]' $sel>$row_f[1]</option>";
                    }
                    echo "</td>
                    </tr>";
	     //} // if tipo
             
            echo "<tr id='tb_setor' ".
              ( $row['mov_saida'] == 'A' || $row['mov_saida'] == 'P' || $row['mov_saida'] == 'D' || $row['mov_saida'] == 'S' ?
               "style='display:none;'" : "" )." >
     		<td width=70>Setor:</td>
	     	<td>
     		 <select name=set_entrada class=box>
     		 ";
                $query = db_query("SELECT * FROM setor ORDER BY set_nome");
                while($setor=pg_fetch_array($query))
                {
                    echo ($setor[set_codigo]==$row[set_entrada])?"<option value='$setor[set_codigo]' selected>
                        $setor[set_nome]</option>":"<option value='$setor[set_codigo]'>$setor[set_nome]</option>";
                }
	   echo "</select>
           </tr>
           
	     <tr>
		<td width=70>Data da Saida:</td>
		<td colspan=2><input type=text name=mov_data class=box size=20 value='$row[mov_data]' maxlength='10'  onKeypress=\"return Ajusta_Data(this, event);\"></td>
	      </tr>
             <tr>
		<td width=70>Numero da Saida:</td>
		<td colspan=2><input type=text name=mov_nr_nota class=box size=20 value='$row[mov_nr_nota]'></td>
	      </tr>
            <tr>
    		<td width=40>Requisitante:</td>
    		<td colspan=2><input type=text name=mov_requisitante class=box size=20 value='$row[mov_requisitante]'></td>
            </tr>
	     <tr>
		<td width=40>Observacao:</td>
            <td colspan=2><textarea name=mov_observacao class=box cols=100 rows=2>$row[mov_observacao]</textarea></td>
	      </tr>
	      <tr>
	       <td><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg></td>
	       <td colspan=2><a href=itens_saida.php?id_login=$id_login&mov_codigo=$mov_codigo&action=form_inclui_item></a></td> 
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

if($acao=="add")
{
	if( $mov_saida == 'A' || $mov_saida == 'D' || $mov_saida == 'P' )
        {
            $set_entrada = $set_saida;
        }
        
        reglog($id_login,"Adicionando Registro em SAIDA");

	$tipo_movimento = 'S';
    
    $select = "SELECT max(setp_codigo), set_codigo, setp_data_inicial, setp_data_final
                FROM setor_periodo
                WHERE set_codigo = $set_saida
                AND '$mov_data' BETWEEN setp_data_inicial AND setp_data_final
                GROUP BY set_codigo, setp_data_inicial, setp_data_final";

    $exec_select = db_query($select);
    $quantidade = pg_fetch_array($exec_select);
    if(pg_num_rows($exec_select) > 0 || $set_saida == 99404 )
    {
    
        $stmt = ("INSERT INTO movimento ( " .
                "mov_codigo, " .
                "mov_data, " .
                "mov_tipo, " .
                "mov_saida, " .
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
                "mov_total_nota,  " .
                "for_codigo,  " .
                "mov_requisitante  " .
                ") VALUES ( " .
                "$mov_codigo" . ", " .  //grava o codigo do movimento para que possa passar posteriormente para o outro
                ($mov_data ? "'$mov_data'" : "null") . ", " .
                "'{$tipo_movimento}'" . ", " .  //tipo da movimentaÃƒÂ§ÃƒÂ£o = E - Entrada
                "'{$mov_saida}'" . ", " .
                ($usu_codigo ? "'$usu_codigo'" : "null") . ", " .
                ($mov_desconto ? "'$mov_desconto'" : "null") . ", " .
                ($mov_observacao ? "'$mov_observacao'" : "null") . ", " .
                ($tipo_movimento == "T"?"NULL":($mov_saida == "I" || $mov_saida == "A" ? "'$set_saida'" : ($set_entrada ? "'$set_entrada'" : "null"))) . ", " .
                ($set_saida ? "'$set_saida'" : "null") . ", " .
                ($mov_nr_nota ? "'$mov_nr_nota'" : "null") . ", " .
                ($mov_dt_nota ? "'$mov_dt_nota'" : "null") . ", " .
                ($id_login ? "'$id_login'" : "null") . ", " . //Mover o login do usuario que esta usando
                "date(now())" . ", " .
                ($mov_ip ? "'$mov_ip'" : "null") . ", " . //Mover o ip do micro do que o usuario esta usando
                ($mov_total_nota ? "'$mov_total_nota'" : "null") . ",  " . //Fazer update na gravacao da nota
                ( $for_codigo ? $for_codigo : 'null' ). ",  " .  // codigo do fornecedorc
                ( $mov_requisitante ? "'$mov_requisitante'" : 'null' ). // codigo do fornecedorc
                ")");
            $sql = db_query( $stmt ) or die("ERRO:".pg_last_error($db));
        echo "
        <script type=\"text/javascript\">
            setTimeout(\"location='itens_saida.php?id_login=$id_login&mov_codigo=$mov_codigo&action=form_inclui_item'\", 0);
        </script>";
    } else {
      
        echo "<script>";
            echo "aviso = 'Data fora do intervalo cadastrado. \\n\\n';";
            echo "aviso+='Voce devera comunicar o Almoxarifado que devera alterar o periodo valido para o seu Centro Estocador ';";            
            echo "aviso+='e devera reiniciar o processo de movimentacao de saida.\\n\\n';";
            echo "var teste=alert(aviso);";
            echo "history.back(1);";
        echo "</script>";
    }
}

//
//-> EDIT <--------------------------------------------------------->
if($acao=="edit")
{
    if( $mov_saida == 'A' || $mov_saida == 'D' || $mov_saida == 'P' )
    {
        $set_entrada = $set_saida;
    }
    
    reglog($id_login,"Editando SAIDA $mov_codigo");
    
    /*$select = "select max(a.setp_codigo), set_codigo, setp_data_inicial, setp_data_final
                from setor_periodo
                where set_codigo = $set_entrada
                and '$mov_data' between setp_data_inicial and setp_data_final
                group by set_codigo, setp_data_inicial, setp_data_final";
                  */
    
    
    //$exec_select = db_query($select);
    //$quantidade = pg_fetch_array($exec_select);
    
    //if(pg_num_rows($exec_select) > 0)
    //{
    
    $sel = "SELECT mov_data
            FROM movimento
            WHERE mov_data BETWEEN (current_date - 30) AND (current_date)
            AND mov_codigo = $mov_codigo";
    
    $exec_sel = db_query($sel);
    
    if(pg_num_rows($exec_sel))
    {
        $stmt = "UPDATE movimento SET " .
                ($mov_data ? "mov_data='$mov_data'" : "mov_data=null") . ", " .
                ($usu_codigo ? "usu_codigo='$usu_codigo'" : "usu_codigo=null") . ", " .
                ($mov_desconto ? "mov_desconto='$mov_desconto'" : "mov_desconto=null") . ", " .
                ($set_entrada ? "set_entrada='$set_entrada'" : "set_entrada=null") . ", " .
                ($mov_observacao ? "mov_observacao='$mov_observacao'" : "mov_observacao=null") . ", " .
                ($mov_nr_nota ? "mov_nr_nota='$mov_nr_nota'" : "mov_nr_nota=null") . ", " .
                ($mov_dt_nota ? "mov_dt_nota='$mov_dt_nota'" : "mov_dt_nota=null") . ", " .
                ($id_login ? "usr_codigo='$id_login'" : "usr_codigo=null") . ", " .
                "mov_data_inclusao = date(now()),  " .
                "mov_saida = '$mov_saida'" . ", " .  //tipo da movimentaÃƒÂ§ÃƒÂ£o = E - Entrada
                ($mov_ip ? "mov_ip='$mov_ip'" : "mov_ip=null") . ", " .
                ($mov_total_nota ? "mov_total_nota='$mov_total_nota'" : "mov_total_nota=null") . ",  " .
                "for_codigo = ".($for_codigo ? $for_codigo : "null") . ",  " .
                "mov_requisitante = '".($mov_requisitante ? $mov_requisitante : "null") . "'  " .
                "WHERE mov_codigo='$mov_codigo'";
        $sql = db_query($stmt);
    }
        echo "
        <script type=\"text/javascript\">
            setTimeout(\"location='itens_saida.php?id_login=$id_login&mov_codigo=$mov_codigo&action=form_inclui_item'\", 0);
        </script>";
    /*} else {
        echo "<script>";
            echo "alert('Data invalida para saida. Centro de Saida sem periodo valido.');";
            echo "history.back(1);";
        echo "</script>";
    }*/
}

//
//-> DEL <---------------------------------------------------------->

if($acao=="del")
{
    $mov_codigo = intval($mov_codigo);
    
    reglog($id_login,"Exluindo Registro de SAIDA $mov_codigo");
    $stmt = "begin;";
//    $stmt .= "delete from itens_movimento where mov_codigo = $mov_codigo;";
    $stmt .= "DELETE FROM movimento WHERE mov_codigo='$mov_codigo';";
    $stmt .= "commit";
    $sql = db_query( $stmt, $LOG=true );
    
    msg($id_login,'txt', $sql,'APAGADO com Sucesso','ERRO: o movimento ainda possue itens');
}

?>
</fieldset>
