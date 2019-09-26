<?
	$init_year = $myF->year();

	$year = $_REQUEST['year'] != '' ? $_REQUEST['year'] : date('Y', mktime());
	$month = $_REQUEST['month'] != '' ? $_REQUEST['month'] : date('m', mktime());
	$month = (intval($month) < 10 ? '0' : '').intval($month);

	if ($_SESSION["userLevel"] == "A"){
		$find_admin = true;
		$find_center_code = $_REQUEST['find_center_code'];
		$find_center_name = $_REQUEST['find_center_name'];
	}else{
		$find_admin = false;
		$find_center_code = $_SESSION['userCenterCode'];
		$find_center_name = $_SESSION['userCenterName'];
	}

	$find_year = $_POST['find_year'] != '' ? $_POST['find_year'] : date('Y', mktime());

	// 회계사코드
	$sql = "select m00_account_firm_cd
			  from m00center as mst
			 where m00_mcode = '$find_center_code'
			   and m00_mkind = (select min(chd.m00_mkind) from m00center as chd where chd.m00_mcode = mst.m00_mcode)";
	$account_firm_code = $conn->get_data($sql);

	$find_from_date = $_REQUEST['find_from_date'] != '' ? $_REQUEST['find_from_date'] : date('Y-m', mkTime()).'-01';
	$find_to_date	= $_REQUEST['find_to_date'] != '' ? $_REQUEST['find_to_date'] : $myF->dateAdd('day', -1, $myF->dateAdd('month', 1, $find_from_date, 'Y-m-d'), 'Y-m-d');

	/*
	 * io_type
	 * i : 입금내역
	 * o : 지출내역
	 */
	$io_type = $_REQUEST['io_type'];

	/*
	 * mode
	 * 1 : 조회
	 * 2 : 등록
	 * 3 : 수정
	 * 4 : 삭제
	 */
	$mode = $_GET['mode'];

	if ($io_type == 'i'){
		$io_title = '입금';
		$io_table = 'income';
		$io_filed = 'income';
	}else{
		$io_title = '지출';
		$io_table = 'outgo';
		$io_filed = 'outgo';
	}
?>
<input name="find_center_code"	type="hidden" value="<?=$find_center_code;?>">
<input name="find_from_date"	type="hidden" value="<?=$find_from_date;?>">
<input name="find_to_date"		type="hidden" value="<?=$find_to_date;?>">
<input name="account_firm_code" type="hidden" value="<?=$account_firm_code;?>">
<input name="io_type"			type="hidden" value="<?=$io_type;?>">
<input name="month"				type="hidden" value="<?=$month;?>">
<input name="mode"				type="hidden" value="<?=$mode;?>">
