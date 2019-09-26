<?
	include_once('../../inc/_db_open.php');
	include_once('../../inc/_login.php');
	include_once('../../inc/_myFun.php');
	include_once('../../inc/_ed.php');

	$mode = $_POST['mode'];
	$code = $_SESSION['userCenterCode'];

	if ($mode == '101'){
		$sql = 'SELECT mem.m02_yjumin AS jumin
				,      mem.m02_yname AS name
				,      his.work_start_dt AS work_dt
				,      his.seq
				  FROM m02yoyangsa AS mem
				 INNER JOIN mem_his AS his
					ON his.org_no = mem.m02_ccode
				   AND his.jumin  = mem.m02_yjumin
				   /*AND his.employ_stat = \'1\'*/
				 WHERE mem.m02_ccode       = \''.$code.'\'
				   AND mem.m02_mkind       = \'0\'
				   AND mem.m02_jikwon_gbn != \'\'
				   AND mem.m02_jikwon_gbn != \'M\'
				 ORDER BY name';

		$conn->query($sql);
		$conn->fetch();

		$rowCount = $conn->row_count();

		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			$data .= $row['seq'].Chr(2)
				  .  $row['jumin'].Chr(2)
				  .  $row['name'].Chr(2)
				  .  $myF->dateStyle($row['work_dt'],'.').Chr(1);
		}

		$conn->row_free();

		echo $data;

	}else{
		$conn->close();
		echo 9;
		exit;
	}

	include_once('../../inc/_db_close.php');
?>