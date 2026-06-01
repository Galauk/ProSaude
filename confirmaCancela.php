<html>
<head>
<link href="tabela.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="view.css" media="all">
<script type="text/javascript" src="view.js"></script>
<script type="text/javascript" src="calendar.js"></script>
<script>
function pegaDadosColuna()
{
	id = document.getElementById('id').value;
	window.opener.deletaVacina(id);
	window.close();		
}
</script>
</head>
<body>
<?
session_start();
$id = $_GET[id];
echo "
<form onSubmit='pegaDadosColuna()'> 
	<input type='hidden' name='id' id ='id' value='$id'>
	<table>
		<tr>
			<td width=50>
				<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/atencao.png'/>
			</td>
			<td class='formataCancela'>
				<b>Deseja Excluir essa Vacina?</b>
			</td>
		</tr>
		<tr>
			<td align='center' colspan='2'>
				<input type='image' alt='Enviar' src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/excluir_on.jpg'>
			</td>
		</tr>
	</table>
</form>
";
?>
 </body>
 </html>