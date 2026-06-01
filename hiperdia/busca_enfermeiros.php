<?php
	session_start();
    include_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
    include_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";

	if(strlen($palavra) > 0)
	{		
		if($acao == "buscar_enfermeiro")
		{
			$where = "where med_nome like upper('%$palavra%') ";
		}
		$sql = "select med.med_codigo,med_nome from medico as med
				  join medico_especialidade as mesp
				    on mesp.med_codigo = med.med_codigo
					$where
				";
	} else {
		exit( "Busca vazia !" );
	}
	$exec = pg_query($sql);

	$i = 0;
	echo "
		<tr>
			<th colspan='3'>
			FORAM ENCONTRADOS <font color='red'>".pg_num_rows($exec)."</font> REGISTROS COM '<font color='red'>".strtoupper($palavra)."</font>'
			</th>
		</tr>
		<tr>
			<th>Medico</th>
		</tr>";
	while($row = pg_fetch_array($exec))
	{
		$i++;
		
		echo "
			<tr id='tr$i' onmouseover=\"trocar_cor(this.id, 'fone$i');\" onmouseout=\"retirar_cor(this.id, 'fone$i')\"
				onclick=\"passar_enfermeiro('$row[med_nome]','$row[med_codigo]')\" style='cursor:pointer;'>
				<td>
					$row[med_nome]
				</td>
				<td>
					<img src='".$_SESSION[linkroot].$_SESSION[comum]."imgs/editar_on.jpg' onclick=\"window.open('../paciente.php?id_login=$id_login&acao=form_edit&usu_codigo=$row[usu_codigo]&controle=1',null,'height=460,width=800,status=yes,toolbar=no,menubar=no,location=no,scrollbars=yes');\"
				</td>
			</tr>";
		echo "
			<div id='fone$i' style='left:15%;width:235px;height:85px;background: 	#FFF8DC;position:absolute;display:none;border:1px solid blue;font-weight:bold;'>
			</div>";
	}
	echo $resp;
