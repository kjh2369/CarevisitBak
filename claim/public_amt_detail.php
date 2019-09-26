<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");

	if ($gDomain == 'kdolbom.net'){
		include('../inc/_error.php');
		include_once("../inc/_body_footer.php");
		include_once("../inc/_footer.php");
		exit;
	}

	$code	= $_POST['code'];
	$kind	= $_POST['kind'];
	$year	= $_POST['year'];
	$month	= $_POST['month'];

	if (empty($code))  $code  = $_SESSION['userCenterCode'];
	if (empty($kind))  $kind  = $conn->center_kind($code);
	if (empty($year))  $year  = date('Y', mktime());
	if (empty($month)) $month = date('m', mktime());

	$init_year = $myF->year();
?>

<script src="../js/claim.js" type="text/javascript"></script>
<script language='javascript'>
<!--

var f = null;

function before(){
	f.action = 'public_amt_list.php';
	f.submit();
}

function set_month(month){
	f.month.value = (parseInt(month, 10) < 10 ? '0' : '') + parseInt(month, 10);
	f.action = 'public_amt_detail.php';
	f.submit();
}

window.onload = function(){
	f = document.f;
}

-->
</script>

<form name="f" method="post">

<div class="title">공단부담금 내역</div>

<table class="my_table my_border">
	<colgroup>
		<col width="35px">
		<col width="70px">
		<col>
		<col width="50px">
	</colgroup>
	<tbody>
		<tr>
			<th>년도</th>
			<td class="last">
			<?
				echo '<select name=\'year\' style=\'width:auto;\'>';

				for($i=$init_year[0]; $i<=$init_year[1]; $i++){
					echo '<option value=\''.$i.'\' '.($i == $year ? 'selected' : '').'>'.$i.'</option>';
				}

				echo '</select>년';
			?>
			</td>
			<td class="left last">
			<?
				for($i=1; $i<=12; $i++){
					$class = 'my_month ';

					if ($i == intval($month)){
						$class .= 'my_month_y ';
						$color  = 'color:#000000;';
					}else{
						$class .= 'my_month_1 ';
						$color  = 'color:#666666;';
					}

					$text = '<a href="#" onclick="set_month('.$i.');">'.$i.'월</a>';

					if ($i == 12){
						$style = 'float:left;';
					}else{
						$style = 'float:left; margin-right:3px;';
					}

					echo '<div class=\''.$class.'\' style=\''.$style.'\'>'.$text.'</div>';
				}
			?>
			</td>
			<td class="right last">
				<span class="btn_pack m icon"><span class="before"></span><button type="button" onclick="before();">이전</button></span>
			</td>
		</tr>
	</tbody>
