<?php 
	session_start();
?>
<table border="1" cellpadding=0 cellspacing=0 width="100%" class="b5">
  <tr>
    <td>

      <center>
        <table border="0" cellpadding=0 cellspacing=0 width="70%" >
          <tr>
            <td>
              <br>
             <b> Controle Tratamento PQT:
            </td>
          </tr>
          <tr>
            <td bgcolor="#000000"></td>
          </tr>
        </table>

      <br>

      <table border="0" cellpadding=0 cellspacing=0 width="70%">
        <tr>
          <td width="2%">&nbsp;</td>
          <td><b>Atendente</td>
        </tr>
        <tr>
          <td width="2%">&nbsp;</td>
          <td>
            <input type="text" name="controleCdAtendente" value="" class="inputForm" size="10" maxlength="10" >
            &nbsp;
            <input type="text" name="controleNmAtendente" value="" class="inputForm" size="50" maxlength="50" >
          </td>
        </tr>
      </table>
     </center>

      <br>

        <table border="0" cellpadding=0 cellspacing=0 width="100%">
          <tr>
           <td colspan="2">
             <center>
              	<img src="<?= $_SESSION[linkroot].$_SESSION[comum]; ?>imgs/salvar_on.jpg" />
             </center>
           </td>
         </tr>
       </table>

      <br>

      <table border="0" cellpadding=0 cellspacing=0 width="100%">
        <tr>
          <td colspan="2">
            <center>
              <div id="controlePQT" style="overflow: auto; height: 150px; width:550px; border-style: solid; border-width: 1px; scrollbar-face-color: #FFFFFF; scrollbar-track-color: #D0D0D0; scrollbar-arrow-color: #000080; background-color: #ffffff" >
                <center>
                 
                </center>
              </div>
            </center>
          </td>
        </tr>
      </table>

    </td>
  </tr>
</table>