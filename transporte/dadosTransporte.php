<link rel="stylesheet" href="/WebSocialSaude/lib/themes/base/jquery.ui.all.css">
<link rel="stylesheet" href="/WebSocialSaude/lib/themes/ui-lightness/jquery-ui-1.8.10.custom.css">
<script type="text/javascript" src="/WebSocialComum/library/js/jquery-1.6.2.min.js"></script>
<script type="text/javascript" src="/WebSocialSaude/lib/ui/jquery-ui-1.8.16.custom.min.js"></script>

<?
/**
 * @version Anderson 14/04/2011
 * @author Anderson <anderson@elotech.com.br>
 *
*/

	$common = new commonClass();
	$form = new classForm();
	$table = new tableClass();

if(empty($acao)) {
	echo "<table>
			<tr>
			  <td>";
	echo $common->commonButton("Adicionar Roteiros","viagem.php","viagem.png",NULL);
	echo "</td>
			<td>";	
	echo $common->commonButton("Adicionar Veiculos","veiculo.php","carro.png",NULL);
	echo "</td>
		  </tr>
		</table>";
}
		
if($acao=="addviagem") {
	echo $form->inputText("usu_rg_compl", $row['usu_rg_compl'], "Estado de Origem", 20, 10, null, "text", "N", "S");
	echo $form->inputText("usu_rg_compl", $row['usu_rg_compl'], "Municipio de Origem", 20, 10, null, "text", "N", "S");
	echo $form->inputText("usu_rg_compl", $row['usu_rg_compl'], "Data Viagem", 20, 10, null, "text", "N", "S");
	echo $form->inputText("usu_rg_compl", $row['usu_rg_compl'], "Data do Retorno", 20, 10, null, "text", "N", "S");
	echo $form->inputText("usu_rg_compl", $row['usu_rg_compl'], "Estado de Destino", 20, 10, null, "text", "N", "S");
	echo $form->inputText("usu_rg_compl", $row['usu_rg_compl'], "Municipio de Destino", 20, 10, null, "text", "N", "S");
	echo $form->inputText("usu_rg_compl", $row['usu_rg_compl'], "Hora de Saida", 20, 10, null, "text", "N", "S");
	echo $form->inputText("usu_rg_compl", $row['usu_rg_compl'], "Hora de Chegada", 20, 10, null, "text", "N", "S");
	echo $form->inputText("usu_rg_compl", $row['usu_rg_compl'], "Km Inicial", 20, 10, null, "text", "N", "S");
	echo $form->inputText("usu_rg_compl", $row['usu_rg_compl'], "Km Final", 20, 10, null, "text", "N", "S");
	echo $form->inputText("usu_rg_compl", $row['usu_rg_compl'], "Motivo", 20, 10, null, "text", "N", "S");
	echo $form->inputText("usu_rg_compl", $row['usu_rg_compl'], "Custo Medio", 20, 10, null, "text", "N", "S");		
	echo $form->textArea("usu_rg_compl", $row['usu_rg_compl'], "Observacao", 20, 10, null, "text", "N", "S");

	echo "<table>
			<tr>
			  <td>";
	echo $common->commonButton("Voltar", $PHP_SELF, "voltar.png", null);
	echo "</td>
			<td>";	
	echo $common->commonButton("Salvar","javascript:void(0);","salvar.gif",NULL);
	echo "</td>
		  </tr>
		</table>";
}

if($acao=="addveiculo") {
	echo $form->inputText("usu_rg_compl", $row['usu_rg_compl'], "Capacidade Maxima", 20, 10, null, "text", "N", "S");
	echo $form->inputText("usu_rg_compl", $row['usu_rg_compl'], "Marca", 20, 10, null, "text", "N", "S");
	echo $form->inputText("usu_rg_compl", $row['usu_rg_compl'], "Modelo", 20, 10, null, "text", "N", "S");
	echo $form->inputText("usu_rg_compl", $row['usu_rg_compl'], "Ano", 20, 10, null, "text", "N", "S");
	echo $form->inputText("usu_rg_compl", $row['usu_rg_compl'], "Km", 20, 10, null, "text", "N", "S");
	echo $form->inputText("usu_rg_compl", $row['usu_rg_compl'], "Estado de Conservacao", 20, 10, null, "text", "N", "S");
	echo $form->inputText("usu_rg_compl", $row['usu_rg_compl'], "Combustivel", 20, 10, null, "text", "N", "S");
	echo $form->inputText("usu_rg_compl", $row['usu_rg_compl'], "Media de Combustivel", 20, 10, null, "text", "N", "S");
	echo $form->textArea("usu_rg_compl", $row['usu_rg_compl'], "Observacao", 20, 10, null, "text", "N", "S");


	echo "<table>
			<tr>
			  <td>";
	echo $common->commonButton("Voltar", $PHP_SELF, "voltar.png", null);
	echo "</td>
			<td>";	
	echo $common->commonButton("Salvar","javascript:void(0);","salvar.gif",NULL);
	echo "</td>
		  </tr>
		</table>";
}

?>



