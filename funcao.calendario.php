<?
	function campoData($dia, $mes, $ano, $valuedia='', $valuemes='', $valueano=''){
		$img = rand(0, 2000);
		$img = "img".$img;
		$calendar = rand($img+1, $img+2000);
		$calendar = "calendar".$calendar;
	?>
		<link rel="stylesheet" type="text/css" href="view.css" media="all">
		<script type="text/javascript" src="view.js"></script>
		<script type="text/javascript" src="calendar.js"></script>
		<span>
			<input id='<?=$dia?>' name='<?=$dia?>' class='boxData' size='2' maxlength='2' value='<?=$valuedia?>' type='text'> /
		</span>
		<span>
			<input id='<?=$mes?>' name='<?=$mes?>' class='boxData' size='2' maxlength='2' value='<?=$valuemes?>' type='text'> /
		</span>
		<span>
			<input id='<?=$ano?>' name='<?=$ano?>' class='boxData' size='4' maxlength='4' value='<?=$valueano?>' type='text'>
		</span>
		
		<span id='<?=$calendar?>'>
			<img id='<?=$img?>' class='datepicker' src='images/calendar.gif' alt='Selecione uma Data.' title='Selecione uma Data.'>	
		</span> 
		<script type='text/javascript'>
			Calendar.setup({
				inputField	 : '<?=$ano?>',
				dayField	 : '<?=$dia?>',
				monthField	 : '<?=$mes?>',
				baseField    : 'element_1',
				displayArea  : '<?=$calendar?>',
				button		 : '<?=$img?>',
				ifFormat	 : '%m %e, %Y',
				onSelect	 : selectEuropeDate
			})
		</script>
	<?
	}
?>

