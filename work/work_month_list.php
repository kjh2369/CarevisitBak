<?
	include("../inc/_header.php");
	include("../inc/_ed.php");

	$con2 = new connection;

	$mCode  = $_POST['mCode'];
	$mKind  = $_POST['mKind'];
	$mYoy   = $_POST['mYoy'];
	$mYear  = $_POST['mYear'];
	$mMonth = $_POST['mMonth'];

	$setYear = $conn->get_iljung_year($mCode);

	if ($mYear == ''){
		$mYear = date('Y', mkTime());
	}

	if ($mMonth == ''){
		$mMonth = date('m', mkTime());
	}
?>
<form name="f" method="post">
<table style="width:100%;">
<tr>
<td class="noborder" style="width:100%; height:33px; text-align:left; vertical-align:bottom; padding-bottom:1px;">
	<select name="mKind" style="width:150px;" onChange="setYoyList('<?=$mCode;?>', 'mYoy');">
	<?
		for($r=0; $r<sizeOf($_SESSION["userCenterKind"]); $r++){
		?>
			<option value="<?=$_SESSION["userCenterKind"][$r];?>"<? if($_SESSION["userCenterKind"][$r] == $_POST["mKind"]){echo "selected";}?>><?=$_SESSION["userCenterKindName"][$r];?></option>
		<?
		}
	?>
	</select>
	<select name="mYear" style="width:65px;" onChange="setYoyList('<?=$mCode;?>', 'mYoy');">
	<?
		for($i=$setYear[0]; $i<=$setYear[1]; $i++){
		?>
			<option value="<?=$i;?>" <? if($i == $mYear){echo 'selected';} ?>><?=$i;?>년</option>
		<?
		}
	?>
	</select>
	<select name="mMonth" style="width:55px;" onChange="setYoyList('<?=$mCode;?>', 'mYoy');">
		<option value="01"<? if($mMonth == "01"){echo "selected";}?>>1월</option>
		<option value="02"<? if($mMonth == "02"){echo "selected";}?>>2월</option>
		<option value="03"<? if($mMonth == "03"){echo "selected";}?>>3월</option>
		<option value="04"<? if($mMonth == "04"){echo "selected";}?>>4월</option>
		<option value="05"<? if($mMonth == "05"){echo "selected";}?>>5월</option>
		<option value="06"<? if($mMonth == "06"){echo "selected";}?>>6월</option>
		<option value="07"<? if($mMonth == "07"){echo "selected";}?>>7월</option>
		<option value="08"<? if($mMonth == "08"){echo "selected";}?>>8월</option>
		<option value="09"<? if($mMonth == "09"){echo "selected";}?>>9월</option>
		<option value="10"<? if($mMonth == "10"){echo "selected";}?>>10월</option>
		<option value="11"<? if($mMonth == "11"){echo "selected";}?>>11월</option>
		<option value="12"<? if($mMonth == "12"){echo "selected";}?>>12월</option>
	</select>
	<select name="mYoy">
	<option value="">-요양사 선택-</option>
	<?
		$sql = "select distinct"
			 . "       m02_yjumin"
			 . ",      m02_yname"
			 . "  from t01iljung"
			 . " inner join m02yoyangsa"
			 . "    on m02_ccode  = t01_ccode"
			 . "   and m02_mkind  = t01_mkind"
			 . "   and m02_yjumin in (t01_mem_cd1, t01_mem_cd2)"
			 . " where t01_ccode = '".$mCode
			 . "'  and t01_mkind = '".$mKind
			 . "'  and left(t01_sugup_date, 6) = '".$mYear.$mMonth
			 . "'"
			 . " order by m02_yname";
		$conn->query($sql);
		$conn->fetch();
		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);
			if ($mYoy == $row['m02_yjumin']){
				echo '<option value="'.$row['m02_yjumin'].'" selected>'.$row['m02_yname'].'</option>';
			}else{
				echo '<option value="'.$row['m02_yjumin'].'">'.$row['m02_yname'].'</option>';
			}
		}
	?>
	</select>
	<span class="btn_pack m icon"><span class="refresh"></span><button type="button" onFocus="this.blur();" onClick="getWorkMonthList('<?=$mCode;?>', document.f.mKind.value, document.f.mYoy.value, document.f.mYear.value, document.f.mMonth.value);">조회</button></span>
