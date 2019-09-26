<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$SR		= $_POST['SR'];
	$year	= $_POST['year'];
	$month	= $_POST['month'];
	$month	= ($month < 10 ? '0' : '').$month;

	//요일
	$weekly = Array(0=>'<span style="color:RED;">일</span>',1=>'월',2=>'화',3=>'수',4=>'목',5=>'금',6=>'<span style="color:BLUE;">토</span>');

	//휴일
	$sql = 'SELECT	mdate AS date
			,		holiday_name AS name
			FROM	tbl_holiday
			WHERE	LEFT(mdate,6) = \''.$year.$month.'\'';

	$holiday = $conn->_fetch_array($sql, 'date');

	//일정
	$sql = 'SELECT	t01_sugup_date AS date
			,		CAST(RIGHT(t01_sugup_date,2) AS unsigned) AS day
			,		t01_suga_code1 AS code
			,		COUNT(t01_jumin) AS cnt
			,		a.suga_nm AS name
			FROM	t01iljung';

	/*
		$sql .= '
			INNER	JOIN	care_suga AS a
					ON		a.org_no	= t01_ccode
					AND		a.suga_sr	= t01_mkind
					AND		CONCAT(a.suga_cd,a.suga_sub)	 = t01_suga_code1
					AND		REPLACE(a.from_dt,	\'-\',\'\') <= t01_sugup_date
					AND		REPLACE(a.to_dt,	\'-\',\'\') >= t01_sugup_date';
	 */
	$sql .= '
			INNER	JOIN (
						SELECT	org_no, suga_sr, suga_cd, suga_sub, suga_nm, from_dt, to_dt
						FROM	care_suga
						WHERE	org_no	= \''.$orgNo.'\'
						AND		suga_sr = \''.$SR.'\'
						UNION	ALL
						SELECT	\''.$orgNo.'\', \''.$SR.'\', LEFT(code,5), MID(code,6), name, from_dt, to_dt
						FROM	care_suga_comm
					) AS a
					ON		a.org_no	= t01_ccode
					AND		a.suga_sr	= t01_mkind
					AND		CONCAT(a.suga_cd,a.suga_sub)	 = t01_suga_code1
					AND		REPLACE(a.from_dt,	\'-\',\'\') <= t01_sugup_date
					AND		REPLACE(a.to_dt,	\'-\',\'\') >= t01_sugup_date';

	$sql .= '
			WHERE	t01_ccode = \''.$orgNo.'\'
			AND		t01_mkind = \''.$SR.'\'
			AND		LEFT(t01_sugup_date,6) = \''.$year.$month.'\'
			AND		t01_status_gbn	= \'1\'
			AND		t01_del_yn		= \'N\'
			GROUP	BY t01_sugup_date, t01_suga_code1
			ORDER	BY date, name';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		if ($tmpDate != $row['date']){
			$tmpDate  = $row['date'];
			$data[$tmpDate] = Array(
				'day'=>$row['day']
			,	'weekday'=>$weekly[Date('w',StrToTime($row['date']))]
			,	'holiday'=>$holiday[$row['date']]
			,	'cnt'=>0
			);
		}

		$data[$row['date']]['cnt'] ++;
		$data[$row['date']]['list'][$row['code']] = Array(
			'name'=>$row['name']
		,	'cnt'=>$row['cnt']
		);
	}

	$conn->row_free();

	if ($IsExcelClass){
		foreach($data as $date => $list){
			foreach($list['list'] as $code => $row){
				$rowNo ++;
				$sheet->getRowDimension($rowNo)->setRowHeight(-1);
				$sheet->SetData( Array('F'=>'B'.$rowNo, 'val'=>$row['name'], 'H'=>'L') );
				$sheet->SetData( Array('F'=>'C'.$rowNo, 'val'=>$row['cnt'], 'H'=>'R') );
				$sheet->SetData( Array('F'=>'D'.$rowNo, 'val'=>'') );

				if ($tmpDate != $date){
					$tmpDate  = $date;
					$sheet->SetData( Array('F'=>'A'.$rowNo, 'T'=>'A'.($rowNo + $list['cnt'] - 1), 'val'=>$list['day'].'일('.$list['weekday'].')', 'H'=>'L') );
				}
			}
		}
	}else{
		if (is_array($data)){
			foreach($data as $date => $list){
				$first = true;

				foreach($list['list'] as $code => $row){
					if ($IsExcel){?>
						<tr><?
						if ($first){
							$first = false;?>
							<td style="" rowspan="<?=$list['cnt'];?>">
								<div style="text-align:left;"><span style="color:<?=$list['holiday']['name'] ? 'RED' : '';?>;"><?=$list['day'];?></span>일(<?=$list['weekday'];?>)</div>
								<div style="text-align:right;"><?=$list['holiday']['name'];?></div>
							</td><?
						}?>
						<td style="text-align:left;"><?=$row['name'];?></td>
						<td style="text-align:right;"><?=$row['cnt'];?></td>
						</tr><?
					}else{?>
						<tr><?
						if ($first){
							$first = false;?>
							<td class="top center" rowspan="<?=$list['cnt'];?>">
								<div class="left"><span style="color:<?=$list['holiday']['name'] ? 'RED' : '';?>;"><?=$list['day'];?></span>일(<?=$list['weekday'];?>)</div><?
								if ($list['holiday']['name']){?>
									<div class="right"><?=$list['holiday']['name'];?></div><?
								}?>
							</td><?
						}?>
						<td class="center"><div class="left"><?=$row['name'];?></div></td>
						<td class="center"><div class="right"><?=$row['cnt'];?></div></td>
						<td class="center last"></td>
						</tr><?
					}
				}
			}
		}else{?>
			<tr>
				<td class="center last" colspan="5">::검색된 데이타가 없습니다.::</td>
			</tr><?
		}
	}

	include_once('../inc/_db_close.php');
?>