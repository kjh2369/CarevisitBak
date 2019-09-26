<?
	include("../inc/_header.php");
	include("../inc/_http_referer.php");
	include("../inc/_myFun.php");
	include("../inc/_ed.php");

	$menu	 = $_POST['mIndex'];
	$tab	 = $_POST['mTab'];
	$mCode	 = $_POST['mCode']; // 기관코드
	$mKind	 = $_POST['mKind']; // 기관분류코드
	$mEmploy = $_POST['mEmploy']; // 고용상태
	$mSuKey = $_POST['mSuKey'];


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
<table class="my_table my_border" style="margin-top:-1px;">
<colGroup>
	<col width="100px">
	<col width="90px">
	<col width="100px">
	<col width="120px">
	<col width="120px">
	<col width="120px">
	<col width="110px">
	<col width="100px">
	<col width="110px">
	<col width="60px">
	<col width="110px">
	<col width="110px">
	<col width="110px">
	<col>
</colGroup>
<tr>
	<th class="head" rowspan="2">수급자성명</th>
	<th class="head" rowspan="2">서비스<br>이용<br>계약서</th>
	<th class="head" rowspan="2">초기상담<br>기록지<br>(개시전)</th>
	<th class="head" colspan="3">평가</th>
	<th class="head" rowspan="2">익월<br>서비스<br>일정표</th>
	<th class="head" rowspan="2">전월<br>본인부담<br>영수증발행</th>
	<th class="head" rowspan="2">당월실적<br>확정처리</th>
	<th class="head" rowspan="2">미수금<br>현황</th>
	<th class="head" colspan="3">만족도조사 (분기별)</th>
	<th class="last head"rowspan="2">비고</th>
</tr>
<tr>
	<th class="head">욕구</th>
	<th class="head">욕창위험도</th>
	<th class="head">낙상위험도</th>
	<th class="head">방문요양</th>
	<th class="head">방문목욕</th>
	<th class="head">방문간호</th>
</tr>

