<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code  = $_POST['code'];
	$date  = $_POST['date'];
	$seq   = $_POST['seq'];
	$mode  = $_POST['mode'];
	$year  = $_POST['year'];
	$month = $_POST['month'];

	if (empty($year))  $year  = date('Y', mktime());
	if (empty($month)) $month = date('m', mktime());

	if (!empty($_POST['find_log_gbn']))
		$find_log_gbn = $_POST['find_log_gbn'];
	else
		$find_log_gbn = $mode;

	if (empty($find_log_gbn)) $find_log_gbn = '1';


	/*********************************************************

		서비스기간

	*********************************************************/
	$sql = 'select nhic_from
			,      nhic_to
			  from nhic_log_mst
			 where org_no   = \''.$code.'\'
			   and nhic_dt  = \''.$date.'\'
			   and nhic_seq = \''.$seq.'\'';

	$tmp = $conn->get_array($sql);

	$min_dt = $myF->dateStyle($tmp[0]);
	$max_dt = $myF->dateStyle($tmp[1]);

	unset($tmp);



	/*********************************************************

		에러정보

	*********************************************************/
	$sql = 'select sum(case when length(nhic_status) = 1 then 1 else 0 end) as cnt1
			,      sum(case when length(nhic_status) > 1 and length(nhic_status) != length(replace(nhic_status, \'E1/\', \'\')) then 1 else 0 end) as cnt2
			,      sum(case when length(nhic_status) > 1 and length(nhic_status) != length(replace(nhic_status, \'E2/\', \'\')) then 1 else 0 end) as cnt3
			,      sum(case when length(nhic_status) > 1 and length(nhic_status) != length(replace(nhic_status, \'E3/\', \'\')) then 1 else 0 end) as cnt4
			,      sum(case when length(nhic_status) > 1 and length(nhic_status) != length(replace(nhic_status, \'E4/\', \'\')) then 1 else 0 end) as cnt5
			  from nhic_log
			 where org_no   = \''.$code.'\'
			   and mst_dt   = \''.$date.'\'
			   and mst_seq  = \''.$seq.'\'';

	$logCnt = $conn->get_array($sql);
?>

<script language='javascript'>
<!--

var mouse_over_yn = false;

/*********************************************************

	리스트 출력

*********************************************************/
function set_list(){
	var f = document.f;

	if (!checkDate(f.date.value)){
		alert('등록일자를 선택하여 주십시오.');
		show_reg_dt();
		return;
	}

	if (!checkDate(f.find_from_dt.value)) f.find_from_dt.value = f.find_from_dt.tag;
	if (!checkDate(f.find_to_dt.value)) f.find_to_dt.value = f.find_to_dt.tag;

	set_list_data();
}

/*********************************************************

	화면 크기에 제한을 둔다.

*********************************************************/
function resize(resize_yn){
	var body   = document.getElementById('my_body');
	var foot   = document.getElementById('my_foot');


	/*********************************************************
		크기
	*********************************************************/
	if (resize_yn){
		if (document.body.clientWidth  < 1100 ||
			document.body.clientHeight < 500){
			window.resizeTo(document.body.clientWidth < 1100 ? 1150 : document.body.clientWidth, document.body.clientHeight < 500 ? 600 : document.body.clientHeight);
		}
	}


	var height = document.body.clientHeight;
	var top    = __getObjectTop(body);

	/*********************************************************
		리스트가 그려질 공간
	*********************************************************/
	body.style.height = height - top - foot.offsetHeight;


	/*********************************************************
		창을 화면 가운데로 이동
	*********************************************************/
	//if (resize_yn)
	//	window.moveTo((screen.width - document.body.clientWidth) / 2, (screen.height - document.body.clientHeight) / 2);
}


/*********************************************************

	등록일자

*********************************************************/
function show_reg_dt(){
	var body = document.getElementById('my_reg_dt');
	var list = document.getElementById('my_reg_dt_list');
	var top  = __getObjectTop(body) + body.offsetHeight + 1;
	var left = __getObjectLeft(body) - 1;

	set_month(document.getElementById('month').value);

	list.style.top     = top;
	list.style.left    = left;
	list.style.display = '';
}


/*********************************************************

	등록년월

*********************************************************/
function set_month(month){
	for(var i=1; i<=12; i++){
		var obj = document.getElementById('obj_month_'+i);

		if (i == month)
			obj.className = 'my_month my_month_y';
		else
			obj.className = 'my_month my_month_1';
	}

	document.getElementById('month').value = month;

	var param   = {'code':document.getElementById('code').value
				  ,'year':document.getElementById('year').value
				  ,'month':document.getElementById('month').value
				  ,'mode':3};
	var URL     = '../nhic/nhic_mstlog_list.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:param,
			onSuccess:function(responseHttpObj){
				var body = document.getElementById('my_reg_dt_log');
					body.innerHTML = responseHttpObj.responseText;
					mouse_over_yn  = false;
			}
		}
	);
}


/*********************************************************

	데이타조회

*********************************************************/
function set_list_data(){
	var log_gbn = __object_get_value('find_log_gbn');
	var param   = {'code':document.getElementById('code').value
				  ,'date':document.getElementById('date').value
				  ,'seq':document.getElementById('seq').value
				  ,'from_dt':document.getElementById('find_from_dt').value
				  ,'to_dt':document.getElementById('find_to_dt').value
				  ,'log_gbn':log_gbn};
	var URL     = '../nhic/nhic_log_data.php';
	var xmlhttp = new Ajax.Request(
		URL, {
			method:'post',
			parameters:param,
			onSuccess:function(responseHttpObj){
				var body = document.getElementById('my_body');
					body.innerHTML = responseHttpObj.responseText;
			}
		}
	);
}


/*********************************************************

	등록선택

*********************************************************/
function get_reg_dt(reg_dt, reg_seq, min_dt, max_dt){
	var body = document.getElementById('my_reg_dt');
	var list = document.getElementById('my_reg_dt_list');

	document.getElementById('date').value = reg_dt;
	document.getElementById('seq').value  = reg_seq;
	document.getElementById('find_from_dt').value = min_dt;
	document.getElementById('find_to_dt').value   = max_dt;



	body.innerHTML = reg_dt+' / '+reg_seq;
	list.style.display = 'none';
}


function set_reg_dt_list(){
	if (!mouse_over_yn) return;

	var list = document.getElementById('my_reg_dt_list');
		list.style.display = 'none';
}



window.onload = function(){
	resize(true);

	__init_form(document.f);

	set_list_data();
}

window.onresize = resize;

-->
</script>

<div class='title title_border'>건보실적 LOG 리스트</div>

<form name='f' method='post'>

<table class='my_table' style='width:100%;'>
	<colgroup>
		<col width='60px'>
		<col width='120px'>
		<col width='80px'>
		<col width='177px'>
		<col width='50px'>
		<col width='160px'>
		<col width='60px'>
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class='head'>등록일자</th>
			<td class='left' id='my_reg_dt' onclick='show_reg_dt();' style='cusror:pointer;'><?=$date.' / '.$seq;?></td>
			<th class='head'>서비스 기간</th>
			<td><input name='find_from_dt' type='text' value='<?=$min_dt;?>' tag='<?=$min_dt;?>' class='date'> ~ <input name='find_to_dt' type='text' value='<?=$max_dt;?>' tag='<?=$max_dt;?>' class='date'></td>
			<th class='head'>LOG</th>
			<td>
				<input name='find_log_gbn' type='radio' value='1' class='radio' onclick='set_list();' <? if($find_log_gbn == '1'){echo 'checked';} ?>>정상LOG
				<input name='find_log_gbn' type='radio' value='E' class='radio' onclick='set_list();' <? if($find_log_gbn == 'E'){echo 'checked';} ?>>에러LOG
			</td>
			<th class='head'>LOG정보</th>
			<td class='left last'>
				정상 : <span><?=number_format($logCnt['cnt1']);?></span> /
				수급자 : <span style='color:#ff0000;'><?=number_format($logCnt['cnt2']);?></span> /
				요양사 : <span style='color:#ff0000;'><?=number_format($logCnt['cnt3']);?></span> /
				계획 : <span style='color:#ff0000;'><?=number_format($logCnt['cnt4']);?></span> /
				실적 : <span style='color:#ff0000;'><?=number_format($logCnt['cnt5']);?></span>
			</td>
		</tr>
	</tbody>
</table>

<table class='my_table' style='width:100%;'>
	<colgroup>
		<col width='40px'>
		<col width='30px'>
		<col width='60px'>
		<col width='50px'>
		<col width='100px'>
		<col width='30px'>
		<col width='70px'>
		<col width='50px' span='6'>
		<col width='30px' span='2'>
		<col width='45px' span='4'>
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class='head' rowspan='2'>No</th>
			<th class='head' rowspan='2'>청구<br>반영</th>
			<th class='head' rowspan='2'>서비스<br>종류</th>
			<th class='head' rowspan='2'>수급자</th>
			<th class='head' rowspan='2'>요양보호사</th>
			<th class='head' rowspan='2'>참여<br>구분</th>
			<th class='head' rowspan='2'>일자</th>
			<th class='head' colspan='3'>계획일정</th>
			<th class='head' colspan='3'>실적일정</th>
			<th class='head' rowspan='2'>90분<br>여부</th>
			<th class='head' rowspan='2'>동거<br>여부</th>
			<th class='head' colspan='4'>오류구분</th>
			<th class='head last' rowspan='2'>비고</th>
		</tr>
		<tr>
			<th class='head'>시작</th>
			<th class='head'>종료</th>
			<th class='head'>제공</th>
			<th class='head'>시작</th>
			<th class='head'>종료</th>
			<th class='head'>제공</th>
			<th class='head'>수급자</th>
			<th class='head'>요양사</th>
			<th class='head'>계획</th>
			<th class='head'>실적</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class='last' colspan='20'>
				<div id='my_body' style='width:100%; height:100px; overflow-x:hidden; overflow-y:scroll;'></div>
			</td>
		</tr>
	</tbody>
	<tfoot id='my_foot'>
		<tr>
			<td class='bottom last' colspan='20'>&nbsp;</td>
		</tr>
	</tfoot>
</table>

<div id='my_reg_dt_list' style='position:absolute; width:auto; left:0; top:0; border:3px solid #cccccc; display:none;'>
	<table class='my_table' style='background-color:#ffffff;'>
		<colgroup>
			<col width='40px'>
			<col width='111px'>
			<col width='70px'>
			<col width='40px'>
			<col width='170px'>
		</colgroup>
		<tbody>
			<tr>
				<th class='head'>년도</th>
				<td>
				<?
					$init_year = $myF->year();

					echo '<select name=\'year\' style=\'width:auto;\'>';

					for($i=$init_year[0]; $i<=$init_year[1]; $i++){
						echo '<option value=\''.$i.'\' '.($i == $year ? 'selected' : '').'>'.$i.'</option>';
					}

					echo '</select>년';
				?>
				</td>
				<th class='head'>등록일</th>
				<th class='head'>회차</th>
				<th class='head last'>일정기간</th>
			</tr>
			<tr>
				<th class='head bottom'>월별</th>
				<td class='bottom' style='padding:3px;'><? echo $myF->_btn_month($month, 'set_month(', ');');?></td>
				<td class='bottom last' colspan='3'><div id='my_reg_dt_log' style='width:100%; height:100%; overflow-x:hidden; overflow-y:scroll;' onmouseover='mouse_over_yn=false;' onmouseout='set_reg_dt_list();'></div></td>
			</tr>
		</tbody>
	</table>
</div>
<input name='code'  type='hidden' value='<?=$code;?>'>
<input name='month' type='hidden' value='<?=$month;?>'>

<input name='date' type='hidden' value='<?=$date;?>'>
<input name='seq'  type='hidden' value='<?=$seq;?>'>

</form>

<?
	include_once('../inc/_footer.php');
?>