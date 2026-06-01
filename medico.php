<?php
session_start();
include_once $_SESSION[root].$_SESSION[modulo]."authlib.inc.php";
verauth($id_login);
require_once $_SESSION[root].$_SESSION[comum]."class/commonClass.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";

$common = new commonClass();
echo $common->incJquery();
Cabecario();
include_once $_SESSION[root].$_SESSION[modulo]."json.inc.php";

?>


<script language='JavaScript' type='text/javascript' src='ajax_motor.js'></script>
<script language='JavaScript' type='text/javascript' src='rua_jean.js'></script>
<script language="Javascript" type='text/javascript' src="g_ajax.js"></script>
<script language="Javascript" type='text/javascript' src="g_script.js"></script>

<script type='text/javascriptfile:///mnt/ssh/webmail/demonstrativo/gps/medico.php'>

function valida_form_medico_submit( med_codigo )
{
    if( ! valida('med_nome', 'Nome') ) return false;
//    if( ! valida('CPF', 'CPF') ) return false;
    
  //  var CPF = $('CPF');
    
    //CPF.value = CPF.value.replace(/[\.\- ]/gi,'');
        
   /* if( ! Verifica_CPF(CPF.value,CPF) )
    {
        CPF.focus();
        return false;
    }*/
    
    var endereco = 'medico_op.php?acao=verifica&cpf='+CPF.value;
    endereco += '&med_codigo='+ ( med_codigo ? med_codigo : 0 );
    
    ajax_tudo( endereco, valida_form_medico_submit_callback );
    //ajax_tudo( endereco, alert );
    return false;
}

function valida_form_medico_submit_callback( txt )
{
    var Resp = eval( txt );
    if( ! Resp.ok )
    {
        alert( Resp.msg );
        $('CPF').select();
        return false;
    }
    // else ...
    $('form_medico').submit();
}

</script>
<fieldset><legend>CADASTRO / MÉDICOS</legend>
<?php

//------------------------------------------------------------------>
echo monta_janela('localiza_rua','Busca Rua');

//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
//------------------------------------------------------------------>

if(empty($acao)) {

//
//-> Botoes
//<td width=156><a href=medico_especialidade.php?acao=><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/medico_especialidade_on.jpg border=0></a></td>
  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Opções</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
	       <td width=95><a href=$PHP_SELF?acao=form_add&id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg border=0></a></td>
           <td width=95>".ChmodBtn($id_login,'especialidade','especialidade.php?acao=form_espec')."</td>
           <td>&nbsp;</td>";
           
//     		<td width=156>".ChmodBtn($id_login,'recomendacao','recomendacao.php?acao=form_med_esp')."</td>
     		echo "
	       <form method=post action=$PHP_SELF?acao=busca&id_login=$id_login>
		<input type=hidden name=acao value=busca>
	       <td width=30>Buscar:</td>
	       <td width=120><input type=text name=palavra_chave class=box onBlur=\"javascript:this.value=this.value.toUpperCase();\"></td>
	       <td><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/procurar_on.jpg></td></form>	       
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
	    <legend>Listando &Uacute;ltimos Medicos Cadastrados</legend>
	     <table width=100% align=center cellspacing=2 cellpadding=4 border=0>
	      <tr bgcolor=FFfFfF>
	  	   <td width=40  style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Codigo</td>
		   <td width=270 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Nome  </td>
		   <td width=20  style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>CRM/CRF   </td>
		   <td width=10  style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td>
		   <td width=10  style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td>
		   <td width=10  style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td>";

   $sql=pg_query("select med_codigo, med_nome, med_crm from medico order by med_codigo desc limit 20");
   while($row=pg_fetch_array($sql)) {
       echo "<tr>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[med_codigo]</td>
	       <td width=210 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[med_nome]</td>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[med_crm]</td>
	       <td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>
            <a href=medico_especialidade.php?acao=form_med_esp&med_codigo=$row[med_codigo]&id_login=$id_login>
                <img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/medico_especialidade_on.jpg border=0></a></td>
	       <td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>
                <a href=$PHP_SELF?id_login=$id_login&acao=form_edit&med_codigo=$row[med_codigo]><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg border=0></a>
            </td>
	       <td width=66 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>
            <a href=$PHP_SELF?id_login=$id_login&acao=del&med_codigo=$row[med_codigo] onClick=\"if (!confirm('Realmente deletar esse Medico?')) return false\"><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.jpg border=0></a>
           </td>
	     </tr>";
     }
	echo "</tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table>";
}

