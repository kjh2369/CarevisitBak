<?
	include("../inc/_header.php");
	include("../inc/_ed.php");
	include("../inc/_myFun.php");

	$mCode = $_GET["code"];
	$mKind = $_GET["kind"];
	$mYear = $_GET["year"];
	$mMonth = $_GET["month"];
	$mType = $_GET["type"];
	$mDate = $mYear.$mMonth;
	$mJumin = $ed->de($_GET["sugupja"]);

	$sugaShow = false;

	if ($mType == "s"){
		$sql = "select m03_name, m03_yoyangsa1_nm, m03_injung_no
				  from m03sugupja
				 where m03_ccode = '$mCode'
				   and m03_mkind = '$mKind'
				   and m03_jumin = '$mJumin'";
		$sugupja = $conn->get_array($sql);
	}else{
		$sql = "select m02_yname, m02_ycode
				  from m02yoyangsa
				 where m02_ccode = '$mCode'
				   and m02_mkind = '$mKind'
				   and m02_yjumin = '$mJumin'";
		$yoyangsa = $conn->get_array($sql);
	}
?>
<style>
body{
	margin-top:10px;
	margin-left:0px;
	overflow-x:hidden;
}

.week{
	color:#000000;
	font-weight:bold;
	background:#f1f1f1;
	height:24px;
}
.sun{
	color:#ff0000;
	font-weight:bold;
	background:#f1f1f1;
	height:24px;
}
.sat{
	color:#0000ff;
	font-weight:bold;
	background:#f1f1f1;
	height:24px;
}
.cell{
	color:#000000;
	font-weight:normal;
	background:#ffffff;
	height:25px;
}
</style>
<script src="../js/work.js" type="text/javascript"></script>
<form name="f" method="post">
<input name="mCode" type="hidden" value="<?=$mCode;?>">
<input name="mKind" type="hidden" value="<?=$mKind;?>">
<input name="mYear" type="hidden" value="<?=$mYear;?>">
<input name="mMonth" type="hidden" value="<?=$mMonth;?>">
<input name="mJumin" type="hidden" value="<?=$ed->en($mJumin);?>">
</form>
<div>
	<span style="font-size:14pt; font-weight:bold;">【<?=$mYear;?>년 『<?=intVal($mMonth);?>월』 서비스일정표<?=$mType == "s" ? "(수급자기준)" : "(요양보호사기준)"?>】</span>
</div>
<div style="padding-top:10px; text-align:left; margin-left:10px; margin-right:10px;">
<table style="width:100%;">
<tr>
	<td style="width:30%; height:20px; text-align:left; padding-left:10px;"><?=$mType == "s" ? "장기요양 인정관리번호" : "요양보호사 번호"?></td>
	<td style="width:15%; height:20px; text-align:left; padding-left:10px;"><?=$mType == "s" ? $sugupja[3] : $yoyangsa[1];?></td>
	<td style="width:7%; height:20px; text-align:left; padding-left:10px;">성명</td>
	<td style="width:10%; height:20px; text-align:left; padding-left:10px;"><?=$mType == "s" ? $sugupja[0] : $yoyangsa[0];?></td>
	<td style="width:*; height:20px; border:0; text-align:right;">
		<span id="btnPrint" class="btn_pack m icon"><span class="print"></span><button type="button" onFocus="this.blur();" onClick="_printServiceCalendar();">인쇄</button></span>
	</td>
</tr>
</table>
</div>
<div style="margin:10px; margin-top:5px;">
<table style="width:100%;" bgcolor="#cccccc" cellpadding="0" cellspacing="0">
<colGroup>
	<col width="15%">
	<col width="14%">
	<col width="14%">
	<col width="14%">
	<col width="14%">
	<col width="14%">
	<col width="15%">
</colGroup>
<tr>
	<td style="height:20px; text-align:left; background:#ffffff; border:0; font-weight:bold;" colspan="7">※ 급여 제공 일정</td>
</tr>
<tr>
	<td class="sun" style="border-bottom:1px solid #ccc;">일</td>
	<td class="week" style="border-bottom:1px solid #ccc;">월</td>
	<td class="week" style="border-bottom:1px solid #ccc;">화</td>
	<td class="week" style="border-bottom:1px solid #ccc;">수</td>
	<td class="week" style="border-bottom:1px solid #ccc;">목</td>
	<td class="week" style="border-bottom:1px solid #ccc;">금</td>
	<td class="sat" style="border-bottom:1px solid #ccc;">토</td>
