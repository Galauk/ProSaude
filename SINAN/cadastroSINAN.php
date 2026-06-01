  <table border="1" cellpadding=0 cellspacing=0 width="100%" class="b5">
    <tr>
      <td>

        <table border="0" cellpadding=0 cellspacing=0 width="100%">
          <tr>
            <td width="2%">&nbsp;</td>
            <td width="19%">Municipio de Notifica&ccedil;&atilde;o</td>
            <td width="75%">
              <input type="text" name="cdIbgeMunicipio" value="" size="10" maxlength="10" class="txReadOnly" readonly>
              <input type="text" name="nmMunicipio" value="" size="70" maxlength="70" class="txReadOnly" readonly>
            </td>
              </tr>

          <tr>
            <td width="2%">&nbsp;</td>
            <td width="19%">Unidade de Sa&uacute;de:</td>
            <td>
              <input type="text" name="cdUnidadeSaude" value="" size="10" maxlength="10" class="txReadOnly" readonly>
              <input type="text" name="nmUnidadeSaude" value="" size="70" maxlength="70" class="txReadOnly" readonly>
            </td>
          </tr>

          <tr>
                <td width="2%">&nbsp;</td>
            <td width="19%">Nome do Atendente</td>
            <td>
              <input type="text" name="cdUsuario" value="" size="10" maxlength="10"class="txReadOnly" readonly size="50">
              <input type="text" name="nmUsuario" value="" size="70" maxlength="70"class="txReadOnly" readonly size="50">
            </td>
          </tr>
        <table>

        <br>

        <table border="0" cellpadding=0 cellspacing=0 width="100%">
          <tr>
            <td width="21%">&nbsp;</td>
            <td width="25%">Data Abertura</td>
            <td>Hora Abertura</td>
          </tr>
          <tr>
            <td width="21%">&nbsp;</td>
            <td width="15%"><input type="text" name="dtAbertura" value="" class="txReadOnly" size="15" maxlength="15" readOnly></td>
            <td><input type="text" name="hrAbertura" value="" class="txReadOnly" maxlength="15" readonly size="15"></td>

          </tr>
        </table>

      </td>
    </tr>
  </table>

<table border="1" cellpadding=0 cellspacing=0 width="100%" class="b5">
  <tr>
    <td>
    <center>

      <table border="0" cellpadding=0 cellspacing=0 width="100%">
        <tr>
          <td width="30%">&nbsp;</td>
          <td width="25%">Valor Inicial</td>
          <td>Valor Final</td>
        </tr>

        </tr>
        <tr>
          <td width="30%">&nbsp;</td>
          <td width="25%"><input type="text" name="valorInicial" value="" class="txOthers" size="15" maxlength="15" ></td>
          <td><input type="text" name="valorFinal" value="" class="txOthers" maxlength="15"  size="15" ></td>
        </tr>
      </table>
    </center>

    <br>

    <center>
      <table border="0" cellpadding=0 cellspacing=0 width="100%">
        <tr>
          <td colspan="2" align="center">
            <input name="btnSalvar" type="button" style="border: 1px solid #808080; background-color: #FFFFFF; width:100; height:20" title="Salvar" onClick='save();' value='Salvar'>

            <input name="btnRemover" type="button" style="border: 1px solid #808080; background-color: #FFFFFF; width:100; height:20" title="Remover" onClick='remove();' value='Remover'>

            <input name="btnLimpar" type="button" style="border: 1px solid #808080; background-color: #FFFFFF; width:100; height:20" title="Limpar" onClick='inferno();' value='Limpar'>
          </td>
        </tr>
      </table>
    </center>

    <table border="0" cellpadding=0 cellspacing=0 width="80%">
      <tr>
        <td width="5%">
          <strong>EM USO / EM ESPERA</strong>
        </td>
      </tr>
      <tr>
        <td bgcolor="#000000"></td>
      </tr>
    </table>

    <br>

    <center>
      <table border="0" cellpadding=0 cellspacing=0 width="90%">
        <tr>
          <td>
            <div style="display: ; overflow: auto; height: 200px; width:750px; border-style: solid; border-width: 1px; scrollbar-face-color: #FFFFFF; scrollbar-track-color: #D0D0D0; scrollbar-arrow-color: #000080; background-color:#FFFFFF">
             
            </div>
          </td>
        </tr>
      </table>
    </center>

   </td>
  </tr>
</table>
