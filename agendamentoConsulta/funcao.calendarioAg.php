<?
	function agData($id){
		echo "
			<html>
				<head>
					<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
					<title>jQuery Date picker</title>
			
					<link rel='stylesheet' type=text/css href='jquery-ui-1.7.2.custom.css' />
				</head>
				<body>
					<form action='/' method='post'>
						<label for='calendario'>Data:</label>
						<input type='image' name='$id' id='$id' />
					</form>
				</body>
			
			</html>
";
	}
?>