<?
	$sql = "select m03_name as name
			,	   m03_jumin as jumin
			,	   m03_skind
			,	   m03_key
			,	   m03_sugup_status
			,      m03_bonin_yul
			  from m03sugupja
			 where m03_ccode = '$mCode'
			   and m03_mkind = '$mKind'";


	if ($mEmploy != ""){
		$sql .= " and m03_sugup_status = '$mEmploy'";
	}
	$sql .= " order by m03_name";

	$conn->query($sql);
	$conn->fetch();
	$rowCount = $conn->row_count();



	ob_start();

	if ($rowCount > 0){
	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		$name = $myF->splits($row["name"], 3);
		$namtTitle = $row["name"];
		$jumin = subStr($row["jumin"], 0, 6)."-".subStr($row["jumin"], 6, 1)."******";


		echo "
			<tr>
				<td class='left' title='$namtTitle'>$name</td>
				";
		// 급여 계약서

			echo "<td style='padding:0; margin:0; text-align:center; padding-top:2px;'><a href='#' onFocus='this.blur();' onClick=\"window.open('../report/report_show_70.php?mCode=$mCode&mKind=$mKind&key=". $row['m03_key']."', 'POPUP', 'left='+((window.screen.width - 1000) / 2)+', top='+((window.screen.height - 600) / 2)+', width=700, height=900, toolbar=no, location=no, status=no, menubar=no, scrollbars=yes, resizable=no');return false;\">출력</a></td>";

		// 초기상담 기록지
		$sql = "select r200_sugup_name"
			 . "  from r200fsttalk"
			 . " where r200_ccode = '".$mCode
			 . "'  and r200_mkind = '".$mKind
			 . "'  and r200_jumin like '".$row["jumin"]
			 . "%'";

		$fsttalk = $conn->get_array($sql);

		if($fsttalk['r200_sugup_name'] == ''){
			?>
				<td class='center'><a type='button' onFocus='this.blur();' onclick="_showPopup(this,'4','<?=$mCode?>','<?=$mKind?>','<?=$ed->en($row["jumin"]);?>');return false;">미작성</a></td>
			<?
		}else{
				?>
				<td class='center'><a type='button' onFocus='this.blur();' onclick="_showPopup(this,'4','<?=$mCode?>','<?=$mKind?>','<?=$ed->en($row["jumin"]);?>');return false;">작성완료</a></td>
				<?
		}

		// 욕구평가 기록지

		$sql = "select r210_sugup_name"
			 . "  from r210nar"
			 . " where r210_ccode = '".$mCode
			 . "'  and r210_mkind = '".$mKind
			 . "'  and r210_sugup_code like '".$row["jumin"]
			 . "%'";

		$nar = $conn->get_array($sql);

		if($nar['r210_sugup_name'] == ''){
			?>
				<td class='center'><a type='button' onFocus='this.blur();' onclick="_showPopup(this,'1','<?=$mCode?>','<?=$mKind?>','<?=$ed->en($row["jumin"]);?>');return false;">미작성</a></td>
			<?
		}else{
				?>
				<td class='center'><a type='button' onFocus='this.blur();' onclick="_showPopup(this,'1','<?=$mCode?>','<?=$mKind?>','<?=$ed->en($row["jumin"]);?>');return false;">작성완료</a></td>
				<?
		}

		// 욕창위험도

		$sql = "select r220_sugupName"
			 . "  from r220purat"
			 . " where r220_ccode = '".$mCode
			 . "'  and r220_mkind = '".$mKind
			 . "'  and r220_sugupCode like '".$row["jumin"]
			 . "%'";

		$purat = $conn->get_array($sql);

		if($purat['r220_sugupName'] == ''){
			?>
				<td class='center'><a type='button' onFocus='this.blur();' onclick="_showPopup(this,'2','<?=$mCode?>','<?=$mKind?>','<?=$ed->en($row["jumin"]);?>');return false;">미작성</a></td>
			<?
		}else{
				?>
				<td class='center'><a type='button' onFocus='this.blur();' onclick="_showPopup(this,'2','<?=$mCode?>','<?=$mKind?>','<?=$ed->en($row["jumin"]);?>');return false;">작성완료</a></td>
				<?
		}


		// 낙상위험도

		$sql = "select r250_sugupja_name"
			 . "  from r250risktoll"
			 . " where r250_ccode = '".$mCode
			 . "'  and r250_mkind = '".$mKind
			 . "'  and r250_sugupja_jumin like '".$row["jumin"]
			 . "%'";

		$risktoll = $conn->get_array($sql);


		if($risktoll['r250_sugupja_name'] == ''){
			?>
				<td class='center'><a type='button' onFocus='this.blur();' onclick="_showPopup(this,'3','<?=$mCode?>','<?=$mKind?>','<?=$ed->en($row["jumin"]);?>');return false;">미작성</a></td>
			<?
		}else{
				?>
				<td class='center'><a type='button' onFocus='this.blur();' onclick="_showPopup(this,'3','<?=$mCode?>','<?=$mKind?>','<?=$ed->en($row["jumin"]);?>');return false;">작성완료</a></td>
				<?
		}


		// 익월 서비스 일정표
		$juminNo = $row['jumin'];
		$beforeYM = date("Ym",strtotime("+1 month"));

		$sql = "select count(t01_jumin)
				  from t01iljung
				 where t01_ccode = '$mCode'
				   and t01_mkind = '$mKind'
				   and t01_sugup_date like '$beforeYM%'
				   and '$juminNo' in (t01_jumin)
				   and t01_del_yn = 'N'";
		$workCount = $conn->get_data($sql);

			if ($workCount > 0){
				echo "<td class='center'><a href='#' onFocus='this.blur();' onClick=\"serviceCalendarShow('".$mCode."', '".$mKind."', '".subStr($beforeYM, 0, 4)."', '".subStr($beforeYM, 4, 2)."', '".$ed->en($row["jumin"])."', 's', 'n', 'pdf', 'y'); return false;\">작성완료</a></td>";
			}else{
				echo "<td class='center'><a href='#' onFocus='this.blur();' onClick=\"window.open('../iljung/iljung.php?menuIndex=work&menuSeq=1&manager=true&gubun=reg', 'POPUP', 'left='+((window.screen.width - 1000) / 2)+', top='+((window.screen.height - 600) / 2)+', width=1000, height=600, toolbar=no, location=no, status=no, menubar=no, scrollbars=yes, resizable=no');return false;\">미작성</td>";
			}

		// 전월 본인부담 영수증 발행

		$mYM = date("Ym",strtotime("-1 month"));

		$sql = "select t13_bonin_yul
				  from t13sugupja
		    inner join m03sugupja
				    on m03_ccode = t13_ccode
				   and m03_mkind = t13_mkind
				   and m03_jumin = t13_jumin
			inner join m81gubun as LVL
				    on m81_gbn  = 'LVL'
				   and m81_code = m03_ylvl
				 where t13_ccode = '$mCode'
				   and t13_mkind = '$mKind'
				   and t13_pay_date = '$mYM'
				   and t13_jumin = '$row[jumin]'
				   and t13_type = '2'";
		$bonin = $conn->get_array($sql);


			if($bonin['t13_bonin_yul'] > 0){
				echo'<td class="center"><a type="button" onFocus="this.blur();" onClick="_printPaymentsBill(\''.$mCode.'\', \''.$mKind.'\', \''.$mYM.'\', \''.$bonin['t13_bonin_yul'].'\', \''.$row['m03_key'].'\');" value="" style="width:67px; height:18px; border:0px; no-repeat; cursor:pointer;">발행완료</a></td>';
			}else {
				echo"<td class='center'><a type='button' onFocus='this.blur();' onClick=''>-</a></td>";
			}

		// 당월 실적 확정처리
		$YM = date("Ym",mktime());

		$sql = "select count(*)
				  from t13sugupja
				 where t13_ccode = '$mCode'
				   and t13_mkind = '$mKind'
				   and t13_pay_date like '$YM%'
				   and '$juminNo' in (t13_jumin)
				   and t13_type = '2'";
		$confFlag = $conn->get_data($sql);

		/*
		// 입금여부
		$sql = "select count(*)
				  from t14deposit
				 where t14_ccode = '$mCode'
				   and t14_mkind = '$mKind'
				   and '$juminNo' in (t14_jumin)
				   and t14_pay_date like '$YM%'
				   ''";
		$depositFlag = $conn->get_data($sql);
		*/

			/*if ($confFlag == 0){
				echo "<td class='center'><a href='#' onFocus='this.blur();' onClick=\"window.open('../work/month_conf_sugupja.php?curYear=".subStr($YM, 0, 4)."&curMonth=".subStr($YM, 4, 2)."&curMcode=$mCode&curMkind=$mKind&curSugupja=".$ed->en($row["jumin"])."&manager=true', 'POPUP', 'left='+((window.screen.width - 1000) / 2)+', top='+((window.screen.height - 600) / 2)+', width=1000, height=600, toolbar=no, location=no, status=no, menubar=no, scrollbars=yes, resizable=no');return false;\">미확정</a></td>";
			}else{
				echo "<td class='center'><a href='#' onFocus='this.blur();' onClick=\"window.open('../work/month_conf_sugupja.php?curYear=".subStr($YM, 0, 4)."&curMonth=".subStr($YM, 4, 2)."&curMcode=$mCode&curMkind=$mKind&curSugupja=".$ed->en($row["jumin"])."&manager=true', 'POPUP', 'left='+((window.screen.width - 1000) / 2)+', top='+((window.screen.height - 600) / 2)+', width=1000, height=600, toolbar=no, location=no, status=no, menubar=no, scrollbars=yes, resizable=no');return false;\">확정</a></td>";
			}*/
			echo "<td style='padding:0; margin:0; text-align:center; padding-top:2px;'><a href='#' onFocus='this.blur();' onClick='stat_chk();return false;'>-</a></td>";

		// 미수금현황

		$sql = "select sum(t13_misu_amt - t13_misu_inamt) as misuAmt
			  from t13sugupja
			 inner join m03sugupja
			    on m03_ccode = t13_ccode
			   and m03_mkind = t13_mkind
			   and m03_jumin = t13_jumin
			 inner join m81gubun as LVL
			    on m81_gbn  = 'LVL'
			   and m81_code = m03_ylvl
			 where t13_ccode = '$mCode'
			   and t13_mkind = '$mKind'
			   and t13_jumin = '$row[jumin]'
			   and t13_type = '2'";
		$amt = $conn->get_array($sql);

			if($amt['misuAmt'] > 0){

				echo'<td class="center"><a type="button" onFocus="this.blur();" onClick="popupDeposit(document.f.mCode.value, document.f.mKind.value, \''.$row['m03_key'].'\');" value="" style="width:67px; height:18px; border:0px; no-repeat; cursor:pointer;">미수</a></td>';
			}else {
				echo"<td class='center'><a type='button' onFocus='this.blur();' onClick=''>-</a></td>";
			}

		$sql = "select r360_service_gbn"
			 . ",      r360_sugupja_name"
			 . "  from r360quest"
			 . " where r360_ccode = '".$mCode
			 . "'  and r360_mkind = '".$mKind
		     . "'  and r360_sugupja = '".$row['jumin']
		     . "'  and r360_service_gbn = '200'";
		$quest200 = $conn->get_array($sql);



			if($quest200['r360_sugupja_name'] == ''){
				?>
				<td class='center'><a type='button' onFocus='this.blur();' onclick="_showPopup(this,'5','<?=$mCode?>','<?=$mKind?>','<?=$ed->en($row["jumin"]);?>');return false;">미작성</a></td>
				<?
			}else{
				?>
				<td class='center'><a type='button' onFocus='this.blur();' onclick="_showPopup(this,'5','<?=$mCode?>','<?=$mKind?>','<?=$ed->en($row["jumin"]);?>');return false;">작성완료</a></td>
				<?
			}
		$sql = "select r360_service_gbn"
			 . ",      r360_sugupja_name"
			 . "  from r360quest"
			 . " where r360_ccode = '".$mCode
			 . "'  and r360_mkind = '".$mKind
		     . "'  and r360_sugupja = '".$row['jumin']
		     . "'  and r360_service_gbn = '500'";
		$quest500 = $conn->get_array($sql);

			if($quest500['r360_sugupja_name'] == ''){
				?>
				<td class='center'><a type='button' onFocus='this.blur();' onclick="_showPopup(this,'6','<?=$mCode?>','<?=$mKind?>','<?=$ed->en($row["jumin"]);?>');return false;">미작성</a></td>
				<?
			}else {
				?>
				<td class='center'><a type='button' onFocus='this.blur();' onclick="_showPopup(this,'6','<?=$mCode?>','<?=$mKind?>','<?=$ed->en($row["jumin"]);?>');return false;">작성완료</a></td>
				<?
			}

		$sql = "select r360_service_gbn"
			 . ",      r360_sugupja_name"
			 . "  from r360quest"
			 . " where r360_ccode = '".$mCode
			 . "'  and r360_mkind = '".$mKind
		     . "'  and r360_sugupja = '".$row['jumin']
		     . "'  and r360_service_gbn = '800'";
		$quest800 = $conn->get_array($sql);

			if($quest800['r360_sugupja_name'] == ''){
				?>
				<td class='center'><a type='button' onFocus='this.blur();' onclick="_showPopup(this,'7','<?=$mCode?>','<?=$mKind?>','<?=$ed->en($row["jumin"]);?>');return false;">미작성</a></td>
				<?
			}else{
				?>
				<td class='center'><a type='button' onFocus='this.blur();' onclick="_showPopup(this,'7','<?=$mCode?>','<?=$mKind?>','<?=$ed->en($row["jumin"]);?>');return false;">작성완료</a></td>
				<?
			}

		//비고
		echo"<td class='other'></td>";
		}
	}else{
		echo '<tr><td class="center" colspan="15">::검색된 데이타가 없습니다.::</td></tr>';
	}
	$conn->row_free();
	$list = ob_get_contents();
	ob_end_clean();
	echo $list;

?>
</table>
<div id="idPopup" style="z-index:11; left:0; top:0; width:220px; position:absolute; color:#000000; display:none;">
</div>
