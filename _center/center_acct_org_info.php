<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_POST['orgNo'];
	$year	= $_POST['year'];
	$month	= IntVal($_POST['month']);
	$yymm	= $year.($month < 10 ? '0' : '').$month;
	$yymm	= $myF->dateAdd('day', -1, $yymm.'01', 'Ym');

	$unitStr = Array('1'=>'고객', '2'=>'직원', '3'=>'문자', '4'=>'고정');

	$colgroup = '
		<col width="120px">
		<col width="80px">
		<col width="50px">
		<col width="50px">
		<col width="50px">
		<col width="80px">
		<col width="80px">
		<col width="80px">
		<col>';
?>
<table id="ID_ACCT_SVC_LIST_CAPTION" class="my_table" style="width:100%;">
	<colgroup><?=$colgroup;?></colgroup>
	<thead>
		<tr>
			<th class="head" rowspan="2">서비스</th>
			<th class="head" rowspan="2">기본금</th>
			<th class="head" colspan="5">초과정보</th>
			<th class="head" rowspan="2">청구<br>금액</th>
			<th class="head last" rowspan="2">비고</th>
		</tr>
		<tr>
			<th class="head">구분</th>
			<th class="head">제한수</th>
			<th class="head">초과수</th>
			<th class="head">단가</th>
			<th class="head">금액</th>
		</tr>
	</thead>
</table>
<div style="width:100%; height:10px; overflow-x:hidden; overflow-y:scroll;">
	<table class="my_table" style="width:100%;">
		<colgroup><?=$colgroup;?></colgroup>
		<tbody id="ID_ACCT_SVC_LIST"><?
			$sql = 'SELECT	a.svc_gbn
					,		a.svc_cd
					,		b.svc_nm
					,		a.pro_cd
					,		a.stnd_amt
					,		a.unit_cd
					,		a.limit_cnt
					,		a.over_cnt
					,		a.over_cost
					,		a.over_amt
					,		a.acct_amt
					FROM	cv_svc_acct_list AS a
					INNER	JOIN (
							SELECT	CAST(\'1\' AS CHAR) AS svc_gbn
							,		svc_cd
							,		svc_nm
							FROM	cv_svc_main
							UNION	ALL
							SELECT	CAST(\'2\' AS CHAR)
							,		svc_cd
							,		svc_nm
							FROM	cv_svc_sub
							) AS b
							ON		b.svc_gbn	= a.svc_gbn
							AND		b.svc_cd	= a.svc_cd
					WHERE	a.org_no = \''.$orgNo.'\'
					AND		a.yymm	 = \''.$yymm.'\'';

			$conn->query($sql);
			$conn->fetch();

			$rowCnt = $conn->row_count();

			for($i=0; $i<$rowCnt; $i++){
				$row = $conn->select_row($i);

				if ($i > 0){
					$style = 'border-top:1px solid #CCCCCC;';
				}else{
					$style = '';
				}?>
				<tr>
					<td class="center bottom" style="<?=$style;?>"><div class="left"><?=$row['svc_nm'];?></div></td>
					<td class="center bottom" style="<?=$style;?>"><div class="right"><?=number_format($row['stnd_amt']);?></div></td>
					<td class="center bottom" style="<?=$style;?>"><?=$unitStr[$row['unit_cd']];?></td>
					<td class="center bottom" style="<?=$style;?>"><div class="right"><?=number_format($row['limit_cnt']);?></div></td>
					<td class="center bottom" style="<?=$style;?>"><div class="right"><?=number_format($row['over_cnt']);?></div></td>
					<td class="center bottom" style="<?=$style;?>"><div class="right"><?=number_format($row['over_cost']);?></div></td>
					<td class="center bottom" style="<?=$style;?>"><div class="right"><?=number_format($row['over_amt']);?></div></td>
					<td class="center bottom" style="<?=$style;?>"><div class="right"><?=number_format($row['acct_amt']);?></div></td>
					<td class="center bottom" style="<?=$style;?>"></td>
				</tr><?

				$stndAmt += $row['stnd_amt'];
				$overAmt += $row['over_amt'];
				$acctAmt += $row['acct_amt'];
			}

			$conn->row_free();?>
		</tbody>
	</table>
</div>
<table id="ID_ACCT_SVC_LIST_SUM" class="my_table" style="width:100%;">
	<colgroup><?=$colgroup;?></colgroup>
	<tbody>
		<tr>
			<td class="center sum" style="border-top:1px solid #CCCCCC;">합계</td>
			<td class="center sum" style="border-top:1px solid #CCCCCC;"><div class="right"><?=number_format($stndAmt);?></div></td>
			<td class="center sum" style="border-top:1px solid #CCCCCC;"></td>
			<td class="center sum" style="border-top:1px solid #CCCCCC;"></td>
			<td class="center sum" style="border-top:1px solid #CCCCCC;"></td>
			<td class="center sum" style="border-top:1px solid #CCCCCC;"></td>
			<td class="center sum" style="border-top:1px solid #CCCCCC;"><div class="right"><?=number_format($overAmt);?></div></td>
			<td class="center sum" style="border-top:1px solid #CCCCCC;"><div class="right"><?=number_format($acctAmt);?></div></td>
			<td class="center sum last" style="border-top:1px solid #CCCCCC;"></td>
		</tr>
	</tbody>
</table>
<?
	include_once('../inc/_db_close.php');
?>