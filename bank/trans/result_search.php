<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	if (!IsSet($_SESSION['USER_CODE'])){
		exit;
	}

	$from = $_POST['from'];
	$to   = $_POST['to'];

	$sql = 'SELECT trans.org_no
			,      trans.request_dt
			,      mst.m00_store_nm AS name
			,      trans.type
			,      bank.name AS bank_nm
			,      trans.bank_no
			,      trans.bank_acct
			,      trans.amt
			,      trans.stat
			,      trans.result_dt
			,      trans.result_other
			  FROM trans
			 INNER JOIN m00center AS mst
				ON mst.m00_mcode = trans.org_no
			   AND mst.m00_mkind = \'0\'
			 INNER JOIN bank
			    ON bank.code = trans.bank_nm
			 WHERE DATE_FORMAT(trans.result_dt,\'%Y%m%d\') >= \''.$from.'\'
			   AND DATE_FORMAT(trans.result_dt,\'%Y%m%d\') <= \''.$to.'\'
			 ORDER BY result_dt DESC';

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		$data .= ($i + 1).chr(2)
			  .  $row['result_dt'].chr(2)
			  .  $row['name'].chr(2)
			  .  $row['type'].chr(2)
			  .  $row['bank_nm'].chr(2)
			  .  $row['bank_no'].chr(2)
			  .  $row['bank_acct'].chr(2)
			  .  $row['amt'].chr(2)
			  .  $row['stat'].chr(2)
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