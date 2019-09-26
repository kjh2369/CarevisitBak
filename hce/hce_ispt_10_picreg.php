<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');

	$orgNo = $_SESSION['userCenterCode'];

	$isptSeq = $_POST['isptSeq'];

	$picName = $_POST['txtPicName'];
	$picFile = $_FILES['txtPicFile'];
	$picSeq = $_POST['txtSeq'];


	//다음순번
	$sql = 'SELECT	IFNULL(MAX(pic_seq),0)+1
			FROM	hce_inspection_pic
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			AND		ispt_seq= \''.$isptSeq.'\'';

	$newSeq = $conn->get_data($sql);


	//갯수
	$cnt = SizeOf($picName);

	for($i=0; $i<$cnt; $i++){
		if (!$picSeq[$i]){
			$picSeq[$i] = $newSeq;
			$newSeq ++;
			$IsNew = true;
		}else{
			$IsNew = false;
		}

		if ($picFile['tmp_name'][$i]){
			$info = pathinfo($picFile['name'][$i]);
			$exp = StrToLower($info['extension']);

			$path = '../hce/img/'.$orgNo;
			if (!is_dir($path)) mkdir($path);

			$path .= '/'.$hce->SR;
			if (!is_dir($path)) mkdir($path);

			$path .= '/'.$hce->IPIN;
			if (!is_dir($path)) mkdir($path);


			/*
				$picPath = $path.'/'.$hce->rcpt.'_'.$isptSeq.'_'.$picSeq[$i].'.'.$exp;

				//이미지 이동
				$result = move_uploaded_file($picFile['tmp_name'][$i], $picPath);
				if (!$result) $picPath = '';
			 */


			$picPath = $path.'/'.$hce->rcpt.'_'.$isptSeq.'_'.$picSeq[$i].'.jpg';

			//이미지 변경
			switch($exp){
				case 'jpg':
					$originalImg = imageCreateFromJpeg($picFile['tmp_name'][$i]);
					break;
				case 'png':
					$originalImg = imageCreateFromPng($picFile['tmp_name'][$i]);
					break;
				case 'gif':
					$originalImg = imageCreateFromGif($picFile['tmp_name'][$i]);
					break;
				case 'bmp':
					$originalImg = imageCreateFromBmp($picFile['tmp_name'][$i]);
					break;
			}

			list($width,$height) = GetImagesize($picFile['tmp_name'][$i]);

			// 새 이미트 틀작성
			$newImg = imageCreateTrueColor($width, $height);

			// 배경을 하얀색으로 설정
			$transColour = imageColorAllocate($newImg, 255,255,255);
			imageFill($newImg, 0, 0, $transColour);

			// 이미지 복사
			imageCopyReSampled($newImg, $originalImg, 0, 0, 0, 0, $width, $height, $width, $height);

			// 이미지 저장
			imageJpeg($newImg, $picPath);

			// 종료
			imageDestroy($originalImg);
			imageDestroy($newImg);
		}else{
			$picPath = 'X';
		}

		if ($IsNew){
			$sql = 'INSERT INTO hce_inspection_pic (
					 org_no
					,org_type
					,IPIN
					,rcpt_seq
					,ispt_seq
					,pic_seq
					,pic_name';

			if ($picPath != 'X'){
				$sql .= '
					,pic_file
					,pic_path';
			}

			$sql .= '
					,insert_id
					,insert_dt) VALUES (
					 \''.$orgNo.'\'
					,\''.$hce->SR.'\'
					,\''.$hce->IPIN.'\'
					,\''.$hce->rcpt.'\'
					,\''.$isptSeq.'\'
					,\''.$picSeq[$i].'\'
					,\''.$picName[$i].'\'';

			if ($picPath != 'X'){
				$sql .= '
					,\''.$picFile['name'][$i].'\'
					,\''.$picPath.'\'';
			}

			$sql .= '
					,\''.$_SESSION['userCode'].'\'
					,NOW()
					)';
		}else{
			$sql = 'UPDATE	hce_inspection_pic
					SET		pic_name	= \''.$picName[$i].'\'';

			if ($picPath != 'X'){
				$sql .= '
					,		pic_file	= \''.$picFile['name'][$i].'\'
					,		pic_path	= \''.$picPath.'\'';
			}

			$sql .= '
					,		update_id	= \''.$_SESSION['userCode'].'\'
					,		update_dt	= NOW()
					WHERE	org_no		= \''.$orgNo.'\'
					AND		org_type	= \''.$hce->SR.'\'
					AND		IPIN		= \''.$hce->IPIN.'\'
					AND		rcpt_seq	= \''.$hce->rcpt.'\'
					AND		ispt_seq	= \''.$isptSeq.'\'
					AND		pic_seq		= \''.$picSeq[$i].'\'';
		}

		$query[] = $sql;
	}

	$conn->begin();

	foreach($query as $sql){
		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo 9;
			 exit;
		}
	}

	$conn->commit();
	echo 1;

	include_once('../inc/_db_close.php');
?>