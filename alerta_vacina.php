<link rel="stylesheet" href="/WebSocialSaude/lib/themes/base/jquery.ui.all.css">
<link rel="stylesheet" href="/WebSocialSaude/lib/themes/ui-lightness/jquery-ui-1.8.10.custom.css">
<script type="text/javascript" src="../WebSocialComum/library/js/jquery-1.6.2.min.js"></script>
<script type="text/javascript" src="/WebSocialSaude/lib/ui/jquery-ui-1.8.16.custom.min.js"></script>
<script type="text/javascript" src="../WebSocialComum/library/js/jquery.buscar.js"></script>
<script type="text/javascript">
  function somenteNumeros(num) {
        var er = /[^0-9.]/;
        er.lastIndex = 0;
        var campo = num;
        if (er.test(campo.value)) {
          campo.value = "";
        }
    }
	
$(function(){



	$("#buscar").buscar({
		callback: function(event, ui){
			var usu_codigo = $("#usu_codigo").val();

			if(ui.item){
				$("#final a").focus();
			}
		}
	});



});

function buscarPorUsuCodigo(usu_codigo){
	window.console && console.log("recebido: "+usu_codigo);
	$.ajax({
		url: '/WebSocialSaude/buscaGenerica.php?tipo=usu_cod_bio',
		datatype: 'JSON',
		type: 'GET',
		data:{
			term: usu_codigo
		},
		success: function(json){
			if (json && json[0].id) {
				for ( var i in json[0].data) {
					$("#" + i).val(json[0].data[i]);
				}
				window.console && console.log("achou: "+usu_codigo);
				var usu_codigo = $("#usu_codigo").val();
				
				$("#iframe").show("normal").find("iframe").attr("src","historico.php?usu_codigo="+usu_codigo);
				$("#final a").focus();
				
			} else {

			}
		}
	});
}


</script>
<?php
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>
	session_start();
	include_once $_SESSION[root].$_SESSION[comum]."library/php/authlib.inc.php";
	//verauth($id_login);
	include_once "global.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();
	if($id_login == ""){
		$id_login = $_SESSION['usr_codigo'];
	}
//------------------------------------------------------------------>

