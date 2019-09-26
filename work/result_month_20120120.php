<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");

	$code = $_SESSION['userCenterCode'];

	$item_count = 20;
	$page_count = 10;
	$page = $_REQUEST["page"];

	if (!is_numeric($page)) $page = 1;

	$find_year = $_POST['find_year'] != '' ? $_POST['find_year'] : date('Y', mktime());
	$find_name = $_POST['find_name'];
	$find_kind = $_POST['find_kind'];
	$find_dept = $_POST['find_dept'];

	if (!isset($find_kind)) $find_kind = 'all';
	if (!isset($find_dept)) $find_dept = 'all';

	/*
	 * mode 설정
	 * 1 : 일실적등록(수급자)
	 * 2 : 월실적등록(수급자)
	 * 3 : 월실적등록(요양보호사)
	 */
	$mode	= $_REQUEST['mode'];

	switch($mode){
	case 1:
		$title = '수급자';
		break;
	case 2:
		$title = '수급자';
		break;
	case 3:
		$title = '요양사';
		break;
	default:
		echo $myF->message('err1', 'Y', 'Y');
		exit;
	}

	$init_year = $myF->year();
?>

<script src="../js/work.js" type="text/javascript"></script>
<script language='javascript'>
<!--
function search(){
	var f = document.f;

	f.submit();
}

function work_list(code, kind, year, month, jumin){
	var f = document.f;

	f.code.value  = code;
	f.kind.value  = kind;
	f.year.value  = year;
	f.month.value = month;
	f.jumin.value = jumin;

	f.action = 'result_detail.php';
	f.submit();
}

function list(p_page){
	var f = document.f;

	f.page.value = p_page;
	f.submit();
}
-->
</script>

<form name="f" method="post">

<div class="title title_border">월 실적 등록(<?=$title;?>)</div>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="35px">
		<col width="40px">
		<col width="60px">
		<col width="40px">
		<col width="70px">
		<col width="40px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>년도</th>
			<td>
				<select name="find_year" style="width:auto;">
				<?
					for($i=$init_year[0]; $i<=$init_year[1]; $i++){ ?>
						<option value="<?=$i;?>" <? if($find_year == $i){?>selected<?} ?>><?=$i;?></option><?
					}
				?>
				</select>
			</td>
			<th><?=($mode == 1 || $mode == 2 ? '서비스' : '부서');?></th>
			<td>
			<?
				if ($mode == 1 || $mode == 2){
					$kind_list = $conn->kind_list($code);

					echo '<select name=\'find_kind\' style=\'width:auto;\'>';
					echo '<option value=\'all\'>전체</option>';

					foreach($kind_list as $i => $k){
						if (($mode != 3) || ($mode == 3 && $k['code'] != '0'))
							echo '<option value=\''.$k['code'].'\' '.($find_kind == $k['code'] ? 'selected' : '').'>'.$k['name'].'</option>';
					}

					echo '</select>';
				}else{
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

					echo '</select>';
				}
			?>
			</td>
			<th><?=$title;?> 성명</th>
			<td>
				<input name="find_name" type="text" value="<?=$find_name;?>">
			</td>
			<td class="left last">
				<span class="btn_pack m icon"><span class="refresh"></span><button type="button" onclick="search();">조회</button></span>
			</td>
		</tr>
	</tbody>
