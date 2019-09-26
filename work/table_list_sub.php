<table class="view_type1" style="width:2000px;">
<colGroup>
	<col width="30px">
	<col width="100px">
	<col width="50px">
	<col width="15px">
	<col width="35px">
	<?
		for($i=1; $i<=$lastDay; $i++){
		?>
			<col width="20px">
		<?
		}
	?>
	<col width="60px">
	<col width="60px">
	<col>
</colGroup>
<tr>
	<th style="height:24px; padding:0px; text-align:center; border-right:1px solid #ccc; border-bottom:1px solid #ccc;">No.</th>
	<th style="height:24px; padding:0px; text-align:center; border-right:1px solid #ccc; border-bottom:1px solid #ccc;">요양보호사</th>
	<th style="height:24px; padding:0px; text-align:center; border-right:1px solid #ccc; border-bottom:1px solid #ccc;">수급자</th>
	<th style="height:24px; padding:0px; text-align:center; border-right:1px solid #ccc; border-bottom:1px solid #ccc;" colspan="2">시간</th>
	<?
		for($i=1; $i<=$lastDay; $i++){
		?>
			<th style='height:24px; padding:0px; text-align:center; border-right:1px solid #ccc; border-bottom:1px solid #ccc;'><?=$i;?></th>
		<?
		}
	?>
	<th style="height:24px; padding:0px; text-align:center; border-right:1px solid #ccc; border-bottom:1px solid #ccc;">근무일수</th>
	<th style="height:24px; padding:0px; text-align:center; border-right:1px solid #ccc; border-bottom:1px solid #ccc;">총시간</th>
	<th style="height:24px; padding:0px; text-align:center; border-bottom:1px solid #ccc;">비고</th>
