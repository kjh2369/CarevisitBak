<?
	$mode  = $_GET['mode'];
	$dir   = $_POST['paper_dir'];
	
	echo '<style>';
	echo 'body{overflow-x:hidden;overflow-y:hidden;}';
	echo '</style>';
	
	if ($type == 'pdf'){
	echo '<input name=\'mode\'  type=\'hidden\' value=\''.$mode.'\'>';
	echo '<input name=\'dir\'   type=\'hidden\' value=\''.$dir.'\'>';