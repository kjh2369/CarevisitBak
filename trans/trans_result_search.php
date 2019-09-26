<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_page_list.php');

	$code = $_SESSION['userCenterCode'];
	$type = $_POST['type'];
	$from = $_POST['from'];
	$to   = $_POST['to'];
	$page = IntVal($_POST['page']);
	$max  = IntVal($_POST['max']);

	if ($page == 0){
		$sql = 'SELECT COUNT(*)
				  FROM trans
				 WHERE org_no = \''.$code.'\'
				   AND DATE_FORMAT('.($type == '1' ? 'request_dt' : 'result_dt').',\'%Y%m%d\') >= \''.$from.'\'
				   AND DATE_FORMAT('.($type == '1' ? 'request_dt' : 'result_dt').',\'%Y%m%d\') <= \''.$to.'\'';

		$rowCount = $conn->get_data($sql);

		echo $rowCount;

		$conn->close();
		exit;
	}

	$itemCount = 20;
	$pageCount = 10;

	if ($page < 1) $page = 1;
	if ($max < (intVal($page) - 1) * $itemCount) $page = 1;

	$pageCount = ($page - 1) * $itemCount;

	$sql = 'SELECT request_dt
			,      type
			,      bank_nm
			,      bank_no
			,      bank_acct
			,      amt
			,      stat
			,      result_dt
			,      result_other
			  FROM trans
			 WHERE org_no = \''.$code.'\'
			   AND DATE_FORMAT('.($type == '1' ? 'request_dt' : 'result_dt').',\'%Y%m%d\') >= \''.$from.'\'
			   AND DATE_FORMAT('.($type == '1' ? 'request_dt' : 'result_dt').',\'%Y%m%d\') <= \''.$to.'\'
			 ORDER BY '.($type == '1' ? 'request_dt' : 'result_dt').' DESC
			 /*LIMIT '.$pageCount.','.$itemCount.'*/';

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		$data .= ($pageCount + ($i + 1)).chr(2)
			  .  $row['request_dt'].chr(2)
			  .  $row['type'].chr(2)
			  .  $row['bank_nm'].chr(2)
			  .  $row['bank_no'].chr(2)
			  .  $row['bank_acct'].chr(2)
			  .  $row['amt'].chr(2)
			  .  $row['stat'].chr(2)
			  .  $row['result_dt'].chr(2)
			  .  $row['result_other'].chr(1);
	}

	$conn->row_free();

	/*
	if ($page == 1){
		$params = array(
			'curMethod'		=> 'post',
			'curPage'		=> 'javascript:lfSearch',
			'curPageNum'	=> $page,
			'pageVar'		=> 'page',
			'extraVar'		=> '',
			'totalItem'		=> $max,
			'perPage'		=> $pageCount,
			'perItem'		=> $itemCount,
			'prevPage'		=> '[이전]',
			'nextPage'		=> '[다음]',
			'prevPerPage'	=> '[이전'.$pageCount.'페이지]',
			'nextPerPage'	=> '[다음'.$pageCount.'페이지]',
			'firstPage'		=> '[처음]',
			'lastPage'		=> '[끝]',
			'pageCss'		=> 'page_list_1',
			'curPageCss'	=> 'page_list_2'
		);

		$paging = new YsPaging($params);
		$data .= chr(3).$paging->returnPaging();
	}
	*/

	echo $data;

	include_once('../inc/_db_close.php');
?>