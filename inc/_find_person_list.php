<?
	include_once('../inc/_db_open.php'); //db
	//include_once('../inc/_config.php'); //설정파일
	include_once("../inc/_myFun.php");

	$comDomain = $myF->_domain();		//도메인
	$branch = $_POST[branch];
	
	switch('branch'){
		case _COM_:
			if ($comDomain == _DWCARE_)
				$mark_val = 'ON';
			else
				$mark_val = 'GE';
			break;
		case _BRAN_:
			if ($_SESSION['userLevel'] == 'A'){
				$mark_val = 'G';
			}else{
				$mark_val = $_SESSION['userBranchCode']; //지사코드
			}
			break;
		case _STORE_:
			$mark_val = 'S';
			break;
	}

	if($branch != ''){
		$sql = "select b01_branch, b01_name, b01_code
				  from b01person
				 where b01_branch = '$branch'
				 order by b01_name";
	}else {
		$sql = "select b00_code
				,      b01_name
				,	   b01_code
				  from b01person
				 inner join b00branch
					on b00_code = b01_branch
				   and b00_domain = '".$comDomain."'
				 where b01_branch like '$mark_val%'
				 order by b01_branch, b01_code";
	}

	$conn->query($sql);
	$conn->fetch();
	$rowCount = $conn->row_count();

	ob_start();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		echo $row[0].'//'.$row[1].'//'.$row[2].';;';
	}

	$list = ob_get_contents();
	ob_end_clean();

	$conn->row_free();

	include_once('../inc/_db_close.php');

	echo $list;
?>