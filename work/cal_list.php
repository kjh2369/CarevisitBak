<?
	include("../inc/_header.php");
	include("../inc/_myFun.php");
	include('../inc/_ed.php');

	$con2 = new connection();

	$mPlan = $_POST['mPlan'];
	$mCode = $_POST['mCode'];
	$mKind = $_POST['mKind'];
	$mYear = $_POST['mYear'];
	$mType = $_POST['mType'];
?>
<table class="view_type1">
<colGroup>
<?
	if ($mType == "s"){
		echo '
			<col width="70px">
			<col width="70px">
			<col width="40px">
			<col width="120px">
			<col width="450px">
			<col>
			 ';
	}else{
		echo '
			<col width="100px">
			<col width="70px">
			<col width="450px">
			<col>
			 ';
	}
?>
</colGroup>
<thead>
<tr>
<?
	if ($mType == "s"){
		echo '
			<th style="height:24px; padding:0px; text-align:center;">수급자</th>
			<th style="height:24px; padding:0px; text-align:center;">인정번호</th>
			<th style="height:24px; padding:0px; text-align:center;">등급</th>
			<th style="height:24px; padding:0px; text-align:center;">구분</th>
			<th style="height:24px; padding:0px; text-align:center;">월별일정</th>
			<th style="height:24px; padding:0px; text-align:center;">비고</th>
			 ';
	}else{
		echo '
			<th style="height:24px; padding:0px; text-align:center;">요양보호사</th>
			<th style="height:24px; padding:0px; text-align:center;">요양사번호</th>
			<th style="height:24px; padding:0px; text-align:center;">월별일정</th>
			<th style="height:24px; padding:0px; text-align:center;">비고</th>
			 ';
	}
