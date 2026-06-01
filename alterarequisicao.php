<?php
/*
 * alterações 11/07/2007: adicionado campo para cadastro de requisitante, e
 * alteracao no BD ( ALTER TABLE requisicao ADD COLUMN req_requisitante varchar(100); )
 * 
 * alterações dia 16/05/2007: adicionado restrição no acao == "add" e no acao == "edit"
 * para restringir as operações com o data, ou seja a data tem que estar dentro de um
 * periodo cadastrado na tabela setor_periodo que corresponda ao setor em questão
 *
 * @author	Anderson - 14/05/2007 - 10:56
 * @brief	Colocando a janela de calendario
 *
 * @version	Renato 11/5/2007 - 09:15
 * alterações: se o req_entrada for I grava o mesmo valor para set_entrada e set_requisicao.
 *
 * @version Marcos C. Ramos 27/07/2007 18:25
 * alterações: Se o periodo para o centro estocador estiver fora do intervalo cadastrado, solicita que seja cadastrado
 *              o periodo para aquele centro estocador.
 *
 * @version Marcos C. Ramos 28/07/2007 08:45
 * alteracoes: comentado a parte do codigo que exibia o botão ver itens que tinha a mesma funcao do botao editar OS 280
 */
?>
<script language="JavaScript" type="text/javascript" src="funcoes.js"></script>
<script>
function abre_movim(id_login, req_nr_nota)
{
    // O Parametro que esta sendo passado para este valor e o req_codigo e nao foi alterado para continuar funcionando ok
    // Marco Aurelio - 20/12/2006
    window.open('/relatorio/ReqExibir.php?acao=form_edit&id_login='+id_login+'&req_nr_nota='+req_nr_nota,null,"height=400,width=750,status=yes,toolbar=no,menubar=no,resizable=yes, location=no,scrollbars=yes");
}

