<?
	$name = $myF->euckr($var['name']);

	$chkSvcCd = str_replace(chr(1),'',$var['chkSvc']);
	$mode = $var['mode'];
	
	
	if ($chkSvcCd == '4_2004_5004_800') $chkSvcCd = '4';

	if(($chkSvcCd == '2' || $chkSvcCd == '4') && $mode == '101'){
		if($_SESSION['userCenterCode'] == 'DW-F-043-02' || //�����
		   $_SESSION['userCenterCode'] == '32623000227' || //����
		   $_SESSION['userCenterCode'] == '31144000115' || //�����ÿ������ڸ��� ������
		   $_SESSION['userCenterCode'] == '31144000173' ){ //�̷ο��簡��缾��
			include_once("../iljung/iljung_print_pdf_sub.php");
		}else {
			include_once("../iljung/iljung_print_pdf_sub2.php");
		}
	}else {
		include_once("../iljung/iljung_print_pdf_sub.php");
	}
?>