?>
</tr>
<?
	if ($mType == "s"){
		$sql = "select m03_name"
			 . ",      m03_jumin"
			 . ",      LVL.m81_name as lvl_name"
			 . ",      STP.m81_name as stp_name"
			 . ",      m03_key"
			 . ",      m03_injung_no"
			 . ",      left(case when ifnull(m03_gaeyak_fm, '') != '' then ifnull(m03_gaeyak_fm, '') else date_format(now(), '%Y%m') end, 6) as m03_gaeyak_fm"
			 . ",      left(case when ifnull(m03_gaeyak_to, '') != '' then ifnull(m03_gaeyak_to, '') else '999912' end, 6) as m03_gaeyak_to"
			 . "  from m03sugupja"
			 . " inner join m81gubun as LVL"
			 . "    on LVL.m81_gbn = 'LVL'"
			 . "   and LVL.m81_code = m03_ylvl"
			 . " inner join m81gubun as STP"
			 . "    on STP.m81_gbn = 'STP'"
			 . "   and STP.m81_code = m03_skind"
			 . " where m03_ccode = '".$mCode
			 . "'  and m03_mkind = '".$mKind
			 . "'  and '".$mYear."' between left(case when ifnull(m03_gaeyak_fm, '') != '' then ifnull(m03_gaeyak_fm, '') else date_format(now(), '%Y') end, 4) and left(case when ifnull(m03_gaeyak_to, '') != '' then ifnull(m03_gaeyak_to, '') else '9999' end, 4)"
			 . " order by m03_name";
		$conn->query($sql);
		$row = $conn->fetch();
		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);

			$sql = "select sum(case substring(t01_sugup_date, 5, 2) when '01' then 1 else 0 end) as mon01"
				 . ",      sum(case substring(t01_sugup_date, 5, 2) when '02' then 1 else 0 end) as mon02"
				 . ",      sum(case substring(t01_sugup_date, 5, 2) when '03' then 1 else 0 end) as mon03"
				 . ",      sum(case substring(t01_sugup_date, 5, 2) when '04' then 1 else 0 end) as mon04"
				 . ",      sum(case substring(t01_sugup_date, 5, 2) when '05' then 1 else 0 end) as mon05"
				 . ",      sum(case substring(t01_sugup_date, 5, 2) when '06' then 1 else 0 end) as mon06"
				 . ",      sum(case substring(t01_sugup_date, 5, 2) when '07' then 1 else 0 end) as mon07"
				 . ",      sum(case substring(t01_sugup_date, 5, 2) when '08' then 1 else 0 end) as mon08"
				 . ",      sum(case substring(t01_sugup_date, 5, 2) when '09' then 1 else 0 end) as mon09"
				 . ",      sum(case substring(t01_sugup_date, 5, 2) when '10' then 1 else 0 end) as mon10"
				 . ",      sum(case substring(t01_sugup_date, 5, 2) when '11' then 1 else 0 end) as mon11"
				 . ",      sum(case substring(t01_sugup_date, 5, 2) when '12' then 1 else 0 end) as mon12"
				 . "  from t01iljung"
				 . " where t01_ccode = '".$mCode
				 . "'  and t01_mkind = '".$mKind
				 . "'  and t01_jumin = '".$row['m03_jumin']
				 . "'  and left(t01_sugup_date, 4) = '".$mYear
				 . "'  and t01_del_yn = 'N'";
			$con2->query($sql);
			$iljung = $con2->fetch();
			$con2->row_free();
			?>
			<tr>
				<td style="padding:0px; text-align:center; border-top:0px;"><?=$row["m03_name"];?></td>
				<td style="padding:0px; text-align:center; border-top:0px;"><?=$row['m03_injung_no'];?></td>
				<td style="padding:0px; text-align:center; border-top:0px;"><?=$row["lvl_name"];?></td>
				<td style="padding:0px; text-align:left; border-top:0px;"><?=$row["stp_name"];?></td>
				<td style="padding:0px; text-align:left; border-top:0px; padding-top:2px;">
					<table>
					<tr>
					<?
						for($j=1; $j<=12; $j++){
							if ($j < 10){
								$curI = '0'.$j;
							}else{
								$curI = $j;
							}

							if (ceil($row['m03_gaeyak_fm']) > ceil($mYear.$curI)){
							?>
								<td style="width:35px; height:23px; border:none; padding:0,1px; margin:0; text-align:center;">&nbsp;</td>
							<?
							}else{
								if (ceil($row['m03_gaeyak_to']) < ceil($mYear.$curI)){
								?>
									<td style="width:35px; height:23px; border:none; padding:0,1px; margin:0; text-align:center;">&nbsp;</td>
								<?
								}else{
								?>
									<td style="width:35px; height:23px; border:none; padding:0,1px; margin:0; text-align:center;<? if($iljung['mon'.$curI] > 0){echo('background:url(\'../image/bg_calendar_y.gif\') no-repeat;');}else{echo('background:url(\'../image/bg_calendar_g.gif\') no-repeat;');}?>"><? if($iljung['mon'.$curI] > 0){?><a href="#" onClick="serviceCalendarShow('<?=$mCode;?>', '<?=$mKind;?>', '<?=$mYear;?>', '<?=$curI;?>', '<?=$ed->en($row["m03_jumin"]);?>', '<?=$mType;?>', document.f.useType.value, document.f.printType.value, document.getElementById('detail_yn').value); return false;"><?=$j;?>월</a><?}else{?><font color="#a1a1a1"><?=$j;?>월</font><?}?></td>
								<?
								}
							}
						}
					?>
					</tr>
					</table>
				</td>
				<td style="padding:0px; text-align:left; border-top:0px;"></td>
			</tr>
		<?
		}
	}else{
		$sql = "select m02_yname as name
				,      m02_yjumin as jumin
				,      m02_ycode
				,      left(case when ifnull(m02_yipsail, '')  != '' then ifnull(m02_yipsail, '')  else date_format(now(), '%Y%m') end, 6) as startDate
				,      left(case when ifnull(m02_ytoisail, '') != '' then ifnull(m02_ytoisail, '') else '999912' end, 6) as endDate
				  from m02yoyangsa
				 where m02_ccode = '$mCode'
				   and m02_mkind = '$mKind'
				   and '$mYear' between left(case when ifnull(m02_yipsail, '') != '' then m02_yipsail else date_format(now(), '%Y') end, 4) and left(case when ifnull(m02_ytoisail, '') != '' then m02_ytoisail else '9999' end, 4)
				 order by m02_yname";
		$conn->query($sql);
		$conn->fetch();
		$rowCount = $conn->row_count();

		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			$sql = "select sum(case substring(t01_sugup_date, 5, 2) when '01' then 1 else 0 end) as mon01
					,      sum(case substring(t01_sugup_date, 5, 2) when '02' then 1 else 0 end) as mon02
					,      sum(case substring(t01_sugup_date, 5, 2) when '03' then 1 else 0 end) as mon03
					,      sum(case substring(t01_sugup_date, 5, 2) when '04' then 1 else 0 end) as mon04
					,      sum(case substring(t01_sugup_date, 5, 2) when '05' then 1 else 0 end) as mon05
					,      sum(case substring(t01_sugup_date, 5, 2) when '06' then 1 else 0 end) as mon06
					,      sum(case substring(t01_sugup_date, 5, 2) when '07' then 1 else 0 end) as mon07
					,      sum(case substring(t01_sugup_date, 5, 2) when '08' then 1 else 0 end) as mon08
					,      sum(case substring(t01_sugup_date, 5, 2) when '09' then 1 else 0 end) as mon09
					,      sum(case substring(t01_sugup_date, 5, 2) when '10' then 1 else 0 end) as mon10
					,      sum(case substring(t01_sugup_date, 5, 2) when '11' then 1 else 0 end) as mon11
					,      sum(case substring(t01_sugup_date, 5, 2) when '12' then 1 else 0 end) as mon12
					  from t01iljung
					 where t01_ccode = '$mCode'
					   and t01_mkind = '$mKind'
					   and '".$row["jumin"]."' in (t01_yoyangsa_id1, t01_yoyangsa_id2, t01_yoyangsa_id3, t01_yoyangsa_id4, t01_yoyangsa_id5)
					   and left(t01_sugup_date, 4) = '$mYear'
					   and t01_del_yn = 'N'";
			$con2->query($sql);
			$iljung = $con2->fetch();
			$con2->row_free();
			?>
			<tr>
				<td style="padding:0px; text-align:left; border-top:0px;"><?=$row["name"];?></td>
				<td style="padding:0px; text-align:left; border-top:0px;"><?=$row['m02_ycode'];?></td>
				<td style="padding:0px; text-align:left; border-top:0px; padding-top:2px;">
					<table>
					<tr>
					<?
						for($j=1; $j<=12; $j++){
							if ($j < 10){
								$curI = '0'.$j;
							}else{
								$curI = $j;
							}

							if (ceil($row['startDate']) > ceil($mYear.$curI)){
							?>
								<td style="width:35px; height:23px; border:none; padding:0,1px; margin:0; text-align:center;">&nbsp;</td>
							<?
							}else{
								if (ceil($row['endDate']) < ceil($mYear.$curI)){
								?>
									<td style="width:35px; height:23px; border:none; padding:0,1px; margin:0; text-align:center;">&nbsp;</td>
								<?
								}else{
								?>
									<td style="width:35px; height:23px; border:none; padding:0,1px; margin:0; text-align:center;<? if($iljung['mon'.$curI] > 0){echo('background:url(\'../image/bg_calendar_y.gif\') no-repeat;');}else{echo('background:url(\'../image/bg_calendar_g.gif\') no-repeat;');}?>"><? if($iljung['mon'.$curI] > 0){?><a href="#" onClick="serviceCalendarShow('<?=$mCode;?>', '<?=$mKind;?>', '<?=$mYear;?>', '<?=$curI;?>', '<?=$ed->en($row["jumin"]);?>', '<?=$mType;?>', document.f.useType.value, document.f.printType.value, document.getElementById('detail_yn').value); return false;"><?=$j;?>월</a><?}else{?><font color="#a1a1a1"><?=$j;?>월</font><?}?></td>
								<?
								}
							}
						}
					?>
					</tr>
					</table>
				</td>
				<td style="padding:0px; text-align:left; border-top:0px;"></td>
			</tr>
		<?
		}
		$conn->row_free();
	}
?>
</thead>
</table>
<?
	include("../inc/_footer.php");
?>