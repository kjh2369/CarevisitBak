<?
	include("../inc/_db_open.php");
	include("../inc/_ed.php");
	include("../inc/_myFun.php");

	$mCode = $_POST['mCode'];
	$mKind = $_POST["mKind"];
	$mYear = $_POST['mYear'];
	$mMonth = $_POST["mMonth"];

	/*
	$fromDate = $mYear.$mMonth.'01';
	$toDate   = $mYear.$mMonth; //$toDate = $mYear.$mMonth.'31';

	if ($toDate == date('Ym', mkTime())){
		$toDate = date("Ymd", mktime());
	}else{
		$toDate = $mYear.$mMonth.date("t", mkTime(0, 0, 1, $mMonth, 1, $mYear));
	}
	*/

	if ($toDate > date("Ymd", mktime())){
		$toDate = '00000000';
	}

	$fromDate = $mYear.$mMonth.'010000';
	$toDate   = $mYear.$mMonth;

	if ($toDate == date('Ym', mkTime())){
		$toDate = date("YmdHi", mktime());
	}else{
		$toDate = $toDate.date("t", mkTime(0, 0, 1, $mMonth, 1, $mYear));
		$toDate = $toDate.'9999';
	}
?>
<table class="view_type1" style="width:100%; margin-top:0px;">
<tr>
	<th style="width:6%; margin:0; padding:0; text-align:center; border-right:1px solid #cccccc; line-height:1.5em;" rowspan="2">수급자</th>
	<th style="width:10%; margin:0; padding:0; text-align:center; border-right:1px solid #cccccc;" rowspan="2">주민번호</th>
	<th style="width:6%; margin:0; padding:0; text-align:center; border-right:1px solid #cccccc;" rowspan="2">등급</th>
	<th style="width:22%; margin:0; padding:0; text-align:center; border-right:1px solid #cccccc;" colspan="3">계획</th>
	<th style="width:22%; margin:0; padding:0; text-align:center; border-right:1px solid #cccccc;" colspan="3">실적</th>
	<th style="width:22%; margin:0; padding:0; text-align:center; border-right:1px solid #cccccc;" colspan="3">차이</th>
	<th style="width:6%; margin:0; padding:0; text-align:center;" rowspan="2">비고</th>
</tr>
<tr>
	<th style="width:8%; margin:0; padding:0; text-align:center; border-right:1px solid #cccccc;">요양</th>
	<th style="width:7%; margin:0; padding:0; text-align:center; border-right:1px solid #cccccc;">목욕</th>
	<th style="width:7%; margin:0; padding:0; text-align:center; border-right:1px solid #cccccc;">간호</th>
	<th style="width:8%; margin:0; padding:0; text-align:center; border-right:1px solid #cccccc;">요양</th>
	<th style="width:7%; margin:0; padding:0; text-align:center; border-right:1px solid #cccccc;">목욕</th>
	<th style="width:7%; margin:0; padding:0; text-align:center; border-right:1px solid #cccccc;">간호</th>
	<th style="width:8%; margin:0; padding:0; text-align:center; border-right:1px solid #cccccc;">요양</th>
	<th style="width:7%; margin:0; padding:0; text-align:center; border-right:1px solid #cccccc;">목욕</th>
	<th style="width:7%; margin:0; padding:0; text-align:center; border-right:1px solid #cccccc;">간호</th>