</tr>
<?
	$calTime = mkTime(0, 0, 1, $mMonth, 1, $mYear);
	$today = date("Ymd", mktime());
	$lastDay = date("t", $calTime);
	$startWeek = date("w", strtotime(date("Y-m", $calTime)."-01"));
	$totalWeek = ceil(($lastDay + $startWeek) / 7);
	$lastWeek = date('w', strtotime(date("Y-m", $calTime)."-".$lastDay));

	for($i=1; $i<=$lastDay; $i++){
		$data[$i] = "";
	}

	if ($mType == "s"){
		$sql = "select case t01_svc_subcode when '200' then '[요양]' when '500' then '[목욕]' when '800' then '[간호]' else '' end as serviceType
				,      t01_sugup_date as sugupDate
				,      t01_sugup_fmtime as fromTime
				,      t01_sugup_totime as toTime
				,      t01_sugup_soyotime as soyoTime
				,      t01_yname1 as yName1
				,      case when t01_yname2 != '' then 1 else 0 end + case when t01_yname3 != '' then 1 else 0 end + case when t01_yname4 != '' then 1 else 0 end + case when t01_yname5 != '' then 1 else 0 end as yoyCount
				  from t01iljung
				 where t01_ccode = '$mCode'
				   and t01_mkind = '$mKind'
				   and t01_jumin = '$mJumin'
				   and t01_sugup_date like '$mDate%'
				   and t01_del_yn = 'N'
				 order by sugupDate, fromTime, toTime";
		$conn->query($sql);
		$conn->fetch();
		$rowCount = $conn->row_count();

		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);
			$day = intVal(subStr($row["sugupDate"], 6, 2));
			$data[$day] .= "<div>".$row["serviceType"]." ".$row["yName1"].($row["yoyCount"] > 0 ? "외 ".$row["yoyCount"]."명" : "")."</div><div style='padding-left:10px;'>".subStr($row["fromTime"], 0, 2).":".subStr($row["fromTime"], 2, 2)."~".subStr($row["toTime"], 0, 2).":".subStr($row["toTime"], 2, 2)."</div>"; //"[".$row["soyoTime"]."분]"
		}
		$conn->row_free();
	}else{
		$sql = "select case t01_svc_subcode when '200' then '[요양]' when '500' then '[목욕]' when '800' then '[간호]' else '' end as serviceType
				,      t01_sugup_date as sugupDate
				,      t01_sugup_fmtime as fromTime
				,      t01_sugup_totime as toTime
				,      t01_sugup_soyotime as soyoTime
				,      m03_name as name
				,      case t01_svc_subcode when '500' then case t01_yoyangsa_id1 when '$mJumin' then '(정)' else '(부)' end else '' end as mainSub
				  from t01iljung
				 inner join m03sugupja
				    on m03_ccode = t01_ccode
				   and m03_mkind = t01_mkind
				   and m03_jumin = t01_jumin
				 where t01_ccode = '$mCode'
				   and t01_mkind = '$mKind'
				   and '$mJumin' in (t01_yoyangsa_id1, t01_yoyangsa_id2, t01_yoyangsa_id3, t01_yoyangsa_id4, t01_yoyangsa_id5)
				   and t01_sugup_date like '$mDate%'
				   and t01_del_yn = 'N'
				 order by sugupDate, fromTime, toTime";
		$conn->query($sql);
		$conn->fetch();
		$rowCount = $conn->row_count();

		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);
			$day = intVal(subStr($row["sugupDate"], 6, 2));
			$data[$day] .= "<div>".$row["serviceType"]." ".$row["name"]."</div><div style='padding-left:10px;'>".subStr($row["fromTime"], 0, 2).":".subStr($row["fromTime"], 2, 2)."~".subStr($row["toTime"], 0, 2).":".subStr($row["toTime"], 2, 2)."</div>"; //"[".$row["soyoTime"]."분]"
		}
		$conn->row_free();
	}

	$day = 1;
	for($i=1; $i<=$totalWeek; $i++){
		echo "<tr>";

		for ($j=0; $j<7; $j++){
			switch($j){
			case 0:
				$class = "sun";
				break;
			case 6:
				$class = "sat";
				break;
			default:
				$class = "week";
			}

			echo "<td class='cell' style='height:50px; padding:0; margin:0; text-align:left; vertical-align:top;'>";

			if (!(($i == 1 && $j < $startWeek) || ($i == $totalWeek && $j > $lastWeek))){
				if ($i == $totalWeek){
					$borderBottom = 'border-bottom:1px solid #ccc;';
				}else{
					$borderBottom = '';
				}
				echo "<div class='$class' style='position:absolute; width:20px; height:100%; text-align:center; border-right:1px solid #ccc; $borderBottom'><span>$day</span></div>";
				echo "<div id='day[]' style='padding-left:20px; line-height:1.3em; border-left:1px solid #ffffff;'>".($data[$day] != "" ? $data[$day] : "<br><br>")."</div>";
				$day++;
			}

			echo "</td>";
		}

		echo "</tr>";
	}
