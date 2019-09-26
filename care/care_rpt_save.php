<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once("../inc/_page_list.php");
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo = $_SESSION['userCenterCode'];
	$suga_cd = $_POST['suga_cd'];
	//$SR = $_POST['SR'];
	//$seq = $_POST['seq'];
	$reg_dt = str_replace('-', '', $_POST['reg_dt']);
	$att_cnt = $_POST['att_cnt'];
	$attendee = addslashes($_POST['attendee']);
	$contents = addslashes($_POST['contents']);

	$SR = $_GET['SR'];
	$seq = $_GET['seq'];

	for($i=1; $i<=2; $i++){
		$pic = $_FILES['filename'.$i];
		$upload = false;

		if ($pic['tmp_name']){
			$tmp_info = pathinfo($pic['name']);
			$pic_nm = mktime();
			$exp_nm = strtolower($tmp_info['extension']);
			$idx = 1;

			while(true){
				$pic_path[$i] = '../care/pic/'.$pic_nm.'_'.$idx.'.'.$exp_nm;

				if (!is_file($pic_path[$i])){
					break;
				}

				$idx ++;
			}

			if (move_uploaded_file($pic['tmp_name'], $pic_path[$i])){
				$original_path = $pic_path[$i];
				$img_w = 515;
				$img_h = 450;
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

				switch($exp_nm){
					case 'jpg':
					case 'jpeg':
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
				switch($exp_nm){
					case 'jpg':
					case 'jpeg':
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

				// 종료
				imageDestroy($new_img);
			}
		}
	}

	if (!$seq){
		$sql = 'SELECT	IFNULL(MAX(seq), 0) + 1
				FROM	care_rpt
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_sr	= \''.$SR.'\'
				AND		suga_cd	= \''.$suga_cd.'\'';
		$seq = $conn->get_data($sql);
		$IsNew = true;
	}else{
		$IsNew = false;
	}

	if ($IsNew){
		$sql = 'INSERT INTO care_rpt (org_no, org_sr, suga_cd, seq, reg_dt, att_cnt, attendee, contents'.($pic_path['1'] ? ', pic1' : '').($pic_path['2'] ? ', pic2' : '').', insert_id, insert_dt) VALUES (
				 \''.$orgNo.'\'
				,\''.$SR.'\'
				,\''.$suga_cd.'\'
				,\''.$seq.'\'
				,\''.$reg_dt.'\'
				,\''.$att_cnt.'\'
				,\''.$attendee.'\'
				,\''.$contents.'\'
				';
		if ($pic_path['1']) $sql .= ',\''.$pic_path['1'].'\'';
		if ($pic_path['2']) $sql .= ',\''.$pic_path['2'].'\'';

		$sql .= ',\''.$_SESSION['userCode'].'\'
				,NOW()
				)';
	}else{
		$sql = 'UPDATE	care_rpt
				SET		reg_dt		= \''.$reg_dt.'\'
				,		att_cnt		= \''.$att_cnt.'\'
				,		attendee	= \''.$attendee.'\'
				,		contents	= \''.$contents.'\'
				';
		if ($pic_path['1']) $sql .= ', pic1		= \''.$pic_path['1'].'\'';
		if ($pic_path['2']) $sql .= ', pic1		= \''.$pic_path['2'].'\'';

		$sql .= ',		del_flag	= \'N\'
				,		update_id	= \''.$_SESSION['userCode'].'\'
				,		update_dt	= NOW()
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_sr	= \''.$SR.'\'
				AND		suga_cd	= \''.$suga_cd.'\'
				AND		seq		= \''.$seq.'\'';
	}

	$conn->begin();

	if ($conn->execute($sql)){
		$conn->commit();
	}else{
		$conn->rollback();
		$conn->close();
		echo '데이타 처리 중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.';
	}

	include_once('../inc/_db_close.php');
?>