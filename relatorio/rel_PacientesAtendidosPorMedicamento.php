<?php
	session_start();
	require_once $_SESSION[root].$_SESSION[comum]."library/php/db.inc.php";
	require_once $_SESSION[root].$_SESSION[comum]."library/php/funcoes.inc.php";
    
$estocador  = (int) $_GET[estocador];
$data_ini   =$_GET[data_ini];
$data_fim   =$_GET[data_fim];
$pro_cod    = (int) $_GET[pro_cod];
$Tit = "Numero Pacientes Atendidos por Medicamento";
$dtIni=$data_ini;
$dtFin=$data_fim;
$bg_color='#ffffff';
$coluna1="<table><tr><td width='45%'><strong>Produto</strong></td>";    
$coluna2="<td width='50%'><strong>Centro Estocador</strong></td>";
$coluna3="<td width='5%'><strong>Pacientes Atendidos</strong></td></tr>";
$total=0;
$subtotal=0;


list($dia,$mes,$ano) = split ('/',$data_ini);
$data_ini = date("Y-m-d",mktime(0,0,0,$mes,$dia,$ano));
list($dia,$mes,$ano) = split ('/',$data_fim);
$data_fim = date("Y-m-d",mktime(0,0,0,$mes,$dia,$ano));



    if ($estocador == -1)
    {
        $coluna1.=$coluna2.$coluna3;        
        $CE = "TODOS";
    }
    else
    {
        $t =pg_fetch_array(db_query("select set_nome from setor where set_codigo = $estocador"));
        $CE = "<b>".$t[set_nome]."</b>";
        $coluna1.=$coluna3;
    }
        
    
    include_once $_SESSION[root].$_SESSION[modulo]."relatorio/cabecalho.php";

    $select = "select count(usu_codigo) as qtde, pro_codigo as codigo,mov_tipo, tipomovim, sinal,codsetor,pro_nome,setor from v_movimentacao";
    $where  = " where usu_codigo is not null";
    $group  = " group by codigo,mov_tipo, tipomovim, sinal,codsetor,pro_nome,setor";
    $orderby= " order by codsetor,pro_nome";
    $and1   = " and mov_data between '$data_ini' and '$data_fim'";
    $and2   = " and pro_codigo = ";
    $and3   = " and codsetor = ";
    
    
    echo "<style type='text/css'>
        tr{
        font-size   :12px;
        }
        </style>";

    echo $coluna1."<tr><td colspan='4'><hr></td></tr>"; // mostra linha com cabecalho.   

    if ($estocador == -1 && $pro_cod == -1)
    {
        $select.=$where.$and1.$group.$orderby;
        //die($select);
		$query1 = pg_query($select);
        $setor_inicial = 0;        
        while ($reg1 = pg_fetch_array($query1))
        {
            if (pg_num_rows($query1)>2)
            {
                $bg_color = ($bg_color=='#A6A6A6') ? '' : '#A6A6A6';
            }
            if ($setor_inicial == $reg1[codsetor] || $setor_inicial == 0)
            {
                $subtotal+=$reg1[qtde];
            }
            else if($subtotal > 0)
            {
                echo "<tr><td colspan ='4'><hr></td></tr>";                
                echo "<tr><td colspan ='4'><strong>Total de pacientes atendidos no centro estocador= ".$subtotal."</strong></td></tr>";
                echo "<tr><td colspan ='4'><hr></td></tr>";
                $subtotal=0;
                $setor_inicial=$reg1[codsetor];
                $subtotal+=$reg1[qtde];                
            }       
            echo "<tr bgcolor='$bg_color'><td>$reg1[pro_nome]</td><td>$reg1[setor]</td><td>$reg1[qtde]</td><tr>";

            $total+=$reg1[qtde];
        }
        if ($subtotal > 0)
        {        
            echo "<tr><td colspan ='4'><hr></td></tr>";                
            echo "<tr><td colspan ='4'><strong>Total de pacientes atendidos no centro estocador= ".$subtotal."</strong></td></tr>";
            echo "<tr><td colspan ='4'><hr></td></tr>";
        }
        echo "<tr><td colspan ='4'><strong>Total de pacientes atendidos = ".$total."</strong></td></tr>";
        echo "</table>";
    }
    else if ($estocador == -1 && $pro_cod != -1)
    {
        $select.=$where.$and1.$and2.$pro_cod.$group.$orderby;
        //die($select);
		$query2 = db_query($select);
        $setor_inicial=0;
        while ($reg_2 = pg_fetch_array($query2))
        {
            if (pg_num_rows($query2)>2)
            {
                $bg_color = ($bg_color=='#A6A6A6') ? '' : '#A6A6A6';
            }
            if ($setor_inicial == $reg_2[codsetor] || $setor_inicial == 0)
            {
                $subtotal+=$reg_2[qtde];
            }
            else if ($subtotal>0)
            {
                echo "<tr><td colspan ='4'><hr></td></tr>";                
                echo "<tr><td colspan ='4'><strong>Total de pacientes atendidos no centro estocador= ".$subtotal."</strong></td></tr>";
                echo "<tr><td colspan ='4'><hr></td></tr>";
                $subtotal=0;
                $subtotal+=$reg_2[qtde];                
            }
            $teste++;
            echo "<tr bgcolor='$bg_color'><td>$reg_2[pro_nome]</td><td>$reg_2[setor]</td><td>$reg_2[qtde]</td></tr>";
            $total+=$reg_2[qtde];
            $setor_inicial=$reg_2[codsetor];            
        }
        if ($subtotal > 0)
        {
            echo "<tr><td colspan ='4'><hr></td></tr>";
            echo "<tr><td colspan ='4'><strong>Total de pacientes atendidos no centro estocador= ".$subtotal."</strong></td></tr>";
            echo "<tr><td colspan ='4'><hr></td></tr>";
        }
        echo "<tr><td colspan ='4'><strong>Total de pacientes atendidos = ".$total."</strong></td></tr>";
        echo "</table>";
    }
    else if ($estocador != -1 && $pro_cod == -1)
    {
        $select.=$where.$and1.$and3.$estocador.$group.$orderby;
        //die($select);
		$query3 = db_query($select);       
        while($reg_3 = pg_fetch_array($query3))
        {
            $bg_color = ($bg_color=='#A6A6A6') ? '' : '#A6A6A6';
            echo "<tr bgcolor='$bg_color'><td>$reg_3[pro_nome]</td><td>$reg_3[qtde]</td><tr>";            
            $total+=$reg_3[qtde];
        }
        echo "<tr><td colspan ='4'><strong>Total de pacientes atendidos = ".$total."</strong></td></tr>";
        echo "</table>";
        
    }
    else if ($estocador != -1 && $pro_cod != -1)
    {
        $select.=$where.$and1.$and2.$pro_cod.$and3.$estocador.$group.$orderby;
        //die($select);
		$query4 = db_query($select);
        while($reg_4 = pg_fetch_array($query4))
        {
            $bg_color = ($bg_color=='#A6A6A6') ? '' : '#A6A6A6';            
            echo "<tr bg_color='$bg_color'><td>$reg_4[pro_nome]</td><td>$reg_4[qtde]</td><tr>";                     
            $total+=$reg_4[qtde];
        }
        echo "<tr><td colspan ='4'><strong>Total de pacientes atendidos = ".$total."</strong></td></tr>";
        echo "</table>";
    }
?>