function movimentacao_requisicao( obj )
{
    if( obj.value == 'M' ) {
        document.getElementById('tb_forncedor').style.display='table-row';
        //document.location.href = 'requisicao.php?id_login='+id_login+'&acao=form_add&tipo=M';
    } else if (obj.value == 'D') {
        document.getElementById('tb_forncedor').style.display='table-row';
    } else {
        document.getElementById('tb_forncedor').style.display='none';
    }
    
    if( obj.value != 'A' && obj.value != 'D' && obj.value != 'P' )
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

$stmt = "SELECT uni_codigo FROM usuarios WHERE usr_codigo = $id_login";
$stmt = db_query($stmt);
$dados = pg_fetch_array($stmt);
//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
reglog($id_login,"Entrando em REQUISICAO");
//------------------------------------------------------------------>

echo "<fieldset><legend>ALTERACAO DE REQUISICAO DE MATERIAIS</legend>";

$vepermissao = pg_fetch_array(db_query("SELECT usr_requisicao FROM usuarios WHERE usr_codigo = $id_login"));
if ($vepermissao['usr_requisicao'] == 'N')
{
    echo ("<script>
    alert('Você não tem permissão para fazer reqRequisicao de Estoque. Procure o Administrador do Estoque ou do Sistema!!!!');
    window.history.back();
    </script>");
    //die;
}

if ($final == 1) {
    $stmt = "UPDATE requisicao SET req_finalizado = 'S'" .
            "WHERE req_codigo='$requis'";
    $sql = db_query($stmt);
}

if (empty($acao) || ($acao == 'form_requisicao'))
{
    
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
                                <td width=95>".ChmodBtn($id_login,'adicionar','requisicao.php?acao=form_add')."</td>";
                                if (chmodbtn($id_login,"procurar_if","alterarequisicao.php"))
                                {                                
                                    echo "<form method=post action=$PHP_SELF>";
                                }
                                echo "
                                        <input type=hidden name=acao value=busca>
                                        <input type=hidden name=id_login value=$id_login>
                                        <td width=180 align=right>Buscar</td>
                                        <td width=90><input type=text name=palavra_chave class=box onBlur=\"javascript:this.value=this.value.toUpperCase();\"></td>
                                        <td>".ChmodBtn($id_login,'procurar','alterarequisicao.php')."</td>
                                </form>
                            </tr>
                        </table>
                    </fieldset>
                </td>
            </tr>
    </table><br>";
    
    //
    //-> Listando

    if (chmodbtn($id_login,"listar_if","alterarequisicao.php"))
    {
            echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
                    <tr>
                        <td>
                            <fieldset>
                                <legend>Listando &Uacute;timas <b>15</b> Requisicoes Cadastradas</legend>
                                <table width=100% align=center cellspacing=2 cellpadding=4 border=0>
                                    <tr bgcolor=F9f9f9>
                                        <td width=40 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Data</td>
                                        <td width=200 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Centro Estoc.</td>
                                        <td width=200 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Setor</td>
                                        <td width=200 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Num.Requisicao</td>
                                        <td width=200 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Tipo Mov.</td>
                                        <td width=200 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Num.Mov.</td>
                                        <td width=200 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Finalizado</td>
                                        <td colspan=3 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td>";
            
            $sql=db_query("SELECT req_codigo, a.set_nome AS desc_saida, b.set_nome AS desc_consumo, req_nr_nota,
                           to_char(req_data,'DD/MM/YYYY') AS req_data , req_codigo,
                           CASE WHEN req_saida = 'S' THEN 'Requisicao de Consumo'
                            END AS tiposaida, 
			    (select mov_codigo from movimento where req_codigo = requisicao.req_codigo) as nummov,
			    req_finalizado
                            FROM requisicao, setor AS a, setor AS b
                            WHERE requisicao.set_saida = a.set_codigo
                            AND requisicao.set_entrada = b.set_codigo
			    AND req_finalizado = 'S'
                            AND req_tipo = 'S'"
                                         .($dados[0]=="" ? "" : " AND a.uni_codigo = ".$dados[0]).
                           "ORDER BY req_codigo DESC LIMIT 15");

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
		$finalizado = "NAO";
		if ($row['req_finalizado'] == 'S') $finalizado = "SIM";
                echo "<tr bgcolor='$cor'>
                        <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[req_data]</td>
                        <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[desc_saida]</td>
                        <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[desc_consumo]</td>
                <!--    <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[req_nr_nota]</td> -->
                        <td align=center width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[req_nr_nota]</td>
                        <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[tiposaida]</td>
                        <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[nummov]</td> 
                        <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$finalizado</td> 
			";
		if ( empty ($row['nummov'] )) {
                   echo " <td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'editar','alterarequisicao.php?acao=form_edit&req_codigo='.$row[req_codigo])."</td>
                        <td width=66 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'apagar','alterarequisicao.php?acao=del&req_codigo='.$row[req_codigo])."</td> ";
            }
	    else {
	      echo "<td> &nbsp; </td> <td> &nbsp; </td> ";
	    }
                 echo " <td width=66 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'><a href='javascript:abre_movim($id_login, $row[req_codigo])'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/imprimir.jpg border=0></a></td>
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
    reglog($id_login,"Buscando em REQUISICAO: $palavra_chave ");
   
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
                            <td width=95>".ChmodBtn($id_login,'adicionar','requisicao.php?acao=form_add')."</td>";
                            if (chmodbtn($id_login,"procurar_if","alterarequisicao.php"))
                            {
                                echo "<form method=post action=$PHP_SELF>";
                            }
                            echo "
                                    <input type=hidden name=acao value=busca>
                                    <input type=hidden name=id_login value=$id_login>
                                    <td width=180 align=right>Buscar:</td>
                                    <td width=90><input type=text name=palavra_chave class=box onBlur=\"javascript:this.value=this.value.toUpperCase();\"></td>
                                    <td>".ChmodBtn($id_login,'procurar','alterarequisicao.php')."</td>
                            </form>
                        </tr>
                    </table>
                </fieldset>
            </td>
        </tr>
    </table><br>";

   $sqlv="SELECT req_codigo, a.set_nome AS desc_saida, b.set_nome AS desc_consumo,
                  to_char(req_data, 'dd/mm/yyyy') AS req_data, req_nr_nota,
                  req_data as req_data2 , req_codigo,
                  CASE WHEN req_saida = 'S' THEN 'Requisicao de Consumo'
                  END AS tiposaida, 
		  (select mov_codigo from movimento where req_codigo = requisicao.req_codigo) as nummov, req_finalizado
                  FROM requisicao, setor AS a, setor AS b
                  WHERE requisicao.set_entrada = b.set_codigo
                  AND requisicao.set_saida   = a.set_codigo
		  AND req_finalizado = 'S' 
                  AND req_tipo = 'S'"
                    .($dados[0]=="" ? "" : " AND a.uni_codigo = ".$dados[0]).
                  "AND   (a.set_nome like upper('%$palavra_chave%')
                  OR     b.set_nome like upper('%$palavra_chave%')
                  OR    upper(case when req_saida = 'S' then 'Requisicao de Consumo'
                  end) like upper('%$palavra_chave%')
                     or  req_nr_nota = '$palavra_chave' ";
    if (strpos($palavra_chave, "/") != 0)
       $sqlv .= "or req_data = '$palavra_chave'";

    $sqlv .= ")
              order by a.set_nome, req_data2 desc ";
              
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
		<td width=200 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Num.Requisicao</td>
		<td width=200 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Tipo Mov.</td>
		<td width=200 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Num.Mov.</td>
		<td width=200 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Finalizado</td>
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
        if ($row['req_finalizado'] == 'S') $finalizado = "SIM";
        echo "<tr bgcolor='$cor'>
                <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[req_data]</td>
                <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[desc_saida]</td>
                <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[desc_consumo]</td>
                <td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[req_nr_nota]</td>
                <!--<td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[req_nr_nota]</td> -->
                <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[tiposaida]</td>
                <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[nummov]</td>
             <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$finalizado</td> "; 
		if ( empty ($row['nummov'] )) {
                echo "<td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'editar','alterarequisicao.php?acao=form_edit&req_codigo='.$row[req_codigo])."</td>
                <td width=66 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'apagar','alterarequisicao.php?acao=del&req_codigo='.$row[req_codigo])."</td> ";
		}
		else {
		echo "
                <td width=66 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'><a href='javascript:abre_movim($id_login, $row[req_codigo])'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/imprimir.jpg border=0></a></td>
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
//-> Formulario de Adicao de Conteudo
//------------------------------------------------------------------>

if($acao=="form_add")
{
    reglog($id_login,"Formulario de ADICAO REQUISICAO");


//
//-> Abaixo sao os botoes de voltar / cadastro simples / cadastro completo

  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Opções de Cadastro</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
	       <td width=79><a href=alterarequisicao.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
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
	    <legend>Cabecalho da Requisicao</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>";
     $sqlrequisicao=db_query("select nextval('seq_req_codigo'::text) as novo_codigo");
     $rowrequisicao = pg_fetch_array($sqlrequisicao);
     $sqldata_hora = db_query("select to_char(current_date, 'dd/mm/yyyy') as data, extract(hour from current_time) || ':' || extract(minute from current_time) as hora");
     $rowdata_hora = pg_fetch_array($sqldata_hora);
     $req_data = $rowdata_hora['data'];
     $req_dt_nota = $rowdata_hora['data'];
     echo "<input type=hidden name=req_codigo value=$rowrequisicao[novo_codigo]>
           <input type=hidden name=req_saida  value='S']>
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
                                where set_estoque = 'S' AND set_distribuidor = 'S'"
             //                   .($dados[0]=="" ? "" : " AND setor.uni_codigo = ".$dados[0]).
                                . "order by set_nome");
	           while($setor=pg_fetch_array($query)) {
	                 echo "<option value='$setor[set_codigo]'>$setor[set_nome]</option>";
	           }
	   echo "</select>
	        </td>
         </tr>
	      <tr id='tb_setor'>
     		<td width=70>Setor:</td>
	     	<td>
     		 <select name=set_entrada class=box>
     		 ";
	         //
     	    //-> SQL do Setor onde ocorrera a entrada do produto (saida para consumo) ou
            // nos outros tipos onde deverÃƒÂ¡ ser debitda a sua movimentacao
	         $query = db_query("select * from setor
				 where set_estoque = 'N' 
				 and   set_codigo in (select set_codigo from usuarios_setores
				                      where usr_codigo = $id_login)
                                order by set_nome");
	           while($setor=pg_fetch_array($query)) {
	                 echo "<option value='$setor[set_codigo]'>$setor[set_nome]</option>";
	           }
	   echo "</select>
	        </td>
         </tr>
	     <tr>
     		<td width=40>Data de Requisicao:</td>
    		<td>
            <table cellspacing=0 cellpadding=0 border=0>
                <tr>
                    <td width=10><input type=text name=req_data id=req_data class=box size=20 value=$req_data  onKeypress=\"return Ajusta_Data(this, event);\"></td>
                    <td>&nbsp;<img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/calendario.png onclick=\"abrirCalendario('req_data');return false;\" style='cursor:pointer'></td>
                </tr>
            </table>
         </tr>
	     <tr>
    		<td width=40>Numero da Requisicao:</td>
    		<td><input type=text name=req_nr_nota class=box size=20 value=$rowrequisicao[novo_codigo]></td>
        </tr>
        <tr>
    		<td width=40>Requisitante:</td>
    		<td><input type=text name=req_requisitante class=box size=20></td>
        </tr>
	     <tr>
		<td width=40>Observacao:</td>
            <td ><textarea name=req_observacao class=box cols=100 rows=2></textarea></td>
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
	 reglog($id_login,"Formulario de EDICAO REQUISICAO");

//
//-> Formulario de edicao do cadastro SIMPLES
  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
         <tr>
          <td>
           <fieldset>
            <legend>Opções de Cadastro</legend>
             <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
              <tr>
               <td width=79><a href=alterarequisicao.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
               <td>&nbsp;</td>
              </tr>
             </table>
           </fieldset>
          </td>
         </tr>
        </table><br>";

//
//-> Pegando as informcoes do banco pra mostrar no formulario
$sqlrequisicao =  db_query("SELECT req_codigo, to_char(req_data, 'dd/mm/yyyy') AS req_data, req_tipo,
                         req_desconto, req_observacao, set_saida, set_entrada, req_nr_nota,
                         to_char(req_dt_nota, 'dd/mm/yyyy') AS req_dt_nota, req_saida, for_codigo,
                         retorna_usuario(usr_codigo) AS login_usuario,
                         to_char(req_data_inclusao, 'dd/mm/yyyy') as req_data_inclusao,
                        CASE WHEN req_saida = 'S' THEN 'Requisicao de Consumo'
                        END AS tiposaida,
                        req_requisitante
                        FROM requisicao
                WHERE  req_codigo = '$req_codigo'");
 $row = pg_fetch_array($sqlrequisicao);
 
  echo "<br><br><form method=post action=$PHP_SELF>
	<input type=hidden name=acao value=edit>
	<input type=hidden name=id_login value=$id_login>
	<input type=hidden name=req_codigo value=$req_codigo>
	<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Requisicao de Materiais</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>";
         echo "Ultima Alteracao: $row[login_usuario] - $row[req_data_inclusao]";
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
		<td width=110>Tipo da Requisicao:</td>
		<td> <b>ALTERACAO DE REQUISICAO DE PRODUTO DE CONSUMO</b></td>
		 <input type='hidden' name='req_saida' value='S'> 
	      </tr>";

 		//if( $row['req_saida'] == 'M' )
	     //{
	     	$style = ( $row['req_saida'] == 'M' ? 'table-row' : 'none' ) ;
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
              ( $row['req_saida'] == 'A' || $row['req_saida'] == 'P' || $row['req_saida'] == 'D' ?
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
		<td width=70>Data da Requisicao:</td>
		<td colspan=2><input type=text name=req_data class=box size=20 value='$row[req_data]' maxlength='10'  onKeypress=\"return Ajusta_Data(this, event);\"></td>
	      </tr>
             <tr>
		<td width=70>Numero da Requisicao:</td>
		<td colspan=2><input type=text name=req_nr_nota class=box size=20 value='$row[req_nr_nota]'></td>
	      </tr>
            <tr>
    		<td width=40>Requisitante:</td>
    		<td colspan=2><input type=text name=req_requisitante class=box size=20 value='$row[req_requisitante]'></td>
            </tr>
	     <tr>
		<td width=40>Observacao:</td>
            <td colspan=2><textarea name=req_observacao class=box cols=100 rows=2>$row[req_observacao]</textarea></td>
	      </tr>
	      <tr>
	       <td colspan=2><a href=alteraitens_requisicao.php?id_login=$id_login&req_codigo=$req_codigo&action=form_inclui_item><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/veritens_on.jpg border=0></a></td> 
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br></form>";
}
//	       <td><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg></td> >

//------------------------------------------------------------------>
//-> SQL's
//------------------------------------------------------------------>
//
//-> ADD <---------------------------------------------------------->

if($acao=="add")
{
	if( $req_saida == 'A' || $req_saida == 'D' || $req_saida == 'P' )
        {
            $set_entrada = $set_saida;
        }
        

	if (! $set_entrada) {
	       print " 
		<script type=\"text/javascript\">
			alert(\"Nao e possivel incluir a Requisicao. Escolha o setor solicitante.\")
                        setTimeout(\"location='alterarequisicao.php?id_login=$id_login'\", 0);
		</script>";
		exit();
	       }
    $select = "SELECT count(*) from req_dispensado
               where codsetorsolicit = $set_entrada 
	       and   ireq_quantidade is not null";
    $exec_select = db_query($select);
    $contdispensado = pg_fetch_array($exec_select);
	if ($contdispensado[0] > 0) {
	       print " 
		<script type=\"text/javascript\">
			alert(\"Nao e possivel incluir a Requisicao. Ha requisicoes pendentes de confirmacao.\")
                        setTimeout(\"location='alterarequisicao.php?id_login=$id_login'\", 0);
		</script>";
		exit();
	       }
        reglog($id_login,"Adicionando Registro em REQUISICAO $req_codigo");

	$tipo_requisicao = 'S';
    
    $select = "SELECT max(setp_codigo), set_codigo, setp_data_inicial, setp_data_final
                FROM setor_periodo
                WHERE set_codigo = $set_saida
                AND '$req_data' BETWEEN setp_data_inicial AND setp_data_final
                GROUP BY set_codigo, setp_data_inicial, setp_data_final";

    $exec_select = db_query($select);
    $quantidade = pg_fetch_array($exec_select);
    if(pg_num_rows($exec_select) > 0 || $set_saida == 99404 )
    {
    
        $stmt = ("INSERT INTO requisicao ( " .
                "req_codigo, " .
                "req_data, " .
                "req_tipo, " .
                "req_saida, " .
                "usu_codigo, " .
                "req_desconto, " .
                "req_observacao, " .
                "set_entrada, " .
                "set_saida, " .
                "req_nr_nota, " .
                "req_dt_nota, " .
                "usr_codigo, " .
                "req_data_inclusao, " .
                "req_ip, " .
                "req_total_nota,  " .
                "for_codigo,  " .
                "req_requisitante  " .
                ") VALUES ( " .
                "$req_codigo" . ", " .  //grava o codigo do requisicao para que possa passar posteriormente para o outro
                ($req_data ? "'$req_data'" : "null") . ", " .
                "'{$tipo_requisicao}'" . ", " .  //tipo da movimentaÃƒÂ§ÃƒÂ£o = E - Entrada
                "'{$req_saida}'" . ", " .
                ($usu_codigo ? "'$usu_codigo'" : "null") . ", " .
                ($req_desconto ? "'$req_desconto'" : "null") . ", " .
                ($req_observacao ? "'$req_observacao'" : "null") . ", " .
                ($req_saida == "I" || $req_saida == "A" ? "'$set_saida'" : ($set_entrada ? "'$set_entrada'" : "null")) . ", " .
                ($set_saida ? "'$set_saida'" : "null") . ", " .
                ($req_nr_nota ? "'$req_nr_nota'" : "null") . ", " .
                ($req_dt_nota ? "'$req_dt_nota'" : "null") . ", " .
                ($id_login ? "'$id_login'" : "null") . ", " . //Mover o login do usuario que esta usando
                "date(now())" . ", " .
                ($req_ip ? "'$req_ip'" : "null") . ", " . //Mover o ip do micro do que o usuario esta usando
                ($req_total_nota ? "'$req_total_nota'" : "null") . ",  " . //Fazer update na gravacao da nota
                ( $for_codigo ? $for_codigo : 'null' ). ",  " .  // codigo do fornecedorc
                ( $req_requisitante ? "'$req_requisitante'" : 'null' ). // codigo do fornecedorc
                ")");
            $sql = db_query( $stmt ) or die("ERRO:".pg_last_error($db));
        echo "
        <script type=\"text/javascript\">
            setTimeout(\"location='alteraitens_requisicao.php?id_login=$id_login&req_codigo=$req_codigo&action=form_inclui_item'\", 0);
        </script>";
    }
    
    /*else {
      
        echo "<script>";
            echo "aviso = 'Data fora do intervalo cadastrado. Deseja alterar/cadastrar periodo para este Centro Estocador? \\n\\n';";
            echo "aviso+='Se clicar em OK, voce sera redirecionado para parte do sistema de cadastro de periodos para Centros Estocadores ';";            
            echo "aviso+='e devera reiniciar o processo de movimentacao de requisicao.\\n\\n';";
            echo "aviso+='Se clicar em CANCELAR, voce voltara para a tela imediatamente anterior a esta.';";
            echo "var teste=confirm(aviso);";
            echo "if (teste)";
            echo "{";            
            echo "location.href='abertura_requisicao.php?acao=&id_login='+$id_login";
            echo "}";
            echo "else";
            echo "{";
            echo "history.back(1);";
            echo "}";
        echo "</script>";
    } */
}

//
//-> EDIT <--------------------------------------------------------->
if($acao=="edit")
{
    if( $req_saida == 'A' || $req_saida == 'D' || $req_saida == 'P' )
    {
        $set_entrada = $set_saida;
    }
    
    reglog($id_login,"Editando REQUISICAO $req_codigo");
    
    /*$select = "select max(a.setp_codigo), set_codigo, setp_data_inicial, setp_data_final
                from setor_periodo
                where set_codigo = $set_entrada
                and '$req_data' between setp_data_inicial and setp_data_final
                group by set_codigo, setp_data_inicial, setp_data_final";
                  */
    
    
    //$exec_select = db_query($select);
    //$quantidade = pg_fetch_array($exec_select);
    
    //if(pg_num_rows($exec_select) > 0)
    //{
    
    $sel = "SELECT req_data
            FROM requisicao
            WHERE req_data BETWEEN (current_date - 30) AND (current_date)
            AND req_codigo = $req_codigo";
    
    $exec_sel = db_query($sel);
    
    if(pg_num_rows($exec_sel))
    {
        $stmt = "UPDATE requisicao SET " .
                ($req_data ? "req_data='$req_data'" : "req_data=null") . ", " .
                ($usu_codigo ? "usu_codigo='$usu_codigo'" : "usu_codigo=null") . ", " .
                ($req_desconto ? "req_desconto='$req_desconto'" : "req_desconto=null") . ", " .
                ($set_entrada ? "set_entrada='$set_entrada'" : "set_entrada=null") . ", " .
                ($req_observacao ? "req_observacao='$req_observacao'" : "req_observacao=null") . ", " .
                ($req_nr_nota ? "req_nr_nota='$req_nr_nota'" : "req_nr_nota=null") . ", " .
                ($req_dt_nota ? "req_dt_nota='$req_dt_nota'" : "req_dt_nota=null") . ", " .
                ($id_login ? "usr_codigo='$id_login'" : "usr_codigo=null") . ", " .
                "req_data_inclusao = date(now()),  " .
                "req_saida = '$req_saida'" . ", " .  //tipo da movimentaÃƒÂ§ÃƒÂ£o = E - Entrada
                ($req_ip ? "req_ip='$req_ip'" : "req_ip=null") . ", " .
                ($req_total_nota ? "req_total_nota='$req_total_nota'" : "req_total_nota=null") . ",  " .
                "for_codigo = ".($for_codigo ? $for_codigo : "null") . ",  " .
                "req_requisitante = '".($req_requisitante ? $req_requisitante : "null") . "'  " .
                "WHERE req_codigo='$req_codigo'";
        $sql = db_query($stmt);
    }
        echo "
        <script type=\"text/javascript\">
            setTimeout(\"location='alteraitens_requisicao.php?id_login=$id_login&req_codigo=$req_codigo&action=form_inclui_item'\", 0);
        </script>";
    /*} else {
        echo "<script>";
            echo "alert('Data invalida para saida. Centro de Requisicao sem periodo valido.');";
            echo "history.back(1);";
        echo "</script>";
    }*/
}

//
//-> DEL <---------------------------------------------------------->

if($acao=="del")
{
    $req_codigo = intval($req_codigo);
    
    reglog($id_login,"Exluindo Registro de REQUISICAO $req_codigo");
    $stmt = "begin;";
    $stmt .= "delete from itens_requisicao where req_codigo = $req_codigo;";
    $stmt .= "DELETE FROM requisicao WHERE req_codigo='$req_codigo';";
    $stmt .= "commit";
    $sql = db_query( $stmt, $LOG=true );
    
    msg($id_login,'txt', $sql,'APAGADO com Sucesso','ERRO: o requisicao ainda possue itens');
}

?>
</fieldset>
