
<?
$number = 1234.56;

// let's print the international format for the en_US locale
setlocale(LC_MONETARY, 'pt_BR');
echo money_format('%i', $number) . "\n"; 
// USD 1,234.56

?>

