<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_myImage.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$orgType= $_POST['sr'];
	$userCd = $_SESSION['userCode'];
	$type	= $_POST['type'];
	$sr		= $_POST['sr'];
	$IPIN	= $hce->IPIN;
	$rcpt	= $hce->rcpt;
	$rstFile= $IPIN.'_'.$rcpt.'.jpg';//Date('YmdHis').'.jpg';

	if ($type == 'FAMILY_MAP'){
		$rstImg = $_SERVER['DOCUMENT_ROOT'].'/hce/map/'.$orgNo.'/'.$orgType.'/'.$rstFile;
		$column = 'family_path';
	}else if ($type == 'ECO_MAP'){
		$rstImg	= $_SERVER['DOCUMENT_ROOT'].'/hce/eco/'.$orgNo.'/'.$orgType.'/'.$rstFile;
		$column = 'eco_path';
	}else{
		exit;
	}

	$sql = 'UPDATE	hce_map
			SET		'.$column.' = NULL
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			AND		ispt_seq= \'1\'';

	$conn->begin();
	$conn->execute($sql);
	$conn->commit();

	if (Is_File($rstImg)){
		Unlink($rstImg);
	}

	include_once('../inc/_db_close.php');
?>