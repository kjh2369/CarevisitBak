<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");

	$con2 = new connection();

	$mGubun = $_POST['mGubun'];
	$mCode = $_POST['mCode'];
	$mKind = $_POST['mKind'];
	$mYear = $_POST['mYear'] != '' ? $_POST['mYear'] : date("Y", mkTime());

	if ($mGubun == 'reg'){
		$goFun = '_setSugupjaReg';
	}else{
		$goFun = '_setSugupjaSearch';
	}
?>
<form name="suList" method="post">
<table style="width:100%;">
	<tr>
		<td class="noborder" style="height:33px; text-align:left; vertical-align:bottom; padding-bottom:1px;">
			<select name="mYear" style="width:auto;" onChange="_getSugupjaList(myBody, this.value, '<?=$mGubun;?>', '<?=$mCode;?>', document.suList.myKind.value);">
			<?
				$years = $conn->get_min_max_year('t01iljung', 't01_sugup_date');
				$years[1] = date("Y", mkTime())+(date("m", mkTime())=="12"?1:0);
				for($i=$years[0]; $i<=$years[1]; $i++){
				?>
					<option value="<?=$i;?>"<? if($i == $mYear){echo "selected";}?>><?=$i;?></option>
				<?
				}
			?>
			</select>
			<select name="myKind" style="width:150px;" onChange="_getSugupjaList(myBody, document.suList.mYear.value, '<?=$mGubun;?>', '<?=$mCode;?>', this.value);">
			<?
				for($r=0; $r<sizeOf($_SESSION["userCenterKind"]); $r++){
				?>
					<option value="<?=$_SESSION["userCenterKind"][$r];?>"<? if($_SESSION["userCenterKind"][$r] == $_POST["mKind"]){echo "selected";}?>><?=$_SESSION["userCenterKindName"][$r];?></option>
				<?
				}
			?>
			</select>
		</td>
	</tr>
</table>
<table class="view_type1" style="width:100%; height:100%;">
<colgroup>
	<col width="70px">
	<col width="70px">
	<col width="50px">
	<col width="130px">
	<col width="100px">
	<col width="450px">
	<col>
</colgroup>
<tr>
	<th style="height:24px; padding:0px; text-align:center;">수급자명</th>
	<th style="height:24px; padding:0px; text-align:center;">인정번호</th>
	<th style="height:24px; padding:0px; text-align:center;">등급</th>
	<th style="height:24px; padding:0px; text-align:center;">구분</th>
	<th style="height:24px; padding:0px; text-align:center;">요양보호사</th>
	<th style="height:24px; padding:0px; text-align:center;">월별일정</th>
	<th></th>
</tr>
<?
	$sql = "select m03_name
			,      m03_jumin
			,      LVL.m81_name as lvl_name
			,      STP.m81_name as stp_name
			,      m03_key
			,      m03_gaeyak_fm
			,      m03_gaeyak_to
			,      m03_injung_no
			,      yoyName
			  from (
				   select m03_name
				   ,      m03_jumin
				   ,      m03_ylvl
				   ,      m03_skind
				   ,      m03_injung_no
				   ,      m03_key
				   ,      left(case when ifnull(m03_gaeyak_fm, '') != '' then ifnull(m03_gaeyak_fm, '') else date_format(now(), '%Y%m') end, 6) as m03_gaeyak_fm
				   ,      left(case when ifnull(m03_gaeyak_to, '') != '' then ifnull(m03_gaeyak_to, '') else '999912' end, 6) as m03_gaeyak_to
				   ,      m03_yoyangsa1_nm as yoyName
				   ,      m03_sdate
				   ,      m03_edate
					 from m03sugupja
					where m03_ccode = '$mCode'
					  and m03_mkind = '$mKind'
					union all
				   select m03_name
				   ,      m31_jumin
				   ,      m31_level
				   ,      m31_kind
				   ,      m03_injung_no
				   ,      m03_key
				   ,      left(case when ifnull(m03_gaeyak_fm, '') != '' then ifnull(m03_gaeyak_fm, '') else date_format(now(), '%Y%m') end, 6) as m03_gaeyak_fm
				   ,      left(case when ifnull(m03_gaeyak_to, '') != '' then ifnull(m03_gaeyak_to, '') else '999912' end, 6) as m03_gaeyak_to
				   ,      m03_yoyangsa1_nm as yoyName
				   ,      m31_sdate
				   ,      m31_edate
					 from m31sugupja
					inner join m03sugupja
					   on m03_ccode = m31_ccode
					  and m03_mkind = m31_mkind
					  and m03_jumin = m31_jumin
					where m31_ccode = '$mCode'
					  and m31_mkind = '$mKind'
				  ) as sugupja
			 inner join m81gubun as LVL
				on LVL.m81_gbn = 'LVL'
			   and LVL.m81_code = sugupja.m03_ylvl
			 inner join m81gubun as STP
				on STP.m81_gbn = 'STP'
			   and STP.m81_code = m03_skind
			 where '$mYear' between left(case when ifnull(m03_gaeyak_fm, '') != '' then ifnull(m03_gaeyak_fm, '') else date_format(now(), '%Y') end, 4) and left(case when ifnull(m03_gaeyak_to, '') != '' then ifnull(m03_gaeyak_to, '') else '9999' end, 4)
			   and date_format(now(), '%Y%m%d') between sugupja.m03_sdate and sugupja.m03_edate
			 order by m03_name";

	/*
	$sql = "select m03_name"
		 . ",      m03_jumin"
		 . ",      LVL.m81_name as lvl_name"
		 . ",      STP.m81_name as stp_name"
		 . ",      m03_key"
		 . ",      left(case when ifnull(m03_gaeyak_fm, '') != '' then ifnull(m03_gaeyak_fm, '') else date_format(now(), '%Y%m') end, 6) as m03_gaeyak_fm"
		 . ",      left(case when ifnull(m03_gaeyak_to, '') != '' then ifnull(m03_gaeyak_to, '') else '999912' end, 6) as m03_gaeyak_to"
		 . ",      m03_yoyangsa1_nm as yoyName"
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
	*/
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
			<td style="padding:0px; text-align:left;"><?=$row["m03_name"];?></td>
			<td style="padding:0px; text-align:left;"><?=$row["m03_injung_no"];?></td>
			<td style="padding:0px; text-align:center;"><?=$row["lvl_name"];?></td>
			<td style="padding:0px; text-align:left;"><?=$row["stp_name"];?></td>
			<td style="padding:0px; text-align:left;"><?=$row["yoyName"];?></td>
			<td style="padding:0px; text-align:left; padding-top:2px;">
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
								<td style="width:35px; height:23px; border:none; padding:0,1px; margin:0; text-align:center;<? if($iljung['mon'.$curI] > 0){echo('background:url(\'../image/bg_calendar_y.gif\') no-repeat;');}else{echo('background:url(\'../image/bg_calendar_g.gif\') no-repeat;');}?>"><a href="#" onClick="<?=$goFun;?>('<?=$_POST["mCode"];?>', '<?=$_POST["mKind"];?>', '<?=$row["m03_key"];?>', document.suList.mYear.value, '<?=$curI;?>');"><?=$j;?>월</a></td>
							<?
							}
						}
					}
				?>
				</tr>
				</table>
			</td>
			<td></td>
		</tr>
	<?
	}
?>
</table>
</form>
<?
	$con2->close();
	include_once("../inc/_footer.php");
?>