</table>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="80px">
		<col width="150px">
		<col width="100px">
		<col width="120px">
		<col width="100px" span="2">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">수급자명</th>
			<th class="head">구분</th>
			<th class="head">한도금액</th>
			<th class="head">실적금액</th>
			<th class="head">공단청구금액</th>
			<th class="head">본인부담금액</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody>
	<?
		if ($lbTestMode){
			$sql = 'select distinct name as client_name
					,      t13_jumin as client_jumin
					,      kind.rate as bonin_yul
					,      concat(case kind when \'3\' then \'기초수급권자\'
											when \'2\' then \'의료수급권자\'
											when \'4\' then \'경감대상자\' else \'일반\' end ,\'(\', cast(ifnull(kind.rate, case when ylvl is null then 100 else 15 end) as char), \')\') as gubun


					,      sum(t13_max_amt) as max_amt
					,      sum(t13_suga_tot4) as suga_amt
					,      sum(t13_chung_amt4) as chung_amt
					,      sum(t13_bonbu_tot4) as bonin_amt
					,      kind.kind as bonin_kind
					  from t13sugupja
					 inner join (
						   select m03_ccode as code
						   ,      min(m03_mkind)
						   ,      m03_jumin as jumin
						   ,      m03_name as name
						   ,      ifnull(lvl.lvl, \'9\') as ylvl
						   ,      ifnull(amt.amt, 0) as kupyeo_max
						   ,      case when ifnull(clm.amt, 0) > 0 then clm.amt else ifnull(amt.amt, 0) end as kupyeo_1
						   ,      svc.from_dt as sdate
						   ,      svc.to_dt as edate
							 from m03sugupja as mst
						    inner join (
								  select jumin
								  ,      date_format(min(from_dt), \'%Y%m%d\') as from_dt
								  ,      date_format(max(to_dt),   \'%Y%m%d\') as to_dt
								   from client_his_svc
								  where org_no = \''.$code.'\'
								    and svc_cd = \'0\'
								    and date_format(from_dt, \'%Y%m\') <= \''.$year.$month.'\'
								    and date_format(to_dt,   \'%Y%m\') >= \''.$year.$month.'\'
								  group by jumin
								  ) as svc
							   on svc.jumin = mst.m03_jumin
							 left join (
								  select jumin
								  ,      min(level) as lvl
								    from client_his_lvl
								   where org_no = \''.$code.'\'
									 and date_format(from_dt, \'%Y%m\') <= \''.$year.$month.'\'
									 and date_format(to_dt,   \'%Y%m\') >= \''.$year.$month.'\' '.$wsl.'
								  group by jumin
								  ) as lvl
							   on lvl.jumin = mst.m03_jumin
							 left join (
								  select m91_code as cd
								  ,      m91_kupyeo as amt
								    from m91maxkupyeo
								   where left(m91_sdate, 6) <= \''.$year.$month.'\'
									 and left(m91_edate, 6) >= \''.$year.$month.'\'
								  ) as amt
							   on amt.cd = lvl.lvl
							 left join (
								  select jumin
								  ,      amt
								    from client_his_limit
								   where org_no = \''.$code.'\'
									 and date_format(from_dt, \'%Y%m\') <= \''.$year.$month.'\'
									 and date_format(to_dt,   \'%Y%m\') >= \''.$year.$month.'\'
								   order by jumin, seq desc
								  ) as clm
							   on clm.jumin = mst.m03_jumin
						    where m03_ccode = \''.$code.'\'
						    group by m03_jumin
						   ) as sugupja
						on t13_jumin = jumin
					   and t13_pay_date between left(sdate, 6) and left(edate, 6)
					  left join (
						  select jumin
						  ,      seq
						  ,      kind
						  ,      rate
							from client_his_kind
						   where org_no = \''.$code.'\'
							 and date_format(from_dt, \'%Y%m\') <= \''.$year.$month.'\'
							 and date_format(to_dt,   \'%Y%m\') >= \''.$year.$month.'\'
						   group by jumin
						   order by jumin, seq desc
						  ) as kind
					    on t13_jumin = kind.jumin
					   /*and t13_bonin_yul = kind.rate*/
					 where t13_ccode    = \''.$code.'\'
					   and t13_mkind    = \''.$kind.'\'
					   and t13_pay_date = \''.$year.$month.'\'
					   and t13_type     = \'2\'
					 group by client_jumin
					 order by name';

		}else{
			$sql = "select distinct m03_name as client_name
					,      t13_jumin as client_jumin
					,      t13_bonin_yul as bonin_yul
					,      concat(STP.m81_name,'(', m03_bonin_yul, ')') as gubun
					,      t13_max_amt as max_amt
					,      t13_suga_tot4 as suga_amt
					,      t13_chung_amt4 as chung_amt
					,      t13_bonbu_tot4 as bonin_amt
					,      m03_skind as bonin_kind
					  from t13sugupja
					 inner join (
						   select m03_name, m03_jumin, m03_ylvl, m03_skind, m03_bonin_yul, m03_sdate, m03_edate
							 from m03sugupja
							where m03_ccode = '$code'
							  and m03_mkind = '$kind'
							union all
						   select m03_name, m31_jumin, m31_level, m31_kind, m31_bonin_yul, m31_sdate, m31_edate
							 from m31sugupja
							inner join m03sugupja
							   on m31_ccode = m03_ccode
							  and m31_mkind = m03_mkind
							  and m31_jumin = m03_jumin
							where m31_ccode = '$code'
							  and m31_mkind = '$kind'
						   ) as sugupja
						on t13_jumin = m03_jumin
					   and t13_pay_date between left(m03_sdate, 6) and left(m03_edate, 6)
					 inner join m81gubun as STP
						on STP.m81_gbn  = 'STP'
					   and STP.m81_code = m03_skind
					 where t13_ccode    = '$code'
					   and t13_mkind    = '$kind'
					   and t13_pay_date = '$year$month'
					   and t13_type     = '2'
					 order by m03_name ";
		}

		//if($debug) echo nl2br($sql); 

		$conn->fetch_type = 'assoc';
		$conn->query($sql);
		$conn->fetch();

		$row_count = $conn->row_count();
		$html = '';

		for($i=0; $i<$row_count; $i++){
			$row = $conn->select_row($i);

			$html .= '<tr>';
			$html .= '<td class=\'center\'>'.($i+1).'</td>';
			$html .= '<td class=\'left\'>'.$row['client_name'].'</td>';
			$html .= '<td class=\'left\'>'.$row['gubun'].'</td>';
			$html .= '<td class=\'right\'>'.number_format($row['max_amt']).'</td>';
			$html .= '<td class=\'right\'>'.number_format($row['suga_amt']).'</td>';
			$html .= '<td class=\'right\'>'.number_format($row['chung_amt']).'</td>';
			$html .= '<td class=\'right\'>'.number_format($row['bonin_amt']).'</td>';
			$html .= '<td class=\'left last\'>';
			$html .= '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'printPerson("'.$code.'","'.$kind.'","'.$year.$month.'","'.$ed->en($row['client_jumin']).'","'.$row['bonin_kind'].'");\'>출력</button></span>';
			$html .= '</td>';
			$html .= '</tr>';

			$max_amt   += intval($row['max_amt']);
			$suga_amt  += intval($row['suga_amt']);
			$chung_amt += intval($row['chung_amt']);
			$bonin_amt += intval($row['bonin_amt']);
		}

		$conn->row_free();

		//실적한도비율
		if($max_amt != ''){
			$suga_max_rate = intval(($suga_amt/$max_amt)*100).'%';
			$val = ' (<font color="blue">'.$suga_max_rate.'</font>)';
		}else {
			$val = '';
		}

		$tot_html  = '';
		$tot_html .= '<tr>';
		$tot_html .= '<td class=\'right bold my_bg_gray\' colspan=\'3\'>합계</td>';
		$tot_html .= '<td class=\'right bold my_bg_gray\'>'.number_format($max_amt).'</td>';
		$tot_html .= '<td class=\'right bold my_bg_gray\'>'.number_format($suga_amt).$val.'</td>';
		$tot_html .= '<td class=\'right bold my_bg_gray\'>'.number_format($chung_amt).'</td>';
		$tot_html .= '<td class=\'right bold my_bg_gray\'>'.number_format($bonin_amt).'</td>';
		$tot_html .= '<td class=\'last my_bg_gray\'>&nbsp;</td>';
		$tot_html .= '</tr>';

		echo $tot_html;
		echo $html;
	?>
	</tbody>
	<tbody>
		<tr>
			<td class="left last bottom" colspan="8"><?=$myF->message($row_count, 'N');?></td>
		</tr>
	</tbody>
</table>

<input type="hidden" name="code"   value="<?=$code;?>">
<input type="hidden" name="kind"   value="<?=$kind;?>">
<input type="hidden" name="month"  value="<?=$month;?>">

</form>

<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>