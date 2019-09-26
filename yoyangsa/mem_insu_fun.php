<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_login.php");
	include_once("../inc/_ed.php");
	include_once("../inc/_myFun.php");

	$code  = $_SESSION['userCenterCode'];
	$jumin = $_POST['jumin'];
	$stat  = $_POST['stat'];
	$type  = $_POST['type'];

	$insuCd	= $_POST['insuCd'];
	$seq	= $_POST['seq'];

	if (!Is_Numeric($jumin)) $jumin = $ed->de($jumin);

	if (Empty($code) || Empty($jumin)){
		exit;
	}

	if ($type == 'REG'){
		$join  = $_POST['join'];
		$start = $_POST['start'];

		//가입신청
		$sql = 'INSERT INTO insu (
				 org_no
				,jumin
				,join_dt
				,start_dt
				,insert_dt) VALUES (
				 \''.$code.'\'
				,\''.$jumin.'\'
				,\''.$join.'\'
				,\''.$start.'\'
				,NOW())';

		if ($conn->execute($sql)){
			echo 1;
		}else{
			echo 9;
		}

	}else if ($type == 'CANCEL'){
		$join  = $_POST['join'];

		$sql = 'SELECT	stat
				FROM	insu
				WHERE	insu_cd	= \''.$insuCd.'\'
				AND		org_no	= \''.$code.'\'
				AND		jumin	= \''.$jumin.'\'
				AND		seq		= \''.$seq.'\'';

		$stat = $conn->get_data($sql);

		if ($stat == '1'){
			//가입취소
			/*
			$sql = 'DELETE
					  FROM insu
					 WHERE org_no  = \''.$code.'\'
					   AND jumin   = \''.$jumin.'\'
					   AND join_dt = \''.$join.'\'
					   AND stat    = \'1\'';
			*/
			$sql = 'DELETE
					FROM	insu
					WHERE	insu_cd	= \''.$insuCd.'\'
					AND		org_no	= \''.$code.'\'
					AND		jumin	= \''.$jumin.'\'
					AND		seq		= \''.$seq.'\'';
		}else{
			switch($stat){
				case '9':
					$tmpStat = '7';
					break;

				case '7':
					$tmpStat = '3';
					break;

				case '3':
					$tmpStat = '1';
					break;
			}

			$sql = 'UPDATE	insu
					SET		stat = \''.$tmpStat.'\'';

			if ($stat == '7'){
				$sql .= ',	end_dt = NULL';
			}

			/*
			$sql .= '
					WHERE org_no  = \''.$code.'\'
					AND jumin   = \''.$jumin.'\'
					AND join_dt = \''.$join.'\'
					AND stat    = \''.$stat.'\'';
			*/

			$sql .= '
					WHERE	insu_cd	= \''.$insuCd.'\'
					AND		org_no	= \''.$code.'\'
					AND		jumin	= \''.$jumin.'\'
					AND		seq		= \''.$seq.'\'';
		}

		if ($conn->execute($sql)){
			echo 1;
		}else{
			echo 9;
		}
	}

	include_once("../inc/_db_close.php");
?>