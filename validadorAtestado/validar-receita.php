<?php 
	session_start();
    require_once 'superior.php';
    require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
   
    //echo $row."adasdasd";
    //while($res = pg_fetch_array($query)){
    	//echo "asdasdasd";
//	    $ate_codigo = $res[ate_codigo];
//	    $atest_codigo = $res[atest_codigo];
//	    $usu_codigo = $res[usu_codigo];
//	    //echo $atest_codigo."<br>";
//	    //echo $usu_codigo."<br>";    
//	    //echo $_GET[checar];
//	
//		list(,$usu_codigo2,$atest_codigo2) = explode("-",$_GET[checar]);
//	    if($usu_codigo == $usu_codigo2 && $atest_codigo == $atest_codigo2){
//	    	$testa = true;
//	    }else{
//	    	$testa = false;
//	    }
//	    echo $testa;
    //}
?>

<table width=713 cellspacing=0 cellpadding="0" border="0" align=center>
      <tr>
        <td align="center" height='40' bgcolor='#5069A6'>&nbsp;<font face='Verdana' color='#ffffff' size='4'><b>Validar Atestado</b></font></td>
        </tr>
		</table>
<form>
<table width=713 cellspacing=0 cellpadding="0" border="0" align=center style='border-left:1px dotted;border-right:1px dotted;border-bottom:1px dotted;border-color:#48A5D0'>
      <tr>
        <td>
   <?php 
   if ($_GET[checar] == ''){
   ?>
    <table width=713 cellspacing=8 cellpadding="0" border="0" align=center>
      <tr>
        <td align="right"><font face="Verdana" color="#5069A6">Codigo de validação:</font></td>
        <td align="left"><input type="text" name="checar"></td>
      </tr>
      <tr>
        <td align="center" colspan=2><input type="submit" value="Validar"></td>
      </tr>
	</table>    
    <?php 
   }else{
   	echo "<table width=713 cellspacing=8 cellpadding='0' border='0' align=center>
      <tr>
        <td align='center'>
        "; list(,$usu_codigo2,$atest_codigo2) = explode("-",$_GET[checar]);
    $sql =" SELECT a.ate_codigo,
    			   atest_codigo,
    			   u.usu_codigo 
			  FROM atestado a
			  JOIN atendimento ate
			    ON ate.ate_codigo = a.ate_codigo
			  JOIN usuario u
			    ON u.usu_codigo = ate.usu_codigo
			 WHERE atest_codigo = $atest_codigo2
			   AND u.usu_codigo = $usu_codigo2";
    $query = pg_query($sql);
    if(pg_num_rows($query) == 0){
    	echo "<p style=color:#F00>Código inválido.</p>";
    }else{
		echo "<iframe src='../zf/prontuario/atestado/imprimir/?atest=$atest_codigo2' width=700 height=550 border=0>
				
			</iframe>"; 
    }
    echo"       
        </td>
      </tr>
      <tr>
        <td align='center'><input type='button' value='voltar' OnClick=history.go(-1);></td>
      </tr>
	</table> ";
   }
    ?>    
        
        </td>
        </tr>
</table>
</form>

<?php 
    require_once 'inferior.php';
?>