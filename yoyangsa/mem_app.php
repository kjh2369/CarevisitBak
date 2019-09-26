<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");
	include_once('../inc/_ed.php');

	$code = $_SESSION['userCenterCode'];

	$mem_stat = $_POST['mem_stat'];
	$mem_dept = $_POST['mem_dept'];
	$mem_name = $_POST['mem_name'];

	$item_count = 20;
	$page_count = 10;
	$page = $_REQUEST["page"];

	if (!is_numeric($page)) $page = 1;
?>

<script language='javascript' src='../js/report.js'></script>
<script language='javascript' src='../js/work.js'></script>
<script language='javascript'>
<!--

var f = null;

function search(page){
	if (page != undefined) f.page.value = page;
	f.target = '';
	f.action = 'mem_app.php';
	f.submit();
}

function report_show(paper_dir, params, r_index){
	var r_val  = r_index.split('_');
	var r_menu = r_val[0];
	var r_id   = r_val[r_val.length - 1];

	f.report_menu.value	= r_menu;
	f.report_index.value= r_index;
	f.report_id.value	= r_id;

	_report_show_pdf(paper_dir, params, r_id);
}

window.onload = function(){
	f = document.f;

	__init_form(f);
}

-->
</script>

<div class="title_border">
	<div id="report_navi" class="title" style="width:auto; float:left;">직원평가관리</div>
	<?
		include_once('../reportMenu/report_view_download.php');
	?>
</div>

<form name="f" method="post">

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="60px">
		<col width="50px">
		<col width="40px">
		<col width="50px">
		<col width="50px">
		<col width="50px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>고용상태</th>
			<td>
			<?
				echo '<select name=\'mem_stat\' style=\'width:auto;\'>
						<option value=\'\'>전체</option>
						<option value=\'1\' '.($mem_stat == '1' ? 'selected' : '').'>재직</option>
						<option value=\'2\' '.($mem_stat == '2' ? 'selected' : '').'>휴직</option>
						<option value=\'9\' '.($mem_stat == '9' ? 'selected' : '').'>퇴사</option>
					  </select>';
			?>
			</td>
			<th>부서</th>
			<td>
			<?
				echo '<select name=\'mem_dept\' style=\'width:auto;\'>';
				echo '<option value=\'\'>전체</option>';

				$sql = 'select dept_cd, dept_nm
						  from dept
						 where org_no   = \''.$code.'\'
						   and del_flag = \'N\'
						 order by order_seq';

				$conn->query($sql);
				$conn->fetch();

				$row_count = $conn->row_count();

				for($i=0; $i<$row_count; $i++){
					$row = $conn->select_row($i);

					echo '<option value=\''.$row['dept_cd'].'\' '.($mem_dept == $row['dept_cd'] ? 'selected' : '').'>'.$row['dept_nm'].'</option>';
				}

				$conn->row_free();

				echo '<option value=\'-\' '.($mem_dept == '-' ? 'selected' : '').'>미등록</option>';
				echo '</select>';
			?>
			</td>
			<th>직원명</th>
			<td>
			<?
				echo '<input name=\'mem_name\' type=\'text\' value=\''.$mem_name.'\'>';
			?>
			</td>
			<td class="left last"><span class="btn_pack m"><button type="button" onclick="search();">조회</button></span></td>
		</tr>
	</tbody>
</table>


