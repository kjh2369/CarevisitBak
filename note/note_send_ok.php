<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$conn->mode = 1;

	$code      = $_POST['code'];
	$mode      = $_POST['mode'];
	$yymm      = date('Ym', mktime());
	$send_type = $_POST['send_type'];
	$send_no   = $_POST[$send_type.'_no'];
	$send_list = $_POST[$send_type.'_cd'];
	$send_name = $_POST[$send_type.'_nm'];
	$subject   = addslashes($_POST['subject']);
	$content   = addslashes($_POST['content']);
	$today     = date('Y-m-d', mktime());
	$reg_id    = $_SESSION['userNo'];
	$reg_nm    = $_SESSION['userName'];

	echo $myF->header_script();

	$sql = "";

	switch($send_type){
		case 'all':
			if ($_SESSION['userLevel'] == 'A'){//본사
				$sql = "select m02_ccode, m02_mem_no, m02_yname
						  from m02yoyangsa
						 where m02_mkind  =  ".$conn->_mem_kind()."
						   and m02_del_yn = 'N'";

			}else if ($_SESSION['userLevel'] == 'B'){//지사
				$sql = "select distinct b02_center as cd
						  from b02center
						 where b02_branch = '".$_SESSION['userBranchCode']."'";

				$conn->query($sql);
				$conn->fetch();

				$row_count = $conn->row_count();

				$sql = "";

				for($i=0; $i<$row_count; $i++){
					$row = $conn->select_row($i);

					if (!empty($sql)) $sql .= " union all ";
					$sql .= "select m02_ccode, m02_mem_no, m02_yname
							   from m02yoyangsa
							  where m02_ccode  = '".$row['cd']."'
								and m02_mkind  =  ".$conn->_mem_kind()."
								and m02_del_yn = 'N'";
				}

				$conn->row_free();

			}else{//가맹점, 개별
				$sql .= "select m02_ccode, m02_mem_no, m02_yname
						   from m02yoyangsa
						  where m02_ccode  = '".$_SESSION['userCenterCode']."'
							and m02_mkind  =  ".$conn->_mem_kind()."
							and m02_del_yn = 'N'";
			}

			break;

		case 'branch':
			for($i=0; $i<sizeof($send_list); $i++){
				if (!empty($sql)) $sql .= " union all ";

				$sql .= "select m02_ccode, m02_mem_no, m02_yname
						   from m02yoyangsa
						  where m02_ccode  = (select b02_center from b02center where b02_branch = '".$send_list[$i]."' limit 1)
							and m02_mkind  =  ".$conn->_mem_kind()."
							and m02_del_yn = 'N'";
			}
			break;

		case 'center':
			for($i=0; $i<sizeof($send_list); $i++){
				if (!empty($sql)) $sql .= " union all ";
				$sql .= "select m02_ccode, m02_mem_no, m02_yname
						   from m02yoyangsa
						  where m02_ccode  = '".$send_list[$i]."'
						    and m02_mkind  =  ".$conn->_mem_kind()."
						    and m02_del_yn = 'N'";
			}
			break;

		case 'dept':
			for($i=0; $i<sizeof($send_list); $i++){
				if (!empty($sql)) $sql .= " union all ";
				$sql .= "select m02_ccode, m02_mem_no, m02_yname
						   from m02yoyangsa
						  where m02_ccode   = '".$send_no[$i]."'
						    and m02_mkind   =  ".$conn->_mem_kind()."
						    and m02_dept_cd = '".$send_list[$i]."'
							and m02_del_yn  = 'N'";
			}
			break;
		case 'person':
			for($i=0; $i<sizeof($send_list); $i++){
				$mem_list[$i]['ct'] = $send_no[$i];
				$mem_list[$i]['cd'] = $send_list[$i];
				$mem_list[$i]['nm'] = $send_name[$i];
			}
			break;
	}

	if (!empty($sql)){
		$conn->query($sql);
		$conn->fetch();

		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);
			$mem_list[$i]['ct'] = $row['m02_ccode'];
			$mem_list[$i]['cd'] = $row['m02_mem_no'];
			$mem_list[$i]['nm'] = $row['m02_yname'];
		}

		$conn->row_free();
	}

	$conn->begin();

	$sql = "select ifnull(max(msg_seq), 0) + 1
			  from msg_send
			 where org_no   = '$code'
			   and msg_yymm = '$yymm'";

	$seq = $conn->get_data($sql);

	$sql = "insert into msg_send (org_no, msg_yymm, msg_seq, msg_send_type, msg_subject, msg_content, msg_send_id, msg_send_nm, msg_send_dt, insert_id, insert_dt) values (
			 '$code'
			,'$yymm'
			,'$seq'
			,'$send_type'
			,'$subject'
			,'$content'
			,'$reg_id'
			,'$reg_nm'
			,now()
			,'$reg_id'
			,'$today')";

	if (!$conn->execute($sql)){
		$conn->rollback();
		echo $conn->err_back();
		if($conn->mode == 1) exit;
	}

	$mem_cnt = sizeof($mem_list);

	$sql = "insert into msg_receipt (from_no, org_no, msg_yymm, msg_seq, msg_mem, msg_mem_nm) values ";

	for($i=0; $i<$mem_cnt; $i++){
		$sql .= ($i > 0 ? ',' : '');
		$sql .= "('$code','".$mem_list[$i]['ct']."','$yymm','$seq','".$mem_list[$i]['cd']."','".$mem_list[$i]['nm']."')";
	}

	if (!$conn->execute($sql)){
		$conn->rollback();
		echo $conn->err_back();
		if($conn->mode == 1) exit;
	}

	$conn->commit();

	include_once('../inc/_db_close.php');

	echo '<script>
			alert(\''.$myF->message('ok','N').'\');';

	if ($conn->mode == 1)
		echo 'location.replace(\'note_list.php?mode=from&page=1\');';

	echo '</script>';
?>