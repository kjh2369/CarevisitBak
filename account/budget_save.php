<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');

	$orgNo = $_SESSION['userCenterCode'];
	$year = $_POST['year'];
	$re_gbn = $_POST['re_gbn'];
	$data = Explode('?', $_POST['para']);
	
	//print_r($_SESSION); exit;

	$sql = 'DELETE
			FROM	fa_budget
			WHERE	org_no	= \''.$orgNo.'\'
			AND		year	= \''.$year.'\'
			AND     re_gbn  = \''.$re_gbn.'\'
			';
	$query[] = $sql;

	foreach($data as $tmpI => $R){
		parse_str($R, $R);

		$sql = 'INSERT INTO fa_budget VALUES (
				 \''.$orgNo.'\'
				,\''.$year.'\'
				,\''.$R['gwan_cd'].'\'
				,\''.$R['hang_cd'].'\'
				,\''.$R['mog_cd'].'\'
				,\''.$re_gbn.'\'
				,\''.$R['amt'].'\'
				,\''.$_SESSION['userCode'].'\'
				,NOW()
				)';

		
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
	
	//echo $conn->query_exec($query);
?>