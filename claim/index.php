<?	include_once('../inc/_header.php');	include_once('../inc/_http_uri.php');	include_once('../inc/_function.php');	include_once('../inc/_myFun.php');	include_once('../inc/_ed.php');	$type  = $_GET['type'];
	$mode  = $_GET['mode'];	$code  = $_POST['code'];	$year  = $_POST['year'];	$month = $_POST['month'];
	$dir   = $_POST['paper_dir'];
	
	echo '<style>';
	echo 'body{overflow-x:hidden;overflow-y:hidden;}';
	echo '</style>';
	
	if ($type == 'pdf'){		echo '<iframe name=\'frame_pdf\' src=\'about:blank\' style=\'width:100%; height:100%;\' frameborder=\'0\' scrolling=\'yes\'></iframe>';	}	echo '<form name=\'f\' method=\'post\'>';	echo '<input name=\'code\'  type=\'hidden\' value=\''.$code.'\'>';	echo '<input name=\'year\'  type=\'hidden\' value=\''.$year.'\'>';	echo '<input name=\'month\' type=\'hidden\' value=\''.$month.'\'>';	echo '<input name=\'type\'  type=\'hidden\' value=\''.$type.'\'>';
	echo '<input name=\'mode\'  type=\'hidden\' value=\''.$mode.'\'>';
	echo '<input name=\'dir\'   type=\'hidden\' value=\''.$dir.'\'>';	echo '</form>';	echo '<script language=\'javascript\'>';	echo 'var f = document.f;';	echo 'f.target = \'frame_pdf\';';	echo 'f.action = \'./claim_show.php\';';	echo 'f.submit();';	echo 'window.onload=function(){self.focus();}';	echo '</script>';	include_once('../inc/_footer.php');?>