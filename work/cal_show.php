<?
	include_once("../inc/_header.php");
	//include_once("../inc/_http_uri.php");
	include_once("../inc/_login.php");
	include_once("../inc/_page_list.php");
	//include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");

	$code		= $_GET['code'];
	$kind		= $_GET['kind'];
	$year		= $_GET['year'];
	$month		= $_GET['month'];
	$type		= $_GET['type'];
	$useType	= $_GET['useType'];
	$detail		= $_GET['detail'];
	$family		= $_GET['family'];

	if ($family == 'Y'){
		$family_sql = " and t01_toge_umu = 'Y'
						and t01_svc_subcode = '200'";
	}else{
		$family_sql = "";
	}

	// 휴일리스트
	$sql = "select *
			  from tbl_holiday
			 where mdate like '$year%'";
	$conn->query($sql);
	$conn->fetch();
	$holiday_count = $conn->row_count();

	for($i=0; $i<$holiday_count; $i++){
		$row = $conn->select_row($i);
		$holiday[$row['mdate']] = $row['holiday_name'];
	}

	$conn->row_free();

	if ($_GET['target'] == 'all'){
		if ($type == 's'){
			$sql = "select distinct m03_jumin
					  from m03sugupja
					 inner join t01iljung
						on t01_ccode = m03_ccode
					   and t01_mkind = m03_mkind
					   and t01_jumin = m03_jumin
					   and t01_sugup_date like '$year$month%' $family_sql
					   and t01_del_yn = 'N'
					 where m03_ccode = '$code'
					   and m03_mkind = '$kind'
					 order by m03_name";
		}else{
			$sql = "select distinct m02_yjumin
					  from (
						   select m02_yjumin
						   ,      m02_yname
						     from m02yoyangsa
						    inner join t01iljung
							   on m02_ccode = t01_ccode
						      and m02_mkind = t01_mkind
						      and t01_yoyangsa_id1 = m02_yjumin
						      and t01_sugup_date like '$year$month%' $family_sql
						      and t01_del_yn = 'N'
						    where m02_ccode = '$code'
						      and m02_mkind = '$kind'
						    union all
						   select m02_yjumin
						   ,      m02_yname
						     from m02yoyangsa
						    inner join t01iljung
							   on m02_ccode = t01_ccode
						      and m02_mkind = t01_mkind
						      and t01_yoyangsa_id2 = m02_yjumin
						      and t01_sugup_date like '$year$month%' $family_sql
						      and t01_del_yn = 'N'
						    where m02_ccode = '$code'
						      and m02_mkind = '$kind'
					       ) as t
					 order by m02_yname";
		}
		$conn->query($sql);
		$conn->fetch();
		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row      = $conn->select_row($i);
			$list[$i] = $row[0];
		}

		$conn->row_free();
	}else if (!is_numeric($_GET['sugupja'])){
		$target = $ed->de($_GET['sugupja']); //iconv("EUC-KR","UTF-8",$_GET['target'])
		$list[0] = $target;

	}else{
		if ($type == 's'){
			$sql = "select m03_jumin
					  from m03sugupja
					 where m03_ccode = '".$code."'
					   and m03_mkind = '".$kind."'
					   and m03_key   = '".$_GET['sugupja']."'";
		}else{
			$sql = "select m02_yjumin
					  from m02yoyangsa
					 where m02_ccode = '".$code."'
					   and m02_mkind = '".$kind."'
					   and m02_key   = '".$_GET['sugupja']."'";
		}
		$target = $conn->get_data($sql);
		$list[0] = $target;
	}

	// 센터정보
	$sql = "select m00_cname, m00_ctel
			  from m00center
			 where m00_mcode = '$code'
			   and m00_mkind = '$kind'";
	$row = $conn->get_array($sql);

	$centerName = $row[0];
	$centerTel	= $myF->phoneStyle($row[1]);

	for($l=0; $l<sizeOf($list); $l++){
		$target = $list[$l];
		if ($type == 's'){
			$sql = "select m03_name
					,      m03_injung_no
					,      LVL.m81_name
					,      STP.m81_name
					,      m03_bonin_yul
					  from m03sugupja
					 inner join m81gubun as LVL
						on LVL.m81_gbn  = 'LVL'
					   and LVL.m81_code = m03_ylvl
					 inner join m81gubun as STP
						on STP.m81_gbn  = 'STP'
					   and STP.m81_code = m03_skind
					 where m03_ccode = '$code'
					   and m03_mkind = '$kind'
					   and m03_jumin = '$target'";
			$row = $conn->get_array($sql);

			$name	= $row[0];
			$jumin	= $myF->issStyle($target);
			$no	= $row[1];
			$level	= $row[2];
			$rate	= $row[3].' / '.$row[4];

		}else{
			$sql = "select m02_yname, m02_ycode, m02_ytel
					  from m02yoyangsa
					 where m02_ccode  = '$code'
					   and m02_mkind  = '$kind'
					   and m02_yjumin = '$target'";
			$row = $conn->get_array($sql);

			$name	= $row[0];
			$jumin	= $row[1];
			$no	= $myF->phoneStyle($row[2]);

		}
	}
