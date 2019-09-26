<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_function.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	/**************************************************

		파라메타

	**************************************************/
	$code  = $_POST['code'];
	$kind  = $_POST['kind'];
	$jumin = $ed->de($_POST['ssn']);
	$type  = $_GET['type'];
	
	$yymm = ($_POST[strtolower($type).'_yymm'] != '' ? $_POST[strtolower($type).'_yymm'] : $_POST['yymm']);
	$seq  = ($_POST[strtolower($type).'_seq'] != '' ? $_POST[strtolower($type).'_seq'] : $_POST['seq']);
	$svc_seq  = $_POST['svc_seq'];
	
	if($code == '34873000011'){ //보현재가(경남칠원)
		if($type == '200_test' or $type == '500_test') $seq = $_POST['seq']; //수급자 이용계약서 순번
	}else {
		if($type == '200' or $type == '500' or $type == '800') $seq = $_POST['seq']; //수급자 이용계약서 순번
	}


	
	/**************************************************

		PDF를 출력할 IFRAME 선언

	**************************************************/
	echo '<iframe name=\'frame_pdf\' src=\'about:blank\' style=\'width:100%; height:100%;\' frameborder=\'0\' scrolling=\'no\'></iframe>';


	/**************************************************

		변수를 넘겨줄 FORM 선언

	**************************************************/
	echo '<form name=\'f\' method=\'post\'>';

	echo '<input name=\'code\'  type=\'hidden\' value=\''.$code.'\'>';
	echo '<input name=\'kind\'  type=\'hidden\' value=\''.$kind.'\'>'; //
	echo '<input name=\'jumin\' type=\'hidden\' value=\''.$ed->en($jumin).'\'>';
	echo '<input name=\'report_id\' type=\'hidden\' value=\''.$_POST['report_id'].'\'>'; //고객평가관리(이용계약서출력시)

	//인사기록카드 주민번호넘김 
	echo '<input name=\'m_cd\' type=\'hidden\' value=\''.$_POST['para_m_cd'].'\'>';
	
	echo '<input name=\'yymm\' type=\'hidden\' value=\''.$yymm.'\'>';
	echo '<input name=\'seq\'  type=\'hidden\' value=\''.$seq.'\'>';
	echo '<input name=\'svc_seq\'  type=\'hidden\' value=\''.$svc_seq.'\'>';

	echo '<input name=\'type\' type=\'hidden\' value=\''.$type.'\'>';
	echo '<input name=\'root\' type=\'hidden\' value=\''.$_POST['root'].'\'>';
	
	/*************************************
		전체출력을위한 환경변수
	*************************************/
	for($i=0; $i<$row_cnt; $i++){
		echo '<input type=\'hidden\' id=\'type_'.$i.'\' name=\'type_'.$i.'\' value=\''.strtoupper($_POST['type_'.$i]).'\'>';
		echo '<input type=\'hidden\' id=\'yymm_'.$i.'\' name=\'yymm_'.$i.'\' value=\''.$_POST['yymm_'.$i].'\'>';
		echo '<input type=\'hidden\' id=\'seq_'.$i.'\' name=\'seq_'.$i.'\' value=\''.$_POST['seq_'.$i].'\'>';
		echo '<input type=\'hidden\' id=\'jumin_'.$i.'\' name=\'jumin_'.$i.'\' value=\''.$_POST['jumin_'.$i].'\'>';
		echo '<input type=\'hidden\' id=\'regDt_'.$i.'\' name=\'regDt_'.$i.'\' value=\''.$_POST['regDt_'.$i].'\'>';
	}
	
	echo '<input type=\'hidden\' id=\'row_cnt\' name=\'row_cnt\' value=\''.$row_cnt.'\'>';

	echo '</form>';


	/**************************************************

		IFRAME에 변수 전달

	**************************************************/
	echo '<script language=\'javascript\'>';
	echo 'var f = document.f;';
	echo 'f.target = \'frame_pdf\';';
	echo 'f.action = \'./counsel_show_pdf.php\';';
	echo 'f.submit();';
	echo 'window.onload=function(){self.focus();}';
	echo '</script>';

	include_once('../inc/_footer.php');
?>