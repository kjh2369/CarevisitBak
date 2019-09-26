<?
	include('../inc/_header.php');
	include('../inc/_body_header.php');
	include_once("../inc/_page_list.php");
	include("../inc/_http_referer.php");
	include("../inc/_myFun.php");
	include('../inc/_ed.php');

	$item_count = 20;
	$page_count = 10;
	$page = $_REQUEST["page"];

	if (!is_numeric($page)) $page = 1;

	$code = $_POST['mCode'] != '' ? $_POST['mCode'] : $_SESSION['userCenterCode'];
	$mKind = $_POST['mKind'] != '' ? $_POST['mKind'] : $_SESSION['userCenterKind'][0];

	$menu = $_POST['mIndex'];
	$tab  = $_POST['mTab'];
	//$mEmploy = $_POST["mEmploy"]; //고용상태
	//$mYoy = $_POST['mYoy'];

	$find_yoy_name		= $_REQUEST['find_yoy_name'];
	$find_yoy_stat		= $_REQUEST['find_yoy_stat'] != '' ? $_REQUEST['find_yoy_stat'] : '1';
	$find_dept          = $_REQUEST['find_dept'];

	if (empty($find_dept)) $find_dept = 'all';

?>
<script type="text/javascript" src="../js/report.js"></script>
<script type="text/javascript" src="../js/salary.js"></script>
<script type="text/javascript" src="../js/work.js"></script>
<form name="f" method="post">
<div class="title title_border">직원평가자료관리</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="60px">
		<col width="60px">
		<col width="50px">
		<col width="130px">
		<col width="80px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>고용상태</th>
			<td>
				<select name="find_yoy_stat" style="width:auto;">
					<option value="all">전체</option>
					<option value="1" <?=$find_yoy_stat == "1" ? "selected" : "";?>>활동</option>
					<option value="2" <?=$find_yoy_stat == "2" ? "selected" : "";?>>휴직</option>
					<option value="9" <?=$find_yoy_stat == "9" ? "selected" : "";?>>퇴사</option>
				</select>
			</td>
			<th class="left">직원명</th>
			<td><input name="find_yoy_name" type="text" value="<?=$find_yoy_name;?>" style="width:120px;" onkeyup="if(event.keyCode==13){_list_center('<?=$page;?>');}" onFocus="this.select();"></td>
			<th class="left">부서명</th>
			<td>
			<?
				echo '<select name=\'find_dept\' style=\'width:auto;\'>';
				echo '<option value=\'all\' '.($find_dept == 'all' ? 'selected' : '').'>전체</option>';

				$sql = "select dept_cd, dept_nm
						  from dept
						 where org_no   = '$code'
						   and del_flag = 'N'
						 order by order_seq";

				$conn->query($sql);
				$conn->fetch();

				$row_count = $conn->row_count();

				for($i=0; $i<$row_count; $i++){
					$row = $conn->select_row($i);

					echo '<option value=\''.$row['dept_cd'].'\' '.($find_dept == $row['dept_cd'] ? 'selected' : '').'>'.$row['dept_nm'].'</option>';
				}

				$conn->row_free();

				echo '<option value=\'-\' '.($find_dept == '-' ? 'selected' : '').'>미등록</option>';
				echo '</select>';
			?>
			</td>
			<td class="left last"><span class="btn_pack m icon"><span class="refresh"></span><button type="button" onFocus="this.blur();" onClick="_manage_list1('1');">조회</button></span></td>
			<td class="right last">당년월 : <?=date('Y년 m월', mkTime());?></td>
		</tr>
	</tbody>
</table>

