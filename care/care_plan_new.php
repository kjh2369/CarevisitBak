<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$year = Date('Y');
?>
<script type="text/javascript">
	$(document).ready(function(){
		lfSearch();
	});

	function lfMoveYear(pos){
		var year = __str2num($('#lblYear').text());

		year += pos;

		$('#lblYear').text(year);

		lfSearch();
	}

	function lfSearch(){
		$.ajax({
			type :'POST'
		,	url  :'./care_plan_search.php'
		,	data :{
				'type'	:'<?=$type;?>'
			,	'SR'	:'<?=$sr;?>'
			,	'year'	:$('#lblYear').text()
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				$('#ID_LIST').html(html);
				$('#tempLodingBar').remove();
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfPlanReg(obj,code){
		var objModal = new Object();
		var url = './care_plan_reg.php';
		var style = 'dialogWidth:600px; dialogHeight:600px; dialogHide:yes; scroll:no; status:no';

		objModal.win	= window;
		objModal.type	= '<?=$type;?>_POP';
		objModal.year	= $('#lblYear').text();
		objModal.sr		= '<?=$sr;?>';
		objModal.code	= code;
		objModal.result	= 0;
		objModal.IsNew	= true;

		window.showModalDialog(url, objModal, style);

		if (objModal.result != 1) return;

		lfSearch();
	}

	function lfPlanDel(obj,code){
		if (!confirm('삭제 후 복구가 불가능합니다. 정말로 삭제하시겠습니까?')) return;

		$.ajax({
			type :'POST'
		,	url  :'./care_plan_delete.php'
		,	data :{
				'sr':'<?=$sr;?>'
			,	'year':$('#lblYear').text()
			,	'code':code
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(result){
				if (__resultMsg(result)){
					lfSearch();
				}else{
				}
				$('#tempLodingBar').remove();
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfPlanCopy(){
		if (!confirm('전년도 사업계획을 복사하시겠습니까?\n선택 년도의 작성된 사업계획은 삭제됩니다.')) return;

		$.ajax({
			type :'POST'
		,	url  :'./care_plan_copy.php'
		,	data :{
				'sr':'<?=$sr;?>'
			,	'year':$('#lblYear').text()
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(result){
				if (!result){
					alert('정상적으로 처리 되었습니다.');
					lfSearch();
				}else{
					alert(result);
				}
				$('#tempLodingBar').remove();
			}
		,	error:function(){
			}
		}).responseXML;
	}
</script>
<div class="title title_border">사업계획(<?=lfGetSPName($sr);?>)</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="85px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">년월</th>
			<td class="center last">
				<div class="left" style="padding-top:2px;">
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_pre_out.gif" style="cursor:pointer;" onclick="lfMoveYear(-1);" onmouseover="this.src='../image/btn/btn_pre_over.gif';" onmouseout="this.src='../image/btn/btn_pre_out.gif';"></div>
					<div style="float:left; width:auto; padding-left:5px; padding-right:5px; font-weight:bold;" id="lblYear"><?=$year;?></div>
					<div style="float:left; width:auto; padding-top:2px;"><img src="../image/btn/btn_next_out.gif" style="cursor:pointer;" onclick="lfMoveYear(1);" onmouseover="this.src='../image/btn/btn_next_over.gif';" onmouseout="this.src='../image/btn/btn_next_out.gif';"></div>
				</div>
			</td>
			<td class="right last">
				<span class="btn_pack small"><button onclick="lfPlanCopy();">전년 사업계획 복사</button></span>
			</td>
		</tr>
	</tbody>
</table>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="95px">
		<col width="80px">
		<col width="80px">
		<col width="80px">
		<col width="60px" span="3">
		<col width="80px" span="3">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head" colspan="4">서비스분류</th>
			<th class="head" colspan="3">계획</th>
			<th class="head" rowspan="2">사업<br>내용</th>
			<th class="head" rowspan="2">기대<br>효과</th>
			<th class="head" rowspan="2">수행 및<br>평가도구</th>
			<th class="head last" rowspan="2">비고</th>
		</tr>
		<tr>
			<th class="head">대분류</th>
			<th class="head">중분류</th>
			<th class="head">소분류</th>
			<th class="head">상세</th>
			<th class="head">목표</th>
			<th class="head">예산</th>
			<th class="head">횟수</th>
		</tr>
	</thead>
	<tbody id="ID_LIST">
		<tr>
			<td class="center last" colspan="11">::검색된 데이타가 없습니다.::</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td class="bottom last"></td>
		</tr>
	</tfoot>
</table>
<?
	include_once('../inc/_db_close.php');
?>