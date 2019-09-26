<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$today = Date('Ymd');

	$sql = 'SELECT	DISTINCT
					log.org_no
			,		log.domain
			,		m00_store_nm AS org_nm
			,		m00_mname AS manager
			,		m00_ctel AS tel
			,		m00_caddr1 AS addr
			,		m00_caddr2 AS addr_dtl
			,		log.day_cnt
			,		log.visit_cnt
			,		log.visit_first
			,		log.visit_last
			FROM (
					SELECT	org_no
					,		domain
					,		COUNT(date) AS day_cnt
					,		SUM(visit_cnt) AS visit_cnt
					,		CASE WHEN date = \''.$today.'\' THEN visit_first ELSE \'\' END AS visit_first
					,		CASE WHEN date = \''.$today.'\' THEN visit_last ELSE \'\' END AS visit_last
					FROM	visit_log
					WHERE	domain = \'carevisit.net\'
					GROUP	BY org_no, domain
			) AS log
			INNER	JOIN m00center
					ON m00_mcode = log.org_no
			ORDER	BY org_nm';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	if ($rowCnt > 0){
		$no = 1;

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);?>
			<tr>
				<td class="center"><?=$no;?></td>
				<td class="center"><div class="left nowrap" style="width:80px;"><?=$row['org_no'];?></div></td>
				<td class="center"><div class="left nowrap" style="width:150px;"><?=$row['org_nm'];?></div></td>
				<td class="center"><div class="left nowrap" style="width:70px;"><?=$row['manager'];?></div></td>
				<td class="center"><?=$myF->phoneStyle($row['tel'],'.');?></td>
				<td class="center"><div class="left nowrap" style="width:150px;"><?=$row['addr'].' '.$row['addr_dtl'];?></div></td>
				<td class="center"><div class="right"><?=$row['day_cnt'];?></div></td>
				<td class="center"><div class="right"><?=$row['visit_cnt'];?></div></td>
				<td class="center"><?=($row['visit_first'] ? Date('H:i:s',StrToTime($today.$row['visit_first'])) : '');?></td>
				<td class="center"><?=($row['visit_last'] ? Date('H:i:s',StrToTime($today.$row['visit_last'])) : '');?></td>
				<td class="center last"></td>
			</tr><?
			$no ++;
		}
	}else{?>
		<tr>
			<td class="center last" colspan="20">::검색된 데이타가 없습니다.::</td>
		</tr><?
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>