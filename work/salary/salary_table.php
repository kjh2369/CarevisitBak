<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");

	$code  = $_SESSION['userCenterCode'];
	$kind  = $conn->center_kind($code);
	$year  = $_REQUEST['year']  != '' ? $_REQUEST['year']  : date('Y', mktime());

	$sql = "select ifnull(max(cast(right(salary_yymm, 2) as unsigned)), 0)
			  from salary_basic
			 where org_no = '$code'";
	$max_month = $conn->get_data($sql);

	$month = $_REQUEST['month'] != '' ? $_REQUEST['month'] : ($max_month > 0 ? $max_month : date('m', mktime()));
	$month = ($month < 10 ? '0' : '').intval($month);

	$init_year = $myF->year();

	// 페이지 설정
	$page = (intval($_REQUEST['page']) > 0 ? intval($_REQUEST['page']) : 1);
	$col_cnt = 5;
	$col_width1 = 35;
	$col_width2 = 64;
	$col_width  = $col_width1 + $col_width2;
?>

<script src="../js/work.js" type="text/javascript"></script>
<script src="../js/report.js" type="text/javascript"></script>
<script language='javascript'>
<!--

function table_list(month){
	var f = document.f;

	f.page.value  = 1;
	f.month.value = month;
	f.submit();
}

function page_move(pos){
	var f = document.f;

	if (pos == 'b'){
		var page = __str2num(f.page.value) - 1;
	}else if (pos == 'n'){
		var page = __str2num(f.page.value) + 1;
	}else{
		var page = pos;
	}
	var max_page = __str2num(f.max_page.value);
	var max_item = __str2num(f.max_item.value);
	var page_class = document.getElementsByName('page_class[]');
	var page_info = document.getElementById('page_info');

	if (page < 1) page = 1;
	if (page > max_page) page = max_page;

	f.page.value = page;

	for(var i=1; i<=max_item; i++){
		for(var j=1; j<=max_page; j++){
			var obj = document.getElementsByName('id_'+i+'_'+j+'[]');

			if (typeof(obj) == 'object'){
				if (j == page){
					var className = 'page_list_2';
					var display = '';
				}else{
					var className = 'page_list_1';
					var display = 'none';
				}

				page_class[j-1].className = className;

				for(var k=0; k<obj.length; k++){
					obj[k].style.display = display;
				}
			}
		}
	}

	page_info.innerHTML = '페이지 '+page+' / '+max_page;
}

function resize_table(){
	var scroll = document.getElementById('table_scroll');
	var height = document.body.clientHeight - 320;

	if (height < 250) height = 250;

	scroll.style.height = document.body.clientHeight - 320;
}

function excel(){
	var f = document.f;
	var code  = f.code.value;
	var kind  = f.kind.value;
	var year  = f.year.value;
	var month = f.month.value;

	location.replace('salary_table_excel.php?code='+code+'&kind='+kind+'&year='+year+'&month='+month);
}

function pdf(){
	showMyReport('17', document.getElementById('code').value, document.getElementById('kind').value, document.getElementById('year').value, document.getElementById('month').value, '', 'month');
}

window.onload = function(){
	if ('<?=$page;?>' != '1'){
		page_move(parseInt('<?=$page;?>'));
	}
	resize_table();
}

window.onresize = function(){
	resize_table();
}

-->
</script>

<form name="f" method="post">

<div class="title">급여대장</div>

<table class="my_table my_border">
	<colgroup>
		<col width="35px">
		<col width="40px">
		<col width="35px">
		<col span="2">
	</colgroup>
	<tbody>
		<tr>
			<th class="head">년도</th>
			<td>
				<select name="year" style="width:auto;" onchange="table_list(<?=$month;?>);">
				<?
					for($i=$init_year[0]; $i<=$init_year[1]; $i++){ ?>
						<option value="<?=$i;?>" <? if($year == $i){?>selected<?} ?>><?=$i;?></option><?
					}
				?>
				</select>
			</td>
			<th class="head">월별</th>
			<td class="left last">
			<?
				for($i=1; $i<=12; $i++){
					$mon[$i] = 0;
				}

				$sql = "select distinct cast(right(salary_yymm, 2) as unsigned)
						  from salary_basic
						 where org_no = '$code'
						   and salary_yymm like '$year%'";
				$conn->query($sql);
				$conn->fetch();
				$row_count = $conn->row_count();

				for($i=0; $i<$row_count; $i++){
					$row = $conn->select_row($i);
					$mon[$row[0]] = $row[0];
				}

				$conn->row_free();

				for($i=1; $i<=12; $i++){
					$class = 'my_month ';

					if ($i == $mon[$i]){
						if ($i == intval($month)){
							$class .= 'my_month_y ';
						}else{
							$class .= 'my_month_g ';
						}
						$link	= '<a href="#" onclick="table_list('.$i.');">'.$i.'월</a>';
					}else{
						if ($i == intval($month)){
							$class .= 'my_month_y ';
						}else{
							$class .= 'my_month_1 ';
						}
						$link	= '<a style="cursor:default;"><span style="color:#7c7c7c;">'.$i.'월</span></a>';
					}

					$margin_right = '2px';

					if ($i == 12){
						$margin_right = '0';
					}?>
					<div class="<?=$class;?>" style="float:left; margin-right:<?=$margin_right;?>;"><?=$link;?></div><?
				}
			?>
			</td>
			<td class="right last">
				<span class="btn_pack m icon"><span class="excel"></span><button type="button" onclick="excel();">Excel</button></span>
				<span class="btn_pack m icon"><span class="pdf"></span><button type="button" onclick="pdf();">PDF</button></span>
			</td>
		</tr>
	</tbody>
</table>
<?
	include('salary_table_sub.php');
?>
<div class="my_center" style="width:auto; border-top:1px solid #cccccc; float:left;">
	 <span id="page_info">페이지 1 / <?=$max_page;?></span>
</div>
<div class="my_center" style="width:auto; border-top:1px solid #cccccc; text-align:center;">
	[<a href="#" onclick="page_move('b');">이전</a>]<?
	for($i=0; $i<$max_page; $i++){
		$str_page = $i + 1;
		if ($str_page == 1){
			$class = 'page_list_2';
		}else{
			$class = 'page_list_1';
		}?>
		<a href="#" onclick="page_move('<?=$str_page;?>');"><span id="page_class[]" class="<?=$class;?>" style="padding-left:2px; padding-right:2px;"><?=$str_page;?></span></a><?
	}
?>	[<a href="#" onclick="page_move('n');">다음</a>]
</div>

<input type="hidden" name="code" value="<?=$code;?>">
<input type="hidden" name="kind" value="<?=$kind;?>">
<input type="hidden" name="jumin" value="">
<input type="hidden" name="month" value="<?=$month;?>">
<input type="hidden" name="page" value="<?=$page;?>">
<input type="hidden" name="max_page" value="<?=$max_page;?>">
<input type="hidden" name="max_item" value="1">
<input type="hidden" name="is_table" value="1">

</form>

<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");

	function draw_blnk($max_cnt, $id, $width, $tag = 'td'){
		for($i=1; $i<$max_cnt; $i++){?>
			<<?=$tag;?> id="id_1_<?=$id;?>[]" class="last" style="width:<?=$width;?>px; padding:0; <? if($id > 1){?>display:none;<?} ?>">&nbsp;</<?=$tag;?>><?
		}
	}
?>