<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$SR		= $_POST['svcCd'];
	$IPIN	= $_POST['IPIN'];
	$seq	= $_POST['seq'];
	$no		= $_POST['no'];
	$date	= $_POST['date'];
	$gbn	= $_POST['gbn'];
	$memCd	= $ed->de($_POST['memCd']);
	$memNm	= $_POST['memNm'];
	$text	= AddSlashes($_POST['text']);
	$mode	= $_POST['mode'];

	if ($mode == 'SAVE'){
		if ($no < 1){
			//상담이력 순번
			$sql = 'SELECT	IFNULL(MAX(proc_seq),0)+1
					FROM	hce_proc_counsel
					WHERE	org_no	= \''.$orgNo.'\'
					AND		org_type= \''.$SR.'\'
					AND		IPIN	= \''.$IPIN.'\'
					AND		rcpt_seq= \''.$seq.'\'';

			$no = $conn->get_data($sql);

			$sql = 'INSERT INTO hce_proc_counsel (
					 org_no
					,org_type
					,IPIN
					,rcpt_seq
					,proc_seq
					,counsel_dt
					,counsel_nm
					,counsel_jumin
					,counsel_gbn
					,counsel_text
					,insert_id
					,insert_dt) VALUES (
					 \''.$orgNo.'\'
					,\''.$SR.'\'
					,\''.$IPIN.'\'
					,\''.$seq.'\'
					,\''.$no.'\'
					,\''.$date.'\'
					,\''.$memNm.'\'
					,\''.$memCd.'\'
					,\''.$gbn.'\'
					,\''.$text.'\'
					,\''.$_SESSION['userCode'].'\'
					,NOW()
					)';
		}else{
			$sql = 'UPDATE	hce_proc_counsel
					SET		counsel_text= \''.$text.'\'
					,		update_id	= \''.$_SESSION['userCode'].'\'
					,		update_dt	= NOW()
					WHERE	org_no	= \''.$orgNo.'\'
					AND		org_type= \''.$SR.'\'
					AND		IPIN	= \''.$IPIN.'\'
					AND		rcpt_seq= \''.$seq.'\'
					AND		proc_seq= \''.$no.'\'';
		}
	}else if ($mode == 'REMOVE'){
		$sql = 'UPDATE	hce_proc_counsel
				SET		del_flag	= \'Y\'
				,		update_id	= \''.$_SESSION['userCode'].'\'
				,		update_dt	= NOW()
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$SR.'\'
				AND		IPIN	= \''.$IPIN.'\'
				AND		rcpt_seq= \''.$seq.'\'
				AND		proc_seq= \''.$no.'\'';
	}else{
		exit;
	}

	$conn->begin();

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo 9;
		 exit;
	}

	$conn->commit();
	echo 1;

	include_once('../inc/_db_close.php');
?>