?>
<style>
table(
	width:100%;
}
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
<script language="javascript">
window.onload = function(){
	__init_form(document.f);
}
</script>
<script src="../js/work.js" type="text/javascript"></script>
<form name="f" method="post">
<div style="padding-top:10px; text-align:left; margin-left:10px; margin-right:10px;">
<div align="center" style="font size:15px; font-weight:bold;"><?=$year;?>년 <?=intval($month);?>월 서비스 일정표<?=$type == 's' ? '(수급자기준)' : '(요양사보호사기준)'?></div><br>
<div style="position:absolute; top:15px; left:750px;"><span id="btnPrint" class="btn_pack m icon"><span class="print"></span><button type="button" onFocus="this.blur();" onClick="serviceCalendarShow('<?=$code;?>', '<?=$kind;?>', '<?=$year;?>', '<?=$month;?>', '<?=$ed->en($target);?>', '<?=$type;?>', '<?=$useType?>', 'pdf', 'y');">인쇄</button></span></div>
<table cellpadding='1' cellspacing='1' bgcolor='cccccc' >
	<table style="width:100%">
	<?
	if($type == 's'){
		?>
		<colgroup>
			<col width="15%">
			<col width="25%">
			<col width="25%">
			<col width="10%">
		<col>
		</colgroup>
			<tr>
				<td class="week">수급자명</td>
				<td class="week">주민등록번호</td>
				<td class="week">장기요양인증번호</td>
				<td class="week">등급</td>
				<td class="week">본인부담율</td>
			</tr>
			<tr>
				<td class="cell"><?=$name?></td>
				<td class="cell"><?=$jumin?></td>
				<td class="cell"><?=$no?></td>
				<td class="cell"><?=$level?></td>
				<td class="cell"><?=$rate?></td>
			</tr>
			<?
	}else {
	?>
		<colgroup>
			<col width="15%">
			<col width="25%">
			<col width="25%">
		<col>
		</colgroup>
			<tr>
				<td class="week">요양보호사명</td>
				<td class="week">요양보호사번호</td>
				<td class="week">연락처</td>
				<td class="week">비고</td>
			</tr>
			<tr>
				<td class="cell"><?=$name?></td>
				<td class="cell"><?=$jumin?></td>
				<td class="cell"><?=$no?></td>
				<td></td>
			</tr>
		<?
	}
	?>
	</table>
	<br>
		<div align="left" style="font-weight:bold;">※급여제공 일정</div>
	<br>
	<table style="width:100%">
		<colgroup>
			<col width="15%">
			<col width="14%">
			<col width="14%">
			<col width="14%">
			<col width="14%">
			<col width="14%">
			<col width="15%">
		</colgroup>
			<tr>
				<td class="sun">일</td>
				<td class="week">월</td>
				<td class="week">화</td>
				<td class="week">수</td>
				<td class="week">목</td>
				<td class="week">금</td>
				<td class="sat">토</td>
			</tr>
			<?
				// 일정 변수 설정
				$calTime   = mkTime(0, 0, 1, $month, 1, $year);
				$today     = date('Ymd', mktime());
				$lastDay   = date('t', $calTime); //총일수 구하기
				$startWeek = date('w', strtotime(date('Y-m', $calTime).'-01')); //시작요일 구하기
				$totalWeek = ceil(($lastDay + $startWeek) / 7); //총 몇 주인지 구하기
				$lastWeek  = date('w', strtotime(date('Y-m', $calTime).'-'.$lastDay)); //마지막 요일 구하기

				$date = $year.$month;

				for($i=1; $i<=$lastDay; $i++){
					$data[$i] = "";
				}

				// 일별 데이타
				if ($type == 's'){
					$sql = "select cast(date_format(t01_sugup_date, '%d') as signed)
							,      date_format(concat(t01_sugup_date, t01_sugup_fmtime,'00'), '%H:%i')
							,      date_format(concat(t01_sugup_date, t01_sugup_totime,'00'), '%H:%i')
							,      t01_sugup_soyotime
							,      case t01_svc_subcode when '200' then '방문요양' when '500' then '방문목욕' when '800' then '방문간호' else '-' end
							,      t01_yname1
							,      concat(case when t01_yname2 != '' then '/' else '' end, left(t01_yname2, 3))
							,	   t01_toge_umu
							  from t01iljung
							 where t01_ccode  = '$code'
							   and t01_mkind  = '$kind'
							   and t01_jumin  = '$target'
							   and t01_sugup_date like '$date%' $family_sql
							   and t01_del_yn = 'N'
							 order by t01_sugup_date, t01_sugup_fmtime, t01_sugup_totime";
				}else{
					$sql = "select cast(date_format(t01_sugup_date, '%d') as signed) as t01_sugup_date
							,      date_format(concat(t01_sugup_date, t01_sugup_fmtime,'00'), '%H:%i') as t01_sugup_fmtime
							,      date_format(concat(t01_sugup_date, t01_sugup_totime,'00'), '%H:%i') as t01_sugup_totime
							,      t01_sugup_soyotime
							,      case t01_svc_subcode when '200' then '요양' when '500' then '목욕' when '800' then '간호' else '-' end
							,      m03_name
							,      case t01_svc_subcode when '500' then case t01_yoyangsa_id1 when '$target' then '(정)' else '(부)' end else '' end
							,	   t01_toge_umu
							  from t01iljung
							 inner join m03sugupja
								on m03_ccode = t01_ccode
							   and m03_mkind = t01_mkind
							   and m03_jumin = t01_jumin
							 where t01_ccode = '$code'
							   and t01_mkind = '$kind'
							   and t01_yoyangsa_id1 = '$target'
							   and t01_sugup_date like '$date%' $family_sql
							   and t01_del_yn = 'N'
							 union all
							select cast(date_format(t01_sugup_date, '%d') as signed) as t01_sugup_date
							,      date_format(concat(t01_sugup_date, t01_sugup_fmtime,'00'), '%H:%i') as t01_sugup_fmtime
							,      date_format(concat(t01_sugup_date, t01_sugup_totime,'00'), '%H:%i') as t01_sugup_totime
							,      t01_sugup_soyotime
							,      case t01_svc_subcode when '200' then '요양' when '500' then '목욕' when '800' then '간호' else '-' end
							,      m03_name
							,      case t01_svc_subcode when '500' then case t01_yoyangsa_id1 when '$target' then '(정)' else '(부)' end else '' end
							,	   t01_toge_umu
							  from t01iljung
							 inner join m03sugupja
								on m03_ccode = t01_ccode
							   and m03_mkind = t01_mkind
							   and m03_jumin = t01_jumin
							 where t01_ccode = '$code'
							   and t01_mkind = '$kind'
							   and t01_yoyangsa_id2 = '$target'
							   and t01_sugup_date like '$date%' $family_sql
							   and t01_del_yn = 'N'
							 order by t01_sugup_date, t01_sugup_fmtime, t01_sugup_totime";
				}

				$conn->query($sql);
				$conn->fetch();
				$rowCount = $conn->row_count();


				$day = 0;

				for($i=0; $i<$rowCount; $i++){
					$row = $conn->select_row($i);

					if ($day != $row[0]){
						$day = $row[0];
						$seq = 0;
					}

					$cal[$day][$seq]['service']	= $row[4];
					$cal[$day][$seq]['cost']	= $row[3];
					$cal[$day][$seq]['toge']	= $row[7];

				if($cal[$day][$seq]['toge'] == 'Y'){
					if ($type == 's'){
						$cal[$day][$seq]['time']	= $row[1].'~'.$row[2];
						$cal[$day][$seq]['worker']	= $row[5].$row[6];
						$data[$day] .= "<div>[동거 ".$cal[$day][$seq]['service']."]</div><div>".$cal[$day][$seq]['time']."</div><div>".$cal[$day][$seq]['worker']."</div>"; //"[".$row["soyoTime"]."분]"
					}else{
						$cal[$day][$seq]['time']	= $row[1].'~'.$row[2].$row[6];
						$cal[$day][$seq]['worker']	= $row[5];
						$data[$day] .= "<div>[동거 ".$cal[$day][$seq]['service']."]".$cal[$day][$seq]['worker']."</div><div>".$cal[$day][$seq]['time']."</div>"; //"[".$row["soyoTime"]."분]"
					}
				}else {
					if ($type == 's'){
						$cal[$day][$seq]['time']	= $row[1].'~'.$row[2];
						$cal[$day][$seq]['worker']	= $row[5].$row[6];
						$data[$day] .= "<div>[".$cal[$day][$seq]['service']."]</div><div>".$cal[$day][$seq]['time']."</div><div>".$cal[$day][$seq]['worker']."</div>"; //"[".$row["soyoTime"]."분]"
					}else{
						$cal[$day][$seq]['time']	= $row[1].'~'.$row[2].$row[6];
						$cal[$day][$seq]['worker']	= $row[5];
						$data[$day] .= "<div>[".$cal[$day][$seq]['service']."]".$cal[$day][$seq]['worker']."</div><div>".$cal[$day][$seq]['time']."</div>"; //"[".$row["soyoTime"]."분]"
					}
				}
					$seq ++;
				}

				$conn->row_free();

				$day = 1; //화면에 표시할 화면의 초기값을 1로 설정
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
							if ($holiday[$year.$month.($day<10?'0':'').$day] != ''){
								$str_day = $day.' ['.$holiday[$year.$month.($day<10?'0':'').$day].']';
								$color_yn = 'Y';
							}else{
								$str_day = $day;
								$color_yn = 'N';
							}
							if($color_yn == 'Y'){
								$color = 'color:#ff0000;';
							}else {
								$color = '';
							}
							echo "<div class='$class' style='width:100%; text-align:left; padding-left:5px; $color'><span>$str_day</span></div>";
							echo "<div id='day[]' style='padding:5px; line-height:1.3em;'>".($data[$day] != "" ? $data[$day] : "<br><br>")."</div>";
							$day++;
						}

						echo "</td>";
					}

					echo "</tr>";
				}

			?>
	</table>
	<br>
		<div align="left" style="font-weight:bold;">※급여제공 기관</div>
	<br>
	<?
		if ($type == 's'){
			// 요양보호사
			$sql = "select distinct m02_yname, m02_ytel
					  from (
						   select t01_yoyangsa_id1 as yoy
							 from t01iljung
							where t01_ccode  = '$code'
							  and t01_mkind  = '$kind'
							  and t01_jumin  = '$target'
							  and t01_sugup_date like '$date%' $family_sql
							  and t01_del_yn = 'N'
							union all
						   select t01_yoyangsa_id2 as yoy
							 from t01iljung
							where t01_ccode  = '$code'
							  and t01_mkind  = '$kind'
							  and t01_jumin  = '$target'
							  and t01_sugup_date like '$date%' $family_sql
							  and t01_del_yn = 'N'
						   ) as y
					 inner join m02yoyangsa
						on m02_ccode = '$code'
					   and m02_mkind = '$kind'
					   and m02_yjumin = yoy
					 order by m02_yname";
			$conn->query($sql);
			$conn->fetch();
			$rowCount = $conn->row_count();

			for($i=0; $i<$rowCount; $i++){
				$row = $conn->select_row($i);
				$yoyInfo .= ($yoyInfo != '' ? '  /  ' : '').$row[0].'('.$myF->phoneStyle($row[1],'.').')';
			}
		}

	if($type == 's'){?>
	<table style="width:100%">
		<tr>
			<td style="text-align:left; padding-left:5px;">담당 요양보호사 : <?=$row[0].'('.$yoyInfo;?></td>
		</tr>
	</table><br><?
	}

	// 제공서비스내역
		if ($type == 's'){
			$sql = "select cast(date_format(t01_sugup_date, '%d') as signed)
					,      date_format(concat(t01_sugup_date, t01_sugup_fmtime, '00'), '%H:%i')
					,      date_format(concat(t01_sugup_date, t01_sugup_totime, '00'), '%H:%i')
					,      t01_yname1
					,      t01_yname2
					,      m01_suga_cont
					,      t01_svc_subcode
					  from t01iljung
					 inner join (
						   select m01_mcode2, m01_suga_cont, m01_sdate, m01_edate
							 from m01suga
							where m01_mcode = '$code'
							union all
						   select m11_mcode2, m11_suga_cont, m11_sdate, m11_edate
							 from m11suga
							where m11_mcode = '$code'
						   ) as suga
						on t01_suga_code1 = m01_mcode2
					   and t01_sugup_date between m01_sdate and m01_edate
					 where t01_ccode  = '$code'
					   and t01_mkind  = '$kind'
					   and t01_jumin  = '$target'
					   and t01_sugup_date like '$date%' $family_sql
					   and t01_del_yn = 'N'
					 order by t01_yname1, t01_yname2, t01_svc_subcode, t01_sugup_fmtime, t01_sugup_totime, t01_sugup_date";
		}else{
			$sql = "select cast(date_format(t01_sugup_date, '%d') as signed) as t01_sugup_date
					,      date_format(concat(t01_sugup_date, t01_sugup_fmtime, '00'), '%H:%i') as t01_sugup_fmtime
					,      date_format(concat(t01_sugup_date, t01_sugup_totime, '00'), '%H:%i') as t01_sugup_totime
					,      m03_name
					,      case t01_svc_subcode when '500' then case t01_yoyangsa_id1 when '$target' then '(정)' else '(부)' end else '' end
					,      m01_suga_cont
					,      t01_svc_subcode
					  from t01iljung
					 inner join m03sugupja
						on m03_ccode = t01_ccode
					   and m03_mkind = t01_mkind
					   and m03_jumin = t01_jumin
					 inner join (
						   select m01_mcode2, m01_suga_cont, m01_sdate, m01_edate
							 from m01suga
							where m01_mcode = '$code'
							union all
						   select m11_mcode2, m11_suga_cont, m11_sdate, m11_edate
							 from m11suga
							where m11_mcode = '$code'
						   ) as suga
						on t01_suga_code1 = m01_mcode2
					   and t01_sugup_date between m01_sdate and m01_edate
					 where t01_ccode  = '$code'
					   and t01_mkind  = '$kind'
					   and t01_yoyangsa_id1 = '$target'
					   and t01_sugup_date like '$date%' $family_sql
					   and t01_del_yn = 'N'
					 union all
					select cast(date_format(t01_sugup_date, '%d') as signed) as t01_sugup_date
					,      date_format(concat(t01_sugup_date, t01_sugup_fmtime, '00'), '%H:%i') as t01_sugup_fmtime
					,      date_format(concat(t01_sugup_date, t01_sugup_totime, '00'), '%H:%i') as t01_sugup_totime
					,      m03_name
					,      case t01_svc_subcode when '500' then case t01_yoyangsa_id1 when '$target' then '(정)' else '(부)' end else '' end
					,      m01_suga_cont
					,      t01_svc_subcode
					  from t01iljung
					 inner join m03sugupja
						on m03_ccode = t01_ccode
					   and m03_mkind = t01_mkind
					   and m03_jumin = t01_jumin
					 inner join (
						   select m01_mcode2, m01_suga_cont, m01_sdate, m01_edate
							 from m01suga
							where m01_mcode = '$code'
							union all
						   select m11_mcode2, m11_suga_cont, m11_sdate, m11_edate
							 from m11suga
							where m11_mcode = '$code'
						   ) as suga
						on t01_suga_code1 = m01_mcode2
					   and t01_sugup_date between m01_sdate and m01_edate
					 where t01_ccode  = '$code'
					   and t01_mkind  = '$kind'
					   and t01_yoyangsa_id2 = '$target'
					   and t01_sugup_date like '$date%' $family_sql
					   and t01_del_yn = 'N'
					 order by m03_name, t01_svc_subcode, t01_sugup_fmtime, t01_sugup_totime, t01_sugup_date";
		}

		$conn->query($sql);
		$conn->fetch();
		$rowCount = $conn->row_count();


		$tempData = '';
		$seq = 0;

		unset($svc);

		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			if ($type == 's'){
				$temp_data = $row[1].'_'.$row[2].'_'.$row[3].'_'.$row[4];
			}else{
				$temp_data = $row[1].'_'.$row[2].'_'.$row[3];
			}

			if ($tempData != $temp_data){
				$tempData  = $temp_data;

				$svc[$seq]['order']	= $row[6].'_'.$row[1].'_'.$row[2].'_'.$row[3].'_'.$row[3];
				$svc[$seq]['start']	= $row[1];
				$svc[$seq]['end']	= $row[2];
				$svc[$seq]['yoy1']	= $row[3];
				$svc[$seq]['yoy2']	= $row[4];
				$svc[$seq]['svc']	= $row[5];
				$svc[$seq]['count']	= 0;
				$svc[$seq]['days']	= '/';
				$seq ++;
			}
			$svc[$seq-1]['count'] ++;
			$svc[$seq-1]['days'] .= $row[0].'/';
		}

		$conn->row_free();

		$temp_svc = $myF->sortArray($svc, 'order', 1);
		$svc = $temp_svc;

		?>
	<table style="width:100%">
		<colgroup>
			<col width="50px">
			<col width="60px">
			<col>
			<col width="40px">
		</colgroup>
			<tr>
				<td class="week">제공시간</td>
				<td class="week"><?= $type == 's' ? '요양보호사' : '수급자'; ?></td>
				<td class="week">제공서비스/제공일</td>
				<td class="week">횟수</td>
			</tr>
			<?
			for($i=0; $i<sizeOf($svc); $i++){

				echo "<tr>";
					echo	"<td class='cell' style='text-align:left; border-bottom:1px solid #ccc; padding-left:10px;' rowspan=2>".$svc[$i]['start']."<br>~".$svc[$i]['end']."</td>";

					if ($type == 's'){
						if ($svc[$i]['yoy2'] == ''){
							echo"<td class='cell' style='border-bottom:1px solid #ccc;'>".$svc[$i]['yoy1']."</td>";
						}else {
							echo"<td class='cell' style='border-bottom:1px solid #ccc;'>".$svc[$i]['yoy1']."".($svc[$i]['yoy2'] != '' ? ', ' : '')."".$svc[$i]['yoy2']."</td>";
						}
					}else{
						echo"<td class='cell' style='border-bottom:1px solid #ccc;'>".$svc[$i]['yoy1']."</td>";
					}

					echo"<td class='cell' style='text-align:left; border-bottom:1px solid #ccc; padding-left:5px;'>".$svc[$i]['svc']."</td>";
					echo"<td class='cell' style='border-bottom:1px solid #ccc;' rowspan=2>".$svc[$i]['count']."</td>";

				echo "</tr>";
				echo "<tr><td class='cell' style='text-align:left; padding-left:5px;' colspan='2'>";
					for($j=1; $j<=31; $j++){
						if (strVal(strPos($svc[$i]['days'], "/$j/")) == ''){
							$fontColor = '#cccccc';
						}else{
							$fontColor = '#000000';
						}

						if ($j <= $lastDay){
							echo "<span style='color:$fontColor; margin-right:3px;'> $j</span>";
						}
					}
				echo "</td></tr>";
			}

		?>
	</table>
	<br>

	<?
	if($useType == 'y'){
		if($type == 's'){   ?>
			<table style="width:100%">
				<colgroup>
					<col width="10%">
					<col width="30%">
					<col width="15%">
					<col width="15%">
					<col>
				</colgroup>
					<tr>
						<td class="week">급여종류</td>
						<td class="week" colspan="2">서비스(서비스명 / 횟수)</td>
						<td class="week">수가</td>
						<td class="week">총급여비용</td>
						<td class="week">본인부담액</td>
					</tr><?
						//수급자
						$sql = "select case t01_svc_subcode when '200' then '방문요양'
															when '500' then '방문목욕'
															when '800' then '방문간호' else '-' end
								,      m01_suga_cont
								,      count(t01_suga_code1)
								,      t01_suga_tot
								,      sum(t01_suga_tot)
								,      case when t01_bipay_umu = 'Y' then t01_suga_tot else 0 end + case when t01_bipay_umu = 'Y' then 0 else (t01_suga_tot * m03_bonin_yul / 100) * count(t01_suga_code1) end
								  from t01iljung
								 inner join (
									   select m03_sdate, m03_edate, m03_bonin_yul
										 from m03sugupja
										where m03_ccode = '$code'
										  and m03_mkind = '$kind'
										  and m03_jumin = '$target'
										union all
									   select m31_sdate, m31_edate, m31_bonin_yul
										 from m31sugupja
										where m31_ccode = '$code'
										  and m31_mkind = '$kind'
										  and m31_jumin = '$target'
									   ) as sugupja
									on t01_sugup_date between m03_sdate and m03_edate
								 inner join (
									   select m01_mcode2, m01_suga_cont, m01_sdate, m01_edate
										 from m01suga
										where m01_mcode = '$code'
										union all
									   select m11_mcode2, m11_suga_cont, m11_sdate, m11_edate
										 from m11suga
										where m11_mcode = '$code'
									   ) as suga
									on t01_suga_code1 = m01_mcode2
								   and t01_sugup_date between m01_sdate and m01_edate
								 where t01_ccode = '$code'
								   and t01_mkind = '$kind'
								   and t01_jumin = '$target'
								   and t01_sugup_date like '$date%' $family_sql
								   and t01_del_yn = 'N'
								 group by t01_svc_subcode, m01_suga_cont, t01_suga_code1, t01_suga_tot
								 order by t01_svc_subcode";
						$conn->query($sql);
						$conn->fetch();
						$rowCount = $conn->row_count();

						$suga	= 0;
						$total	= 0;
						$bonin	= 0;

						for($i=0; $i<$rowCount; $i++){
							$row = $conn->select_row($i);

							echo"<tr>";
								echo "<td class='cell' >$row[0]</td>";
								echo "<td class='cell' style='border-right:0px; text-align:left; padding-left:5px;'>$row[1]</td><td class='cell' style='border-left:0px; text-align:right; padding-right:5px;'>$row[2]</td>";
								echo "<td class='cell' style='text-align:right; padding-right:5px;'>".number_format($row[3])."</td>";
								echo "<td class='cell' style='text-align:right; padding-right:5px;'>".number_format($row[4])."</td>";
								echo "<td class='cell' style='text-align:right; padding-right:5px;'>".number_format($myF->cutOff($row[5]))."</td>";
							echo"</tr>";

							$suga += $row[3];
							$total += $row[4];
							$bonin += $myF->cutOff($row[5]);
						}
						echo"<tr>";
							echo "<td class='cell' >합 계</td>";
							echo "<td class='cell' style='border-right:0px; text-align:left;'></td><td class='cell' style='border-left:0px; text-align:right; padding-right:5px;'></td>";
							echo "<td class='cell' style='text-align:right; padding-right:5px;'>".number_format($suga)."</td>";
							echo "<td class='cell' style='text-align:right; padding-right:5px;'>".number_format($total)."</td>";
							echo "<td class='cell' style='text-align:right; padding-right:5px;'>".number_format($bonin)."</td>";
						echo"</tr>";

						$conn->row_free();
		}else{ ?>
			<table style="width:100%">
				<colgroup>
					<col width="10%">
					<col width="45%">
					<col width="15%">
					<col width="15%">
				</colgroup>
				<tr>
					<td class="week">급여종류</td>
					<td class="week" colspan="2">서비스(서비스명 / 횟수)</td>
					<td class="week">수가</td>
					<td class="week">총급여비용</td>
				</tr><?
					// 요양보호사
					$sql = "select t01_svc_subcode
							,      m01_suga_cont
							,      sum(cnt) as cnt
							,      t01_suga_tot
							,      sum(tot_amt)
							  from (
								   select case t01_svc_subcode when '200' then '방문요양'
															   when '500' then '방문목욕'
															   when '800' then '방문간호' else '-' end as t01_svc_subcode
								   ,      m01_suga_cont
								   ,      count(t01_suga_code1) as cnt
								   ,      t01_suga_tot
								   ,      sum(t01_suga_tot) as tot_amt
									 from t01iljung
									inner join (
										  select m01_mcode2, m01_suga_cont, m01_sdate, m01_edate
											from m01suga
										   where m01_mcode = '$code'
										   union all
										  select m11_mcode2, m11_suga_cont, m11_sdate, m11_edate
											from m11suga
										   where m11_mcode = '$code'
										  ) as suga
									   on t01_suga_code1 = m01_mcode2
									  and t01_sugup_date between m01_sdate and m01_edate
									where t01_ccode = '$code'
									  and t01_mkind = '$kind'
									  and t01_sugup_date like '$date%' $family_sql
									  and t01_yoyangsa_id1 = '$target'
									  and t01_del_yn = 'N'
									group by t01_svc_subcode, t01_suga_code1
									union all
								   select case t01_svc_subcode when '200' then '방문요양'
															   when '500' then '방문목욕'
															   when '800' then '방문간호' else '-' end as t01_svc_subcode
								   ,      m01_suga_cont
								   ,      count(t01_suga_code1) as cnt
								   ,      t01_suga_tot
								   ,      sum(t01_suga_tot) as tot_amt
									 from t01iljung
									inner join (
										  select m01_mcode2, m01_suga_cont, m01_sdate, m01_edate
											from m01suga
										   where m01_mcode = '$code'
										   union all
										  select m11_mcode2, m11_suga_cont, m11_sdate, m11_edate
											from m11suga
										   where m11_mcode = '$code'
										  ) as suga
									   on t01_suga_code1 = m01_mcode2
									  and t01_sugup_date between m01_sdate and m01_edate
									where t01_ccode = '$code'
									  and t01_mkind = '$kind'
									  and t01_sugup_date like '$date%' $family_sql
									  and t01_yoyangsa_id2 = '$target'
									  and t01_del_yn = 'N'
									group by t01_svc_subcode, t01_suga_code1
								   ) as t
							 group by t01_svc_subcode, m01_suga_cont, t01_suga_tot
							 order by t01_svc_subcode, m01_suga_cont";
					$conn->query($sql);
					$conn->fetch();
					$rowCount = $conn->row_count();

					$suga	= 0;
					$total	= 0;

					for($i=0; $i<$rowCount; $i++){
						$row = $conn->select_row($i);

						echo"<tr>";
							echo "<td class='cell' >$row[0]</td>";
							echo "<td class='cell' style='border-right:0px; text-align:left; padding-left:5px;'>$row[1]</td><td class='cell' style='border-left:0px; text-align:right; padding-right:5px;' >$row[2]</td>";
							echo "<td class='cell' style='text-align:right; padding-right:5px;'>".number_format($row[3])."</td>";
							echo "<td class='cell' style='text-align:right; padding-right:5px;'>".number_format($row[4])."</td>";
						echo"</tr>";

						$suga += $row[3];
						$total += $row[4];
					}

					echo"<tr>";
						echo "<td class='cell'>합 계</td>";
						echo "<td class='cell' style='border-right:0px; text-align:left; padding-left:5px;'></td><td class='cell' style='border-left:0px; padding-right:5px;' align=right></td>";
						echo "<td class='cell' style='text-align:right; padding-right:5px;'>".number_format($suga)."</td>";
						echo "<td class='cell' style='text-align:right; padding-right:5px;'>".number_format($total)."</td>";
					echo"</tr>";

					$conn->row_free();
		}
	}
		$sql = "select m00_cname, m00_ctel, m00_bank_no, m00_bank_name, m00_bank_depos
					  from m00center
					 where m00_mcode = '$code'
					   and m00_mkind = '$kind'";
			$bank = $conn->get_array($sql);


		?>

		</table>
		</div>
		<div align="center" style="padding-top:20px; padding-bottom:20px;">
			<span style="font-size:14pt; font-weight:bold;"><?=$bank["m00_cname"]." (☎ ".$myF->phoneStyle($bank["m00_ctel"]).")";?></span>
		</div>

</table>
</div>
</form>

<?
	//include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>
<script language="javascript">
window.self.focus();
</script>
