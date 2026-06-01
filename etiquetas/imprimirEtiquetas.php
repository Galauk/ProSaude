<html>
   <head><title>jZebra Demo</title></head>
   <body>
   <script language="JavaScript" type="text/javascript" src="../ajax_motor.js"></script>
   <script type="text/javascript">
      function print(apelidos,usuario,data_coleta,data_nascimento,apelidos2) {
	         var applet = document.jZebra;

	         if (applet != null) {
	            // Send characters/raw commands to applet using "append"
	            // Hint:  Carriage Return = \r, New Line = \n, Escape Double Quotes= \"
	            //alert(applet);
	            applet.append("N\n");
				//applet.append("A590,1600,2,3,1,1,N,\"jZebra " + applet.getVersion() + " sample.html\"\n");
	            //applet.append("A590,1570,2,3,1,1,N,\"Testing the print() function\"\n");
				
				//applet.append("A467,103,2,2,1,1,N,\"CALCINHA DE RENDA\"\nA459,40,2,1,1,1,N,\"A Vista R$\"\n");
				
				applet.append("A650,230,2,3,1,1,N,\"Nome:{"+usuario+"}\"\n");
				applet.append("A650,200,2,3,1,1,N,\"Data Nasc.:{"+data_nascimento+"}\"\n");
				applet.append("A650,170,2,3,1,1,N,\"Data Coleta:{"+data_coleta+"}\"\n");				
				applet.append("A660,130,2,2,1,1,N,\"{"+apelidos+"}\"\n");
				if(apelidos2){
					applet.append("A660,110,2,2,1,1,N,\"{"+apelidos2+"}\"\n");
				}
	            applet.append("P1\n");
	            
	            // Send characters/raw commands to printer
	          
	            applet.print()
		 }
	 
         /**
           *  PHP PRINTING:
           *  // Uses the php `"echo"` function in conjunction with jZebra `"append"` function
           *  // This assumes you have already assigned a value to `"$commands"` with php
           *  document.jZebra.append(<?php echo $commands; ?>);
           */
           
         /**
           *  SPECIAL ASCII ENCODING
           *  //applet.setEncoding("UTF-8");
           *  applet.setEncoding("Cp1252"); 
           *  applet.append("\xDA");
           *  applet.append(String.fromCharCode(218));
           *  applet.append(chr(218));
           */
         
      }


   </script>
   <applet name="jZebra" code="jzebra.PrintApplet.class" archive="./jzebra.jar" width="100" height="100">
      <param name="printer" value="zebra">
      <!-- <param name="sleep" value="200"> -->
   </applet>
   <!-- <input type=button onClick="print()" value="Print"><br> -->
  	<? // 
  		include "../global.php";
  		include "../funcoes.inc.php";
  		$age_codigo = $_GET[age_codigo];
  		
  		$sqlGruposColeta = " SELECT distinct(g.gruex_codigo), 
									gruex_descricao 
							   FROM coleta AS c 
							   JOIN agenda_itens AS ai 
							     ON c.agei_codigo = ai.agei_codigo 
							   JOIN agenda AS a ON a.age_codigo = ai.age_codigo 
							   JOIN convenio_itens AS ci 
							     ON ci.coni_codigo = ai.coni_codigo 
							   JOIN grupoexame_procedimento AS gp 
							     ON gp.proc_codigo = ci.proc_codigo 
							   LEFT join grupoexame_procedimento AS gpr 
							     ON gpr.proc_codigo = ci.proc_codigo 
							   LEFT join grupoexame AS g 
							     ON g.gruex_codigo = gpr.gruex_codigo 
							  WHERE a.age_codigo = $age_codigo 
							  ORDER by gruex_descricao ";
  		$queryGruposColeta = pg_query($sqlGruposColeta);
  		$val = 0;
  		$i = 0;
  		while($registro = pg_fetch_array($queryGruposColeta)){
  			$grupo = $registro[gruex_descricao];
  			$sqlExamesColeta = "SELECT proc_sis_nome_generico,
								       proc_nome,
								       usu_nome,
								       to_char(usu_datanasc,'DD/MM/YYYY') as data_nascimento,
								       to_char(col_data_coleta,'DD/MM/YYYY') as data_coleta
								  FROM coleta AS c
								  JOIN agenda_itens AS ai
								    ON c.agei_codigo = ai.agei_codigo
								  JOIN agenda AS a
								    ON a.age_codigo = ai.age_codigo
								  JOIN convenio_itens AS ci
								    ON ci.coni_codigo = ai.coni_codigo
								  JOIN grupoexame_procedimento AS gp
								    ON gp.proc_codigo = ci.proc_codigo
								  JOIN procedimento AS p
								    ON p.proc_codigo = gp.proc_codigo
								  LEFT join procedimentos_sisprenatal AS ps
								    ON ps.proc_codigo = p.proc_codigo
								  LEFT join grupoexame_procedimento AS gpr
								    ON gpr.proc_codigo = p.proc_codigo
								  LEFT join grupoexame AS g
								    ON g.gruex_codigo = gpr.gruex_codigo
								  JOIN usuario as u
    								ON u.usu_codigo = a.usu_codigo
								 WHERE a.age_codigo = $age_codigo
								   AND g.gruex_codigo = $registro[gruex_codigo]
								 ORDER by gruex_descricao";
  			$queryExamesColeta = pg_query($sqlExamesColeta);
  			while($regExames = pg_fetch_array($queryExamesColeta)){
  				//echo $quantidade."<br/>";
				$quantidade += strlen($regExames[proc_sis_nome_generico]."|");
  				if($quantidade > 35){
					$apelidos2 .= $regExames[proc_sis_nome_generico]."|"; 
				}else{
					$apelidos .= $regExames[proc_sis_nome_generico]."|";
				}
				$nome = $regExames[usu_nome];
				$data_nascimento = $regExames[data_nascimento];
				$data_coleta = $regExames[data_coleta];
				
  			}
  			
			//echo $quantidade;
			echo "<br/>".$apelidos;
			//echo "<br/>".$apelidos.$quantidade;
  			$nome = abrevianome($nome,24);
  			echo "aok";
  			echo "<script>print('$apelidos','$nome','$data_coleta','$data_nascimento','$apelidos2');</script>";
  			$apelidos = "";
  			$quantidade = 0;
  			$apelidos2 = "";
  		}
  	?>
   </body>
</html>