</tr>
<?
	$sql = "select m03_name as sugupjaNamd"
		 . ",      t01_jumin as sugupjaCode"
		 . ",      concat(substring(t01_jumin, 1, 6), '-', substring(t01_jumin, 7, 1), '******') as sugupjaJumin"
		 . ",      LVL.m81_name as lvlName"
		 . ",      sum(case when t01_svc_subcode = '200' then t01_sugup_soyotime else 0 end) as plan200"
		 . ",      sum(case when t01_svc_subcode = '500' then 1 else 0 end) as plan500"
		 . ",      sum(case when t01_svc_subcode = '800' then 1 else 0 end) as plan800"
		 . ",      sum(case when t01_svc_subcode = '200' and t01_status_gbn = '1' then t01_conf_soyotime else 0 end) as conf200"
		 . ",      sum(case when t01_svc_subcode = '500' and t01_status_gbn = '1' then 1 else 0 end) as conf500"
		 . ",      sum(case when t01_svc_subcode = '800' and t01_status_gbn = '1' then 1 else 0 end) as conf800"
		 . "  from t01iljung"
		 . " inner join m03sugupja"
		 . "    on m03_ccode = t01_ccode"
		 . "   and m03_mkind = t01_mkind"
		 . "   and m03_jumin = t01_jumin"
		 . " inner join m81gubun as LVL"
		 . "    on LVL.m81_gbn = 'LVL'"
		 . "   and LVL.m81_code = m03_ylvl"
		 . " where t01_ccode = '".$mCode
		 . "'  and t01_mkind = '".$mKind
		 . "'  and concat(t01_sugup_date, t01_sugup_fmtime) between '".$fromDate
		 . "'                                                   and '".$toDate
		 . "'  and t01_del_yn = 'N'"
		 . " group by m03_name, t01_jumin, LVL.m81_name"
		 . " order by m03_name";
	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();

	if ($row_count > 0){
		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);

			$plan200 = $myF->getMinToHM($row['plan200']);
			$plan500 = $myF->numberFormat($row['plan500'],'회');
			$plan800 = $myF->numberFormat($row['plan800'],'회');

			$conf200 = $myF->getMinToHM($row['conf200']);
			$conf500 = $myF->numberFormat($row['conf500'],'회');
			$conf800 = $myF->numberFormat($row['conf800'],'회');

			$rst200 = $myF->getMinToHM($row['conf200'] - $row['plan200']);
			$rst500 = $myF->numberFormat($row['conf500'] - $row['plan500'],'회');
			$rst800 = $myF->numberFormat($row['conf800'] - $row['plan800'],'회');

			if (subStr($rst200, 0, 1) == '-') $rst200 = '<span style="color:#ff0000;">'.$rst200.'</span>';
			if (subStr($rst500, 0, 1) == '-') $rst500 = '<span style="color:#ff0000;">'.$rst500.'</span>';
			if (subStr($rst800, 0, 1) == '-') $rst800 = '<span style="color:#ff0000;">'.$rst800.'</span>';

			echo '<tr>';
			echo '<td style="margin:0; padding:0; text-align:left;">'.$row['sugupjaNamd'].'</td>';
			echo '<td style="margin:0; padding:0; text-align:left;">'.$row['sugupjaJumin'].'</td>';
			echo '<td style="margin:0; padding:0; text-align:left;">'.$row['lvlName'].'</td>';
			echo '<td style="margin:0; padding:0,5px,0,0; text-align:right;">'.$plan200.'</td>';
			echo '<td style="margin:0; padding:0,5px,0,0; text-align:right;">'.$plan500.'</td>';
			echo '<td style="margin:0; padding:0,5px,0,0; text-align:right;">'.$plan800.'</td>';
			echo '<td style="margin:0; padding:0,5px,0,0; text-align:right; background:#f1f4f7;">'.$conf200.'</td>';
			echo '<td style="margin:0; padding:0,5px,0,0; text-align:right; background:#f1f4f7;">'.$conf500.'</td>';
			echo '<td style="margin:0; padding:0,5px,0,0; text-align:right; background:#f1f4f7;">'.$conf800.'</td>';
			echo '<td style="margin:0; padding:0,5px,0,0; text-align:right;">'.$rst200.'</td>';
			echo '<td style="margin:0; padding:0,5px,0,0; text-align:right;">'.$rst500.'</td>';
			echo '<td style="margin:0; padding:0,5px,0,0; text-align:right;">'.$rst800.'</td>';
			echo '<td style="margin:0; padding:0; text-align:center;">';
			echo '<input type="button" value="상세" class="btnSmall2" onClick="_nowMonthDiaryDetail(myDetail, \''.$mCode.'\',\''.$mKind.'\',\''.$mYear.$mMonth.'\',\''.$ed->en($row['sugupjaCode']).'\');">';
			echo '</td>';
			echo '</tr>';
		}
	}else{
		echo '<tr><td style="margin:0; padding:0; text-align:center;" colspan="13">::검색된 데이타가 없습니다.::</td></tr>';
	}

	$conn->row_free();

	if ($toDate == '00000000'){
		$fromDate = '';
		$toDate = '';
	}
?>
<input name="rngDate" type="hidden" value="<?=$myF->dateStyle($fromDate,'.');?>-<?=$myF->dateStyle($toDate,'.');?>">
</table>
<?
	include("../inc/_db_close.php");
?>