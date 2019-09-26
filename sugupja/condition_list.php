<?
	include_once("../inc/_header.php");
	include_once("../inc/_myFun.php");
	include_once('../inc/_ed.php');

	$mCode = $_POST['mCode'];
	$mKind = $_POST['mKind'];
	$mGubun = $_POST['mGubun'];
?>
<table class="view_type1" style="width:100%; height:100%;">
<tr>
	<th style="width:20%; height:24px; padding:0px; text-align:center;">구분</th>
	<?
		$statusList = $definition->SugupjaStatusList();
		$statusCount = sizeOf($statusList);

		for($i=0; $i<$statusCount; $i++){
			echo '<th style="width:10%; height:24px; padding:0px; text-align:center;">'.$statusList[$i]['name'].'</th>';
		}
	?>
	<th style="width:10%; height:24px; padding:0px; text-align:center;">계</th>
</tr>
<?
	$sql = "select ".($mGubun == '1' ? "m81_name" : "concat(m92_cont, '(', cast(m92_bonin_yul as char), ')')")." as gubun
			,      ".($mGubun == '1' ? "m81_code" : "m92_code")." as gubunCode";

	for($i=0; $i<$statusCount; $i++){
		$sql .= ", sum(case m03_sugup_status when '".$statusList[$i]['code']."' then 1 else 0 end) as gubun".$statusList[$i]['code'];
	}
	$sql .="  from m03sugupja";

	if ($mGubun == '1'){
		$sql .= " inner join m81gubun
					 on m81_gbn = 'LVL'
				    and m81_code = m03_ylvl";
	}else{
		$sql .= " inner join m92boninyul
		             on m92_code = m03_skind
					and m92_edate = '99999999'";
	}

	$sql .=" where m03_ccode = '$mCode'
			   and m03_mkind = '$mKind'
			 group by ".($mGubun == '1' ? 'm81_name, m81_code' : 'm92_cont, m92_bonin_yul')."
			 order by gubunCode";

	$conn->query($sql);
	$conn->fetch();
	$rowCount = $conn->row_count();
	$trString = '';

	for($i=0; $i<$statusCount; $i++){
		$statusValue[$statusList[$i]['code']] = 0;
	}
	$statusValue['total'] = 0;

	if ($rowCount > 0){
		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			$trString .= '<tr>';

			$trString .= '<td style="text-align:left;">'.$row['gubun'].'</td>';

			$sumGubun = 0;

			for($j=0; $j<$statusCount; $j++){
				$sumGubun += $row['gubun'.$statusList[$j]['code']];
				$statusValue[$statusList[$j]['code']] += $row['gubun'.$statusList[$j]['code']];
				$statusValue['total'] += $row['gubun'.$statusList[$j]['code']];
				$trString .= '<td style="text-align:center;">'.$row['gubun'.$statusList[$j]['code']].'</td>';
			}

			$trString .= '<td style="text-align:center;">'.$sumGubun.'</td>';

			$trString .= '</tr>';
		}

		$thString  = '<tr>';
		$thString .= '<td style="text-align:left; background:#eeeeee; font-weight:bold;">계</td>';

		for($i=0; $i<$statusCount; $i++){
			$thString .= '<td style="text-align:center; background:#eeeeee; font-weight:bold;">'.$statusValue[$statusList[$i]['code']].'</td>';
		}
		$thString .= '<td style="text-align:center; background:#eeeeee; font-weight:bold;">'.$statusValue['total'].'</td>';
		$thString .= '</tr>';

		echo $thString.$trString;
	}else{
		echo '<tr><td style="text-align:center;" colspan="'.($statusCount+2).'">::검색된 데이타가 없습니다.::</td></tr>';
	}
	$conn->row_free();
?>
</table>
<?
	include_once("../inc/_footer.php");
?>