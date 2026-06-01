<?php
	session_start(); 
?>
<table border="0" cellpadding=0 cellspacing=0 width="100%" class="b5">
  <tr>
    <td>

      <table border="0" cellpadding=0 cellspacing=0 width="100%" class="b5">
        <tr>
          <td>

            <table border="0" cellpadding=0 cellspacing=0 width="100%">
              <tr>
                <td width="65">Nome</td>

                <td>
                  <input type="text" name="cdParente" value="" class="inputForm" size="10" maxlength="10" readonly>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                  <input type="text" name="nmParente" value="" class="inputForm" size="50" maxlength="50" >
                  <a href="#" title="Pesquisar Parente"><img border="0" src="<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/buscar4_on.jpg" ></a>
                </td>
              </tr>
            </table>

            <table border="0" cellpadding=0 cellspacing=0 width="80%">
              <tr>
                <td width="2%">Parentesco</td>
                <!--
                
                -->
                <td width="80%">
                  <input type="text" name="parentesco" value="" class="inputForm" size="20" maxlength="20">
                </td>
			  </tr>
              <tr>
              	<td>Idade</td>
                <td>
                  <input type="text" name="idadeParente" value="" class="inputForm" size="10" maxlength="10">
                </td>
              </tr>
			  <tr>
              	<td>Sexo</td>
                <td>
                  <select name="sexoParente" class="inputForm">
                    <option value="XX" selected>-- Escolha o Sexo --</option>
                    <option value="MASCULINO">MASCULINO</option>
                    <option value="FEMININO">FEMININO</option>
                  </select>
                </td>
			 </tr>
             <tr>
             	<td>Resultado Exames</td>
                <td>
                  <select name="resultadoParente" class="inputForm">
                    <option value="XX" selected>-- RESULTADO --</option>
                    <option value="NEGATIVO">NEGATIVO</option>
                    <option value="POSITIVO">POSITIVO</option>
                  </select>
                </td>
              </tr>
            </table>

            <br>

            <center>
              <table border="0" cellpadding=0 cellspacing=0 width="100%" class="b5">
                <tr>
                  <td colspan="2" align="center">
                   <img src="<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/salvar_on.jpg" />
                   <img src="<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/apagar_on.jpg" />
                   <img src="<?= $_SESSION[linkroot].$_SESSION[comum];?>imgs/limpar_on.jpg" />
                  </td>
                </tr>
              </table>
            </center>

          </td>
        </tr>
      </table>

      <br>
<center>
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td>
              <div style="display: ; overflow: auto; height: 150px; width:750px; border-style: solid; border-width: 1px; scrollbar-face-color: #FFFFFF; scrollbar-track-color: #D0D0D0; scrollbar-arrow-color: #000080; background-color:#FFFFFF">
               
              </div>
             </td>
          </tr>
        </table>
</center>
    </td>
  </tr>
</table>




