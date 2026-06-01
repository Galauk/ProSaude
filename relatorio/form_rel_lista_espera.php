<?php

require_once '../global.php';
include_once COMUM."/library/php/funcoes.inc.php";
include_once SAUDE . '/__array.php';

?>
<html>
	<head>
<script language="JavaScript" type="text/javascript" src="../../WebSocialComum/library/js/jquery-1.6.2.min.js"></script>
<script language="JavaScript" type="text/javascript" src="../funcoes.js"></script>
<script language="JavaScript" type="text/javascript" src="../ajax_motor.js"></script>
<?

$common = new commonClass();
$form = new classForm();
$table = new tableClass();
echo $common->incJquery();

?>
<script>

	// function abreRelatorio(){
	// 	window.open('zf/relatorio/lista-espera/imprimir/tipo/'. $("#tle_codigo").val() .'/proc_nome/'. $("#proc_nome").val() .'');
	// }

$( document ).ready(function(){
	console.log("aqui");

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
});
</script>

	</head>
	<body>
<?

$data= date("d/m/Y");

echo $common->menuTab(array("Relatório de Lista de Espera por Procedimento"));
echo $common->bodyTab("1");
	echo $form->openForm("$PHP_SELF","POST","");

  // $rr = pg_fetch_array(pg_query("select proc.proc_nome as proc,to_char(dt_entrada,'dd/mm/YYYY') as dt_ent,*
  //           from listaespera as a
  //           left join medico as b on b.med_codigo=a.med_codigo_solicitante
  //           left join usuario as usu on usu.usu_codigo=a.usu_codigo
  //           left join tipo_listaespera as tle on tle.tle_codigo=a.tle_codigo
  //           left join procedimento as proc on proc.proc_codigo = a.necessidade_procsia
  //           left join tb_ocupacao as t on t.co_ocupacao = a.necessidade_cbo
  //           where lis_codigo = '".$_REQUEST['lis_codigo']."'"));

    $sqlTipo = "select tle_codigo,tle_nome from tipo_listaespera where tle_codigo != 4 order by tle_nome";
    echo $form->inputSelect("tle_codigo",null,"Tipo Lista",$sqlTipo,null,null,$rr[tle_codigo],null,"TODAS");
		echo $form->inputText("proc_nome",$rr[proc_nome],"Procedimento/Especialidade",50);

		echo "<div style='clear:both'>";
		echo "<div style='clear:both; width:400px; border:solid 0px;'>";
					echo"<div style='float:right; width:205px;'>";
						echo $common->commonButton("gerar relatorio","","report.png","onClick=\"abreRelatorio()\"");
					echo"</div>";
					echo"<div style='float:right'>";
						echo $common->commonButton("voltar","../rel_index.php?id_login=$id_login#tabs-16","voltar.png");
					echo"</div>";
				echo"</div>";


		echo "</div>";

	echo $form->closeForm();
echo $common->closeTab();
?>
	</body>
</html>
