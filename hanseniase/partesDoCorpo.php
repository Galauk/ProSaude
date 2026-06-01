<?
	session_start();
?>
    <table border="0" cellpadding=0 cellspacing=0 width="100%" class="b5">
    	<tr>
    		<td>
    			<center>
    			<table border="0" cellpadding=0 cellspacing=0 width="100%" class="b5">
    				<tr>
    					<td width="2%">&nbsp;</td>
    					<td width="19%"><b>Observa&ccedil;&otilde;es Cl&iacute;nicas:</td>
    					<td>
    						<input type="text" name="obsClinica" value="" size="100" maxlength="100" class="inputForm" />
    					</td>
    				</tr>
    				<tr>
    					<td width="2%">&nbsp;</td>
    					<td width="19%"><b>In&iacute;cio da Doen&ccedil;a:</td>
    					<td>
    						<input type="text" name="inicioDoenca" value="" size="10" maxlength="10" class="inputForm">
    						<a href="#"><img src="<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/calendario_on.jpg" width="34" height="21" border="no" title='Calend&aacute;rio' align="absmiddle"></a>
    						&nbsp;&nbsp;&nbsp;
    						<b>Primeiros Sintomas:
    						&nbsp;
    						<input type="text" name="primeirosSintomas" value="" size="60" maxlength="60" class="inputForm" >
    					</td>
    				</tr>
    			</table>
    			<br>
    		</td>
    	</tr>
    </table>


	<table border="0" cellpadding=0 cellspacing=0 width="100%" class="b5">
    	<tr valign="top">
    		<td width="60%">
    			<center>
    			<table border="0" cellpadding=0 cellspacing=0 width="90%" class="b5">
    				<tr>
    					<td>
    						<br>
    						<b>Sintomatologia Atual: Les&otilde;es Cutâneas e Sintomas Neurais
    					</td>
    				</tr>
    				<tr>
    					<td bgcolor="#000000"></td>
    				</tr>
    				<tr>
    			</table>
				<br>
				<textarea cols="150" rows="5" name="sintomatologiaAtual" class="txArea"></textarea>
				<br><br>
    			</center>
    		</td>
    		<!--  antes o textarea acima estava com cols = 50 e rows = 8
    		<td>
    			<center>
    			<table border="0" cellpadding=0 cellspacing=0 width="90%" class="b5">
    				<tr>
    					<td>
    						<br>
    						Localização das Les&otilde;es:
    					</td>
    				</tr>
    				<tr>
    					<td bgcolor="#000000"></td>
    				</tr>
    				<tr>
    			</table>
    			<br>
    			<img src="x.gif" width="200" height="220">
    			</center>
    			<br>    			
    		</td>
    		 -->
    	</tr>
    </table>

    <table border="0" cellpadding=0 cellspacing=0 width="100%" class="b5">
    	<tr>
    		<td>
    			<center>
    			<table border="0" cellpadding=0 cellspacing=0 width="100%" class="b5">
    				<tr valign="top">
    					<td>
    					<br>
    					<center>
    						<img src="<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/adicionar_on.jpg" />
							<img src="<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/salvar_on.jpg" />
							<img src="<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/apagar_on.jpg" />
    					</center>
    					<br>
    					</td>
    				</tr>
    			</table>
    			</center>
    		</td>
    	</tr>
    </table>