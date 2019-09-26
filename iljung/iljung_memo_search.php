<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$year	= IntVal($_POST['year']);
	$month	= $_POST['month'];
	$month	= ($month < 10 ? '0' : '').$month;
	$name	= $_POST['name'];
	$fromDt	= $_POST['from'];
	$toDt	= $_POST['to'];
	$svcCd  = $_POST['svcCd'];

	$sql = 'SELECT	jumin
			,		yymm
			,		seq
			,		m03_name AS name
			,		reg_dt
			,		memo
			,		svc_cd
			FROM	iljung_memo
			INNER	JOIN	m03sugupja
					ON		m03_ccode	= org_no
					AND		m03_mkind	= CASE WHEN svc_cd = \'S\' OR svc_cd = \'R\' THEN \'6\' ELSE svc_cd END
					AND		m03_jumin	= jumin
			WHERE	org_no	= \''.$orgNo.'\'';

	if ($year > 0){
		$sql .= ' AND yymm = \''.$year.$month.'\'';
	}
	
	if ($svcCd != 'all'){
		$sql .= ' AND svc_cd = \''.$svcCd.'\'';
	}

	if ($fromDt && $toDt){
		$sql .= ' AND reg_dt BETWEEN \''.$fromDt.'\' AND \''.$toDt.'\'';
	}else if ($fromDt){
		$sql .= ' AND reg_dt >= \''.$fromDt.'\'';
	}else if ($toDt){
		$sql .= ' AND reg_dt <= \''.$toDt.'\'';
	}

	if ($name) $sql .= ' AND m03_name like \'%'.$name.'%\'';

	$sql .= '
			AND		del_flag= \'N\'
			ORDER	BY	reg_dt DESC';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	$no = 1;

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);?>
		<tr id="rowId_<?=$no;?>">
			<td class="center"><?=$no;?></td>
			<td class="center"><?=$myF->dateStyle($row['reg_dt'],'.');?></td>
			<td class="center"><div class="left nowrap" style="width:70px;"><?=$row['name'];?></div></td>
			<td class="center"><div class="left nowrap" style="width:500px;"><?=$row['memo'];?></div></td>
			<td class="center last">
				<div class="left">
					<span class="btn_pack m"><button onclick="lfModify('<?=$ed->en($row['jumin']);?>','<?=$row['yymm'];?>','NEW','<?=$row['svc_cd'];?>');">등록</button></span>
					<span class="btn_pack m"><button onclick="lfModify('<?=$ed->en($row['jumin']);?>','<?=$row['yymm'];?>','<?=$row['seq'];?>','<?=$row['svc_cd'];?>');">수정</button></span>
					<span class="btn_pack m"><button onclick="lfDelete('<?=$ed->en($row['jumin']);?>','<?=$row['yymm'];?>','<?=$row['seq'];?>','<?=$no;?>');">삭제</button></span>
				</div>
			</td>
		</tr><?
		$no ++;
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>