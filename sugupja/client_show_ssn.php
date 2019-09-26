<?
	include_once('../inc/_ed.php');

	$ssn = $ed->de($_POST['ssn']);
	$ssn = substr($ssn,0,6).'-'.substr($ssn,6);

	echo '<input name=\'str_ssn\' type=\'hidden\' value=\''.$ssn.'\'>';
?>