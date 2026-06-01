<?php
include "../global.php";
$common = new commonClass();
$table = new tableClass();
$form = new classForm();

echo $common->incJquery();// inclui o Jquery, esse comando que ativa o javascript que faz funcionar o layout

echo $common->menuTab(array("Modelo Maldito","Ponei Maldito")); // Aqui voce coloca o nome das abas
	echo $common->bodyTab(1);// declara que isso é a primeira aba
	echo $common->divisoria("Teste");
		echo "
		<table width=100% border=0 class=lista>
			<tr>
				<td width=200 valign='top'>";
					echo $form->inputLabel("Visao geral");
		echo"			
				</td>
				<td>
					<p>Nesse teste faremos isso isso isso isso isso e isso</p>
					<p>Nesse teste faremos isso isso isso isso isso e isso</p>
					<p>Nesse teste faremos isso isso isso isso isso e isso</p>
					<p>Nesse teste faremos isso isso isso isso isso e isso</p>
					<p>Nesse teste faremos isso isso isso isso isso e isso</p>
					<p>Nesse teste faremos isso isso isso isso isso e isso</p>
					<p>Nesse teste faremos isso isso isso isso isso e isso</p>
					<p>Nesse teste faremos isso isso isso isso isso e isso</p>
				</td>
			</tr>
			<tr>
				<td width=200 valign='top'>";
					echo $form->inputLabel("Principais Campos");
		echo"			
				</td>
				<td>
					<p>Nesse teste faremos isso isso isso isso isso e isso</p>
					<p>Nesse teste faremos isso isso isso isso isso e isso</p>
					<p>Nesse teste faremos isso isso isso isso isso e isso</p>
					<p>Nesse teste faremos isso isso isso isso isso e isso</p>
					<p>Nesse teste faremos isso isso isso isso isso e isso</p>
					<p>Nesse teste faremos isso isso isso isso isso e isso</p>
					<p>Nesse teste faremos isso isso isso isso isso e isso</p>
					<p>Nesse teste faremos isso isso isso isso isso e isso</p>
				</td>
			</tr>
				<tr>
				<td width=200 valign='top'>";
					echo $form->inputLabel("Principais Campos");
		echo"			
				</td>
				<td>
					<p>Nesse teste faremos isso isso isso isso isso e isso</p>
					<p>Nesse teste faremos isso isso isso isso isso e isso</p>
					<p>Nesse teste faremos isso isso isso isso isso e isso</p>
					<p>Nesse teste faremos isso isso isso isso isso e isso</p>
					<p>Nesse teste faremos isso isso isso isso isso e isso</p>
					<p>Nesse teste faremos isso isso isso isso isso e isso</p>
					<p>Nesse teste faremos isso isso isso isso isso e isso</p>
					<p>Nesse teste faremos isso isso isso isso isso e isso</p>
				</td>
			</tr>
		</table>
		";
	echo $common->closeTab();//fecha a aba
	echo $common->bodyTab(2);//abre uma segunda aba
		echo "plapallpalpala";
	echo $common->closeTab();//fecha a aba
?>