</td>
</tr>
</table>
<table class="view_type1" style="width:100%; height:100%;">
<tr style="height:24px;">
<th style="padding:0px; text-align:center; border-right:1px solid #e5e5e5;" rowspan="2">일자</th>
<th style="padding:0px; text-align:center; border-right:1px solid #e5e5e5;" rowspan="2">시간</th>
<th style="padding:0px; text-align:center; border-right:1px solid #e5e5e5;" colspan="2">계획시간</th>
<th style="padding:0px; text-align:center; border-right:1px solid #e5e5e5; line-height:1.2em;" rowspan="2">수급자<br>성명</th>
<th style="padding:0px; text-align:center; border-right:1px solid #e5e5e5;" rowspan="2">서비스명</th>
<th style="padding:0px; text-align:center; border-right:1px solid #e5e5e5;" rowspan="2">주소</th>
<th style="padding:0px; text-align:center; border-right:1px solid #e5e5e5;" colspan="2">수행시간</th>
<th style="padding:0px; text-align:center; border-right:1px solid #e5e5e5; line-height:1.2em;" rowspan="2">실소요<br>시간(분)</th>
<th style="padding:0px; text-align:center; border-right:1px solid #e5e5e5; line-height:1.2em;" rowspan="2">인정<br>시간</th>
<th style="padding:0px; text-align:center; border-right:1px solid #e5e5e5;" rowspan="2">상황</th>
<th style="padding:0px; text-align:center; line-height:1.2em;" rowspan="2">위치<br>정보</th>
</tr>
<tr>
<th style="padding:0px; text-align:center; border-right:1px solid #e5e5e5;">시작</th>
<th style="padding:0px; text-align:center; border-right:1px solid #e5e5e5;">종료</th>
<th style="padding:0px; text-align:center; border-right:1px solid #e5e5e5;">시작</th>
<th style="padding:0px; text-align:center; border-right:1px solid #e5e5e5;">종료</th>
</tr>
<?
	$sql = "select concat(cast(substring(t01_sugup_date, 7, 2) as unsigned), '일') as curDay"
		 . ",      m03_name as suName"
		 . ",      m03_key"
		 . ",      t01_sugup_date"
		 . ",      t01_sugup_fmtime"
		 . ",      t01_sugup_seq"
		 . ",      t01_suga_code1 as sugaCode"
		 . ",      concat('[',left(m03_post_no,3),'-',substring(m03_post_no,4,3),'] ', m03_juso1, ' ', m03_juso2) as address"
		 . ",      concat(left(t01_sugup_fmtime, 2),':',substring(t01_sugup_fmtime,3,2)) as suFmTime"
		 . ",      concat(left(t01_sugup_totime, 2),':',substring(t01_sugup_totime,3,2)) as suToTime"
		 . ",      concat(left(ifnull(t01_wrk_fmtime, ''), 2),':',substring(ifnull(t01_wrk_fmtime, ''),3,2)) as wrkFmTime"
		 . ",      concat(left(ifnull(t01_wrk_totime, ''), 2),':',substring(ifnull(t01_wrk_totime, ''),3,2)) as wrkToTime"
		 . ",      (hour(ifnull(concat(substring(t01_wrk_totime, 1, 2), ':', substring(t01_wrk_totime, 3, 2)), '00:00')) * 60 + minute(ifnull(concat(substring(t01_wrk_totime, 1, 2), ':', substring(t01_wrk_totime, 3, 2)), '00:00'))) -"
		 . "       (hour(ifnull(concat(substring(t01_wrk_fmtime, 1, 2), ':', substring(t01_wrk_fmtime, 3, 2)), '00:00')) * 60 + minute(ifnull(concat(substring(t01_wrk_fmtime, 1, 2), ':', substring(t01_wrk_fmtime, 3, 2)), '00:00'))) as wrkSoyoTime"
		 . ",      t01_status_gbn as statGbnCode"
		 . ",      case when m81_name is null or m81_name = 'A' then '대기'"
		 . "            when concat(t01_sugup_date, t01_sugup_fmtime) <= date_format(now(), '%Y%m%d%H%i')"
		 . "             and t01_status_gbn = '9' then '미수행'"
		 . "            else m81_name end as statGbnName"
		 . ",      t01_conf_soyotime"
		 . ",      m03_jumin as sugupJumin"
		 . ",      t01_ccode as centerCode"
		 . ",      t01_mkind as centerKind"
		 . ",      t01_mem_cd1 as yoyCode1, t01_mem_cd2 as yoyCode2"
		 . ",      t01_mem_nm1 as yoyName1, t01_mem_nm2 as yoyName2
		    ,      t01_modify_pos as modPos
			,      t01_modify_yn  as modYN"
		 . "  from t01iljung"
		 . " inner join m03sugupja"
		 . "    on m03_ccode = t01_ccode"
		 . "   and m03_mkind = t01_mkind"
		 . "   and m03_jumin = t01_jumin"
		 . "  left join m81gubun"
		 . "    on m81_gbn  = 'STA'"
		 . "   and m81_code = t01_status_gbn"
		 . " where t01_ccode = '".$mCode
		 . "'  and t01_mkind = '".$mKind
		 //. "'  and t01_yoyangsa_id1 = '".$mYoy
		 . "'  and '".$mYoy."' in (t01_mem_cd1, t01_mem_cd2)"
		 . "   and '".$mYoy."' != ''"
		 . "   and left(t01_sugup_date, 6) = '".$mYear.$mMonth
		 . "'  and t01_del_yn = 'N'"
		 . " order by t01_sugup_date"
		 . ",         t01_sugup_fmtime"
		 . ",         t01_sugup_totime";

	//case when t01_wrk_fmtime > t01_wrk_totime then 24 else 0 end

	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();

	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);

		if (strLen($row['wrkFmTime']) == 5 and strLen($row['wrkToTime']) == 5){
			$wrkSoyoTime = $row['wrkSoyoTime'];

			if ($wrkSoyoTime < 0){
				$wrkSoyoTime = 24 * 60 + $wrkSoyoTime;
			}

			$wrkSoyoTime .= '분';
		}else{
			$wrkSoyoTime = '';
		}

		if (strLen($row['t01_conf_soyotime']) != 0 && $wrkSoyoTime != ''){
			$confSoyoTime = $row['t01_conf_soyotime'].'분';
		}else{
			$confSoyoTime = '';
		}

		$sugaName = $conn->get_suga($mCode, $row['sugaCode']);

		switch($row['statGbnName']){
			case '미수행':
				$statImage = '<img src="../image/icon_stat_0.png">';
				break;
			case '수행중':
				$statImage = '<img src="../image/icon_stat_5.png">';
				break;
			case '준비중':
				$statImage = '<img src="../image/icon_stat_9.png">';
				break;
			case '완료':
				if ($row['modYN'] == 'N'){
					$statImage = '<input name="btnSuccess[]" type="button" value="" onClick="popupWorkDetail(\''.$mCode.'\',\''.$mKind.'\',\''.$row['m03_key'].'\',\''.$row['t01_sugup_date'].'\',\''.$row['t01_sugup_fmtime'].'\',\''.$row['t01_sugup_seq'].'\');" style="width:44px; height:16px; border:0px; background:url(\'../image/icon_stat_1.png\') no-repeat; cursor:pointer;">';
				}else{
					$statImage = '<img src="../image/icon_stat_1_1.png" title="수동입력건입니다.">';
				}
				break;
			case '에러':
				$statImage = '<img src="../image/icon_error.png">';
				break;
			default:
				$statImage = '<img src="../image/icon_stat_A.png">';
		}

		$locationFind = '';
		if ($row['statGbnName'] == '수행중' ||
			$row['statGbnName'] == '완료' ||
			$row['statGbnName'] == '에러'){
			$tempSugupja = $ed->en($row['sugupJumin']);
			$tempYoyangsa = $ed->en($mYoy);

			if ($row['modYN'] == 'N'){
				$locationFind = '<a href="#" onClick="_locationFind(\''.$row['centerCode'].'\',\''.$row['centerKind'].'\',\''.$tempSugupja.'\',\''.$row['t01_sugup_date'].'\',\''.$row['t01_sugup_fmtime'].'\',\''.$row['t01_sugup_seq'].'\',\''.$tempYoyangsa.'\');"><img src="../image/btn_location_find.gif"></a>';
			}
		}

		if ($mYoy == $row['yoyCode1']){
			$master = '';
		}else{
			$master = '<span style="color:#ff0000;">(부)</span>';
		}

		echo '<tr>';

		if ($curDay != $row['curDay']){
			$curDay  = $row['curDay'];
			$border1 = 'border-bottom:none; border-top:1px solid #cccccc;';
			echo '<td style="width:5%; padding:0px; text-align:center;'.$border1.'">'.$row['curDay'].'</td>';
		}else{
			$border1 = 'border-bottom:none; border-top:none;';
			echo '<td style="width:5%; padding:0px; text-align:center;'.$border1.'"></td>';
		}

		if ($curTime != $curDay.'_'.subStr($row['t01_sugup_fmtime'], 0, 2)){
			$curTime  = $curDay.'_'.subStr($row['t01_sugup_fmtime'], 0, 2);
			$border2 = 'border-bottom:none; border-top:1px solid #cccccc;';
			echo '<td style="width:; padding:0px; text-align:center;'.$border2.'">'.subStr($row['t01_sugup_fmtime'], 0, 2).'</td>';
		}else{
			$border2 = 'border-bottom:none; border-top:none;';
			echo '<td style="width:; padding:0px; text-align:center;'.$border2.'">'.subStr($row['t01_sugup_fmtime'], 0, 2).'</td>';
		}

		echo '<td style="width:; padding:0px; text-align:center;'.$border2.'">'.$row['suFmTime'].'</td>';
		echo '<td style="width:; padding:0px; text-align:center;'.$border2.'">'.$row['suToTime'].'</td>';
		echo '<td style="width:; padding:0px; text-align:center;'.$border2.'">'.$row['suName'].'</td>';
		echo '<td style="width:; padding:0px; text-align:left;  '.$border2.'">'.$sugaName.$master.'</td>';
		echo '<td style="width:; padding:0px; text-align:left;  '.$border2.'">'.left($row['address'], 20).'</td>';
		echo '<td style="width:; padding:0px; text-align:center;'.$border2.'">'.($row['wrkFmTime'] != ':' ? $row['wrkFmTime'] : '').'</td>';
		echo '<td style="width:; padding:0px; text-align:center;'.$border2.'">'.($row['wrkToTime'] != ':' ? $row['wrkToTime'] : '').'</td>';
		echo '<td style="width:; padding:0px; text-align:center;'.$border2.'">'.$wrkSoyoTime.'</td>';
		echo '<td style="width:; padding:0px; text-align:center;'.$border2.'">'.$confSoyoTime.'</td>';
		echo '<td style="width:; padding:0px; text-align:center;'.$border2.'">'.$statImage.'</td>';
		echo '<td style="width:; padding:0px; text-align:center;'.$border2.'">'.$locationFind.'</td>';
		echo '</tr>';
	}

	$conn->row_free();
?>
</table>
</form>
<?
	$con2->close();

	include("../inc/_footer.php");
?>