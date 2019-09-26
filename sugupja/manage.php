<?
	include('../inc/_header.php');
	include('../inc/_body_header.php');
	include("../inc/_page_list.php");
	include("../inc/_http_referer.php");
	include("../inc/_myFun.php");
	include('../inc/_ed.php');

	$item_count = 20;
	$page_count = 10;
	$page = $_REQUEST["page"];

	if (!is_numeric($page)) $page = 1;
	
	$mCode = $_POST['mCode'] != '' ? $_POST['mCode'] : $_SESSION['userCenterCode'];
	$mKind = $_POST['mKind'] != '' ? $_POST['mKind'] : $_SESSION['userCenterKind'][0];

	$menu = $_POST['mIndex'];
	$tab  = $_POST['mTab'];
	//$mEmploy = $_POST["mEmploy"]; //고용상태
	//$mYoy = $_POST['mYoy'];

	$find_su_name		= $_REQUEST['find_su_name'];
	$find_su_stat		= $_REQUEST['find_su_stat'] != '' ? $_REQUEST['find_su_stat'] : '1';
	
?>
<script type="text/javascript" src="../js/report.js"></script>
<script type="text/javascript" src="../js/salary.js"></script>
<script type="text/javascript" src="../js/work.js"></script>
<form name="f" method="post">
<div class="title">고객평가자료관리</div>
<table class="my_table my_border">
	<colgroup>
		<col width="60px">
		<col width="70px">
		<col width="60px">
		<col width="60px">
		<col width="60px">
		<col width="130px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>기관분류</th>
			<td>
				<input name="mCode" type="hidden" value="<?=$mCode;?>">
				<select name="mKind" style="width:auto;">
				<?
					include('../inc/_kind_option.php');
				?>
				</select>
			</td>
			<th>수급상태</th>
			<td>
				<select name="find_su_stat" style="width:auto;">
					<option value="all">-전체-</option>
					<option value="1" <?=$find_su_stat == "1" ? "selected" : "";?>>수급중</option>
					<option value="2"  <?=$find_su_stat == "2" ? "selected" : "";?>>계약해지</option>
					<option value="3"  <?=$find_su_stat == "3" ? "selected" : "";?>>보류</option>
					<option value="4"  <?=$find_su_stat == "4" ? "selected" : "";?>>사망</option>
					<option value="5"  <?=$find_su_stat == "5" ? "selected" : "";?>>타기관 이전</option>
					<option value="6"  <?=$find_su_stat == "6" ? "selected" : "";?>>등외판정</option>
					<option value="7"  <?=$find_su_stat == "7" ? "selected" : "";?>>입원</option>
				</select>
			</td>
			<th class="left">수급자명</th>
			<td style="width:130px;">
				<input name="find_su_name" type="text" value="<?=$find_su_name;?>" style="width:120px;" onkeyup="if(event.keyCode==13){_list_center('<?=$page;?>');}" onFocus="this.select();">
			</td>
			<td class="other" style="text-align:left; line-height:26px; padding-left:5px; vertical-align:top; padding-top:2px;">
				<span class="btn_pack m icon"><span class="refresh"></span><button type="button" onFocus="this.blur();" onClick="_manage_list1('1');">조회</button></span>
				<?
				$mYear  = $_POST['mYear']  != '' ? $_POST['mYear']  : date('Y', mkTime());
				$mMonth = $_POST['mMonth'] != '' ? $_POST['mMonth'] : date('m', mkTime());
				?>
				<div style="position:absolute; left:900; top:163"><span style="font size:10pt;">당월:&nbsp<?=$mYear?>년&nbsp<?=$mMonth?>월</span></div>
			</td>

		</tr>
	</tbody>
