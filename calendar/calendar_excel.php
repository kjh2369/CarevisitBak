<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$orgNm	= $_SESSION['userCenterName'];
	$year	= $_POST['year'];
	$month	= $_POST['month'];
	$title	= $year.'년 '.IntVal($month).'월 스케줄';

	//휴일
	$sql = 'SELECT	CAST(RIGHT(mdate,2) AS unsigned) AS date
			,		holiday_name AS name
			FROM	tbl_holiday
			WHERE	LEFT(mdate,6) = \''.$year.$month.'\'';

	$holiday = $conn->_fetch_array($sql,'date');

	//일정
	$sql = 'SELECT	CAST(RIGHT(cld_dt,2) AS unsigned) AS day
			,		LEFT(cld_from,5) AS from_time
			,		LEFT(cld_to,5) AS to_time
			,		cld_fulltime AS fulltime_yn
			,		cld_subject AS subject
			FROM	calendar
			WHERE	org_no	= \''.$orgNo.'\'
			AND		cld_yymm= \''.$year.$month.'\'
			AND		del_flag= \'N\'
			ORDER	BY cld_dt, CASE WHEN cld_fulltime = \'Y\' THEN 1 ELSE 2 END, cld_from, cld_to';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		$data[$row['day']][] = Array(
			'time'=>($row['fulltime_yn'] == 'Y' ? '종일' : $row['from_time']. '~' .$row['to_time'])
		,	'text'=>$row['subject']
		);
	}

	$conn->row_free();

	header("Content-type: application/vnd.ms-excel;charset=UTF-8");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Pragma: public");
	header("Content-Disposition: attachment; filename=".$myF->euckr($title).".xls");?>
	<html xmlns:v="urn:schemas-microsoft-com:vml"
	xmlns:o="urn:schemas-microsoft-com:office:office"
	xmlns:x="urn:schemas-microsoft-com:office:excel"
	xmlns="http://www.w3.org/TR/REC-html40">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<meta name=ProgId content=Excel.Sheet>
	</head>
	<body>
		<table border="0" cellpadding="0" cellspacing="0" style="border-collapse:collapse; table-layout:fixed;">
			<tr style="height:40px;">
				<td style="border:none; text-align:center; vertical-align:middle; font-size:17px; font-weight:bold;" colspan="14"><?=$title;?></td>
			</tr>
			<tr style="height:3px;">
				<td style="width:86px; border:none;">&nbsp;</td>
				<td style="width:94px; border:none;">&nbsp;</td>
				<td style="width:86px; border:none;">&nbsp;</td>
				<td style="width:94px; border:none;">&nbsp;</td>
				<td style="width:86px; border:none;">&nbsp;</td>
				<td style="width:94px; border:none;">&nbsp;</td>
				<td style="width:86px; border:none;">&nbsp;</td>
				<td style="width:94px; border:none;">&nbsp;</td>
				<td style="width:86px; border:none;">&nbsp;</td>
				<td style="width:94px; border:none;">&nbsp;</td>
				<td style="width:86px; border:none;">&nbsp;</td>
				<td style="width:94px; border:none;">&nbsp;</td>
				<td style="width:86px; border:none;">&nbsp;</td>
				<td style="width:94px; border:none;">&nbsp;</td>
			</tr>
			<tr style="height:40px;">
				<td colspan="2" style="border:0.5pt solid BLACK; background-color:#EAEAEA; text-align:center; vertical-align:middle; font-weight:bold; color:RED;">일</td>
				<td colspan="2" style="border:0.5pt solid BLACK; background-color:#EAEAEA; text-align:center; vertical-align:middle; font-weight:bold;">월</td>
				<td colspan="2" style="border:0.5pt solid BLACK; background-color:#EAEAEA; text-align:center; vertical-align:middle; font-weight:bold;">화</td>
				<td colspan="2" style="border:0.5pt solid BLACK; background-color:#EAEAEA; text-align:center; vertical-align:middle; font-weight:bold;">수</td>
				<td colspan="2" style="border:0.5pt solid BLACK; background-color:#EAEAEA; text-align:center; vertical-align:middle; font-weight:bold;">목</td>
				<td colspan="2" style="border:0.5pt solid BLACK; background-color:#EAEAEA; text-align:center; vertical-align:middle; font-weight:bold;">금</td>
				<td colspan="2" style="border:0.5pt solid BLACK; background-color:#EAEAEA; text-align:center; vertical-align:middle; font-weight:bold; color:BLUE;">토</td>
			</tr><?
			$lastday		= $myF->lastDay($year, $month);
			$startWeekday	= date('w', strtotime($year.'-'.$month.'-01'));
			$endWeekday		= date('w', strtotime($year.'-'.$month.'-'.$lastday));
			$startWeekly	= $startWeekday;

			//1일 이전 일자
			for($i=0; $i<$startWeekly; $i++){
				if ($i == 0){?>
					<tr><?
				}?>
				<td colspan="2" style="border:0.5pt solid BLACK; border-bottom:none; text-align:center; vertical-align:middle;">&nbsp;</td><?
			}

			for($i=1; $i<=$lastday; $i++){
				if ($startWeekday == 0){?>
					<tr><?
				}else{
					if ($startWeekday % 7 == 0){
						$startWeekday = 0;?>
						</tr>
						<tr><?
					}
				}

				//ROWS
				$rows = 1;
				for($ii=0; $ii<$i; $ii++){
					
					if (is_array($data[$ii])){
					
						if ($rows < SizeOf($data[$ii])){
							$rows = SizeOf($data[$ii]);
						}
					}
				}
				
				//일색상
				//$color = $myF->_weekColor($startWeekday % 7);
				if ($startWeekday % 7 == 0){
					$color = 'RED';
				}else if ($startWeekday % 7 == 6){
					$color = 'BLUE';
				}else{
					$color = '';
				}

				if ($holiday[$i]['name']){
					$color = 'RED';
				}?>
				<td style="border:0.5pt solid BLACK; border-bottom:none; border-right:none; text-align:left; vertical-align:middle; color:<?=$color;?>;"><?=$i;?></td>
				<td style="border:0.5pt solid BLACK; border-bottom:none; border-left:none; text-align:right; vertical-align:middle;"><?=$holiday[$i]['name'];?></td><?

				if ($startWeekday % 7 == 6){
					for($j=0; $j<$rows; $j++){?>
						<tr><?
							for($ii=0; $ii<7; $ii++){
								$tmpI = $i - 7 + 1 + $ii;
								if (is_array($data[$tmpI][$j])){?>
									<td style="border:0.5pt solid BLACK; border-top:<?=$j > 0 ? '0.5pt dashed #CCCCCC' : 'none';?>; border-bottom:none; border-right:none; text-align:left; vertical-align:top;"><?=$data[$tmpI][$j]['time'];?></td>
									<td style="border:0.5pt solid BLACK; border-top:<?=$j > 0 ? '0.5pt dashed #CCCCCC' : 'none';?>; border-bottom:none; border-left:none; text-align:left; vertical-align:top;"><?=$data[$tmpI][$j]['text'];?></td><?
								}else{?>
									<td colspan="2" style="border:0.5pt solid BLACK; border-top:none; border-bottom:none; text-align:center; vertical-align:middle;">&nbsp;</td><?
								}
							}?>
						</tr><?
					}
				}

				$startWeekday ++; //요일증가
			}

			$tmpI ++;
			
			$tmp = $tmpI;

			for($ii=$endWeekday+1; $ii<7; $ii++){?>
				<td colspan="2"  style="border:0.5pt solid BLACK; border-bottom:none; text-align:center; vertical-align:middle;">&nbsp;</td><?
			}?>
			</tr><?
			
			$rows = 1;

			for($ii=$tmpI; $ii<$tmpI+6; $ii++){
				if (is_array($data[$ii])){
					if ($rows < SizeOf($data[$ii])){
						$rows = SizeOf($data[$ii]);
					}
				}
			}
			
			for($j=0; $j<$rows; $j++){
				$tmpI = $tmp; ?>
				<tr><?
					for($ii=0; $ii<7; $ii++){
						if (is_array($data[$tmpI][$j])){?>
							<td style="border:0.5pt solid BLACK; border-top:<?=$j > 0 ? '0.5pt dashed #CCCCCC' : 'none';?>; border-bottom:none; border-right:none; text-align:left; vertical-align:top;"><?=$data[$tmpI][$j]['time'];?></td>
							<td style="border:0.5pt solid BLACK; border-top:<?=$j > 0 ? '0.5pt dashed #CCCCCC' : 'none';?>; border-bottom:none; border-left:none; text-align:left; vertical-align:top;"><?=$data[$tmpI][$j]['text'];?></td><?
						}else{?>
							<td colspan="2" style="border:0.5pt solid BLACK; border-top:none; border-bottom:none; text-align:center; vertical-align:middle;">&nbsp;</td><?
						}
						$tmpI ++;
					}?>
				</tr><?
			}
			?>
			<tr style="height:60px;">
				<td colspan="14" style="border-top:0.5pt solid BLACK; text-align:center; font-size:17px; font-weight:bold;"><?=$orgNm;?></td>
			</tr>
		</table>
	</body>
	</html><?
	include_once('../inc/_db_close.php');
?>