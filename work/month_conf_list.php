<?
	include("../inc/_db_open.php");
	include("../inc/_ed.php");

	$con2 = new connection();
	$ed = new ed();

	$mCode = $_POST['mCode'];
	$mKind = $_POST['mKind'];
	$mYear = $_POST['mYear'];
	$mRate = $_POST['mRate'];


?>
<table class="view_type1" style="width:100%; height:100%;">
<tr>
	<th style="width:5%; height:24px; padding:0px; text-align:center;">No</th>
	<th style="width:10%; height:24px; padding:0px; text-align:center;">수급자</th>
	<th style="width:15%; height:24px; padding:0px; text-align:center;">인정번호</th>
	<th style="width:7%;  height:24px; padding:0px; text-align:center;">등급</th>
	<th style="width:8%; height:24px; padding:0px; text-align:center;">부담율</th>
	<th style="width:55%; height:24px; padding:0px; text-align:center;">월별확정내역</th>
</tr>
<?
	$sql = "select m03_name"
		 . ",      LVL.m81_name as lvlName"
		 . ",      m03_bonin_yul"
		 . ",      m03_jumin"
		 . ",      m03_key
			,      m03_injung_no"
		 . ",      left(case when ifnull(m03_gaeyak_fm, '') != '' then ifnull(m03_gaeyak_fm, '') else date_format(now(), '%Y%m') end, 6) as m03_gaeyak_fm"
		 . ",      left(case when ifnull(m03_gaeyak_to, '') != '' then ifnull(m03_gaeyak_to, '') else date_format(now(), '%Y%m') end, 6) as m03_gaeyak_to"
		 . ",      sum(case substring(t01_sugup_date, 5, 2) when '01' then 1 else 0 end) as mon01"
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
		 . " inner join m03sugupja"
		 . "    on m03_ccode = t01_ccode"
		 . "   and m03_mkind = t01_mkind"
		 . "   and m03_jumin = t01_jumin"
		 . " inner join m81gubun as LVL"
		 . "    on LVL.m81_gbn  = 'LVL'"
		 . "   and LVL.m81_code = m03_ylvl"
		 . " where t01_ccode = '".$mCode
		 . "'  and t01_mkind = '".$mKind
		 . "'  and left(t01_sugup_date, 4) = '".$mYear
		 //. "'  and t01_status_gbn = '1'" //상태구분값
		 . "'  and t01_del_yn = 'N'";
		 /*
		 // 확정된 데이타 여부
		 . "   and (select count(*)"
		 . "          from t13sugupja"
		 . "         where t13_ccode = t01_ccode"
		 . "           and t13_mkind = t01_mkind"
		 . "           and t13_jumin = t01_jumin"
		 . "           and left(t13_pay_date, 4) = left(t13_pay_date, 4)) > 0";
		 */
	if ($mRate != 'all'){
		$sql .= " and m03_bonin_yul = '".$mRate
			 .  "'";
	}

	$sql .=" group by m03_name"
		 . ",         m03_bonin_yul"
		 . ",         m03_jumin"
		 . ",         m03_key"
		 . " order by m03_name";

	$conn->query($sql);
	$row = $conn->fetch();
	$row_count = $conn->row_count();

	if ($row_count > 0){
		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);

			$sql = "select sum(case substring(t13_pay_date, 5, 2) when '01' then 1 else 0 end) as mon01"
				 . ",      sum(case substring(t13_pay_date, 5, 2) when '02' then 1 else 0 end) as mon02"
				 . ",      sum(case substring(t13_pay_date, 5, 2) when '03' then 1 else 0 end) as mon03"
				 . ",      sum(case substring(t13_pay_date, 5, 2) when '04' then 1 else 0 end) as mon04"
				 . ",      sum(case substring(t13_pay_date, 5, 2) when '05' then 1 else 0 end) as mon05"
				 . ",      sum(case substring(t13_pay_date, 5, 2) when '06' then 1 else 0 end) as mon06"
				 . ",      sum(case substring(t13_pay_date, 5, 2) when '07' then 1 else 0 end) as mon07"
				 . ",      sum(case substring(t13_pay_date, 5, 2) when '08' then 1 else 0 end) as mon08"
				 . ",      sum(case substring(t13_pay_date, 5, 2) when '09' then 1 else 0 end) as mon09"
				 . ",      sum(case substring(t13_pay_date, 5, 2) when '10' then 1 else 0 end) as mon10"
				 . ",      sum(case substring(t13_pay_date, 5, 2) when '11' then 1 else 0 end) as mon11"
				 . ",      sum(case substring(t13_pay_date, 5, 2) when '12' then 1 else 0 end) as mon12"
				 . ",     (select count(*) from t01iljung where t01_ccode = t13_ccode and t01_mkind = t13_mkind and t01_jumin = t13_jumin and left(t01_sugup_date, 6) = concat(left(t13_pay_date, 4), '01')) as iljung01"
				 . ",     (select count(*) from t01iljung where t01_ccode = t13_ccode and t01_mkind = t13_mkind and t01_jumin = t13_jumin and left(t01_sugup_date, 6) = concat(left(t13_pay_date, 4), '02')) as iljung02"
				 . ",     (select count(*) from t01iljung where t01_ccode = t13_ccode and t01_mkind = t13_mkind and t01_jumin = t13_jumin and left(t01_sugup_date, 6) = concat(left(t13_pay_date, 4), '03')) as iljung03"
				 . ",     (select count(*) from t01iljung where t01_ccode = t13_ccode and t01_mkind = t13_mkind and t01_jumin = t13_jumin and left(t01_sugup_date, 6) = concat(left(t13_pay_date, 4), '04')) as iljung04"
				 . ",     (select count(*) from t01iljung where t01_ccode = t13_ccode and t01_mkind = t13_mkind and t01_jumin = t13_jumin and left(t01_sugup_date, 6) = concat(left(t13_pay_date, 4), '05')) as iljung05"
				 . ",     (select count(*) from t01iljung where t01_ccode = t13_ccode and t01_mkind = t13_mkind and t01_jumin = t13_jumin and left(t01_sugup_date, 6) = concat(left(t13_pay_date, 4), '06')) as iljung06"
				 . ",     (select count(*) from t01iljung where t01_ccode = t13_ccode and t01_mkind = t13_mkind and t01_jumin = t13_jumin and left(t01_sugup_date, 6) = concat(left(t13_pay_date, 4), '07')) as iljung07"
				 . ",     (select count(*) from t01iljung where t01_ccode = t13_ccode and t01_mkind = t13_mkind and t01_jumin = t13_jumin and left(t01_sugup_date, 6) = concat(left(t13_pay_date, 4), '08')) as iljung08"
				 . ",     (select count(*) from t01iljung where t01_ccode = t13_ccode and t01_mkind = t13_mkind and t01_jumin = t13_jumin and left(t01_sugup_date, 6) = concat(left(t13_pay_date, 4), '09')) as iljung09"
				 . ",     (select count(*) from t01iljung where t01_ccode = t13_ccode and t01_mkind = t13_mkind and t01_jumin = t13_jumin and left(t01_sugup_date, 6) = concat(left(t13_pay_date, 4), '10')) as iljung10"
				 . ",     (select count(*) from t01iljung where t01_ccode = t13_ccode and t01_mkind = t13_mkind and t01_jumin = t13_jumin and left(t01_sugup_date, 6) = concat(left(t13_pay_date, 4), '11')) as iljung11"
				 . ",     (select count(*) from t01iljung where t01_ccode = t13_ccode and t01_mkind = t13_mkind and t01_jumin = t13_jumin and left(t01_sugup_date, 6) = concat(left(t13_pay_date, 4), '12')) as iljung12"
				 . "  from t13sugupja"
				 . " where t13_ccode = '".$mCode
				 . "'  and t13_mkind = '".$mKind
				 . "'  and t13_jumin = '".$row['m03_jumin']
				 . "'  and left(t13_pay_date, 4) = '".$mYear
				 . "'  and t13_type = '2'";
			$con2->query($sql);
			$payRow = $con2->fetch();
			$con2->row_free();
			?>
			<tr>
				<td style="padding:0px; text-align:center; border-top:0px;"><?=$i+1;?></td>
				<td style="padding:0px; text-align:center; border-top:0px;"><?=$row["m03_name"];?></td>
				<td style="padding:0px; text-align:left; border-top:0px;"><?=$row['m03_injung_no'];?></td>
				<td style="padding:0px; text-align:center; border-top:0px;"><?=$row["lvlName"];?></td>
				<td style="padding:0px; text-align:center; border-top:0px;"><?=$row["m03_bonin_yul"];?>%</td>
				<td style="padding:0px; text-align:left;   border-top:0px; padding-top:2px;">
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
									if (ceil($payRow['mon'.$curI]) > 0){
									?>
										<td style="width:35px; height:23px; border:none; padding:0,1px; margin:0; text-align:center;<? if($payRow['iljung'.$curI] > 0){echo('background:url(\'../image/bg_calendar_y.gif\') no-repeat;');}else{echo('background:url(\'../image/bg_calendar_g.gif\') no-repeat;');}?>"><a href="#" onClick="goMonthConfSugupja('<?=$mYear;?>', '<?=$curI;?>', '<?=$mCode;?>', '<?=$mKind;?>', '<?=urlEncode($ed->encode($row['m03_jumin']));?>');"><?=$j;?>월</a></td>
									<?
									}else{
										if($row['mon'.$curI] > 0){
										?>
											<td style="width:35px; height:23px; border:none; padding:0,1px; margin:0; text-align:center; background:url('../image/bg_calendar_g.gif') no-repeat;"><a href="#" onClick="goMonthConfSugupja('<?=$mYear;?>', '<?=$curI;?>', '<?=$mCode;?>', '<?=$mKind;?>', '<?=urlEncode($ed->encode($row['m03_jumin']));?>');"><?=$j;?>월</a></td>
										<?
										}else{
										?>
											<td style="width:35px; height:23px; border:none; padding:0,1px; margin:0; text-align:center;">&nbsp;</td>
										<?
										}
									}
								}
							}
						}
					?>
					</tr>
					</table>
				</td>
			</tr>
		<?
		}
	}else{
		echo "
			<tr>
				<td style='text-align:center;' colspan='6'>::검색된 데이타가 없습니다.::</td>
			</tr>
			 ";
	}
?>
</table>
<input name="curYear" type="hidden" value="">
<input name="curMonth" type="hidden" value="">
<input name="curMcode" type="hidden" value="">
<input name="curMkind" type="hidden" value="">
<input name="curSugupja" type="hidden" value="">
<?
	$con2->close();
	include("../inc/_db_close.php");
?>