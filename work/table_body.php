<?
	include_once('../inc/_header.php');
	//include_once('../inc/_body_header.php');
	include_once("../inc/_myFun.php");

	$mCode = $_POST['mCode'] != '' ? $_POST['mCode'] : $_SESSION['userCenterCode'];
	$mKind = $_POST['mKind'] != "" ? $_POST['mKind'] : $_SESSION["userCenterKind"][0];
	$mYear = $_POST['mYear'] != '' ? $_POST['mYear'] : date('Y', mkTime());
	$mMonth = $_POST['mMonth'] != '' ? $_POST['mMonth'] : date('m', mkTime());

	$mMonth = ($mMonth < 10 ? '0' : '').intval($mMonth);

	$setYear[0] = 2010;
	$setYear[1] = date('Y', mkTime());

	$mService = $_POST['mService'];
	$mDate = $mYear.$mMonth;
	$lastDay = $myF->lastDay($mYear, $mMonth);
?>
	<script language="javascript">
		function resize_table(){
			var scroll = document.getElementById('table_scroll');
			var height = document.body.clientHeight - 320;

			if (height < 250) height = 250;

			scroll.style.height = document.body.clientHeight - 320;
		}
	</script>
	<div id="table_scroll" style="overflow-x:hidden; overflow-y:scroll; width:854px; height:100px;">
		<table class="my_table">
			<colGroup>
				<col width="30px">
				<col width="100px">
				<col width="80px">
				<col width="30px">
				<col width="50px">
				<col width="35px">
				<?
					for($i=1; $i<=$lastDay; $i++){
					?>
						<col width="25px">
					<?
					}
				?>
				<col width="60px">
				<col width="60px">
				<col>
			</colGroup>
			<tbody>
			<?
				$sql = "select idx, serviceCode, serviceName, yoyangsa, sugupja";

				for($i=1; $i<=31; $i++){
					$sql .= ", sum(dt".($i<10?'0':'').$i.") as dt".($i<10?'0':'').$i;
				}

				$sql .= ", sum(";

				for($i=1; $i<=31; $i++){
					if ($i > 1) $sql .= " + ";
					$sql .= " dt".($i<10?'0':'').$i;
				}

				$sql .= ") as tot_dt";

				$sql .="
						,      fromTime, toTime, soyoTime
						  from (";

				for($i=1; $i<=31; $i++){
					if ($i > 1) $sql .= " union all ";
					$sql .= "select '1' as idx
							,      t01_svc_subcode as serviceCode
							,      case t01_svc_subcode when '200' then '요양' when '500' then '목욕' when '800' then '간호' else '-' end as serviceName
							,      concat(t01_yname1, case when t01_yname2 != '' then concat('/', t01_yname2) else '' end) as yoyangsa
							,      m03_name as sugupja ";

					for($j=1; $j<$i; $j++){
						$sql .= "
								,      0 as dt".($j<10?'0':'').$j."";
					}


					$sql .= "
							,      1 as dt".($i<10?'0':'').$i." ";

					for($j=$i+1; $j<=31; $j++){
						$sql .= "
								,      0 as dt".($j<10?'0':'').$j." ";
					}


					$sql .= "
							,      t01_sugup_fmtime as fromTime
							,      t01_sugup_totime as toTime
							,      t01_sugup_soyotime as soyoTime
							  from t01iljung
							 inner join m03sugupja
								on m03_ccode = t01_ccode
							   and m03_mkind = t01_mkind
							   and m03_jumin = t01_jumin
							 where t01_ccode = '$mCode'
							   and t01_mkind = '$mKind'
							   and t01_sugup_date = '$mDate".($i<10?'0':'').$i."'
							   and t01_del_yn = 'N' ";
				}

				$sql .= "
						  ) as t
						 group by idx, serviceCode, serviceName, yoyangsa, sugupja, fromTime, toTime, soyoTime";


				$sql .= " union all ";
				$sql .= "select idx, serviceCode, serviceName, yoyangsa, sugupja";

				for($i=1; $i<=31; $i++){
					$sql .= ", sum(dt".($i<10?'0':'').$i.") as dt".($i<10?'0':'').$i;
				}

				$sql .= ", sum(";

				for($i=1; $i<=31; $i++){
					if ($i > 1) $sql .= " + ";
					$sql .= " dt".($i<10?'0':'').$i;
				}

				$sql .= ") as tot_dt";

				$sql .="
						,      fromTime, toTime, soyoTime
						  from (";

				for($i=1; $i<=31; $i++){
					if ($i > 1) $sql .= " union all ";
					$sql .= "select '2' as idx
							,      t01_svc_subcode as serviceCode
							,      case t01_svc_subcode when '200' then '요양' when '500' then '목욕' when '800' then '간호' else '-' end as serviceName
							,      concat(t01_yname1, case when t01_yname2 != '' then concat('/', t01_yname2) else '' end) as yoyangsa
							,      m03_name as sugupja ";

					for($j=1; $j<$i; $j++){
						$sql .= "
								,      0 as dt".($j<10?'0':'').$j." ";
					}


					$sql .= "
							,      case when t01_status_gbn = '1' then 1 else 0 end as dt".($i<10?'0':'').$i." ";

					for($j=$i+1; $j<=31; $j++){
						$sql .= "
								,      0 as dt".($j<10?'0':'').$j." ";
					}



					$sql .= "
							,      t01_sugup_fmtime as fromTime
							,      t01_sugup_totime as toTime
							,      t01_sugup_soyotime as soyoTime
							  from t01iljung
							 inner join m03sugupja
								on m03_ccode = t01_ccode
							   and m03_mkind = t01_mkind
							   and m03_jumin = t01_jumin
							 where t01_ccode = '$mCode'
							   and t01_mkind = '$mKind'
							   and t01_sugup_date = '$mDate".($i<10?'0':'').$i."'
							   and t01_del_yn = 'N' ";
				}

				$sql .= "
						  ) as t
						 group by idx, serviceCode, serviceName, yoyangsa, sugupja, fromTime, toTime, soyoTime";



				$sql .= "
						 order by serviceCode, yoyangsa, sugupja, fromTime, toTime, idx";

				$conn->query($sql);
				$conn->fetch();
				$rowCount = $conn->row_count();
				$rows = 0;
				for($i=0; $i<$rowCount; $i++){
					$row = $conn->select_row($i);

					$time = subStr($row["fromTime"], 0, 2).":".subStr($row["fromTime"], 2, 2)."<br>~".subStr($row["toTime"], 0, 2).":".subStr($row["toTime"], 2, 2);


					for($j=1; $j<=$lastDay; $j++){
						if($row['dt'.($j<10?'0':'').$j] == 1){
							$soyo = $row['soyoTime'];
						}else {
							$soyo = '';
						}

						$totalTime += $soyo;
					}


					echo "<tr>";
					/*
					if($row[$i-1]["yoyangsa"].$row[$i-1]["sugupja"] != $row[$i]["yoyangsa"].$row[$i]["sugupja"]){
						echo "<td>".$rows."</td>";
						echo "<td>".$row['yoyangsa']."</td>";
						echo "<td>".$row['sugupja']."</td>";
						echo "<td>".$totalTime."</td>";
					}else {
						echo "<td rowspan=2>".$rows."</td>";
						echo "<td rowspan=2>".$row['yoyangsa']."</td>";
						echo "<td rowspan=2>".$row['sugupja']."</td>";
						echo "<td rowspan=2>".$totalTime."</td>";
					}
					*/
					if($row['idx'] == 1){
						$rows++;
						echo "<td style='text-align:center;' rowspan=2>".$rows."</td>";
						echo "<td style='text-align:left; padding-left:5px;' rowspan=2>".$row['yoyangsa']."</td>";
						echo "<td style='text-align:left; padding-left:5px;' rowspan=2>".$row['sugupja']."</td>";
						echo "<td style='text-align:center;' rowspan=2>".number_format($row['soyoTime'] / 60, 1)."</td>";
						echo "<td style='text-align:left; padding-left:5px;' rowspan=2>".$time."</td>";
					}
					if($row['idx'] == 1){
						echo "<td style='text-align:center;'>계획</td>";
					}else {
						echo "<td style='text-align:center;'>실적</td>";
					}

					for($j=1; $j<=$lastDay; $j++){
						if($row['dt'.($j<10?'0':'').$j] == 1){
							echo "<td style='text-align:center;'>".$row['dt'.($j<10?'0':'').$j]."</td>";
						}else {
							echo "<td></td>";
						}
					}

					echo "<td style='text-align:center;'>".$row['tot_dt']."</td>";
					echo "<td style='text-align:center;'>".number_format($totalTime / 60, 1)."</td>";
					echo "</tr>";

					$totalTime = 0;
				}

				$conn->row_free();



			?>
			</tbody>
		</table>
	</div>
		<script language="javascript">
			resize_table();
		</script>