<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_hce.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$f		= $_FILES['imgMap'];
	$rough	= AddSlashes($_POST['txtRough']);
	$ispt	= $_POST['isptSeq'];
	$rstFile= $hce->IPIN.'_'.$hce->rcpt.'.jpg';
	$rstImg	= '../hce/user_map/'.$orgNo.'/'.$hce->SR.'/'.$rstFile;

	//설명
	$sql = 'SELECT	COUNT(*)
			FROM	hce_inspection_needs
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			AND		ispt_seq= \''.$ispt.'\'';

	$cnt = $conn->get_data($sql);

	if ($cnt > 0){
		$sql = 'UPDATE	hce_inspection_needs
				SET		rough_text	= \''.$rough.'\'
				,		update_id	= \''.$_SESSION['userCode'].'\'
				,		update_dt	= NOW()
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$hce->SR.'\'
				AND		IPIN	= \''.$hce->IPIN.'\'
				AND		rcpt_seq= \''.$hce->rcpt.'\'
				AND		ispt_seq= \''.$ispt.'\'';
	}else{
		$sql = 'INSERT INTO hce_inspection_needs (
				 org_no
				,org_type
				,IPIN
				,rcpt_seq
				,ispt_seq
				,rough_text
				,insert_id
				,insert_dt) VALUES (
				 \''.$orgNo.'\'
				,\''.$hce->SR.'\'
				,\''.$hce->IPIN.'\'
				,\''.$hce->rcpt.'\'
				,\''.$ispt.'\'
				,\''.$rough.'\'
				,\''.$_SESSION['userCode'].'\'
				,NOW()
				)';
	}

	$conn->begin();
	$conn->execute($sql);
	$conn->commit();


	if (!$f['tmp_name']){
		$conn->close();
		echo 1;
		exit;
	}

	if (!Is_Dir('../hce/user_map/'.$orgNo)) MkDir('../hce/user_map/'.$orgNo);
	if (!Is_Dir('../hce/user_map/'.$orgNo.'/'.$hce->SR)) MkDir('../hce/user_map/'.$orgNo.'/'.$hce->SR);

	$exp = Explode('.',$f['name']);
	$exp = StrTolower($exp[SizeOf($exp)-1]);


	switch($exp){
		case 'jpg':
			$originalImg = imageCreateFromJpeg($f['tmp_name']);
			break;
		case 'png':
			$originalImg = imageCreateFromPng($f['tmp_name']);
			break;
		case 'gif':
			$originalImg = imageCreateFromGif($f['tmp_name']);
			break;
		case 'bmp':
			$originalImg = imageCreateFromBmp($f['tmp_name']);
			break;
	}

	list($width,$height) = GetImagesize($f['tmp_name']);

	// 새 이미트 틀작성
	$newImg = imageCreateTrueColor($width, $height);

	// 배경을 하얀색으로 설정
	$transColour = imageColorAllocate($newImg, 255,255,255);
	imageFill($newImg, 0, 0, $transColour);

	// 이미지 복사
	imageCopyReSampled($newImg, $originalImg, 0, 0, 0, 0, $width, $height, $width, $height);

	// 이미지 저장
	imageJpeg($newImg, $rstImg);

	// 종료
	imageDestroy($originalImg);
	imageDestroy($newImg);

	include_once('../inc/_db_close.php');
?>