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

	$init_year = $myF->year();
?>

<script src="../js/work.js" type="text/javascript"></script>
<script language='javascript'>
<!--
function search(){
	var f = document.f;

	f.submit();
}

function work_list(code, kind, year, month, member){
	var f = document.f;

	f.code.value   = code;
	f.kind.value   = kind;
	f.year.value   = year;
	f.month.value  = month;
	f.member.value = member;

	f.action = 'month_menber_work_list.php';
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

<div class="title">월 실적 등록(요양보호사)</div>

<table class="my_table my_border">
	<colgroup>
		<col width="35px">
		<col width="40px">
		<col width="100px">
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
			<th>요양보호사 성명</th>
			<td>
				<input name="find_name" type="text" value="<?=$find_name;?>">
			</td>
			<td class="left last">
				<span class="btn_pack m icon"><span class="refresh"></span><button type="button" onclick="search();">조회</button></span>
			</td>
		</tr>
	</tbody>
</table>

<table class="my_table my_border" style="margin-top:-1px; border-bottom:none;">
	<colgroup>
		<col width="40px">
		<col width="100px">
		<col width="150px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">요양보호사</th>
			<th class="head">직책</th>
			<th class="head last">월별</th>
		</tr>
	</thead>
	<tbody>
	<?
		$wsl = " and m02_mkind = (select min(chd.m02_mkind) from m02yoyangsa as chd where chd.m02_ccode = mst.m02_ccode)
				 and '$find_year' between left(m02_yipsail, 4) and left(case when length(m02_ytoisail) = 8 then m02_ytoisail else '99999999' end, 4)";

		if ($find_name != ''){
			$wsl .= " and m02_yname >= '$find_name'";
		}

		$sql = "select count(*)
				  from m02yoyangsa as mst
				 where m02_ccode = '$code'
				   and m02_del_yn = 'N' $wsl";
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

		$sql = "select m02_ccode as code
				,      m02_mkind as kind
				,      m02_yjumin as jumin
				,      m02_yname as name
				,      m98_name as position
				,      left(m02_yipsail, 6) as i_date
				,      left(case when length(m02_ytoisail) = 8 then m02_ytoisail else '99999999' end, 6) as o_date
				  from m02yoyangsa as mst
				 inner join m98job
					on m98_code = m02_yjikjong
				 where m02_ccode = '$code'
				   and m02_del_yn = 'N' $wsl
				 order by m02_yname
				 limit $pageCount, $item_count";

		$conn->query($sql);
		$conn->fetch();
		$row_count = $conn->row_count();

		if ($row_count > 0){
			for($i=0; $i<$row_count; $i++){
				$mem[$i] = $conn->select_row($i);
			}

			$mem_count = sizeof($mem);

			$conn->row_free();

			for($i=0; $i<$mem_count; $i++){
				$kind  = $mem[$i]['kind'];
				$jumin = $mem[$i]['jumin'];

				$sql = "select sum(case date_format(t01_sugup_date, '%m') when '01' then 1 else 0 end) as m01
						,      sum(case date_format(t01_sugup_date, '%m') when '02' then 1 else 0 end) as m02
						,      sum(case date_format(t01_sugup_date, '%m') when '03' then 1 else 0 end) as m03
						,      sum(case date_format(t01_sugup_date, '%m') when '04' then 1 else 0 end) as m04
						,      sum(case date_format(t01_sugup_date, '%m') when '05' then 1 else 0 end) as m05
						,      sum(case date_format(t01_sugup_date, '%m') when '06' then 1 else 0 end) as m06
						,      sum(case date_format(t01_sugup_date, '%m') when '07' then 1 else 0 end) as m07
						,      sum(case date_format(t01_sugup_date, '%m') when '08' then 1 else 0 end) as m08
						,      sum(case date_format(t01_sugup_date, '%m') when '09' then 1 else 0 end) as m09
						,      sum(case date_format(t01_sugup_date, '%m') when '10' then 1 else 0 end) as m10
						,      sum(case date_format(t01_sugup_date, '%m') when '11' then 1 else 0 end) as m11
						,      sum(case date_format(t01_sugup_date, '%m') when '12' then 1 else 0 end) as m12
						  from t01iljung
						 where t01_ccode = '$code'
						   and t01_mkind = '$kind'
						   and t01_sugup_date like '$find_year%'
						   and t01_del_yn = 'N'
						   and t01_yoyangsa_id1 = '$jumin'";
				$month = $conn->get_array($sql); ?>
				<tr>
					<td class="center"><?=$i+1;?></td>
					<td class="left"><?=$mem[$i]['name'];?></td>
					<td class="left"><?=$mem[$i]['position'];?></td>
					<td class="left last" style="padding-top:3px;">
						<table>
							<tr>
							<?
								for($j=1; $j<=12; $j++){
									$mon = ($j < 10 ? '0' : '').$j;

									$class = 'my_month ';

									if ($mem[$i]['i_date'] <= $find_year.$mon && $mem[$i]['o_date'] >= $find_year.$mon){
										if ($month['m'.$mon] > 0){
											$class .= 'my_month_y ';
											$text   = '<a href="#" onclick="work_list(\''.$mem[$i]['code'].'\',\''.$mem[$i]['kind'].'\',\''.$find_year.'\',\''.$mon.'\',\''.$ed->en($mem[$i]['jumin']).'\');">'.$j.'월</a>';
										}else{
											$class .= 'my_month_g ';
											$text   = $j.'월';
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
				</tr><?
				unset($month);
			}
		}else{?>
			<tr>
				<td class="center last" colspan="4">::<?=$myF->message('nodata','N');?>::</td>
			</tr><?
		}

		$conn->row_free();
	?>
	</tbody>
	<tbody>
		<tr>
			<td class="bottom last" colspan="4">
				<div style="text-align:left;">
					<div style="position:absolute; width:auto; padding-left:10px;">검색된 전체 갯수 : <?=number_format($total_count);?></div>
					<div style="width:100%; text-align:center;">
					<?
						$paging = new YsPaging($params);
						$paging->printPaging();
					?>
					</div>
				</div>
			</td>
		</tr>
	</tbody>
</table>

<input type="hidden" name="code"   value="">
<input type="hidden" name="kind"   value="">
<input type="hidden" name="year"   value="">
<input type="hidden" name="month"  value="">
<input type="hidden" name="member" value="">
<input type="hidden" name="page"   value="<?=$page;?>">

</form>

<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>