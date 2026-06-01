<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
include_once('../class/tcpdf/tcpdf.php');
include_once("../class/PHPJasperXML.inc.php");
include_once ('../setting.php');
include_once("../parametros_db.php");

$server = $host;
$user = $usuario;
$pass = $senha;
$db = $banco;
$port = $porta;

$usr_codigo = $_POST["usr_codigo"];
$data_inicial = $_POST["data_inicial"];
$data_final = $_POST["data_final"];
$usu_codigo = $_POST["usu_codigo"];

$where = "";
if($usr_codigo)
	$where = "AND usr_codigo = $usr_codigo";
	
if($data_inicial)
	$where .= "AND dt_requisicao >= '$data_inicial'";

if($data_final)
	$where .= "AND dt_requisicao <= '$data_final'";
	
$sql = "select p.proc_codigo,
					   (CASE WHEN proc_apelido is NULL THEN PROC_NOME ELSE proc_apelido END) as proc_nome,
					   usr_nome,
					   count(*)
				  from requisicao_exames re
				  join procedimento p
					on p.proc_codigo=re.proc_codigo
				  join atendimento ate
					on re.ate_codigo=ate.ate_codigo
				  join agendamento ag
					on ag.age_codigo=ate.age_codigo
				  join usuarios usr
					on ag.med_codigo=usr.usr_codigo
				 where 1=1
			    $where
				 group by p.proc_codigo,p.proc_nome,usr_nome
				 order by usr_nome,count desc";

$PHPJasperXML = new PHPJasperXML();
$PHPJasperXML->connect($server,$user,$pass,$db,$port,$cndriver="psql"); 
//echo $_SESSION[id_login];
//die($id_login);
$sqlDados = "select uni_desc,
					'CNPJ: ' || uni_cnpj as uni_cnpj,
					uni_endereco,
					uni_numero,
					uni_cep
			   from logon l
			   join unidade u
			     on u.uni_codigo=l.uni_codigo
			   join cidade c
			     on c.cid_codigo_ibge=u.uni_codigo_ibge
			  where id_login = $_SESSION[id_login]";
 //die($sqlDados);
$queryDados = pg_query($sqlDados);
$reg_dados = pg_fetch_array($queryDados);
$caminho = "../../zf/public/images/brasao.jpg";

$sqlSecretaria = "select *,endereco_secretaria || ', ' || numero_end_secretaria || ', ' || cnpj_secretaria || ', ' || telefone_secretaria as rodape from secretaria";
$querySecretaria = pg_query($sqlSecretaria);
$regSecretaria = pg_fetch_array($querySecretaria);
//$PHPJasperXML->debugsql=true;
$PHPJasperXML->arrayParameter=array("uni_desc"=>$reg_dados[uni_desc],
								    "uni_cnpj"=>$reg_dados[uni_cnpj],
									"caminho_img"=>$caminho,
									"nome_secretaria"=>$regSecretaria[nome_secretaria],
									"rodape"=>$regSecretaria[rodape],
									"sql" => $sql);
						
$PHPJasperXML->load_xml_file("exames_solicitados.jrxml");

$PHPJasperXML->transferDBtoArray($server,$user,$pass,$db,$cndriver="psql");
$PHPJasperXML->outpage("I");    //page output method I:standard output  D:Download file

?>
