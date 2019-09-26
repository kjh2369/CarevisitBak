<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	
	$page = IntVal($_POST['page']);
	$max  = IntVal($_POST['max']);
	$code = $_POST['code'];
	$name = $_POST['name'];
	
	
	$sql = ' SELECT	mr.org_no 
			,		mst.org_nm
			,		mr.request_area as area
			FROM	medical_request AS mr
			INNER	JOIN (
						SELECT	DISTINCT
								m00_mcode AS org_no
						,	    m00_store_nm As org_nm
						FROM	m00center
					) AS mst
					ON		mst.org_no = mr.org_no
			WHERE mr.complete_yn = \'Y\'
			  AND mr.del_flag    = \'N\'';
	
		
	$sql .= ' order by org_nm ';

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);
		
		if($row['area'] == '01'){
			$area = '서울-서대문';
		}else if($row['area'] == '02'){
			$area = '서울-은평,강동';
		}else if($row['area'] == '03'){
			$area = '부산-동래';
		}else if($row['area'] == '04'){
			$area = '대구-달서';
		}else if($row['area'] == '05'){
			$area = '인천-부평';
		}else if($row['area'] == '06'){
			$area = '광주-서구';
		}else if($row['area'] == '07'){
			$area = '경기-일산';
		}else if($row['area'] == '08'){
			$area = '경기-광명';
		}else if($row['area'] == '09'){
			$area = '경남-창원';
		}else if($row['area'] == '10'){
			$area = '세종시-조치원읍';
		}else if($row['area'] == '11'){
			$area = '충북-청주';
		}else if($row['area'] == '12'){
			$area = '경남-함양';
		}else if($row['area'] == '13'){
			$area = '경북-김천,상주';
		}else if($row['area'] == '14'){
			$area = '강원-삼척';
		}else if($row['area'] == '15'){
			$area = '강원-원주,횡성';
		}else if($row['area'] == '16'){
			$area = '전남-목포,영암';
		}


		$sql = 'SELECT count(*)
				FROM   medical_connect
				WHERE  org_no = \''.$row['org_no'].'\'';
		$regCnt = $conn -> get_data($sql);
		
		if($regCnt>0){
			$regYn = 'Y';
		}else {
			$regYn = 'N';
		}


		$data .= ($pageCnt + ($i + 1)).chr(2)
			  .  $row['org_no'].chr(2)
			  .  str_replace('"','',$row['org_nm']).chr(2)
			  .  $regYn.chr(2)
			  .  $area.chr(1);
	}

	$conn->row_free();

	echo $data;

	include_once('../inc/_db_close.php');
?>