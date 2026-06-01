<?php
require_once "global.php";
include_once COMUM."/library/php/funcoes.inc.php";
include_once SAUDE . '/__array.php';
$usr = pg_fetch_array(pg_query("select *from usuarios where usr_codigo = ".$_SESSION['id_login'].""));
?>
<script language="JavaScript" type="text/javascript" src="funcoes.js"></script>
<script language="JavaScript" type="text/javascript" src="ajax_motor.js"></script>
<?php
$form = new classForm();
$common = new commonClass();
$table = new tableClass();
echo $common->incJquery();
?>
<script language="JavaScript" type="text/javascript" src="/WebSocialComum/library/js/jquery-1.6.2.min.js"></script>
<script type="text/javascript" src="/WebSocialSaude/lib/ui/jquery-ui-1.8.10.custom.js"></script>
<script type="text/javascript" src="/WebSocialComum/library/js/jquery.buscar.js"></script>

<script language="javascript">
 $(function(){
//    $(".style1:contains('SALVAR')").parents("tr:eq(1)").click(function(){
//
//        if(typeof($("#usu_nome").val()) != "undefined" && $("#usu_nome").val() !== null) {
//             	if($("#usu_nome").val() != "" && $("#no_ocupacao").val() != "" && $("#proc_nome").val() != "" && $("#med_codigo_solicitante").val() != 0 && $("#id_nivelurgencia").val() != 0 && $("#dt_entrada").val() != ""){
////                    setOrdem();
////                    console.log("teste");
//                }
//        }else{
//            setOrdem($("#tle_codigo").val(),$("#proc_codigo").val());
////            console.log("tesasdasdasdadate");
//        }
//
//     });
 });
function submitForm(){
    var val = document.filtro.especialidade.value;
    if(val!=-1){
        document.filtro.submit();
    }
}
function submitFormTipo(){
    var val = document.tipoForm.tipo.value;
    if(val!=-1){
        document.tipoForm.submit();
    }
}
</script>

<script>

  //var tle_codigo;

  // function imgCarregando(){
  // 	return "<div class=\"c\"><img class=\"loading\" src=\"zf/public/images/load.gif\" alt=\"Carregando...\" title=\"Carregando...\" /></div>";
  // }
  // function mensagemSemOk(id, titulo, mensagem, x, y){
  // 	$("body").append("<div id=\""+id+"\" title=\""+titulo+"\"><div class=\"c\">"+mensagem+"</div>"+imgCarregando()+"</div>");
  // 	$("#"+id).dialog({
  // 		modal: true,
  // 		resizable: false,
  // 		width: x,
  // 		height: y,
  //     close: function(){
	// 			$(this).remove();
	// 		},
  //     buttons: {
	// 			CIRURGIAS: function(){
  //         var tipo_escolhido = 3;
  //         atualizarTableComTipo(tipo_escolhido);
	// 				$(this).dialog('close');
	// 			},
  //       EXAMES: function(){
  //         var tipo_escolhido = 2;
  //         atualizarTableComTipo(tipo_escolhido);
	// 				$(this).dialog('close');
	// 			},
	// 			CONSULTAS: function(){
  //         var tipo_escolhido = 1;
  //         atualizarTableComTipo(tipo_escolhido);
	// 				$(this).dialog('close');
	// 			},
  //       ODONTO: function(){
  //         var tipo_escolhido = 5;
  //         atualizarTableComTipo(tipo_escolhido);
	// 				$(this).dialog('close');
	// 			}
	// 		},
  // 	});
  // }

  // function atualizarTableComTipo(tipo_escolhido){
  //   $.ajax({
  //       type: 'POST',
  //       url: "listadeespera.php",
  //       data: {
  //           tipo_escolhido: tipo_escolhido
  //       },
  //       success: function(data){
  //         $("body").html(data);
  //       }
  //   });
  // }

  function mensagemValidaAdd(id, titulo, mensagem, x, y){
  	$("body").append("<div id=\""+id+"\" title=\""+titulo+"\"><div class=\"c\">"+mensagem+"</div></div>");
  	$("#"+id).dialog({
  		modal: true,
  		resizable: false,
  		width: x,
  		height: y,
      close: function(){
				$(this).remove();
			},
      buttons: {
				OK: function(){
					$(this).dialog('close');
				}
			},
  	});
  }

  function validarAdd(form){
  	if($("#usu_nome").val() == ""){
  		mensagemValidaAdd("select-tipo", "Erro", "Preencha o Paciente para continuar.", 250, 110);
    } else if($("#no_ocupacao").val() == "") {
      mensagemValidaAdd("select-tipo", "Erro", "CBO precisa ser preenchido. Se n&atilde;o houver valor deixe zero (0).", 250, 110);
  	} else if($("#proc_nome").val() == "") {
      mensagemValidaAdd("select-tipo", "Erro", "Preencha Procedimento para continuar.", 250, 110);
    } else if($("#med_codigo_solicitante").val() == 0) {
      mensagemValidaAdd("select-tipo", "Erro", "Escolha um M&eacute;dico para continuar.", 250, 110);
    } else if($("#id_nivelurgencia").val() == 0) {
      mensagemValidaAdd("select-tipo", "Erro", "Escolha um n&iacute;vel de urg&ecirc;ncia para continuar.", 250, 110);
    } else if($("#dt_entrada").val() == "") {
      mensagemValidaAdd("select-tipo", "Erro", "Preencha a Data para continuar.", 250, 110);
  	} else {
  		form.submit();
  	}
  }

  function validaBuscaInit(){
    if($("#tle_codigo").val() == "" || $("#tle_codigo").val() == 0 ) {
      mensagemValidaAdd("select-tipo", "Erro", "Preencha Tipo para continuar..", 250, 110);
  	} else if($("#proc_nome").val() == "") {
      mensagemValidaAdd("select-tipo", "Erro", "Preencha Procedimento para continuar.", 250, 110);
    } else {
      $.ajax({
          type: 'POST',
          url: "listadeespera.php",
          data: {
            somente_ativos : 1,
            tle_codigo : $("#tle_codigo").val(),
            proc_nome : $("#proc_nome").val(),
            pagina_atual : 1
          },
          success: function(data){
            $("body").html(data);
          }
      });
    }
  }

  function validaBusca(){
      $.ajax({
          type: 'POST',
          url: "listadeespera.php",
          data: {
            busca : $("#busca").val(),
            tipo_busca : $("#tipo_busca").val(),
            somente_ativos : $("#somente_ativos").val(),
            tle_codigo : $("#tle_codigo").val(),
            proc_nome : $("#proc_nome").val(),
            pagina_atual : 1
          },
          success: function(data){
            $("body").html(data);
          }
      });
  }

  function validaPaginacao(){
      $.ajax({
          type: 'POST',
          url: "listadeespera.php",
          data: {
            busca : $("#busca").val(),
            tipo_busca : $("#tipo_busca").val(),
            somente_ativos : $("#somente_ativos").val(),
            tle_codigo : $("#tle_codigo").val(),
            proc_nome : $("#proc_nome").val(),
            pagina_atual : $("#pagina_atual").val()
          },
          success: function(data){
            $("body").html(data);
          }
      });
  }

  $( document ).ready(function() {

    $("#no_ocupacao").val("0");

    $("#tle_codigo").change(function(){
      	$("#proc_nome").show();
          $("#proc_nome").buscar({
      			tipo:"procedimento",
            parametro:$("#tle_codigo").val(),
      			template : function(ul, item) {
      			return jQuery("<li></li>").data("item.autocomplete", item).append(
      						"<a><strong>" + item.label + "</strong></a>").appendTo(ul);
      			},
      			callback:function(){
              return true;
            }
      		});
    });

		$("#usu_nome").buscar({
			tipo:"usuario",
			template : function(ul, item) {
			return jQuery("<li></li>").data("item.autocomplete", item).append(
						"<a><strong>" + item.label + "</strong>"
								+ "<br><strong>Data Nasc.:</strong> "
								+ item.data.usu_datanasc
								+ " <strong>Mãe:</strong> " + item.data.usu_mae
								+ "</a>&nbsp;").appendTo(ul);
			},
			callback:function(){return true;}
		});

    $("#busca").buscar({
			tipo:"usuario",
			template : function(ul, item) {
			return jQuery("<li></li>").data("item.autocomplete", item).append(
						"<a><strong>" + item.label + "</strong>"
								+ "<br><strong>Data Nasc.:</strong> "
								+ item.data.usu_datanasc
								+ " <strong>Mãe:</strong> " + item.data.usu_mae
								+ "</a>&nbsp;").appendTo(ul);
			},
			callback:function(){return true;}
		});

		$("#no_ocupacao").buscar({
			tipo:"cbo",
			template : function(ul, item) {
			return jQuery("<li></li>").data("item.autocomplete", item).append(
						"<a><strong>" + item.label + "</strong></a>").appendTo(ul);
			},
			callback:function(){return true;}
		});

    $("#proc_busca").buscar({
      tipo:"procedimento",
      parametro:$("#tipo_escolhido").val(),
      template : function(ul, item) {
      return jQuery("<li></li>").data("item.autocomplete", item).append(
            "<a><strong>" + item.label + "</strong></a>").appendTo(ul);
      },
      callback:function(){
        return true;
      }
    });

    $('#pagina_atual').keypress(function(event) {
        if (event.keyCode == 13) {
            event.preventDefault();
        }
    });

	});