$med_cod = $row[med_codigo];


//------------------------------------------------------------------>
//-> Mostrando o resultado da busca
//------------------------------------------------------------------>

if($acao=="busca")
{
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
	       <td width=95><a href=$PHP_SELF?acao=form_add&id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg border=0></a></td>
           <td width=95>".ChmodBtn($id_login,'especialidade','especialidade.php?acao=form_espec')."</td>
           <td>&nbsp;</td>
	       <form method=post action='$PHP_SELF?id_login=$id_login'>
		<input type=hidden name=acao value=busca>
	       <td width=30>Buscar:</td>
	       <td width=120><input type=text name=palavra_chave class=box onBlur=\"javascript:this.value=this.value.toUpperCase();\"></td>
	       <td><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/procurar_on.jpg></td></form>	       
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br>";
$key = trim(strtoupper($palavra_chave));
$sql=pg_query("select med_codigo, med_nome, med_crm from medico where (med_nome like '%$key%')");
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
	      <tr bgcolor=FFfFfF>
	  	   <td width=40  style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Codigo</td>
		   <td width=270 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Nome  </td>
		   <td width=20  style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>CRM/CRF   </td>
		   <td width=10  style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td>
		   <td width=10  style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td>
		   <td width=10  style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td>";

   while($row=pg_fetch_array($sql)) {
       echo "<tr>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[med_codigo]</td>
	       <td width=210 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[med_nome]</td>
	       <td align=center style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[med_crm]</td>
	       <td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>
            <a href=cadMedicoEspecialidade.php?med_codigo=$row[med_codigo]&id_login=$id_login>
                <img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/medico_especialidade_on.jpg border=0></a></td>
	       <td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>
                <a href=$PHP_SELF?id_login=$id_login&acao=form_edit&med_codigo=$row[med_codigo]><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg border=0></a>
            </td>
	       <td width=66 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>
            <a href=$PHP_SELF?id_login=$id_login&acao=del&med_codigo=$row[med_codigo]><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.jpg border=0></a>
           </td>
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

else if($acao=="form_add") {

//
//-> Abaixo sao os botoes de voltar / cadastro simples / cadastro completo

  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Opções de Cadastro</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
	       <td width=79><a href=medico.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
	       <td>&nbsp;</td>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
   </table>";

//
//-> Este if esta apontando quando a acao for vazia ele vai mostrar o cadastro simples
//   no cadastro completo vc vai ter que passar a variavel acao para completo

    //if(($type=="" OR $acao=="simples")) {

    echo "
    <fieldset>
    <legend>Cadastro de Medico</legend>
    
    <p>Campo(s) Obrigat&oacute;rio(s): <span class='destaque'>Nome, CPF,Num.Conselho</span></p>
    
    <form method='post' action='{$PHP_SELF}?id_login={$id_login}&acao=add' name='MedicoEspecialidade' id='form_medico'
        onsubmit='return valida_form_medico_submit()'>
    <table>
    <tr>
		<td width='120'>Num.Conselho:</td>
		<td><input type=text name=med_crm class=box size=10></td>
    </tr>
    <tr>
        <td>Estado Conselho:</td>
		<td>
        <select name=uf_codigo_crm class=box>";
	    //
	    //-> SQL do Estado
	    $query = pg_query("select * from estado order by uf_sigla");
	      while($uf=pg_fetch_array($query)) {
	       echo "<option value='$uf[uf_codigo]'>$uf[uf_sigla]</option>";
	      }
    echo "</select>
    </td>
    </tr>
    <tr>
        <td class='destaque'>Nome:</td>
        <td><input type=text name=med_nome id=med_nome class=box size=100></td>
    </tr>
    <tr>
		<td>E-mail: </td>
		<td><input type=text name=med_email class=box size=100></td>
    </tr>";
//atualizações 12/06/07
echo "<tr>
        <td>Logradouro Residencial:</td>
        <td>
        <select name='logra_codigo_res' class=box>";

	    $query = pg_query("SELECT logra_codigo, logra_logradouro FROM logradouro ORDER BY logra_logradouro");
	    while($logra_logradouro=pg_fetch_array($query)) {
	       echo "<option value='$logra_logradouro[logra_codigo]'>$logra_logradouro[logra_logradouro]</option>";
	      }
        echo "</select>
        </td>
    </tr>
    <tr>
        <td>Num:</td>
        <td><input type='text' name='med_end_numero_res' class='box' size='10'></td>
		</tr>
    <tr>
        <td>Complemento:</td>
        <td><input type='text' name='med_end_complemento_res' class='box' size='20'></td>
    </tr>
    <tr>
        <td>Bairro:</td>
        <td><input type='text' name='med_end_bairro_res' class='box' size='20'/></td>
    </tr>
    <tr>
        <td>CEP:</td>
        <td><input type='text' name='med_end_cep_res' id='med_end_cep_res' class='box' size='20'/></td>
    </tr>
    <tr>
        <td>Telefone Residencial:</td>
        <td><input type='text' name='med_end_telefone_res' id='med_end_telefone_res' class='box' size='20'/></td>
    </tr>";
//fim (12/06/07)
echo"
    <tr>
        <td>Endere&ccedil;o:</td>
		<td>
			<input type=text name=med_endereco id=med_endereco class=box size=100 value=''>
			<input type=hidden name=rua_codigo id=rua_codigo value=''>
			<img src=\"".$_SESSION[linkroot].$_SESSION[comum]."imgs/localizar.jpg\" onclick=\"mostra_janela('localiza_rua');init_med('<? echo $id_login;?>','');\" />
		</td>
    </tr>
    <tr>
        <td>Num:</td>
        <td><input type=text name=med_end_numero class=box size=10></td>
		</tr>
    <tr>
        <td>Complemento:</td>
        <td><input type=text name=med_end_complemento class=box size=20></td>
    </tr>
    <tr>
        <td>Bairro:</td>
        <td><input type=text name=med_end_bairro class=box size=20/></td>
    </tr>
    <tr>
        <td>CEP:</td>
        <td><input type=text name=med_end_cep id=med_end_cep class=box size=20/></td>
    </tr>
    <tr>
        <td>Celular:</td>
        <td><input type=text name=med_end_celular id=med_end_celular class=box size=20/></td>
    </tr>
    <tr>
        <td>Tipo de Vinculo:</td>
        <td>
            <select name=med_vinculo class=box>
                <option value='CONCURSO'>Concurso</option>
                <option value='CLT'>CLT</option>
                <option value='TEMPORARIO'>Temporario</option>
                <option value='CONTRATO'>Contrato</option>
            </select>
        </td>
    </tr>
    <tr>
        <td>Carga Horaria:</td>
        <td><input type=text name=med_carga_horaria class=box size=20/></td>
    </tr>		
    <tr>
        <td>UF</td>
        <td>
            <select id=\"uf\" name=\"uf\" class=\"box\">
                <option value=\"0\">---</option>";
    
                $stmt = "SELECT DISTINCT uf_sigla FROM cidade ORDER BY 1";
                $qry = pg_query($stmt);
                while( $row = pg_fetch_row($qry) )
                    echo "\n\t<option>{$row[0]}</option>";
            echo"</select>
        </td>
    </tr>
    <tr>			
        <td>Cidade</td>
        <td>
            <select id=\"cid_codigo\" name=\"cid_codigo\" class=\"box\">
                <option value=\"0\">---</option>
            </select>

				<script type=\"text/javascript\">
				document.getElementById(\"uf\").onchange = function()
				{
					var Cid = document.getElementById(\"cid_codigo\");
					Cid.length = 1;
					Cid.options[0].value = 0;
					Cid.options[0].text = \"...carregando...\" ;
					ajax_tudo( 'uf_cidade_op_jean.php?uf='+this.value, callback_uf );
				}
				function callback_uf( text )
				{
					var CidArr = ( eval(text) );
					var CidSel = document.getElementById(\"cid_codigo\");
	
					CidSel.length = 0;
					for( var i=0; i < CidArr.length; i++ )
					{
						CidArr[ i ].cid_nome = unescape( CidArr[ i ].cid_nome );
						CidSel.options[ CidSel.options.length ]=new Option( CidArr[ i ].cid_nome,  CidArr[ i ].cid_codigo );
					}
				}
			</script>
			</td>
    </tr>
    <tr>
         <td class='destaque'>CPF:</td>
         <td><input type=text name=med_cpf id=CPF class=box size=20 value=\"\" /></td>
     </tr>
     <tr>
         <td>RG:</td>
         <td><input type=text name=med_rg class=box size=20></td>
     </tr>
     <tr>
         <td>CNES:</td>
         <td><input type=text name=med_cnes class=box size=20></td>
     </tr>";
//atualização 13/06/07
echo "<tr>
         <td>Banco:</td>
         <td><input type=text name=med_banco class=box size=20></td>
     </tr>
     <tr>
         <td>Agencia:</td>
         <td><input type=text name=med_agencia class=box size=20></td>
     </tr>
     <tr>
         <td>Tipo de conta:</td>
         <td><input type=text name=med_tp_conta class=box size=20></td>
     </tr>
     <tr>
         <td>Nro. da Conta:</td>
         <td><input type=text name=med_conta_nro class=box size=20></td>
     </tr>";
//fim(13/06/07)
echo "<tr>
         <td>Tipo Prestador:</td>
         <td>
             <select class='box' name='prestador_servico'>
                 <option value='M'>Medico(a)</option>
                 <option value='E'>Enfermeiro(a)</option>
                 <option value='X'>Aux. Enfermagem</option>
                 <option value='B'>Farm. Bioquimico(a)</option> 
             </select>							
         </td>		
      </tr>	    
    <tr>
        <td>&nbsp;</td>
        <td><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg></td>
    </tr>
    </table>
    </fieldset>
    </form>";

    //}//fechamento do if

    echo "<div id='grid'></div> ";


}

//------------------------------------------------------------------>
//-> Formulario de Edicao de Conteudo
//------------------------------------------------------------------>

if($acao=="form_edit") {
    //
    //-> Formulario de edicao do cadastro SIMPLES
    
    //
    //-> Pegando as informcoes do banco pra mostrar no formulario
    $sqlmedico = "SELECT * FROM medico WHERE med_codigo='$med_codigo'";
    //echo $sqlmedico;
    $exec_sqlmedico = pg_query($sqlmedico);                                      
       
    $row = pg_fetch_array($exec_sqlmedico);                                      
    //echo $row[med_cpf]."<br>";
    //echo $row[med_rg];
     //$row=pg_fetch_array(pg_query($sqlmedico));
?>
<script>

function seleciona(med_vinculo){
	document.bluft.med_vinculo.value = med_vinculo;
}
</script>
<?php

  echo "
  <p>Campo(s) Obrigat&oacute;rio(s): <span class='destaque'>Nome, CPF</span></p>
  
  <form name=\"bluft\" method=post action='$PHP_SELF?id_login=$id_login&med_codigo=$med_codigo&acao=edit'
            id='form_medico' onsubmit='return valida_form_medico_submit($med_codigo)'>
	<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Cadastro de Medico</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
		<td width='120'>Num Conselho:</td>
		<td><input type=text name=med_crm class=box size=10 value='$row[med_crm]'></td>
	      </tr>
	      <tr>
		<td>Estado Conselho:</td>
		<td>
		 <select name=uf_codigo_crm class=box>";
	    //
	    //-> SQL do Estado
	    $query = pg_query("select * from estado order by uf_sigla");
	      while($uf=pg_fetch_array($query)) {
	       echo ($uf[uf_codigo]==$row[uf_codigo_crm])?"<option value='$uf[uf_codigo]' selected>$uf[uf_sigla]</option>":"<option value='$uf[uf_codigo]'>$uf[uf_sigla]</option>";
	      }
	   echo "</select>
	        </td>
	      </tr>
	      <tr>
		<td >Nome:</td>
		<td><input type=text name=med_nome id=med_nome class=box size=100 value='$row[med_nome]'></td>
	      </tr>
	      <tr>
		<td >E-mail: </td>
		<td><input type=text name=med_email class=box size=100 value='$row[med_email]'></td>
	      </tr>";
//atualização 12/06/07
echo "		<tr>
			<td>Logradouro Residencial:</td>
			<td>";
			
			$sql = "select logra_codigo_res from medico where med_codigo = $med_codigo";
			$exec_sql = pg_query($sql);
			$logra_codigo = pg_fetch_array($exec_sql);
			$query = pg_query("SELECT logra_codigo, logra_logradouro FROM logradouro ORDER BY logra_logradouro");
			$t = pg_fetch_array($query);
			echo "<select name=logra_codigo_res class=box>";
			  while($logra_logradouro = pg_fetch_array($query)) {
			    
			    	if($logra_codigo[logra_codigo_res] == $logra_logradouro[logra_codigo])
			    	{
			    		echo "<option value=\"$logra_logradouro[logra_codigo]\" selected>$logra_logradouro[logra_logradouro]</option>";
			    	} else {
			    		echo "<option value=\"$logra_logradouro[logra_codigo]\">$logra_logradouro[logra_logradouro]</option>";
			    	}
			       
			   }			
			echo "</select>			
			</td>	    
	    </tr>

		<tr>
			<td >Num:</td>
			<td><input type=text name=med_end_numero_res class=box size=10 value='$row[med_end_numero_res]'></td>
		</tr>
		<tr>
			<td>Complemento:</td>
			<td><input type=text name=med_end_complemento_res class=box size=20 value='$row[med_end_complemento_res]'></td>
		</tr>
		<tr>
			<td>Bairro:</td>
			<td><input type=text name=med_end_bairro_res class=box size=20  value='$row[med_end_bairro_res]'/></td>
		</tr>
		<tr>
			<td>CEP:</td>
			<td><input type=text name=med_end_cep_res id=med_end_cep_res class=box size=20 value='$row[med_end_cep_res]'/></td>
		</tr>
		<tr>
			<td>Telefone Residencial:</td>
			<td><input type=text name=med_end_telefone_res id=med_end_telefone_res class=box size=20 value='$row[med_end_telefone_res]'/></td>
		</tr>";
echo"
	    <tr>
		<td >Endere&ccedil;o:</td>
		<td>
			<input type=text name=med_endereco id=med_endereco class=box size=100 value='$row[med_endereco]'>
			<input type=hidden name=rua_codigo id=rua_codigo value=''>
			<img src=\"".$_SESSION[linkroot].$_SESSION[comum]."imgs/localizar.jpg\" onclick=\"mostra_janela('localiza_rua');init_med('<? echo $id_login;?>','');\" />
		</td>
	      </tr>
		<tr>
			<td >Num:</td>
			<td><input type=text name=med_end_numero class=box size=10 value='$row[med_end_numero]'></td>
		</tr>
		<tr>
			<td>Complemento:</td>
			<td><input type=text name=med_end_complemento class=box size=20 value='$row[med_end_complemento]'></td>
		</tr>
		<tr>
			<td>Bairro:</td>
			<td><input type=text name=med_end_bairro class=box size=20  value='$row[med_end_bairro]'/></td>
		</tr>
		<tr>
			<td>CEP:</td>
			<td><input type=text name=med_end_cep id=med_end_cep class=box size=20 value='$row[med_end_cep]'/></td>
		</tr>
		<tr>
			<td>Celular:</td>
			<td><input type=text name=med_end_celular id=med_end_celular class=box size=20 value='$row[med_end_celular]'/></td>
		</tr>
		<tr>
			<td>Tipo de Vinculo:</td>
			<td>
                <select name=med_vinculo class=box>
                    <option value='CONCURSO'>Concurso</option>
                    <option value='CLT'>CLT</option>
                    <option value='TEMPORARIO'>Temporario</option>
                    <option value='CONTRATO'>Contrato</option>
                </select>
		</tr>
		<tr>
			<td>Carga Horaria:</td>
			<td><input type=text name=med_carga_horaria class=box size=20 value='$row[med_carga_horaria]'/></td>
		</tr>	      
		<tr>
			<td>UF</td>
			<td> ";
					$stmt =" select b.uf_sigla, a.cid_codigo FROM medico a, cidade b where 
							 a.med_codigo = $med_codigo and
							 b.cid_codigo = a.cid_codigo  ";
							 //echo $stmt;
							
					$qry = pg_query($stmt);
					$cid = pg_fetch_array($qry);
					$uf = $cid[1];
					
				   
				    $stmt = "SELECT DISTINCT uf_sigla FROM cidade ORDER BY 1";
					$qry = pg_query($stmt); 
				  
			echo "	<select id=\"uf\" name=\"uf\" class=\"box\">
					<option value=\"uf_codigo\">---</option>";
		
		     		while($row1 = pg_fetch_row($qry) ){
					$a = $cid[0];
					$b = $row1[0];
					
					($a ==  $b ? $selected="selected" : $selected="");
					   echo "\n\t<option value=\"$row1[0]\" $selected >{$row1[0]}</option>";
					}	
				echo"</select>
					
			</td>
		</tr>
		<tr>			
			<td>Cidade</td>
			<td>
				<select id=\"cid_codigo\" name=\"cid_codigo\" class=\"box\">
					<option value=\"cid_codigo\">---</option>
				</select>


				<script type=\"text/javascript\" src=\"ajax_motor.js\"></script>
				<script type=\"text/javascript\">
				if(document.bluft.uf.selected=true){
				    var meleca =document.bluft.uf.value; 
     				ajax_tudo( 'uf_cidade_op_jean.php?uf='+meleca, callback_uf );
				}
				
				document.getElementById(\"uf\").onchange = function()
				{
					var Cid = document.getElementById(\"cid_codigo\");
					Cid.length = 1;
					Cid.options[0].value = '0';
					Cid.options[0].text = \"...carregando...\" ;
					ajax_tudo( 'uf_cidade_op_jean.php?uf='+this.value, callback_uf );
				}
				
				function callback_uf( text )
				{ 
					
					var CidArr = ( eval(text) );

					var CidSel = document.getElementById(\"cid_codigo\");
					var cidade = '$uf';
					var selecionar;
	
					CidSel.length = 0;
					
					for(var i=0; i < CidArr.length; i++)
					{
					   if(CidArr[ i ].cid_codigo==cidade) {selecionar=true}else{selecionar=false}
	
			    		CidArr[ i ].cid_nome = unescape( CidArr[ i ].cid_nome);
     					CidSel.options[ CidSel.options.length ]=new Option( CidArr[ i ].cid_nome,  CidArr[ i ].cid_codigo,false,selecionar);
   					
					}
					
				}
			</script>
			</td>
		</tr>
	      <tr>
			<td >CPF:</td>
			<td><input type=text name=med_cpf class=box size=20 value='$row[med_cpf]' id=CPF onChange=Verifica_CPF(this.value)</td>
	      </tr>
	      <tr>
			<td >RG:</td>
			<td><input type=text name=med_rg class=box size=20 value='$row[med_rg]' id=med_rg></td>
	      </tr>
		  <tr>
	         <td>CNES:</td>
	         <td><input type=text name=med_cnes class=box size=20></td>
	      </tr>";
//atualização 13/06/07
	echo "<tr>
			<td>Banco:</td>
			<td><input type=text name=med_banco class=box size=20 value='$row[med_banco]'></td>
		</tr>
		<tr>
			<td>Agencia:</td>
			<td><input type=text name=med_agencia class=box size=20 value='$row[med_agencia]'></td>
		</tr>
		<tr>
			<td>Tipo de conta:</td>
			<td><input type=text name=med_tp_conta class=box size=30 value='$row[med_tp_conta]'></td>
		</tr>
		<tr>
			<td>Nro. da Conta:</td>
			<td><input type=text name=med_conta_nro class=box size=20 value='$row[med_conta_nro]'></td>
		</tr>";
//fim(13/06/07)
echo "<tr>
         <td>Tipo Prestador:</td>
         <td>
             <select class='box' name='prestador_servico'>
                 <option value='M'".($row['prestador_servico']=='M'?" selected":"").">Medico(a)</option>
                 <option value='E'".($row['prestador_servico']=='E'?" selected":"").">Enfermeiro(a)</option>
                 <option value='X'".($row['prestador_servico']=='X'?" selected":"").">Aux. Enfermagem</option>
                 <option value='B'".($row['prestador_servico']=='B'?" selected":"").">Farm. Bioquimico(a)</option> 
             </select>							
         </td>		
      </tr>	
      <tr>
	       <td>&nbsp;</td>
	       <td><a href=medico.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a>&nbsp;&nbsp;<input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg></td>
	      </tr>
	     </table>
	   </fieldset>
	  </td>
	 </tr>
        </table><br></form>";
?>     
<script>

	seleciona("<?php  echo $row['med_vinculo']; ?>");
	
</script>   
<?php
        
}

//------------------------------------------------------------------>
//-> SQL's
//------------------------------------------------------------------>
//
//-> ADD <---------------------------------------------------------->

if($acao=="add")
{
    $sql = "insert into medico ( " .
            "med_nome, " .
            "med_crm, " .
            "uf_codigo_crm, " .
            "med_email, " .
            "logra_codigo," .
            "med_endereco, " .
            "med_end_numero, " .
            "med_end_complemento, " .
            "med_end_bairro, " .
            "med_end_cep, " .
            "med_end_telefone, " .
            "med_end_celular, " .
            "med_vinculo, " .
            "med_carga_horaria, " .
            "cid_codigo, " .
            "med_cpf, " .
            "med_rg, " .
            "prestador_servico, " .
            "logra_codigo_res," .
            "med_endereco_res, " .
            "med_end_numero_res, " .
            "med_end_complemento_res, " .
            "med_end_bairro_res, " .
            "med_end_cep_res, " .
            "med_end_telefone_res, " .
            "med_banco, " .
            "med_agencia, " .
            "med_tp_conta, " .
            "med_conta_nro, " .
    		"cnes".
            ") values ( " .
            "upper('$med_nome'), " .
            ($med_crm ? "'$med_crm'" : "'NAOTEM'") . ", " .
            ($uf_codigo_crm ? "'$uf_codigo_crm'" : "18") . ", " . //se nao for digitado grava o parana (18)
            ($med_email ? "'$med_email'" : "null") . ", " .
            ($logra_codigo ? "'$logra_codigo'" : "null") . ", " .
            ($med_endereco ? "'$med_endereco'" : "null") . ", " .
            ($med_end_numero ? "'$med_end_numero'" : "null") . ", " .
            ($med_end_complemento ? "'$med_end_complemento'" : "null") . ", " .
            ($med_end_bairro ? "'$med_end_bairro'" : "null") . ", " .
            ($med_end_cep ? "'$med_end_cep'" : "null") . ", " .
            ($med_end_telefone ? "'$med_end_telefone'" : "null") . ", " .
            ($med_end_celular ? "'$med_end_celular'" : "null") . ", " .
            ($med_vinculo ? "'$med_vinculo'" : "null") . ", " .
            ($med_carga_horaria ? "'$med_carga_horaria'" : "null") . ", " .
            ($cid_codigo ? "'$cid_codigo'" : "null") . ", " .
            ($med_cpf ? "'$med_cpf'" : "null") . ", " .
            ($med_rg ? "'$med_rg'" : "null") . " , " .
            ($prestador_servico ? "'$prestador_servico'" : "null") . ", " .
            ($logra_codigo_res ? "'$logra_codigo_res'" : "null") . ", " .
            ($med_endereco_res ? "'$med_endereco_res'" : "null") . ", " .
            ($med_end_numero_res ? "'$med_end_numero_res'" : "null") . ", " .
            ($med_end_complemento_res ? "'$med_end_complemento_res'" : "null") . ", " .
            ($med_end_bairro_res ? "'$med_end_bairro_res'" : "null") . ", " .
            ($med_end_cep_res ? "'$med_end_cep_res'" : "null") . ", " .
            ($med_end_telefone_res ? "'$med_end_telefone_res'" : "null") . ", " .
            ($med_banco ? "'$med_banco'" : "null") . ", " .
            ($med_agencia ? "'$med_agencia'" : "null") . ", " .
            ($med_tp_conta ? "'$med_tp_conta'" : "null") . ", " .
            ($med_conta_nro ? "'$med_conta_nro'" : "null") . ",  " .
            ($med_cnes ? "'$med_cnes'" : "null") . "  " .
            ")";



 	$qyr = db_query($sql);

	msg($id_login,$acao,$sql);

}

//
//-> EDIT <--------------------------------------------------------->

if($acao=="edit") {
    $sql = "update medico set " .
            "med_nome=upper('$med_nome'), " .
            ($med_crm ? "med_crm='$med_crm'" : "med_crm='NAOTEM'") . ", " .
            "uf_codigo_crm='$uf_codigo_crm', " .
            ($med_email ? "med_email='$med_email'" : "med_email=null") . ", " .
            ($logra_codigo ? "logra_codigo='$logra_codigo'" : "logra_codigo=null") . ", " .
            ($med_endereco ? "med_endereco='$med_endereco'" : "med_endereco=null") . ", " .
            ($med_end_numero ? "med_end_numero='$med_end_numero'" : "med_end_numero=null") . ", " .
            ($med_end_complemento ? "med_end_complemento='$med_end_complemento'" : "med_end_complemento=null") . ", " .
            ($med_end_bairro ? "med_end_bairro='$med_end_bairro'" : "med_end_bairro=null") . ", " .
            ($med_end_cep ? "med_end_cep='$med_end_cep'" : "med_end_cep=null") . ", " .
            ($med_end_telefone ? "med_end_telefone='$med_end_telefone'" : "med_end_telefone=null") . ", " .
            ($med_end_celular ? "med_end_celular='$med_end_celular'" : "med_end_celular=null") . ", " .
            ($med_vinculo ? "med_vinculo='$med_vinculo'" : "med_vinculo=null") . ", " .
            ($med_carga_horaria ? "med_carga_horaria='$med_carga_horaria'" : "med_carga_horaria=null") . ", " .   
            ($cid_codigo ? "cid_codigo='$cid_codigo'" : "cid_codigo=null") . ", " .
            ($med_cpf ? "med_cpf='$med_cpf'" : "med_cpf=null") . ", " .
            ($med_rg ? "med_rg='$med_rg'" : "med_rg=null") . ", " .
            ($prestador_servico ? "prestador_servico='$prestador_servico'" : "prestador_servico=null") . ", " .
            ($logra_codigo_res ? "logra_codigo_res='$logra_codigo_res'" : "logra_codigo_res=null") . ", " .
            ($med_endereco_res ? "med_endereco_res='$med_endereco_res'" : "med_endereco_res=null") . ", " .
            ($med_end_numero_res ? "med_end_numero_res='$med_end_numero_res'" : "med_end_numero_res=null") . ", " .
            ($med_end_complemento_res ? "med_end_complemento_res='$med_end_complemento_res'" : "med_end_complemento_res=null") . ", " .
            ($med_end_bairro_res ? "med_end_bairro_res='$med_end_bairro_res'" : "med_end_bairro_res=null") . ", " .
            ($med_end_cep_res ? "med_end_cep_res='$med_end_cep_res'" : "med_end_cep_res=null") . ", " .
            ($med_end_telefone_res ? "med_end_telefone_res='$med_end_telefone_res'" : "med_end_telefone_res=null") . ", " .
            ($med_banco ? "med_banco='$med_banco'" : "med_banco=null") . ", " .
            ($med_agencia ? "med_agencia='$med_agencia'" : "med_agencia=null") . ", " .
            ($med_tp_conta ? "med_tp_conta='$med_tp_conta'" : "med_tp_conta=null") . ", " .
            ($med_conta_nro ? "med_conta_nro='$med_conta_nro'" : "med_conta_nro=null") . ",  " .
            ($med_cnes ? "cnes='$med_cnes'" : "cnes=null") . "  " .
            "where med_codigo='$med_codigo'";

#            exit(0);
	
	$qry = db_query($sql);
	msg($id_login,$acao,$sql);
}

//
//-> DEL <---------------------------------------------------------->

if($acao=="del")
{ 
    $sql = pg_query("delete from medico where med_codigo='$med_codigo'");
    msg($id_login,$acao,$sql);
}
?>

</fieldset>
</body>
</html>
