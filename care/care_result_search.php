<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$gbn	= $_SESSION['userLevel'];
	$jumin	= $_SESSION['userSSN'];
	$SR		= $_POST['SR'];
	$year	= $_POST['year'];
	$month	= IntVal($_POST['month']);
	$month	= ($month < 10 ? '0' : '').$month;
	$fromDt	= str_replace('-','',$_POST['from']);
	$toDt	= str_replace('-','',$_POST['to']);
	$order	= $_POST['order'];
	$resultGbn	= $_POST['resultGbn'];

	if (!$fromDt) $fromDt = $year.$month.'01';
	if (!$toDt) $toDt = $year.$month.'31';

	$sql = 'SELECT	t01_jumin AS jumin
			,		t01_sugup_date AS date
			,		t01_sugup_fmtime AS time
			,		t01_sugup_seq AS seq
			,		m03_name AS name
			,		suga.suga_nm AS suga_nm
			,		t01_yname1 AS customer
			,		t01_yname2 AS member
			,		t01_status_gbn AS stat
			,		result.content AS cont
			,		result.picture AS pic
			,		result.no AS idx
			,		IFNULL(mst_jumin.jumin,t01_jumin) AS real_jumin
			FROM	t01iljung
			INNER	JOIN	m03sugupja
					ON		m03_ccode = t01_ccode
					AND		m03_mkind = \'6\'
					AND		m03_jumin = t01_jumin
			INNER	JOIN	mst_jumin
					ON		mst_jumin.org_no = t01_ccode
					AND		mst_jumin.gbn = \'1\'
					AND		mst_jumin.code = t01_jumin
			INNER	JOIN	care_suga AS suga
					ON		suga.org_no = t01_ccode
					AND		suga.suga_sr= t01_mkind
					AND		CONCAT(suga.suga_cd,suga.suga_sub) = t01_suga_code1
					AND		REPLACE(suga.from_dt,\'-\',\'\')<= t01_sugup_date
					AND		REPLACE(suga.to_dt,\'-\',\'\')	>= t01_sugup_date
			LEFT	JOIN	care_result AS result
					ON		result.org_no	= t01_ccode
					AND		result.org_type	= t01_mkind
					AND		result.jumin	= t01_jumin
					AND		result.date		= t01_sugup_date
					AND		result.time		= t01_sugup_fmtime
					AND		result.seq		= t01_sugup_seq
					AND		result.del_flag	= \'N\'
			WHERE	t01_ccode = \''.$orgNo.'\'
			AND		t01_mkind = \''.$SR.'\'
			AND		t01_sugup_date >= \''.$fromDt.'\'
			AND		t01_sugup_date <= \''.$toDt.'\'
			AND		t01_del_yn = \'N\'';

	if ($gbn != 'C'){
		$sql .= '
			AND		t01_yoyangsa_id2 = \''.$jumin.'\'';
	}

	if ($resultGbn == 'Y'){
		$sql .= '
			AND		t01_status_gbn = \'1\'';
	}else if ($resultGbn == 'N'){
		$sql .= '
			AND		t01_status_gbn != \'1\'';
	}

	if ($order == '1'){
		$sql .= ' ORDER BY date,time,name';
	}else if ($order == '2'){
		$sql .= ' ORDER BY member,date,time';
	}else if ($order == '3'){
		$sql .= ' ORDER BY suga_nm,date,time';
	}else if ($order == '4'){
		$sql .= ' ORDER BY name,date,time';
	}

	$weekly = Array(0=>'<span style="color:#FF0000;">일</span>',1=>'월',2=>'화',3=>'수',4=>'목',5=>'금',6=>'<span style="color:#0000FF;">토</span>');

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	$no = 1;

	if ($rowCnt > 0){
		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);

			$date	= $row['date'];
			$week	= Date('w',StrToTime($date));
			$date	= IntVal(Date('d',StrToTime($date)));

			if ($row['pic']){
				$pic = '<a id="btnPic" href="#" onclick="lfPictureReg(this); return false;"><img src="../image/f_list.gif"></a>';
			}else{
				$pic = '<a id="btnPic" href="#" onclick="lfPictureReg(this); return false;">등록</a>';
			}

			//성별
			$gender = SubStr($row['real_jumin'],6,1);

			if ($gender){
				if ($gender % 2 == 1){
					$gender = '<span style="color:BLUE;">남</span>';
				}else{
					$gender = '<span style="color:RED;">여</span>';
				}
			}?>
			<tr id="ROW_<?=$no;?>" para="jumin=<?=$ed->en($row['jumin']);?>&date=<?=$row['date'];?>&time=<?=$row['time'];?>&seq=<?=$row['seq'];?>&no=<?=$row['idx'];?>" no="<?=$row['idx'];?>" stat="<?=$row['stat'];?>" style="cursor:default;" onmouseover="this.style.backgroundColor='#D9E5FF';" onmouseout="this.style.backgroundColor='#FFFFFF';">
				<td class="center"><?=$no;?></td>
				<td class="center"><?=$date;?>(<?=$weekly[$week];?>)</td>
				<td class="center"><?=$myF->timeStyle($row['time']);?></td>
				<td class="center"><div class="left nowrap" style="width:70px;"><?=$row['name'];?></div></td>
				<td class="center"><?=$myF->issToBirthday($row['real_jumin'],'.');?></td>
				<td class="center"><?=$gender;?></td>
				<td class="center"><div class="left nowrap" style="width:100px;"><?=$row['suga_nm'];?></div></td>
				<td class="center"><div class="left nowrap" style="width:100px;"><?=$row['customer'];?></div></td>
				<td class="center"><div class="left nowrap" style="width:70px;"><?=$row['member'];?></div></td>
				<td class="center"><div class="left nowrap" style="width:200px;" onclick="lfContent(this);"><?=StripSlashes($row['cont']);?></div></td>
				<td class="center"><?=$pic;?></td>
				<td class="center last"></td>
			</tr><?
			$no ++;
		}
	}else{?>
		<tr>
			<td class="center last" colspan="12">::검색된 데이타가 없습니다.::</td>
		</tr><?
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>