<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$svcCd	= $_POST['svcCd'];
	$jumin	= $ed->de($_POST['jumin']);
	$date	= $_POST['date'];
	$time	= $_POST['time'];
	$seq	= $_POST['seq'];

	//수급자 정보
	$sql = 'SELECT	m03_name
			FROM	m03sugupja
			WHERE	m03_ccode = \''.$orgNo.'\'
			AND		m03_mkind = \''.$svcCd.'\'
			AND		m03_jumin = \''.$jumin.'\'';

	$name = $conn->get_data($sql);

	//등급 및 인정번호
	$sql = 'SELECT	app_no, level
			FROM	client_his_lvl
			WHERE	org_no	 = \''.$orgNo.'\'
			AND		svc_cd	 = \''.$svcCd.'\'
			AND		jumin	 = \''.$jumin.'\'
			AND		from_dt <= \''.$date.'\'
			AND		to_dt	>= \''.$date.'\'';

	$row = $conn->get_array($sql);

	$lvl = $myF->_lvlNm($row['level']);
	$appNo = $row['app_no'];

	Unset($row);

	//구분 및 본인부담율
	$sql = 'SELECT	kind, rate
			FROM	client_his_kind
			WHERE	org_no	 = \''.$orgNo.'\'
			AND		jumin	 = \''.$jumin.'\'
			AND		from_dt <= \''.$date.'\'
			AND		to_dt	>= \''.$date.'\'';

	$row = $conn->get_array($sql);

	$kind = $myF->_kindSub($row['kind']);
	$rage = $row['rate'];

	Unset($row);

	//일정정보
	$sql = 'SELECT	t01_svc_subcode AS sub_cd
			,		t01_mem_cd1 AS mem_cd1
			,		t01_mem_cd2 AS mem_cd2
			,		t01_mem_nm1 AS mem_nm1
			,		t01_mem_nm2 AS mem_nm2
			,		t01_sugup_fmtime AS from_time
			,		t01_sugup_totime AS to_time
			,		t01_suga_code1 AS suga_cd
			,		t01_suga_tot AS suga_tot
			FROM	t01iljung
			WHERE	t01_ccode		= \''.$orgNo.'\'
			AND		t01_mkind		= \''.$svcCd.'\'
			AND		t01_jumin		= \''.$jumin.'\'
			AND		t01_sugup_date	= \''.$date.'\'
			AND		t01_sugup_fmtime= \''.$time.'\'
			AND		t01_sugup_seq	= \''.$seq.'\'
			AND		t01_del_yn		= \'N\'';

	$row = $conn->get_array($sql);

	switch($row['sub_cd']){
		case '200':
			$subNm = '방문요양';
			break;

		case '500':
			$subNm = '방문목욕';
			break;

		case '800':
			$subNm = '방문간호';
			break;
	}

	$memCd1 = $ed->en($row['mem_cd1']);
	$memCd2 = $ed->en($row['mem_cd2']);
	$memNm1 = $row['mem_nm1'];
	$memNm2 = $row['mem_nm2'];

	$fromTime = $myF->timeStyle($row['from_time']);
	$toTime = $myF->timeStyle($row['to_time']);

	Unset($row);
?>
<script type="text/javascript">
	$(document).ready(function(){
		__init_form(document.f);
	});
</script>
<div class="title title_border">일정변경등록</div>
<form name="f">
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col width="170px">
		<col width="70px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>수급자</th>
			<td><div class="left"><?=$name;?>(<?=$appNo;?>)</div></td>
			<th>등급</th>
			<td><div class="left"><?=$lvl;?>(<?=$kind;?>)</div></td>
		</tr>
		<tr>
			<th>서비스</th>
			<td><div class="left">재가요양</div></td>
			<th>구분</th>
			<td><div class="left"><?=$subNm;?></div></td>
		</tr>
		<tr>
			<th>요양보호사</th>
			<td>
				<input id="" type="text" value="<?=$memNm1;?>" code="<?=$memCd1;?>" style="width:60px;" readonly> /
				<input id="" type="text" value="<?=$memNm2;?>" code="<?=$memCd2;?>" style="width:60px;" readonly>
			</td>
			<th>방문시간</th>
			<td>
				<input id="" type="text" value="<?=$fromTime;?>" class="no_string" alt="time"> ~
				<input id="" type="text" value="<?=$toTime;?>" class="no_string" alt="time">
			</td>
		</tr>
		<tr>
			<th>수가명</th>
			<td></td>
			<th>수가</th>
			<td></td>
		</tr>
	</tbody>
</table>
<input id="svcCd" type="hidden" value="<?=$svcCd;?>">
<input id="jumin" type="hidden" value="<?=$ed->en($jumin);?>">
<input id="orgDate" type="hidden" value="<?=$date;?>">
<input id="orgTime" type="hidden" value="<?=$time;?>">
<input id="orgSeq" type="hidden" value="<?=$seq;?>">
</form>
<?
	include_once('../inc/_footer.php');
?>