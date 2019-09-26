<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_login.php");
	include_once("../inc/_ed.php");
	include_once("../inc/_myFun.php");

	$code		= $_SESSION['userCenterCode'];
	$jumin		= $_POST['jumin'];
	$joinDt		= $_POST['joinDt'];
	$quitDt		= $_POST['quitDt'];
	$insuCd		= $_POST['insuCd'];
	$insuFrom	= $_POST['insuFrom'];
	$insuTo		= $_POST['insuTo'];
	$insuYn		= $_POST['insuYN'];
	$hisSeq		= $_POST['memHisSeq'];

	if (!Is_Numeric($jumin)) $jumin = $ed->de($jumin);

	if ($insuYn == 'Y'){
		$sql = 'SELECT	COUNT(*)
				FROM	insu
				WHERE	insu_cd	= \''.$insuCd.'\'
				AND		org_no	= \''.$code.'\'
				AND		jumin	= \''.$jumin.'\'
				AND		stat	<= \'3\'';
		$tmp = $conn->get_data($sql);

		if ($tmp > 0){
			$conn->close();
			echo -1;
			exit;
		}

		$sql = 'SELECT	IFNULL(MAX(seq), 0) + 1
				FROM	insu
				WHERE	insu_cd	= \''.$insuCd.'\'
				AND		org_no	= \''.$code.'\'
				AND		jumin	= \''.$jumin.'\'';
		$seq = $conn->get_data($sql);

		$sql = 'INSERT INTO insu (
				 insu_cd
				,org_no
				,jumin
				,seq
				,join_dt
				,start_dt
				,stat
				,insert_dt) VALUES (
				 \''.$insuCd.'\'
				,\''.$code.'\'
				,\''.$jumin.'\'
				,\''.$seq.'\'
				,\''.$joinDt.'\'
				,\''.$insuFrom.'\'
				,\'1\'
				,NOW())';
	}else{
		$sql = 'SELECT	seq
				FROM	insu
				WHERE	insu_cd	= \''.$insuCd.'\'
				AND		org_no	= \''.$code.'\'
				AND		jumin	= \''.$jumin.'\'
				AND		join_dt	= \''.$joinDt.'\'
				AND		stat	= \'3\'';
		$seq = $conn->get_data($sql);

		$sql = 'UPDATE	insu
				SET		quit_dt   = '.(!Empty($quitDt) ? '\''.$quitDt.'\'' : 'NULL').'
				,		end_dt    = \''.$insuTo.'\'
				,		stat      = \'7\'
				,		insert_dt = NOW()
				WHERE	insu_cd	= \''.$insuCd.'\'
				AND		org_no	= \''.$code.'\'
				AND		jumin	= \''.$jumin.'\'
				AND		seq		= \''.$seq.'\'
				AND		stat	= \'3\'';
	}

	$conn->begin();

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo 0;
		 exit;
	}

	if ($insuYn == 'Y'){
		$sql = 'UPDATE	mem_his
				SET		insu_yn	= \''.$insuYn.'\'
				WHERE	org_no	= \''.$code.'\'
				AND		jumin	= \''.$jumin.'\'
				AND		seq		= \''.$hisSeq.'\'';

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo 0;
			 exit;
		}
	}

	$conn->commit();

	$sql = 'SELECT	stat
			FROM	insu
			WHERE	insu_cd	= \''.$insuCd.'\'
			AND		org_no	= \''.$code.'\'
			AND		jumin	= \''.$jumin.'\'
			AND		join_dt	= \''.$joinDt.'\'
			ORDER	BY seq DESC
			LIMIT	1';
	$stat = $conn->get_data($sql);

	echo $stat;

	include_once("../inc/_db_close.php");
?>