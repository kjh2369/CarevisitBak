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


	/*********************************************************

		등급별 한도금액

	*********************************************************/
	$sql = 'select m91_code as cd
			,      m91_kupyeo as pay
			,      left(m91_sdate,6) as f_yymm
			,      left(m91_edate,6) as t_yymm
			  from m91maxkupyeo
			 where left(m91_sdate, 4) <= \''.$find_year.'\'
			   and left(m91_edate, 4) >= \''.$find_year.'\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		$tmpLimitPay[$row['cd']] = array(
				'cd'   => $row['cd']
			,	'pay'  => $row['pay']
			,	'from' => $row['f_yymm']
			,	'to'   => $row['t_yymm']
		);
	}

	$conn->row_free();

	if ($lbTestMode){
		/*********************************************************
			선택년도별 수급자 등급별 한도금액
		*********************************************************/
		$sql = 'select svc.jumin
				,      svc.svc_cd
				,      lvl.level
				,      date_format(lvl.from_dt,\'%Y%m\') as f_yymm
				,      date_format(lvl.to_dt,\'%Y%m\') as t_yymm
				  from client_his_svc as svc
				  left join client_his_lvl as lvl
					on lvl.jumin            = svc.jumin
				 where svc.org_no           = \''.$code.'\'
				   and left(svc.from_dt,4) <= \''.$find_year.'\'
				   and left(svc.to_dt,4)   >= \''.$find_year.'\'
				 order by svc.jumin';

		$conn->query($sql);
		$conn->fetch();

		$rowCount = $conn->row_count();

		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			if (!isset($laClientLvl[$row['jumin']])){
				$laClientLvl[$row['jumin']] = array(1=>'',2=>'',3=>'',4=>'',5=>'',6=>'',7=>'',8=>'',9=>'',10=>'',11=>'',12=>'');
			}

			for($j=1; $j<=12; $j++){
				$yymm = $find_year.($j < 10 ? '0' : '').$j;

				if ($yymm >= $row['f_yymm'] && $yymm <= $row['t_yymm'])
					$laClientLvl[$row['jumin']][$j] = $row['level'];
			}
		}

		$conn->row_free();

		foreach($laClientLvl as $jumin => $row){
			foreach($row as $mon => $lvl){
				$yymm = $find_year.($mon < 10 ? '0' : '').$mon;

				if ($yymm >= $tmpLimitPay[$lvl]['from'] && $yymm <= $tmpLimitPay[$lvl]['to'])
					$laLimitPay[$jumin][$mon] = $tmpLimitPay[$lvl]['pay'];
			}
		}
	}else{
		$sql = 'select jumin
				,      lvl
				,      left(f_dt, 6) as f_yymm
				,      left(t_dt, 6) as t_yymm
				  from (
					   select min(m03_mkind) as kind
					   ,      m03_jumin as jumin
					   ,      m03_ylvl as lvl
					   ,      m03_sdate as f_dt
					   ,      m03_edate as t_dt
						 from m03sugupja
						where m03_ccode = \''.$code.'\'
						  and m03_mkind = \'0\'
						group by m03_jumin
						union all
					   select min(m31_mkind) as kind
					   ,      m31_jumin as jumin
					   ,      m31_level as lvl
					   ,      m31_sdate as f_dt
					   ,      m31_edate as t_dt
						 from m31sugupja
						where m31_ccode = \''.$code.'\'
						  and m31_mkind = \'0\'
						group by m31_jumin
					   ) as mem
				 where left(f_dt, 4) <= \''.$find_year.'\'
				   and left(t_dt, 4) >= \''.$find_year.'\'
				 order by jumin';

		$conn->query($sql);
		$conn->fetch();

		$rowCount = $conn->row_count();

		for($i=0; $i<$rowCount; $i++){
			$row = $conn->select_row($i);

			for($j=1; $j<=12; $j++){
				$yymm = $find_year.($j<10?'0':'').$j;

				if ($yymm >= $tmpLimitPay[$row['lvl']]['from'] && $yymm <= $tmpLimitPay[$row['lvl']]['to']){
					$arrLimitPay[$row['jumin']][$j] = $tmpLimitPay[$row['lvl']]['pay'];
				}
			}
		}

		$conn->row_free();
	}
?>

<script src="../js/work.js" type="text/javascript"></script>
<script language='javascript'>
<!--
function search(){
	var f = document.f;

	f.submit();
}

function work_list(code, kind, year, month, jumin, abVal){
	var f = document.f;

	f.code.value  = code;
	f.kind.value  = kind;
	f.year.value  = year;
	f.month.value = month;
	f.jumin.value = jumin;

	if (abVal){
		f.action = 'result_detail_new.php';
	}else{
		f.action = 'result_detail.php';
	}
	f.submit();

	return false;
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
					$kind_list = $conn->kind_list($code, $gHostSvc['voucher']);

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
		<col width="100px"><?
		if ($mode == 1 || $mode == 2){?>
			<col width="100px">
			<col width="50px">
			<col width="100px"><?
		}else { ?>
			<col width="100px"><?
		}?>
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head"><?=$title;?></th>
			<?
				if ($mode == 1 || $mode == 2){?>
					<th class="head">제공서비스</th>
					<th class="head">등급</th>
					<th class="head">요양보호사</th><?
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
			$sql = "select sum(case when right(closing_yymm, 2) = '01' and act_cls_flag = 'Y' then 1 else 0 end) as act01
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
					  from closing_progress
					 where org_no                = '".$code."'
					   and left(closing_yymm, 4) = '".$find_year."'";

			$actFlag = $conn->_fetch_array($sql);



			if ($lbTestMode){
				/*********************************************************
					고객 계약 이력
				*********************************************************/
				$sql = 'select jumin
						,      svc_cd
						,      seq
						,      date_format(from_dt,\'%Y%m%d\') as from_dt
						,      date_format(to_dt,\'%Y%m%d\') as to_dt
						  from client_his_svc
						 where org_no           = \''.$code.'\'
						   and left(from_dt,4) <= \''.$find_year.'\'
						   and left(to_dt,4)   >= \''.$find_year.'\'';

				$conn->query($sql);
				$conn->fetch();

				$rowCount = $conn->row_count();

				for($i=0; $i<$rowCount; $i++){
					$row = $conn->select_row($i);

					if ($row['svc_cd'] == '0' || $row['svc_cd'] == '4')
						$laClt[$row['jumin']]['lvl'] = '일반';

					if (empty($laClt[$row['jumin']]['i_date']) || $laClt[$row['jumin']]['i_date'] > $row['from_dt']) $laClt[$row['jumin']]['i_date'] = $row['from_dt'];
					if (empty($laClt[$row['jumin']]['o_date']) || $laClt[$row['jumin']]['o_date'] < $row['to_dt']) $laClt[$row['jumin']]['o_date'] = $row['to_dt'];
				}

				$conn->row_free();


				/*********************************************************
					고객 구분 이력
				*********************************************************/
				$sql = 'select jumin
						,      seq
						,      case level when \'9\' then \'일반\' else concat(level,\'등급\') end as lvl
						,      date_format(from_dt,\'%Y%m%d\') as from_dt
						,      date_format(to_dt,\'%Y%m%d\') as to_dt
						  from client_his_lvl
						 where org_no           = \''.$code.'\'
						   and svc_cd           = \'0\'
						   and left(from_dt,4) <= \''.$find_year.'\'
						   and left(to_dt,4)   >= \''.$find_year.'\'';

				$conn->query($sql);
				$conn->fetch();

				$rowCount = $conn->row_count();

				for($i=0; $i<$rowCount; $i++){
					$row = $conn->select_row($i);
					$dt  = str_replace('-','',$myF->_getDt($laClt[$row['jumin']]['i_date'],$laClt[$row['jumin']]['o_date']));

					if ($dt >= $row['from_dt'] && $dt <= $row['to_dt'])
						$laClt[$row['jumin']]['lvl'] = $row['lvl'];
				}

				$conn->row_free();

				$sql = 'select jumin
						,      seq
						,      svc_lvl
						,      date_format(from_dt,\'%Y%m%d\') as from_dt
						,      date_format(to_dt,\'%Y%m%d\') as to_dt
						  from client_his_dis
						 where org_no           = \''.$code.'\'
						   and left(from_dt,4) <= \''.$find_year.'\'
						   and left(to_dt,4)   >= \''.$find_year.'\'';

				$conn->query($sql);
				$conn->fetch();

				$rowCount = $conn->row_count();

				for($i=0; $i<$rowCount; $i++){
					$row = $conn->select_row($i);
					$dt  = str_replace('-','',$myF->_getDt($laClt[$row['jumin']]['i_date'],$laClt[$row['jumin']]['o_date']));

					if ($dt >= $row['from_dt'] && $dt <= $row['to_dt']){
						if (empty($laClt[$row['jumin']]['lvl']) || $laClt[$row['jumin']]['lvl'] == '일반')
							$laClt[$row['jumin']]['lvl'] = $row['svc_lvl'].'등급';
					}
				}

				$conn->row_free();
				$wsl = '';

				//이름검색
				if(!empty($find_name)){
					$wsl .= " and m03_name like '%".$find_name."%'";
				}

				$sql = "select t.code
						,      t.jumin
						,	   t.kind
						,      c.name
						,	   c.mem_nm
						,      m01, m02, m03, m04, m05, m06, m07, m08, m09, m10, m11, m12
						,      stat01, stat02, stat03, stat04, stat05, stat06, stat07, stat08, stat09, stat10, stat11, stat12
						,      conf01, conf02, conf03, conf04, conf05, conf06, conf07, conf08, conf09, conf10, conf11, conf12
						,      conf_var01, conf_var02, conf_var03, conf_var04, conf_var05, conf_var06, conf_var07, conf_var08, conf_var09, conf_var10, conf_var11, conf_var12
						  from (
							   select code
							   ,      min(kind) as kind
							   ,      jumin
							   ,      sum(m01) as m01, sum(m02) as m02, sum(m03) as m03, sum(m04) as m04, sum(m05) as m05, sum(m06) as m06, sum(m07) as m07, sum(m08) as m08, sum(m09) as m09, sum(m10) as m10, sum(m11) as m11, sum(m12) as m12
							   ,      sum(stat01) as stat01, sum(stat02) as stat02, sum(stat03) as stat03, sum(stat04) as stat04, sum(stat05) as stat05, sum(stat06) as stat06, sum(stat07) as stat07, sum(stat08) as stat08, sum(stat09) as stat09, sum(stat10) as stat10, sum(stat11) as stat11, sum(stat12) as stat12
							   ,      sum(conf01) as conf01, sum(conf02) as conf02, sum(conf03) as conf03, sum(conf04) as conf04, sum(conf05) as conf05, sum(conf06) as conf06, sum(conf07) as conf07, sum(conf08) as conf08, sum(conf09) as conf09, sum(conf10) as conf10, sum(conf11) as conf11, sum(conf12) as conf12
							   ,      sum(conf_var01) as conf_var01, sum(conf_var02) as conf_var02, sum(conf_var03) as conf_var03, sum(conf_var04) as conf_var04, sum(conf_var05) as conf_var05, sum(conf_var06) as conf_var06, sum(conf_var07) as conf_var07, sum(conf_var08) as conf_var08, sum(conf_var09) as conf_var09, sum(conf_var10) as conf_var10, sum(conf_var11) as conf_var11, sum(conf_var12) as conf_var12
								 from (
									  select t01_ccode as code
									  ,      t01_mkind as kind
									  ,      t01_jumin as jumin

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

									  ,      sum(case when date_format(t01_sugup_date, '%m') = '01' and t01_status_gbn = '1' and t01_del_yn = 'N' and t01_bipay_umu != 'Y' then t01_conf_suga_value else 0 end) as conf_var01
									  ,      sum(case when date_format(t01_sugup_date, '%m') = '02' and t01_status_gbn = '1' and t01_del_yn = 'N' and t01_bipay_umu != 'Y' then t01_conf_suga_value else 0 end) as conf_var02
									  ,      sum(case when date_format(t01_sugup_date, '%m') = '03' and t01_status_gbn = '1' and t01_del_yn = 'N' and t01_bipay_umu != 'Y' then t01_conf_suga_value else 0 end) as conf_var03
									  ,      sum(case when date_format(t01_sugup_date, '%m') = '04' and t01_status_gbn = '1' and t01_del_yn = 'N' and t01_bipay_umu != 'Y' then t01_conf_suga_value else 0 end) as conf_var04
									  ,      sum(case when date_format(t01_sugup_date, '%m') = '05' and t01_status_gbn = '1' and t01_del_yn = 'N' and t01_bipay_umu != 'Y' then t01_conf_suga_value else 0 end) as conf_var05
									  ,      sum(case when date_format(t01_sugup_date, '%m') = '06' and t01_status_gbn = '1' and t01_del_yn = 'N' and t01_bipay_umu != 'Y' then t01_conf_suga_value else 0 end) as conf_var06
									  ,      sum(case when date_format(t01_sugup_date, '%m') = '07' and t01_status_gbn = '1' and t01_del_yn = 'N' and t01_bipay_umu != 'Y' then t01_conf_suga_value else 0 end) as conf_var07
									  ,      sum(case when date_format(t01_sugup_date, '%m') = '08' and t01_status_gbn = '1' and t01_del_yn = 'N' and t01_bipay_umu != 'Y' then t01_conf_suga_value else 0 end) as conf_var08
									  ,      sum(case when date_format(t01_sugup_date, '%m') = '09' and t01_status_gbn = '1' and t01_del_yn = 'N' and t01_bipay_umu != 'Y' then t01_conf_suga_value else 0 end) as conf_var09
									  ,      sum(case when date_format(t01_sugup_date, '%m') = '10' and t01_status_gbn = '1' and t01_del_yn = 'N' and t01_bipay_umu != 'Y' then t01_conf_suga_value else 0 end) as conf_var10
									  ,      sum(case when date_format(t01_sugup_date, '%m') = '11' and t01_status_gbn = '1' and t01_del_yn = 'N' and t01_bipay_umu != 'Y' then t01_conf_suga_value else 0 end) as conf_var11
									  ,      sum(case when date_format(t01_sugup_date, '%m') = '12' and t01_status_gbn = '1' and t01_del_yn = 'N' and t01_bipay_umu != 'Y' then t01_conf_suga_value else 0 end) as conf_var12


										from t01iljung
									   where t01_ccode               = '$code'
										 and left(t01_sugup_date, 4) = '$find_year'
										 and t01_del_yn              = 'N'
									   group by t01_ccode, t01_mkind, t01_jumin
									  ) as t
								group by code, jumin
							   ) as t
						 inner join (
							   select m03_ccode as code
							   ,      min(m03_mkind) as kind
							   ,      m03_jumin as jumin
							   ,      m03_name as name
							   ,      m03_yoyangsa1_nm as mem_nm
							  	 from m03sugupja
								where m03_ccode = '$code'
								 $wsl
								group by m03_ccode, m03_jumin
							   ) as c
							on c.code  = t.code
						   and c.jumin = t.jumin
						 order by c.name";

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
									<td class="left"><?=$conn->kind_name_svc($mst[$i]['kind']);?></td>
									<td class="center"><?=$laClt[$mst[$i]['jumin']]['lvl'];?></td>
									<td class="left"><?=$mst[$i]['mem_nm'];?></td><?
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

											if (substr($laClt[$mst[$i]['jumin']]['i_date'],0,6) <= $find_year.$mon &&
												substr($laClt[$mst[$i]['jumin']]['o_date'],0,6) >= $find_year.$mon){
												if ($mst[$i]['m'.$mon] > 0){
													if ($mst[$i]['conf'.$mon] > 0 || $actFlag[0]['act'.$mon] > 0){
														$class .= 'my_month_y ';
													}else{
														if ($mst[$i]['act_yn'] == 'Y' || $actFlag[0]['act'.$mon] == 0){
															if ($mst[$i]['m'.$mon] == $mst[$i]['stat'.$mon]){
																$class .= 'my_month_r ';
															}else{
																$class .= 'my_month_g ';
															}
														}else{
															$class .= 'my_month_2 ';
														}
													}

													if ($mode == 2){
														if ($mst[$i]['kind'] == '0' && $laLimitPay[$mst[$i]['jumin']][$j] < $mst[$i]['conf_var'.$mon])
															$style = 'color:#ff0000; font-weight:bold;';
														else
															$style = '';
													}

													$text = '<a href="#" onclick="return work_list(\''.$mst[$i]['code'].'\',\''.$mst[$i]['kind'].'\',\''.$find_year.'\',\''.$mon.'\',\''.$ed->en($mst[$i]['jumin']).'\',\''.$lbConfMode.'\');" style=\''.$style.'\'>'.$j.'월</a>';
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
			}else{
				$sql = "select t.code
						,      t.jumin
						,	   t.kind
						,      c.name
						,	   c.mem_nm
						,	   c.lvl_name
						,      c.i_date
						,      c.o_date
						,      m01, m02, m03, m04, m05, m06, m07, m08, m09, m10, m11, m12
						,      stat01, stat02, stat03, stat04, stat05, stat06, stat07, stat08, stat09, stat10, stat11, stat12
						,      conf01, conf02, conf03, conf04, conf05, conf06, conf07, conf08, conf09, conf10, conf11, conf12
						,      conf_var01, conf_var02, conf_var03, conf_var04, conf_var05, conf_var06, conf_var07, conf_var08, conf_var09, conf_var10, conf_var11, conf_var12
						  from (
							   select code
							   ,      min(kind) as kind
							   ,      jumin
							   ,      sum(m01) as m01, sum(m02) as m02, sum(m03) as m03, sum(m04) as m04, sum(m05) as m05, sum(m06) as m06, sum(m07) as m07, sum(m08) as m08, sum(m09) as m09, sum(m10) as m10, sum(m11) as m11, sum(m12) as m12
							   ,      sum(stat01) as stat01, sum(stat02) as stat02, sum(stat03) as stat03, sum(stat04) as stat04, sum(stat05) as stat05, sum(stat06) as stat06, sum(stat07) as stat07, sum(stat08) as stat08, sum(stat09) as stat09, sum(stat10) as stat10, sum(stat11) as stat11, sum(stat12) as stat12
							   ,      sum(conf01) as conf01, sum(conf02) as conf02, sum(conf03) as conf03, sum(conf04) as conf04, sum(conf05) as conf05, sum(conf06) as conf06, sum(conf07) as conf07, sum(conf08) as conf08, sum(conf09) as conf09, sum(conf10) as conf10, sum(conf11) as conf11, sum(conf12) as conf12
							   ,      sum(conf_var01) as conf_var01, sum(conf_var02) as conf_var02, sum(conf_var03) as conf_var03, sum(conf_var04) as conf_var04, sum(conf_var05) as conf_var05, sum(conf_var06) as conf_var06, sum(conf_var07) as conf_var07, sum(conf_var08) as conf_var08, sum(conf_var09) as conf_var09, sum(conf_var10) as conf_var10, sum(conf_var11) as conf_var11, sum(conf_var12) as conf_var12
								 from (
									  select t01_ccode as code
									  ,      t01_mkind as kind
									  ,      t01_jumin as jumin

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

									  ,      sum(case when date_format(t01_sugup_date, '%m') = '01' and t01_status_gbn = '1' and t01_del_yn = 'N' and t01_bipay_umu != 'Y' then t01_conf_suga_value else 0 end) as conf_var01
									  ,      sum(case when date_format(t01_sugup_date, '%m') = '02' and t01_status_gbn = '1' and t01_del_yn = 'N' and t01_bipay_umu != 'Y' then t01_conf_suga_value else 0 end) as conf_var02
									  ,      sum(case when date_format(t01_sugup_date, '%m') = '03' and t01_status_gbn = '1' and t01_del_yn = 'N' and t01_bipay_umu != 'Y' then t01_conf_suga_value else 0 end) as conf_var03
									  ,      sum(case when date_format(t01_sugup_date, '%m') = '04' and t01_status_gbn = '1' and t01_del_yn = 'N' and t01_bipay_umu != 'Y' then t01_conf_suga_value else 0 end) as conf_var04
									  ,      sum(case when date_format(t01_sugup_date, '%m') = '05' and t01_status_gbn = '1' and t01_del_yn = 'N' and t01_bipay_umu != 'Y' then t01_conf_suga_value else 0 end) as conf_var05
									  ,      sum(case when date_format(t01_sugup_date, '%m') = '06' and t01_status_gbn = '1' and t01_del_yn = 'N' and t01_bipay_umu != 'Y' then t01_conf_suga_value else 0 end) as conf_var06
									  ,      sum(case when date_format(t01_sugup_date, '%m') = '07' and t01_status_gbn = '1' and t01_del_yn = 'N' and t01_bipay_umu != 'Y' then t01_conf_suga_value else 0 end) as conf_var07
									  ,      sum(case when date_format(t01_sugup_date, '%m') = '08' and t01_status_gbn = '1' and t01_del_yn = 'N' and t01_bipay_umu != 'Y' then t01_conf_suga_value else 0 end) as conf_var08
									  ,      sum(case when date_format(t01_sugup_date, '%m') = '09' and t01_status_gbn = '1' and t01_del_yn = 'N' and t01_bipay_umu != 'Y' then t01_conf_suga_value else 0 end) as conf_var09
									  ,      sum(case when date_format(t01_sugup_date, '%m') = '10' and t01_status_gbn = '1' and t01_del_yn = 'N' and t01_bipay_umu != 'Y' then t01_conf_suga_value else 0 end) as conf_var10
									  ,      sum(case when date_format(t01_sugup_date, '%m') = '11' and t01_status_gbn = '1' and t01_del_yn = 'N' and t01_bipay_umu != 'Y' then t01_conf_suga_value else 0 end) as conf_var11
									  ,      sum(case when date_format(t01_sugup_date, '%m') = '12' and t01_status_gbn = '1' and t01_del_yn = 'N' and t01_bipay_umu != 'Y' then t01_conf_suga_value else 0 end) as conf_var12


										from t01iljung
									   where t01_ccode               = '".$code."'
										 and left(t01_sugup_date, 4) = '".$find_year."'
										 and t01_del_yn              = 'N'
									   group by t01_ccode, t01_mkind, t01_jumin
									  ) as t
								group by code, jumin
							   ) as t
						 inner join (
							   select m03_ccode as code
							   ,      min(m03_mkind) as kind
							   ,      m03_jumin as jumin
							   ,      m03_name as name
							   ,      m03_yoyangsa1_nm as mem_nm
							   ,      case when m03_mkind = '0' then LVL.m81_name
									  when m03_mkind = '4' then concat(m03sugupja.m03_ylvl, '등급') else '' end as lvl_name
							   ,      left(min(m03_gaeyak_fm), 6) as i_date
							   ,      left(ifnull(max(m03_gaeyak_to),'99999999'),6) as o_date    /*left(case when length(m03_gaeyak_to) = 8 then m03_gaeyak_to else '99999999' end, 6) as o_date*/
								 from m03sugupja
								 left join m81gubun as LVL
								   on LVL.m81_gbn = 'LVL'
								  and LVL.m81_code = m03sugupja.m03_ylvl
								where m03_ccode = '".$code."'
								$wsl
								group by m03_ccode, m03_jumin
							   ) as c
							on c.code  = t.code
						   and c.jumin = t.jumin
						 order by c.name";

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
									<td class="left"><?=$conn->kind_name_svc($mst[$i]['kind']);?></td>
									<td class="center"><?=$mst[$i]['lvl_name'];?></td>
									<td class="left"><?=$mst[$i]['mem_nm'];?></td><?
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
													if ($mst[$i]['conf'.$mon] > 0 || $actFlag[0]['act'.$mon] > 0){
														$class .= 'my_month_y ';
													}else{
														if ($mst[$i]['act_yn'] == 'Y' || $actFlag[0]['act'.$mon] == 0){
															if ($mst[$i]['m'.$mon] == $mst[$i]['stat'.$mon]){
																$class .= 'my_month_r ';
															}else{
																$class .= 'my_month_g ';
															}
														}else{
															$class .= 'my_month_2 ';
														}
													}

													if ($mode == 2){
														if ($mst[$i]['kind'] == '0' && $arrLimitPay[$mst[$i]['jumin']][$j] < $mst[$i]['conf_var'.$mon])
															$style = 'color:#ff0000; font-weight:bold;';
														else
															$style = '';
													}

													$text = '<a href="#" onclick="work_list(\''.$mst[$i]['code'].'\',\''.$mst[$i]['kind'].'\',\''.$find_year.'\',\''.$mon.'\',\''.$ed->en($mst[$i]['jumin']).'\');" style=\''.$style.'\'>'.$j.'월</a>';
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
			}
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
								<td class="left"><?=$conn->kind_name_svc($mst[$i]['kind']);?></td>
								<td class="center"><?=$mst[$i]['lvl_name'];?></td>
								<td class="left"><?=$mst[$i]['mem_nm'];?></td><?
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
												if ($mst[$i]['conf'.$mon] > 0 || $actFlag[0]['act'.$mon] > 0){
													$class .= 'my_month_y ';
												}else{
													if ($mst[$i]['act_yn'] == 'Y' || $actFlag[0]['act'.$mon] == 0){
														if ($mst[$i]['m'.$mon] == $mst[$i]['stat'.$mon]){
															$class .= 'my_month_r ';
														}else{
															$class .= 'my_month_g ';
														}
													}else{
														$class .= 'my_month_2 ';
													}
												}

												if ($mode == 2){
													if ($mst[$i]['kind'] == '0' && $arrLimitPay[$mst[$i]['jumin']][$j] < $mst[$i]['conf_var'.$mon])
														$style = 'color:#ff0000; font-weight:bold;';
													else
														$style = '';
												}

												$text = '<a href="#" onclick="work_list(\''.$mst[$i]['code'].'\',\''.$mst[$i]['kind'].'\',\''.$find_year.'\',\''.$mon.'\',\''.$ed->en($mst[$i]['jumin']).'\');" style=\''.$style.'\'>'.$j.'월</a>';
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
		}
	?>
	</tbody>
	<tbody>
		<tr>
			<td class="bottom last" colspan="5">
				<div style="text-align:left;">
				<?
					if ($lbTestMode){
					}else{?>
						<div style="position:absolute; width:auto; padding-left:10px;">검색된 전체 갯수 : <?=number_format($row_count);?></div>
						<div style="width:100%; text-align:center;"></div><?
					}
				?>
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