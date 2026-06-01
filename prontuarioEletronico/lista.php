<?php 

$common = new commonClass();
echo $common->menuTab(array("Pacientes Agendados")).$common->bodyTab(1)."<div id=\"target\"></div>".$common->closeTab();

?>
<script type="text/javascript">
	$(function(){
		$("#target").load("/WebSocialSaude/zf/prontuario/agenda-do-dia?zf=0");
	});
</script>