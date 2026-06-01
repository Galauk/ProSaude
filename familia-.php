<script type="text/javascript" src="ajax_motor.js"></script>
<script type="text/javascript" src="paciente.js"></script>
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
//------------------------------------------------------------------>

 if(empty($acao)) {

//
//-> Botoes
  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Opções</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
	       <td width=95>".ChmodBtn($id_login,'adicionar','familia.php?acao=form_add')."</td>
           <td width=56 align=right>".ChmodBtn($id_login,'area','area.php?acao=')."</td>
           <td width=86>".ChmodBtn($id_login,'microarea','microarea.php?acao=')."</td>
           <td width=116>".ChmodBtn($id_login,'atendimento_psf','atendimento_psf.php?acao=')."</td>
   	        <form method=post action=$PHP_SELF>
		     <input type=hidden name=acao value=busca>
	<input type=hidden name=id_login value=$id_login>
	         <td width=30>Buscar:</td>
	         <td width=120><input type=text name=palavra_chave class=box onBlur=\"javascript:this.value=this.value.toUpperCase();\"></td>
	         <td>".ChmodBtn($id_login,'procurar','familia')."</td>
            </form>
	       <td width=107><a href='logoff.php?id_login=$id_login' target='_parent'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/sair.gif border=0></a></td>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br>";

//
//-> Listando
  
  echo "
    <table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Listando Últimas 15 <b>Familias</b> Cadastradas</legend>
	     <table width=100% align=center cellspacing=2 cellpadding=4 border=0>
	      <tr bgcolor=F9f9f9 align=center>
		   <td width=10  style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Nr.Ficha</td>
		   <td width=140 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Responsavel</td>
		   <td width=20  style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Nr.Pess</td>
		   <td width=5   style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>MicroReg</td>
		   <td width=20  style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Endereco</td>
		   <td width=20  style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Cidade</td>
		   <td colspan=2 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td>";
           $sql=pg_query("SELECT A.*, B.mic_descricao, C.cid_nome, fam_responsavel
                            FROM Familia A, Microarea B, cidade C
                           WHERE A.mic_codigo = B.mic_codigo
                             and A.cid_codigo = C.cid_codigo 
                        ORDER BY fam_responsavel asc limit 15");
           while($row=pg_fetch_array($sql)) {
           echo "<tr>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[fam_nr_ficha]</td>
	       <td align=left   style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[fam_responsavel]</td>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[fam_nr_pessoas]</td>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[mic_descricao]</td>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[fam_endereco]</td>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[cid_nome]</td>
	       <td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'editar','familia.php?acao=form_edit&fam_codigo='.$row[fam_codigo])."</td>
	       <td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'apagar','familia.php?acao=del&fam_codigo='.$row[fam_codigo])."</td>
 	             </tr>";
           }
	       echo "
          </tr>
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
if(strlen($palavra_chave)<"2") {
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
	       <td width=95>".ChmodBtn($id_login,'adicionar','familia.php?acao=form_add')."</td>
           <td width=56 align=right>".ChmodBtn($id_login,'area','area.php?acao=')."</td>
           <td width=86>".ChmodBtn($id_login,'microarea','microarea.php?acao=')."</td>
           <td width=116>".ChmodBtn($id_login,'atendimento_psf','atendimento_psf.php')."</td>
   	        <form method=post action=$PHP_SELF>
		     <input type=hidden name=acao value=busca>
	<input type=hidden name=id_login value=$id_login>
	         <td width=30>Buscar:</td>
	         <td width=120><input type=text name=palavra_chave class=box onBlur=\"javascript:this.value=this.value.toUpperCase();\"></td>
	         <td>".ChmodBtn($id_login,'procurar','familia')."</td>
            </form>
	       <td width=107><a href='logoff.php?id_login=$id_login' target='_parent'><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/sair.gif border=0></a></td>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br>";
  $sql=pg_query("SELECT A.*, B.mic_descricao, C.cid_nome, fam_responsavel
                   FROM familia A, Microarea B, cidade C
                  WHERE A.mic_codigo = B.mic_codigo
                    and A.cid_codigo = C.cid_codigo 
                    and (fam_responsavel like '%$str%')");  
  $num=pg_num_rows($sql);
  if($num=="0") { $resp="Nenhum Registro encontrado com \"$palavra_chave\""; }
  if($num=="1") { $resp="Encontrado <b>$num</b> Registro com \"$palavra_chave\""; }
  if($num> "1") { $resp="Encontrados <b>$num</b> Registros com \"$palavra_chave\""; }

  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>$resp</legend>
	     <table width=100% align=center cellspacing=2 cellpadding=4 border=0>
	      <tr bgcolor=F9f9f9 align=center>
		   <td width=10  style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Nr.Ficha</td>
		   <td width=140 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Responsavel</td>
		   <td width=20  style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Nr.Pess</td>
		   <td width=5   style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>MicroReg</td>
		   <td width=20  style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Endereco</td>
		   <td width=20  style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Cidade</td>
		   <td colspan=2 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td>";

     while($row=pg_fetch_array($sql)) {
       echo "<tr>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[fam_nr_ficha]</td>
	       <td align=left   style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[fam_responsavel]</td>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[fam_nr_pessoas]</td>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[mic_descricao]</td>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[fam_endereco]</td>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[cid_nome]</td>
	       <td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'editar','familia.php?acao=form_edit&fam_codigo='.$row[fam_codigo])."</td>
	       <td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>".ChmodBtn($id_login,'apagar','familia.php?acao=del&fam_codigo='.$row[fam_codigo])."</td>
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
	
echo monta_janela('janela_paci','Busca C&oacute;digo IBGE');
//
//-> Abaixo sao os botoes de voltar / cadastro simples / cadastro completo

  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Opções de Cadastro</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
	       <td width=79><a href=familia.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
	       <td>&nbsp;</td>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br>";
//
//-> Este if esta apontando quando a acao for vazia ele vai mostrar o cadastro simples
//   no cadastro completo vc vai ter que passar a variavel acao para completo

 if(($type=="" OR $acao=="simples")) {
  echo "<form name=familia method=post action=$PHP_SELF>
	<input type=hidden name=acao value=add>
	<input type=hidden name=id_login value=$id_login>
	<input type=hidden name=type value=simples>
	<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Cadastro de Familia</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
  		   <td width=70>Data Cadastro:</td>
		   <td><input type=text name=fam_dt_cadastro class=box size=10></td>
	      </tr>
	      <tr>
  		   <td width=70>Data Inclus&atilde;o</td>
		   <td><input type=text name=fam_dt_inclusao value=".date('d/m/Y')." class=box size=10></td>
	      </tr>
	      <tr>
  		   <td width=70>Nr.Ficha:</td>
		   <td><input type=text name=fam_nr_ficha class=box size=5></td>
	      </tr>
	      <tr>
		   <td width=70>Seg_Codigo:</td>
		   <td><input type=text name=seg_codigo class=box size=5></td>
	      </tr>
	      <tr>
		   <td width=70>Micro Regiao:</td>
		   <td><input type=text name=mic_codigo class=box size=5></td>
	      </tr>
	      <tr>
		   <td width=70>Tel.:</td>
		   <td><input type=text name=fam_telefone class=box size=11></td>
	      </tr>
	      <tr>
		   <td width=70>Endereco: </td>
		   <td><input type=text name=fam_endereco class=box size=70></td>
	      </tr>
	      <tr>
		   <td width=70>No:</td>
		   <td><input type=text name=fam_numero_res class=box size=11></td>
	      </tr>
	      <tr>
		   <td width=70>Bairro:</td>
		   <td><input type=text name=fam_bairro class=box size=11></td>
	      </tr>
	      <tr>
		   <td width=70>Complemento:</td>
		   <td><input type=text name=fam_complemento class=box size=25></td>
	      </tr>
	      <tr>
		   <td width=70>CEP:</td>
		   <td><input type=text name=fam_cep class=box size=11></td>
	      </tr>
	      <tr>
		   <td width=70>Logradouro:</td>
		   <td>
			<select name=log_tipo>";
				$sql = pg_query("SELECT * FROM logradouro");
				while ($log = pg_fetch_array($sql));
				echo "<option value=".$log['logra_codigo'].">".$log['logra_logradouro']."</option>";
	echo "	</select>
		   </td>
	      </tr>
	      <tr>
		   <td width=90>Cidade / Cod IBGE:</td>
		   <td>
			<input type=text name=cid_nome class=box size=15> / 
			<input type=text name=cid_codigo_ibge class=box size=15>
			<input type=hidden name='cid_codigo' value=''>
			<a href='javascript:;' onclick=\"mostra_janela('janela_paci');init_paci('<?=$id_login;?>');\">
				<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/localizar.jpg' alt='Localizar' align='absmiddle' border='0' />	
			</a>
		   </td>
	      </tr>
	      <tr>
		   <td width=70>Cidade:</td>
		   <td><input type=text name=cid_codigo class=box size=8></td>
	      </tr>
	      <tr>
		   <td width=70>Nr.Pessoas:</td>
		   <td><input type=text name=fam_nr_pessoas class=box size=8></td>
	      </tr>
	      <tr>
		   <td width=70>Respons&aacute;vel:</td>
		   <td><input type=text name=fam_responsavel class=box size=60></td>
	      </tr>
	      <tr>
		   <td width=70>Comodos:</td>
		   <td><input type=text name=fam_comodos class=box size=60></td>
	      </tr>
	      <tr>
		   <td width=70>Energia:</td>
		   <td>
			<select name=fam_energia>
				<option value=1>Sim</option>
				<option value=2>N&atilde;o</option>
			</select>
		   </td>
	      </tr>
	      <tr>
		   <td width=70>Esgoto:</td>
		   <td>
			<select name=fam_esgoto>
				<option value=1>Rede P&uacute;blica</option>
				<option value=2>Fossa</option>
				<option value=3>A c&eacute;u aberto</option>
			</select>
		   </td>
	      </tr>
	      <tr>
		   <td width=70>Domicilio:</td>
		   <td>
			<select name=fam_tipo_domicilio>
				<option value=1>Tijolo</option>
				<option value=2>Taipa revestida</option>
				<option value=3>Taipa n&atilde;o revestida</option>
				<option value=4>Madeira</option>
				<option value=5>Material aproveitado</option>
				<option value=6>Adobe</option>
				<option value=7>Outro</option>
			</select>
		   </td>
	      </tr>
	      <tr>
		   <td width=70>&Aacute;gua:</td>
		   <td>
			<select name=fam_agua>
				<option value=1>Rede P&uacute;blica</option>
				<option value=2>Po&ccedil;o</option>
				<option value=3>Outro</option>
			</select>
		   </td>
	      </tr>
	      <tr>
		   <td width=70>Tratamento &aacute;gua:</td>
		   <td>
			<select name=fam_tratamento_agua>
				<option value=1>Filtrada</option>
				<option value=2>Fervida</option>
				<option value=3>Clorada</option>
				<option value=4>Sem tratamento</option>
			</select>
		   </td>
	      </tr>
	      <tr>
		   <td width=70>Destino Lixo:</td>
		   <td>
			<select name=fam_destino_lixo>
				<option value=1>Coletado</option>
				<option value=2>Queimado</option>
				<option value=3>C&eacute;u aberto</option>
			</select>
		   </td>
	      </tr>
	      <tr>
		   <td width=70>Cadastrador:</td>
		   <td><input type=text name=fam_cadastrador class=box size=60></td>
	      </tr>
	      <tr>
	       <td>&nbsp;</td>
	       <td><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg></td>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br></form>";
 }//fechamento do if
}
//}

//------------------------------------------------------------------>
//-> Formulario de Edicao de Conteudo
//------------------------------------------------------------------>

 if($acao=="form_edit") {

	echo monta_janela('janela_paci','Busca C&oacute;digo IBGE');
//
//-> Formulario de edicao do cadastro SIMPLES

//
//-> Pegando as informcoes do banco pra mostrar no formulario
 $sqlFamilia = "SELECT A.*,  to_char(A.fam_dt_cadastro,'dd/mm/yyyy') as fam_dt_cadastro,to_char(A.fam_dt_inclusao,'dd/mm/yyyy') as fam_dt_inclusao, B.mic_descricao, C.cid_nome, C.cid_codigo_ibge
                  FROM familia A, Microarea B, cidade C
                  WHERE A.mic_codigo = B.mic_codigo
                   and A.cid_codigo = C.cid_codigo 
                   and fam_codigo='$fam_codigo'";
 
$row=pg_fetch_array(pg_query($sqlFamilia));
?>
<script>
function seleciona(fam_energia,fam_esgoto,fam_tipo_domicilio,fam_agua,fam_tratamento_agua,log_tipo,fam_destino_lixo)
{
	dc = document.familia;
	dc.fam_energia.value = fam_energia;
	dc.fam_energia.value = fam_energia;
	dc.fam_esgoto.value = fam_esgoto
	dc.fam_tipo_domicilio.value = fam_tipo_domicilio
	dc.fam_agua.value = fam_agua
	dc.fam_tratamento_agua.value = fam_tratamento_agua
	dc.log_tipo.value = log_tipo
	dc.fam_destino_lixo.value = fam_destino_lixo;
}
</script>

<?

 echo "<br><br><form name='familia' method=post action=$PHP_SELF>
	<input type=hidden name=acao value=edit>
	<input type=hidden name=fam_codigo value=$fam_codigo>
	<input type=hidden name=id_login value=$id_login>
	<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Cadastro de Familia</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
  		   <td width=70>Data Cadastro:</td>
		   <td><input type=text name=fam_dt_cadastro value=$row[fam_dt_cadastro] class=box size=10></td>
	      </tr>
	      <tr>
  		   <td width=70>Data Inclus&atilde;o</td>
		   <td><input type=text name=fam_dt_inclusao value=$row[fam_dt_inclusao] class=box size=10></td>
	      </tr>
	      <tr>
  		   <td width=70>Nr.Ficha:</td>
		   <td><input type=text name=fam_nr_ficha value=$row[fam_nr_ficha] class=box size=5></td>
	      </tr>
	      <tr>
		   <td width=70>Seg_Codigo:</td>
		   <td><input type=text name=seg_codigo value=$row[seg_codigo] class=box size=5></td>
	      </tr>
	      <tr>
		   <td width=70>Micro Regiao:</td>
		   <td><input type=text name=mic_codigo value=$row[mic_codigo] class=box size=5></td>
	      </tr>
	      <tr>
		   <td width=70>Tel.:</td>
		   <td><input type=text name=fam_telefone value=$row[fam_telefone] class=box size=11></td>
	      </tr>
	      <tr>
		   <td width=70>Endereco: </td>
		   <td><input type=text name=fam_endereco value=$row[fam_endereco] class=box size=70></td>
	      </tr>
	      <tr>
		   <td width=70>No:</td>
		   <td><input type=text name=fam_numero_res value=$row[fam_numero_res] class=box size=11></td>
	      </tr>
	      <tr>
		   <td width=70>Bairro:</td>
		   <td><input type=text name=fam_bairro value=$row[fam_bairro] class=box size=11></td>
	      </tr>
	      <tr>
		   <td width=70>Complemento:</td>
		   <td><input type=text name=fam_complemento value=$row[fam_complemento] class=box size=25></td>
	      </tr>
	      <tr>
		   <td width=70>CEP:</td>
		   <td><input type=text name=fam_cep value=$row[fam_cep] class=box size=11></td>
	      </tr>
	      <tr>
		   <td width=70>Logradouro:</td>
		   <td>
			<select name=log_tipo>";
				$sql = pg_query("SELECT * FROM logradouro");
				while ($log = pg_fetch_array($sql));
				echo "<option value=".$log['logra_codigo'].">".$log['logra_logradouro']."</option>";
	echo "	</select>
		   </td>
	      </tr>
	      <tr>
		   <td width=90>Cidade / Cod IBGE:</td>
		   <td>
			<input type=text name=cid_nome value=$row[cid_nome] class=box size=15> / 
			<input type=text name=cid_codigo_ibge value=$row[cid_codigo_ibge] class=box size=15>
			<input type=hidden name='cid_codigo' value=''>
			<a href='javascript:;' onclick=\"mostra_janela('janela_paci');init_paci('<?=$id_login;?>');\">
				<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/localizar.jpg' alt='Localizar' align='absmiddle' border='0' />	
			</a>
		   </td>
	      </tr>
	      <tr>
		   <td width=70>Nr.Pessoas:</td>
		   <td><input type=text name=fam_nr_pessoas value=$row[fam_nr_pessoas] class=box size=8></td>
	      </tr>
	      <tr>
		   <td width=70>Respons&aacute;vel:</td>
		   <td><input type=text name=fam_responsavel value=$row[fam_responsavel] class=box size=60></td>
	      </tr>
	      <tr>
		   <td width=70>Comodos:</td>
		   <td><input type=text name=fam_comodos value=$row[fam_comodos] class=box size=60></td>
	      </tr>
	      <tr>
		   <td width=70>Energia:</td>
		   <td>
			<select name=fam_energia>
				<option value=1>Sim</option>
				<option value=2>N&atilde;o</option>
			</select>
		   </td>
	      </tr>
	      <tr>
		   <td width=70>Esgoto:</td>
		   <td>
			<select name=fam_esgoto>
				<option value=1>Rede P&uacute;blica</option>
				<option value=2>Fossa</option>
				<option value=3>A c&eacute;u aberto</option>
			</select>
		   </td>
	      </tr>
	      <tr>
		   <td width=70>Domicilio:</td>
		   <td>
			<select name=fam_tipo_domicilio>
				<option value=1>Tijolo</option>
				<option value=2>Taipa revestida</option>
				<option value=3>Taipa n&atilde;o revestida</option>
				<option value=4>Madeira</option>
				<option value=5>Material aproveitado</option>
				<option value=6>Adobe</option>
				<option value=7>Outro</option>
			</select>
		   </td>
	      </tr>
	      <tr>
		   <td width=70>&Aacute;gua:</td>
		   <td>
			<select name=fam_agua>
				<option value=1>Rede P&uacute;blica</option>
				<option value=2>Po&ccedil;o</option>
				<option value=3>Outro</option>
			</select>
		   </td>
	      </tr>
	      <tr>
		   <td width=70>Tratamento &aacute;gua:</td>
		   <td>
			<select name=fam_tratamento_agua>
				<option value=1>Filtrada</option>
				<option value=2>Fervida</option>
				<option value=3>Clorada</option>
				<option value=4>Sem tratamento</option>
			</select>
		   </td>
	      </tr>
	      <tr>
		   <td width=70>Destino Lixo:</td>
		   <td>
			<select name=fam_destino_lixo>
				<option value=1>Coletado</option>
				<option value=2>Queimado</option>
				<option value=3>C&eacute;u aberto</option>
			</select>
		   </td>
	      </tr>
	      <tr>
		   <td width=70>Cadastrador:</td>
		   <td><input type=text name=fam_cadastrador value=".$row['fam_cadastrador']." class=box size=60></td>
	      </tr>
	      <tr>
	       <td>&nbsp;</td>
	       <td><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg></td>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br></form>
<script>seleciona($row[fam_energia],$row[fam_esgoto],$row[fam_tipo_domicilio],$row[fam_agua],$row[fam_tratamento_agua],$row[log_tipo],$row[fam_destino_lixo])</script>
";
}

//------------------------------------------------------------------>
//-> SQL's
//------------------------------------------------------------------>
//
//-> ADD <---------------------------------------------------------->

 if($acao=="add") {
 $sql = pg_query("INSERT INTO familia ( 
	fam_nr_ficha, 
	seg_codigo, 
	mic_codigo, 
	fam_endereco, 
	fam_complemento, 
	fam_cep, 
	cid_codigo, 
	fam_nr_pessoas, 
	fam_responsavel, 
	fam_comodos, 
	fam_energia, 
	fam_esgoto, 
	fam_tipo_domicilio, 
	fam_agua, 
	fam_tratamento_agua, 
	fam_cadastrador, 
	fam_numero_res, 
	fam_bairro, 
	fam_telefone, 
	log_tipo,
	fam_dt_cadastro,
	fam_dt_inclusao,
	fam_destino_lixo,
	fam_dom_id
	 ) VALUES ( 
	".intval($fam_nr_ficha).", 
	".intval($seg_codigo).", 
	".intval($mic_codigo).", 
	'".trim(strtoupper(substr($fam_endereco,0,50)))."', 
	'".trim(strtoupper(substr($fam_complemento,0,25)))."', 
	'".trim(strtoupper(substr($fam_cep,0,9)))."', 
	".intval($cid_codigo).", 
	".intval($fam_nr_pessoas).", 
	'".trim(strtoupper(substr($fam_responsavel,0,60)))."', 
	".intval($fam_comodos).", 
	'".trim(strtoupper(substr($fam_energia,0,1)))."', 
	'".trim(strtoupper(substr($fam_esgoto,0,1)))."', 
	'".trim(strtoupper(substr($fam_tipo_domicilio,0,1)))."', 
	'".trim(strtoupper(substr($fam_agua,0,1)))."', 
	'".trim(strtoupper(substr($fam_tratamento_agua,0,1)))."', 
	".intval($fam_cadastrador).", 
	'".trim(strtoupper(substr($fam_numero_res,0,10)))."', 
	'".trim(strtoupper(substr($fam_bairro,0,50)))."', 
	'".trim(strtoupper(substr($fam_telefone,0,14)))."', 
	".intval($log_tipo).",
	'".trim(strtoupper($fam_dt_cadastro))."', 
	'".trim(strtoupper($fam_dt_inclusao))."', 
	'".trim(strtoupper(substr($fam_destino_lixo,0,1)))."',
	'".trim(strtoupper(substr($fam_dom_id,0,16)))."')");

msg($id_login,$acao,$sql);
}

//
//-> EDIT <--------------------------------------------------------->

 if($acao=="edit") {
 $sql = pg_query("UPDATE familia SET 
	fam_nr_ficha = ".intval($fam_nr_ficha).", 
	seg_codigo = ".intval($seg_codigo).", 
	mic_codigo = ".intval($mic_codigo).", 
	fam_endereco = '".trim(strtoupper(substr($fam_endereco,0,50)))."', 
	fam_complemento = '".trim(strtoupper(substr($fam_complemento,0,25)))."', 
	fam_cep = '".trim(strtoupper(substr($fam_cep,0,9)))."', 
	cid_codigo = ".intval($cid_codigo).", 
	fam_nr_pessoas = ".intval($fam_nr_pessoas).", 
	fam_responsavel = '".trim(strtoupper(substr($fam_responsavel,0,60)))."', 
	fam_comodos = ".intval($fam_comodos).", 
	fam_energia = '".trim(strtoupper(substr($fam_energia,0,1)))."', 
	fam_esgoto = '".trim(strtoupper(substr($fam_esgoto,0,1)))."', 
	fam_tipo_domicilio = '".trim(strtoupper(substr($fam_tipo_domicilio,0,1)))."', 
	fam_agua = '".trim(strtoupper(substr($fam_agua,0,1)))."', 
	fam_tratamento_agua = '".trim(strtoupper(substr($fam_tratamento_agua,0,1)))."', 
	fam_cadastrador = ".intval($fam_cadastrador).", 
	fam_numero_res = '".trim(strtoupper(substr($fam_numero_res,0,10)))."', 
	fam_bairro = '".trim(strtoupper(substr($fam_bairro,0,50)))."', 
	fam_telefone = '".trim(strtoupper(substr($fam_telefone,0,14)))."', 
	log_tipo = ".intval($log_tipo).",
	fam_dom_id = '".trim(strtoupper(substr($fam_dom_id,0,16)))."', 
	fam_dt_cadastro = '".trim(strtoupper($fam_dt_cadastro))."', 
	fam_dt_inclusao = '".trim(strtoupper($fam_dt_inclusao))."', 
	fam_destino_lixo = '".trim(strtoupper(substr($fam_destino_lixo,0,1)))."'
	WHERE fam_codigo = ".$fam_codigo);
msg($id_login,$acao,$sql);
}

//
//-> DEL <---------------------------------------------------------->

 if($acao=="del") {
  $sql = pg_query("DELETE FROM Familia WHERE fam_codigo='$fam_codigo'");
msg($id_login,$acao,$sql);
}

?>

