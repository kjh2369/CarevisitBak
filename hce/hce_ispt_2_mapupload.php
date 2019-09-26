<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');

	$orgNo = $_SESSION['userCenterCode'];

	$familymap = $_FILES['familymap'];
	$ecomap = $_FILES['ecomap'];

	$IPIN = $hce->IPIN;
	$rcpt = $hce->rcpt;

	$sql = 'SELECT	COUNT(*)
			FROM	hce_map
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$hce->SR.'\'
			AND		IPIN	= \''.$hce->IPIN.'\'
			AND		rcpt_seq= \''.$hce->rcpt.'\'
			AND		ispt_seq= \'1\'';

	$cnt = $conn->get_data($sql);
	$rst = '';

	if ($familymap['tmp_name']){
		$picInfo = pathinfo($familymap['name']);
		$exp = StrToLower($picInfo['extension']);
		$rstImg = '../hce/map/'.$orgNo.'/'.$hce->SR.'/'.$IPIN.'_'.$rcpt.'.'.$exp;

		//이미지 이동
		$result = move_uploaded_file($familymap['tmp_name'], $rstImg);

		if (!$result){
			$conn->close();
			echo 'FAMILYMAP_ERROR';
			exit;
		}

		if ($cnt > 0){
			$sql = 'UPDATE	hce_map
					SET		family_path = \''.$rstImg.'\'
					WHERE	org_no	= \''.$orgNo.'\'
					AND		org_type= \''.$hce->SR.'\'
					AND		IPIN	= \''.$hce->IPIN.'\'
					AND		rcpt_seq= \''.$hce->rcpt.'\'
					AND		ispt_seq= \'1\'';
		}else{
			$sql = 'INSERT INTO hce_map (
					 org_no
					,org_type
					,IPIN
					,rcpt_seq
					,ispt_seq
					,family_path) VALUES (
					 \''.$orgNo.'\'
					,\''.$hce->SR.'\'
					,\''.$hce->IPIN.'\'
					,\''.$hce->rcpt.'\'
					,\'1\'
					,\''.$rstImg.'\'
					)';
		}

		$query[] = $sql;

		$rst .= '?gbn=FAMILYMAP&src='.$rstImg;
	}

	if ($ecomap['tmp_name']){
		$picInfo = pathinfo($ecomap['name']);
		$exp = StrToLower($picInfo['extension']);
		$rstImg = '../hce/eco/'.$orgNo.'/'.$hce->SR.'/'.$IPIN.'_'.$rcpt.'.'.$exp;

		//이미지 이동
		$result = move_uploaded_file($ecomap['tmp_name'], $rstImg);

		if (!$result){
			$conn->close();
			echo 'ECOMAP_ERROR';
			exit;
		}

		if ($cnt > 0){
			$sql = 'UPDATE	hce_map
					SET		eco_path = \''.$rstImg.'\'
					WHERE	org_no	= \''.$orgNo.'\'
					AND		org_type= \''.$hce->SR.'\'
					AND		IPIN	= \''.$hce->IPIN.'\'
					AND		rcpt_seq= \''.$hce->rcpt.'\'
					AND		ispt_seq= \'1\'';
		}else{
			$sql = 'INSERT INTO hce_map (
					 org_no
					,org_type
					,IPIN
					,rcpt_seq
					,ispt_seq
					,eco_path) VALUES (
					 \''.$orgNo.'\'
					,\''.$hce->SR.'\'
					,\''.$hce->IPIN.'\'
					,\''.$hce->rcpt.'\'
					,\'1\'
					,\''.$rstImg.'\'
					)';
		}

		$query[] = $sql;

		$rst .= '?gbn=ECOMAP&src='.$rstImg;
	}

	if (is_array($query)){
		$conn->begin();

		foreach($query as $sql){
			if (!$conn->execute($sql)){
				 $conn->rollback();
				 $conn->close();
				 echo 'ERROR';
				 exit;
			}
		}

		$conn->commit();
	}

	echo $rst;

	include_once('../inc/_db_close.php');
?>