</table>
<table style="width:100%;">
<tr>
	<td style="border:none; vertical-align:top;">
		<table class="my_table my_border" style="margin-top:-1px;">
		<colGroup>
			<col width="50px">
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
		</colGroup>
		<tr>
			<th class="head" rowspan="2">NO</th>
			<th class="head" rowspan="2">고객성명</th>
			<th class="head" rowspan="2">서비스<br>이용<br>계약서</th>
			<th class="head" rowspan="2">초기상담<br>기록지<br>(개시전)</th>
			<th class="head" colspan="3">평가</th>
			<th class="head" rowspan="2">익월<br>서비스<br>일정표</th>
			<th class="head" rowspan="2">전월<br>본인부담<br>영수증발행</th>
			<th class="head" rowspan="2">당월실적<br>확정처리</th>
			<th class="head" rowspan="2">미수금<br>현황</th>
			<th class="head" colspan="3">만족도조사 (분기별)</th>
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
			$wsl = "";
		
			if ($mCode != '') $wsl .= " and m03_ccode like '$mCode'";
			if ($mKind != '') $wsl .= " and m03_mkind like '$mKind'";
			if ($find_su_name  != '') $wsl .= " and m03_name >= '$find_su_name'";
			if ($find_su_stat  != 'all') $wsl .= " and m03_sugup_status = '$find_su_stat'";

			$sql = "select count(*)
					  from m03sugupja
					 left join m00center
						on m00_mcode = m03_ccode
					   and m00_mkind = m03_mkind
					  where m03_ccode = '$mCode'
					   and m03_mkind = '$mKind' $wsl";
			$total_count = $conn->get_data($sql);
		
			// 전체 갯수가 현재페이지 리스트 갯수보다 작으면
			if ($total_count < (intVal($page) - 1) * $item_count) $page = 1;

			$params = array(
				'curMethod'		=> 'post',
				'curPage'		=> 'javascript:_manage_list1',
				'curPageNum'	=> $page,
				'pageVar'		=> 'page',
				'extraVar'		=> '',
				'totalItem'		=> $total_count,
				'perPage'		=> $page_count,
				'perItem'		=> $item_count,
				'prevPage'		=> '[이전]',
				'nextPage'		=> '[다음]',
				'prevPerPage'	=> '[이전'.$page_count.'페이지]',
				'nextPerPage'	=> '[다음'.$page_count.'페이지]',
				'firstPage'		=> '[처음]',
				'lastPage'		=> '[끝]',
				'pageCss'		=> 'page_list_1',
				'curPageCss'	=> 'page_list_2'
			);

			$pageCount = $page;

			if ($pageCount == ""){
				$pageCount = "1";
			}

			$pageCount = (intVal($pageCount) - 1) * $item_count;

			$sql = "select m03_name as name
					,	   m03_jumin as jumin
					,	   m03_skind
					,	   m03_key
					,	   m03_sugup_status
					,      m03_bonin_yul
					  from m03sugupja
					 where m03_ccode = '$mCode'
					   and m03_mkind = '$mKind' $wsl
					 order by m03_name
					 limit $pageCount, $item_count";

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
				$no = $i+1;
				
				echo "
					<tr>
						<td class='center'>$no</td>
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
						<td id="td_1_<?=$i;?>" class='center'><a href='#' onFocus='this.blur();' onclick="__my_modal(Array('<?=$mKind?>','','<?=$ed->en($row["jumin"]);?>','','report','input','31','php','1','2'), 'td_1_<?=$i;?>', '31', 'code', 'kind', '<?=$ed->en($row["jumin"]);?>');return false;">미작성</a></td>
					<?
				}else{
						?>
						<td id="td_1_<?=$i;?>" class='center'><a href='#' onFocus='this.blur();' onclick="_member_report_layer('td_1_<?=$i;?>', '31', '<?=$mCode;?>', '<?=$mKind;?>', '<?=$ed->en($row["jumin"]);?>'); return false;">작성완료</a></td>
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
						<td id="td_2_<?=$i;?>" class='center'><a href='#' onFocus='this.blur();' onclick="__my_modal(Array('<?=$mKind?>','','<?=$ed->en($row["jumin"]);?>','','report','input','37','php','1','2'), 'td_2_<?=$i;?>', '37', 'code', 'kind', '<?=$ed->en($row["jumin"]);?>'); return false;">미작성</a></td>
					<?
				}else{
						?>
						<td id="td_2_<?=$i;?>" class='center'><a href='#' onFocus='this.blur();' onclick="_member_report_layer('td_2_<?=$i;?>', '37', '<?=$mCode;?>', '<?=$mKind;?>', '<?=$ed->en($row["jumin"]);?>'); return false;">작성완료</a></td>
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
						<td id="td_3_<?=$i;?>" class='center'><a href='#' onFocus='this.blur();' onclick="__my_modal(Array('<?=$mKind?>','','<?=$ed->en($row["jumin"]);?>','','report','input','41','php','1','2'), 'td_3_<?=$i;?>', '41', 'code', 'kind', '<?=$ed->en($row["jumin"]);?>'); return false;">미작성</a></td>
					<?
				}else{
						?>
						<td id="td_3_<?=$i;?>" class='center'><a href='#' onFocus='this.blur();' onclick="_member_report_layer('td_3_<?=$i;?>', '41', '<?=$mCode;?>', '<?=$mKind;?>', '<?=$ed->en($row["jumin"]);?>'); return false;">작성완료</a></td>
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
						<td id="td_4_<?=$i;?>" class='center'><a href='#' onFocus='this.blur();' onclick="__my_modal(Array('<?=$mKind?>','','<?=$ed->en($row["jumin"]);?>','','report','input','81','php','1','2'), 'td_4_<?=$i;?>', '81', 'code', 'kind', '<?=$ed->en($row["jumin"]);?>'); return false;">미작성</a></td>
					<?
				}else{
						?>
						<td id="td_4_<?=$i;?>" class='center'><a href='#' onFocus='this.blur();' onclick="_member_report_layer('td_4_<?=$i;?>', '81', '<?=$mCode;?>', '<?=$mKind;?>', '<?=$ed->en($row["jumin"]);?>'); return false;">작성완료</a></td>
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
						echo "<td class='center'><a href='#' onFocus='this.blur();' onClick=\"serviceCalendarShow('".$mCode."', '".$mKind."', '".subStr($beforeYM, 0, 4)."', '".subStr($beforeYM, 4, 2)."', '".$ed->en($row["jumin"])."', 's', 'y', 'pdf', 'y'); return false;\">보기</a></td>";
					}else{
						echo "<td class='center'><a href='#' onFocus='this.blur();' return false;'>미등록</a></td>";
						//echo "<td class='center'><a href='#' onFocus='this.blur();' onClick=\"window.open('../iljung/iljung.php?menuIndex=work&menuSeq=1&manager=true&gubun=reg', 'POPUP', 'left='+((window.screen.width - 1000) / 2)+', top='+((window.screen.height - 600) / 2)+', width=1000, height=600, toolbar=no, location=no, status=no, menubar=no, scrollbars=yes, resizable=no');return false;\">미작성</td>";
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
						echo'<td class="center"><a href="#" onFocus="this.blur();" onClick="_printPaymentsBill(\''.$mCode.'\', \''.$mKind.'\', \''.$mYM.'\', \''.$bonin['t13_bonin_yul'].'\', \''.$row['m03_key'].'\');" value="" style="width:67px; height:18px; border:0px; no-repeat; cursor:pointer;">발행완료</a></td>';
					}else {
						echo"<td class='center'>-</td>";
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
				echo"<td class='center'>-</td>";

				// 미수금현황
				/*
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
						echo"<td class='center'>-</td>";
					}
				*/
				echo"<td class='center'>-</td>";
				
				//방문요양 만족도조사
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
						<td id="td_5_<?=$i;?>" class='center'><a href='#' onFocus='this.blur();' onclick="__my_modal(Array('<?=$mKind?>','','<?=$ed->en($row["jumin"]);?>','','report','input','40','php','1','2'), 'td_5_<?=$i;?>', '40', 'code', 'kind', '<?=$ed->en($row["jumin"]);?>', '200'); return false;">미작성</a></td>
						<?
					}else {
						?>
						<td id="td_5_<?=$i;?>" class='center'><a href='#' onFocus='this.blur();' onclick="_member_report_layer('td_6_<?=$i;?>', '40', '<?=$mCode;?>', '<?=$mKind;?>', '<?=$ed->en($row["jumin"]);?>', '200'); return false;">작성완료</a></td>
						<?
					}
				
				//방문목욕 만족도조사
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
						<td id="td_6_<?=$i;?>" class='center'><a href='#' onFocus='this.blur();' onclick="__my_modal(Array('<?=$mKind?>','','<?=$ed->en($row["jumin"]);?>','','report','input','74','php','1','2'), 'td_7_<?=$i;?>', '74', 'code', 'kind', '<?=$ed->en($row["jumin"]);?>','500'); return false;">미작성</a></td>
						<?
					}else {
						?>
						<td id="td_6_<?=$i;?>" class='center'><a href='#' onFocus='this.blur();' onclick="_member_report_layer('td_6_<?=$i;?>', '74', '<?=$mCode;?>', '<?=$mKind;?>', '<?=$ed->en($row["jumin"]);?>','500'); return false;">작성완료</a></td>
						<?
					}
				
				//방문간호 만족도조사
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
						<td id="td_7_<?=$i;?>" class='center'><a href='#' onFocus='this.blur();' onclick="__my_modal(Array('<?=$mKind?>','','<?=$ed->en($row["jumin"]);?>','','report','input','75','php','1','2'), 'td_7_<?=$i;?>', '75', 'code', 'kind', '<?=$ed->en($row["jumin"]);?>','800');return false;">미작성</a></td>
						<?
					}else{
						?>
						<td id="td_7_<?=$i;?>" class='center'><a href='#' onFocus='this.blur();' onclick="_member_report_layer('td_7_<?=$i;?>', '75', '<?=$mCode;?>', '<?=$mKind;?>','<?=$ed->en($row["jumin"]);?>','800'); return false;">작성완료</a></td>
						<?
					}

				//비고
				//echo"<td class='other'></td>";
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
	</td>
</tr>
</table>
<div style="text-align:left;">
	<div style="position:absolute; width:auto; padding-left:10px;">검색된 전체 갯수 : <?=number_format($total_count);?></div>
	<div style="width:100%; text-align:center;">
	<?
		$paging = new YsPaging($params);
		$paging->printPaging();
	?>
	</div>
</div>
<input name="code"	type="hidden" value="<?=$mCode;?>">
<input name="kind"	type="hidden" value="<?=$mKind;?>">
<input name="page"	type="hidden" value="<?=$page;?>">
<input name="jumin"	type="hidden" value="">
</form>
<?
	include('../inc/_body_footer.php');
	include('../inc/_footer.php');
?>
<script language="javascript">
		// 기관리스트
	function _manage_list1(page){
		var f = document.f;

		f.page.value = page;
		f.action = 'manage.php';
		f.submit();
	}
</script>