</script>
<style>
div.cl5 {
  width : 30px;
}
</style>
<?php
// if(isset($_POST['tle_codigo'])){
//     $tle_codigo = $_POST['tle_codigo'];
//     $sqlUnidade = "select proc_name from procedimento where tle_codigo=$tle_codigo";
//     echo json_encode(pg_query($sqlUnidade));
// }
echo $common->menuTab(array('Lista de Espera de Consultas'));
echo $common->bodyTab('1');
  $sqlTipo = "select tle_codigo,tle_nome from tipo_listaespera where tle_codigo != 4 order by tle_nome";

  if(!isset($_REQUEST['pagina_atual'])){
    $pagina_atual = 1;
  } else {
    $pagina_atual = $_REQUEST['pagina_atual'];
  }

  if(strpos($acao, 'init') !== false){
    echo $table->openTable();
           $arrayAddBuscaForm = array($form->openForm("listadeespera.php?id_login=$id_login", "POST", "formBusca").
                                   $form->inputSelect("tle_codigo",null,"Tipo Lista",$sqlTipo,null,null,$rr[tle_codigo],null,"SELECIONE").
                                   $form->inputText("proc_nome",$rr[proc_nome],"Procedimento/Especialidade",50));
           $arrayAddBusca = array( $common->commonButton("Gerar Tabela    ", null, "buscar.png", "onClick=\"validaBuscaInit();\"").$form->closeForm());
           $arrayAdd = array($common->commonButton("Adicionar a lista de espera", $PHP_SELF."?acao=form_add&tipo_escolhido=$tipo_escolhido", "adicionar.png", null));
           echo $table->criaLinha($arrayAddBuscaForm);
           echo $table->criaLinha($arrayAddBusca);
           echo $table->criaLinha($arrayAdd);
           echo $table->closeTable();
           echo "</select></td></tr>
     					</table><br></form>";
  }
	if($acao == ""){

    //  if(isset($_POST['tipo_escolhido']) || $tipo_escolhido != NULL){
    //    if(isset($_POST['tipo_escolhido'])){
    //       $tipo_escolhido = $_POST['tipo_escolhido'];
    //    }

		 echo $table->openTable();
            // $arrayTipoBusca = array("usu_nome"      =>"NOME",
            //                         "usu_mae"       =>"NOME DA M&Atilde;E",
            //                         "usu_datanasc"  =>"DATA DE NASCIMENTO",
            //                         "usu_cartao_sus"=>"CNS",
            //                         "usu_prontuario"=>"PRONTU&Aacute;RIO",
            //                         "usu_rg"        =>"RG",
            //                         "usu_cpf"       =>"CPF");
            $arrayAddBuscaForm = array(
                                    $form->openForm("listadeespera.php?id_login=$id_login", "POST", "formBusca").$form->hiddenForm("act", "busca").$form->inputText("busca", null, "Buscar", 40, null, null, "text", "N", "S").
                                    //$form->inputSelect("tipo_busca", $arrayTipoBusca, "Tipo Busca:", null, "onChange=\"verificaTipo(this.value);\"", null, "usu_nome", "style=\"width:150px;\"").
                                    $form->inputSelect("somente_ativos",array('1'=>'AGUARDANDO','2'=>'AGENDADOS','3'=>'CANCELADOS'),"Agendados/Cancelados:",null,null,null,$_REQUEST['somente_ativos'],null,"SELECIONE").
                                    $form->inputSelect("tle_codigo",null,"Tipo Lista",$sqlTipo,null,null,$_REQUEST['tle_codigo'],null,"SELECIONE").
                                    $form->inputText("proc_nome",$_REQUEST['proc_nome'],"Procedimento/Especialidade",50));
            $arrayAddBusca = array( $common->commonButton("Buscar    ", null, "buscar.png", "onClick=\"validaBusca();\"").$form->closeForm());
            $arrayAdd = array($common->commonButton("Adicionar a lista de espera", $PHP_SELF."?acao=form_add&tipo_escolhido=$tipo_escolhido", "adicionar.png", null));
            echo $table->criaLinha($arrayAddBuscaForm);
            echo $table->criaLinha($arrayAddBusca);
            echo $table->criaLinha($arrayAdd);
            echo $table->closeTable();
            //var_dump($_REQUEST['busca']);
//		echo $common->commonButton("Adicionar a lista",$PHP_SELF."?acao=form_add","adicionar.png");
	// 		echo "<form method='post' action='listadeespera.php' name='filtro'>
	// 				<br><table width=100% cellspacing=2 cellpadding=5 border=0>
	// 				<tr>
	// 				 <td width=30 align='right'><b>Especialidade:</b>&nbsp;</td>
	// 					<input type='hidden' name='tipo' value='".$_REQUEST['tipo']."'>
	// 				 <td><select name='especialidade' id='especialidade' onchange='submitForm();' class='box'>
	// 				 <option value=''>::..:: SELECIONE ::..::</option>";
	// 	$sq = pg_query("select distinct(necessidade_sforma),sf.descricao from listaespera as a join sforma as sf on sf.sforma = a.necessidade_sforma group by sf.descricao,necessidade_sforma order by descricao");
	// while($rw=pg_fetch_array($sq)) {
	// 		echo ($rw[necessidade_sforma]==$_REQUEST['especialidade'])?"<option value='$rw[necessidade_sforma]' selected>$rw[descricao]</option>":"<option value='$rw[necessidade_sforma]'>$rw[descricao]</option>";
	// }
	// 		echo "</select></td></tr></form>
	// 				<tr><form method='post' action='listadeespera.php' name='tipoForm' >
	// 					<input type='hidden' name='especialidade' value='".$_REQUEST['especialidade']."'>
	// 				 <td width='30' align='right'><b>Tipo:</b>&nbsp;</td>
	// 				 <td><select name='tipo' onchange='submitFormTipo();' class='box'>
	// 				 <option value=''>::..:: SELECIONE ::..::</option>";
	// 	$sq = pg_query("select *from tipo_listaespera order by tle_nome");
	// while($rt=pg_fetch_array($sq)) {
	// 		echo ($rt[tle_codigo]==['tipo'])?"<option value='$rt[tle_codigo]' selected>$rt[tle_nome]</option>":"<option value='$rt[tle_codigo]'>$rt[tle_nome]</option>";
	// }
			echo "</select></td></tr>
					</table><br></form>";
      echo $table->openTable("pags");
        //$andTip = "AND a.tle_codigo = 1";
        if(!empty($_REQUEST['busca'])) {
      		$Andbusca = "AND usu.usu_nome ilike '".$_REQUEST['busca']."%'";
      	}
      	if(!empty($_REQUEST['proc_nome'])) {
          $andEsp = "AND proc.proc_nome = '".$_REQUEST['proc_nome']."'";
        }
        if(!empty($_REQUEST['tle_codigo'])) {
            $andTip = "AND a.tle_codigo = '".$_REQUEST['tle_codigo']."'";
        }
        if(($_REQUEST['somente_ativos']=="2")) {
          $sql_count = "select count(*)
  							from listaespera as a
  							left join medico as b on b.med_codigo=a.med_codigo_solicitante
                left join procedimento as proc on proc.proc_codigo = a.necessidade_procsia
                left join sforma as sf on sf.sforma = a.necessidade_procsia
  							left join usuario as usu on usu.usu_codigo=a.usu_codigo
  							left join tipo_listaespera as tle on tle.tle_codigo=a.tle_codigo
  							where status_espera='A'
                $Andbusca
  							$andEsp
  							$andTip";
        } elseif(($_REQUEST['somente_ativos']=="3")) {
          $sql_count = "select count(*)
  							from listaespera as a
  							left join medico as b on b.med_codigo=a.med_codigo_solicitante
                left join procedimento as proc on proc.proc_codigo = a.necessidade_procsia
                left join sforma as sf on sf.sforma = a.necessidade_procsia
  							left join usuario as usu on usu.usu_codigo=a.usu_codigo
  							left join tipo_listaespera as tle on tle.tle_codigo=a.tle_codigo
  							where status_espera='C'
                $Andbusca
  							$andEsp
  							$andTip";
        } else {
          $sql_count = "select count(*)
  							from listaespera as a
  							left join medico as b on b.med_codigo=a.med_codigo_solicitante
                left join procedimento as proc on proc.proc_codigo = a.necessidade_procsia
                left join sforma as sf on sf.sforma = a.necessidade_procsia
  							left join usuario as usu on usu.usu_codigo=a.usu_codigo
  							left join tipo_listaespera as tle on tle.tle_codigo=a.tle_codigo
  							where status_espera is null
                $Andbusca
  							$andEsp
  							$andTip";
        }

        $count_sql = pg_fetch_array(pg_query($sql_count));
        $numero_paginas = ceil($count_sql[count]/20);
        //var_dump($numero_paginas);

        echo $table->criaLinha(array($form->openForm("listadeespera.php?id_login=$id_login", "GET", "paginacao").$form->inputText("pagina_atual",$pagina_atual,"Pagina",5),
                                    "<label> de $numero_paginas </label>".$form->closeForm(),
                                    $common->commonButton("Ir", null, "next.gif", "onClick=\"validaPaginacao();\"")));
      echo $table->closeTable();



      echo $table->openTable("lista");
				//echo $table->criaLinha(array("Status","C&oacute;digo","Paciente","Dt Agendamento","Data Entrada","Urgen","Medico","Especialidade","Uni Origem","Usuario","Tipo","&nbsp;"),null,array("","","","","","","","","","3"),"S");
				echo $table->criaLinha(array("Posicao","Status","Paciente","Data Agendamento","Data Entrada","Urgencia","Medico","Procedimento","Tipo","Comandos"),null,array("","","","","","","","","3"),"S");

	if(!empty($_REQUEST['busca'])) {
		$Andbusca = "AND usu.usu_nome ilike '".$_REQUEST['busca']."%'";
    //var_dump($Andbusca);
	}

	if(!empty($_REQUEST['proc_nome'])) { $andEsp = "AND proc.proc_nome ilike '".$_REQUEST['proc_nome']."%'"; }

  $lim = "limit 20";
  $offset = "offset ($pagina_atual-1)*20";

  //if(!empty($_REQUEST['tipo'])) { $andTip = "AND a.tle_codigo = '".$_REQUEST['tipo']."'"; }
  if(!empty($_REQUEST['tle_codigo'])) {
      $andTip = "AND a.tle_codigo = '".$_REQUEST['tle_codigo']."'";
  }

//	echo $andEsp;
	if(($_REQUEST['somente_ativos']=="2")) {
				$sqlSec = "select to_char(atendido_data_agenda,'dd/mm/YYYY') as data_agendada,cancelamento_datahora,atendido_tabela,lis_codigo,tle_nome,usu_nome,to_char(dt_entrada,'dd/mm/YYYY') as data_entrada,usr_nome,id_nivelurgencia,numero_ordem,med_nome, status_espera,proc.proc_nome as prc, sf.sforma as sfor
							from listaespera as a
							left join medico as b on b.med_codigo=a.med_codigo_solicitante
              left join procedimento as proc on proc.proc_codigo = a.necessidade_procsia
              left join sforma as sf on sf.sforma = a.necessidade_procsia
							left join usuario as usu on usu.usu_codigo=a.usu_codigo
							left join tipo_listaespera as tle on tle.tle_codigo=a.tle_codigo
							where status_espera='A'
							$Andbusca
							$andEsp
							$andTip
							order by a.numero_ordem
							$lim
              $offset";

		} elseif(($_REQUEST['somente_ativos']=="3")) {
				$sqlSec = "select to_char(atendido_data_agenda,'dd/mm/YYYY') as data_agendada,cancelamento_datahora,atendido_tabela,lis_codigo,tle_nome,usu_nome,to_char(dt_entrada,'dd/mm/YYYY') as data_entrada,usr_nome,id_nivelurgencia,numero_ordem,med_nome, status_espera, proc.proc_nome as prc, sf.sforma as sfor
							from listaespera as a
							left join medico as b on b.med_codigo=a.med_codigo_solicitante
              left join procedimento as proc on proc.proc_codigo = a.necessidade_procsia
              left join sforma as sf on sf.sforma = a.necessidade_procsia
							left join usuario as usu on usu.usu_codigo=a.usu_codigo
							left join tipo_listaespera as tle on tle.tle_codigo=a.tle_codigo
							where status_espera='C'
							$Andbusca
							$andEsp
							$andTip
							order by a.numero_ordem
							$lim
              $offset";
		} else {
				$sqlSec = "select to_char(atendido_data_agenda,'dd/mm/YYYY') as data_agendada,cancelamento_datahora,atendido_tabela,lis_codigo,tle_nome,usu_nome,to_char(dt_entrada,'dd/mm/YYYY') as data_entrada,usr_nome,id_nivelurgencia,numero_ordem,med_nome, status_espera, proc.proc_nome as prc, sf.sforma as sfor
							from listaespera as a
							left join medico as b on b.med_codigo=a.med_codigo_solicitante
              left join procedimento as proc on proc.proc_codigo = a.necessidade_procsia
              left join sforma as sf on sf.sforma = a.necessidade_procsia
							left join usuario as usu on usu.usu_codigo=a.usu_codigo
							left join tipo_listaespera as tle on tle.tle_codigo=a.tle_codigo
							where status_espera is null
							$Andbusca
							$andEsp
							$andTip
							order by a.numero_ordem
							$lim
              $offset";
		}

				$qrySec = pg_query($sqlSec) or die(pg_last_error());

				while($rr = pg_fetch_array($qrySec)){

				/** SE AGENDAMENTO REALIZADO OU CANCELADO OU AGUARDANDO AGENDAR **/
				if(strpos($rr[status_espera], 'A') === 0) {
          $b2 = $common->commonButton("cancelar",$PHP_SELF."?acao=cancelar&lis_codigo=$rr[lis_codigo]","removeEvent.png");
					$f2 = "<font color=green><b>AGENDADO</b><font>";
					$b1 = "";
				} elseif(strpos($rr[status_espera], 'C') === 0) {
          $b1 = $common->commonButton("Agendar",$PHP_SELF."?acao=agendar&lis_codigo=$rr[lis_codigo]","recepcionar_calendar.png");
					$f2 = "<font color=red><b>CANCELADO<b></font>";
					$b2 = "";
				} else {
					$f2 = "<font color=blue><b>AGUARDANDO<b></font>";
					$b1 = $common->commonButton("Agendar",$PHP_SELF."?acao=agendar&lis_codigo=$rr[lis_codigo]","recepcionar_calendar.png");
					$b2 = $common->commonButton("cancelar",$PHP_SELF."?acao=cancelar&lis_codigo=$rr[lis_codigo]","removeEvent.png");
				}



				if($rr[id_nivelurgencia]==1) { $nivel = '<font color=green>Baixa</font>'; }
				if($rr[id_nivelurgencia]==2) { $nivel = '<font color=orange>Media</font>'; }
				if($rr[id_nivelurgencia]==3) { $nivel = '<font color=red>Alta</font>'; }
				if($rr[id_nivelurgencia]==4) { $nivel = '<font color=yellow>Retorno</font>'; }

            echo $table->criaLinha(array("$rr[numero_ordem]","$f2","$rr[usu_nome]","<font color=blue><b><center>$rr[data_agendada]</center></b></font>","$rr[data_entrada]","<b>$nivel</b>","$rr[med_nome]","$rr[prc]","$rr[tle_nome]",
  					$common->commonButton("Editar",$PHP_SELF."?acao=form_edit&lis_codigo=$rr[lis_codigo]","editar_on.png"),
  					$b1,
  					$b2
  					));
				}
			echo $table->closeTable();
    }
	// }
