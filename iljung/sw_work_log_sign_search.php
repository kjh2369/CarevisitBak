<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo = $_SESSION['userCenterCode'];
	$fromDt = $_POST['fromDt'] != '' ? $_POST['fromDt'] : date('Y-m-d');
	$toDt = $_POST['toDt'] != '' ? $_POST['toDt'] : date('Y-m-d');
	$orderByGbn = $_POST['orderByGbn'];
	$clientNm   = $_POST['clientName'];
	$memNm      = $_POST['memName'];

	$sql = 'SELECT	jumin
			,		date
			,		time
			,		to_time
			,		reg_name
			,		yymm
			,		seq
			,	    comment
			,	    command
			,		sign_manager
			,		clt_name
			FROM	sw_log
			LEFT    JOIN ( SELECT m03_jumin, m03_name as clt_name
						   FROM   m03sugupja
						   WHERE  m03_ccode = \''.$orgNo.'\'
						   AND    m03_mkind = \'0\'
						 ) as clt
			ON		clt.m03_jumin = sw_log.jumin
			WHERE	org_no	= \''.$orgNo.'\'
			AND		del_flag= \'N\'
			AND		date >= \''.str_replace('-','',$fromDt).'\'
			AND		date <= \''.str_replace('-','',$toDt).'\'';

	if($orderByGbn != '1'){ 
		if($orderByGbn == '2'){
			$sql .= 'AND command is not null ';
		}else {
			$sql .= 'AND command is null ';
		}
	}
	
	if($clientNm) $sql .= 'AND clt_name like \'%'.$clientNm.'%\'';

	if($memNm) $sql .= 'AND reg_name like \'%'.$memNm.'%\'';

	$sql .=	'ORDER	BY date desc, time desc';
	

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	$seq = 1;

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		/*
		$sql = 'select m03_name
				  from m03sugupja
				 where m03_ccode = \''.$orgNo.'\'
				   and m03_mkind = \'0\'
				   and m03_jumin = \''.$row['jumin'].'\'';
		$clt_nm = $conn -> get_data($sql);
		*/

		//요양보호사
		$sql = 'SELECT	t01_yoyangsa_id1 AS mem_cd1
				,		t01_yname1 AS mem_nm1
				,		t01_yoyangsa_id2
				,		t01_yname2 AS mem_nm2
				,		t01_status_gbn AS stat
				,		t01_sugup_fmtime AS plan_from
				,		t01_sugup_totime AS plan_to
				,		t01_wrk_fmtime AS work_from
				,		t01_wrk_totime AS work_to
				,		t01_conf_fmtime AS conf_from
				,		t01_conf_totime AS conf_to
				FROM	t01iljung
				WHERE	t01_ccode = \''.$orgNo.'\'
				AND		t01_mkind = \'0\'
				AND		t01_jumin = \''.$row['jumin'].'\'
				AND		t01_sugup_date = \''.$row['date'].'\'
				AND		t01_sugup_fmtime <= \''.$row['time'].'\'
				AND		t01_sugup_totime >= \''.$row['time'].'\'
				AND		t01_del_yn = \'N\'
				ORDER   BY t01_sugup_fmtime desc';

		$mem1 = $conn->get_array($sql);

		//요양보호사
		$sql = 'SELECT	t01_yoyangsa_id1 AS mem_cd1
				,		t01_yname1 AS mem_nm1
				,		t01_yoyangsa_id2
				,		t01_yname2 AS mem_nm2
				,		t01_status_gbn AS stat
				,		t01_sugup_fmtime AS plan_from
				,		t01_sugup_totime AS plan_to
				,		t01_wrk_fmtime AS work_from
				,		t01_wrk_totime AS work_to
				,		t01_conf_fmtime AS conf_from
				,		t01_conf_totime AS conf_to
				FROM	t01iljung
				WHERE	t01_ccode = \''.$orgNo.'\'
				AND		t01_mkind = \'0\'
				AND		t01_jumin = \''.$row['jumin'].'\'
				AND		t01_sugup_date = \''.$row['date'].'\'
				AND		t01_sugup_fmtime <= \''.$row['to_time'].'\'
				AND		t01_sugup_totime >= \''.$row['to_time'].'\'
				AND		t01_del_yn = \'N\'
				ORDER   BY t01_sugup_fmtime desc';

		$mem2 = $conn->get_array($sql);

		
		$mem_nm = $mem1['mem_nm1'] != '' ? $mem1['mem_nm1'] : $mem2['mem_nm1'];
		
		if($row['sign_manager'] != ''){
			$sign_yn = 'Y';
		}else {
			$sign_yn = '';
		}

		?>
		<tr>
			<td class="center"><?=$myF->dateStyle($row['date'],'.');?> <?=$myF->timeStyle($row['time']);?></td>
			<td class="left"><?=$row['clt_name'];?></td>
			<td class="left"><div class="nowrap" style="width:70px;"><?=$row['reg_name'];?></div></td>
			<td class="left"><div class="nowrap" style="width:70px;"><?=$mem_nm;?></div></td>
			<td class="left"><div class="nowrap" style="width:180px;"><?=$row['comment'];?></div></td>
			<td class="left"><div class="nowrap" style="width:180px;"><?=$row['command'];?></div></td>
			<td class="center"><?=$sign_yn;?></td>
			<td class="center" ><span class='btn_pack m'><button type='button' onclick="lfWokrLogReg('<?=$ed->en($row['jumin']);?>','<?=substr($row['date'], 0, 4);?>','<?=substr($row['date'], 4, 2);?>', '<?=$row['seq'];?>');">수정</button></span></td>
			<td class="center last"><span class='btn_pack m'><button type='button' onclick="lfPrint('<?=$ed->en($row['jumin']);?>','<?=substr($row['date'], 0, 6);?>', '<?=$row['seq'];?>');">출력</button></span></td>
		</tr><?

	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>
