<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_mySuga.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$fromDt	= str_replace('-', '', $_POST['fromDt']);
	$toDt	= str_replace('-', '', $_POST['toDt']);
	$outStat= $_POST['outStat'];

	if (!$fromDt) $fromDt = '20010101';
	if (!$toDt) $toDt = '99991231';

	$sql = 'SELECT	claim_dt, claim_amt, issue_dt, issue_time, in_gbn, in_amt, out_stat, out_bank, out_acct_no, remark
			FROM	cv_pay_in
			WHERE	org_no	 = \''.$orgNo.'\'
			AND		del_flag = \'N\'';

	if ($fromDt && $toDt){
		$sql .= ' AND issue_dt BETWEEN \''.$fromDt.'\' AND \''.$toDt.'\' ';
	}

	if ($outStat == '1'){
		$sql .= ' AND INSTR(CASE WHEN in_gbn = \'1\' THEN out_stat ELSE \'출금성공\' END, \'출금성공\') > 0';
	}else if ($outStat == '2'){
		$sql .= ' AND INSTR(CASE WHEN in_gbn = \'1\' THEN out_stat ELSE \'출금성공\' END, \'출금성공\') = 0';
	}

	$sql .= '
			ORDER	BY CASE WHEN claim_dt != \'\' THEN claim_dt ELSE issue_dt END DESC, issue_dt DESC, issue_time DESC';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	if ($rowCnt > 0){
		$no = 1;

		$inGbn = Array('1'=>'CMS', '2'=>'무통장');

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);?>
			<tr>
			<td class="center"><?=$no;?></td>
			<td class="center"><?=$myF->dateStyle($row['claim_dt'], '.');?></td>
			<td class="center"><div class="right"><?=number_format($row['claim_amt']);?></div></td>
			<td class="center"><?=$myF->dateStyle($row['issue_dt'], '.');?></td>
			<td class="center"><?=$myF->timeStyle($row['issue_time']);?></td>
			<td class="center"><?=$inGbn[$row['in_gbn']];?></td>
			<td class="center"><div class="right"><?=number_format($row['in_amt']);?></div></td>
			<td class="center"><div class="left nowrap" style="width:100px;" title="<?=$row['out_stat'];?>"><?=$row['out_stat'];?></div></td>
			<td class="center"><div class="left nowrap" style="width:50px;"><?=$row['out_bank'];?></div></td>
			<td class="center"><div class="left"><?=$row['out_acct_no'];?></div></td>
			<td class="center last"><div class="left"><?=$row['remark'];?></div></td>
			</tr><?

			$no ++;
		}
	}else{?>
		<tr>
		<td class="center last" colspan="11">::검색된 데이타가 없습니다.::</td>
		</tr><?
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>