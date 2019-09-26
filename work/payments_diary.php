<?
	include('../inc/_header.php');
	
	$mCode  = $_GET['mCode'];
	$mKind  = $_GET['mKind'];
	$mDate  = $_GET['mDate'];
	$mBoninYul= $_GET['mBoninYul'];
	$mKey = $_GET['mKey'];
	$mYear = subStr($mDate, 0, 4);
	$mMonth = subStr($mDate, 4, 2);
	$mJumin = $conn->get_sugupja_jumin($mCode, $mKind, $mKey);
?>
<style>
body{
	margin-top:0px;
	margin-left:0px;
}
</style>
<table style="width:100%; height:100%;">
<tr>
<th colspan="14" style="height:5%; border:none; padding:0px; text-align:center; font-weight:bold;"><?=$mYear;?>년 <?=$mMonth;?>월</th>
</tr>
<?
	$calTime   = mkTime(0, 0, 1, $mMonth, 1, $mYear);
	$today     = date('Ymd', mktime());
	$lastDay   = date('t', $calTime); //총일수 구하기
	$startWeek = date('w', strtotime(date('Y-m', $calTime).'-01')); //시작요일 구하기
	$totalWeek = ceil(($lastDay + $startWeek) / 7); //총 몇 주인지 구하기
	$lastWeek  = date('w', strtotime(date('Y-m', $calTime).'-'.$lastDay)); //마지막 요일 구하기
	$day       = 1; //화면에 표시할 화면의 초기값을 1로 설정

	$sql = "select t01_conf_date"
		 . ",      t01_conf_fmtime"
		 . ",      t01_conf_totime"
		 . ",      m02_yname"
		 . ",      case when length(ifnull(t01_yoyangsa_id2, '')) > 0 then 1 else 0 end +"
		 . "       case when length(ifnull(t01_yoyangsa_id3, '')) > 0 then 1 else 0 end +"
		 . "       case when length(ifnull(t01_yoyangsa_id4, '')) > 0 then 1 else 0 end +"
		 . "       case when length(ifnull(t01_yoyangsa_id5, '')) > 0 then 1 else 0 end as yoyCount"
		 . ",      suga_cont"
		 . "  from t01iljung"
		 . " inner join m02yoyangsa"
		 . "    on m02_ccode  = t01_ccode"
		 . "   and m02_mkind  = t01_mkind"
		 . "   and m02_yjumin = t01_yoyangsa_id1"
		 . " inner join ("
		 . "       select m01_mcode as suga_mcode"
		 . "       ,      m01_mcode2 as suga_mcode2"
		 . "       ,      m01_suga_cont as suga_cont"
		 . "       ,      m01_sdate as suga_sdate"
		 . "       ,      m01_edate as suga_edate"
		 . "         from m01suga"
		 . "        where m01_mcode = '".$mCode
		 . "'      union all"
		 . "       select m11_mcode as suga_mcode"
		 . "       ,      m11_mcode2 as suga_mcode2"
		 . "       ,      m11_suga_cont as suga_cont"
		 . "       ,      m11_sdate as suga_sdate"
		 . "       ,      m11_edate as suga_edate"
		 . "         from m11suga"
		 . "        where m11_mcode = '".$mCode
		 . "'     ) as suga_table"
		 . "    on t01_ccode = suga_mcode"
		 . "   and t01_conf_suga_code = suga_mcode2"
		 . "   and t01_conf_date between suga_sdate and suga_edate"
		 . " where t01_ccode = '".$mCode
		 . "'  and t01_mkind = '".$mKind
		 . "'  and t01_jumin = '".$mJumin
		 . "'  and left(t01_conf_date, 6) = '".$mDate
		 . "'  and length(ifnull(t01_conf_fmtime, '')) = 4"
		 . "   and length(ifnull(t01_conf_totime, '')) = 4"
		 . "   and t01_conf_soyotime > 0"
		 . "   and t01_del_yn = 'N'"
		 . " order by t01_conf_date, t01_conf_fmtime, t01_conf_totime";
	
	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();

	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);
		
		$rowData[$i]['Date']   = $row['t01_conf_date'];
		$rowData[$i]['FmTime'] = SubStr($row['t01_conf_fmtime'], 0, 2).':'.SubStr($row['t01_conf_fmtime'], 2, 2);
		$rowData[$i]['ToTime'] = SubStr($row['t01_conf_totime'], 0, 2).':'.SubStr($row['t01_conf_totime'], 2, 2);
		$rowData[$i]['Yname']  = $row['m02_yname'];
		$rowData[$i]['Ycount'] = $row['yoyCount'];
		$rowData[$i]['Suga']   = $row['suga_cont'];
	}
	$conn->row_free();

	$rowCount = sizeOf($rowData);
	
	// 총 주 수에 맞춰서 세로줄 만들기
	for($i=1; $i<=6; $i++){
		if ($i == 1){
			echo '<tr>';
			echo '<td colspan="2" style="height:5%; border:none; border-top:1px solid #cccccc; border-bottom:1px solid #cccccc; color:#ff0000;">일</td>';
			echo '<td colspan="2" style="height:5%; border:none; border-top:1px solid #cccccc; border-bottom:1px solid #cccccc;">월</td>';
			echo '<td colspan="2" style="height:5%; border:none; border-top:1px solid #cccccc; border-bottom:1px solid #cccccc;">화</td>';
			echo '<td colspan="2" style="height:5%; border:none; border-top:1px solid #cccccc; border-bottom:1px solid #cccccc;">수</td>';
			echo '<td colspan="2" style="height:5%; border:none; border-top:1px solid #cccccc; border-bottom:1px solid #cccccc;">목</td>';
			echo '<td colspan="2" style="height:5%; border:none; border-top:1px solid #cccccc; border-bottom:1px solid #cccccc;">금</td>';
			echo '<td colspan="2" style="height:5%; border:none; border-top:1px solid #cccccc; border-bottom:1px solid #cccccc; color:#0000ff;">토</td>';
			echo '</tr>';
		}
		echo '<tr>';

		// 총 가로칸 만들기
		for ($j=0; $j<7; $j++){
			switch($j){
				case 0: echo '<td style="width:2%; height:15%; border:none; border-bottom:#cccccc 1px solid; vertical-align:top; background-color:#eeeeee;">'; break;
				case 1: echo '<td style="width:2%; height:15%; border:none; border-bottom:#cccccc 1px solid; vertical-align:top; background-color:#eeeeee;">'; break;
				case 2: echo '<td style="width:2%; height:15%; border:none; border-bottom:#cccccc 1px solid; vertical-align:top; background-color:#eeeeee;">'; break;
				case 3: echo '<td style="width:2%; height:15%; border:none; border-bottom:#cccccc 1px solid; vertical-align:top; background-color:#eeeeee;">'; break;
				case 4: echo '<td style="width:2%; height:15%; border:none; border-bottom:#cccccc 1px solid; vertical-align:top; background-color:#eeeeee;">'; break;
				case 5: echo '<td style="width:2%; height:15%; border:none; border-bottom:#cccccc 1px solid; vertical-align:top; background-color:#eeeeee;">'; break;
				case 6: echo '<td style="width:2%; height:15%; border:none; border-bottom:#cccccc 1px solid; vertical-align:top; background-color:#eeeeee;">'; break;
			}

			$drawDay = 0;
			
			if (!(($i == 1 && $j < $startWeek) || ($i == $totalWeek && $j > $lastWeek)) && ($i <= $totalWeek)){
				$mDate = $mYear.$mMonth.(($day < 10 ? '0' : '').$day);

				if ($today == $mDate){
					$fontBold = 'font-weight:bold;';
				}else{
					$fontBold = '';
				}
				echo '<a href="#" onClick="_getWorkList(myBody, \''.$mCode.'\', \''.$mKind.'\', \''.$mDate.'\');">';
				if($j == 0){
					echo '<font color="#FF0000" style="'.$fontBold.'">'.$day.'</font>';
				}else if($j == 6){
					echo '<font color="#0000FF" style="'.$fontBold.'">'.$day.'</font>';
				}else{
					echo '<font color="#000000" style="'.$fontBold.'">'.$day.'</font>';
				}
				echo '</a>';

				$drawDay = $day;
				$day ++;
			}
			echo '</td>';

			switch($j){
				case 0: echo '<td style="width:13%; height:15%; border:none; border-bottom:#cccccc 1px solid; line-height:1.2em; vertical-align:top; text-align:left;">'; break;
				case 1: echo '<td style="width:12%; height:15%; border:none; border-bottom:#cccccc 1px solid; line-height:1.2em; vertical-align:top; text-align:left;">'; break;
				case 2: echo '<td style="width:12%; height:15%; border:none; border-bottom:#cccccc 1px solid; line-height:1.2em; vertical-align:top; text-align:left;">'; break;
				case 3: echo '<td style="width:12%; height:15%; border:none; border-bottom:#cccccc 1px solid; line-height:1.2em; vertical-align:top; text-align:left;">'; break;
				case 4: echo '<td style="width:12%; height:15%; border:none; border-bottom:#cccccc 1px solid; line-height:1.2em; vertical-align:top; text-align:left;">'; break;
				case 5: echo '<td style="width:12%; height:15%; border:none; border-bottom:#cccccc 1px solid; line-height:1.2em; vertical-align:top; text-align:left;">'; break;
				case 6: echo '<td style="width:13%; height:15%; border:none; border-bottom:#cccccc 1px solid; line-height:1.2em; vertical-align:top; text-align:left;">'; break;
			}

			if ($drawDay > 0){
				$dairyBorder = '';
				for($k=0; $k<$rowCount; $k++){
					if ($drawDay == Ceil(SubStr($rowData[$k]['Date'], 6, 2))){
						echo '<div style="'.$dairyBorder.'">';
						echo $rowData[$k]['FmTime'].'~'.$rowData[$k]['ToTime'].'<br>';
						echo $rowData[$k]['Yname'];

						if ($rowData[$k]['Ycount'] > 0){
							echo '외 '.$rowData[$k]['Ycount'].'명';
						}
						echo '<br>';
						echo $rowData[$k]['Suga'];
						echo '</div>';

						$dairyBorder = 'border-top:#cccccc dashed 1px;';
					}
				}
			}
			
			echo '</td>';
		}
		echo '</tr>';
	}
?>
</tr>
</table>
<?
	include('../inc/_footer.php');
?>
<script>self.focus();</script>