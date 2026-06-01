<script>
function changeLocation(menuObj)
{
   var i = menuObj.selectedIndex;

   if(i > 0)
   {
      window.location = menuObj.options[i].value;
   }
}
</script>
<?
//------------------------------------------------------------------>
// -> Inclusao principal para montagem do sistema
//------------------------------------------------------------------>
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
	cabecario();
//------------------------------------------------------------------>


//------------------------------------------------------------------>
//-> Secao Vazia, mostrando registros e botoes
//------------------------------------------------------------------>

 if(empty($acao)) {

//
//-> Botoes
  echo "<table width=98% align=center cellspacing=0 cellpadding=0 border=0>
         <tr>
          <td>
           <fieldset>
            <legend>Opções</legend>
             <table width=100% align=center cellspacing=3 cellpadding=0 border=0>
              <tr>
               <td width=200><a href=$PHP_SELF?acao=form_add><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/adicionar_on.jpg border=0></a></td>
               <form method=post action=$PHP_SELF>
                <input type=hidden name=acao value=busca>
               <td width=30>Buscar:</td>
               <td width=120><input type=text name=palavra_chave class=box onBlur=\"javascript:this.value=this.value.toUpperCase();\"></td>
               <td><input type=image src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/procurar_on.jpg></td></form>
               <td width=107><img src=".$_SESSION[linkroot].$_SESSION[comum]."imgs/sair.gif></td>
              </tr>
             </table>
           </fieldset>
          </td>
         </tr>
        </table><br>";

//
//-> Listando
}


