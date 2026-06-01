 <?php
// phpinfo();
 // die();
 error_reporting(E_ALL);
include "../global.php";
include "../../WebSocialComum/library/php/funcoes.db.php";
set_time_limit(0);

 	pg_query("SET CLIENT_ENCODING=UTF8");

if($_REQUEST['acao']=="") {

 	 $sql = "SELECT pro_nome,pro.pro_codigo,sum(ite_vlrunit)as ite_vlrunit from itens_movimento as ite
				join produto as pro on pro.pro_codigo=ite.pro_codigo
				where vlr_unitario = '0.0000' or ite_vlrunit > '5.0000' or vlr_unitario is null
				group by pro_nome,pro.pro_codigo
				order by pro_nome
			";
	$query = pg_query($sql) or die(pg_last_error());
	echo "<form method=post action=produto_subgrupo.php>
	    <input type=hidden name='acao' value='ok'>
		<table border=1>";
		$k=0;
	while($rr = pg_fetch_array($query)){
		$k++;
	echo "<tr>
			<td>$k - $rr[pro_nome] - </td><input type=hidden name='pro_codigo[]' value='".$rr[pro_codigo]."'>
			<td width='400'>Antigo: R$ $rr[ite_vlrunit] QTD:<input type=text name=qtd[]></td>
			<td width='200'>R$ <input type=text name=vlr[]></td>

		  </tr>";
	}
    echo "  <tr>
                <td COLSPAN=3 align=center><BR><input type=submit value='ATUALIZAR OS DADOS DO PRODUTO (VAI PLANETA !!'>
                    <br>
                </td>
            </tr>
	</table>
	</form>";
	} else {
		$teste = $_REQUEST['vlr'];
        
        // echo "<pre>";print_r($teste);die();

	    $vlr = ($_REQUEST['vlr']=="")?"1":$_REQUEST['vlr'];

        // echo "<pre>";print_r($vlr);die();

	    $pro_codigo = ($_REQUEST['pro_codigo']=="")?"0.00":$_REQUEST['pro_codigo'];
        
        // echo "<pre>";print_r($pro_codigo);die();

	    $qtd = ($_REQUEST['qtd']=="")?"1":$_REQUEST['qtd'];

	    for($i = 0; $i <= count($vlr) ; $i++) {	
            
            $valor = ($vlr[$i]/$qtd[$i]);


            if(!empty($vlr[$i])) {
                $sq = pg_query(
                        "UPDATE itens_movimento set  vlr_unitario='".$valor."' where pro_codigo=".$pro_codigo[$i]
                    ) or die(pg_last_error());
                    if($sq) {
                        echo "FOI TUDO CERTO";
                    } else {
                         echo "ALGO ERRADO E AGORA?"; 
                    }
            }
	    }
	}
?>