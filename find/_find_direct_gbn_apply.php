<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');


	$code   = $_POST['code'];
	$jumin  = $ed->de($_POST['jumin']);
	$gbn	= str_replace(',','',$_POST['direct_gbn']);
	$yymm   = str_replace('-','',$_POST['yymm']);
	
	
	$sql = 'SELECT	COUNT(*)
			FROM	mem_direct_gbn
			WHERE	org_no	= \''.$code.'\'
			AND		jumin	= \''.$jumin.'\'
			AND		yymm	= \''.$yymm.'\'';

	$cnt = $conn->get_data($sql);

	if ($cnt > 0){
		$sql = 'UPDATE	mem_direct_gbn
				SET		gbn	= \''.$gbn.'\'
				WHERE	org_no	= \''.$code.'\'
				AND		jumin	= \''.$jumin.'\'
				AND		yymm	= \''.$yymm.'\'';
	}else{
		$sql = 'INSERT INTO mem_direct_gbn (
				 org_no
				,jumin
				,yymm
				,gbn) VALUES (
				 \''.$code.'\'
				,\''.$jumin.'\'
				,\''.$yymm.'\'
				,\''.$gbn.'\'
				)';
	}

	
	$conn->begin();

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo $conn->error_msg;
		 echo 9;
		 exit;
	}

	$conn->commit();
	
	echo 'ok';


	include_once('../inc/_db_close.php');
?>