<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$type = $_POST['type'];
	$code = $_POST['code'];

	if ($type == 'BRANCH'){?>
		<option value="">-지사선택-</option><?
		$sql = 'SELECT	b00_code AS cd
				,		b00_name AS nm
				FROM	b00branch
				WHERE	b00_domain = \''.$code.'\'
				ORDER	BY nm';

	}else if ($type == 'PERSON'){?>
		<option value="">-담당자-</option><?
		$sql = 'SELECT	b01_code AS cd
				,		b01_name AS nm
				FROM	b01person
				WHERE	b01_branch = \''.$code.'\'
				ORDER	BY nm';

	}else if ($type == 'GROUP'){
		$sql = 'SELECT	group_cd AS cd
				,		group_nm AS nm
				FROM	care_group
				WHERE	area_cd = \''.$code.'\'
				ORDER	BY show_seq, group_cd';

	}else{
		$conn->close();
		exit;
	}

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);?>
		<option value="<?=$row['cd'];?>"><?=$row['nm'];?></option><?
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>