<form>
		<input type="hidden" name="comando" >
  <table>
	<tr>
		<td>
			Entreee ! 
		</td>
	</tr>
	<tr>
		<td>
			<input type="submit" value="enviar" name="teste">
		</td>
	</tr>
  </table>
</form>

<?php
$comando = $_REQUEST[comando];
	if($comando =! ""){
		exit();
	   include "form.php";	
	   
	}
?>