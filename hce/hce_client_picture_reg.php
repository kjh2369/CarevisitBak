<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');

	$orgNo = $_SESSION['userCenterCode'];
	$IPIN = $_GET['fileCd'];

	if (!$orgNo || !$IPIN) exit;

	//사진저장
	$pic = $_FILES['filePicture'];

	if ($pic['tmp_name']){
		$picInfo = pathinfo($pic['name']);
		$picName = MkTime();
		$picExp = StrToLower($picInfo['extension']);
		$picIdx = 1;

		$picPath = '../sugupja/picture';

		if (!is_dir($picPath)) mkdir($picPath);

		$picStr = $orgNo.'_'.$IPIN.'.'.$picExp;
		$picNew = $orgNo.'_'.$IPIN.'.jpg';
		$newPath = $picPath.'/'.$picNew;
		$picPath .= '/'.$picStr;

		if (!move_uploaded_file($pic['tmp_name'], $picPath)){
			echo 9;
			exit;
		}

		$original_path = $picPath;
		$img_w = 114;
		$img_h = 130;
		$img_s = getimagesize($original_path);

		/**************************************************

			가로, 세로 비율에 맞게 축소한다.

		**************************************************/
		if ($img_w < $img_s[0] || $img_h < $img_s[1]){
			if ($img_s[0] > $img_s[1]){
				$img_r = $img_s[1] / $img_s[0];
				$img_h = $img_h * $img_r;
			}else{
				$img_r = $img_s[0] / $img_s[1];
				$img_w = $img_w * $img_r;
			}
		}else{
			$img_w = $img_s[0];
			$img_h = $img_s[1];
		}

		switch($picExp){
			case 'jpg':
				$original_img = imageCreateFromJpeg($original_path);
				break;
			case 'png':
				$original_img = imageCreateFromPng($original_path);
				break;
			case 'gif':
				$original_img = imageCreateFromGif($original_path);
				break;
			case 'bmp':
				$original_img = imageCreateFromBmp($original_path);
				break;
		}

		// 새 이미트 틀작성
		$new_img = imageCreateTrueColor($img_w, $img_h);

		// 배경을 하얀색으로 설정
		$trans_colour = imageColorAllocate($new_img, 255,255,255);
		imageFill($new_img, 0, 0, $trans_colour);

		// 이미지 복사
		imageCopyReSampled($new_img, $original_img, 0, 0, 0, 0, $img_w, $img_h, $img_s[0], $img_s[1]);

		// 이미지 저장
		/*
		switch($exp_nm){
			case 'jpg':
				imageJpeg($new_img, $original_path);
				break;
			case 'png':
				imagePng($new_img, $original_path);
				break;
			case 'gif':
				imageGif($new_img, $original_path);
				break;
			case 'bmp':
				imageBmp($new_img, $original_path);
				break;
		}
		*/
		imageJpeg($new_img, $newPath);

		// 종료
		imageDestroy($new_img);

		if ($newPath != $original_path){
			unlink($original_path);
		}
	}

	echo 1;

	include_once('../inc/_db_close.php');
?>