if(($acao == "form_add" OR $acao == "form_edit")){

		$rr = pg_fetch_array(pg_query("select proc.proc_nome as proc,to_char(dt_entrada,'dd/mm/YYYY') as dt_ent,*
							from listaespera as a
							left join medico as b on b.med_codigo=a.med_codigo_solicitante
							left join usuario as usu on usu.usu_codigo=a.usu_codigo
							left join tipo_listaespera as tle on tle.tle_codigo=a.tle_codigo
							left join procedimento as proc on proc.proc_codigo = a.necessidade_procsia
							left join tb_ocupacao as t on t.co_ocupacao = a.necessidade_cbo
							where lis_codigo = '".$_REQUEST['lis_codigo']."'"));


   echo $form->openForm($PHP_SELF,'POST','form');

if(empty($_REQUEST['lis_codigo'])) {
		echo $form->hiddenForm("acao", "salvar");
		echo $form->hiddenForm("usu_codigo", "$usu_codigo");
		echo $form->hiddenForm("co_ocupacao", "$co_ocupacao");
		echo $form->hiddenForm("proc_codigo", "$proc_codigo");
} else {
		echo $form->hiddenForm("acao", "salvar_edicao");
		echo $form->hiddenForm("usu_codigo", "$usu_codigo");
		echo $form->hiddenForm("co_ocupacao", "$co_ocupacao");
		echo $form->hiddenForm("proc_codigo", "$proc_codigo");
		echo $form->hiddenForm("new_proc_codigo", "$rr[necessidade_procsia]");
		echo $form->hiddenForm("lis_codigo", $_REQUEST['lis_codigo']);
}
		echo $form->inputText("usu_nome",$rr[usu_nome],"Paciente",50,null,null,null,'N');
		echo $form->inputCheckboxRadio("gestante", ($rr['gestante'] == "f" ? "t" : "f"), "Gestante", "onChange=\"swap('cmp2','cmp',this.value)\"", array("f"=>"Nao","t"=>"Sim"), "radio");
		echo $form->inputText("no_ocupacao",$rr[co_ocupacao],"CBO",50);
		$sqlTipo = "select tle_codigo,tle_nome from tipo_listaespera where tle_codigo != 4 order by tle_nome";
		echo $form->inputSelect("tle_codigo",null,"Tipo Lista",$sqlTipo,null,null,$rr[tle_codigo],null,"SELECIONE");
		echo $form->inputText("proc_nome",$rr[proc_nome],"Procedimento/Especialidade",50);
    // $sqlUnidade = "select proc_nome from procedimento where tle_codigo=3";
    // echo $form->inputSelect("proc_nome",null,"Procedimento/Especialidade",$sqlUnidade,null,null,null,"TODAS");

		//$sqlUnidade = "select uni_codigo,uni_desc from unidade order by uni_desc";
		//echo $form->inputSelect("origem_uni_codigo_solicitante",null,"Unidade Solicitante",$sqlUnidade,null,null,$rr[origem_uni_codigo_solicitante],null,"TODAS");
		echo $form->inputSelect("id_nivelurgencia",array('1'=>'Baixa','2'=>'Media','3'=>'Alta','4'=>'Retorno'),"Prioridade",null,null,null,$rr[id_nivelurgencia],null,"TODAS");

		//$sqlForma = "select sforma,descricao from sforma order by descricao";
		//echo $form->inputSelect("necessidade_sforma",null,"Especialidade",$sqlForma,null,null,$rr[necessidade_sforma],null,"TODAS");

		$sqlMed = "select med_codigo,med_nome from medico order by med_nome";
		echo $form->inputSelect("med_codigo_solicitante",null,"Profissional Solicitante",$sqlMed,null,null,$rr[med_codigo_solicitante],null,"TODAS");
     //echo $form->inputSelect("med_codigo_solicitante",null,"Profissional Solicitante",null,null,null,$rr[med_codigo_solicitante],null,"TODAS");

    //echo $form->inputSelect("tipo_cons",array('1'=>'Normal','2'=>'Urgencia','3'=>'Retorno'),"Tipo Atendimento",null,null,null,$rr[tipo_cons],null,"TODAS");
		echo $form->inputText("dt_entrada",$rr[dt_ent],"Data Entrada",null,10,"onKeypress=\"return Ajusta_Data(this,event)\"");
		echo $form->textArea('motivo',$rr[motivo],'Motivo da Solicit./Obs. Geral','id_campo_nome',"style='width:330px;height:130px;'",null);



				echo"<br><br><br><br><br><br><div style='float:left;width:200px;'>&nbsp;</div><div style='float:left;'>";
				echo $common->commonButton("voltar","listadeespera.php?acao=init","voltar.png");
				echo"</div>";
				echo"<div style='float:left;'>";
				echo $common->commonButton("Salvar","","report.png","onclick='validarAdd(document.form);'");
				echo"</div><br><br>";

				echo $form->closeForm();
	}

if($acao == "agendar"){

	$rr = pg_fetch_array(pg_query("select proc.proc_nome as proc_nome, proc.proc_codigo as proc_codigo,to_char(dt_entrada,'dd/mm/YYYY') as dt_ent,*
							from listaespera as a
							left join medico as b on b.med_codigo=a.med_codigo_solicitante
							left join usuario as usu on usu.usu_codigo=a.usu_codigo
							left join tipo_listaespera as tle on tle.tle_codigo=a.tle_codigo
							left join procedimento as proc on proc.proc_codigo = a.necessidade_procsia
							where lis_codigo = '".$_REQUEST['lis_codigo']."'"));
   echo $form->openForm($PHP_SELF,'POST','form');


		echo "<table width='100%' cellspacing='1' cellpadding='5' border='0' style='height:10px;!important;border:1px dotted'>
				<tr>
					<td style='border-bottom:1px dotted' height=30 width=140 align=right><b>Paciente:&nbsp;</b></td>
					<td style='border-bottom:1px dotted'>$rr[usu_nome]</td>
				</tr>
				<tr>
					<td style='border-bottom:1px dotted' height=30 width=100 align=right><b>Procedimento:&nbsp;</b></td>
					<td style='border-bottom:1px dotted'>$rr[proc_nome]</td>
				</tr>
				<tr>
					<td style='border-bottom:1px dotted' height=30 width=100 align=right><b>Tipo:&nbsp;</b></td>
					<td style='border-bottom:1px dotted'>$rr[tle_nome]</td>
				</tr>
				<tr>
					<td style='border-bottom:1px dotted' height=30 width=100 align=right><b>Medico Solicitante:&nbsp;</b></td>
					<td style='border-bottom:1px dotted'>$rr[med_nome]</td>
				</tr>
				<tr>
					<td style='border-bottom:1px dotted'  height=30 width=100 align=right><b>Data Entrada:&nbsp;</b></td>
					<td style='border-bottom:1px dotted'>$rr[dt_ent]</td>
				</tr>
				<tr>
					<td  height=30 width=100 align=right><b>Motivo:&nbsp;</b></td>
					<td >$rr[motivo]</td>
				</tr>
			</table><br>";

		echo $form->hiddenForm("acao", "salva_agenda");
                echo $form->hiddenForm("proc_codigo", $rr[proc_codigo]);
                echo $form->hiddenForm("tle_codigo", $rr[tle_codigo]);
                echo $form->hiddenForm("proc_nome", $rr[proc_nome]);

		$sqlMed = "select med_codigo,med_nome from medico order by med_nome";
		echo $form->inputSelect("med_codigo",null,"Profissional Solicitante",$sqlMed,null,null,null,null,"TODAS");
		echo $form->inputText("dt_agenda",null,"Data Agendamento",null,10,"onKeypress=\"return Ajusta_Data(this,event)\"");
		echo $form->textArea('obs',$motivo,'Atendido Observacao','id_campo_nome',"style='width:330px;height:130px;'",null);


				echo"<br><br><br><br><br><br><div style='float:left;width:200px;'>&nbsp;</div><div style='float:left;'>";
				echo $common->commonButton("voltar","listadeespera.php?tle_codigo=$rr[tle_codigo]&proc_nome=$rr[proc_nome]","voltar.png");
				echo"</div>";
				echo"<div style='float:left;'>";
				echo $common->commonButton("Salvar","","report.png","onclick='document.form.submit();'");
				echo"</div><br><br>";

				echo $form->closeForm();

}


if($acao == "cancelar"){
	$rr = pg_fetch_array(pg_query("select proc.proc_codigo as proc_codigo,proc.proc_nome as proc_nome,to_char(dt_entrada,'dd/mm/YYYY') as dt_ent,*
							from listaespera as a
							left join medico as b on b.med_codigo=a.med_codigo_solicitante
							left join usuario as usu on usu.usu_codigo=a.usu_codigo
							left join tipo_listaespera as tle on tle.tle_codigo=a.tle_codigo
							left join procedimento as proc on proc.proc_codigo = a.necessidade_procsia
							where lis_codigo = '".$_REQUEST['lis_codigo']."'"));



   echo $form->openForm($PHP_SELF,'POST','form');


		echo "<table width='100%' cellspacing='1' cellpadding='5' border='0' style='height:10px;!important;border:1px dotted'>
				<tr>
					<td style='border-bottom:1px dotted' height=30 width=140 align=right><b>Paciente:&nbsp;</b></td>
					<td style='border-bottom:1px dotted'>$rr[usu_nome]</td>
				</tr>
				<tr>
					<td style='border-bottom:1px dotted' height=30 width=100 align=right><b>Procedimento:&nbsp;</b></td>
					<td style='border-bottom:1px dotted'>$rr[proc_nome]</td>
				</tr>
				<tr>
					<td style='border-bottom:1px dotted' height=30 width=100 align=right><b>Tipo:&nbsp;</b></td>
					<td style='border-bottom:1px dotted'>$rr[tle_nome]</td>
				</tr>
				<tr>
					<td style='border-bottom:1px dotted' height=30 width=100 align=right><b>Medico Solicitante:&nbsp;</b></td>
					<td style='border-bottom:1px dotted'>$rr[med_nome]</td>
				</tr>
				<tr>
					<td  height=30 width=100 align=right><b>Data Entrada:&nbsp;</b></td>
					<td >$rr[dt_ent]</td>
				</tr>
			</table><br>";

		echo $form->hiddenForm("acao", "cancela_agenda");
                echo $form->hiddenForm("proc_codigo", $rr[proc_codigo]);
                echo $form->hiddenForm("tle_codigo", $rr[tle_codigo]);
                echo $form->hiddenForm("proc_nome", $rr[proc_nome]);

		echo $form->textArea('motivo',$rr[motivo],'Motivo Cancelamento','id_campo_nome',"style='width:330px;height:130px;'",null)."<br><br><br><br><br><br>";
		echo $form->textArea('obs',$motivo,'Observacao','id_campo_nome',"style='width:330px;height:130px;'",null);



				echo"<br><br><br><br><br><br><div style='float:left;width:200px;'>&nbsp;</div><div style='float:left;'>";
				echo $common->commonButton("voltar","listadeespera.php?tle_codigo=$rr[tle_codigo]&proc_nome=$rr[proc_nome]","voltar.png");
				echo"</div>";
				echo"<div style='float:left;'>";
				echo $common->commonButton("Salvar","","report.png","onclick='document.form.submit();'");
				echo"</div><br><br>";

				echo $form->closeForm();


}


if($acao == "salvar"){
  //var_dump($_REQUEST);die;
 if($_REQUEST['no_ocupacao'] == ""){
   $_REQUEST['no_ocupacao'] = "Sem CBO";
 }

 $sql = "insert into listaespera (numero_ordem,origem_uni_codigo,usr_nome,usu_codigo,gestante,necessidade_cbo,necessidade_procsia,tle_codigo,id_nivelurgencia,med_codigo_solicitante,dt_entrada,motivo)
		values ('1','".$_SESSION['uni_codigo']."','$usr[usr_nome]','".$_REQUEST['usu_codigo']."','".$_REQUEST['gestante']."','".$_REQUEST['no_ocupacao']."','".$_REQUEST['proc_codigo']."','".$_REQUEST['tle_codigo']."',
		'".$_REQUEST['id_nivelurgencia']."','".$_REQUEST['med_codigo_solicitante']."','".$_REQUEST['dt_entrada']."','".$_REQUEST['motivo']."')";


	$query = pg_query($sql) or die(pg_last_error());

        $sql2 = "select  * from((
                    select * from social.listaespera as a
                    left join social.procedimento as proc on proc.proc_codigo = a.necessidade_procsia
                    left join social.tipo_listaespera as tle on tle.tle_codigo=a.tle_codigo where a.status_espera is null and  a.necessidade_procsia = {$_REQUEST['proc_codigo']} and a.tle_codigo = {$_REQUEST['tle_codigo']} and a.id_nivelurgencia = 3  order by a.dt_entrada, a.lis_codigo
                    )UNION ALL(
                    select  * from social.listaespera as a
                    left join social.procedimento as proc on proc.proc_codigo = a.necessidade_procsia
                    left join social.tipo_listaespera as tle on tle.tle_codigo=a.tle_codigo where a.status_espera is null   and a.necessidade_procsia = {$_REQUEST['proc_codigo']} and a.tle_codigo = {$_REQUEST['tle_codigo']} and a.id_nivelurgencia = 2 order by a.dt_entrada, a.lis_codigo
                    )UNION ALL(
                    select  * from social.listaespera as a
                    left join social.procedimento as proc on proc.proc_codigo = a.necessidade_procsia
                    left join social.tipo_listaespera as tle on tle.tle_codigo=a.tle_codigo where a.status_espera is null and  a.necessidade_procsia = {$_REQUEST['proc_codigo']} and a.tle_codigo = {$_REQUEST['tle_codigo']} and a.id_nivelurgencia in(1,4) order by a.dt_entrada, a.lis_codigo
                    ))as t";
//                           echo "<pre>"; var_dump($sql2);die();;
                    $query2 = pg_query($sql2) or die(pg_last_error());
                    $ordem = 1;
                     while($rr = pg_fetch_array($query2)){
                        $update = "update social.listaespera set numero_ordem = {$ordem} where lis_codigo = {$rr['lis_codigo']}";
            //            echo $update;
                        $query = pg_query($update) or die(pg_last_error());
                        $ordem++;
                     }

	echo $common->modalMsg("OK","Salva Com Sucesso!","listadeespera.php?tle_codigo=$_REQUEST[tle_codigo]&proc_nome=$_REQUEST[proc_nome]");

}

if($acao == "salvar_edicao"){
//    var_dump($_REQUEST);die;
 $sql = "update listaespera set gestante='".$_REQUEST['gestante']."',necessidade_cbo='".$_REQUEST['co_ocupacao']."',necessidade_procsia='".$_REQUEST['new_proc_codigo']."',
 tle_codigo='".$_REQUEST['tle_codigo']."',id_nivelurgencia='".$_REQUEST['id_nivelurgencia']."',
 med_codigo_solicitante='".$_REQUEST['med_codigo_solicitante']."',motivo='".$_REQUEST['motivo']."' where lis_codigo = '".$_REQUEST['lis_codigo']."'";

 //die($sql);

	$query = pg_query($sql) or die(pg_last_error());

        $sql2 = "select  * from((
                    select * from social.listaespera as a
                    left join social.procedimento as proc on proc.proc_codigo = a.necessidade_procsia
                    left join social.tipo_listaespera as tle on tle.tle_codigo=a.tle_codigo where a.status_espera is null and  a.necessidade_procsia = {$_REQUEST['new_proc_codigo']} and a.tle_codigo = {$_REQUEST['tle_codigo']} and a.id_nivelurgencia = 3  order by a.dt_entrada, a.lis_codigo
                    )UNION ALL(
                    select  * from social.listaespera as a
                    left join social.procedimento as proc on proc.proc_codigo = a.necessidade_procsia
                    left join social.tipo_listaespera as tle on tle.tle_codigo=a.tle_codigo where a.status_espera is null   and a.necessidade_procsia = {$_REQUEST['new_proc_codigo']} and a.tle_codigo = {$_REQUEST['tle_codigo']} and a.id_nivelurgencia = 2 order by a.dt_entrada, a.lis_codigo
                    )UNION ALL(
                    select  * from social.listaespera as a
                    left join social.procedimento as proc on proc.proc_codigo = a.necessidade_procsia
                    left join social.tipo_listaespera as tle on tle.tle_codigo=a.tle_codigo where a.status_espera is null and  a.necessidade_procsia = {$_REQUEST['new_proc_codigo']} and a.tle_codigo = {$_REQUEST['tle_codigo']} and a.id_nivelurgencia in(1,4) order by a.dt_entrada, a.lis_codigo
                    ))as t";
//                           echo "<pre>"; var_dump($sql2);die();;
                    $query2 = pg_query($sql2) or die(pg_last_error());
                    $ordem = 1;
                     while($rr = pg_fetch_array($query2)){
                        $update = "update social.listaespera set numero_ordem = {$ordem} where lis_codigo = {$rr['lis_codigo']}";
            //            echo $update;
                        $query = pg_query($update) or die(pg_last_error());
                        $ordem++;
                     }
	echo $common->modalMsg("OK","Salva Com Sucesso!","listadeespera.php?tle_codigo=$_REQUEST[tle_codigo]&proc_nome=$_REQUEST[proc_nome]");

}



	if($acao == "salva_agenda") {
//            var_dump($_REQUEST);die;
		// if($_REQUEST['dt_agenda'] >= date("d/m/y")){

			$sql = "update listaespera set atendido_ususys='".$usr[usr_nome]."',atendido_datahora=NOW(),atendido_observacao='".$_REQUEST['obs']."',
			atendido_usr_codigo_login='".$usr[usr_codigo]."',atendido_tabela='listaespera',atendido_med_codigo='".$_REQUEST['med_codigo']."',atendido_data_agenda='".$_REQUEST['dt_agenda']."',status_espera='A',
			update_at=NOW()
			where lis_codigo = '".$_REQUEST['lis_codigo']."'";

			$query = pg_query($sql) or die(pg_last_error());

                        $sql2 = "select  * from((
                    select * from social.listaespera as a
                    left join social.procedimento as proc on proc.proc_codigo = a.necessidade_procsia
                    left join social.tipo_listaespera as tle on tle.tle_codigo=a.tle_codigo where a.status_espera is null and  a.necessidade_procsia = {$_REQUEST['proc_codigo']} and a.tle_codigo = {$_REQUEST['tle_codigo']} and a.id_nivelurgencia = 3  order by a.dt_entrada, a.lis_codigo
                    )UNION ALL(
                    select  * from social.listaespera as a
                    left join social.procedimento as proc on proc.proc_codigo = a.necessidade_procsia
                    left join social.tipo_listaespera as tle on tle.tle_codigo=a.tle_codigo where a.status_espera is null   and a.necessidade_procsia = {$_REQUEST['proc_codigo']} and a.tle_codigo = {$_REQUEST['tle_codigo']} and a.id_nivelurgencia = 2 order by a.dt_entrada, a.lis_codigo
                    )UNION ALL(
                    select  * from social.listaespera as a
                    left join social.procedimento as proc on proc.proc_codigo = a.necessidade_procsia
                    left join social.tipo_listaespera as tle on tle.tle_codigo=a.tle_codigo where a.status_espera is null and  a.necessidade_procsia = {$_REQUEST['proc_codigo']} and a.tle_codigo = {$_REQUEST['tle_codigo']} and a.id_nivelurgencia in(1,4) order by a.dt_entrada, a.lis_codigo
                    ))as t";
//                           echo "<pre>"; var_dump($sql2);die();;
                    $query2 = pg_query($sql2) or die(pg_last_error());
                    $ordem = 1;
                     while($rr = pg_fetch_array($query2)){
                        $update = "update social.listaespera set numero_ordem = {$ordem} where lis_codigo = {$rr['lis_codigo']}";
            //            echo $update;
                        $query = pg_query($update) or die(pg_last_error());
                        $ordem++;
                     }
			echo $common->modalMsg("OK","Salva Com Sucesso!","listadeespera.php?tle_codigo=$_REQUEST[tle_codigo]&proc_nome=$_REQUEST[proc_nome]");

		// } else {
		// 	echo $common->modalMsg("Erro","Data Inválida");
		// }

	}

	if($acao == "cancela_agenda") {
          //  var_dump($_REQUEST);die;
	$sql = "update listaespera set atendido_observacao='".$_REQUEST['obs']."',cancelamento_datahora=NOW(),cancelamento_motivo='".$_REQUEST['motivo']."',cancelamento_ususys='$usr[usr_nome]',status_espera='C'
	where lis_codigo = '".$_REQUEST['lis_codigo']."'";

	$query = pg_query($sql) or die(pg_last_error());
        $sql2 = "select  * from((
                    select * from social.listaespera as a
                    left join social.procedimento as proc on proc.proc_codigo = a.necessidade_procsia
                    left join social.tipo_listaespera as tle on tle.tle_codigo=a.tle_codigo where a.status_espera is null and  a.necessidade_procsia = {$_REQUEST['proc_codigo']} and a.tle_codigo = {$_REQUEST['tle_codigo']} and a.id_nivelurgencia = 3  order by a.dt_entrada, a.lis_codigo
                    )UNION ALL(
                    select  * from social.listaespera as a
                    left join social.procedimento as proc on proc.proc_codigo = a.necessidade_procsia
                    left join social.tipo_listaespera as tle on tle.tle_codigo=a.tle_codigo where a.status_espera is null   and a.necessidade_procsia = {$_REQUEST['proc_codigo']} and a.tle_codigo = {$_REQUEST['tle_codigo']} and a.id_nivelurgencia = 2 order by a.dt_entrada, a.lis_codigo
                    )UNION ALL(
                    select  * from social.listaespera as a
                    left join social.procedimento as proc on proc.proc_codigo = a.necessidade_procsia
                    left join social.tipo_listaespera as tle on tle.tle_codigo=a.tle_codigo where a.status_espera is null and  a.necessidade_procsia = {$_REQUEST['proc_codigo']} and a.tle_codigo = {$_REQUEST['tle_codigo']} and a.id_nivelurgencia in(1,4) order by a.dt_entrada, a.lis_codigo
                    ))as t";