</tr>
<?
	if ($mPlan == "plan"){
		$filed = "sugup";
	}else{
		$filed = "conf";
	}

	$sql = "select t01_svc_subcode as serviceCode
			,      case t01_svc_subcode when '200' then '요양' when '500' then '목욕' when '800' then '간호' else '-' end as serviceName
			,      concat(t01_yname1, case when t01_yname2 != '' then concat('<br>/', t01_yname2) else '' end) as yoyangsa
			,      m03_name as sugupja
			,      t01_sugup_date as sugupDate
			,      t01_sugup_fmtime as fromTime
			,      t01_sugup_totime as toTime
			,      t01_sugup_soyotime as soyoTime

			,      t01_conf_date as confDate
			,      t01_conf_fmtime as confFrom
			,      t01_conf_totime as confTo
			,	   CASE WHEN t01_svc_subcode = '200' AND t01_bipay_umu != 'Y' THEN
							 t01_conf_soyotime - CASE WHEN t01_conf_soyotime >= 270 THEN 30 ELSE 0 END
						ELSE t01_conf_soyotime END as confSoyo

			  from t01iljung
			 inner join m03sugupja
				on m03_ccode = t01_ccode
			   and m03_mkind = t01_mkind
			   and m03_jumin = t01_jumin
			 where t01_ccode = '$mCode'
			   and t01_mkind = '$mKind'
			   and t01_".$filed."_date like '$mDate%'
			   and t01_del_yn = 'N'";

	if ($mService != 'all'){
		$sql .= "
			   and t01_svc_subcode = '$mService'";
	}

	$sql .= "
			 order by serviceCode, yoyangsa, sugupja, fromTime, toTime, sugupDate";
	$conn->query($sql);
	$conn->fetch();
	$rowCount = $conn->row_count();
	$rows = 0;

	if ($rowCount > 0){
		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);
			
			if ($tempRow != $row['serviceCode']."/".$row["yoyangsa"]."/".$row["sugupja"]."/".$row["fromTime"]."/".$row["toTime"]){
				$tempRow  = $row['serviceCode']."/".$row["yoyangsa"]."/".$row["sugupja"]."/".$row["fromTime"]."/".$row["toTime"];
				
				$rows ++;
					
				if ($cell[$rows-1]["yoy"].$cell[$rows-1]["su"] != $row["yoyangsa"].$row["sugupja"]){						
					$row_no ++;
					$cell[$rows]["no"] = $row_no;
					$cell[$rows]["yoy"] = $row["yoyangsa"];
					$cell[$rows]["su"] = $row["sugupja"];
				}else{
					$cell[$rows]["no"] = "";
					$cell[$rows]["yoy"] = "";
					$cell[$rows]["su"] = "";
				}
				
				$cell[$rows]["service"] = $row["serviceName"];
				$cell[$rows]["soyo"] = ($mPlan == 'plan' ? $row["soyoTime"] : $row["confSoyo"]);;
				if($mPlan == 'plan'){
					$cell[$rows]["time"] = subStr($row["fromTime"], 0, 2).":".subStr($row["fromTime"], 2, 2)."~".subStr($row["toTime"], 0, 2).":".subStr($row["toTime"], 2, 2);
				}else {
					$cell[$rows]["time"] = subStr($row["confFrom"], 0, 2).":".subStr($row["confFrom"], 2, 2)."~".subStr($row["confTo"], 0, 2).":".subStr($row["confTo"], 2, 2);
				}
				for($j=1; $j<=$lastDay; $j++){
					$cell[$rows][$j] = 0;
				}
			}

			//$cell[$rows][intVal(subStr($row["sugupDate"], 6, 2))] ++;
			$cell[$rows][intVal(subStr($row["sugupDate"], 6, 2))] = ($mPlan == 'plan' ? number_format($row["soyoTime"] / 60, 1) : number_format($row["confSoyo"] / 60, 1));

			if ($tempWorkDay != $row["sugupDate"]){
				$cell[$rows]["workDay"] ++;
			}

			$cell[$rows]["workTime"] += ($mPlan == 'plan' ? $row["soyoTime"] : $row["confSoyo"]);
		}

		$totalTime = 0;
		$rows = sizeOf($cell);

		for($row=1; $row<=$rows; $row++){
			if ($cell[$row]["no"] == ""){
				$borderTop = "border-top:1px solid #ffffff;";
			}else{
				$borderTop = "";
			}

			if ($mService == 'all'){
				if ($tempService != $cell[$row]["service"]){
					$tempService  = $cell[$row]["service"];
				?>
					<tr>
						<th colspan="<?=$cols;?>" style="background-color:#eeeeee;"><?=$cell[$row]["service"];?></th>
					</tr>
				<?
				}
			}

			echo "<tr>";
			echo "<td style='text-align:center; border-right:1px solid #ccc; border-bottom:1px solid #ccc; $borderTop;'>".$cell[$row]["no"]."</td>";
			echo "<td style='text-align:left; border-right:1px solid #ccc; border-bottom:1px solid #ccc; line-height:1.3em; $borderTop'>".$cell[$row]["yoy"]."</td>";
			echo "<td style='text-align:center; border-right:1px solid #ccc; border-bottom:1px solid #ccc; $borderTop'>".$cell[$row]["su"]."</td>";
			echo "<td style='text-align:center; border-right:1px solid #ccc; border-bottom:1px solid #ccc; background:#f4f5f6;'>".number_format($cell[$row]["soyo"] / 60, 1)."</td>";
			echo "<td style='text-align:left; border-right:1px solid #ccc; border-bottom:1px solid #ccc; background:#f4f5f6; line-height:1.3em;'>".$cell[$row]["time"]."</td>";

			for($i=1; $i<=$lastDay; $i++){
				echo "<td style='text-align:center; border-right:1px solid #ccc; border-bottom:1px solid #ccc; mso-number-format:\@;'>".($cell[$row][$i] > 0 ? $cell[$row][$i] : "")."</td>";
			}
			echo "<td style='text-align:center; border-right:1px solid #ccc; border-bottom:1px solid #ccc;'>".$cell[$row]["workDay"]."</td>";
			echo "<td style='text-align:center; border-right:1px solid #ccc; border-bottom:1px solid #ccc; mso-number-format:\@;'>".number_format($cell[$row]["workTime"] / 60, 1)."</td>";
			echo "<td style='text-align:left;'></td>";
			echo "</tr>";

			$totalTime += $cell[$row]["workTime"];

			if ($cell[$row+1]["no"] != "" && $cell[$row]["yoy"].$cell[$row]["su"] != $cell[$row+1]["yoy"].$cell[$row+1]["su"]){
				echo "<tr>";
				echo "<td style='border-right:1px solid #ccc; border-top:1px solid #ffffff; border-bottom:1px solid #ccc;'></td>";
				echo "<td style='border-right:1px solid #ccc; border-top:1px solid #ffffff; border-bottom:1px solid #ccc;'></td>";
				echo "<td style='border-right:1px solid #ccc; border-top:1px solid #ffffff; border-bottom:1px solid #ccc;'></td>";
				echo "<td style='text-align:center; border-right:1px solid #ccc; border-bottom:1px solid #ccc; background:#f4f5f6;'>계</td>";
				echo "<td style='border-right:1px solid #ccc; background:#f4f5f6; border-bottom:1px solid #ccc;' colspan='".($lastDay+2)."'></td>";
				echo "<td style='text-align:center; border-right:1px solid #ccc; border-bottom:1px solid #ccc; background:#f4f5f6; mso-number-format:\@;'>".number_format($totalTime / 60, 1)."</td>";
				echo "</tr>";

				$totalTime = 0;
			}
		}
		echo "<tr>";
		echo "<td style='border-right:1px solid #ccc; border-top:1px solid #ffffff; border-bottom:1px solid #ccc;'></td>";
		echo "<td style='border-right:1px solid #ccc; border-top:1px solid #ffffff; border-bottom:1px solid #ccc;'></td>";
		echo "<td style='border-right:1px solid #ccc; border-top:1px solid #ffffff; border-bottom:1px solid #ccc;'></td>";
		echo "<td style='text-align:center; border-right:1px solid #ccc; border-bottom:1px solid #ccc; background:#f4f5f6;'>계</td>";
		echo "<td style='border-right:1px solid #ccc; background:#f4f5f6; border-bottom:1px solid #ccc;' colspan='".($lastDay+2)."'></td>";
		echo "<td style='text-align:center; border-right:1px solid #ccc; border-bottom:1px solid #ccc; background:#f4f5f6; mso-number-format:\@;'>".number_format($totalTime / 60, 1)."</td>";
		echo "</tr>";
	}else{
	?>
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td colspan="<?=$lastDay;?>" style="text-align:center;">::검색된 데이타가 없습니다.::</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
	<?
	}
	$conn->row_free();
?>
</table>