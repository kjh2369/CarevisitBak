<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$fromDt	= str_replace('-','',$_POST['fromDt']);
	$toDt	= str_replace('-','',$_POST['toDt']);


	//직원정보
	$sql = 'SELECT	jumin
			,		join_dt
			,		quit_dt
			,		employ_type
			FROM	mem_his
			WHERE	org_no = \''.$orgNo.'\'
			AND		CASE WHEN LEFT(REPLACE(join_dt,\'-\',\'\'),6) BETWEEN \''.$fromDt.'\' AND \''.$toDt.'\' THEN 1 ELSE 0 END +
					CASE WHEN LEFT(REPLACE(IFNULL(quit_dt,\''.$toDt.'\'),\'-\',\'\'),6) BETWEEN \''.$fromDt.'\' AND \''.$toDt.'\' THEN 1 ELSE 0 END > 0';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		if ($row['employ_type'] == '1'){
			$row['employ_type'] = '정규직';
		}else if ($row['employ_type'] == '2'){
			$row['employ_type'] = '계약직';
		}else if ($row['employ_type'] == '3'){
			$row['employ_type'] = '60시간이상';
		}else if ($row['employ_type'] == '4'){
			$row['employ_type'] = '60시간미만';
		}

		$mem[$row['jumin']] = Array(
			'joinDt'=>$myF->dateStyle($row['join_dt'],'.')
		,	'quitDt'=>$myF->dateStyle($row['quit_dt'],'.')
		,	'employ'=>$row['employ_type']
		);
	}

	$conn->row_free();


	$sql = 'SELECT	a.jumin
			,		m02_yname AS name
			,		days, times, pay, saved_money
			FROM	(
					SELECT	jumin
					,		SUM(work_cnt) AS days
					,		SUM(work_time) AS times
					,		SUM(work_pay) AS pay
					,		SUM(saved_money) AS saved_money
					FROM	salary_retirement
					WHERE	org_no = \''.$orgNo.'\'
					AND		yymm BETWEEN \''.$fromDt.'\' AND \''.$toDt.'\'
					GROUP	BY jumin
					) AS a
			INNER	JOIN	m02yoyangsa
					ON		m02_ccode	= \''.$orgNo.'\'
					AND		m02_mkind	= \'0\'
					AND		m02_yjumin	= a.jumin
			ORDER	BY name';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	$no = 1;

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);?>
		<tr>
			<td class="center"><?=$no;?></td>
			<td class="center"><?=$row['name'];?></td>
			<td class="center"><?=$myF->issToBirthday($row['jumin'],'.');?></td>
			<td class="center"><?=$mem[$row['jumin']]['joinDt'];?></td>
			<td class="center"><?=$mem[$row['jumin']]['quitDt'];?></td>
			<td class="center"><?=$mem[$row['jumin']]['employ'];?></td>
			<td class="center"><div class="right"><?=number_format($row['days']);?></div></td>
			<td class="center"><div class="right"><?=number_format($row['times'],1);?></div></td>
			<td class="center"><div class="right"><?=number_format($row['pay']);?></div></td>
			<td class="center"><div class="right"><?=number_format($row['saved_money']);?></div></td>
			<td class="center last"></td>
		</tr><?

		$total['salary'] += $row['pay'];
		$total['savedMoney'] += $row['saved_money'];

		$no ++;
	}

	$conn->row_free();?>

	<!--CUT_LINE--><?

	if ($rowCnt > 0){?>
		<tr>
			<td class="sum center" colspan="8"><div class="right">합계</div></td>
			<td class="sum center"><div class="right"><?=number_format($total['salary']);?></div></td>
			<td class="sum center"><div class="right"><?=number_format($total['savedMoney']);?></div></td>
			<td class="sum center last"></td>
		</tr><?
	}else{?>
		<tr>
			<td class="center last" colspan="11">::검색된 데이타가 없습니다.::</td>
		</tr><?
	}


	Unset($data);
	Unset($total);

	include_once('../inc/_db_close.php');
?>