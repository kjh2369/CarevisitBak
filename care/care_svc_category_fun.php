<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once("../inc/_page_list.php");
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$SR		= $_POST['SR'];
	$gbn	= $_POST['gbn'];
	$code	= $_POST['code'];
	$parent	= $code;


	if ($gbn == 'FULLNAME'){
		//카테고리 전체 명칭
		$name = '';

		while(true){
			$sql = 'SELECT	parent
					,		name
					FROM	mst_category
					WHERE	org_no	= \''.$orgNo.'\'
					AND		org_type= \''.$SR.'\'
					AND		gbn		= \'01\'
					AND		code	= \''.$parent.'\'';

			$row = $conn->get_array($sql);
			$cd = $row['parent'];
			$name = $row['name'].($name ? ' - ' : '').$name;

			if ($cd == '0') $cd = '';
			if (!$cd){
				break;
			}else{
				$parent = $cd;
			}
		}

		echo $name;


	}else if ($gbn == 'LIST_A'){
		//카테고리 등록 묶음내역
		$sql = 'SELECT	suga_cd
				,		seq
				,		group_nm
				FROM	care_svc_group
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$SR.'\'
				AND		category= \''.$code.'\'
				AND		del_flag= \'N\'
				ORDER	BY group_nm';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);?>
			<div id="ID_LIST_A_<?=$i;?>" cd="<?=$row['suga_cd'];?>" seq="<?=$row['seq'];?>" selYn="N" style="<?=$i > 0 ? 'border-top:1px solid #CCC;' : '';?>"><?=$row['group_nm'];?></div><?
		}

		$conn->row_free();


	}else if ($gbn == 'LIST_B'){
		//카테고리 미등록 묶음내역
		$sql = 'SELECT	suga_cd
				,		seq
				,		group_nm
				FROM	care_svc_group
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$SR.'\'
				AND		del_flag= \'N\'
				AND		category IS NULL
				ORDER	BY group_nm';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);?>
			<div id="ID_LIST_B_<?=$i;?>" cd="<?=$row['suga_cd'];?>" seq="<?=$row['seq'];?>" selYn="N" style="<?=$i > 0 ? 'border-top:1px solid #CCC;' : '';?>"><?=$row['group_nm'];?></div><?
		}

		$conn->row_free();


	}else if ($gbn == 'SET_A'){
		//카테고리 등록 해제
		$suga	= $_POST['suga'];
		$seq	= $_POST['seq'];

		$sql = 'UPDATE	care_svc_group
				SET		category	= NULL
				,		update_id	= \''.$_SESSION['userCode'].'\'
				,		update_dt	= NOW()
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$SR.'\'
				AND		suga_cd	= \''.$suga.'\'
				AND		seq		= \''.$seq.'\'
				AND		del_flag= \'N\'';

		$conn->begin();

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo 9;
			 exit;
		}

		$conn->commit();
		echo 1;


	}else if ($gbn == 'SET_B'){
		//카테고리 등록
		$suga	= $_POST['suga'];
		$seq	= $_POST['seq'];

		$sql = 'UPDATE	care_svc_group
				SET		category	= \''.$code.'\'
				,		update_id	= \''.$_SESSION['userCode'].'\'
				,		update_dt	= NOW()
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$SR.'\'
				AND		suga_cd	= \''.$suga.'\'
				AND		seq		= \''.$seq.'\'
				AND		del_flag= \'N\'';

		$conn->begin();

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo 9;
			 exit;
		}

		$conn->commit();
		echo 1;


	}else{
		exit;
	}

	include_once('../inc/_db_close.php');
?>