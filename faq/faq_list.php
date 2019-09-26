<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	//상담구분리스트
	$sql = 'SELECT gbn_cd AS cd
			,      gbn_nm AS nm
			  FROM faq_gbn
			 WHERE del_flag = \'N\'';

	$arrGbn = $conn->_fetch_array($sql,'cd');

	$page = $_POST['page'];

	$itemCount = 20;
	$pageCount = 10;

	$pageCnt = $page;

	if (Empty($pageCount)){
		$pageCount = 1;
	}

	$pageCnt = (intVal($pageCnt) - 1) * $itemCount;

	if ($page == 0){
		$sql = 'SELECT COUNT(*)
				  FROM faq
				 WHERE domain = \''.$gDomainID.'\'';

		$maxCount = $conn->get_data($sql);

		echo $maxCount;

		$conn->close();
		exit;
	}

	$sql = 'SELECT id, faq_gbn, quest, reg_dt, reg_name, count, domain
			  FROM faq
			 WHERE domain = \''.$gDomainID.'\'
			 ORDER BY id
			 LIMIT '.$pageCnt.','.$itemCount;

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		$no = $max - ($pageCnt + $i);

		$data .= $row['id'].chr(3).$no.chr(2) //KEY/번호
			  .  $arrGbn[$row['faq_gbn']]['nm'].chr(2) //
			  .  $row['quest'].chr(2) //제목
			  .  $row['reg_name'].chr(2) //작성일
			  .  Str_Replace('-','.',$row['reg_dt']).chr(2) //작성자
			  .  $row['count'].chr(2) //카운트
			  .  $row['domain'].chr(1);
	}

	$conn->row_free();
	$conn->close();

	echo $data;

	include_once('../inc/_db_close.php');
?>