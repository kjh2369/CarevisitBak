<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');

	$orgNo = $_SESSION['userCenterCode'];

	$meetSeq = $_POST['meetSeq'];

	$reportName = $_POST['txtReportName'];
	$reportFile = $_FILES['txtReportFile'];
	$reportSeq = $_POST['txtSeq'];
	$sr     = $_POST['sr'];


	//다음순번
	$sql = 'SELECT	IFNULL(MAX(report_seq),0)+1
			FROM	care_report_file
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$sr.'\'';

	$newSeq = $conn->get_data($sql);


	//갯수
	$cnt = SizeOf($reportName);

	for($i=0; $i<$cnt; $i++){
		if (!$reportSeq[$i]){
			$reportSeq[$i] = $newSeq;
			$newSeq ++;
			$IsNew = true;
		}else{
			$IsNew = false;
		}

		if ($reportFile['tmp_name'][$i]){
			$info = pathinfo($reportFile['name'][$i]);
			$exp = StrToLower($info['extension']);

			$path = '../care/report_data/'.$orgNo;
			if (!is_dir($path)) mkdir($path);

			$path .= '/'.$sr;
			if (!is_dir($path)) mkdir($path);

			if($exp == 'jpg' || $exp == 'png' || $exp == 'gif' || $exp == 'bmp'){
				/*
					$reportPath = $path.'/'.$hce->rcpt.'_'.$isptSeq.'_'.$reportSeq[$i].'.'.$exp;

					//이미지 이동
					$result = move_uploaded_file($reportFile['tmp_name'][$i], $reportPath);
					if (!$result) $reportPath = '';
				 */

				
				$reportPath = $path.'/'.$reportSeq[$i].'.jpg';

				//이미지 변경
				switch($exp){
					case 'jpg':
						$originalImg = imageCreateFromJpeg($reportFile['tmp_name'][$i]);
						break;
					case 'png':
						$originalImg = imageCreateFromPng($reportFile['tmp_name'][$i]);
						break;
					case 'gif':
						$originalImg = imageCreateFromGif($reportFile['tmp_name'][$i]);
						break;
					case 'bmp':
						$originalImg = imageCreateFromBmp($reportFile['tmp_name'][$i]);
						break;
				}

				list($width,$height) = GetImagesize($reportFile['tmp_name'][$i]);

				// 새 이미트 틀작성
				$newImg = imageCreateTrueColor($width, $height);

				// 배경을 하얀색으로 설정
				$transColour = imageColorAllocate($newImg, 255,255,255);
				imageFill($newImg, 0, 0, $transColour);

				// 이미지 복사
				imageCopyReSampled($newImg, $originalImg, 0, 0, 0, 0, $width, $height, $width, $height);

				// 이미지 저장
				imageJpeg($newImg, $reportPath);

				// 종료
				imageDestroy($originalImg);
				imageDestroy($newImg);
			}else {
				$reportPath = $path.'/'.$reportSeq[$i].'.'.$exp;
				
				move_uploaded_file($reportFile['tmp_name'][$i], $reportPath);
			}
		}else{
			$reportPath = 'X';
		}

		if ($IsNew){
			$sql = 'INSERT INTO care_report_file (
					 org_no
					,org_type
					,report_seq
					,report_name';

			if ($reportPath != 'X'){
				$sql .= '
					,report_file
					,report_path';
			}

			$sql .= '
					,insert_id
					,insert_dt) VALUES (
					 \''.$orgNo.'\'
					,\''.$sr.'\'
					,\''.$reportSeq[$i].'\'
					,\''.$reportName[$i].'\'';

			if ($reportPath != 'X'){
				$sql .= '
					,\''.$reportFile['name'][$i].'\'
					,\''.$reportPath.'\'';
			}

			$sql .= '
					,\''.$_SESSION['userCode'].'\'
					,NOW()
					)';
		}else{
			$sql = 'UPDATE	care_report_file
					SET		report_name	= \''.$reportName[$i].'\'';

			if ($reportPath != 'X'){
				$sql .= '
					,		report_file	= \''.$reportFile['name'][$i].'\'
					,		report_path	= \''.$reportPath.'\'';
			}

			$sql .= '
					,		update_id	= \''.$_SESSION['userCode'].'\'
					,		update_dt	= NOW()
					WHERE	org_no		= \''.$orgNo.'\'
					AND		org_type	= \''.$sr.'\'
					AND		report_seq	= \''.$reportSeq[$i].'\'';
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