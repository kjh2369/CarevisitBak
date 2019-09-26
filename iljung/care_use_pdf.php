<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_function.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	
	$mode  = $_POST['mode'];
	$code  = $_POST['code'];
	$year  = $_POST['year'];
	$month = $_POST['month'];
	$svcGbn	= $_POST['svcGbn'];
	$jumin = $_POST['jumin'];
	$printDT = $_POST['printDT'];
	$data   = $_POST['data'];
	
	echo '<iframe name=\'frame_pdf\' src=\'about:blank\' style=\'width:100%; height:100%;\' frameborder=\'0\' scrolling=\'no\'></iframe>';
	
	echo '<form name=\'f\' method=\'post\'>';

	echo '<input name=\'code\'  type=\'hidden\' value=\''.$code.'\'>';
	echo '<input name=\'mode\'  type=\'hidden\' value=\''.$mode.'\'>';
	echo '<input name=\'year\'  type=\'hidden\' value=\''.$year.'\'>';
	echo '<input name=\'month\' type=\'hidden\' value=\''.$month.'\'>';
	echo '<input name=\'jumin\' type=\'hidden\' value=\''.$jumin.'\'>';
	echo '<input name=\'svcGbn\'  type=\'hidden\' value=\''.$svcGbn.'\'>';
	echo '<input name=\'printDT\'  type=\'hidden\' value=\''.$printDT.'\'>';
	echo '<input name=\'data\'  type=\'hidden\' value=\''.$data.'\'>';
	
	echo '</form>';

	echo '<script language=\'javascript\'>';
	echo 'var f = document.f;';
	echo 'f.target = \'frame_pdf\';';
	echo 'f.action = \'./care_use_pdf_sub.php\';';
	echo 'f.submit();';
	echo 'window.onload=function(){self.focus();}';
	echo '</script>';

	include_once('../inc/_footer.php');
?>