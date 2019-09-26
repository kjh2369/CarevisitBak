<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo = $_SESSION['userCenterCode'];
	$rowId = $_POST['rowId'];
	$jumin = $ed->de($_POST['jumin']);
	$SR = $_POST['SR'];
	$date = $_POST['date'];
	$time = $_POST['time'];
	$seq = $_POST['seq'];
	$no = $_POST['no'];

	$pic = $_FILES['pic'];

	if ($pic['tmp_name']){
		$picInfo = pathinfo($pic['name']);
		$picName = MkTime();
		$picExp = StrToLower($picInfo['extension']);
		$picIdx = 1;
		$picStr = $picName.'_'.$picIdx.'.'.$picExp;
		$picPath = '../care/pic/'.$picStr;

		while(true){
			if (!is_file($picPath)) break;

			$picIdx ++;
			$picStr = $picName.'_'.$picIdx.'.'.$picExp;
			$picPath = '../care/pic/'.$picStr;
		}

		if (!move_uploaded_file($pic['tmp_name'], $picPath)){?>
			 <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			 <script type="text/javascript">
				alert('사진등록중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
				self.close();
			 </script><?
			 exit;
		}
	}

	$sql = 'SELECT	COUNT(*)
			FROM	care_result
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$SR.'\'
			AND		jumin	= \''.$jumin.'\'
			AND		date	= \''.$date.'\'
			AND		time	= \''.$time.'\'
			AND		seq		= \''.$seq.'\'
			AND		no		= \''.$no.'\'';

	$resultCnt = $conn->get_data($sql);

	if ($resultCnt > 0){
		$sql = 'UPDATE	care_result
				SET		picture		= \''.$picStr.'\'
				,		update_id	= \''.$_SESSION['userCode'].'\'
				,		update_dt	= NOW()
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$SR.'\'
				AND		jumin	= \''.$jumin.'\'
				AND		date	= \''.$date.'\'
				AND		time	= \''.$time.'\'
				AND		seq		= \''.$seq.'\'
				AND		no		= \''.$no.'\'';
	}else{
		$sql = 'INSERT INTO care_result(
				 org_no
				,org_type
				,jumin
				,date
				,time
				,seq
				,picture
				,insert_id
				,insert_dt) VALUES (
				 \''.$orgNo.'\'
				,\''.$SR.'\'
				,\''.$jumin.'\'
				,\''.$date.'\'
				,\''.$time.'\'
				,\''.$seq.'\'
				,\''.$picStr.'\'
				,\''.$_SESSION['userCode'].'\'
				,NOW())';
	}

	$conn->begin();

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();?>
		 <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		 <script type="text/javascript">
			alert('사진등록중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
			self.close();
		 </script><?
		 exit;
	}

	$sql = 'UPDATE	t01iljung
			SET		t01_status_gbn = \'1\'
			WHERE	t01_ccode= \''.$orgNo.'\'
			AND		t01_mkind= \''.$SR.'\'
			AND		t01_jumin= \''.$jumin.'\'
			AND		t01_sugup_date	= \''.$date.'\'
			AND		t01_sugup_fmtime= \''.$time.'\'
			AND		t01_sugup_seq	= \''.$seq.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();?>
		 <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		 <script type="text/javascript">
			alert('사진등록중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
			self.close();
		 </script><?
		 exit;
	}

	$conn->commit();

	$sql = 'SELECT	no
			FROM	care_result
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$SR.'\'
			AND		jumin	= \''.$jumin.'\'
			AND		date	= \''.$date.'\'
			AND		time	= \''.$time.'\'
			AND		seq		= \''.$seq.'\'
			AND		del_flag= \'N\'';

	$newNo = $conn->get_data($sql);

	include_once('../inc/_db_close.php');
?>
<script type="text/javascript">
	opener.lfPictureView('<?=$rowId;?>','<?=$newNo;?>','<?=$picStr;?>');
	self.close();
</script>