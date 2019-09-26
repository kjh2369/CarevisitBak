<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$SR		= $_POST['svcCd'];
	$date	= $_POST['date'];
	$time	= $_POST['time'];
	$seq	= $_POST['seq'];
	$jumin	= $ed->de($_POST['jumin']);
	$suga	= $_POST['suga'];
	$res	= $_POST['resource'];
	$mem	= $ed->de($_POST['mem']);
	$origin	= $_POST['origin'];
	$pic	= $_FILES['pic'];
	$result = false;

	if (!$SR || !$date || !$jumin || !$suga || !$res){
		include_once('../inc/_db_close.php');
		exit;
	}

	//사진등록
	if ($pic['tmp_name']){
		$fileName = $pic['name'];

		$picInfo = pathinfo($pic['name']);
		$picName = mktime();
		$picExp = StrToLower($picInfo['extension']);
		$picIdx = 1;

		/*
		$picStr = $picName.'_'.$picIdx.'.'.$picExp;

		$picPath = '../care/work_log/'.$orgNo;
		if (!is_dir($picPath)) mkdir($picPath);

		$picPath .= '/'.$SR;
		if (!is_dir($picPath)) mkdir($picPath);

		$picPath .= '/'.$date;
		if (!is_dir($picPath)) mkdir($picPath);

		$filePath = $picPath.'/'.$picStr;

		while(true){
			if (!is_file($filePath)) break;

			$picIdx ++;
			$picStr = $picName.'_'.$picIdx.'.'.$picExp;
			$filePath = $picPath.'/'.$picStr;
		}
		*/

		$filePath = lfGetFileName($orgNo, $SR, $date, $picName, $picIdx, $picExp);

		//이미지 이동
		$result = move_uploaded_file($pic['tmp_name'], $filePath);

		if (!$result){
			$conn->close();
			echo 'ATTACH_ERROR';
			exit;
		}
	}else if ($origin == 'OLD'){
		//이전 이미지 복사
		$sql = 'SELECT	picture
				FROM	care_result
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$SR.'\'
				AND		jumin	= \''.$jumin.'\'
				AND		date	= \''.$date.'\'
				AND		time	= \''.$time.'\'
				AND		seq		= \''.$seq.'\'
				AND		del_flag= \'N\'
				ORDER	BY no
				LIMIT	1';

		$fileName = $conn->get_data($sql);

		if ($fileName){
			$picInfo = pathinfo($fileName);
			$picName = mktime();
			$picExp = StrToLower($picInfo['extension']);
			$picIdx = 1;

			$filePath = lfGetFileName($orgNo, $SR, $date, $picName, $picIdx, $picExp);

			if (is_file('../care/pic/'.$fileName)){
				if (!copy('../care/pic/'.$fileName, $filePath)){
					$conn->close();
					echo 'ATTACH_ERROR';
					exit;
				}
			}
		}
	}else{
		$fileName = '';
		$filePath = '';
	}

	$sql = 'SELECT	COUNT(*)
			FROM	care_works_log
			WHERE	org_no		= \''.$orgNo.'\'
			AND		org_type	= \''.$SR.'\'
			AND		date		= \''.$date.'\'
			AND		jumin		= \''.$jumin.'\'
			AND		suga_cd		= \''.$suga.'\'
			AND		resource_cd	= \''.$res.'\'
			AND		mem_cd		= \''.$mem.'\'';

	$cnt = $conn->get_data($sql);

	if ($cnt > 0){
		$sql = 'UPDATE	care_works_log
				SET		contents	= \''.AddSlashes($_POST['txtContents']).'\'';

		if ($fileName){
			$sql .= '
				,		pic_nm		= \''.$fileName.'\'
				,		file_path	= \''.$filePath.'\'';
		}

		$sql .= '
				,		update_id	= \''.$_SESSION['userCode'].'\'
				,		update_dt	= NOW()
				WHERE	org_no		= \''.$orgNo.'\'
				AND		org_type	= \''.$SR.'\'
				AND		date		= \''.$date.'\'
				AND		jumin		= \''.$jumin.'\'
				AND		suga_cd		= \''.$suga.'\'
				AND		resource_cd	= \''.$res.'\'
				AND		mem_cd		= \''.$mem.'\'';
	}else{
		$sql = 'INSERT INTO care_works_log (
				 org_no
				,org_type
				,date
				,jumin
				,suga_cd
				,resource_cd
				,mem_cd
				,contents';

		if ($fileName){
			$sql .= '
				,pic_nm
				,file_path';
		}

		$sql .= '
				,insert_id
				,insert_dt) VALUES (
				 \''.$orgNo.'\'
				,\''.$SR.'\'
				,\''.$date.'\'
				,\''.$jumin.'\'
				,\''.$suga.'\'
				,\''.$res.'\'
				,\''.$mem.'\'
				,\''.AddSlashes($_POST['txtContents']).'\'';

		if ($fileName){
			$sql .= '
				,\''.$fileName.'\'
				,\''.$filePath.'\'';
		}

		$sql .= '
				,\''.$_SESSION['userCode'].'\'
				,NOW()
				)';
	}

	$conn->begin();

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 if ($result) @unlink($filePath);
		 echo 'DATA_ERROR';
		 exit;
	}

	$conn->commit();

	if ($fileName){
		$tmp_info = pathinfo($fileName);
		$exp_nm = strtolower($tmp_info['extension']);

		$original_path = $filePath;
		$img_w = 800;
		$img_h = 600;
		$img_s = getimagesize($original_path);

		/**************************************************

			����, ���� ������ �°� ����Ѵ�.

		**************************************************/
		if ($img_w < $img_s[0] || $img_h < $img_s[1]){
			if ($img_s[0] > $img_s[1]){
				$img_h = $img_s[1] * ($img_w / $img_s[0]);
			}else{
				$img_w = $img_s[0] * ($img_h / $img_s[1]);
			}

			switch($exp_nm){
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

			// �� �̹�Ʈ Ʋ�ۼ�
			$new_img = imageCreateTrueColor($img_w, $img_h);

			// ����� �Ͼ������ ����
			$trans_colour = imageColorAllocate($new_img, 255,255,255);
			imageFill($new_img, 0, 0, $trans_colour);

			// �̹��� ����
			imageCopyReSampled($new_img, $original_img, 0, 0, 0, 0, $img_w, $img_h, $img_s[0], $img_s[1]);

			// �̹��� ����
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

			// ����
			imageDestroy($new_img);
		}

		$size = GetImageSize($filePath);
		$data = 'src='.$filePath.'&width='.$size[0].'&height='.$size[1];
		echo $data;
	}

	function lfGetFileName($orgNo, $SR, $date, $picName, $picIdx, $picExp){
		$path = '../care/work_log/';

		$picPath = $path.$orgNo;
		if (!is_dir($picPath)) mkdir($picPath);

		$picPath .= '/'.$SR;
		if (!is_dir($picPath)) mkdir($picPath);

		$picPath .= '/'.$date;
		if (!is_dir($picPath)) mkdir($picPath);

		$picStr = $picName.'_'.$picIdx.'.'.$picExp;
		$filePath = $picPath.'/'.$picStr;

		while(true){
			if (!is_file($filePath)) break;

			$picIdx ++;
			$picStr = $picName.'_'.$picIdx.'.'.$picExp;
			$filePath = $picPath.'/'.$picStr;
		}

		return $filePath;
	}

	include_once('../inc/_db_close.php');
?>