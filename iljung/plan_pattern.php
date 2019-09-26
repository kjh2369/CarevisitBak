<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code  = $_POST['code'];
	$jumin = $_POST['jumin'];
	$svcCd = $_POST['svcCd'];
	$yymm  = $_POST['yymm'];
	$key   = $_POST['key'];
	$mode  = $_POST['mode'];

	if(!is_numeric($jumin)) $jumin = $ed->de($jumin);

	if ($mode == 'set'){
		//저장
		$para = Str_Replace(chr(9),'',$_POST['para']);

		$sql = 'replace into pattern (
				 org_no
				,jumin
				,svc_cd
				,yymm
				,cd
				,pattern
				,insert_dt) values (
				 \''.$code.'\'
				,\''.$jumin.'\'
				,\''.$svcCd.'\'
				,\''.$yymm.'\'
				,\''.$key.'\'
				,\''.$para.'\'
				,now())';

		if ($conn->execute($sql)){
			echo 1;
		}else{
			echo 9;
		}
	}else if ($mode == 'remove'){
		$year  = intval(substr($yymm,0,4));
		$month = intval(substr($yymm,4,2));

		$month --;

		if ($month < 1){
			$month = 12;
			$year --;
		}

		$month = (intval($month) < 10 ? '0' : '').intval($month);

		$sql = 'delete
				  from pattern
				 where org_no = \''.$code.'\'
				   and jumin  = \''.$jumin.'\'
				   and svc_cd = \''.$svcCd.'\'
				   and yymm   = \''.$year.$month.'\'
				   and cd     = \''.$key.'\'';

		$conn->execute($sql);
	}else{
		$year  = intval(substr($yymm,0,4));
		$month = intval(substr($yymm,4,2));

		$month --;

		if ($month < 1){
			$month = 12;
			$year --;
		}

		$month = (intval($month) < 10 ? '0' : '').intval($month);

		$sql = 'select cd
				,      pattern
				  from pattern
				 where org_no = \''.$code.'\'
				   and jumin  = \''.$jumin.'\'
				   and svc_cd = \''.$svcCd.'\'
				   and yymm   = \''.$year.$month.'\'
				 order by insert_dt desc';

		$conn->query($sql);
		$conn->fetch();

		$rowCount = $conn->row_count();
		$lsStr = '';

		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);
			$lsStr .= $row['cd'].'|'.Str_Replace(chr(9),'',$row['pattern']).';';
		}

		$conn->row_free();

		echo $lsStr;
	}

	include_once('../inc/_db_close.php');
?>