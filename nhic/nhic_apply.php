<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code = $_POST['code'];
	$date = $_POST['date'];
	$seq  = $_POST['seq'];

	$min_dt = $_POST['min_dt'];
	$max_dt = $_POST['max_dt'];

	$sql = 'select nhic_from
			,      nhic_to
			  from nhic_log_mst
			 where org_no   = \''.$code.'\'
			   and nhic_dt  = \''.$date.'\'
			   and nhic_seq = \''.$seq.'\'';

	$tmp = $conn->get_array($sql);

	$min_limit = $myF->dateStyle($tmp[0]);
	$max_limit = $myF->dateStyle($tmp[1]);

	if (empty($min_dt)) $min_dt = $min_limit;
	if (empty($max_dt)) $max_dt = $max_limit;

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

var f = null;
var proc_i = 0;
var proc_timer = null;
var proc_body = null;

function timer_init(){
	var load = document.getElementById('my_load');

	load.style.display = '';

	__show_loading('my_load');

	proc_timer = setInterval('timer()',100);
}

function timer_clear(){
	var load = document.getElementById('my_load');

	load.style.display = 'none';

	clearInterval(proc_timer);
	proc_timer = null;
	proc_i = 0;
}

function timer(){
	if (proc_i == 0){
		if (document.getElementById('min_dt').value == '')
			document.getElementById('min_dt').value = document.getElementById('min_dt').tag;

		if (document.getElementById('max_dt').value == '')
			document.getElementById('max_dt').value = document.getElementById('max_dt').tag;

		var param   = {'code':document.getElementById('code').value
					  ,'date':document.getElementById('date').value
					  ,'seq':document.getElementById('seq').value
					  ,'min_dt':document.getElementById('min_dt').value
					  ,'max_dt':document.getElementById('max_dt').value
					  ,'c_cd':document.getElementById('c_cd').value
					  ,'m_cd':document.getElementById('m_cd').value};
		var URL     = '../nhic/nhic_apply_list.php';
		var xmlhttp = new Ajax.Request(
			URL, {
				method:'post',
				parameters:param,
				onSuccess:function(responseHttpObj){
					proc_body.innerHTML = responseHttpObj.responseText;
					proc_body.scrollTop = 0;
					proc_body = null;
				}
			}
		);
	}

	proc_i ++;

	if (proc_body == null) timer_clear();
}

function close_yn(){
	if (!confirm('현재 창을 닫으시겠습니까?')) return;

	self.close();
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

	리스트 출력

*********************************************************/
function set_list(){
	proc_body = document.getElementById('my_body');

	timer_init();
}


/*********************************************************

	저장

*********************************************************/
function save_svc(){
	if (!__checkRowCount()) return;

	f.target = '_self';
	f.action = '../nhic/nhic_save.php';
	f.submit();
}


window.onload = function(){
	resize(true);

	f = document.f;

	__init_form(f);
	set_list();
}

window.onresize = resize;

-->
</script>

<div class='title title_border'>건보실적(TEXT) 리스트</div>

<form name='f' method='post'>

<table class='my_table' style='width:100%;'>
	<colgroup>
		<col width='60px'>
		<col width='172px'>
		<col width='60px'>
		<col width='100px'>
		<col width='80px'>
		<col width='100px'>
		<col span='2'>
	</colgroup>
	<tbody>
		<tr>
			<th class='head'>등록정보</th>
			<td class='left'><?=$date.' / '.$seq;?></td>
			<th class='head'>LOG보기</th>
			<td class='left'>
				정상[<?=number_format($logCnt['cnt1']);?>]
			</td>
			<th class='head'>오류구분</th>
			<td class='left last' colspan='5'>
				수급자오류[<span style='color:#ff0000;'><?=number_format($logCnt['cnt2']);?></span>] |
				요양보호사오류[<span style='color:#ff0000;'><?=number_format($logCnt['cnt3']);?></span>] |
				계획오류[<span style='color:#ff0000;'><?=number_format($logCnt['cnt4']);?></span>] |
				실적오류[<span style='color:#ff0000;'><?=number_format($logCnt['cnt5']);?></span>]
			</td>
		</tr>
		<tr>
			<th class='head'>조회일자</th>
			<td class=''>
				<input name='min_dt' type='text' value='<?=$min_dt;?>' tag='<?=$min_limit;?>' class='date'> ~ <input name='max_dt' type='text' value='<?=$max_dt;?>' tag='<?=$max_limit;?>' class='date'>
			</td>
			<th class='head'>수급자</th>
			<td class='left'>
				<span class="btn_pack find" onClick="__find_sugupja3('<?=$code?>','0','c_cd','c_nm');"></span>
				<span id="c_nm" style="height:100%; margin-left:5px; font-weight:bold;"></span>
				<input name="c_cd" type="hidden" value="">
			</td>
			<th class='head'>요양보호사</th>
			<td class='left'>
				<span class="btn_pack find" onClick="__find_yoyangsa('<?=$code?>','0','m_cd','m_nm');"></span>
				<span id="m_nm" style="height:100%; margin-left:5px; font-weight:bold;"></span>
				<input name="m_cd" type="hidden" value="">
			</td>
			<td class='left last'>
				<span class='btn_pack m'><button type='button' onclick='set_list();'>조회</button></span>
			</td>
			<td class='right last'>
				<span class='btn_pack m'><button type='button' onclick='save_svc();'>등록</button></span>
				<span class='btn_pack m'><button type='button' onclick='close_yn();'>닫기</button></span>
			</td>
		</tr>
	</tbody>
</table>

<table class='my_table' style='width:100%;'>
	<colgroup>
		<col width='40px'>
		<col width='70px'>
		<col width='60px'>
		<col width='100px'>
		<col width='70px'>
		<col width='50px' span='4'>
		<col width='50px' span='3'>
		<col width='40px' span='2'>
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class='head' rowspan='2'>No</th>
			<th class='head' rowspan='2'>서비스</th>
			<th class='head' rowspan='2'>수급자</th>
			<th class='head' rowspan='2'>요양보호사</th>
			<th class='head' rowspan='2'>일자</th>
			<th class='head' colspan='4'>계획일정</th>
			<th class='head' colspan='3'>실적일정</th>
			<th class='head'>저장</th>
			<th class='head' rowspan='2'>등록<br>여부</th>
			<th class='head last' rowspan='2'>비고</th>
		</tr>
		<tr>
			<th class='head'>시작</th>
			<th class='head'>종료</th>
			<th class='head'>제공</th>
			<th class='head'>상태</th>
			<th class='head'>시작</th>
			<th class='head'>종료</th>
			<th class='head'>제공</th>
			<th class='head'><input name='check_all' type='checkbox' class='checkbox' onclick='__checkMyValue("check[]", this.checked);'></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class='last' colspan='15'>
				<div id='my_body' style='width:100%; height:100px; overflow-x:hidden; overflow-y:scroll;'></div>
			</td>
		</tr>
	</tbody>
	<tfoot id='my_foot'>
		<tr>
			<td class='bottom last' colspan='15'>&nbsp;</td>
		</tr>
	</tfoot>
</table>

<input name='code' type='hidden' value='<?=$code;?>'>
<input name='date' type='hidden' value='<?=$date;?>'>
<input name='seq'  type='hidden' value='<?=$seq;?>'>

<div id='my_load' style='position:absolute; width:100%; height:100%; left:0; top:0; display:none;'></div>

</form>

<?

	include_once('../inc/_footer.php');
?>