<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="70px">
		<col width="100px">
		<col width="45px">
		<col width="55px">
		<col width="60px">
		<col width="70px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">직원명</th>
			<th class="head">고용형태</th>
			<th class="head">근로<br>계약서</th>
			<th class="head">초기상담<br>기록지</th>
			<th class="head">상담일지<br>(격월주기)</th>
			<th class="head">직무평가및<br>만족도조사<br>(격월주기)</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody>
	<?
		$wsl = ' where m02_ccode  = \''.$code.'\'
				   and m02_del_yn = \'N\'';

			if (!empty($mem_stat)) $wsl .= ' and m02_ygoyong_stat = \''.$mem_stat.'\'';
			if (!empty($mem_dept)) $wsl .= ' and m02_dept_cd = \''.$mem_dept.'\'';
			if (!empty($mem_name)) $wsl .= ' and m02_yname >= \''.$mem_name.'\'';

		$sql = 'select count(*)
				  from (
					   select min(m02_mkind)
					   ,      m02_yjumin
						 from m02yoyangsa '.$wsl.'
						group by m02_yjumin
					   ) as t';
		$total_count = $conn->get_data($sql);

		// 전체 갯수가 현재페이지 리스트 갯수보다 작으면
		if ($total_count < (intVal($page) - 1) * $item_count) $page = 1;

		$params = array(
			'curMethod'		=> 'post',
			'curPage'		=> 'javascript:search',
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

		$yymm = date('Ym', strtotime('+1 month'));

		$sql = 'select m02_ccode as k_cd
				,      min(m02_mkind) as k_kind
				,      m02_yjumin as m_cd
				,      m02_yname as m_nm
				,      m02_ygoyong_kind as m_pay
				,	   m02_yipsail as yipsail

				,     (select count(*)
						 from counsel_mem
						where org_no  = m02_ccode
						  and mem_ssn = m02_yjumin) as counsel_cnt

				,	  (select max(concat(\'_\', stress_dt, \'_\', stress_seq))
						 from counsel_stress
						where org_no     = m02_ccode
						  and stress_ssn = m02_yjumin) as stress_cnt

				,     (select concat(datediff(date_add(date_format(ifnull(max(r_reg_dt), \'0000-00-00\'), \'%Y-%m-%d\'), interval 2 month), date_format(now(), \'%Y-%m-%d\')), \'_\', max(concat(r_yymm, \'_\', r_seq)))
					     from r_memtr
					    where r_memtr.org_no = m02_ccode
					      and r_memtr.r_m_id = m02_yjumin) as memtr_cnt

				,     (select concat(datediff(date_add(date_format(ifnull(max(r_reg_dt), \'0000-00-00\'), \'%Y-%m-%d\'), interval 2 month), date_format(now(), \'%Y-%m-%d\')), \'_\', max(concat(r_yymm, \'_\', r_seq)))
					     from r_memjas
					    where r_memjas.org_no = m02_ccode
					      and r_memjas.r_m_id = m02_yjumin) as memjas_cnt
				  from m02yoyangsa'.$wsl;

		$sql .= ' group by m02_ccode, m02_yjumin, m02_yname
				  order by m_nm
				  limit '.$pageCount.','.$item_count;
	
		$conn->query($sql);
		$conn->fetch();

		$row_count = $conn->row_count();

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);
			$seq ++;

			echo '<tr>';
			echo '<td class=\'center\'>'.$seq.'</td>';
			echo '<td class=\'center\'><div class=\'left nowrap\' style=\'width:70px;\' title=\''.$row['m_nm'].'\'>'.$row['m_nm'].'</div></td>';
			if ($row['m_pay'] == '1'){
				#월급
				$html = '{"report_id":"WR60M","m_cd":"'.$ed->en($row['m_cd']).'","dt":"'.$row['yipsail'].'"}';
				$title = '정규직';
			}else if ($row['m_pay'] == '3'){
				#60시간 이상
				$html = '{"report_id":"WR60U","m_cd":"'.$ed->en($row['m_cd']).'","dt":"'.$row['yipsail'].'"}';
				$title = '단시간(60 이상)';
			}else if ($row['m_pay'] == '2'){
				#계약직
				$title = '계약직';
				$html = '{"report_id":"WR60U","m_cd":"'.$ed->en($row['m_cd']).'","dt":"'.$row['yipsail'].'"}';
			}else{
				$html = '{"report_id":"WR60D","m_cd":"'.$ed->en($row['m_cd']).'","dt":"'.$row['yipsail'].'"}';
				$title = '단시간(60 미만)';
			}

			/*************************************************************
			    고용형태
			**************************************************************/
				echo '<td class=\'center\'><div class=\'left\'>'.$title.'</div></td>';

			/**************************************************************

				근로계약서

			**************************************************************/
				echo '<td class=\'center\'>';

				if (!empty($html)){
					echo '<img src=\'../image/icon_word.png\' style=\'cursor:pointer;\' onclick=\'_contract_dt_input_layer(this,'.$html.');\' alt=\'워드문서 출력\'>';
				}else{
					echo '-';
				}

				echo '</td>';



			/**************************************************************

				초기상담 기록지

			**************************************************************/
				echo '<td class=\'center\'>';
				echo '<img src=\'../image/'.($row['counsel_cnt'] > 0 ? 'icon_editer' : 'icon_writer').'.png\' style=\'cursor:pointer;\' onclick=\'location.href="../counsel/mem_counsel_reg.php?parent_id=110&ssn='.$ed->en($row['m_cd']).'";\' alt=\'초기상담기록지 수정\'> ';
				echo '<img src=\'../image/icon_pdf.png\' style=\'cursor:pointer;\' onclick=\'window.open("../counsel/mem_counsel_print.php?code='.$row['k_cd'].'&ssn='.$ed->en($row['m_cd']).'", "REPORT", "width=700,height=900,scrollbars=yes,resizable=yes,location=no,toolbar=no,menubar=no");\' alt=\'초기상담기록지 출력\'>';
				echo '</td>';



			/**************************************************************

				익월서비스 일정표

			**************************************************************/
			/*
				echo '<td class=\'center\'>';

				if ($row['i_cnt'] > 0){
					echo '<img src=\'../image/icon_html.png\' style=\'cursor:pointer;\' onclick=\'serviceCalendarShow("'.$row['k_cd'].'","'.$row['k_kind'].'","'.substr($yymm, 0, 4).'","'.substr($yymm, 4).'","'.$ed->en($row['m_cd']).'","y","y","html","y");\' alt=\'익월 서비스 일정표 보기\'> ';
					echo '<img src=\'../image/icon_pdf.png\' style=\'cursor:pointer;\' onclick=\'serviceCalendarShow("'.$row['k_cd'].'","'.$row['k_kind'].'","'.substr($yymm, 0, 4).'","'.substr($yymm, 4).'","'.$ed->en($row['m_cd']).'","y","y","pdf","y");\' alt=\'익월 서비스 일정표 출력\'>';
				}else{
					echo '-';
				}

				echo '</td>';
			*/


			/**************************************************************

				상담일지

			**************************************************************/
				echo '<td class=\'center\'>';

				$arr   = explode('_', $row['stress_cnt']);
				$para  = ' "m_cd":"'.$ed->en($row['m_cd']).'"';
				$para .= ',"seq":"'.$arr[2].'"';
				$para  = '{'.$para.'}';

				if (!empty($row['stress_cnt'])){
					echo '<img src=\'../image/btn_rep_list.gif\' style=\'cursor:pointer;\' onclick=\'_report_app_list(this,"APP_LIST","20_10_50_MEMTR","'.$row['k_cd'].'","'.$ed->en($row['m_cd']).'");\' alt=\'상담일지 리스트\'>';
				}else{
					echo '<img src=\'../image/btn_rep_reg.gif\' style=\'cursor:pointer;\' onclick=\'_report_reg("'.$row['k_cd'].'","20","20_10_50_MEMTR","'.date('Ym', mktime()).'","0","'.$ed->en($row['m_cd']).'");\' alt=\'상담일지 작성\'>';
				}

				/*
				echo '<img src=\'../image/icon_writer.png\' style=\'cursor:pointer;\' onclick=\'_report_reg("'.$row['k_cd'].'","20","20_10_50_MEMTR","'.date('Ym', mktime()).'","0","'.$ed->en($row['m_cd']).'");\' alt=\'상담일지 작성\'> ';

				if (!empty($row['stress_cnt'])){
					echo '<img src=\'../image/icon_editer.png\' style=\'cursor:pointer;\' onclick=\'_report_reg("'.$row['k_cd'].'","20","20_10_50_MEMTR","'.$arr[1].'","'.$arr[2].'","'.$ed->en($row['m_cd']).'");\' alt=\'상담일지 수정\'> ';
					echo '<img src=\'../image/icon_pdf.png\' style=\'cursor:pointer;\' onclick=\'report_show(1,'.$para.',"20_10_50_MEMTR");\' alt=\'상담일지 출력\'>';
				}
				*/

				echo '</td>';

				unset($arr);



			/**************************************************************

				직무평가 및 만족도 조사

			**************************************************************/
				echo '<td class=\'center\'>';

				$arr   = explode('_', $row['memjas_cnt']);
				$para  = ' "yymm":"'.$arr[1].'"';
				$para .= ',"seq":"'.$arr[2].'"';
				$para  = '{'.$para.'}';

				if (!empty($row['memjas_cnt'])){
					echo '<img src=\'../image/btn_rep_list.gif\' style=\'cursor:pointer;\' onclick=\'_report_app_list(this,"APP_LIST","20_10_50_MEMJAS","'.$row['k_cd'].'","'.$ed->en($row['m_cd']).'");\' alt=\'직무평가 및 만족도 조사 리스트\'>';
				}else{
					echo '<img src=\'../image/btn_rep_reg.gif\' style=\'cursor:pointer;\' onclick=\'_report_reg("'.$row['k_cd'].'","20","20_10_50_MEMJAS","'.date('Ym', mktime()).'","0","'.$ed->en($row['m_cd']).'");\' alt=\'직무평가 및 만족도 조사 작성\'>';
				}

				/*
				if ($row['memjas_cnt'] < 1)
					echo '<img src=\'../image/icon_writer.png\' style=\'cursor:pointer;\' onclick=\'_report_reg("'.$row['k_cd'].'","20","20_10_50_MEMJAS","'.date('Ym', mktime()).'","0","'.$ed->en($row['m_cd']).'");\' alt=\'직무평가 및 만족도 조사 작성\'> ';
				else
					echo '<img src=\'../image/icon_editer.png\' style=\'cursor:pointer;\' onclick=\'_report_reg("'.$row['k_cd'].'","20","20_10_50_MEMJAS","'.$arr[1].'","'.$arr[2].'","'.$ed->en($row['m_cd']).'");\' alt=\'직무평가 및 만족도 조사 수정\'> ';

				if (!empty($row['memjas_cnt'])) echo '<img src=\'../image/icon_pdf.png\' style=\'cursor:pointer;\' onclick=\'report_show(1,'.$para.',"20_10_50_MEMJAS");\' alt=\'직무평가 및 만족도 조사 출력\'>';
				*/

				echo '</td>';

				unset($arr);



			echo '<td class=\'left last\'></td';
			echo '</tr>';
		}

		$conn->row_free();

		echo '<input name=\'para_yymm\' type=\'hidden\' value=\'\'>'; //
		echo '<input name=\'para_seq\'  type=\'hidden\' value=\'\'>'; //
		echo '<input name=\'para_kind\' type=\'hidden\' value=\'\'>'; //서비스
		echo '<input name=\'para_dt\'   type=\'hidden\' value=\'\'>'; //일자
		echo '<input name=\'para_m_cd\' type=\'hidden\' value=\'\'>'; //직원
		echo '<input name=\'para_c_cd\' type=\'hidden\' value=\'\'>'; //고객
		echo '<input name=\'para_type\' type=\'hidden\' value=\'\'>'; //타입
	?>
	</tbody>
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
<input name="page"	type="hidden" value="<?=$page;?>">

<input name="ssn"	type="hidden" value="">
<input name="report_menu"	type="hidden" value="">
<input name="report_index"	type="hidden" value="">
<input name="report_id"		type="hidden" value="">
<input name="yymm"	type="hidden" value="">
<input name="seq"	type="hidden" value="">
<input name="copy_yn"	type="hidden" value="">

<div id="APP_LIST" style="position:absolute; top:0; left:0; width:auto; background-color:#ffffff; border:2px solid #0e69b0; display:none;"></div>

</form>

<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>