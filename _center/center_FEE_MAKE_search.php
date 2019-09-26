<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	//$company = $_POST['company'];
	$year	= $_POST['year'];
	$month	= IntVal($_POST['month']);
	$acctYm	= $year.($month < 10 ? '0' : '').$month;
	$yymm	= $myF->dateAdd('day', -1, $acctYm.'01', 'Ym');


	//마감여부
	$sql = 'SELECT	cls_yn
			FROM	cv_close_set
			WHERE	yymm = \''.$acctYm.'\'';

	$clsYn = $conn->get_data($sql);

	$sql = 'SELECT	*, CASE WHEN acct_amt + tmp_amt > 0 THEN 1 ELSE (SELECT COUNT(*) FROM cv_svc_fee WHERE org_no = a.org_no AND use_yn = \'Y\' AND acct_yn = \'Y\' AND del_flag = \'N\' AND LEFT(from_dt,6) <= \''.$yymm.'\' AND LEFT(to_dt,6)	>= \''.$yymm.'\') END AS fee_cnt
			FROM	(
					SELECT	a.org_no
					,		a.org_nm
					,		a.manager
					,		SUM(CASE WHEN b.svc_gbn != \'9\' AND b.svc_cd != \'99\' THEN b.acct_amt ELSE 0 END - CASE WHEN b.svc_gbn = \'9\' AND b.svc_cd = \'99\' THEN b.acct_amt ELSE 0 END) AS acct_amt
					,		SUM(b.tmp_amt) AS tmp_amt
					FROM	(
							SELECT	m00_mcode AS org_no
							,		GROUP_CONCAT(DISTINCT m00_store_nm) AS org_nm
							,		m00_mname AS manager
							FROM	m00center
							INNER	JOIN (
									SELECT	DISTINCT org_no
									FROM	cv_reg_info
									WHERE	LEFT(from_dt,6) <= \''.$yymm.'\'
									AND		LEFT(to_dt,6)	>= \''.$yymm.'\'
									) AS a
									ON		a.org_no = m00_mcode
							/*WHERE	m00_domain = \''.$company.'\'*/
							GROUP	BY m00_mcode
							) AS a
					LEFT	JOIN	cv_svc_acct_list AS b
							ON		b.org_no = a.org_no
							AND		b.yymm	 = \''.$yymm.'\'
					WHERE	b.org_no IS NOT NULL
					GROUP	BY a.org_no
					) AS a
			ORDER	BY CASE WHEN acct_amt + tmp_amt > 0 THEN 1
							WHEN fee_cnt > 0 THEN 2 ELSE 3 END, org_nm';

	//echo '<tr><td colspan="10">'.nl2br($sql).'</td></tr>';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	$no = 1;

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		$feeCnt = 0;

		/*if ($row['acct_amt'] == 0){
			$sql = 'SELECT	COUNT(*)
					FROM	cv_svc_fee
					WHERE	org_no	= \''.$row['org_no'].'\'
					AND		use_yn	= \'Y\'
					AND		acct_yn	= \'Y\'
					AND		del_flag= \'N\'
					AND		LEFT(from_dt,6) <= \''.$yymm.'\'
					AND		LEFT(to_dt,6)	>= \''.$yymm.'\'';
			$feeCnt = $conn->get_data($sql);
		}*/

		/*$sql = 'SELECT	COUNT(*)
				FROM	cv_cms_link
				WHERE	org_no	= \''.$row['org_no'].'\'
				AND		yymm	= \''.$yymm.'\'
				AND		del_flag= \'N\'';
		$linkCnt = $conn->get_data($sql);*/?>
		<tr>
			<td class="center"><?=$no;?></td>
			<td class="center"><div class="left"><?=$row['org_no'];?></div></td>
			<td class="center"><div class="left"><?=$row['org_nm'];?></div></td>
			<td class="center"><div class="left"><?=$row['manager'];?></div></td>
			<td class="center"><div class="right"><?=number_format($row['acct_amt']);?></div></td>
			<td class="center"><div class="right"><?=number_format($row['tmp_amt']);?></div></td>
			<td class="center">
				<div style="float:left; width:auto; margin-top:3px; margin-left:5px;"><span class="btn_pack small"><button onclick="Selection('<?=$row['org_no'];?>');">선택</button></span></div>
				<div style="float:left; width:auto; margin-top:3px; margin-left:5px;"><span class="btn_pack small"><button onclick="lfContSvc('<?=$row['org_no'];?>');">계약서비스</button></span></div><?

				if ($clsYn != 'Y'){
					if ($row['acct_amt'] + $row['tmp_amt'] > 0){?>
						<div style="float:left; width:auto; margin-top:3px; margin-left:5px;"><span class="btn_pack small"><button onclick="lfRemove('<?=$row['org_no'];?>');" style="color:RED;">삭제</button></span></div><?
					}else if ($row['fee_cnt'] > 0){?>
						<!--span class="btn_pack small"><button onclick="lfMake('<?=$row['org_no'];?>');" style="color:BLUE;">생성</button></span-->
						<div style="float:left; width:auto; margin-top:1px; margin-left:5px; color:blue;">기간 외</div><?
					}else{?>
						<div style="float:left; width:auto; margin-top:1px; margin-left:5px; color:red;">미과금</div><?
					}
				}

				if ($linkCnt > 0){?>
					<div style="float:left; width:auto; margin-top:3px; margin-left:5px;"><span class="btn_pack small"><button onclick="">연결내역</button></span></div><?
				}?>
			</td>
		</tr><?

		$no ++;
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>