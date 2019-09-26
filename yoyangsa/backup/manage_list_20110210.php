<?
	include("../inc/_header.php");
	include("../inc/_http_referer.php");
	include("../inc/_myFun.php");
	include("../inc/_ed.php");


	$menu = $_POST['mIndex'];
	$tab  = $_POST['mTab'];
	$mCode = $_POST["mCode"]; //기관코드
	$mKind = $_POST["mKind"]; //기관분류코드
	$mEmploy = $_POST["mEmploy"]; //고용상태
	$mYoy = $_POST['mYoy'];
?>
<style>
.view_type1 tr th{
	font-size:8pt;
	padding:0;
	text-align:center;
	line-height:1.3em;
	border-right:1px solid #ccc;
}
</style>
<!--<a href="javascript:__modal(Array('report','input','47','php'));">test</a>-->
<table class="view_type1" style="width:100%; margin:0;">
<colGroup>
	<col width="10%">
	<col width="7%">
	<col width="6%">
	<col width="8%">
	<col width="8%">
	<col width="8%">
	<col width="9%">
	<col width="6%">
	<col width="7%">
	<col width="7%">
	<col width="9%">
	<col width="7%">
</colGroup>
<tr>
	<th rowspan="2">요양보호사</th>
	<th rowspan="2">주민번호</th>
	<th rowspan="2">근로<br>계약서</th>
	<th rowspan="2">개인정보<br>보호동의서</th>
	<th rowspan="2">익월서비스<br>일정표</th>
	<th rowspan="2">상담일지<br>(격월주기)</th>
	<th rowspan="2">직무평가및<br>만족도조사<br>(격월주기)</th>
	<th colspan="4">교육</th>
	<th style="border-right:0;" rowspan="2">건강검진<br>(년주기)</th>
</tr>
<tr>
	<th>신규</th>
	<th>급여제공</th>
	<th>업무범위</th>
	<th>개인정보보호</th>
