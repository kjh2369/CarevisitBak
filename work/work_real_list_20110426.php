<?
	include("../inc/_header.php");
	include("../inc/_ed.php");

	$con2 = new connection;

	$mCode = $_POST['mCode'];
	$mKind = $_POST['mKind'];
	$mStat = $_POST['mStat'];

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
<td class="noborder" style="width:40%; height:33px; text-align:left; vertical-align:bottom; padding-bottom:1px;">
	<select name="mKind" style="width:150px;">
	<option value="all">전체</option>
	<?
		for($r=0; $r<sizeOf($_SESSION["userCenterKind"]); $r++){
		?>
			<option value="<?=$_SESSION["userCenterKind"][$r];?>"<? if($_SESSION["userCenterKind"][$r] == $_POST["mKind"]){echo "selected";}?>><?=$_SESSION["userCenterKindName"][$r];?></option>
		<?
		}
	?>
	</select>
	<select name="mStat" style="width:100px;">
	<option value="all">전체</option>
	<?
		$sql = "select m81_code"
			 . ",      m81_name"
			 . "  from m81gubun"
			 . " where m81_gbn = 'STA'";
		$conn->query($sql);
		$conn->fetch();
		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);
			?>
				<option value="<?=$row['m81_code'];?>" <? if($row['m81_code'] == $mStat){echo 'selected';} ?>><?=$row['m81_name'];?></option>
			<?
		}

		$conn->row_free();
	?>
	</select>
	<span class="btn_pack m icon"><span class="refresh"></span><button type="button" onFocus="this.blur();" onClick="getWorkRealList('<?=$mCode;?>', document.f.mKind.value, document.f.mStat.value);">조회</button></span>
</td>
<td class="noborder" style="width:60%; height:33px; text-align:right; vertical-align:bottom;">
<?
	$weekDay = date('w', mkTime());

	switch($weekDay){
		case 0: $weekDay = '일요일'; break;
		case 1: $weekDay = '월요일'; break;
		case 2: $weekDay = '화요일'; break;
		case 3: $weekDay = '수요일'; break;
		case 4: $weekDay = '목요일'; break;
		case 5: $weekDay = '금요일'; break;
		case 6: $weekDay = '토요일'; break;
	}
	$dateTimeString = '[일자 및 시간 : '.date('Y.m.d', mkTime()).' '.$weekDay.' '.date('H:i', mkTime()).']';
	echo $dateTimeString;