?>
</table>
</div>
<?
	if ($mType == "s"){
	?>
		<div style="margin:10px; margin-top:5px;">
		<table style="width:100%;" bgcolor="#cccccc" cellpadding="0" cellspacing="0">
		<colGroup>
			<col width="25%">
			<col width="25%">
			<col width="25%">
			<col width="25%">
		</colGroup>
		<tr>
			<td style="height:20px; text-align:left; background:#ffffff; border:0; font-weight:bold;" colspan="4">※ 급여 제공 기관</td>
		</tr>
		<tr>
			<td class="week" style="border-bottom:1px solid #ccc;">요양기관명</td>
			<td class="week" style="border-bottom:1px solid #ccc;">요양보호사명</td>
			<td class="week" style="border-bottom:1px solid #ccc;">전화번호</td>
			<td class="week" style="border-bottom:1px solid #ccc;">비고</td>
		</tr>
		<?
			$sql = "select distinct
						   m00_cname as cName
					,      t01_yname1 as yName1, y1.m02_ytel as yTel1
					,      t01_yname2 as yName2, y2.m02_ytel as yTel2
					,      t01_yname3 as yName3, y3.m02_ytel as yTel3
					,      t01_yname4 as yName4, y4.m02_ytel as yTel4
					,      t01_yname5 as yName5, y5.m02_ytel as yTel5
					  from t01iljung
					 inner join m00center
						on m00_mcode = t01_ccode
					   and m00_mkind = t01_mkind
					  left join m02yoyangsa as y1
						on y1.m02_ccode = t01_ccode
					   and y1.m02_mkind = t01_mkind
					   and y1.m02_yjumin = t01_yoyangsa_id1
					  left join m02yoyangsa as y2
						on y2.m02_ccode = t01_ccode
					   and y2.m02_mkind = t01_mkind
					   and y2.m02_yjumin = t01_yoyangsa_id2
					  left join m02yoyangsa as y3
						on y3.m02_ccode = t01_ccode
					   and y3.m02_mkind = t01_mkind
					   and y3.m02_yjumin = t01_yoyangsa_id3
					  left join m02yoyangsa as y4
						on y4.m02_ccode = t01_ccode
					   and y4.m02_mkind = t01_mkind
					   and y4.m02_yjumin = t01_yoyangsa_id4
					  left join m02yoyangsa as y5
						on y5.m02_ccode = t01_ccode
					   and y5.m02_mkind = t01_mkind
					   and y5.m02_yjumin = t01_yoyangsa_id5
					 where t01_ccode = '$mCode'
					   and t01_mkind = '$mKind'
					   and t01_jumin = '$mJumin'
					   and t01_sugup_date like '$mDate%'
					   and t01_del_yn = 'N'";
			$conn->query($sql);
			$conn->fetch();
			$rowCount = $conn->row_count();
			$index = 0;

			for($i=0; $i<$rowCount; $i++){
				$row = $conn->select_row($i);

				if ($cName == "") $cName = $row["cName"];

				for($j=1; $j<=5; $j++){
					if ($row["yName".$j] != ""){
						if ($index > 0){
							$result = true;
							for($k=0; $k<$index; $k++){
								if ($yName[$k] == $row["yName".$j]){
									$result = false;
									break;
								}
							}
							if ($result == true){
								$yName[$index] = $row["yName".$j];
								$yTel[$index] = $myF->phoneStyle($row["yTel".$j]);
								$index ++;
							}
						}else{
							$yName[$index] = $row["yName".$j];
							$yTel[$index] = $myF->phoneStyle($row["yTel".$j]);
							$index ++;
						}
					}
				}
			}
			$conn->row_free();

			for($i=0; $i<sizeOf($yName); $i++){
				echo "
					<tr>
						<td class='cell' style='border-bottom:1px solid #ccc;'>".$cName."</td>
						<td class='cell' style='border-bottom:1px solid #ccc;'>".$yName[$i]."</td>
						<td class='cell' style='border-bottom:1px solid #ccc;'>".$yTel[$i]."</td>
						<td class='cell' style='border-bottom:1px solid #ccc;'></td>
					</tr>
					 ";
			}
		?>
		</table>
		</div>
		<div style="margin:10px; margin-top:5px;">
		<table style="width:100%;" bgcolor="#cccccc" cellpadding="0" cellspacing="0">
		<colGroup>
			<col width="17%">
			<col width="16%">
			<col width="17%">
			<col width="17%">
			<col width="16%">
			<col width="17%">
		</colGroup>
		<tr>
			<td style="height:20px; text-align:left; background:#ffffff; border:0; font-weight:bold;" colspan="6">※ 급여 내역 현황(월)</td>
		</tr>
		<tr>
			<td class="week" style="border-bottom:1px solid #ccc;">급여종류</td>
			<td class="week" style="border-bottom:1px solid #ccc;">서비스시간</td>
			<td class="week" style="border-bottom:1px solid #ccc;">서비스횟수</td>
			<?
				if ($sugaShow == true){
					echo '
						<td class="week" style="border-bottom:1px solid #ccc;">수가</td>
						<td class="week" style="border-bottom:1px solid #ccc;">총급여비용</td>
						<td class="week" style="border-bottom:1px solid #ccc;">본인부담액</td>
						 ';
				}
			?>
		</tr>
		<?
			$sql = "select case t01_svc_subcode when '200' then '방문요양' when '500' then '방문목욕' when '800' then '방문간호' else '' end as serviceType
					,      t01_sugup_fmtime as fromTime
					,      t01_sugup_totime as toTime
					,      t01_sugup_soyotime as soyoTime
					,      t01_suga_code1 as sugaCode
					,      t01_suga as suga
					,      t01_suga_over as sugaOver
					,      t01_suga_night as sugaNight
					,      t01_suga_tot as sugaTotal
					,      sugupja.mBoninYul as boninYul
					  from t01iljung
					 inner join (".$conn->joinBoninYulQuerty($mCode, $mKind, $mJumin).") as sugupja
						on t01_ccode = sugupja.mCode
					   and t01_mkind = sugupja.mKind
					   and t01_jumin = sugupja.mJumin
					   and t01_sugup_date between sugupja.mSdate and sugupja.mEdate
					 where t01_ccode = '$mCode'
					   and t01_mkind = '$mKind'
					   and t01_jumin = '$mJumin'
					   and t01_sugup_date like '$mDate%'
					   and t01_del_yn = 'N'
					 order by t01_svc_subcode, t01_sugup_fmtime, t01_sugup_totime";
			$conn->query($sql);
			$conn->fetch();
			$rowCount = $conn->row_count();
			$data = array();
			$index = 0;

			for($i=0; $i<$rowCount; $i++){
				$row = $conn->select_row($i);

				if ($tempService != $row["serviceType"] || $tempTime != $row["fromTime"].$row["toTime"]){
					$tempService = $row["serviceType"];
					$tempTime = $row["fromTime"].$row["toTime"];
					$index ++;

					$data[$index]["service"] = $row["serviceType"];
					$data[$index]["fromTime"] = subStr($row["fromTime"], 0, 2).":".subStr($row["fromTime"], 2, 2);
					$data[$index]["toTime"] = subStr($row["toTime"], 0, 2).":".subStr($row["toTime"], 2, 2);
					$data[$index]["suga"] = $row["suga"];
					$data[$index]["count"] = 0;
					$data[$index]["total"] = 0;
					$data[$index]["bonin"] = 0;
				}
				$data[$index]["count"] ++;
				$data[$index]["total"] += $row["sugaTotal"];
				$data[$index]["bonin"] += $row["sugaTotal"] * $row["boninYul"] / 100;
			}
			$conn->row_free();

			$dataCount = sizeOf($data);
			for($i=1; $i<=$dataCount; $i++){
				echo "
					<tr>
						<td class='cell'>".$data[$i]["service"]."</td>
						<td class='cell'>".$data[$i]["fromTime"]."~".$data[$i]["toTime"]."</td>
						<td class='cell'>".$data[$i]["count"]."</td>
					 ";
				if ($sugaShow == true){
					echo "
							<td class='cell' style='text-align:right; padding-right:10px;'>".number_format($data[$i]["suga"])."</td>
							<td class='cell' style='text-align:right; padding-right:10px;'>".number_format($data[$i]["total"])."</td>
							<td class='cell' style='text-align:right; padding-right:10px;'>".number_format($data[$i]["bonin"])."</td>
						</tr>
						 ";
				}
			}
		?>
		</table>
		</div>
		<div style="margin:10px; margin-top:5px;">
		<table style="width:100%;" bgcolor="#cccccc" cellpadding="0" cellspacing="0">
		<colGroup>
			<col width="33%">
			<col width="34%">
			<col width="33%">
		</colGroup>
		<tr>
			<td style="height:20px; text-align:left; background:#ffffff; border:0; font-weight:bold;" colspan="6">※ 본인부담금입금계좌</td>
		</tr>
		<tr>
			<td class="week" style="border-bottom:1px solid #ccc;">은행명</td>
			<td class="week" style="border-bottom:1px solid #ccc;">계좌번호</td>
			<td class="week" style="border-bottom:1px solid #ccc;">예금주</td>
		</tr>
		<?
			$sql = "select m00_cname, m00_ctel, m00_bank_no, m00_bank_name, m00_bank_depos
					  from m00center
					 where m00_mcode = '$mCode'
					   and m00_mkind = '$mKind'";
			$bank = $conn->get_array($sql);
		?>
		<tr>
			<td class="cell" style=""><?=$definition->GetBankName($bank["m00_bank_name"]);?></td>
			<td class="cell" style=""><?=$bank["m00_bank_no"];?></td>
			<td class="cell" style=""><?=$bank["m00_bank_depos"];?></td>
		</tr>
		</table>
		</div>
		<div style="padding-top:20px; padding-bottom:20px;">
			<span style="font-size:14pt; font-weight:bold;"><?=$bank["m00_cname"]." (☎ ".$myF->phoneStyle($bank["m00_ctel"]).")";?></span>
		</div>
	<?
	}else{
	?>
		<div style="margin:10px; margin-top:5px;">
		<table style="width:100%;" bgcolor="#cccccc" cellpadding="0" cellspacing="0">
		<colGroup>
			<col width="25%">
			<col width="50%">
			<col width="25%">
		</colGroup>
		<tr>
			<td style="height:20px; text-align:left; background:#ffffff; border:0; font-weight:bold;" colspan="4">※ 수급자</td>
		</tr>
		<tr>
			<td class="week" style="border-bottom:1px solid #ccc;">수급자명</td>
			<td class="week" style="border-bottom:1px solid #ccc;">연락처</td>
			<td class="week" style="border-bottom:1px solid #ccc;">비고</td>
		</tr>
		<?
			$sql = "select distinct m03_name as name
					,      m03_tel as tel
					,      m03_hp as hp
					  from t01iljung
					 inner join m03sugupja
						on m03_ccode = t01_ccode
					   and m03_mkind = t01_mkind
					   and m03_jumin = t01_jumin
					 where t01_ccode = '$mCode'
					   and t01_mkind = '$mKind'
					   and '$mJumin' in (t01_yoyangsa_id1, t01_yoyangsa_id2, t01_yoyangsa_id3, t01_yoyangsa_id4, t01_yoyangsa_id5)
					   and t01_sugup_date like '$mDate%'
					   and t01_del_yn = 'N'";
			$conn->query($sql);
			$conn->fetch();
			$rowCount = $conn->row_count();

			for($i=0; $i<$rowCount; $i++){
				$row = $conn->select_row($i);

				echo "
					<tr>
						<td class='cell' style='border-bottom:1px solid #ccc;'>".$row["name"]."</td>
						<td class='cell' style='border-bottom:1px solid #ccc; text-align:left; padding-left:10px;'>".$myF->phoneStyle($row["tel"]).($row["hp"] != "" ? "(".$myF->phoneStyle($row["hp"]).")": "")."</td>
						<td class='cell' style='border-bottom:1px solid #ccc;'></td>
					</tr>
					 ";
			}
			$conn->row_free();
		?>
		</table>
		</div>
		<div style="margin:10px; margin-top:5px; padding-bottom:10px;">
		<table style="width:100%;" bgcolor="#cccccc" cellpadding="0" cellspacing="0">
		<colGroup>
			<col width="17%">
			<col width="16%">
			<col width="17%">
			<col width="17%">
			<col width="16%">
			<col width="17%">
		</colGroup>
		<tr>
			<td style="height:20px; text-align:left; background:#ffffff; border:0; font-weight:bold;" colspan="6">※ 급여 내역 현황(월)</td>
		</tr>
		<tr>
			<td class="week" style="border-bottom:1px solid #ccc;">급여종류</td>
			<td class="week" style="border-bottom:1px solid #ccc;">서비스시간</td>
			<td class="week" style="border-bottom:1px solid #ccc;">서비스횟수</td>
			<td class="week" style="border-bottom:1px solid #ccc;">서비스시간</td>
			<?
				if ($sugaShow == true){
					echo '
						<td class="week" style="border-bottom:1px solid #ccc;">수가</td>
						<td class="week" style="border-bottom:1px solid #ccc;">총급여비용</td>
						 ';
				}
			?>
		</tr>
		<?
			$sql = "select case t01_svc_subcode when '200' then '방문요양' when '500' then '방문목욕' when '800' then '방문간호' else '' end as serviceType
					,      t01_sugup_fmtime as fromTime
					,      t01_sugup_totime as toTime
					,      t01_sugup_soyotime as soyoTime
					,      t01_suga_code1 as sugaCode
					,      t01_suga as suga
					,      t01_suga_over as sugaOver
					,      t01_suga_night as sugaNight
					,      t01_suga_tot as sugaTotal
					  from t01iljung
					 where t01_ccode = '$mCode'
					   and t01_mkind = '$mKind'
					   and '$mJumin' in (t01_yoyangsa_id1, t01_yoyangsa_id2, t01_yoyangsa_id3, t01_yoyangsa_id4, t01_yoyangsa_id5)
					   and t01_sugup_date like '$mDate%'
					   and t01_del_yn = 'N'
					 order by t01_svc_subcode, t01_sugup_fmtime, t01_sugup_totime";
			$conn->query($sql);
			$conn->fetch();
			$rowCount = $conn->row_count();
			$data = array();
			$index = 0;

			for($i=0; $i<$rowCount; $i++){
				$row = $conn->select_row($i);

				if ($tempService != $row["serviceType"] || $tempTime != $row["fromTime"].$row["toTime"]){
					$tempService = $row["serviceType"];
					$tempTime = $row["fromTime"].$row["toTime"];
					$index ++;

					$data[$index]["service"] = $row["serviceType"];
					$data[$index]["fromTime"] = subStr($row["fromTime"], 0, 2).":".subStr($row["fromTime"], 2, 2);
					$data[$index]["toTime"] = subStr($row["toTime"], 0, 2).":".subStr($row["toTime"], 2, 2);
					$data[$index]["suga"] = $row["suga"];
					$data[$index]["count"] = 0;
					$data[$index]["soyo"] = 0;
					$data[$index]["total"] = 0;
				}
				$data[$index]["count"] ++;
				$data[$index]["soyo"] += $row["soyoTime"];
				$data[$index]["total"] += $row["sugaTotal"];
			}
			$conn->row_free();

			$dataCount = sizeOf($data);
			for($i=1; $i<=$dataCount; $i++){
				echo "
					<tr>
						<td class='cell'>".$data[$i]["service"]."</td>
						<td class='cell'>".$data[$i]["fromTime"]."~".$data[$i]["toTime"]."</td>
						<td class='cell'>".$data[$i]["count"]."</td>
						<td class='cell' style='text-align:left; padding-left:10px;'>".$myF->getMinToHM($data[$i]["soyo"])."</td>
					 ";
				if ($sugaShow == true){
					echo "
							<td class='cell' style='text-align:right; padding-right:10px;'>".number_format($data[$i]["suga"])."</td>
							<td class='cell' style='text-align:right; padding-right:10px;'>".number_format($data[$i]["total"])."</td>
						</tr>
						 ";
				}
			}
		?>
		</table>
		</div>
	<?
	}
	include("../inc/_footer.php");
?>
<script language="javascript">
	self.focus();
</script>