reglog($id_login,"Alarme Vacina");
//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
//------------------------------------------------------------------>

 if(empty($_REQUEST['acao'])) {

//
//-> Botoes
  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Opções</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
	       <td width=85><a href='alerta_vacina.php?acao=form_add'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg' /></a></td>";
           
  			
			  echo "<form method=post action=alerta_vacina.php>";
			  
			echo "
		     <input type=hidden name=id_login value=$id_login>
	         <td width=130 align=right>Buscar:</td>
	         <td width=120><input type=text name=palavra_chave class=box onBlur=\"javascript:this.value=this.value.toUpperCase();\"></td>
	         <td><input type=image src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/procurar_on.jpg' /></td>
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
	    <legend>Listando Últimas 15 <b>alerta_vacinas</b> Cadastradas</legend>
	     <table width=100% align=center cellspacing=2 cellpadding=4 border=0>
	      <tr bgcolor=F9f9f9>
		   <td width=140  style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Paciente</td>
		   <td width=60  style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Descricao</td>
		   <td width=160 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>Tempo</td>
		   <td colspan=2 style='border-bottom:1px solid;border-right:1px solid;border-color:c9c9c9;'>&nbsp;</td>";

if(empty($_REQUEST['palavra_chave'])) {		   
                $sql=pg_query("SELECT *FROM alerta_vacina as alv join usuario as u on u.usu_codigo = alv.usu_codigo ORDER BY alv_descricao asc limit 15");
} else {
                $sql=pg_query("SELECT *FROM alerta_vacina as alv join usuario as u on u.usu_codigo = alv.usu_codigo where usu_nome ilike '%".$_REQUEST['palavra_chave']."%' ORDER BY usu_nome") or die(pg_last_error());
}				
           while($row=pg_fetch_array($sql)) {
           echo "<tr>
	       <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[usu_nome]</td>
	       <td align=left   style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[alv_descricao]</td>
	       <td style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'>$row[alv_tempo]</td>
	       <td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'><a href='alerta_vacina.php?acao=form_edit&alv_codigo=$row[alv_codigo]'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg' /></td>
	       <td width=60 style='border-bottom:1px dotted;border-right:1px dotted;border-color:c9c9c9;'><a href='alerta_vacina.php?acao=del&alv_codigo=$row[alv_codigo]'><img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/apagar_on.jpg' /></td>
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
//-> Formulario de Adicao de Conteudo
//------------------------------------------------------------------>

 if(($_REQUEST['acao']=="form_add" OR $_REQUEST['acao']=="form_edit")) {
reglog($id_login,"Formulario de Adicao de alerta_vacina");
//
//-> Abaixo sao os botoes de voltar / cadastro simples / cadastro completo

  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Opções de Cadastro</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
	      <tr>
	       <td width=79><a href=alerta_vacina.php?id_login=$id_login><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/voltar_on.gif border=0></a></td>
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
$rr = pg_fetch_array(pg_query("SELECT *FROM alerta_vacina as alv join usuario as u on u.usu_codigo = alv.usu_codigo where alv_codigo = '".$_REQUEST['alv_codigo']."'"));
  echo "<form method=post action=$PHP_SELF>
	
	<input type=hidden name=id_login value=$id_login>
	<input type=hidden name=usu_codigo id=usu_codigo value=''/>
	<input type=hidden name=alv_codigo value='".$_REQUEST['alv_codigo']."' />

	<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
	 <tr>
	  <td>
	   <fieldset>
	    <legend>Cadastro de alerta_vacina</legend>
	     <table width=100% align=center cellspacing=3 cellpadding=0 border=0>";

 if(empty($_REQUEST['alv_codigo'])) {		 
	 echo "<input type=hidden name=acao value=add>
			<tr>
  		   <td width=170 align=right>Paciente:</td>
		   <td><input type=text name='buscar' class=box size=60 id='buscar'></td>
	      </tr>";
 } else {		  
	 echo "<input type=hidden name=acao value=edit>
		<tr>
  		   <td width=170 align=right>Paciente:</td>
		   <td>$rr[usu_nome]</td>
	      </tr>";
 }
	echo "<tr>
		   <td width=70  align=right>Descricao:</td>
		   <td><textarea name=alv_descricao cols=55 rows=10 class=boxt>".$rr['alv_descricao']."</textarea></td>
	      </tr>
	      <tr>
  		   <td width=70  align=right>Tempo (em dias):</td>
		   <td><input type=text name=alv_tempo class=box size=10 onkeyup='somenteNumeros(this);' maxlength='2'  value='".$rr['alv_tempo']."'></td>
	      </tr>
	      <tr>
  		   <td width=70  align=right>Desfecho:</td>
		   <td><input type=text name=alv_desfecho class=box size=60 value='".$rr['alv_desfecho']."'></td>
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
}

//
//-> ADD <---------------------------------------------------------->

 if($_REQUEST['acao']=="add") {
    									
									
 $sql = pg_query("INSERT INTO alerta_vacina (usu_codigo, alv_descricao, alv_tempo, alv_desfecho) VALUES ('".$_REQUEST['usu_codigo']."','".strtoupper($_REQUEST['alv_descricao'])."', '".$_REQUEST['alv_tempo']."', '".strtoupper($_REQUEST['alv_desfecho'])."')") or die(pg_last_error());

reglog($id_login,"Adicionando alerta_vacina: '".$_REQUEST['alv_codigo']."'");
msg($id_login,$acao,$sql);
}

//
//-> EDIT <--------------------------------------------------------->

 if($_REQUEST['acao']=="edit") {
  $sql = pg_query("UPDATE alerta_vacina SET alv_descricao='".$_REQUEST['alv_descricao']."', alv_tempo ='".$_REQUEST['alv_tempo']."', alv_desfecho = '".$_REQUEST['alv_desfecho']."'
                   WHERE alv_codigo = '".$_REQUEST['alv_codigo']."'") or die(pg_last_error());

reglog($id_login,"Editando alerta_vacina: $alerta_vacina_desc");
msg($id_login,$acao,$sql);
}

//
//-> DEL <---------------------------------------------------------->

 if($_REQUEST['acao']=="del") {
reglog($id_login,"Excluindo alerta_vacina Cod.: '".$_REQUEST['alv_codigo']."'");
  $sql = pg_query("DELETE FROM alerta_vacina WHERE alv_codigo='".$_REQUEST['alv_codigo']."'");
msg($id_login,$acao,$sql);
}

?>

