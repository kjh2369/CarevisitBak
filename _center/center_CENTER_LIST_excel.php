<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$gbn = $_POST['gbn'];

	if ($gbn == '1'){
		$title = '미계약';
	}else if ($gbn == '2'){
		$title = 'CMS계약';
	}else if ($gbn == '3'){
		$title = '무통장계약';
	}else{
		exit;
	}

	$title .= '리스트';

	header("Content-type: application/vnd.ms-excel;charset=UTF-8");
	header("Expires: 0" );
	header("Cache-Control: must-revalidate, post-check=0,pre-check=0" );
	header("Pragma: public" );
	header("Content-Disposition: attachment; filename=".$myF->euckr($title).".xls" );

	//CMS 리스트
	$sql = 'SELECT	org_no, GROUP_CONCAT(CONCAT(cms_no,\' / \',CASE cms_com WHEN \'1\' THEN \'굿이오스\' WHEN \'2\' THEN \'지케어\' WHEN \'3\' THEN \'케어비지트\' ELSE cms_com END)) AS cms_com
			FROM	cv_cms_list
			GROUP	BY org_no';
	$arrCMSList = $conn->_fetch_array($sql, 'org_no');


	if ($gbn == '1'){
		include_once('./center_cont_not_list.php');
	}else if ($gbn == '2'){
		include_once('./center_cont_cms_list.php');
	}else if ($gbn == '3'){
		include_once('./center_cont_bank_list.php');
	}

	include_once('../inc/_db_close.php');
?>