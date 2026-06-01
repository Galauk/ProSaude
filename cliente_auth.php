<?php
require_once "global.php";
include_once COMUM."/library/php/funcoes.inc.php";
include_once SAUDE . '/__array.php';
?>

<script language="JavaScript" type="text/javascript" src="ajax_motor.js"></script>

<?php
$form = new classForm();
$common = new commonClass();
$table = new tableClass();
echo $common->incJquery();
?>

<script>

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

  function validaCNS(){
    if($("#usu_cns").val() == ""){
  		mensagemValidaAdd("select-tipo", "Inv&aacutelido", "Por Favor, insira seu CNS para acessar. ", 250, 110);
    } else {
      $.ajax({
          type: 'POST',
          url: "cliente_auth.php",
          data: {
              cns_codigo: $("#usu_cns").val()
          },
          success: function(data){
            $("body").html(data);
          }
      });
    }
  }
</script>
<style>
  .btn {
  position: relative;

  display: block;
  margin: auto auto;
  padding: 0;
  font-weight: bold;
  overflow: hidden;

  border-width: 0;
  outline: none;
  border-radius: 2px;
  box-shadow: 0 1px 4px rgba(0, 0, 0, .6);

  background-color: #2196F3;
  color: #FFF;

  width: 100px;
  height: 30px;
  }
  table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
  }

  td, th {
  border: 1px solid #000;
  text-align: left;
  padding: 8px;
  color: black;
  background-color: #dddddd;
  }

  tr:nth-child(even) {
  background-color: #dddddd;
  }
  .footer.rodape {
      text-align: center;
  }
  input#usu_cns.box {
  	height: 26px;
  }
  input:focus {
  	border:none;
  }
  th.titulos {
  	color: black;
  	font-size: 16px;
  	font-weight: bold;
  }
</style>

<html>
<head>
  <script language="JavaScript" type="text/javascript" src="/WebSocialComum/library/js/jquery-1.6.2.min.js"></script>
  <script type="text/javascript" src="/WebSocialSaude/lib/ui/jquery-ui-1.8.10.custom.js"></script>
  <script type="text/javascript" src="/WebSocialComum/library/js/jquery.buscar.js"></script>
</head>
<body bgcolor="#006064">

