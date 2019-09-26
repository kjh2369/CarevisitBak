<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");
	include_once('../inc/_ed.php');

	if ($gDomain == 'kdolbom.net'){
		include('../inc/_error.php');
		include_once("../inc/_body_footer.php");
		include_once("../inc/_footer.php");
		exit;
	}

	$code = $_SESSION['userCenterCode'];
	$kind = $conn->center_kind($code);
	$name = $conn->center_name($code, $kind);

	$init_year = $myF->year();

	$year  = ($_POST['year'] ? $_POST['year'] : date('Y', mktime()));
	$month = date('m', mktime());

	$item_count = 20;
	$page_count = 10;
	$page = $_REQUEST["page"];

	if (!is_numeric($page)) $page = 1;
?>
<script language='javascript'>
<!--

function list(page){
	var f = document.f;

	f.page.value = page;
	f.submit();
}

function set_month(ssn, month){
	var f = document.f;

	f.ssn.value = ssn;
	f.month.value = month;
	f.action = 'client_desire_reg.php';
	f.submit();
}

function set_copy_ym(){
	var f = document.f;
	var year_from  = f.year;
	var month_from = f.copy_from_month;
	var year_to    = document.getElementById('copy_to_year');
	var month_to   = document.getElementById('copy_to_month');

	var month = parseInt(month_from.value, 10) + 1;

	year_to.value = parseInt(year_from.value, 10)

	if (month > 12){
		year_to.value = parseInt(year_to.value, 10) + 1;
		month = 1;
	}

	month_to.value = month;

	document.getElementById('to_month').innerHTML = month;
}

function run_copy(){
	var f = document.f;
	var URL = 'client_desire_copy.php';
	var params = {'code':f.code.value,'kind':f.kind.value,'year_from':f.year.value,'month_from':f.copy_from_month.value,'year_to':f.copy_to_year.value,'month_to':f.copy_to_month.value};
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:params,
			onSuccess:function (responseHttpObj) {
				if (responseHttpObj.responseText == 'Y'){
					list(1);
				}else{
					alert('데이타 복사중 오류가 발생하였습니다. 잠시후 다시 시도하여 주십시오.');
				}
			}
		}
	);
}

window.onload = function(){
	set_copy_ym();
	__init_form(document.f);
}

//-->
</script>

<form name="f" method="post">

<div class="title">수급자 욕구상담</div>

<table class="my_table my_border">
	<colgroup>
		<col width="60px">
		<col width="150px">
		<col width="60px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>기관기호</th>
			<td class="left"><?=$code;?></td>
			<th>기관명</th>
			<td class="left last"><?=$name;?></td>
		</tr>
		<tr>
			<th>년도</th>
			<td>
			<?
				echo '<select name=\'year\' style=\'width:auto;\'>';

				for($i=$init_year[0]; $i<=$init_year[1]; $i++){
					echo '<option value=\''.$i.'\' '.($year == $i ? 'selected' : '').'>'.$i.'</option>';
				}

				echo '</select>년 ';
				echo '<span class=\'btn_pack m icon\'><span class=\'refresh\'></span><button type=\'button\' onclick=\'list(1);\'>조회</button></span>';
			?>
			</td>
			<th>복사</th>
			<td class="last">
			<?
				echo '<select name=\'copy_from_month\' style=\'width:auto;\' onchange=\'set_copy_ym();\'>';

				for($i=1; $i<=12; $i++){
					$mon = ($i<10?'0':'').$i;
					echo '<option value=\''.$i.'\' '.($month == $mon ? 'selected' : '').'>'.$i.'</option>';
				}

				echo '</select>월 -> ';

				echo '<span id=\'to_month\'></span>월 ';
				echo '<input name=\'copy_to_year\'  type=\'hidden\' value=\'\'>';
				echo '<input name=\'copy_to_month\' type=\'hidden\' value=\'\'>';
				echo '<span class=\'btn_pack m\'><button type=\'button\' onclick=\'run_copy();\'>복사</button></span>';
			?>
			</td>
		</tr>
	</tbody>
