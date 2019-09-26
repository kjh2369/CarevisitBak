<?
	include('../inc/_header.php');

	$mCode  = $_POST['mCode']  != '' ? $_POST['mCode']  : $_SESSION['userCenterCode'];
	$mKind  = $_POST['mKind']  != '' ? $_POST['mKind']  : $_SESSION['userCenterKind'][0];
	$mDate  = $_POST['mDate']  != '' ? $_POST['mDate']  : date('Ymd', mKtime());
?>
<table class="view_type1" style="width:100%; margin-top:0px;">
<tr>
<th colspan="7" style="text-align:left;">
	<div style="width:auto; float:left;">[<?=getDateStyle($mDate, '.');?>]</div>
	<div style="width:auto; float:right;">
		[<a href="#" onClick="_printDayWork('<?=$mCode;?>','<?=$mKind;?>','<?=$mDate;?>','all');">전체</a>]
		[<a href="#" onClick="_printDayWork('<?=$mCode;?>','<?=$mKind;?>','<?=$mDate;?>','200');">방문요양</a>]
		[<a href="#" onClick="_printDayWork('<?=$mCode;?>','<?=$mKind;?>','<?=$mDate;?>','500');">방문목욕</a>]
		[<a href="#" onClick="_printDayWork('<?=$mCode;?>','<?=$mKind;?>','<?=$mDate;?>','800');">방문간호</a>]
		[<a href="#" onClick="_printDayWork('<?=$mCode;?>','<?=$mKind;?>','<?=$mDate;?>','20');">바우처</a>]
		[<a href="#" onClick="_printDayWork('<?=$mCode;?>','<?=$mKind;?>','<?=$mDate;?>','30');">기타유료</a>]
	</div>
</th>
</tr>
<tr>
<td style="width:6%;  background-color:#f4f5f6; padding:0px; text-align:center; font-weight:bold;">시간</td>
<td style="width:15%; background-color:#f4f5f6; padding:0px; text-align:center; font-weight:bold;">방문시간</td>
<td style="width:11%; background-color:#f4f5f6; padding:0px; text-align:center; font-weight:bold;">수급자</td>
<td style="width:15%; background-color:#f4f5f6; padding:0px; text-align:center; font-weight:bold;">서비스구분</td>
<td style="width:20%; background-color:#f4f5f6; padding:0px; text-align:center; font-weight:bold;">서비스종류</td>
<td style="width:13%; background-color:#f4f5f6; padding:0px; text-align:center; font-weight:bold;">담당보호사</td>
<td style="width:20%; background-color:#f4f5f6; padding:0px; text-align:center; font-weight:bold;">실시시간</td>
</tr>
<?
	$sql = "select t01_sugup_fmtime"
		 . ",      t01_sugup_totime"
		 . ",      m03_name"
		 . ",      t01_suga_code1"
		 . ",      t01_svc_subcode"
		 . ",      t01_mem_nm1 as yoyangsaName1"
		 . ",      t01_mem_nm2 as yoyangsaName2"
		 . ",      t01_wrk_fmtime"
		 . ",      t01_wrk_totime"
		 . "  from t01iljung"
		 . " inner join m03sugupja"
		 . "    on m03_ccode = t01_ccode"
		 . "   and m03_mkind = t01_mkind"
		 . "   and m03_jumin = t01_jumin"
		 . " where t01_ccode = '".$mCode
		 . "'  and t01_sugup_date = '".$mDate
		 . "'  and t01_del_yn = 'N'"
		 . " order by t01_sugup_fmtime"
		 . ",         t01_sugup_totime"
		 . ",         m03_name";

	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();

	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);
		$timeHour = ceil(subStr($row['t01_sugup_fmtime'],0,2)).'시';
		$timeRang = subStr($row['t01_sugup_fmtime'],0,2).':'.subStr($row['t01_sugup_fmtime'],2,2).'~'.subStr($row['t01_sugup_totime'],0,2).':'.subStr($row['t01_sugup_totime'],2,2);
		$sugupja  = $row['m03_name'];
		$sugaName = $conn->get_suga($mCode, $row['t01_suga_code1'], $row['t01_sugup_date']); //수가명
		$yoyangsa = $row['yoyangsaName1'];

		if ($row['yoyangsaName2'] != ''){
			$yoyangsa .= '/'.$row['yoyangsaName2'];
		}

		$workTime = subStr($row['t01_wrk_fmtime'],0,2).':'.subStr($row['t01_wrk_fmtime'],2,2).'~'.subStr($row['t01_wrk_totime'],0,2).':'.subStr($row['t01_wrk_totime'],2,2);
		$workTime = str_replace(':~:','',$workTime);
		$workTime = str_replace('~:','~',$workTime);

		$service  = $conn->kind_name_svc($row['t01_svc_subcode']);

		if ($tempTimeHour != $timeHour){
			$tempTimeHour  = $timeHour;
			$borderTop  = 'border-top:1px solid #cccccc;';
		}else{
			$timeHour   = '';
			$borderTop  = 'border-top:1px solid #ffffff;';
		}

		echo '<tr>';
		echo '<td style="padding:0px; text-align:center;'.$borderTop.'">'.$timeHour.'</td>';
		echo '<td style="padding:0px; text-align:center;'.$borderTop.'">'.$timeRang.'</td>';
		echo '<td style="padding:0px; text-align:center;'.$borderTop.'">'.$sugupja.'</td>';
		echo '<td style="padding:0px; text-align:left;  '.$borderTop.'">'.$service.'</td>';
		echo '<td style="padding:0px; text-align:left;  '.$borderTop.'">'.$sugaName.'</td>';
		echo '<td style="padding:0px; text-align:left;  '.$borderTop.'">'.$yoyangsa.'</td>';
		echo '<td style="padding:0px; text-align:center;'.$borderTop.'">'.$workTime.'</td>';
		echo '</tr>';
	}

	$conn->row_free();
?>
</table>
<?
	include('../inc/_footer.php');
?>