<?php
if(!isset($_POST['cns_codigo'])) {
?>
  <form>
    <div class="logo" style="position: absolute; top: 20%; left: 35%;">
    	<img src="imgs/logoprosaude.png" width="430"/>
    </div>
  	<div style="position: absolute; top: 40%; left: 38%; text-align: center;">
  			<span style='font-family:Monospace;color:#FFFFFF;font-size:26px'>Acesse sua posi&ccedil&atildeo na <br>
  			Lista de Espera:</span><br><br>
  			<div style="text-align= right; color:#FFF; font-size:16px; font-family:Monospace;">CNS (Cart&atildeo SUS): <input type=text nome="usu_cns" id="usu_cns" class='box' ><br></div>
        <br><br>
        <input type="button" value="ACESSAR" href="#" class="btn" onclick="validaCNS()"><br><br>
  			<img src='imgs/logoibitechbranco.png' style='vertical-align:middle' height='50'>
  	</div>
  </form>
<?php
} else {
  $cns_codigo = $_POST['cns_codigo'];
  $sqlpesc = "select numero_ordem,to_char(atendido_data_agenda,'dd/mm/YYYY') as data_agendada,usu_nome,to_char(dt_entrada,'dd/mm/YYYY') as data_entrada,id_nivelurgencia,med_nome, status_espera,proc.proc_nome as prc
              from listaespera as a
              left join medico as b on b.med_codigo=a.med_codigo_solicitante
              left join procedimento as proc on proc.proc_codigo = a.necessidade_procsia
              left join tipo_listaespera as tle on tle.tle_codigo=a.tle_codigo
              join usuario as usu on usu.usu_codigo=a.usu_codigo
              where usu.usu_cartao_sus = '".$cns_codigo."'";
  $qrySec = pg_query($sqlpesc) or die(pg_last_error());
  $rr = pg_fetch_array($qrySec);
  if($rr){
?>


<div class="logo" style="position: absolute; left: 35%;">
    	<img src="imgs/logoprosaude.png" width="430"/>
<div class="header" style="text-align: center; font-size: 26px; font-weight: bold; color: #fff; top: 5%; left:35%;">
	Posi&ccedil&atildeo na Lista de Espera
		<div style="top: 10%; left: 45%;">
			<INPUT TYPE="button" onClick="history.go(0)" VALUE="VOLTAR" class="btn">
    </div>
		</div>
</div>

<div class="container" style="padding: 200px 50px 0px 50px;">
	<table width="100%" >
	  <tbody>
	    <table>
	        <tr>
	          <th style="width: 250px;" class="titulos">
	              <b>Posi&ccedil&atildeo</b>
	          </th>
	          <th style="width: 250px;" class="titulos">
	              <b>Status</b>
	          </th>
	          <th style="width: 1000px;" class="titulos">
	              <b>Paciente</b>
	          </th>
	          <th style="width: 300px;" class="titulos">
	              <b>Data Agendamento</b>
	          </th>
	          <th style="width: 300px;" class="titulos">
	              <b>Data Entrada</b>
	          </th>
	          <th style="width: 300px;" class="titulos">
	              <b>Urgencia</b>
	          </th>
	              <th style="width: 1000px;" class="titulos">
	              <b>Medico</b>
	          </th>
	          <th style="width: 300px;" class="titulos">
	              <b>Procedimento</b>
	          </th>
	        </tr>
	        <?php
	            while($rr) {
	         ?>
	        <tr>
	          <td class="registros">
	              <?php echo $rr[numero_ordem]; ?>
	          </td>
	          <td class="registros">
	              <?php
	              if(strcmp($rr[status_espera],"A") === 24){
	                echo "AGENDADO";
	              } elseif(strcmp($rr[status_espera],"A") === 24){
	                echo "CANCELADO";
	              } else {
	                echo "AGUARDANDO";
	              }
	              ?>
	          </td>
	          <td class="registros">
	              <?php echo $rr[usu_nome]; ?>
	          </td>
	          <td  class="registros">
	              <?php echo $rr[data_agendada]; ?>
	          </td>
	          <td  class="registros">
	             <?php echo $rr[data_entrada]; ?>
	          </td>
	          <td  class="registros">
	            <?php
	            if($rr[id_nivelurgencia] === 1){
	              echo "Baixa";
	            } elseif($rr[id_nivelurgencia] === 2){
	              echo "Media";
	            } elseif($rr[id_nivelurgencia] === 3){
	              echo "Alta";
	            } else {
	              echo "Retorno";
	            }
	            ?>
	          </td>
	          <td  class="registros">
	              <?php echo $rr[med_nome]; ?>
	          </td>
	          <td  class="registros">
	             <?php echo $rr[prc]; ?>
	          </td>
	        </tr>
	        <?php
	              $rr = pg_fetch_array($qrySec);
	            }
	          } else {
	            ?>
				<div class="logo" style="position: absolute; top: 20%; left: 35%;">
					<img src="imgs/logoprosaude.png" width="430"/>
				</div>
	            <div style="position: absolute; top:50%; left:25%; text-align: center;">
	            <p style="font-weight: bold; font-size: 26px; color: #fff;">Desculpe! N&atildeo foi encontrado este usuario na lista de espera.<p>
	            </div>
	            <div style="position: absolute; top: 55%; left: 45%;">
	            <INPUT TYPE="button" onClick="history.go(0)" VALUE="VOLTAR" class="btn">
	            </div>
	            <?php
	          }
	        ?>
	    </table>
	    </tbody>
	</table>
</div>
<?php
}
?>
</body>
</html>