</table>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="80px">
		<col width="100px">
		<col width="60px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">성명</th>
			<th class="head">제공서비스</th>
			<th class="head">등급</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody>
	<?
		if ($lbTestMode){
			/*
			$sql = 'select count(distinct jumin)
					  from client_his_svc
					 where org_no           = \''.$code.'\'
					   and left(from_dt,4) <= \''.$year.'\'
					   and left(to_dt,4)   >= \''.$year.'\'';
			*/
		}else{
			$wsl = " where m03_ccode  = '$code'
					   and m03_mkind  = ".$conn->_client_kind()."
					   and m03_del_yn = 'N'
					   and '$year' between left(m03_gaeyak_fm, 4) and left(m03_gaeyak_to, 4)
					   and m03_sugup_status = '1'";

			$sql = "select count(*)
					  from m03sugupja $wsl";

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
		}

		if ($lbTestMode){
			$sql = 'select desire_ssn as cd
					,      sum(case when right(desire_yymm,2) = \'01\' then 1 else 0 end) as m01
					,      sum(case when right(desire_yymm,2) = \'02\' then 1 else 0 end) as m02
					,      sum(case when right(desire_yymm,2) = \'03\' then 1 else 0 end) as m03
					,      sum(case when right(desire_yymm,2) = \'04\' then 1 else 0 end) as m04
					,      sum(case when right(desire_yymm,2) = \'05\' then 1 else 0 end) as m05
					,      sum(case when right(desire_yymm,2) = \'06\' then 1 else 0 end) as m06
					,      sum(case when right(desire_yymm,2) = \'07\' then 1 else 0 end) as m07
					,      sum(case when right(desire_yymm,2) = \'08\' then 1 else 0 end) as m08
					,      sum(case when right(desire_yymm,2) = \'09\' then 1 else 0 end) as m09
					,      sum(case when right(desire_yymm,2) = \'10\' then 1 else 0 end) as m10
					,      sum(case when right(desire_yymm,2) = \'11\' then 1 else 0 end) as m11
					,      sum(case when right(desire_yymm,2) = \'12\' then 1 else 0 end) as m12
					  from counsel_client_desire
					 where org_no              = \''.$code.'\'
					   and left(desire_yymm,4) = \''.$year.'\'
					 group by desire_ssn';

			$desireCnt = $conn->_fetch_array($sql, 'cd');

			$sql = 'select his.svc_cd as kind
					,      his.jumin as cd
					,      mst.nm
					,      case lvl.svc_cd when \'0\' then case lvl.level when \'9\' then \'일반\' else concat(lvl.level,\'등급\') end
										   when \'4\' then concat(dis.svc_lvl,\'등급\') else \'\' end as lvl
					,      his.from_dt
					,      his.to_dt
					  from (
						   select org_no
						   ,      jumin
						   ,      svc_cd
						   ,      date_format(from_dt,\'%Y%m\') as from_dt
						   ,      date_format(to_dt,\'%Y%m\') as to_dt
							 from client_his_svc
							where org_no           = \''.$code.'\'
							  and left(from_dt,4) <= \''.$year.'\'
							  and left(to_dt,4)   >= \''.$year.'\'
						   ) as his
					 inner join (
						   select min(m03_mkind) as kind
						   ,      m03_jumin as jumin
						   ,      m03_name as nm
						    from m03sugupja
							where m03_ccode    = \''.$code.'\'
							  and m03_del_yn   = \'N\'
							group by m03_jumin
						   ) as mst
						on mst.jumin = his.jumin
					  left join client_his_lvl as lvl
					    on lvl.org_no   = his.org_no
					   and lvl.jumin    = his.jumin
					   and lvl.from_dt <= date_format(now(), \'%Y-%m-%d\')
					   and lvl.to_dt   >= date_format(now(), \'%Y-%m-%d\')
					  left join client_his_dis as dis
					    on dis.org_no   = his.org_no
					   and dis.jumin    = his.jumin
					   and dis.from_dt <= date_format(now(), \'%Y-%m-%d\')
					   and dis.to_dt   >= date_format(now(), \'%Y-%m-%d\')
					 order by nm,kind';

			$conn->query($sql);
			$conn->fetch();

			$rowCount = $conn->row_count();

			$no = 1;
			for($i=0; $i<$rowCount; $i++){
				$row = $conn->select_row($i);

				if ($tmpKey != $row['cd']){
					$tmpKey  = $row['cd'];

					$idx = sizeof($data);

					$data[$idx] = array(
							'no'	=>$no
						,	'jumin'	=>$row['cd']
						,	'name'	=>$row['nm']
						,	'lvlNm'	=>$row['lvl']
						,	'svcCd'	=>$row['kind']
						,	'svcNm'	=>$conn->kind_name_svc($row['kind'])
						,	'period'=>array(1=>0,2=>0,3=>0,4=>0,5=>0,6=>0,7=>0,8=>0,9=>0,10=>0,11=>0,12=>0)
					);

					$no ++;
				}

				for($j=1; $j<=12; $j++){
					$mon = ($j < 10 ? '0' : '').$j;

					if ($row['from_dt'] <= $year.$mon && $row['to_dt'] >= $year.$mon)
						$data[$idx]['period'][$j] = 1;
				}
			}

			$liTotCnt = sizeof($data);
			$conn->row_free();

			if (Is_Array($data)){
				foreach($data as $row){?>
					<tr>
						<td class="center"><?=$row['no'];?></td>
						<td class="left"><?=$row['name'];?></td>
						<td class="left"><?=$row['svcNm'];?></td>
						<td class="center"><?=$row['lvlNm'];?></td>
						<td class="left last">
						<?
							for($j=1; $j<=12; $j++){
								$class = 'my_month ';
								$mon   = ($j<10?'0':'').$j;
								$text  = $j.'월';

								if ($j == 12){
									$style = 'float:left;';
								}else{
									$style = 'float:left; margin-right:2px;';
								}

								if ($row['period'][$j] > 0){
									$link  = 'set_month(\''.$ed->en($row['jumin']).'\',\''.$j.'\');';
									$color = 'color:#000000; cursor:pointer;';

									if ($desireCnt[$row['jumin']]['m'.$mon] > 0){
										$class .= 'my_month_y';
									}else{
										$class .= 'my_month_1';
									}

									$style .= $color;?>
									<div class="<?=$class;?>" style="<?=$style;?>" onclick="<?=$link;?>"><?=$text;?></div><?
								}else{?>
									<div class="<?=$class;?>" style="<?=$style;?>"></div><?
								}
							}
						?>
						</td>
					</tr><?
				}
			}
		}else{
			$sql = "select m03_mkind as kind
					,      m03_jumin as cd
					,      m03_name as nm
					,      m81_name as lvl
					,      left(m03_gaeyak_fm, 6) as from_dt
					,      left(m03_gaeyak_to, 6) as to_dt
					  from m03sugupja
					  left join m81gubun
						on m81_gbn    = 'LVL'
					   and m81_code   = m03_ylvl $wsl
					 order by m03_name
					 limit $pageCount, $item_count";

			$conn->query($sql);
			$conn->fetch();

			$client_cnt = $conn->row_count();

			for($i=0; $i<$client_cnt; $i++){
				$client[$i] = $conn->select_row($i);
			}

			$conn->row_free();

			for($i=0; $i<$client_cnt; $i++){
				echo '
					<tr>
						<td class=\'center\'>'.($pageCount + ($i + 1)).'</td>
						<td class=\'left\'>'.($client[$i]['nm']).'</td>
						<td class=\'left\'>'.$conn->kind_name_svc($client[$i]['kind']).'</td>
						<td class=\'center\'>'.($client[$i]['lvl']).'</td>
						<td class=\'left last\'>';

				$ssn = $client[$i]['cd'];
				$sql = "select sum(case when right(desire_yymm, 2) = '01' then 1 else 0 end) as m01
						,      sum(case when right(desire_yymm, 2) = '02' then 1 else 0 end) as m02
						,      sum(case when right(desire_yymm, 2) = '03' then 1 else 0 end) as m03
						,      sum(case when right(desire_yymm, 2) = '04' then 1 else 0 end) as m04
						,      sum(case when right(desire_yymm, 2) = '05' then 1 else 0 end) as m05
						,      sum(case when right(desire_yymm, 2) = '06' then 1 else 0 end) as m06
						,      sum(case when right(desire_yymm, 2) = '07' then 1 else 0 end) as m07
						,      sum(case when right(desire_yymm, 2) = '08' then 1 else 0 end) as m08
						,      sum(case when right(desire_yymm, 2) = '09' then 1 else 0 end) as m09
						,      sum(case when right(desire_yymm, 2) = '10' then 1 else 0 end) as m10
						,      sum(case when right(desire_yymm, 2) = '11' then 1 else 0 end) as m11
						,      sum(case when right(desire_yymm, 2) = '12' then 1 else 0 end) as m12
						  from counsel_client_desire
						 where org_no         = '$code'
						   and desire_ssn     = '$ssn'
						   and desire_yymm like '$year%'";

				$mons = $conn->get_array($sql);

				for($j=1; $j<=12; $j++){
					$mon   = ($j < 10 ? '0' : '').$j;
					$class = 'my_month ';

					if ($mons['m'.$mon] > 0){
						$class .= 'my_month_y ';
						$color  = 'color:#000000;';
					}else{
						$class .= 'my_month_1 ';
						$color  = 'color:#c7c7c7;';
					}

					if ($year.$mon >= $client[$i]['from_dt'] && $year.$mon <= $client[$i]['to_dt']){
						$text  = '<a href="#" style="'.$color.'" onclick="set_month(\''.$ed->en($ssn).'\',\''.$j.'\');">'.$j.'월</a>';
						$style = '';
					}else{
						$text  = '&nbsp;';
						$class = '';
						$style = 'width:35px; height:23px; border:none; padding:0,1px; margin:0; text-align:center;';
					}

					if ($j == 12){
						$style .= 'float:left;';
					}else{
						$style .= 'float:left; margin-right:3px;';
					}

					echo '<div class=\''.$class.'\' style=\''.$style.'\'>'.$text.'</div>';
				}

				echo '	</td>
					</tr>';
			}
		}
	?>
	</tbody>
</table>

<div style="text-align:left;">
	<?
		if ($lbTestMode){?>
			<div style="width:auto; padding-left:10px;">검색된 전체 갯수 : <?=number_format($liTotCnt);?></div><?
		}else{?>
			<div style="position:absolute; width:auto; padding-left:10px;">검색된 전체 갯수 : <?=number_format($total_count);?></div>
			<div style="width:100%; text-align:center;"><?
				$paging = new YsPaging($params);
				$paging->printPaging();?>
			</div><?
		}
	?>
</div>

<input name="code" type="hidden" value="<?=$code;?>">
<input name="kind" type="hidden" value="<?=$kind;?>">
<input name="page" type="hidden" value="<?=$page;?>">
<input name="ssn" type="hidden" value="">
<input name="month" type="hidden" value="">

</form>

<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>
