<?
echo"
<table border='0' cellpadding=0 cellspacing=0 width='100%' class='b5'>
    <tr>
      <td>
      	<table border='0' cellpadding=0 cellspacing=0 width='100%' class='b5'>
            <tr>
              <td width='27px'>&nbsp;</td>
              <td width='155px'><b>Tipo de Entrada</td>
              <td>
                <select name='tipoEntrada' class='inputForm'>
                  <option value='' selected>Selecione uma Entrada</option>
                  <option value='caso novo'>Caso Novo</option>
                  <option value='reincidencia'>Reincid&ecirc;ncia</option>
                  <option value='reingresso apos abandono'>Reingresso ap&oacute;s Abandono</option>
                  <option value='nao sabe'>N&atilde;o Sabe</option>
                  <option value='transferencia'>Transfer&ecirc;ncia</option>
                </select>
              </td>
            </tr>
      	</table>
       </td>
     </tr>
</table>
<table border='0' cellpadding=0 cellspacing=0 width='100%' class='b5'>
    <tr>
      <td>
        <table border='0' cellpadding=0 cellspacing=0 width='100%' class='b5'>
          <tr>
            <td width='2%'>&nbsp;</td>
            <td width='10%'><b>Raio-X do Torax</td>
            <td width='30%'>
              <select name='raioXTorax' class='inputForm'>
                <option value='' selected>Selecione uma Op&ccedil;&atilde;o</option>
                <option value='suspeito'>Suspeito</option>
                <option value='normal'>Normal</option>
                <option value='outra patologia'>Outra Patologia</option>
                <option value='nao realizado'>N&atilde;o Realizado</option>
              </select>
            </td>

            <td width='2%'>&nbsp;</td>
            <td width='10%'><b>Teste Tubercul&iacute;nico</td>
            <td width='30%'>
              <select name='testeTuberculinico' class='inputForm'>
                <option value='' selected>Selecione uma Op&ccedil;&atilde;o</option>
                <option value='nao reator'>N&atilde;o Reator</option>
                <option value='reator fraco'>Reator Fraco</option>
                <option value='reator forte'>Reator Forte</option>
                <option value='naorealizado'>N&atilde;o Realizado</option>
              </select>
            </td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td><b>Forma</td>
            <td>
              <select name='forma' class='inputForm'>
                <option value='' selected> Selecione uma Op&ccedil;&atilde;o </option>
                <option value='pulmonar'>Pulmonar</option>
                <option value='extrapulmonar'>Extrapulmonar</option>
                <option value='pulmonar+extrapulmonar'>Pulmonar + Extrapulmonar</option>
              </select>
             </td>
           <td>&nbsp;</td>
           <td><b>Extrapulmonar</td>
           <td>
              <select name='extrapulmonar' class='inputForm'>
                <option value='' selected>Selecione uma Op&ccedil;&atilde;o </option>
                <option value='pleural'>Pleural</option>
                <option value='gang.perif'>Gang. Perif.</option>
                <option value='geniturinaria'>GenitUn&aacute;ria</option>
                <option value='ossea'>&Oacute;ssea</option>
                <option value='ocular'>Ocular</option>
                <option value='miliar'>Miliar</option>
                <option value='meningite'>Meningite</option>
                <option value='outras'>Outras</option>
                <option value='nao se explica'>N&atilde;o se Explica</option>
              </select>
            </td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td><b>Agravos</td>
            <td>
              <select name='agravos' class='inputForm'>
                <option value='' selected>Ignorado</option>
                <option value='aids'>AIDS</option>
                <option value='alcoolismo'>Alcoolismo</option>
                <option value='doenca mental'>Doen&ccedil;a Mental</option>
                <option value='diabetes'>Diabetes </option>
                <option value='tuberculose'>Tuberculose </option>
                <option value='outros'>Outros</option>
              </select>
            </td>
          </tr>
        </table>
      </td>
    </tr>
</table>
</td>
</tr>
</table>

<table border='0' cellpadding=0 cellspacing=0 width='100%' class='b5'>
<tr>
<td>
  <table border='0' cellpadding=0 cellspacing=0 width='100%' class='b5'>
    <tr>
      <td width='2%'>&nbsp;</td>
      <td width='10%'><b>Baciloscopia de Escarro</td>
      <td width='30%'>
        <select name='bacEscarro' class='inputForm'>
          <option value='nao realizada' selected>N&atilde;o Realizada</option>
          <option value='positiva'>Positiva</option>
          <option value='negativa'>Negativa</option>
        </select>
      </td>

      <td width='2%'>&nbsp;</td>
      <td width='10%'><b>Baciloscopia de Outro Material</td>
      <td width='30%'>
        <select name='bacOutroMaterial' class='inputForm'>
          <option value='' selected>N&atilde;o Realizada</option>
          <option value='positiva'>Positiva</option>
          <option value='negativa'>Negativa</option>
        </select>
      </td>
    </tr>

    <tr>
      <td>&nbsp;</td>
      <td><b>Cultura de Escarro</td>
      <td>
        <select name='culturaEscarro' class='inputForm'>
          <option value='' selected>N&atilde;o Realizada</option>
          <option value='positiva'>Positiva</option>
          <option value='negativa'>Negativa</option>
          <option value='em andamento'>Em Andamento</option>
        </select>
      </td>

      <td>&nbsp;</td>
      <td><b>Cultura de Outro Material</td>
      <td>
        <select name='culturaOutroMaterial' class='inputForm'>
          <option value='' selected>N&atilde;o Realizada</option>
          <option value='positiva'>Positiva</option>
          <option value='negativa'>Negativa</option>
          <option value='em andamento'>Em Andamento</option>
        </select>
      </td>
    </tr>

    <tr>
      <td>&nbsp;</td>
      <td><b>HIV</td>
      <td>
        <select name='hiv' class='inputForm'>
          <option value='' selected>N&atilde;o Realizada</option>
          <option value='positiva'>Positiva</option>
          <option value='negativa'>Negativa</option>
          <option value='em andamento'>Em Andamento</option>
        </select>
      </td>

      <td>&nbsp;</td>
      <td><b>Histopatologia</td>
      <td>
        <select name='histopatologia' class='inputForm'>
          <option value='' selected>N&atilde;o Realizada</option>
          <option value='baar positiva'>Baar Positiva</option>
          <option value='sugestivo de TB'>Sugestivo de TB</option>
          <option value='nao sugestiva de tb'>N&atilde;o Sugestiva de TB</option>
          <option value='em andamento'>Em Andamento</option>
        </select>
      </td>
    </tr>
</table>
</td>
</tr>
</table>
";
?>