</table>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="100px">
		<col width="100px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head"><?=$title;?></th>
			<?
				if ($mode == 1 || $mode == 2){?>
					<th class="head">제공서비스</th><?
				}else{?>
					<th class="head">부서</th><?
				}
			?>
			<th class="head last">월별</th>
		</tr>
	</thead>
	<tbody>
	<?
		if ($mode == 1 || $mode == 2){
			if ($find_kind == 'all'){
				//$wsl = ' and m03_mkind = '.$conn->_client_kind('N');
			}else{
				$wsl = ' and m03_mkind =\''.$find_kind.'\'';
			}
			$wsl .= ' and \''.$find_year.'\' between left(m03_gaeyak_fm, 4) and left(case when length(m03_gaeyak_to) = 8 then m03_gaeyak_to else \'99999999\' end, 4)';

			if ($find_name != '') $wsl .= " and m03_name >= '$find_name'";

			$sql = "select count(distinct m03_jumin)
					  from m03sugupja
					 inner join t01iljung
						on t01_ccode         = m03_ccode
					   and t01_mkind         = m03_mkind
					   and t01_jumin         = m03_jumin
					   and t01_del_yn        = 'N'
					   and t01_sugup_date like '$find_year%'
					 where m03_ccode = '$code'
					   and m03_del_yn = 'N' $wsl";
		}else{
			if ($find_dept != 'all'){
				$wsl = ' and m02_dept_cd = \''.$find_dept.'\'';
			}

			//$wsl .= ' and m02_mkind = '.$conn->_member_kind('N');
			$wsl .= ' and \''.$find_year.'\' between left(m02_yipsail, 4) and left(case when length(m02_ytoisail) = 8 then m02_ytoisail else \'99999999\' end, 4)';

			if ($find_name != '') $wsl .= " and m02_yname >= '$find_name'";

			$sql = "select count(distinct m02_yjumin)
					  from m02yoyangsa
					 inner join t01iljung
						on t01_ccode         = m02_ccode
					   and t01_mkind         = m02_mkind
					   and t01_yoyangsa_id1  = m02_yjumin
					   and t01_del_yn        = 'N'
					   and t01_sugup_date like '$find_year%'
					 where m02_ccode = '$code'
					   and m02_del_yn = 'N' $wsl";
		}

		$total_count = $conn->get_data($sql);

		// 전체 갯수가 현재페이지 리스트 갯수보다 작으면
		if ($total_count < (intVal($page) - 1) * $item_count) $page = 1;

		$params = array(
			'curMethod'		=> 'post',
			'curPage'		=> 'javascript:list',
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

		if ($mode == 1 || $mode == 2){
			$sql = "select m03_ccode as code
					,      min(m03_mkind) as kind
					,      m03_jumin as jumin
					,      m03_name as name
					,      left(m03_gaeyak_fm, 6) as i_date
					,      left(case when length(m03_gaeyak_to) = 8 then m03_gaeyak_to else '99999999' end, 6) as o_date
					,      sum(case when date_format(t01_sugup_date, '%m') = '01' and t01_del_yn = 'N' then 1 else 0 end) as m01
					,      sum(case when date_format(t01_sugup_date, '%m') = '02' and t01_del_yn = 'N' then 1 else 0 end) as m02
					,      sum(case when date_format(t01_sugup_date, '%m') = '03' and t01_del_yn = 'N' then 1 else 0 end) as m03
					,      sum(case when date_format(t01_sugup_date, '%m') = '04' and t01_del_yn = 'N' then 1 else 0 end) as m04
					,      sum(case when date_format(t01_sugup_date, '%m') = '05' and t01_del_yn = 'N' then 1 else 0 end) as m05
					,      sum(case when date_format(t01_sugup_date, '%m') = '06' and t01_del_yn = 'N' then 1 else 0 end) as m06
					,      sum(case when date_format(t01_sugup_date, '%m') = '07' and t01_del_yn = 'N' then 1 else 0 end) as m07
					,      sum(case when date_format(t01_sugup_date, '%m') = '08' and t01_del_yn = 'N' then 1 else 0 end) as m08
					,      sum(case when date_format(t01_sugup_date, '%m') = '09' and t01_del_yn = 'N' then 1 else 0 end) as m09
					,      sum(case when date_format(t01_sugup_date, '%m') = '10' and t01_del_yn = 'N' then 1 else 0 end) as m10
					,      sum(case when date_format(t01_sugup_date, '%m') = '11' and t01_del_yn = 'N' then 1 else 0 end) as m11
					,      sum(case when date_format(t01_sugup_date, '%m') = '12' and t01_del_yn = 'N' then 1 else 0 end) as m12
					,      case when t01_sugup_date > case when ifnull(act_cls_dt_from, '') != '' then act_cls_dt_from else '00000000' end then 'Y' else 'N' end as act_yn

					,      sum(case when right(closing_yymm, 2) = '01' and act_cls_flag = 'Y' then 1 else 0 end) as act01
					,      sum(case when right(closing_yymm, 2) = '02' and act_cls_flag = 'Y' then 1 else 0 end) as act02
					,      sum(case when right(closing_yymm, 2) = '03' and act_cls_flag = 'Y' then 1 else 0 end) as act03
					,      sum(case when right(closing_yymm, 2) = '04' and act_cls_flag = 'Y' then 1 else 0 end) as act04
					,      sum(case when right(closing_yymm, 2) = '05' and act_cls_flag = 'Y' then 1 else 0 end) as act05
					,      sum(case when right(closing_yymm, 2) = '06' and act_cls_flag = 'Y' then 1 else 0 end) as act06
					,      sum(case when right(closing_yymm, 2) = '07' and act_cls_flag = 'Y' then 1 else 0 end) as act07
					,      sum(case when right(closing_yymm, 2) = '08' and act_cls_flag = 'Y' then 1 else 0 end) as act08
					,      sum(case when right(closing_yymm, 2) = '09' and act_cls_flag = 'Y' then 1 else 0 end) as act09
					,      sum(case when right(closing_yymm, 2) = '10' and act_cls_flag = 'Y' then 1 else 0 end) as act10
					,      sum(case when right(closing_yymm, 2) = '11' and act_cls_flag = 'Y' then 1 else 0 end) as act11
					,      sum(case when right(closing_yymm, 2) = '12' and act_cls_flag = 'Y' then 1 else 0 end) as act12

					,      sum(case when date_format(t01_sugup_date, '%m') = '01' and t01_status_gbn = '1' and t01_del_yn = 'N' then 1 else 0 end) as stat01
					,      sum(case when date_format(t01_sugup_date, '%m') = '02' and t01_status_gbn = '1' and t01_del_yn = 'N' then 1 else 0 end) as stat02
					,      sum(case when date_format(t01_sugup_date, '%m') = '03' and t01_status_gbn = '1' and t01_del_yn = 'N' then 1 else 0 end) as stat03
					,      sum(case when date_format(t01_sugup_date, '%m') = '04' and t01_status_gbn = '1' and t01_del_yn = 'N' then 1 else 0 end) as stat04
					,      sum(case when date_format(t01_sugup_date, '%m') = '05' and t01_status_gbn = '1' and t01_del_yn = 'N' then 1 else 0 end) as stat05
					,      sum(case when date_format(t01_sugup_date, '%m') = '06' and t01_status_gbn = '1' and t01_del_yn = 'N' then 1 else 0 end) as stat06
					,      sum(case when date_format(t01_sugup_date, '%m') = '07' and t01_status_gbn = '1' and t01_del_yn = 'N' then 1 else 0 end) as stat07
					,      sum(case when date_format(t01_sugup_date, '%m') = '08' and t01_status_gbn = '1' and t01_del_yn = 'N' then 1 else 0 end) as stat08
					,      sum(case when date_format(t01_sugup_date, '%m') = '09' and t01_status_gbn = '1' and t01_del_yn = 'N' then 1 else 0 end) as stat09
					,      sum(case when date_format(t01_sugup_date, '%m') = '10' and t01_status_gbn = '1' and t01_del_yn = 'N' then 1 else 0 end) as stat10
					,      sum(case when date_format(t01_sugup_date, '%m') = '11' and t01_status_gbn = '1' and t01_del_yn = 'N' then 1 else 0 end) as stat11
					,      sum(case when date_format(t01_sugup_date, '%m') = '12' and t01_status_gbn = '1' and t01_del_yn = 'N' then 1 else 0 end) as stat12

					,     (select count(*) from t13sugupja where t13_ccode = t01_ccode and t13_mkind = t01_mkind and t13_jumin = t01_jumin and t13_pay_date = '".$find_year."01' and t13_type = '2') as conf01
					,     (select count(*) from t13sugupja where t13_ccode = t01_ccode and t13_mkind = t01_mkind and t13_jumin = t01_jumin and t13_pay_date = '".$find_year."02' and t13_type = '2') as conf02
					,     (select count(*) from t13sugupja where t13_ccode = t01_ccode and t13_mkind = t01_mkind and t13_jumin = t01_jumin and t13_pay_date = '".$find_year."03' and t13_type = '2') as conf03
					,     (select count(*) from t13sugupja where t13_ccode = t01_ccode and t13_mkind = t01_mkind and t13_jumin = t01_jumin and t13_pay_date = '".$find_year."04' and t13_type = '2') as conf04
					,     (select count(*) from t13sugupja where t13_ccode = t01_ccode and t13_mkind = t01_mkind and t13_jumin = t01_jumin and t13_pay_date = '".$find_year."05' and t13_type = '2') as conf05
					,     (select count(*) from t13sugupja where t13_ccode = t01_ccode and t13_mkind = t01_mkind and t13_jumin = t01_jumin and t13_pay_date = '".$find_year."06' and t13_type = '2') as conf06
					,     (select count(*) from t13sugupja where t13_ccode = t01_ccode and t13_mkind = t01_mkind and t13_jumin = t01_jumin and t13_pay_date = '".$find_year."07' and t13_type = '2') as conf07
					,     (select count(*) from t13sugupja where t13_ccode = t01_ccode and t13_mkind = t01_mkind and t13_jumin = t01_jumin and t13_pay_date = '".$find_year."08' and t13_type = '2') as conf08
					,     (select count(*) from t13sugupja where t13_ccode = t01_ccode and t13_mkind = t01_mkind and t13_jumin = t01_jumin and t13_pay_date = '".$find_year."09' and t13_type = '2') as conf09
					,     (select count(*) from t13sugupja where t13_ccode = t01_ccode and t13_mkind = t01_mkind and t13_jumin = t01_jumin and t13_pay_date = '".$find_year."10' and t13_type = '2') as conf10
					,     (select count(*) from t13sugupja where t13_ccode = t01_ccode and t13_mkind = t01_mkind and t13_jumin = t01_jumin and t13_pay_date = '".$find_year."11' and t13_type = '2') as conf11
					,     (select count(*) from t13sugupja where t13_ccode = t01_ccode and t13_mkind = t01_mkind and t13_jumin = t01_jumin and t13_pay_date = '".$find_year."12' and t13_type = '2') as conf12
					  from m03sugupja
					 inner join t01iljung
						on t01_ccode         = m03_ccode
					   and t01_mkind         = m03_mkind
					   and t01_jumin         = m03_jumin
					   and t01_del_yn        = 'N'
					   and t01_sugup_date like '$find_year%'
					  left join closing_progress
						on closing_progress.org_no                = t01_ccode
					   and left(closing_progress.closing_yymm, 4) = left(t01_sugup_date, 4)
					 where m03_ccode = '$code' $wsl
					 group by m03_ccode, m03_jumin, m03_name
					 order by m03_name
					 /*limit $pageCount, $item_count*/";
		}else{
			$sql = "select m02_ccode as code
					,      min(m02_mkind) as kind
					,      m02_yjumin as jumin
					,      m02_yname as name
					,      dept_nm
					,      left(m02_yipsail, 6) as i_date
					,      left(case when length(m02_ytoisail) = 8 then m02_ytoisail else '99999999' end, 6) as o_date
					,      sum(case when date_format(t01_sugup_date, '%m') = '01' and t01_del_yn = 'N' then 1 else 0 end) as m01
					,      sum(case when date_format(t01_sugup_date, '%m') = '02' and t01_del_yn = 'N' then 1 else 0 end) as m02
					,      sum(case when date_format(t01_sugup_date, '%m') = '03' and t01_del_yn = 'N' then 1 else 0 end) as m03
					,      sum(case when date_format(t01_sugup_date, '%m') = '04' and t01_del_yn = 'N' then 1 else 0 end) as m04
					,      sum(case when date_format(t01_sugup_date, '%m') = '05' and t01_del_yn = 'N' then 1 else 0 end) as m05
					,      sum(case when date_format(t01_sugup_date, '%m') = '06' and t01_del_yn = 'N' then 1 else 0 end) as m06
					,      sum(case when date_format(t01_sugup_date, '%m') = '07' and t01_del_yn = 'N' then 1 else 0 end) as m07
					,      sum(case when date_format(t01_sugup_date, '%m') = '08' and t01_del_yn = 'N' then 1 else 0 end) as m08
					,      sum(case when date_format(t01_sugup_date, '%m') = '09' and t01_del_yn = 'N' then 1 else 0 end) as m09
					,      sum(case when date_format(t01_sugup_date, '%m') = '10' and t01_del_yn = 'N' then 1 else 0 end) as m10
					,      sum(case when date_format(t01_sugup_date, '%m') = '11' and t01_del_yn = 'N' then 1 else 0 end) as m11
					,      sum(case when date_format(t01_sugup_date, '%m') = '12' and t01_del_yn = 'N' then 1 else 0 end) as m12
					,      case when t01_sugup_date > case when ifnull(act_cls_dt_from, '') != '' then act_cls_dt_from else '00000000' end then 'Y' else 'N' end as act_yn

					,      sum(case when date_format(t01_sugup_date, '%m') = '01' and t01_status_gbn = '1' and t01_del_yn = 'N' then 1 else 0 end) as stat01
					,      sum(case when date_format(t01_sugup_date, '%m') = '02' and t01_status_gbn = '1' and t01_del_yn = 'N' then 1 else 0 end) as stat02
					,      sum(case when date_format(t01_sugup_date, '%m') = '03' and t01_status_gbn = '1' and t01_del_yn = 'N' then 1 else 0 end) as stat03
					,      sum(case when date_format(t01_sugup_date, '%m') = '04' and t01_status_gbn = '1' and t01_del_yn = 'N' then 1 else 0 end) as stat04
					,      sum(case when date_format(t01_sugup_date, '%m') = '05' and t01_status_gbn = '1' and t01_del_yn = 'N' then 1 else 0 end) as stat05
					,      sum(case when date_format(t01_sugup_date, '%m') = '06' and t01_status_gbn = '1' and t01_del_yn = 'N' then 1 else 0 end) as stat06
					,      sum(case when date_format(t01_sugup_date, '%m') = '07' and t01_status_gbn = '1' and t01_del_yn = 'N' then 1 else 0 end) as stat07
					,      sum(case when date_format(t01_sugup_date, '%m') = '08' and t01_status_gbn = '1' and t01_del_yn = 'N' then 1 else 0 end) as stat08
					,      sum(case when date_format(t01_sugup_date, '%m') = '09' and t01_status_gbn = '1' and t01_del_yn = 'N' then 1 else 0 end) as stat09
					,      sum(case when date_format(t01_sugup_date, '%m') = '10' and t01_status_gbn = '1' and t01_del_yn = 'N' then 1 else 0 end) as stat10
					,      sum(case when date_format(t01_sugup_date, '%m') = '11' and t01_status_gbn = '1' and t01_del_yn = 'N' then 1 else 0 end) as stat11
					,      sum(case when date_format(t01_sugup_date, '%m') = '12' and t01_status_gbn = '1' and t01_del_yn = 'N' then 1 else 0 end) as stat12

					,     (select count(*) from salary_basic where salary_basic.org_no = t01_ccode and salary_jumin = t01_yoyangsa_id1 and salary_yymm = '".$find_year."01') as conf01
					,     (select count(*) from salary_basic where salary_basic.org_no = t01_ccode and salary_jumin = t01_yoyangsa_id1 and salary_yymm = '".$find_year."02') as conf02
					,     (select count(*) from salary_basic where salary_basic.org_no = t01_ccode and salary_jumin = t01_yoyangsa_id1 and salary_yymm = '".$find_year."03') as conf03
					,     (select count(*) from salary_basic where salary_basic.org_no = t01_ccode and salary_jumin = t01_yoyangsa_id1 and salary_yymm = '".$find_year."04') as conf04
					,     (select count(*) from salary_basic where salary_basic.org_no = t01_ccode and salary_jumin = t01_yoyangsa_id1 and salary_yymm = '".$find_year."05') as conf05
					,     (select count(*) from salary_basic where salary_basic.org_no = t01_ccode and salary_jumin = t01_yoyangsa_id1 and salary_yymm = '".$find_year."06') as conf06
					,     (select count(*) from salary_basic where salary_basic.org_no = t01_ccode and salary_jumin = t01_yoyangsa_id1 and salary_yymm = '".$find_year."07') as conf07
					,     (select count(*) from salary_basic where salary_basic.org_no = t01_ccode and salary_jumin = t01_yoyangsa_id1 and salary_yymm = '".$find_year."08') as conf08
					,     (select count(*) from salary_basic where salary_basic.org_no = t01_ccode and salary_jumin = t01_yoyangsa_id1 and salary_yymm = '".$find_year."09') as conf09
					,     (select count(*) from salary_basic where salary_basic.org_no = t01_ccode and salary_jumin = t01_yoyangsa_id1 and salary_yymm = '".$find_year."10') as conf10
					,     (select count(*) from salary_basic where salary_basic.org_no = t01_ccode and salary_jumin = t01_yoyangsa_id1 and salary_yymm = '".$find_year."11') as conf11
					,     (select count(*) from salary_basic where salary_basic.org_no = t01_ccode and salary_jumin = t01_yoyangsa_id1 and salary_yymm = '".$find_year."12') as conf12
					  from m02yoyangsa
					 inner join t01iljung
						on t01_ccode         = m02_ccode
					   and t01_mkind         = m02_mkind
					   and t01_yoyangsa_id1  = m02_yjumin
					   and t01_del_yn        = 'N'
					   and t01_sugup_date like '$find_year%'
					  left join dept
					    on dept.org_no   = m02_ccode
					   and dept.dept_cd  = m02_dept_cd
					   and dept.del_flag = 'N'
					  left join closing_progress
						on closing_progress.org_no       = t01_ccode
					   and closing_progress.closing_yymm = left(t01_sugup_date, 6)
					 where m02_ccode = '$code'
					   and m02_del_yn = 'N' $wsl
					 group by m02_ccode, m02_yjumin, m02_yname, dept_nm
					 order by m02_yname
					 /*limit $pageCount, $item_count*/";
		}

		$conn->query($sql);
		$conn->fetch();
		$row_count = $conn->row_count();

		if ($row_count > 0){
			for($i=0; $i<$row_count; $i++){
				$mst[$i] = $conn->select_row($i);
			}

			$mst_count = sizeof($mst);

			$conn->row_free();

			for($i=0; $i<$mst_count; $i++){?>
				<tr>
					<td class="center"><?=$pageCount + ($i + 1);?></td>
					<td class="left"><?=$mst[$i]['name'];?></td>
					<?
						if ($mode == 1 || $mode == 2){?>
							<td class="left"><?=$conn->kind_name_svc($mst[$i]['kind']);?></td><?
						}else{?>
							<td class="left"><?=$mst[$i]['dept_nm'];?></td><?
						}
					?>
					<td class="left last" style="padding-top:3px;">
						<table>
							<tr>
							<?
								for($j=1; $j<=12; $j++){
									$mon = ($j < 10 ? '0' : '').$j;

									$class = 'my_month ';

									if ($mst[$i]['i_date'] <= $find_year.$mon && $mst[$i]['o_date'] >= $find_year.$mon){
										if ($mst[$i]['m'.$mon] > 0){
											if ($mst[$i]['conf'.$mon] > 0 ||
												$mst[$i]['act'.$mon] > 0){
												$class .= 'my_month_y ';
											}else{
												if ($mst[$i]['act_yn'] == 'Y'){
													if ($mst[$i]['m'.$mon] == $mst[$i]['stat'.$mon]){
														$class .= 'my_month_r ';
													}else{
														$class .= 'my_month_g ';
													}
												}else{
													$class .= 'my_month_2 ';
												}
											}
											$text = '<a href="#" onclick="work_list(\''.$mst[$i]['code'].'\',\''.$mst[$i]['kind'].'\',\''.$find_year.'\',\''.$mon.'\',\''.$ed->en($mst[$i]['jumin']).'\');">'.$j.'월</a>';
										}else{
											$class .= 'my_month_2 ';
											$text   = '<font color="#7c7c7c">'.$j.'월</font>';
										}
									}else{
										$text = '&nbsp;';
									}?>
									<td class="<?=$class;?>" style="border:none; text-align:center;"><?=$text;?></td><?
								}
							?>
							</tr>
						</table>
					</td>
				</tr><?;
			}
		}else{?>
			<tr>
				<td class="center last" colspan="5">::<?=$myF->message('nodata','N');?>::</td>
			</tr><?
		}

		$conn->row_free();
	?>
	</tbody>
	<tbody>
		<tr>
			<td class="bottom last" colspan="5">
				<div style="text-align:left;">
					<div style="position:absolute; width:auto; padding-left:10px;">검색된 전체 갯수 : <?=number_format($row_count);?></div>
					<div style="width:100%; text-align:center;">
					<?
						//$paging = new YsPaging($params);
						//$paging->printPaging();
					?>
					</div>
				</div>
			</td>
		</tr>
	</tbody>
</table>

<input type="hidden" name="code"  value="">
<input type="hidden" name="kind"  value="">
<input type="hidden" name="year"  value="">
<input type="hidden" name="month" value="">
<input type="hidden" name="jumin" value="">
<input type="hidden" name="page"  value="<?=$page;?>">
<input type="hidden" name="mode"  value="<?=$mode;?>">

</form>

<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>