<table class="my_table" style="width:100%;">
	<colGroup>
		<col width="50px">
		<col width="100px">
		<col width="50px">
		<col width="80px">
		<col width="80px">
		<col width="70px">
		<col width="80px">
		<col width="80px">
		<col width="80px">
		<col width="80px">
		<col width="80px">
		<col width="80px">
	</colGroup>
	<tr>
		<th class="head" rowspan="2">NO</th>
		<th class="head" rowspan="2">직원명</th>
		<th class="head" rowspan="2">근로<br>계약서</th>
		<th class="head" rowspan="2">초기상담<br>기록지</th>
		<th class="head" rowspan="2">익월서비스<br>일정표</th>
		<th class="head" rowspan="2">상담일지<br>(격월주기)</th>
		<th class="head" rowspan="2">직무평가및<br>만족도조사<br>(격월주기)</th>
		<th class="head" colspan="4">교육</th>
		<th class="head last" rowspan="2">건강검진<br>(년주기)</th>
	</tr>
	<tr>
		<th class="head">신규</th>
		<th class="head">급여제공</th>
		<th class="head">업무범위</th>
		<th class="head">개인정보보호</th>
	</tr>
<?
	$wsl = "";

	if ($code != '') $wsl .= " and m02_ccode like '$code'";
	if ($find_yoy_name  != '') $wsl .= " and m02_yname >= '$find_yoy_name'";
	if ($find_yoy_stat  != 'all') $wsl .= " and m02_ygoyong_stat = '$find_yoy_stat'";
	if ($find_dept      != 'all') $wsl .= " and m02_dept_cd = '".str_replace('-','',$find_dept)."'";
	if ($find_dept      != 'all') $wsl .= " and m02_dept_cd = '$find_dept'";

	$sql = "select count(*)
			  from m02yoyangsa
			 left join m00center
				on m00_mcode  = m02_ccode
			   and m00_mkind  = m02_mkind
			  where m02_ccode = '$code'
			    and m02_mkind = ".$conn->_member_kind().$wsl;
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
	$beforeYM = date("Ym",strtotime("+1 month"));
	$sql = "select m02_yname as name
			,      m02_yjumin as jumin
			,      m02_ygoyong_kind
			,      m02_key
			,      m02_ygoyong_stat
			,     (select count(t01_jumin)
					 from t01iljung
					where t01_ccode = m02_ccode
					  and t01_mkind = m02_mkind
					  and t01_sugup_date like '$beforeYM%'
					  and m02_yjumin in (t01_yoyangsa_id1, t01_yoyangsa_id2, t01_yoyangsa_id3, t01_yoyangsa_id4, t01_yoyangsa_id5)
					  and t01_del_yn = 'N') as iljungCount
			,     (select datediff(date_add(date_format(concat(ifnull(max(r260_date),''),'000000'), '%Y-%m-%d'), interval 2 month), date_format(now(), '%Y-%m-%d'))
					 from r260talk
					where r260_ccode = m02_ccode
					  and r260_mkind = m02_mkind
					  and r260_yoyangsa = m02_yjumin) as talkDays
			,     (select datediff(date_add(date_format(concat(ifnull(max(r270_date),''),'000000'), '%Y-%m-%d'), interval 2 month), date_format(now(), '%Y-%m-%d'))
					 from r270test
					where r270_ccode = m02_ccode
					  and r270_mkind = m02_mkind
					  and r270_yoy_code = m02_yjumin) as testDays
			  from m02yoyangsa
			 where m02_ccode = '$code'
			   and m02_mkind = ".$conn->_member_kind().$wsl."
			 order by m02_yname
			 limit $pageCount, $item_count";

	$conn->query($sql);
	$conn->fetch();
	$rowCount = $conn->row_count();

	ob_start();

	if ($rowCount > 0){
		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			$name = $row["name"];
			$namtTitle = $row["name"];
			$jumin = subStr($row["jumin"], 0, 6)."-".subStr($row["jumin"], 6, 1)."******";
			$pageNo = $pageCount + ($i + 1);

			echo "
				<tr>
					<td class='center'>$pageNo</td>
					<td class='left' title='$namtTitle'>$name</td>
					";

			//근로계약서 출력
			if($row['m02_ygoyong_kind'] == "1"){?>
				<td class='center'><a href='#' onFocus='this.blur();' onClick="showReport3('96','<?=$code;?>','<?=$mKind;?>','','','<?=$ed->en($row['jumin']);?>');">출력</a></td><?
			}else if ($row['m02_ygoyong_kind'] == "2"){?>
				<td class='center'><a href='#' onFocus='this.blur();' onClick="showReport3('95','<?=$code;?>','<?=$mKind;?>','','','<?=$ed->en($row['jumin']);?>');">출력</a></td><?
			}else { ?>
				<td class='center'><a href='#' onFocus='this.blur();' onClick="showReport3('90','<?=$code;?>','<?=$mKind;?>','','','<?=$ed->en($row['jumin']);?>');">출력</a></td><?
			}

			// 개인정보보호 동의서
			echo '<td style=\'padding:0; margin:0; text-align:center; padding-top:2px;\'><a href=\'#\' onClick=\'location.href="../counsel/mem_counsel_reg.php?parent_id=100&ssn='.$ed->en($row['jumin']).'";\'>보기</a></td>';

			//익월서비스일정표
			if ($row['iljungCount'] > 0){
				echo "<td class='center'><a href='#' onFocus='this.blur();' onClick=\"serviceCalendarShow('".$code."', '".$mKind."', '".subStr($beforeYM, 0, 4)."', '".subStr($beforeYM, 4, 2)."', '".$ed->en($row["jumin"])."', 'y', 'y', 'html', 'y'); return false;\">보기</a></td>";
			}else{
				if($row["m02_ygoyong_stat"] == "1"){
					echo "<td class='center'>미등록</td>";
				}else {
					echo "<td class='center'>-</td>";
				}
			}

			//상담일지(격월주기)
			if($row['talkDays'] > 0){
			?>
				<td id="td_6_<?=$i;?>" class='center'><a href='#' onFocus='this.blur();' onclick="_member_report_layer('td_6_<?=$i;?>', '47', '<?=$code;?>', '<?=$mKind;?>', '<?=$ed->en($row["jumin"]);?>'); return false;">작성완료</a></td>
			<?
			}else {
				if($row["m02_ygoyong_stat"] == "1"){
				?>
					<td id="td_6_<?=$i;?>" class='center'><a href='#' onFocus='this.blur();' onclick="__my_modal(Array('<?=$mKind?>','','<?=$ed->en($row["jumin"]);?>','','report','input','47','php','1','2'), 'td_6_<?=$i;?>', '47', 'code', 'kind', '<?=$ed->en($row["jumin"]);?>'); return false;">미작성</a></td>
				<?
				}else {
				?>
					<td style='padding:0; margin:0; text-align:center; padding-top:2px;'><a href='#' onFocus='this.blur();' onClick='stat_chk();return false;'>-</a></td>
				<?
				}
			}

			//직무평가 및 만족도조사
			if($row['testDays'] > 0){
			?>
				<td id="td_7_<?=$i;?>" class='center'><a href='#' onFocus='this.blur();' onclick="_member_report_layer('td_7_<?=$i;?>', '33', '<?=$code;?>', '<?=$mKind;?>', '<?=$ed->en($row["jumin"]);?>'); return false;" >작성완료</a></td>
			<?
			}else {
				if($row["m02_ygoyong_stat"] == "1"){
				?>
					<td id="td_7_<?=$i;?>" class='center'><a href='#' onFocus='this.blur();' onclick="__my_modal(Array('<?=$mKind?>','','<?=$ed->en($row["jumin"]);?>','','report','input','33','php','1','2'), 'td_7_<?=$i;?>', '33', 'code', 'kind', '<?=$ed->en($row["jumin"]);?>'); return false;">미작성</a></td>
				<?
				}else {
				?>
					<td id="td_7_<?=$i;?>" class='center'><a href='#' onFocus='this.blur();' onClick='stat_chk();return false;'>-</a></td>
				<?
				}
			}

			echo "
					<td class='center'>-</td>
					<td class='center'>-</td>
					<td class='center'>-</td>
					<td class='center'>-</td>
					<td class='center last'>-</td>
				</tr>
				 ";
		}
	}else{
		echo '<tr><td class="center" colspan="13">::검색된 데이타가 없습니다.::</td></tr>';
	}
	$conn->row_free();
	$list = ob_get_contents();
	ob_end_clean();
	echo $list;
?>
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
<input name="code"	type="hidden" value="<?=$code;?>">
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

	function stat_chk(){
		alert("퇴사자이거나 휴직중인 직원입니다!");
		return;
	}

</script>
