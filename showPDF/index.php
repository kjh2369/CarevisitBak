<?
	parse_str($_POST['arguments'], $val);
	
	
	if($val['hompageYn'] == 'Y' || $val['mobileYn'] == 'Y'){
		//개인홈페이지에서 출력 시
	}else {
		include_once('../inc/_header.php');
		include_once('../inc/_http_uri.php');
		include_once('../inc/_myFun.php');
		include_once('../inc/_ed.php');
	}
	
	$target = !empty($val['target']) ? $val['target'] : 'show_pdf.php';
	
	
	echo '<iframe name=\'framePDF\' src=\'about:blank\' style=\'width:100%; height:100%;\' frameborder=\'0\' scrolling=\'no\'></iframe>';
	echo '<form name=\'f\' method=\'post\'>';
	echo '<input name=\'para\' type=\'hidden\' value=\''.$_POST['arguments'].'\'>';
	echo '</form>';

	echo '<script language=\'javascript\'>';
	echo 'var f = document.f;';
	echo 'f.target = \'framePDF\';';
	echo 'f.action = \'./'.$target.'\';';
	echo 'f.submit();';
	echo 'window.onload=function(){self.focus();}';
	echo '</script>';
	
	if($val['hompageYn'] == 'Y' || $val['mobileYn'] == 'Y'){
		//개인홈페이지에서 출력 시
	}else {
		include_once('../inc/_footer.php');
	}
?>