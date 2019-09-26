<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_POST['orgNo'];
	$orgNm	= $_POST['orgNm'];
	$popFrom= str_replace('-','',$_POST['popFrom']);
	$popTo	= str_replace('-','',$_POST['popTo']);
	$stat	= $_POST['stat'];
	$page	= $_POST['page'];

	$itemCnt = 25;

	$bsl = 'SELECT	DISTINCT a.org_no, m00_store_nm As org_nm, stop_gbn, stop_dt, def_txt, def_amt, cls_yn, stop_yn, close_dt, memo
			FROM	stop_set AS a
			INNER	JOIN	m00center
					ON		m00_mcode = a.org_no';

	if ($orgNo) $bsl .= ' AND INSTR(a.org_no,\''.$orgNo.'\') > 0';
	if ($orgNm) $bsl .= ' AND INSTR(m00_store_nm \''.$orgNm.'\') > 0';

	$bsl .= '
			WHERE	a.org_no != \'\'';

	if ($stat) $bsl .= ' AND a.stop_gbn = \''.$stat.'\'';

	if ($popFrom && $popTo){
		$bsl .= ' AND a.stop_dt BETWEEN \''.$popFrom.'\' AND \''.$popTo.'\'';
	}else if ($popFrom){
		$bsl .= ' AND a.stop_dt >= \''.$popFrom.'\'';
	}else if ($popTo){
		$bsl .= ' AND a.stop_dt <= \''.$popTo.'\'';
	}


	$sql = 'SELECT	COUNT(*)
			FROM	('.$bsl.') AS a';
	$totCnt = $conn->get_data($sql);

	$pageCnt = (intVal($page) - 1) * $itemCnt;

	$sql = 'SELECT	*
			FROM	('.$bsl.') AS a
			ORDER	BY org_nm';

	//LIMIT	'.$pageCnt.','.$itemCnt;

	#echo '<tr><td colspan="10">'.nl2br($sql).'</td></tr>';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	$no = 1;

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		if ($row['stop_gbn'] == '1'){ //중지
			if ($row['cls_yn'] == 'N'){
				$row['cls_yn'] = '<span style="color:RED;">중지설정</span>';
			}else if ($row['cls_yn'] == 'Y'){
				$row['cls_yn'] = '<span style="color:BLUE;">중지해제</span>';
			}
		}else{ //미납
			if ($row['close_dt'] >= Date('Ymd')){
				$row['cls_yn'] = '<span style="">미납설정</span>';
			}else{
				$row['cls_yn'] = '<span style="">미납해제</span>';
			}
		}?>
		<tr>
			<td class="center"><?=$pageCnt + $no;?></td>
			<td class=""><div class="left"><a href="#" onclick="Selection('<?=$row['org_no'];?>');"><?=$row['org_nm'];?></a></div></td>
			<td class=""><div class="left"><?=$row['org_no'];?></div></td>
			<td class="center"><?=$myF->dateStyle($row['stop_dt'],'.');?></td>
			<td class=""><div class="left"><?=$row['def_txt'];?></div></td>
			<td class=""><div class="right"><?=number_format($row['def_amt']);?></div></td>
			<td class="center"><?=$row['cls_yn'];?></td>
			<td class="last"><div class="left"><?=$row['memo'];?></div></td>
		</tr><?

		$no ++;
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>