<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$year = $_POST['year'];
	$month = IntVal($_POST['month']);
	$memCd = $ed->de($_POST['memCd']);
	$yymm = $year.($month < 10 ? '0' : '').$month;
	$prtYn = $_POST['prtYn'];
	$lvlYn = $_POST['lvlYn'];
	$IsExcel = true;

	header( "Content-type: application/vnd.ms-excel;charset=UTF-8");
	header( "Expires: 0" );
	header( "Cache-Control: must-revalidate, post-check=0,pre-check=0" );
	header( "Pragma: public" );
	header( "Content-Disposition: attachment; filename=carevisit_excel_".date('Ymd').".xls" );

	$style = 'border:0.5pt solid black;';

	include_once('../iljung/sw_fun.php');
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
</head>
<body><?
	$sql = 'SELECT	CAST(DATE_FORMAT(mdate, \'%d\') AS unsigned) AS day, holiday_name AS holiday
			FROM	tbl_holiday
			WHERE	LEFT(mdate,6) = \''.$yymm.'\'';

	$holiday = $conn->_fetch_array($sql,'day');

	$sql = 'SELECT	DISTINCT m02_yjumin AS mem_cd, m02_yname AS mem_nm, m02_ytel AS mobile
			FROM	m02yoyangsa
			INNER	JOIN	mem_his AS a
					ON		a.org_no = m02_ccode
					AND		a.jumin = m02_yjumin
					AND		DATE_FORMAT(a.join_dt,\'%Y%m\') <= \''.$yymm.'\'
					AND		DATE_FORMAT(IFNULL(a.quit_dt,\'9999-12-31\'),\'%Y%m\') >= \''.$yymm.'\'
			WHERE	m02_ccode = \''.$orgNo.'\'';

	if ($memCd) $sql .= ' AND m02_yjumin = \''.$memCd.'\'';

	$sql .= '
			AND		m02_jikwon_gbn IN (\'B\',\'C\',\'D\',\'W\')
			ORDER	BY mem_nm';

	$memList = $conn->_fetch_array($sql, 'mem_cd');

	if (is_array($memList)){?>
		<table><?
		$IsFirst = true;
		foreach($memList as $memCd => $R){
			$targetCnt = 0;
			$visitCnt = 0;
			$iljung = iljungData($memCd, $prtYn);
			if (is_array($iljung)){
				foreach($iljung as $tmpDay => $tmpR){
					if (is_array($tmpR)){
						foreach($tmpR as $tmpI => $v){
							$targetCnt += $v['tgCnt'];
							$visitCnt += ($v['bold'] == 'Y' ? 1 : 0);
						}
					}
				}
			}


			if ($IsFirst){
				$IsFirst = false;
			}else{
				echo '<tr><td style="height:150px;" colspan="7">&nbsp;</td></tr>';
			}?>
			<tr>
				<td style="<?=$style;?> text-align:center;" rowspan="2" colspan="4"><?=$year;?>년 <?=$month;?>월 사회복지사 업무수행 방문관리 출근표</td>
				<td style="<?=$style;?> text-align:center;" rowspan="2">결<br style="mso-data-placement:same-cell;">재</td>
				<td style="<?=$style;?> text-align:center; height:40px;">관리자</td>
				<td style="<?=$style;?> text-align:center;">시설장</td>
			</tr>
			<tr>
				<td style="<?=$style;?> height:70px;"></td>
				<td style="<?=$style;?>"></td>
			</tr>
			<tr>
				<td style="<?=$style;?> height:5px;" colspan="7"></td>
			</tr>
			<tr>
				<td style="<?=$style;?>" colspan="7">
					성명 : <?=$R['mem_nm'];?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					연락처 : <?=$myF->phoneStyle($R['mobile'],'.');?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					방문대장자수 : <?=number_format($targetCnt);?>명&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					방문건수 : <?=number_format($visitCnt);?>건&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				</td>
			</tr>
			<tr>
				<td style="<?=$style;?> height:5px;" colspan="7"></td>
			</tr>
			<tr>
				<td style="<?=$style;?> text-align:center; width:<?=$lvlYn == 'Y' ? '170' : '130';?>px; color:red;">일</td>
				<td style="<?=$style;?> text-align:center; width:<?=$lvlYn == 'Y' ? '170' : '130';?>px;">월</td>
				<td style="<?=$style;?> text-align:center; width:<?=$lvlYn == 'Y' ? '170' : '130';?>px;">화</td>
				<td style="<?=$style;?> text-align:center; width:<?=$lvlYn == 'Y' ? '170' : '130';?>px;">수</td>
				<td style="<?=$style;?> text-align:center; width:<?=$lvlYn == 'Y' ? '170' : '130';?>px;">목</td>
				<td style="<?=$style;?> text-align:center; width:<?=$lvlYn == 'Y' ? '170' : '130';?>px;">금</td>
				<td style="<?=$style;?> text-align:center; width:<?=$lvlYn == 'Y' ? '170' : '130';?>px; color:blue;">토</td>
			</tr><?

			#$iljung = iljungData($memCd, $prtYn);
			$w = Date('w',StrToTime($yymm.'01'));
			$lastday = $myF->lastday($year, $month);
			$day = 1;
			for($i=1; $i<=6; $i++){?>
				<tr><?
				for($j=0; $j<=6; $j++){
					if ($j == 0){
						$clr = 'red';
					}else if ($j == 6){
						$clr = 'blue';
					}else{
						$clr = '';
					}

					if ($holiday[$day]['holiday']){
						$clr = 'red';
					}?>
					<td style="<?=$style;?> vertical-align:top;"><?
						if ((($i == 1 && $j >= $w) || ($i > 1)) && $day <= $lastday){
							echo '<span style="color:'.$clr.';">'.$day.' '.($holiday[$day]['holiday'] ? ' '.$holiday[$day]['holiday'] : '').'</span><br style="mso-data-placement:same-cell;">';
							if (is_array($iljung[$day])){
								foreach($iljung[$day] as $tmpI => $v){
									echo $myF->min2time($v['from']).'~'.$myF->min2time($v['to']).' <span style="font-weight:'.($v['bold'] == 'Y' ? 'bold' : 'normal').';">'.$v['work'].($lvlYn == 'Y' && $v['level'] ? '('.$v['level'].'등급)' : '').'</span><br style="mso-data-placement:same-cell;">';
								}
							}
							$day ++;
						}?>
					</td><?
				}?>
				</tr><?
			}

			Unset($iljung);
		}?>
		</table><?
	}

	Unset($memList);
?>
</body>
</html>
<?
	include_once('../inc/_db_close.php');
?>