</tr>
<?
	$YMD = date("Y-m-d", mkTime());

	$sql = "select m02_yname as name
			,      m02_yjumin as jumin
			,      m02_ygoyong_kind
			,      m02_key
			,      m02_ygoyong_stat
			  from m02yoyangsa
			 where m02_ccode = '$mCode'
			   and m02_mkind = '$mKind'";

	if ($mEmploy != ""){
		$sql .= " and  m02_ygoyong_stat = '$mEmploy'";
	}
	$sql .= " order by m02_yname";

	$conn->query($sql);
	$conn->fetch();
	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		$name = $myF->splits($row["name"], 3);
		$namtTitle = $row["name"];
		$jumin = subStr($row["jumin"], 0, 6)."-".subStr($row["jumin"], 6, 1);

		//익월일정확인
		$juminNo = $row["jumin"];

		//$beforeYM = date("Ym", mkTime());
		$beforeYM = date("Ym",strtotime("+1 month"));

		$sql = "select count(*)
				  from t01iljung
				 where t01_ccode = '$mCode'
				   and t01_mkind = '$mKind'
				   and t01_sugup_date like '$beforeYM%'
				   and '$juminNo' in (t01_yoyangsa_id1, t01_yoyangsa_id2, t01_yoyangsa_id3, t01_yoyangsa_id4, t01_yoyangsa_id5)
				   and t01_del_yn = 'N'";
		$workCount = $conn->get_data($sql);

		//상담일지
		$sql = "select r260_date"
			 . "  from r260talk"
			 . " where r260_ccode = '".$mCode
			 . "'  and r260_mkind = '".$mKind
			 . "'  and r260_yoyangsa = '".$row["jumin"]
			 . "'";
		$talk = $conn->get_array($sql);

		$date1 = $myF->dateAdd('month', 2, $talk['r260_date'], 'y-m-d');
		//$date1 = date("Y-m-d",strtotime("+2 month, $date"));
		$date2 = $myF->dateDiff('d', $date1, $YMD);

		//직무평가 및 만족도조사
		$sql = "select r270_date"
			 . "  from r270test"
			 . " where r270_ccode = '".$mCode
			 . "'  and r270_mkind = '".$mKind
			 . "' and r270_yoy_code = '".$row["jumin"]
			 . "'";
		$test = $conn->get_array($sql);



		$date3 = $myF->dateAdd('month', 2, $test['r270_date'], 'y-m-d');
		$date4 = $myF->dateDiff('d', $date3, $YMD);


		echo "
			<tr>
				<td title='$namtTitle'>$name</td>
				<td>$jumin</td>
				";
		if($row['m02_ygoyong_kind'] == "1"){

			//근로계약서 출력
			echo "<td style='padding:0; margin:0; text-align:left; vertical-align:top; padding-top:2px;'><div style='position:absolute; width:100%; text-align:center; margin:0; padding:0;'><span class='btn_pack m'><button type='button' onFocus='this.blur();' onClick=\"window.open('../yoyangsa/Report_show_92.php?mCode=$mCode&mKind=$mKind&key=".$row['m02_key']."', 'POPUP', 'left='+((window.screen.width - 1000) / 2)+', top='+((window.screen.height - 600) / 2)+', width=700, height=900, toolbar=no, location=no, status=no, menubar=no, scrollbars=yes, resizable=no');\">출력</button></span></div></td>";
		}else if ($row['m02_ygoyong_kind'] == "2"){
			echo "<td style='padding:0; margin:0; text-align:left; vertical-align:top; padding-top:2px;'><div style='position:absolute; width:100%; text-align:center; margin:0; padding:0;'><span class='btn_pack m'><button type='button' onFocus='this.blur();' onClick=\"window.open('../yoyangsa/Report_show_91.php?mCode=$mCode&mKind=$mKind&key=".$row['m02_key']."', 'POPUP', 'left='+((window.screen.width - 1000) / 2)+', top='+((window.screen.height - 600) / 2)+', width=700, height=900, toolbar=no, location=no, status=no, menubar=no, scrollbars=yes, resizable=no');\">출력</button></span></div></td>";
		}else {
			echo "<td style='padding:0; margin:0; text-align:left; vertical-align:top; padding-top:2px;'><div style='position:absolute; width:100%; text-align:center; margin:0; padding:0;'><span class='btn_pack m'><button type='button' onFocus='this.blur();' onClick=\"window.open('../yoyangsa/Report_show_90.php?mCode=$mCode&mKind=$mKind&key=".$row['m02_key']."', 'POPUP', 'left='+((window.screen.width - 1000) / 2)+', top='+((window.screen.height - 600) / 2)+', width=700, height=900, toolbar=no, location=no, status=no, menubar=no, scrollbars=yes, resizable=no');\">출력</button></span></div></td>";
		}

		//
		echo "<td style='padding:0; margin:0; text-align:left; vertical-align:top; padding-top:2px;'><div style='position:absolute; width:100%; text-align:center; margin:0; padding:0;'><span class='btn_pack y'><button type='button' onFocus='this.blur();' onClick='';> </button></span></div></td>";

			//익월서비스일정표
		if ($workCount > 0){
			echo "<td style='padding:0; margin:0; text-align:left; vertical-align:top; padding-top:2px;'><div style='position:absolute; width:100%; text-align:center; margin:0; padding:0;'><span class='btn_pack m'><button style='width:52px;' type='button' onFocus='this.blur();' onClick=\"window.open('../work/cal_show.php?code=$mCode&kind=$mKind&year=".subStr($beforeYM, 0, 4)."&month=".subStr($beforeYM, 4, 2)."&sugupja=".$ed->en($row["jumin"])."&type=y', 'POPUP', 'left='+((window.screen.width - 1000) / 2)+', top='+((window.screen.height - 600) / 2)+', width=1000, height=600, toolbar=no, location=no, status=no, menubar=no, scrollbars=yes, resizable=no');\">보기</button></span></td>";
		}else{
			echo "<td style='padding:0; margin:0; text-align:left; vertical-align:top; padding-top:2px;'><div style='position:absolute; width:100%; text-align:center; margin:0; padding:0;'><span class='btn_pack y'><button type='button' onFocus='this.blur();' onClick='location.href=\"../iljung/iljung.php?menuIndex=work&menuSeq=1&gubun=reg\";'>미등록</button></span></td>";
		}

			//상담일지(격월주기)
		if($row["m02_ygoyong_stat"] == "1"){
			if($date2 > 0 or $talk['r260_date'] == ''){
				?>
				<td style='padding:0; margin:0; text-align:left; vertical-align:top; padding-top:2px;'><div style='position:absolute; width:100%; text-align:center; margin:0; padding:0;'><span id="listPopup" class='btn_pack y'><button style="width:52px;" type='button' onFocus='this.blur();' onclick="_showPopup(this,'1','<?=$mCode?>','<?=$mKind?>','<?=$ed->en($row["jumin"]);?>');">미작성</button></span></td>
				<?
				/*
				echo "<td style='padding:0; margin:0; text-align:left; vertical-align:top; padding-top:2px;'><div style='position:absolute; width:100%; text-align:center; margin:0; padding:0;'><span class='btn_pack y'><button type='button' onFocus='this.blur();' onClick=\"__modal(Array('".$mKind."','".$ed->en($row["jumin"])."','report','input','47','php','1','2'));\">미작성</button></span></div></td>";
				*/
			}else {
			?>
				<td style='padding:0; margin:0; text-align:left; vertical-align:top; padding-top:2px;'><div style='position:absolute; width:100%; text-align:center; margin:0; padding:0;'><span id="listPopup" class='btn_pack m'><button style="width:52px;" type='button' onFocus='this.blur();' onclick="_showPopup(this,'1','<?=$mCode?>','<?=$mKind?>','<?=$ed->en($row["jumin"]);?>');">보기</button></span></td>
			<?
			}

			}else {
				?>
					<td style='padding:0; margin:0; text-align:left; vertical-align:top; padding-top:2px;'><div style='position:absolute; width:100%; text-align:center; margin:0; padding:0;'><span class='btn_pack y'><button type='button' onFocus='this.blur();' onClick='stat_chk();'>미활동</button></span></td>
				<?
			}

			//직무평가 및 만족도조사
		if($row["m02_ygoyong_stat"] == "1"){
			if($date4 > 0 or $test['r270_date'] == ''){
				/*
				echo "<td style='padding:0; margin:0; text-align:left; vertical-align:top; padding-top:2px;'><div style='position:absolute; width:100%; text-align:center; margin:0; padding:0;'><span class='btn_pack y'><button type='button' onFocus='this.blur();' onClick=\"__modal(Array('".$mKind."','".$ed->en($row["jumin"])."','report','input','33','php','1','2'));\">미작성</button></span></div></td>";
				*/
				?>
				<td style='padding:0; margin:0; text-align:left; vertical-align:top; padding-top:2px;'><div style='position:absolute; width:100%; text-align:center; margin:0; padding:0;'><span id="listPopup2" class='btn_pack y'><button style="width:52px;" type='button' onFocus='this.blur();' onclick="_showPopup(this,'2','<?=$mCode?>','<?=$mKind?>','<?=$ed->en($row["jumin"]);?>');">미작성</button></span></td>
				<!--
					<td style='padding:0; margin:0; text-align:left; vertical-align:top; padding-top:2px;'><div style='position:absolute; width:100%; text-align:center; margin:0; padding:0;'><span class='btn_pack y'><button type='button' onFocus='this.blur();' onClick='location.href="../reportMenu/menu.php?menuIndex=12&menuSeq=1&mIndex=33_1&mTab=2&mMenu=1&mCode=<?=$mCode;?>&mKind=<?=$mKind; ?>&mYoyangsa=<?=$ed->en($row["jumin"]);?>";'>미작성</button></span></td>
				-->
			<?
			}else {
			?>
				<td style='padding:0; margin:0; text-align:left; vertical-align:top; padding-top:2px;'><div style='position:absolute; width:100%; text-align:center; margin:0; padding:0;'><span id="listPopup2" class='btn_pack m'><button style="width:52px;" type='button' onFocus='this.blur();' onclick="_showPopup(this,'2','<?=$mCode?>','<?=$mKind?>','<?=$ed->en($row["jumin"]);?>');">보기</button></span></td>
			<?
				/*
				echo "<td style='padding:0; margin:0; text-align:left; vertical-align:top; padding-top:2px;'><div style='position:absolute; width:100%; text-align:center; margin:0; padding:0;'><span class='btn_pack m'><button type='button' onFocus='this.blur();' onClick=\"window.open('../report/report_show_33.php?mCode=$mCode&mKind=$mKind&mDate=".$test['r270_date']."&mYoyKey=".$ed->en($row["jumin"])."', 'POPUP', 'left='+((window.screen.width - 1000) / 2)+', top='+((window.screen.height - 600) / 2)+', width=700, height=900, toolbar=no, location=no, status=no, menubar=no, scrollbars=yes, resizable=no');\">보기</button></span></td>";
				*/
			}
		}else {
		?>
				<td style='padding:0; margin:0; text-align:left; vertical-align:top; padding-top:2px;'><div style='position:absolute; width:100%; text-align:center; margin:0; padding:0;'><span class='btn_pack y'><button type='button' onFocus='this.blur();' onClick='stat_chk();'>미활동</button></span></td>
		<?
		}

		echo "

				<td style='padding:0; margin:0; text-align:left; vertical-align:top; padding-top:2px;'><div style='position:absolute; width:100%; text-align:center; margin:0; padding:0;'><span class='btn_pack y'><button type='button' onFocus='this.blur();' onClick=''> </button></span></div></td>
				<td style='padding:0; margin:0; text-align:left; vertical-align:top; padding-top:2px;'><div style='position:absolute; width:100%; text-align:center; margin:0; padding:0;'><span class='btn_pack y'><button type='button' onFocus='this.blur();' onClick=''> </button></span></div></td>
				<td style='padding:0; margin:0; text-align:left; vertical-align:top; padding-top:2px;'><div style='position:absolute; width:100%; text-align:center; margin:0; padding:0;'><span class='btn_pack y'><button type='button' onFocus='this.blur();' onClick=''> </button></span></div></td>
				<td style='padding:0; margin:0; text-align:left; vertical-align:top; padding-top:2px;'><div style='position:absolute; width:100%; text-align:center; margin:0; padding:0;'><span class='btn_pack y'><button type='button' onFocus='this.blur();' onClick=''> </button></span></div></td>
				<td style='padding:0; margin:0; text-align:left; vertical-align:top; padding-top:2px;'><div style='position:absolute; width:100%; text-align:center; margin:0; padding:0;'><span class='btn_pack y'><button type='button' onFocus='this.blur();' onClick=''> </button></span></div></td>
			</tr>
			 ";
	}

	$conn->row_free();
?>
</table>

<div id="idTalkPopup" style="z-index:11; left:0; top:0; width:370px; position:absolute; color:#000000; display:none;">
</div>
<!--
<div id="idTestPopup" style="z-index:11; left:0; top:0; width:450px; position:absolute; color:#000000; display:none;">
</div>
-->