<?
	include_once("../inc/_header.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");

	$code = $_POST['mCode'];
	$kind = $_POST['mKind'];
	$status = $_POST['mStat'];		//이용상태
	$su_name = $_POST['mSuName'];	//고객명
	$svc_kind     = $_POST['mSvc'] != '' ? $_POST['mSvc'] : 'all';	//서비스명
	$mode		  = $_POST['mode']; //엑셀모드유무

	$year = date('Y', mkTime());
	$month = date('m', mkTime());

?>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="60px">
		<col width="60px">
		<col width="70px">
		<col>
		<col width="90px">
		<col width="60px">
		<col width="60px">
		<col width="60px">
		<col width="60px">
		<col width="70px">
		<col width="70px">
	</colgroup>
	<thead>
		<tr>
			<th class="head" rowspan="2">시간</th>
			<th class="head" colspan="2">계획시간</th>
			<th class="head" rowspan="2">수급자<br>성명</th>
			<th class="head" rowspan="2">서비스명</th>
			<th class="head" rowspan="2" title="요양보호사 성명을 클릭하면 월별 진행일정을 볼 수 있습니다."><u>요양보호사<br>성명</u></th>
			<th class="head" colspan="2">수행시간</th>
			<th class="head" rowspan="2">실소요<br>시간(분)</th>
			<th class="head" rowspan="2">인정<br>시간</th>
			<th class="head" rowspan="2" title="녹색완료는 스마트폰으로 정상완료를 한 경우이며, 클릭시 상세서비스항목이 표시됩니다. 적색완료는 수동으로 입력하여 완료한 경우 입니다."><u>진행<br>상태</u></th>
			<th class="head last" rowspan="2">위치<br>정보</th>
		</tr>
		<tr>
			<th class="head">시작</th>
			<th class="head">종료</th>
			<th class="head">시작</th>
			<th class="head">종료</th>
		</tr>
	</thead>
	<tbody>
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
			 . ",      t01_conf_soyotime"
			 . ",      m03_jumin as sugupJumin"
			 . ",      t01_ccode as centerCode"
			 . ",      t01_mkind as centerKind"
			 . ",      t01_mem_cd1 as yoyJumin1"
			 . ",      t01_mem_cd2 as yoyJumin2"
			 . ",      t01_yname1 as yoyName1"
			 . ",      t01_yname2 as yoyName2"
			 . ",      t01_modify_pos as modPos
				,      t01_modify_yn  as modYN"
			 . "  from t01iljung"
			 . "  left join m00center"
			 . "    on m00_mcode = t01_ccode"
			 . "   and m00_mkind = t01_mkind"
			 . " inner join m03sugupja"
			 . "    on m03_ccode = t01_ccode"
			 . "   and m03_mkind = t01_mkind"
			 . "   and m03_jumin = t01_jumin"
			 . "  left join m81gubun"
			 . "    on m81_gbn  = 'STA'"
			 . "   and m81_code = t01_status_gbn"
			 . " where t01_ccode = '".$code
			 . "'  and t01_del_yn = 'N'";
		
		if ($su_name != '') $sql .= " and m03_name like '%$su_name%'";	#이름검색
		
		if ($svc_kind != 'all') $sql .= " and m03_mkind = '$svc_kind'"; #서비스검색

		if ($status != 'all'){
			if ($status == '9' || $status == '0'){
				$sql .= " and case when t01_status_gbn = '9' and concat(t01_sugup_date, t01_sugup_fmtime) <= date_format(now(), '%Y%m%d%H%i') then '0' else t01_status_gbn end = '".$status."'";
			}else{
				$sql .= " and t01_status_gbn = '".$status."'";
			}
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

			$sugaName  = $conn->get_suga($code, $row['sugaCode'], $row['t01_sugup_date']); //수가명

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
						$statImage = '<input name="btnSuccess[]" type="button" value="" onClick="popupWorkDetail(\''.$row['t01_ccode'].'\',\''.$row['centerKind'].'\',\''.$row['m03_key'].'\',\''.$row['t01_sugup_date'].'\',\''.$row['t01_sugup_fmtime'].'\',\''.$row['t01_sugup_seq'].'\');" style="width:44px; height:16px; border:0px; background:url(\'../image/icon_stat_1.png\') no-repeat; cursor:pointer;">';
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
				$tempSugupja  = urlEncode($ed->encrypt_md5($row['sugupJumin'], $ed->myKey));
				$tempYoyangsa = urlEncode($ed->encrypt_md5($row['yoyJumin1'],  $ed->myKey));

				if ($row['modYN'] == 'N'){
					$locationFind = '<a href="#" onClick="_locationFind(\''.$row['centerCode'].'\',\''.$row['centerKind'].'\',\''.$tempSugupja.'\',\''.$row['t01_sugup_date'].'\',\''.$row['t01_sugup_fmtime'].'\',\''.$row['t01_sugup_seq'].'\',\''.$tempYoyangsa.'\');"><img src="../image/btn_location_find.gif"></a>';
				}
			}

			if ($tempTime != subStr($row['suFmTime'], 0, 2)){
				$tempTime  = subStr($row['suFmTime'], 0, 2);
				$str_time  = $tempTime;
				$top_cls= 'border-top:1px solid #cccccc;';
			}else{
				$str_time  = '';
				$top_cls= '';
			}

			$yoyangsa = '<a href="#" onclick="member_detail(\''.$mYear.'\',\''.$mMonth.'\',\''.$ed->en($row['yoyJumin1']).'\',\''.$row['yoyName1'].'\');">'.$row['yoyName1'].'</a>';

			if ($row['yoyName2'] != ''){
				$yoyangsa .= '/<a href="#" onclick="member_detail(\''.$mYear.'\',\''.$mMonth.'\',\''.$ed->en($row['yoyJumin2']).'\',\''.$row['yoyName2'].'\');">'.$row['yoyName2'].'</a>';
			}?>
			<tr>
				<td class="center <? if($i + 1 != $row_count){?>bottom<?} ?>" style="<?=$top_cls;?>"><?=$str_time;?></td>
				<td class="center"		><?=$row['suFmTime'];?></td>
				<td class="center"		><?=$row['suToTime'];?></td>
				<td class="left"		><?=$row['suName'];?></td>
				<td class="left"		><?=$sugaName;?></td>
				<td class="left"		><?=$yoyangsa;?></td>
				<td class="left"		><?=($row['wrkFmTime'] != ':' ? $row['wrkFmTime'] : '');?></td>
				<td class="center"		><?=($row['wrkToTime'] != ':' ? $row['wrkToTime'] : '');?></td>
				<td class="center"		><?=$wrkSoyoTime;?></td>
				<td class="center"		><?=$confSoyoTime;?></td>
				<td class="center"		><?=$statImage;?></td>
				<td class="center last"	><?=$locationFind;?></td>
			</tr>
			<?
		}

		$conn->row_free();
	?>
	</tbody>
	<tbody>
		<tr>
		<?
			if ($row_count > 0){?>
				<td class="left last bottom" colspan="12"><?=$myF->message($row_count, 'N');?></td><?
			}else{?>
				<td class="center last bottom" colspan="12"><?=$myF->message('nodata', 'N');?></td><?
			}
		?>
		</tr>
	</tbody>
</table><? 

if($mode != '2'){ ?>
	<input type="hidden" name="mode"		value="2">
	<input type="hidden" name="back_mode"	value="day">
	<input type="hidden" name="year"		value="">
	<input type="hidden" name="month"		value="">
	<input type="hidden" name="jumin"		value="">
	<input type="hidden" name="member"		value="">

<?
	include_once("../inc/_footer.php");

}
?>