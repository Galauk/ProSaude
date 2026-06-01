<style>
#teste{
	width:200px;
	}
#menu{ 
  width: 150px;
margin:0 0 0 -11px;
}

#menu ul{
  list-style: none;
  margin: 0;
  padding: 0;
}

#menu ul li{
 
  margin: 0 0 2px 0;
  padding: 0px 5px;
  text-align: left;
}

#menu ul li a {
  text-decoration: none;
}


</style>
<link href="css/estiloForm.css" rel="stylesheet" type="text/css" />
<link href="css/estiloCommon.css" rel="stylesheet" type="text/css" />
<link href="css/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css" />
<?php 
include 'global.php';

$form = new classForm();
$common = new commonClass();
echo $common->incJquery();
echo "<div id=teste>";	
echo $common->menuTab(array('Menu','Favoritos'));
echo $common->bodyTab('1');
echo "<div id='menu'>
				<ul>
			<li class='ui-corner-tr ui-corner-bl ui-state-default' style='font-weight: normal'><a href='/WebSocialSaude/zf/prontuario/agenda-do-dia'><img src='/WebSocialSaude/zf/public/images/icons/agenda.png' alt='' title='' />Ajuda</a></li>
		<li class='ui-corner-tr ui-corner-bl ui-state-default' style='font-weight: normal'><a href='/WebSocialSaude/zf/prontuario/agenda-do-dia/atendidos'><img src='/WebSocialSaude/zf/public/images/icons/paciente-check.png' alt='' title='' /> Ajuda  </a></li>
                            
   					<li class='ui-corner-tr ui-corner-bl ui-state-default' style='font-weight: normal'><a href='/WebSocialSaude/zf/prontuario/enfermagem/listar'> <img src='/WebSocialSaude/zf/public/images/icons/detalhes.png' alt='' title='' /> Cadastros</a></li>
            	</ul>

			</div>";
echo $common->closeTab();
echo "<div>";
echo $common->bodyTab('2');
echo $common->closeTab();

?>