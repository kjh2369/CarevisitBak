<?
	include_once('../inc/_header.php');

	$mCode  = $_POST['mCode']  != '' ? $_POST['mCode']  : $_SESSION['userCenterCode'];
	$mKind  = $_POST['mKind']  != '' ? $_POST['mKind']  : $_SESSION['userCenterKind'][0];
	$mYear  = $_POST['mYear']  != '' ? $_POST['mYear']  : date('Y', mKtime());
	$mMonth = $_POST['mMonth'] != '' ? $_POST['mMonth'] : date('m', mKtime());
	$mMonth = ($mMonth < 10 ? '0' : '').ceil($mMonth);

	if (ceil($mMonth) == 1){
		$beYear  = $mYear - 1;
		$beMonth = 12;
		$neYear  = $mYear;
		$neMonth = $mMonth + 1;
	}else if (ceil($mMonth) == 12){
		$neYear  = $mYear + 1;
		$neMonth = 1;
		$beYear  = $mYear;
		$beMonth = $mMonth - 1;
	}else{
		$beYear  = $mYear;
		$beMonth = $mMonth - 1;
		$neYear  = $mYear;
		$neMonth = $mMonth + 1;
	}
?>
<table style="width:160px;">
<tr>
<td style="border:none; width:160px; text-align:left;">
	<table class="view_type1" style="margin-top:0px;">
	<tr>
	<th colspan="7" style="height:26px; border:none; padding:0px; text-align:center; font-weight:bold;">
		<a href="#" onClick="_getCalendar(myCalendar, '<?=$mCode;?>', '<?=$mKind;?>', '<?=$mYear-1;?>', '<?=$mMonth;?>')"><img src='../image/ico_prev_ca.gif'></a>
		<?=$mYear;?>년
		<a href="#" onClick="_getCalendar(myCalendar, '<?=$mCode;?>', '<?=$mKind;?>', '<?=$mYear+1;?>', '<?=$mMonth;?>')"><img src='../image/ico_next_ca.gif'></a>

		<a href="#" onClick="_getCalendar(myCalendar, '<?=$mCode;?>', '<?=$mKind;?>', '<?=$beYear;?>', '<?=$beMonth;?>')"><img src='../image/ico_prev_ca.gif'></a>
		 <?=$mMonth;?>월
		<a href="#" onClick="_getCalendar(myCalendar, '<?=$mCode;?>', '<?=$mKind;?>', '<?=$neYear;?>', '<?=$neMonth;?>')"><img src='../image/ico_next_ca.gif'></a>
	</th>
	</tr>
	<?
		$calTime   = mkTime(0, 0, 1, $mMonth, 1, $mYear);
		$today     = date('Ymd', mktime());
		$lastDay   = date('t', $calTime); //총일수 구하기
		$startWeek = date('w', strtotime(date('Y-m', $calTime).'-01')); //시작요일 구하기
		$totalWeek = ceil(($lastDay + $startWeek) / 7); //총 몇 주인지 구하기
		$lastWeek  = date('w', strtotime(date('Y-m', $calTime).'-'.$lastDay)); //마지막 요일 구하기
		$day     = 1; //화면에 표시할 화면의 초기값을 1로 설정

		// 총 주 수에 맞춰서 세로줄 만들기
		for($i=1; $i<=$totalWeek; $i++){
			if ($i == 1){
				echo '<tr>';
				echo '<td style="border:none; border-top:1px solid #cccccc; border-bottom:1px solid #cccccc; width:20px; height:10px; color:#ff0000;">일</td>';
				echo '<td style="border:none; border-top:1px solid #cccccc; border-bottom:1px solid #cccccc; width:20px; height:10px;">월</td>';
				echo '<td style="border:none; border-top:1px solid #cccccc; border-bottom:1px solid #cccccc; width:20px; height:10px;">화</td>';
				echo '<td style="border:none; border-top:1px solid #cccccc; border-bottom:1px solid #cccccc; width:20px; height:10px;">수</td>';
				echo '<td style="border:none; border-top:1px solid #cccccc; border-bottom:1px solid #cccccc; width:20px; height:10px;">목</td>';
				echo '<td style="border:none; border-top:1px solid #cccccc; border-bottom:1px solid #cccccc; width:20px; height:10px;">금</td>';
				echo '<td style="border:none; border-top:1px solid #cccccc; border-bottom:1px solid #cccccc; border-right:1px solid #cccccc; width:20px; height:10px; color:#0000ff;">토</td>';
				echo '</tr>';
			}
			echo '<tr>';

			// 총 가로칸 만들기
			for ($j=0; $j<7; $j++){
				if ($i == $totalWeek){
					$borderBottom = 'border-bottom:1px solid #cccccc;';
				}else{
					$borderBottom = '';
				}

				switch($j){
					case 0: echo '<td style="border:none;'.$borderBottom.'height:10px;">'; break;
					case 1: echo '<td style="border:none;'.$borderBottom.'height:10px;">'; break;
					case 2: echo '<td style="border:none;'.$borderBottom.'height:10px;">'; break;
					case 3: echo '<td style="border:none;'.$borderBottom.'height:10px;">'; break;
					case 4: echo '<td style="border:none;'.$borderBottom.'height:10px;">'; break;
					case 5: echo '<td style="border:none;'.$borderBottom.'height:10px;">'; break;
					case 6: echo '<td style="border:none;'.$borderBottom.'border-right:1px solid #cccccc; height:10px;">'; break;
				}

				if (!(($i == 1 && $j < $startWeek) || ($i == $totalWeek && $j > $lastWeek))){
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
					$day ++;
				}
				echo '</td>';
			}
			echo '</tr>';
		}
	?>
	</table>
</td>
</tr>
</table>
<?
	include_once('../inc/_footer.php');
?>