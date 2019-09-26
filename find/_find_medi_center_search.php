<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	
	$conn -> close();	//케어비지트 닫기
	###############################################


	//방문간호 디비 접속
	$conn -> user = "nhcs";
	$conn -> pass = "nhcs9482";
	$conn -> db_name = "nhcs";
	$conn -> connection();

	$page = IntVal($_POST['page']);
	$max  = IntVal($_POST['max']);
	$code = $_POST['code'];
	$name = $_POST['name'];
	$pCnt = $_POST['pCnt'];

	if (!$pCnt) $pCnt = 10;

	
	$table = '';
	

	$itemCount = 20;
	$pageCount = $pCnt;

	$pageCnt = $page;

	if (Empty($pageCount)){
		$pageCount = 1;
	}

	$pageCnt = (intVal($pageCnt) - 1) * $itemCount;

	if ($page == 0){
		
		$sql = ' select count(*)
				   from medical_org 
				  where del_flag = \'N\'';


		$maxCount = $conn->get_data($sql);

		echo $maxCount;

		$conn->close();
		exit;
	}
	

	$sql = ' select medical_org_no as code
			 ,	    medical_org_name as name
			 ,		ceo_name as manager
			 ,	    concat(addr1,\' \',addr2) as addr
			   from medical_org
			  where del_flag = \'N\'';
	
		
	$sql .= ' order by medical_org_name
			  limit '.$pageCnt.','.$itemCount.'';

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		$data .= ($pageCnt + ($i + 1)).chr(2)
			  .  $row['code'].chr(2)
			  .  $row['name'].chr(2)
			  .  $row['manager'].chr(2)
			  .  $row['addr'].chr(1);
	}

	$conn->row_free();

	echo $data;

	include_once('../inc/_db_close.php');
?>