?>
</td>
</tr>
</table>
<table class="view_type1" style="width:100%; height:100%;">
<tr style="height:24px;">
<?
if ($mKind == 'all'){ ?>
	<th style="padding:0px; text-align:center; border-right:1px solid #d2d2d2;" rowspan="2">요양기관</th><?
} ?>
<th style="padding:0px; text-align:center; border-right:1px solid #d2d2d2;" rowspan="2">시간</th>
<th style="padding:0px; text-align:center; border-right:1px solid #d2d2d2;" colspan="2">계획시간</th>
<th style="padding:0px; text-align:center; border-right:1px solid #d2d2d2; line-height:1.2em;" rowspan="2">수급자<br>성명</th>
<th style="padding:0px; text-align:center; border-right:1px solid #d2d2d2;" rowspan="2">서비스명</th>
<th style="padding:0px; text-align:center; border-right:1px solid #d2d2d2; line-height:1.2em;" rowspan="2">요양보호사<br>성명</th>
<th style="padding:0px; text-align:center; border-right:1px solid #d2d2d2;" colspan="2">수행시간</th>
<th style="padding:0px; text-align:center; border-right:1px solid #d2d2d2; line-height:1.2em;" rowspan="2">실소요<br>시간(분)</th>
<th style="padding:0px; text-align:center; border-right:1px solid #d2d2d2; line-height:1.2em;" rowspan="2">인정<br>시간</th>
<th style="padding:0px; text-align:center; border-right:1px solid #d2d2d2;" rowspan="2">상황</th>
<th style="padding:0px; text-align:center; line-height:1.2em;" rowspan="2">위치<br>정보</th>
</tr>
<tr>
<th style="padding:0px; text-align:center; border-right:1px solid #d2d2d2;">시작</th>
<th style="padding:0px; text-align:center; border-right:1px solid #d2d2d2;">종료</th>
<th style="padding:0px; text-align:center; border-right:1px solid #d2d2d2;">시작</th>
<th style="padding:0px; text-align:center; border-right:1px solid #d2d2d2;">종료</th>
</tr>
<?
	$sql = "select m00_cname as mkindName"
		 . ",      m03_name as suName"
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
		 . "            when t01_sugup_fmtime < date_format(now(), '%H%i') and t01_status_gbn = '9' then '미수행'"
		 . "            else m81_name end as statGbnName"
		 . ",      m03_key"
		 . ",      t01_sugup_date"
		 . ",      t01_sugup_fmtime"
		 . ",      t01_sugup_seq"
		 . ",      t01_ccode"
		 . ",      t01_mkind"
		 . ",      t01_conf_soyotime"
		 . ",      m03_jumin as sugupJumin"
		 . ",      t01_ccode as centerCode"
		 . ",      t01_mkind as centerKind"
		 . ",      t01_yoyangsa_id1 as yoyJumin1"
		 . ",      t01_yoyangsa_id2 as yoyJumin2"
		 . ",      t01_yoyangsa_id3 as yoyJumin3"
		 . ",      t01_yoyangsa_id4 as yoyJumin4"
		 . ",      t01_yoyangsa_id5 as yoyJumin5"
		 . ",      t01_yname1 as yoyName1"
		 . ",      t01_yname2 as yoyName2"
		 . ",      t01_yname3 as yoyName3"
		 . ",      t01_yname4 as yoyName4"
		 . ",      t01_yname5 as yoyName5
			,      t01_modify_pos as modPos
			,      t01_modify_yn  as modYN"
		 . "  from t01iljung"
		 . " inner join m00center"
		 . "    on m00_mcode = t01_ccode"
		 . "   and m00_mkind = t01_mkind"
		 . " inner join m03sugupja"
		 . "    on m03_ccode = t01_ccode"
		 . "   and m03_mkind = t01_mkind"
		 . "   and m03_jumin = t01_jumin"
		 . "  left join m81gubun"
		 . "    on m81_gbn  = 'STA'"
		 . "   and m81_code = t01_status_gbn"
		 . " where t01_ccode = '".$mCode
		 . "'  and t01_del_yn = 'N'";

	if ($mKind != 'all'){
		$sql .= " and t01_mkind = '".$mKind."'";
	}

	if ($mStat != 'all'){
		$sql .= " and t01_status_gbn = '".$mStat."'";
	}

	$sql .= "   and t01_sugup_date = date_format(now(), '%Y%m%d')"
		 .  " order by t01_sugup_fmtime"
		 .  ",         t01_sugup_fmtime"
		 .  ",         t01_sugup_totime";

	$conn->query($sql);
	$conn->fetch();
	$row_count = $conn->row_count();

	$myKey = 'GoodEos';
	//$encrypted = encrypt_md5('1234567890', $key);
	//$decrypted = decrypt_md5($encrypted, $key);

	for($i=0; $i<$row_count; $i++){
		$row = $conn->select_row($i);

		if (strLen($row['wrkFmTime']) == 5 and strLen($row['wrkToTime']) == 5){
			$wrkSoyoTime = $row['wrkSoyoTime'].'분';
		}else{
			$wrkSoyoTime = '';
		}

		if (strLen($row['t01_conf_soyotime']) != 0){
			$confSoyoTime = $row['t01_conf_soyotime'].'분';
		}else{
			$confSoyoTime = '';
		}

		/*
		if ($row['wrkSoyoTime'] != '0'){
			$wrkSoyoTime = $row['wrkSoyoTime'];
		}else{
			$wrkSoyoTime = '';
		}
		*/

		$sql = "select m01_suga_cont"
			 . "  from m01suga"
			 . " where m01_mcode  = '".$mCode
			 . "'  and m01_mcode2 = '".$row['sugaCode']
			 . "'  and date_format(now(), '%Y%m%d') between m01_sdate and m01_edate";
		$con2->query($sql);
		$row2 = $con2->fetch();
		$sugaName = $row2[0];
		$con2->row_free();

		if ($sugaName == ''){
			$sql = "select m11_suga_cont"
				 . "  from m11suga"
				 . " where m11_mcode  = '".$mCode
				 . "'  and m11_mcode2 = '".$row['sugaCode']
				 . "'  and date_format(now(), '%Y%m%d') between m11_sdate and m11_edate";
			$con2->query($sql);
			$row2 = $con2->fetch();
			$sugaName = $row2[0];
			$con2->row_free();
		}

		echo '<tr>';

		if ($mKind == 'all'){
			if ($mkindName != $row['mkindName']){
				$mkindName  = $row['mkindName'];
				echo '<td style="width:10%; border-right:1px solid #d2d2d2; padding:0px; border-bottom:none;">'.left($row['mkindName'], 5).'</td>';
			}else{
				echo '<td style="width:10%; border-right:1px solid #d2d2d2; padding:0px; border-bottom:none; border-top:none;"></td>';
			}
		}

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
					$statImage = '<input name="btnSuccess[]" type="button" value="" onClick="popupWorkDetail(\''.$row['t01_ccode'].'\',\''.$row['t01_mkind'].'\',\''.$row['m03_key'].'\',\''.$row['t01_sugup_date'].'\',\''.$row['t01_sugup_fmtime'].'\',\''.$row['t01_sugup_seq'].'\');" style="width:44px; height:16px; border:0px; background:url(\'../image/icon_stat_1.png\') no-repeat; cursor:pointer;">';
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
			$tempSugupja = urlEncode($ed->encrypt_md5($row['sugupJumin'], $ed->myKey));
			$tempYoyangsa = urlEncode($ed->encrypt_md5($row['yoyJumin1'], $ed->myKey));

			if ($row['modYN'] == 'N'){
				$locationFind = '<a href="#" onClick="_locationFind(\''.$row['centerCode'].'\',\''.$row['centerKind'].'\',\''.$tempSugupja.'\',\''.$row['t01_sugup_date'].'\',\''.$row['t01_sugup_fmtime'].'\',\''.$row['t01_sugup_seq'].'\',\''.$tempYoyangsa.'\');"><img src="../image/btn_location_find.gif"></a>';
			}
		}

		if ($tempTime != subStr($row['suFmTime'], 0, 2)){
			$tempTime  = subStr($row['suFmTime'], 0, 2);
			$tempBorder = 'border-bottom:1px solid #46aaeb; border-top:0px solid #46aaeb;';
			echo '<td style="width:4%; border-right:1px solid #d2d2d2; padding:0px; text-align:center;'.$tempBorder.'">'.$tempTime.'</td>';
		}else{
			$tempBorder = 'border-bottom:1px solid #46aaeb; border-top:1px solid #d2d2d2;';
			echo '<td style="width:4%; border-right:1px solid #d2d2d2; padding:0px; text-align:center;'.$tempBorder.'"></td>';
		}

		$yoyangsa = $row['yoyName1'];

		if ($row['yoyName2'] != ''){
			$yoyangsa .= '/'.$row['yoyName2'];
		}

		echo '<td style="width:5%; padding:0px; border-right:1px solid #d2d2d2; text-align:center;'.$tempBorder.'">'.$row['suFmTime'].'</td>';
		echo '<td style="width:5%; padding:0px; border-right:1px solid #d2d2d2; text-align:center;'.$tempBorder.'">'.$row['suToTime'].'</td>';
		echo '<td style="width:8%; padding:0px; border-right:1px solid #d2d2d2; text-align:center;  '.$tempBorder.'">'.$row['suName'].'</td>';
		echo '<td style="width:;   padding:0px; border-right:1px solid #d2d2d2; text-align:center;  '.$tempBorder.'">'.$sugaName.'</td>';
		echo '<td style="width:;   padding:0px; border-right:1px solid #d2d2d2; text-align:center;  '.$tempBorder.'">'.$yoyangsa.'</td>';
		echo '<td style="width:5%; padding:0px; border-right:1px solid #d2d2d2; text-align:center;'.$tempBorder.'">'.($row['wrkFmTime'] != ':' ? $row['wrkFmTime'] : '').'</td>';
		echo '<td style="width:5%; padding:0px; border-right:1px solid #d2d2d2; text-align:center;'.$tempBorder.'">'.($row['wrkToTime'] != ':' ? $row['wrkToTime'] : '').'</td>';
		echo '<td style="width:8%; padding:0px; border-right:1px solid #d2d2d2; text-align:center;'.$tempBorder.'">'.$wrkSoyoTime.'</td>';
		echo '<td style="width:8%; padding:0px; border-right:1px solid #d2d2d2; text-align:center;'.$tempBorder.'">'.$confSoyoTime.'</td>';
		echo '<td style="width:8%; padding:0px; border-right:1px solid #d2d2d2; text-align:center;'.$tempBorder.'">'.$statImage.'</td>';
		echo '<td style="width:8%; padding:0px; border-right:1px solid #d2d2d2; text-align:center;'.$tempBorder.'">'.$locationFind.'</td>';
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