//               echo "<pre>"; var_dump($sql2);die();;
            $query2 = pg_query($sql2) or die(pg_last_error());
            $ordem = 1;
         while($rr = pg_fetch_array($query2)){
            $update = "update social.listaespera set numero_ordem = {$ordem} where lis_codigo = {$rr['lis_codigo']}";
//            echo $update;
            $query = pg_query($update) or die(pg_last_error());
            $ordem++;
         }
//            die();
	echo $common->modalMsg("OK","Salva Com Sucesso!","listadeespera.php?tle_codigo=$_REQUEST[tle_codigo]&proc_nome=$_REQUEST[proc_nome]");


        }

	if($acao == "edita"){
		 if($fer_facultativo=="N") {
		 	$f_data = $fer_data."/9999";
		 } else {
		 	$f_data = $fer_data_nova;
		 }
		$sql = "UPDATE SET
					fer_data = '$f_data',
					fer_nome = UPPER('$fer_nome'),
					fer_facultativo = '$fer_facultativo'
				WHERE fer_codigo = $fer_codigo";
		$query = pg_query($sql);
		echo $common->modalMsg("OK","Salva Com Sucesso!",$PHP_SELF);
	}

        if($acao == "setOrdem"){
            var_dump($acao);
            die('asdasdasd');
		$sql = "select  * from((
                    select * from social.listaespera as a
                    left join social.procedimento as proc on proc.proc_codigo = a.necessidade_procsia
                    left join social.tipo_listaespera as tle on tle.tle_codigo=a.tle_codigo where a.necessidade_procsia = {$procedimento} and a.tle_codigo = {$tipo} and a.id_nivelurgencia = 3  order by a.dt_entrada, a.lis_codigo
                    )UNION ALL(
                    select  * from social.listaespera as a
                    left join social.procedimento as proc on proc.proc_codigo = a.necessidade_procsia
                    left join social.tipo_listaespera as tle on tle.tle_codigo=a.tle_codigo where a.necessidade_procsia = {$procedimento} and a.tle_codigo = {$tipo} and a.id_nivelurgencia = 2 order by a.dt_entrada, a.lis_codigo
                    )UNION ALL(
                    select  * from social.listaespera as a
                    left join social.procedimento as proc on proc.proc_codigo = a.necessidade_procsia
                    left join social.tipo_listaespera as tle on tle.tle_codigo=a.tle_codigo where a.necessidade_procsia = {$procedimento} and a.tle_codigo = {$tipo} and a.id_nivelurgencia in(1,4) order by a.dt_entrada, a.lis_codigo
                    ))as t";
                var_dump($sql);die();
            $query = pg_query($sql);
            $ordem = 1;
         while($rr = pg_fetch_array($query)){
            $update = "update social.listaespera set numero_ordem = {$ordem} where lis_codigo = {$rr['lis_codigo']}";
            $query = pg_query($update);
            $ordem++;
         }
	}



echo $common->closeTab();


?>
